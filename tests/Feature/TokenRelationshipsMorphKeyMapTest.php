<?php declare(strict_types=1);

use Cline\Bearer\Database\Models;
use Cline\Bearer\Facades\Bearer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\Fixtures\MappedBoundary;
use Tests\Fixtures\MappedContext;
use Tests\Fixtures\MappedOwner;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));

    $schema = $this->app['db']->connection()->getSchemaBuilder();

    $schema->create('mapped_users', function ($table): void {
        $table->id();
        $table->string('ulid')->unique();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });

    $schema->create('mapped_contexts', function ($table): void {
        $table->id();
        $table->string('ulid')->unique();
        $table->string('name');
        $table->timestamps();
    });

    $schema->create('mapped_boundaries', function ($table): void {
        $table->id();
        $table->string('ulid')->unique();
        $table->string('name');
        $table->timestamps();
    });

    Models::reset();
    Models::enforceMorphKeyMap([
        MappedOwner::class => 'ulid',
        MappedContext::class => 'ulid',
        MappedBoundary::class => 'ulid',
    ]);
});

afterEach(function (): void {
    Models::reset();
});

it('uses the mapped owner key for access tokens', function (): void {
    $owner = MappedOwner::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Owner',
        'email' => 'mapped-owner@example.com',
        'password' => bcrypt('password'),
    ]);

    $token = Bearer::for($owner)->issue('sk', 'Mapped Token')->accessToken->refresh();

    expect($owner->accessTokens()->count())->toBe(1);
    expect($token->owner_id)->toBe($owner->ulid);
    expect($token->owner->ulid)->toBe($owner->ulid);
});

it('uses the mapped owner key for access token groups', function (): void {
    $owner = MappedOwner::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Owner',
        'email' => 'mapped-group@example.com',
        'password' => bcrypt('password'),
    ]);

    $group = Bearer::for($owner)->issueGroup(
        types: ['sk', 'pk'],
        name: 'Mapped Group',
    );

    expect($owner->accessTokenGroups()->count())->toBe(1);
    expect($group->owner_id)->toBe($owner->ulid);
    expect($group->owner->ulid)->toBe($owner->ulid);
});

it('uses the mapped context key for context tokens', function (): void {
    $owner = MappedOwner::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Owner',
        'email' => 'mapped-context-owner@example.com',
        'password' => bcrypt('password'),
    ]);
    $context = MappedContext::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Context',
    ]);

    $token = Bearer::for($owner)
        ->context($context)
        ->issue('sk', 'Context Token')
        ->accessToken
        ->refresh();

    expect($context->contextTokens()->count())->toBe(1);
    expect($token->context_id)->toBe($context->ulid);
    expect($token->context->ulid)->toBe($context->ulid);
});

it('uses the mapped boundary key for boundary tokens', function (): void {
    $owner = MappedOwner::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Owner',
        'email' => 'mapped-boundary-owner@example.com',
        'password' => bcrypt('password'),
    ]);
    $boundary = MappedBoundary::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Boundary',
    ]);

    $token = Bearer::for($owner)
        ->boundary($boundary)
        ->issue('sk', 'Boundary Token')
        ->accessToken
        ->refresh();

    expect($boundary->boundaryTokens()->count())->toBe(1);
    expect($token->boundary_id)->toBe($boundary->ulid);
    expect($token->boundary->ulid)->toBe($boundary->ulid);
});

it('cascades deletes through mapped owner relations', function (): void {
    $owner = MappedOwner::query()->create([
        'ulid' => (string) Str::ulid(),
        'name' => 'Mapped Owner',
        'email' => 'mapped-delete@example.com',
        'password' => bcrypt('password'),
    ]);

    $owner->createAccessTokenGroup(
        types: ['sk', 'pk'],
        name: 'Mapped Delete Group',
    );
    Bearer::for($owner)->issue('sk', 'Mapped Delete Token');

    expect($owner->accessTokens()->count())->toBe(3);
    expect($owner->accessTokenGroups()->count())->toBe(1);

    $owner->delete();

    expect($owner->accessTokens()->count())->toBe(0);
    expect($owner->accessTokenGroups()->count())->toBe(0);
});

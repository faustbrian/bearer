<?php declare(strict_types=1);

use Cline\Bearer\Facades\Bearer;
use Cline\Bearer\NewAccessToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;

describe('Token Rotation', function (): void {
    beforeEach(function (): void {
        Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    });

    it('rotates a token with immediate invalidation', function (): void {
        $user = createUser();
        $oldToken = createAccessToken($user);
        $oldPlainText = 'original_token';

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken)->toBeInstanceOf(NewAccessToken::class);
        expect($newToken->plainTextAccessToken)->not->toBe($oldPlainText);
        expect($oldToken->fresh()->isRevoked())->toBeTrue();
    });

    it('creates new token with same configuration', function (): void {
        $user = createUser();
        $oldToken = Bearer::for($user)
            ->abilities(['users:read', 'posts:write'])
            ->environment('production')
            ->issue('sk', 'Original Key')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->type)->toBe($oldToken->type);
        expect($newToken->accessToken->name)->toBe($oldToken->name);
        expect($newToken->accessToken->abilities)->toBe($oldToken->abilities);
        expect($newToken->accessToken->environment)->toBe($oldToken->environment);
        expect($newToken->accessToken->owner_id)->toBe($oldToken->owner_id);
    });

    it('preserves IP restrictions on rotation', function (): void {
        $user = createUser();
        $oldToken = Bearer::for($user)
            ->allowedIps(['192.168.1.1', '10.0.0.1'])
            ->issue('sk', 'IP Restricted')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->allowed_ips)->toBe($oldToken->allowed_ips);
    });

    it('preserves domain restrictions on rotation', function (): void {
        $user = createUser();
        $oldToken = Bearer::for($user)
            ->allowedDomains(['example.com', 'app.example.com'])
            ->issue('pk', 'Domain Restricted')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->allowed_domains)->toBe($oldToken->allowed_domains);
    });

    it('preserves rate limit on rotation', function (): void {
        $user = createUser();
        $oldToken = Bearer::for($user)
            ->rateLimit(100)
            ->issue('sk', 'Rate Limited')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->rate_limit_per_minute)->toBe($oldToken->rate_limit_per_minute);
    });

    it('preserves metadata on rotation', function (): void {
        $user = createUser();
        $oldToken = Bearer::for($user)
            ->metadata(['app' => 'mobile', 'version' => '2.0'])
            ->issue('sk', 'Metadata Key')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->metadata)->toBe($oldToken->metadata);
    });

    it('maintains group association on rotation', function (): void {
        $user = createUser();
        $group = Bearer::for($user)->issueGroup(['sk', 'pk'], 'Rotatable Keys');
        $oldToken = $group->secretKey();

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->group_id)->toBe($group->id);
        expect($group->fresh()->accessTokens)->toHaveCount(3);
    });

    it('rotates with grace period mode', function (): void {
        $user = createUser();
        $oldToken = createAccessToken($user);

        $newToken = Bearer::rotate($oldToken, 'grace_period');

        expect($newToken)->toBeInstanceOf(NewAccessToken::class);
        expect($newToken->plainTextAccessToken)->not->toBe($oldToken->token);
    });

    it('does not reactivate a revoked token during grace period rotation', function (): void {
        $user = createUser();
        $oldToken = createAccessToken($user);
        $oldToken->revoke();

        $newToken = Bearer::rotate($oldToken, 'grace_period');

        expect($oldToken->fresh()->isRevoked())->toBeTrue()
            ->and($oldToken->fresh()->isValid())->toBeFalse()
            ->and($newToken->accessToken->expires_at)->toBeNull()
            ->and($newToken->accessToken->isValid())->toBeTrue();
    });

    it('does not postpone an earlier scheduled revocation during rotation', function (): void {
        $user = createUser();
        $revokesAt = now()->addMinutes(5)->startOfSecond();
        $oldToken = createAccessToken($user);
        $oldToken->update(['revoked_at' => $revokesAt]);

        Bearer::rotate($oldToken, 'grace_period');

        expect($oldToken->fresh()->revoked_at?->equalTo($revokesAt))->toBeTrue();
    });

    it('rotates with dual valid mode', function (): void {
        $user = createUser();
        $oldToken = createAccessToken($user);

        $newToken = Bearer::rotate($oldToken, 'dual_valid');

        expect($newToken)->toBeInstanceOf(NewAccessToken::class);
        expect($oldToken->fresh()->isRevoked())->toBeFalse();
        expect($newToken->accessToken->isValid())->toBeTrue();
    });

    it('generates unique token on rotation', function (): void {
        $user = createUser();
        $token = createAccessToken($user);
        $originalHash = $token->token;

        $newToken = Bearer::rotate($token, 'immediate');

        expect($newToken->accessToken->token)->not->toBe($originalHash);
        expect($newToken->plainTextAccessToken)->not->toContain($originalHash);
    });

    it('resets last_used_at on rotation', function (): void {
        $user = createUser();
        $oldToken = createAccessToken($user);
        $oldToken->update(['last_used_at' => now()->subHours(5)]);

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->last_used_at)->toBeNull();
    });

    it('preserves recoverable plaintext support on rotation', function (): void {
        config(['bearer.types.sk.revealable' => true]);

        $user = createUser();
        $oldToken = Bearer::for($user)->issue('sk', 'Recoverable')->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->hasRecoverablePlainText())->toBeTrue();
        expect($newToken->accessToken->revealPlainTextToken())->toBe($newToken->plainTextAccessToken);
    });

    it('preserves the exact finite expiration on rotation', function (): void {
        $user = createUser();
        $expiresAt = now()->addHour()->startOfSecond();
        $oldToken = Bearer::for($user)
            ->expiresAt($expiresAt)
            ->issue('sk', 'Expiring Key')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->getKey())->not->toBe($oldToken->getKey())
            ->and($newToken->accessToken->expires_at?->equalTo($expiresAt))->toBeTrue()
            ->and($newToken->accessToken->isValid())->toBeTrue()
            ->and($oldToken->fresh()->isValid())->toBeFalse();

        try {
            Date::setTestNow($expiresAt->copy()->addSecond());

            expect($newToken->accessToken->isValid())->toBeFalse();
        } finally {
            Date::setTestNow();
        }
    });

    it('keeps non-expiring replacements non-expiring', function (): void {
        $user = createUser();
        $oldToken = Bearer::for($user)
            ->issue('sk', 'Non-expiring Key')
            ->accessToken;

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->expires_at)->toBeNull()
            ->and($newToken->accessToken->isValid())->toBeTrue()
            ->and($oldToken->fresh()->isValid())->toBeFalse();
    });

    it('issues a valid replacement without moving an earlier revocation', function (): void {
        $user = createUser();
        $revokedAt = now()->subHour()->startOfSecond();
        $oldToken = createAccessToken($user);
        $oldToken->update(['revoked_at' => $revokedAt]);

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($oldToken->fresh()->revoked_at?->equalTo($revokedAt))->toBeTrue()
            ->and($newToken)->toBeInstanceOf(NewAccessToken::class)
            ->and($newToken->accessToken->isRevoked())->toBeFalse()
            ->and($newToken->accessToken->isValid())->toBeTrue();
    });

    it('keeps an expired replacement expired', function (): void {
        $user = createUser();
        $expiresAt = now()->subMinute()->startOfSecond();
        $oldToken = Bearer::for($user)
            ->expiresAt($expiresAt)
            ->issue('sk', 'Expired')
            ->accessToken;

        expect($oldToken->isExpired())->toBeTrue();

        $newToken = Bearer::rotate($oldToken, 'immediate');

        expect($newToken->accessToken->getKey())->not->toBe($oldToken->getKey())
            ->and($newToken->accessToken->expires_at?->equalTo($expiresAt))->toBeTrue()
            ->and($newToken->accessToken->isExpired())->toBeTrue()
            ->and($newToken->accessToken->isValid())->toBeFalse()
            ->and($oldToken->fresh()->isValid())->toBeFalse();
    });

    it('rotates multiple times sequentially', function (): void {
        $user = createUser();
        $token1 = createAccessToken($user);

        $token2 = Bearer::rotate($token1, 'immediate');
        $token3 = Bearer::rotate($token2->accessToken, 'immediate');

        expect($token1->fresh()->isRevoked())->toBeTrue();
        expect($token2->accessToken->fresh()->isRevoked())->toBeTrue();
        expect($token3->accessToken->isRevoked())->toBeFalse();
    });

    it('increments user token count on rotation with dual valid', function (): void {
        $user = createUser();
        $initialCount = $user->accessTokens()->count();
        $oldToken = createAccessToken($user);

        Bearer::rotate($oldToken, 'dual_valid');

        expect($user->fresh()->accessTokens()->count())->toBe($initialCount + 2);
    });

    it('maintains same token count on rotation with immediate', function (): void {
        $user = createUser();
        $token = createAccessToken($user);
        $countAfterCreate = $user->accessTokens()->count();

        Bearer::rotate($token, 'immediate');

        expect($user->fresh()->accessTokens()->count())->toBe($countAfterCreate + 1);
    });
});

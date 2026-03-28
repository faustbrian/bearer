<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Bearer\BearerServiceProvider;
use Cline\Bearer\Database\Models;
use Tests\Fixtures\UlidUser;
use Tests\Fixtures\User;

describe('Models Facade', function (): void {
    beforeEach(function (): void {
        Models::reset();
    });

    afterEach(function (): void {
        Models::reset();
    });

    test('forwards morphKeyMap calls to the registry', function (): void {
        // Arrange
        Models::morphKeyMap([
            User::class => 'uuid',
        ]);

        // Act
        $key = Models::getModelKeyFromClass(User::class);

        // Assert
        expect($key)->toBe('uuid');
    });

    test('forwards enforceMorphKeyMap calls to the registry', function (): void {
        // Arrange
        Models::enforceMorphKeyMap([
            UlidUser::class => 'ulid',
        ]);

        // Act
        $key = Models::getModelKeyFromClass(UlidUser::class);

        // Assert
        expect($key)->toBe('ulid');
    });

    test('reset clears facade-backed morph key mappings', function (): void {
        // Arrange
        Models::morphKeyMap([
            User::class => 'uuid',
        ]);
        expect(Models::getModelKeyFromClass(User::class))->toBe('uuid');

        // Act
        Models::reset();

        // Assert
        expect(Models::getModelKeyFromClass(User::class))->toBe('id');
    });
});

describe('BearerServiceProvider morph key maps', function (): void {
    beforeEach(function (): void {
        Models::reset();
    });

    afterEach(function (): void {
        Models::reset();
    });

    test('applies morphKeyMap config through the facade', function (): void {
        // Arrange
        $originalMorphKeyMap = config('bearer.morphKeyMap');
        $originalEnforceMorphKeyMap = config('bearer.enforceMorphKeyMap');

        try {
            config([
                'bearer.morphKeyMap' => [
                    User::class => 'uuid',
                ],
                'bearer.enforceMorphKeyMap' => [],
            ]);

            // Act
            new BearerServiceProvider(app())->bootingPackage();

            // Assert
            expect(Models::getModelKeyFromClass(User::class))->toBe('uuid');
        } finally {
            config([
                'bearer.morphKeyMap' => $originalMorphKeyMap,
                'bearer.enforceMorphKeyMap' => $originalEnforceMorphKeyMap,
            ]);
        }
    });

    test('applies enforceMorphKeyMap config through the facade', function (): void {
        // Arrange
        $originalMorphKeyMap = config('bearer.morphKeyMap');
        $originalEnforceMorphKeyMap = config('bearer.enforceMorphKeyMap');

        try {
            config([
                'bearer.morphKeyMap' => [],
                'bearer.enforceMorphKeyMap' => [
                    UlidUser::class => 'ulid',
                ],
            ]);

            // Act
            new BearerServiceProvider(app())->bootingPackage();

            // Assert
            expect(Models::getModelKeyFromClass(UlidUser::class))->toBe('ulid');
        } finally {
            config([
                'bearer.morphKeyMap' => $originalMorphKeyMap,
                'bearer.enforceMorphKeyMap' => $originalEnforceMorphKeyMap,
            ]);
        }
    });
});

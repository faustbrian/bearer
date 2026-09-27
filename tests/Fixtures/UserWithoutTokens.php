<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Test fixture user model WITHOUT HasAccessTokensTrait.
 *
 * Used to test edge cases where authenticated users don't support tokens.
 *
 * @internal
 */
#[Unguarded()]
#[Table(name: 'users')]
final class UserWithoutTokens extends Authenticatable
{
    use HasFactory;
}

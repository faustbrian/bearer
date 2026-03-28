<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Override;

/**
 * Test fixture user model WITHOUT HasAccessTokensTrait.
 *
 * Used to test edge cases where authenticated users don't support tokens.
 *
 * @internal
 */
final class UserWithoutTokens extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    #[Override()]
    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    #[Override()]
    protected $table = 'users';
}

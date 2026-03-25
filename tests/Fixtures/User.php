<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Cline\Bearer\Concerns\HasAccessTokensTrait;
use Cline\Bearer\Contracts\HasAccessTokensInterface;
use Cline\Warden\Database\Concerns\HasAbilities;
use Cline\Warden\Database\Concerns\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Override;

/**
 * Test fixture user model.
 *
 * Provides a minimal User implementation for testing the Bearer package.
 * Uses the HasAccessTokensTrait to enable token-based authentication functionality.
 *
 * This fixture model is used throughout the test suite to simulate real-world
 * usage of the package with a typical Eloquent user model.
 *
 * @author Brian Faust <brian@cline.sh>
 * @internal
 */
final class User extends Authenticatable implements HasAccessTokensInterface
{
    use HasFactory;
    use HasAccessTokensTrait;
    use HasAbilities;
    use HasRoles;

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

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
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Override;

/**
 * Test fixture user model with a ULID primary key.
 *
 * Used to prove that Bearer's polymorphic owner relations can resolve models
 * whose primary key column is not `id`.
 *
 * @author Brian Faust <brian@cline.sh>
 * @internal
 */
final class UlidUser extends Authenticatable implements HasAccessTokensInterface
{
    use HasAccessTokensTrait;
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    #[Override()]
    protected $guarded = [];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    #[Override()]
    protected $primaryKey = 'ulid';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    #[Override()]
    protected $table = 'ulid_users';
}

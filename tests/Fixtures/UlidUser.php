<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Cline\Bearer\Concerns\HasAccessTokensTrait;
use Cline\Bearer\Contracts\HasAccessTokensInterface;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Test fixture user model with a ULID primary key.
 *
 * Used to prove that Bearer's polymorphic owner relations can resolve
 * models whose primary key column is not `id`.
 *
 * @internal
 */
#[Unguarded()]
#[Table(name: 'ulid_users', key: 'ulid')]
final class UlidUser extends Authenticatable implements HasAccessTokensInterface
{
    use HasAccessTokensTrait;
    use HasFactory;
    use HasUlids;
}

<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Cline\Bearer\Concerns\HasAccessTokensTrait;
use Cline\Bearer\Contracts\HasAccessTokensInterface;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Test fixture user model whose morph key is mapped to a non-primary column.
 *
 * @internal
 */
#[Unguarded()]
#[Table(name: 'mapped_users')]
final class MappedOwner extends Authenticatable implements HasAccessTokensInterface
{
    use HasAccessTokensTrait;
    use HasFactory;
}

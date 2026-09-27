<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Cline\Bearer\Concerns\HasAccessTokensTrait;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Test fixture context model whose morph key is mapped to a non-primary column.
 *
 * @internal
 */
#[Unguarded()]
#[Table(name: 'mapped_contexts')]
final class MappedContext extends Authenticatable
{
    use HasAccessTokensTrait;
    use HasFactory;
}

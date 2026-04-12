<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Cline\Bearer\Concerns\HasAccessTokensTrait;
use Cline\Bearer\Contracts\HasAccessTokensInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Override;

/**
 * Test fixture user model whose morph key is mapped to a non-primary column.
 *
 * @internal
 */
final class MappedOwner extends Authenticatable implements HasAccessTokensInterface
{
    use HasAccessTokensTrait;
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
    protected $table = 'mapped_users';
}

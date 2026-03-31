<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Cline\Bearer\Concerns\HasAccessTokensTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Test fixture boundary model whose morph key is mapped to a non-primary
 * column.
 *
 * @internal
 */
final class MappedBoundary extends Authenticatable
{
    use HasAccessTokensTrait;
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapped_boundaries';
}

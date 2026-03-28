<?php declare(strict_types=1);

namespace Cline\Bearer\Contracts;

use Cline\Bearer\Database\Models\AccessToken;
use Illuminate\Database\Eloquent\Builder;

interface QueriesAbilitiesInterface
{
    /**
     * @param Builder<AccessToken> $query
     */
    public function applyAbilityConstraint(Builder $query, string $ability): void;
}

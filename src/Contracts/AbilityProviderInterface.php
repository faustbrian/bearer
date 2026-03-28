<?php declare(strict_types=1);

namespace Cline\Bearer\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AbilityProviderInterface
{
    public function can(HasAbilitiesInterface $token, string $ability, ?Model $authority = null): bool;
}

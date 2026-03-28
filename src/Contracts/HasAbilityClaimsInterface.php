<?php declare(strict_types=1);

namespace Cline\Bearer\Contracts;

interface HasAbilityClaimsInterface
{
    /**
     * @return array<int, string>
     */
    public function abilityClaims(): array;
}

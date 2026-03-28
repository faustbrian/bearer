<?php declare(strict_types=1);

namespace Cline\Bearer\AbilityProviders;

use Cline\Bearer\Contracts\AbilityProviderInterface;
use Cline\Bearer\Contracts\HasAbilitiesInterface;
use Cline\Bearer\Contracts\HasAbilityClaimsInterface;
use Cline\Bearer\Contracts\QueriesAbilitiesInterface;
use Cline\Bearer\Database\Models\AccessToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use function in_array;

final class ArrayAbilityProvider implements AbilityProviderInterface, QueriesAbilitiesInterface
{
    public function can(HasAbilitiesInterface $token, string $ability, ?Model $authority = null): bool
    {
        if ($token instanceof HasAbilityClaimsInterface) {
            $claims = $token->abilityClaims();

            return in_array('*', $claims, true) || in_array($ability, $claims, true);
        }

        return $token->can($ability);
    }

    /**
     * @param Builder<AccessToken> $query
     */
    public function applyAbilityConstraint(Builder $query, string $ability): void
    {
        $query->where(function (Builder $query) use ($ability): void {
            $query->whereJsonContains('abilities', '*')
                ->orWhereJsonContains('abilities', $ability);
        });
    }
}

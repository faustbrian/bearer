<?php declare(strict_types=1);

namespace Cline\Bearer\AbilityProviders;

use Cline\Bearer\Contracts\AbilityProviderInterface;
use Cline\Bearer\Contracts\HasAbilitiesInterface;
use Cline\Bearer\Exceptions\MissingAbilityProviderDependencyException;
use Cline\Warden\Contracts\ClipboardInterface;
use Illuminate\Database\Eloquent\Model;

use function interface_exists;
use function method_exists;
use function resolve;

/**
 * @psalm-immutable
 */
final readonly class WardenAbilityProvider implements AbilityProviderInterface
{
    public function __construct(
        private ArrayAbilityProvider $arrayProvider,
    ) {}

    public function can(HasAbilitiesInterface $token, string $ability, ?Model $authority = null): bool
    {
        if (!$this->arrayProvider->can($token, $ability, $authority)) {
            return false;
        }

        if (!$authority instanceof Model) {
            return true;
        }

        $clipboardClass = ClipboardInterface::class;

        if (!interface_exists($clipboardClass)) {
            throw MissingAbilityProviderDependencyException::forPackage('warden', 'cline/warden');
        }

        $clipboard = resolve($clipboardClass);

        if (!method_exists($clipboard, 'check')) {
            throw MissingAbilityProviderDependencyException::forPackage('warden', 'cline/warden');
        }

        return $clipboard->check($authority, $ability);
    }
}

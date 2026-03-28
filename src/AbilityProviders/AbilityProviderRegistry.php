<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\AbilityProviders;

use Cline\Bearer\Contracts\AbilityProviderInterface;
use Cline\Bearer\Exceptions\AbilityProviderNotFoundException;
use Cline\Bearer\Exceptions\CannotSetDefaultAbilityProviderException;
use Cline\Bearer\Exceptions\NoDefaultAbilityProviderException;

use function array_key_exists;
use function array_keys;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class AbilityProviderRegistry
{
    /** @var array<string, AbilityProviderInterface> */
    private array $providers = [];

    private ?string $defaultProvider = null;

    public function register(string $name, AbilityProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;

        if ($this->defaultProvider !== null) {
            return;
        }

        $this->defaultProvider = $name;
    }

    public function get(string $name): AbilityProviderInterface
    {
        if (!$this->has($name)) {
            throw AbilityProviderNotFoundException::forName($name);
        }

        return $this->providers[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->providers);
    }

    public function default(): AbilityProviderInterface
    {
        if ($this->defaultProvider === null) {
            throw NoDefaultAbilityProviderException::noDefault();
        }

        return $this->get($this->defaultProvider);
    }

    public function setDefault(string $name): void
    {
        if (!$this->has($name)) {
            throw CannotSetDefaultAbilityProviderException::cannotSetAsDefault($name);
        }

        $this->defaultProvider = $name;
    }

    /**
     * @return array<int, string>
     */
    public function all(): array
    {
        return array_keys($this->providers);
    }
}

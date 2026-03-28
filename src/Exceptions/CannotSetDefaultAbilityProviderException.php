<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotSetDefaultAbilityProviderException extends AbstractAbilityProviderNotRegisteredException
{
    public static function cannotSetAsDefault(string $name): self
    {
        return new self(sprintf('Cannot set unregistered ability provider "%s" as default.', $name));
    }
}

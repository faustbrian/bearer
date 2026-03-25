<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AbilityProviderInterface
{
    public function can(HasAbilitiesInterface $token, string $ability, ?Model $authority = null): bool;
}

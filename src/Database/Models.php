<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Database;

use Illuminate\Support\Facades\Facade;
use Override;

/**
 * Facade for accessing the Bearer model registry.
 *
 * Provides static access to polymorphic key configuration while keeping the
 * registry container-bound for Octane compatibility.
 *
 * @method static void   enforceMorphKeyMap(array<class-string, string> $map)
 * @method static string getModelKey(\Illuminate\Database\Eloquent\Model $model)
 * @method static string getModelKeyFromClass(string $class)
 * @method static void   morphKeyMap(array<class-string, string> $map)
 * @method static void   requireKeyMap()
 * @method static void   reset()
 *
 * @author Brian Faust <brian@cline.sh>
 * @see ModelRegistry
 */
final class Models extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string The service container binding key for the ModelRegistry
     */
    #[Override()]
    protected static function getFacadeAccessor(): string
    {
        return ModelRegistry::class;
    }
}

<?php declare(strict_types=1);

namespace Cline\Bearer\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * Facade for accessing the Bearer model registry.
 *
 * Provides static access to polymorphic key configuration while keeping the
 * registry container-bound for Octane compatibility.
 *
 * @method static void   enforceMorphKeyMap(array<class-string, string> $map)
 * @method static string getModelKey(Model $model)
 * @method static string getModelKeyFromClass(string $class)
 * @method static void   morphKeyMap(array<class-string, string> $map)
 * @method static void   requireKeyMap()
 * @method static void   reset()
 *
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

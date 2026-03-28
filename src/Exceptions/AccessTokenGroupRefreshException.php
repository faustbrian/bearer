<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a token group cannot be refreshed after creation.
 *
 * After creating a token group and its associated tokens, the group must be
 * refreshed to load the relationships. If the refresh returns null, it
 * indicates an unexpected database state that should not occur.
 */
final class AccessTokenGroupRefreshException extends RuntimeException implements BearerExceptionInterface
{
    /**
     * Create an exception for a failed token group refresh after creation.
     *
     * This occurs when a token group is successfully created in the database
     * but the subsequent refresh operation to load relationships returns null,
     * indicating an unexpected database state.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function afterCreation(): self
    {
        return new self('Failed to refresh token group after creation');
    }
}

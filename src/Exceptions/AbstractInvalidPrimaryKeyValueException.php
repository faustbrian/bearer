<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for invalid primary key value errors.
 *
 * This abstract exception is raised when non-string values are assigned to
 * model primary keys that require string types, such as UUIDs (Universally
 * Unique Identifiers) or ULIDs (Universally Unique Lexicographically Sortable
 * Identifiers). These identifier formats require string representation for
 * proper storage and querying.
 */
abstract class AbstractInvalidPrimaryKeyValueException extends InvalidArgumentException implements BearerExceptionInterface
{
    // Base exception class - see concrete implementations for specific factory methods
}

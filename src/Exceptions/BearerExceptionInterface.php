<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use Throwable;

/**
 * Marker interface for all Bearer package exceptions.
 *
 * Consumers can catch this interface to handle any exception thrown by the
 * Bearer package.
 */
interface BearerExceptionInterface extends Throwable
{
    // Marker interface - no methods required
}

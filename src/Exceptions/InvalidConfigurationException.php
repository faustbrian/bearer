<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for all configuration-related errors.
 *
 * Configuration errors can prevent the package from functioning correctly. This
 * exception occurs when required configuration values are missing or when
 * configuration values reference components that do not exist in the system.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class InvalidConfigurationException extends RuntimeException implements BearerException
{
    // Abstract base class - no factory methods
}

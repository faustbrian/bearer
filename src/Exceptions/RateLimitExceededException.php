<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

use function sprintf;

/**
 * Exception thrown when a token exceeds its configured rate limit.
 *
 * Rate limiting prevents abuse by restricting the number of requests that can
 * be made using a token within a specific time window. This exception occurs
 * when the request count exceeds the configured threshold, providing
 * information about when the limit will reset.
 */
final class RateLimitExceededException extends RuntimeException implements BearerExceptionInterface
{
    /**
     * Number of seconds until the rate limit resets and new requests can be
     * made.
     */
    private int $retryAfterSeconds = 0;

    /**
     * Create an exception for an exceeded rate limit.
     *
     * This occurs when the number of requests made with a token exceeds the
     * configured limit within the rate limiting time window.
     *
     * @param  int  $limit             The maximum number of requests allowed
     * @param  int  $retryAfterSeconds Number of seconds until the rate limit resets
     * @return self Exception instance with descriptive error message
     */
    public static function forToken(int $limit, int $retryAfterSeconds): self
    {
        $exception = new self(sprintf('Rate limit of %d requests exceeded. Retry after %d seconds.', $limit, $retryAfterSeconds));
        $exception->retryAfterSeconds = $retryAfterSeconds;

        return $exception;
    }

    /**
     * Get the number of seconds to wait before retrying.
     *
     * This value indicates when the rate limit window will reset and new
     * requests can be made with the token.
     *
     * @return int Number of seconds until retry is allowed
     */
    public function retryAfter(): int
    {
        return $this->retryAfterSeconds;
    }
}

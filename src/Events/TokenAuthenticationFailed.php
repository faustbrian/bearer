<?php declare(strict_types=1);

namespace Cline\Bearer\Events;

use Cline\Bearer\Database\Models\AccessToken;
use Cline\Bearer\Enums\AuditEvent;

/**
 * Event fired when authentication fails.
 *
 * Dispatched whenever a token authentication attempt fails, whether due to
 * invalid credentials, expired tokens, or other security violations. Useful for
 * security monitoring, rate limiting, and tracking attack patterns.
 *
 * @psalm-immutable
 */
final readonly class TokenAuthenticationFailed
{
    /**
     * Create a new Token Authentication Failed event.
     *
     * @param null|AccessToken     $token     The token that failed authentication (null if not found)
     * @param AuditEvent           $reason    The reason for authentication failure
     * @param null|string          $ipAddress The IP address from which the attempt originated
     * @param array<string, mixed> $context   Additional context about the failure
     */
    public function __construct(
        public ?AccessToken $token,
        public AuditEvent $reason,
        public ?string $ipAddress = null,
        public array $context = [],
    ) {}
}

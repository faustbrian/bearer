<?php declare(strict_types=1);

namespace Cline\Bearer\Events;

use Cline\Bearer\Database\Models\AccessToken;
use Cline\Bearer\Enums\RevocationMode;

/**
 * Event fired when a token is revoked.
 *
 * Dispatched whenever a personal access token is revoked, either manually or
 * automatically through the system. Useful for auditing revocations, triggering
 * cleanup actions, and tracking security-related token lifecycle events.
 *
 * @psalm-immutable
 */
final readonly class TokenRevoked
{
    /**
     * Create a new Token Revoked event.
     *
     * @param AccessToken    $token  The token that was revoked
     * @param RevocationMode $mode   The mode used for revocation
     * @param null|string    $reason Optional reason for the revocation
     */
    public function __construct(
        public AccessToken $token,
        public RevocationMode $mode,
        public ?string $reason = null,
    ) {}
}

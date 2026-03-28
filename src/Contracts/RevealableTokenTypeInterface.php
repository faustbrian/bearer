<?php declare(strict_types=1);

namespace Cline\Bearer\Contracts;

/**
 * Contract for token types that support later plaintext retrieval.
 *
 * Implement this alongside TokenTypeInterface when a token type intentionally
 * opts into storing a recoverable plaintext copy for legacy reveal flows.
 */
interface RevealableTokenTypeInterface
{
    /**
     * Determine if tokens of this type may be revealed after creation.
     *
     * @return bool True if tokens may be retrieved later, false otherwise
     */
    public function isRevealable(): bool;
}

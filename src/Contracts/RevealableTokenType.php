<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Contracts;

/**
 * Contract for token types that support later plaintext retrieval.
 *
 * Implement this alongside TokenType when a token type intentionally opts
 * into storing a recoverable plaintext copy for legacy reveal flows.
 *
 * @author Brian Faust <brian@cline.sh>
 */
interface RevealableTokenType
{
    /**
     * Determine if tokens of this type may be revealed after creation.
     *
     * @return bool True if tokens may be retrieved later, false otherwise
     */
    public function isRevealable(): bool;
}

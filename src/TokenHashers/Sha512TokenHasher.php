<?php declare(strict_types=1);

namespace Cline\Bearer\TokenHashers;

use Cline\Bearer\Contracts\TokenHasherInterface;

use function hash;
use function hash_equals;

/**
 * SHA-512 token hasher implementation.
 *
 * Uses SHA-512 hashing algorithm for token storage. Provides stronger security
 * than SHA-256 at the cost of slightly longer hash values.
 */
final class Sha512TokenHasher implements TokenHasherInterface
{
    /**
     * {@inheritDoc}
     */
    public function hash(string $token): string
    {
        return hash('sha512', $token);
    }

    /**
     * {@inheritDoc}
     */
    public function verify(string $token, string $hash): bool
    {
        return hash_equals($hash, $this->hash($token));
    }
}

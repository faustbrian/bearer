## Table of Contents

1. [Getting Started](#doc-docs-readme) (`docs/README.md`)
2. [Basic Usage](#doc-docs-basic-usage) (`docs/basic-usage.md`)
3. [Authentication](#doc-docs-authentication) (`docs/authentication.md`)
4. [Custom Token Types](#doc-docs-custom-token-types) (`docs/custom-token-types.md`)
5. [Token Metadata](#doc-docs-token-metadata) (`docs/token-metadata.md`)
6. [Derived Keys](#doc-docs-derived-keys) (`docs/derived-keys.md`)
7. [Revocation & Rotation](#doc-docs-revocation-rotation) (`docs/revocation-rotation.md`)
8. [IP & Domain Restrictions](#doc-docs-ip-domain-restrictions) (`docs/ip-domain-restrictions.md`)
9. [Rate Limiting](#doc-docs-rate-limiting) (`docs/rate-limiting.md`)
10. [Usage Tracking](#doc-docs-usage-tracking) (`docs/usage-tracking.md`)
11. [Audit Logging](#doc-docs-audit-logging) (`docs/audit-logging.md`)
12. [Token Generators](#doc-docs-token-generators) (`docs/token-generators.md`)
13. [Token Relationships](#doc-docs-token-relationships) (`docs/token-relationships.md`)
<a id="doc-docs-readme"></a>

## Requirements

Bearer requires PHP 8.4+ and Laravel 11+.

## Installation

Install Bearer with composer:

```bash
composer require cline/bearer
```

## Add the Trait

Add Bearer's trait to your user model:

```php
use Cline\Bearer\Concerns\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

## Run Migrations

First publish the migrations into your app's `migrations` directory:

```bash
php artisan vendor:publish --tag="bearer-migrations"
```

Then run the migrations:

```bash
php artisan migrate
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="bearer-config"
```

### Morph Type Configuration

Bearer supports different polymorphic relationship types. Configure this **before** running migrations:

```php
// config/bearer.php
return [
    'database' => [
        // Primary key type: 'numeric' (bigint), 'uuid', 'ulid'
        'primary_key' => env('BEARER_PRIMARY_KEY', 'numeric'),

        // Morph type: 'numeric', 'uuid', 'ulid', 'string'
        'morph_type' => env('BEARER_MORPH_TYPE', 'numeric'),

        // Table names (customize if needed)
        'tables' => [
            'tokens' => 'personal_access_tokens',
            'groups' => 'token_groups',
            'audit_logs' => 'token_audit_logs',
        ],

        // Database connection (null = default)
        'connection' => env('BEARER_DB_CONNECTION'),
    ],
];
```

### Morph Type Options

| Type | ID Column | Best For |
|------|-----------|----------|
| `numeric` | `unsignedBigInteger` | Traditional Laravel apps with auto-incrementing IDs |
| `uuid` | `uuid` | Distributed systems, privacy-focused apps |
| `ulid` | `ulid` (26 chars) | Time-ordered distributed IDs |
| `string` | `string` | Legacy systems, external ID integration |

### User Model Configuration

Your User model must match the configured morph type:

```php
// For UUID:
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
}

// For ULID:
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';
}
```

## Migrating from Sanctum

If you're migrating from Sanctum, add the required columns to your existing table:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('type', 10)->default('sk')->after('name');
            $table->string('environment', 10)->default('test')->after('type');
            $table->foreignId('group_id')->nullable()->after('tokenable_id');
            $table->json('allowed_ips')->nullable();
            $table->json('allowed_domains')->nullable();
            $table->unsignedInteger('rate_limit')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->index('type');
            $table->index('environment');
            $table->index('group_id');
        });

        Schema::create('token_groups', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('token_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_access_token_id')->constrained()->cascadeOnDelete();
            $table->string('event', 50);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
        });
    }
};
```

## Index Optimization

For high-volume applications, consider these additional indexes:

```php
Schema::table('personal_access_tokens', function (Blueprint $table) {
    $table->index('token');
    $table->index(['tokenable_type', 'tokenable_id', 'revoked_at']);
    $table->index(['type', 'environment']);
    $table->index('expires_at');
});

Schema::table('token_audit_logs', function (Blueprint $table) {
    $table->index('event');
    $table->index('created_at');
    $table->index(['personal_access_token_id', 'event', 'created_at']);
});
```

## Using the Facade

Whenever you use the `Bearer` facade in your code, remember to add this line to your namespace imports:

```php
use Cline\Bearer\Facades\Bearer;
```

## Next Steps

- **[Basic Usage](#doc-docs-basic-usage)** - Creating, validating, and managing tokens
- **[Authentication](#doc-docs-authentication)** - Integrating with Laravel authentication
- **[Custom Token Types](#doc-docs-custom-token-types)** - Defining typed tokens with abilities

<a id="doc-docs-basic-usage"></a>

## Issuing Tokens

Issue a single secret key (server-side only):

```php
use App\Models\User;
use Cline\Bearer\Facades\Bearer;

$user = User::find(1);
$token = Bearer::for($user)->issue(
    type: 'sk',
    name: 'Production API Key',
);

// The plain text token is only available at creation time
echo $token->plainTextToken; // sk_test_abc123...

// Access the token model
$token->accessToken->type;        // 'sk'
$token->accessToken->environment; // 'test'
$token->accessToken->name;        // 'Production API Key'
```

## Issuing Token Groups

Issue a group of related tokens (sk, pk, rk linked together):

```php
$group = Bearer::for($user)->issueGroup(
    types: ['sk', 'pk', 'rk'],
    name: 'Payment Integration Keys',
);

// Access individual tokens in the group
$secretKey = $group->secretKey();           // sk_test_...
$publishableKey = $group->publishableKey(); // pk_test_...
$restrictedKey = $group->restrictedKey();   // rk_test_...

// Find sibling tokens
$pkFromSk = $secretKey->sibling('pk'); // Get publishable key from secret key's group
```

## Configuring Tokens

Issue with custom configuration using the fluent API:

```php
$token = Bearer::for($user)
    ->environment('live')                          // Set environment
    ->abilities(['users:read', 'orders:write'])    // Custom abilities
    ->allowedIps(['192.168.1.0/24', '10.0.0.1'])   // IP restrictions
    ->allowedDomains(['*.example.com'])            // Domain restrictions
    ->rateLimit(100)                               // 100 requests per minute
    ->expiresIn(60 * 24 * 30)                      // Expires in 30 days
    ->issue('pk', 'Frontend Widget Key');
```

## Finding Tokens

```php
// Find by plain text token
$token = Bearer::findToken('sk_test_abc123...');

// Find by prefix (partial match)
$token = Bearer::findByPrefix('sk_test_abc');
```

## Using the HasApiTokens Trait

Add the trait to your User model:

```php
use Cline\Bearer\Concerns\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

Then use the convenience methods:

```php
// Create token via user model
$token = $user->createToken('sk', 'My Token');

// Create token group via user model
$group = $user->createTokenGroup(['sk', 'pk'], 'My Keys');

// Check current token abilities
if ($user->tokenCan('users:write')) {
    // User has write access
}

// Check token type
if ($user->tokenIs('sk')) {
    // Using a secret key
}

// Get current token environment
$env = $user->tokenEnvironment(); // 'test' or 'live'
```

## Next Steps

- **[Authentication](#doc-docs-authentication)** - Protecting routes and checking permissions
- **[Custom Token Types](#doc-docs-custom-token-types)** - Creating your own token types
- **[Token Metadata](#doc-docs-token-metadata)** - Attaching custom data to tokens

<a id="doc-docs-authentication"></a>

## Protecting Routes

Basic authentication with any valid token:

```php
// routes/api.php
use Illuminate\Support\Facades\Route;

Route::middleware('auth:bearer')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
```

## Checking Abilities

Require **all** specified abilities:

```php
Route::middleware(['auth:bearer', 'abilities:users:read,users:write'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
});
```

Require **any** of the specified abilities:

```php
Route::middleware(['auth:bearer', 'ability:admin,moderator'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

## Checking Token Type

Only allow secret keys (server-side endpoints):

```php
Route::middleware(['auth:bearer', 'token-type:sk'])->group(function () {
    Route::post('/webhooks', [WebhookController::class, 'handle']);
    Route::post('/payments', [PaymentController::class, 'charge']);
});
```

Only allow publishable keys (client-side endpoints):

```php
Route::middleware(['auth:bearer', 'token-type:pk'])->group(function () {
    Route::post('/checkout/session', [CheckoutController::class, 'createSession']);
});
```

Allow multiple token types:

```php
Route::middleware(['auth:bearer', 'token-type:sk,rk'])->group(function () {
    Route::get('/internal/status', [StatusController::class, 'index']);
});
```

## Checking Environment

Only allow live environment tokens:

```php
Route::middleware(['auth:bearer', 'environment:live'])->group(function () {
    Route::post('/payments/charge', [PaymentController::class, 'charge']);
});
```

Only allow test environment tokens:

```php
Route::middleware(['auth:bearer', 'environment:test'])->group(function () {
    Route::post('/test/webhook', [TestController::class, 'simulateWebhook']);
});
```

## Combining Middleware

Require: valid token + secret key + live environment + payment ability:

```php
Route::middleware([
    'auth:bearer',
    'token-type:sk',
    'environment:live',
    'abilities:payments:charge',
])->group(function () {
    Route::post('/payments/charge', [PaymentController::class, 'charge']);
});
```

## In Controllers

```php
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        // Check token type
        if ($user->tokenIs('pk')) {
            return $this->limitedResponse();
        }

        // Check abilities
        if ($user->tokenCan('admin')) {
            return $this->adminResponse();
        }

        // Check environment
        if ($user->tokenEnvironment() === 'test') {
            return $this->testResponse();
        }

        return $this->standardResponse();
    }
}
```

## Testing with actingAs

```php
use Cline\Bearer\Bearer;
use Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_user_can_access_api(): void
    {
        $user = User::factory()->create();

        // Act as user with specific abilities
        Bearer::actingAs($user, ['users:read', 'users:write']);

        $response = $this->getJson('/api/users');

        $response->assertOk();
    }

    public function test_secret_key_required(): void
    {
        $user = User::factory()->create();

        // Act as user with specific token type
        Bearer::actingAs($user, ['*'], 'sk');

        $response = $this->postJson('/api/webhooks');

        $response->assertOk();
    }
}
```

## Stateful Authentication (SPA/First-Party)

Configure stateful domains for session-based auth:

```php
// config/bearer.php
'stateful' => ['localhost', 'spa.example.com', '*.example.com']
```

These domains will use session-based auth (cookies) instead of tokens. Perfect for first-party SPAs where you don't want to expose tokens.

```php
// routes/api.php
Route::middleware('auth:bearer')->group(function () {
    // Works with both:
    // 1. Bearer tokens (Authorization: Bearer sk_test_...)
    // 2. Session cookies (for stateful domains)
    Route::get('/user', fn (Request $request) => $request->user());
});
```

## Next Steps

- **[IP & Domain Restrictions](#doc-docs-ip-domain-restrictions)** - Network-based access control
- **[Rate Limiting](#doc-docs-rate-limiting)** - Throttling token usage
- **[Audit Logging](#doc-docs-audit-logging)** - Recording token events

<a id="doc-docs-custom-token-types"></a>

## Using Configurable Token Types (Via Config)

Add custom types directly in your configuration:

```php
// config/bearer.php
return [
    'types' => [
        // Built-in types
        'sk' => ['class' => SecretTokenType::class],
        'pk' => ['class' => PublishableTokenType::class],
        'rk' => ['class' => RestrictedTokenType::class],

        // Custom type via config
        'wh' => [
            'prefix' => 'wh',
            'name' => 'Webhook',
            'server_side_only' => true,
            'default_abilities' => ['webhooks:receive'],
            'default_expiration' => null, // Never expires
            'default_rate_limit' => 10000, // High limit for webhooks
            'allowed_environments' => ['test', 'live'],
        ],

        // Another custom type
        'tmp' => [
            'prefix' => 'tmp',
            'name' => 'Temporary',
            'server_side_only' => false,
            'default_abilities' => ['read'],
            'default_expiration' => 60, // 1 hour
            'default_rate_limit' => 100,
            'allowed_environments' => ['test'],
        ],
    ],
];
```

Now use them:

```php
$webhookToken = Bearer::for($user)->issue('wh', 'Stripe Webhooks');
$tempToken = Bearer::for($user)->issue('tmp', 'One-time Access');
```

## Creating a Custom Token Type Class

```php
use Cline\Bearer\TokenTypes\AbstractTokenType;

final class WebhookTokenType extends AbstractTokenType
{
    public function __construct()
    {
        parent::__construct(
            name: 'Webhook',
            prefix: 'wh',
            defaultAbilities: ['webhooks:receive', 'webhooks:verify'],
            defaultExpiration: null, // Never expires
            defaultRateLimit: 50000, // Very high limit
            allowedEnvironments: ['test', 'live'],
            serverSideOnly: true,
        );
    }
}
```

Register in config:

```php
// config/bearer.php
'types' => [
    'wh' => ['class' => App\Bearer\WebhookTokenType::class],
],
```

## Creating a Scoped Token Type

For multi-tenant applications where each tenant has their own token type:

```php
final class TenantTokenType extends AbstractTokenType
{
    public function __construct(
        private readonly string $tenantId,
    ) {
        parent::__construct(
            name: "Tenant {$tenantId}",
            prefix: "t{$tenantId}",
            defaultAbilities: ['tenant:access'],
            defaultExpiration: 60 * 24 * 365, // 1 year
            defaultRateLimit: 1000,
            allowedEnvironments: ['live'],
            serverSideOnly: false,
        );
    }
}
```

Register dynamically in a service provider:

```php
use Cline\Bearer\TokenTypes\TokenTypeRegistry;

$this->app->afterResolving(TokenTypeRegistry::class, function (TokenTypeRegistry $registry) {
    foreach (Tenant::all() as $tenant) {
        $registry->register(
            "t{$tenant->id}",
            new TenantTokenType($tenant->id)
        );
    }
});
```

## Implementing the TokenType Interface Directly

```php
use Cline\Bearer\Contracts\TokenType;

final class ApiKeyTokenType implements TokenType
{
    public function name(): string
    {
        return 'API Key';
    }

    public function prefix(): string
    {
        return 'api';
    }

    public function defaultAbilities(): array
    {
        return config('api.default_abilities', ['api:read']);
    }

    public function defaultExpiration(): ?int
    {
        return null; // Never expires
    }

    public function defaultRateLimit(): ?int
    {
        return config('api.rate_limit', 1000);
    }

    public function allowedEnvironments(): array
    {
        return ['test', 'live', 'staging'];
    }

    public function isServerSideOnly(): bool
    {
        return true;
    }
}
```

## Token Types with Custom Generators

Specify a different generator per token type:

```php
// config/bearer.php
'types' => [
    'sk' => [
        'class' => SecretTokenType::class,
        'generator' => 'seam', // Stripe-style: sk_test_abc123
    ],
    'pk' => [
        'class' => PublishableTokenType::class,
        'generator' => 'uuid', // UUID-style: pk_test_550e8400-e29b-...
    ],
    'legacy' => [
        'prefix' => 'leg',
        'name' => 'Legacy',
        'generator' => 'random', // Sanctum-style random string
    ],
],
```

## Validating Token Types

```php
use Cline\Bearer\TokenTypes\TokenTypeRegistry;

$registry = app(TokenTypeRegistry::class);

// Check if type exists
if ($registry->has('custom')) {
    $type = $registry->get('custom');
}

// Find type by prefix (useful when parsing incoming tokens)
$type = $registry->findByPrefix('wh'); // Returns WebhookTokenType

// Get all registered types
$allTypes = $registry->all(); // ['sk' => ..., 'pk' => ..., 'wh' => ...]
```

## Next Steps

- **[Token Generators](#doc-docs-token-generators)** - Custom token generation strategies
- **[Token Metadata](#doc-docs-token-metadata)** - Attaching custom data to tokens

<a id="doc-docs-token-metadata"></a>

## Storing Metadata

Add metadata during token creation:

```php
use App\Models\User;
use Cline\Bearer\Facades\Bearer;

$user = User::find(1);

$token = Bearer::for($user)
    ->metadata([
        'purpose' => 'payment_processing',
        'created_by' => 'admin@example.com',
        'department' => 'engineering',
    ])
    ->issue('sk', 'Payment Service Key');
```

Complex metadata structures:

```php
$token = Bearer::for($user)
    ->metadata([
        'integration' => [
            'type' => 'webhook',
            'version' => '2.0',
            'events' => ['payment.completed', 'payment.failed'],
        ],
        'limits' => [
            'max_amount' => 10000,
            'currency' => 'USD',
        ],
        'contact' => [
            'email' => 'dev@example.com',
            'slack' => '#payments-alerts',
        ],
    ])
    ->issue('sk', 'Webhook Integration');
```

## Reading Metadata

```php
$token = $user->currentAccessToken();

// Get all metadata
$metadata = $token->metadata; // Returns array or null

// Check if metadata exists
if ($token->metadata !== null) {
    $purpose = $token->metadata['purpose'] ?? 'general';
}

// Safe access with null coalescing
$department = $token->metadata['department'] ?? 'unknown';
$maxAmount = $token->metadata['limits']['max_amount'] ?? 0;
```

## Updating Metadata

Replace all metadata:

```php
$token->accessToken->update([
    'metadata' => [
        'purpose' => 'updated_purpose',
        'version' => '2.0',
    ],
]);
```

Merge with existing metadata:

```php
$token->accessToken->update([
    'metadata' => array_merge(
        $token->accessToken->metadata ?? [],
        ['last_reviewed' => now()->toISOString()]
    ),
]);
```

Add a single key:

```php
$currentMetadata = $token->accessToken->metadata ?? [];
$currentMetadata['audit_note'] = 'Reviewed by security team';
$token->accessToken->update(['metadata' => $currentMetadata]);
```

Remove a key:

```php
$currentMetadata = $token->accessToken->metadata ?? [];
unset($currentMetadata['temporary_flag']);
$token->accessToken->update(['metadata' => $currentMetadata]);
```

Clear all metadata:

```php
$token->accessToken->update(['metadata' => null]);
```

## Querying by Metadata

```php
use Cline\Bearer\Database\Models\PersonalAccessToken;

// Find tokens by metadata value (JSON queries)
$paymentTokens = PersonalAccessToken::query()
    ->whereJsonContains('metadata->purpose', 'payment_processing')
    ->get();

// Find tokens by nested metadata
$webhookTokens = PersonalAccessToken::query()
    ->where('metadata->integration->type', 'webhook')
    ->get();

// Find tokens with specific event subscriptions
$paymentEventTokens = PersonalAccessToken::query()
    ->whereJsonContains('metadata->integration->events', 'payment.completed')
    ->get();

// Find tokens by department
$engineeringTokens = PersonalAccessToken::query()
    ->where('metadata->department', 'engineering')
    ->where('revoked_at', null)
    ->get();
```

## Use Cases

### Track Token Creator/Approver

```php
$token = Bearer::for($user)
    ->metadata([
        'created_by' => auth()->id(),
        'approved_by' => $approver->id,
        'approval_date' => now()->toISOString(),
        'ticket' => 'JIRA-1234',
    ])
    ->issue('sk', 'Production API Key');
```

### Customer/Tenant Identification

```php
$token = Bearer::for($user)
    ->metadata([
        'customer_id' => $customer->id,
        'plan' => 'enterprise',
        'features' => ['advanced_analytics', 'custom_reports'],
    ])
    ->issue('sk', "Customer {$customer->name}");
```

### Integration-Specific Configuration

```php
$token = Bearer::for($user)
    ->metadata([
        'webhook_url' => 'https://example.com/webhooks',
        'webhook_secret' => 'whsec_...',
        'retry_policy' => ['max_attempts' => 3, 'backoff' => 'exponential'],
    ])
    ->issue('sk', 'Webhook Delivery');
```

### Compliance and Audit Tracking

```php
$token = Bearer::for($user)
    ->metadata([
        'compliance' => [
            'pci_scope' => true,
            'data_classification' => 'confidential',
            'review_required_by' => now()->addMonths(3)->toISOString(),
        ],
        'security_review' => [
            'reviewer' => 'security@example.com',
            'date' => now()->toISOString(),
            'findings' => 'none',
        ],
    ])
    ->issue('sk', 'PCI Compliant Key');
```

### Feature Flags per Token

```php
$token = Bearer::for($user)
    ->metadata([
        'features' => [
            'beta_api_v2' => true,
            'experimental_search' => false,
            'legacy_support' => true,
        ],
    ])
    ->issue('sk', 'Beta Tester Key');

// Then in your API:
$features = $request->user()->currentAccessToken()->metadata['features'] ?? [];
if ($features['beta_api_v2'] ?? false) {
    // Use v2 API logic
}
```

## Metadata Validation

You can validate metadata in a custom token type:

```php
use Cline\Bearer\TokenTypes\AbstractTokenType;

final class ValidatedTokenType extends AbstractTokenType
{
    public function validateMetadata(array $metadata): void
    {
        $required = ['purpose', 'department'];

        foreach ($required as $key) {
            if (!isset($metadata[$key])) {
                throw new InvalidArgumentException("Metadata must include '{$key}'");
            }
        }

        $allowedPurposes = ['development', 'production', 'testing'];
        if (!in_array($metadata['purpose'], $allowedPurposes, true)) {
            throw new InvalidArgumentException('Invalid purpose');
        }
    }
}
```

## Next Steps

- **[Revocation & Rotation](#doc-docs-revocation-rotation)** - Token lifecycle management
- **[Audit Logging](#doc-docs-audit-logging)** - Recording token events

<a id="doc-docs-derived-keys"></a>

## Overview

Token derivation is useful for reseller scenarios where master tokens need to issue customer tokens without those customers requiring full accounts in your system. Child tokens:

- **Inherit restrictions** from their parent (IP/domain/rate limits)
- **Have limited abilities** (subset of parent abilities)
- **Cannot outlive their parent** (expiration ≤ parent expiration)
- **Are automatically revoked** when parent is revoked (with cascade_descendants strategy)

## Prerequisites

Token derivation requires the `cline/ancestry` package for hierarchical management:

```bash
composer require cline/ancestry
php artisan vendor:publish --tag=ancestry-migrations
php artisan migrate
```

## Configuration

Configure derivation in `config/bearer.php`:

```php
'derivation' => [
    'enabled' => true,
    'max_depth' => 3, // master -> reseller -> customer
    'hierarchy_type' => 'token_derivation',
    'inherit_restrictions' => true,
    'enforce_ability_subset' => true,
    'enforce_expiration' => true,
],
```

## Basic Usage

### Create a Master Token

```php
use Cline\Bearer\Facades\Bearer;

$reseller = User::find(1);

$masterToken = Bearer::for($reseller)
    ->abilities(['invoices:read', 'invoices:write', 'webhooks:receive'])
    ->allowedIps(['192.168.1.0/24'])
    ->expiresAt(now()->addYear())
    ->issue('sk', 'Reseller Master Key');
```

### Derive a Child Token

```php
$customerToken = Bearer::derive($masterToken->accessToken)
    ->abilities(['invoices:read', 'webhooks:receive']) // Subset of parent
    ->metadata([
        'reseller_customer_id' => 'cust_xyz',
        'billing_account' => 'acc_789',
    ])
    ->expiresAt(now()->addMonths(6)) // Must be <= parent expiration
    ->as('Customer XYZ Key');

// Use the plain-text token once
echo $customerToken->plainTextToken;
// sk_live_abc123...
```

## Derived Metadata

Store derivation-specific context separate from main token metadata:

```php
$customerToken = Bearer::derive($masterToken->accessToken)
    ->abilities(['orders:read'])
    ->metadata([
        'reseller_id' => 'res_123',
        'customer_id' => 'cust_abc',
        'plan' => 'premium',
        'created_by' => 'integration_v2',
    ])
    ->as('Customer ABC');

// Access derived metadata
$metadata = $customerToken->accessToken->derived_metadata;
// ['reseller_id' => 'res_123', ...]
```

## Querying the Hierarchy

### Get Parent Token

```php
$parent = $customerToken->accessToken->parentToken();

if ($parent) {
    echo "Parent: {$parent->name}";
}
```

### Get Direct Children

```php
$children = $masterToken->accessToken->childTokens();

foreach ($children as $child) {
    echo "Child: {$child->name}\n";
}
```

### Get All Descendants

```php
$allDescendants = $masterToken->accessToken->allDescendantTokens();

echo "Total descendants: {$allDescendants->count()}";
```

### Check Hierarchy Position

```php
// Check if token is a root (no parent)
if ($masterToken->accessToken->isRootToken()) {
    echo "This is a master token";
}

// Check if token can derive children
if ($masterToken->accessToken->canDeriveTokens()) {
    echo "Can create child tokens";
}
```

## Multi-Level Hierarchies

Create nested derivation hierarchies up to the configured `max_depth`:

```php
// Level 0: Platform master
$platform = Bearer::for($admin)
    ->issue('sk', 'Platform Master', abilities: ['*']);

// Level 1: Reseller
$reseller = Bearer::derive($platform->accessToken)
    ->abilities(['customers:manage', 'billing:read'])
    ->as('Reseller Key');

// Level 2: Customer
$customer = Bearer::derive($reseller->accessToken)
    ->abilities(['billing:read'])
    ->as('Customer Key');

// Check depth (0-indexed)
$depth = $customer->accessToken->getAncestryDepth('token_derivation');
// 2
```

## Revocation Strategies

### Cascade Descendants

Revoke a master token and **all** derived tokens:

```php
use Cline\Bearer\Facades\Bearer;

Bearer::revoke($masterToken->accessToken)->withDescendants();

// All children and grandchildren are now revoked
```

### Check Affected Tokens

```php
use Cline\Bearer\RevocationStrategies\CascadeDescendantsStrategy;

$strategy = new CascadeDescendantsStrategy();
$affected = $strategy->getAffectedTokens($masterToken->accessToken);

echo "Revoking will affect {$affected->count()} tokens";
```

## Validation Rules

### Ability Subset

Child abilities must be a subset of parent abilities:

```php
$parent = Bearer::for($user)
    ->issue('sk', 'Parent', abilities: ['users:read', 'posts:read']);

// ✅ Valid: subset of parent
$child = Bearer::derive($parent->accessToken)
    ->abilities(['users:read'])->as('Child');

// ❌ Invalid: 'users:write' not in parent
$child = Bearer::derive($parent->accessToken)
    ->abilities(['users:read', 'users:write'])->as('Child');
// Throws InvalidDerivedAbilitiesException
```

### Expiration

Child expiration must be ≤ parent expiration:

```php
$parent = Bearer::for($user)
    ->expiresAt(now()->addDays(7))
    ->issue('sk', 'Parent', abilities: ['*']);

// ✅ Valid: expires before parent
$child = Bearer::derive($parent->accessToken)
    ->abilities(['*'])->expiresAt(now()->addDays(3))->as('Child');

// ❌ Invalid: expires after parent
$child = Bearer::derive($parent->accessToken)
    ->abilities(['*'])->expiresAt(now()->addDays(14))->as('Child');
// Throws InvalidDerivedExpirationException
```

### Parent Validity

Cannot derive from revoked or expired tokens:

```php
$parent = Bearer::for($user)->issue('sk', 'Parent', abilities: ['*']);
$parent->accessToken->revoke();

// ❌ Invalid: parent is revoked
$child = Bearer::derive($parent->accessToken)
    ->abilities(['*'])->as('Child');
// Throws CannotDeriveTokenException
```

### Maximum Depth

Cannot exceed configured `max_depth`:

```php
// config: max_depth = 2

$level0 = Bearer::for($user)->issue('sk', 'Level 0', abilities: ['*']);
$level1 = Bearer::derive($level0->accessToken)->abilities(['*'])->as('Level 1');
$level2 = Bearer::derive($level1->accessToken)->abilities(['*'])->as('Level 2');

// ❌ Invalid: exceeds max depth
$level3 = Bearer::derive($level2->accessToken)->abilities(['*'])->as('Level 3');
// Throws CannotDeriveTokenException
```

## Inherited Restrictions

Child tokens automatically inherit parent restrictions:

```php
$parent = Bearer::for($user)
    ->allowedIps(['192.168.1.1', '10.0.0.0/8'])
    ->allowedDomains(['api.example.com'])
    ->rateLimit(1000)
    ->issue('sk', 'Parent', abilities: ['*']);

$child = Bearer::derive($parent->accessToken)
    ->abilities(['users:read'])->as('Child');

// Child inherits all restrictions
$child->accessToken->allowed_ips; // ['192.168.1.1', '10.0.0.0/8']
$child->accessToken->allowed_domains; // ['api.example.com']
$child->accessToken->rate_limit_per_minute; // 1000
```

## Audit Logging

Derivation events are automatically logged:

```php
use Cline\Bearer\Enums\AuditEvent;

$child = Bearer::derive($parent->accessToken)
    ->abilities(['*'])->as('Child');

// Check audit log
$auditLog = $child->accessToken->auditLogs()
    ->where('event', AuditEvent::Derived)
    ->first();

$auditLog->metadata;
// [
//     'parent_token_id' => 123,
//     'depth' => 1,
// ]
```

## Reseller Use Case Example

Complete example for a reseller platform:

```php
use Cline\Bearer\Facades\Bearer;

// 1. Reseller signs up and gets master key
$reseller = User::create([
    'name' => 'Acme Reseller',
    'email' => 'admin@acme.com',
]);

$resellerMaster = Bearer::for($reseller)
    ->abilities(['customers:manage', 'billing:read', 'webhooks:receive'])
    ->environment('live')
    ->issue('sk', 'Acme Master Key');

// 2. Reseller integrates and creates customer keys
foreach ($reseller->customers as $customer) {
    $customerKey = Bearer::derive($resellerMaster->accessToken)
        ->abilities(['billing:read', 'webhooks:receive'])
        ->metadata([
            'reseller_id' => $reseller->id,
            'customer_id' => $customer->id,
            'plan' => $customer->plan,
        ])
        ->expiresAt($customer->subscription_ends_at)
        ->as("Customer: {$customer->name}");

    // Send to customer
    $customer->notify(new ApiKeyCreated($customerKey->plainTextToken));
}

// 3. Customer makes API requests with their derived key
// The key is scoped to their data via derived_metadata

// 4. Reseller revokes all customer keys at once
Bearer::revoke($resellerMaster->accessToken)->withDescendants();
```

## Best Practices

1. **Use derived_metadata** for customer/reseller context instead of main metadata
2. **Revoke hierarchically** using `cascade_descendants` for master token invalidation
3. **Set reasonable depth limits** (3 levels usually sufficient: platform → reseller → customer)
4. **Inherit restrictions** to maintain security boundaries
5. **Log derivation events** for audit trails and billing
6. **Validate abilities** before derivation to provide clear error messages
7. **Document hierarchy structure** for your integration partners

## Related Documentation

- [Basic Usage](#doc-docs-basic-usage) - Creating and managing tokens
- [Revocation & Rotation](#doc-docs-revocation-rotation) - Token lifecycle management
- [Token Metadata](#doc-docs-token-metadata) - Attaching and querying metadata
- [Audit Logging](#doc-docs-audit-logging) - Recording token events

<a id="doc-docs-revocation-rotation"></a>

## Revoking Tokens

```php
use App\Models\User;
use Cline\Bearer\Facades\Bearer;

$user = User::find(1);
$token = Bearer::for($user)->issue('sk', 'API Key');

// Simple revocation (only this token)
Bearer::revoke($token->accessToken);

// Check if revoked
$token->accessToken->isRevoked(); // true
```

## Revocation Modes

```php
use Cline\Bearer\Enums\RevocationMode;

// Create a token group first
$group = Bearer::for($user)->issueGroup(['sk', 'pk', 'rk'], 'Payment Keys');
$secretKey = $group->secretKey();
```

### None

Only revoke the specified token:

```php
Bearer::revoke($secretKey, RevocationMode::None);
// Result: Only sk is revoked, pk and rk remain valid
```

### Cascade

Revoke all tokens in the group:

```php
$group = Bearer::for($user)->issueGroup(['sk', 'pk', 'rk'], 'Keys');
Bearer::revoke($group->secretKey(), RevocationMode::Cascade);
// Result: sk, pk, and rk are ALL revoked
```

### Partial

Revoke only server-side tokens (sk, rk) but keep pk valid:

```php
$group = Bearer::for($user)->issueGroup(['sk', 'pk', 'rk'], 'Keys');
Bearer::revoke($group->secretKey(), RevocationMode::Partial);
// Result: sk and rk are revoked, pk remains valid
```

### Timed

Schedule revocation for later (default 60 minutes):

```php
$group = Bearer::for($user)->issueGroup(['sk', 'pk', 'rk'], 'Keys');
Bearer::revoke($group->secretKey(), RevocationMode::Timed);
// Result: Token will be invalid after 60 minutes
```

## Rotating Tokens

```php
$token = Bearer::for($user)->issue('sk', 'API Key');

// Simple rotation (immediate invalidation of old token)
$newToken = Bearer::rotate($token->accessToken);

// The new token has the same configuration
echo $newToken->plainTextToken; // sk_test_newtoken...

// Old token is now invalid
$token->accessToken->fresh()->isRevoked(); // true
```

## Rotation Modes

```php
use Cline\Bearer\Enums\RotationMode;
```

### Immediate

Old token invalid immediately (default):

```php
$newToken = Bearer::rotate($token->accessToken, RotationMode::Immediate);
// Result: Old token is revoked immediately
```

### Grace Period

Old token valid for a grace period (default 60 minutes):

```php
$newToken = Bearer::rotate($token->accessToken, RotationMode::GracePeriod);
// Result: Both tokens work for 60 minutes, then old token becomes invalid
```

### Dual Valid

Both tokens remain valid until explicit revocation:

```php
$newToken = Bearer::rotate($token->accessToken, RotationMode::DualValid);
// Result: Both tokens work indefinitely until you manually revoke the old one
```

## Fluent Revocation API

```php
use Cline\Bearer\Conductors\TokenRevocationConductor;

$conductor = new TokenRevocationConductor(app(BearerManager::class), $token->accessToken);
$conductor
    ->using(RevocationMode::Cascade)
    ->withReason('Security incident - compromised credentials')
    ->revoke();
```

## Fluent Rotation API

```php
use Cline\Bearer\Conductors\TokenRotationConductor;

$conductor = new TokenRotationConductor(app(BearerManager::class), $token->accessToken);
$newToken = $conductor
    ->using(RotationMode::GracePeriod)
    ->withGracePeriod(120) // 2 hours
    ->rotate();
```

## Batch Operations

```php
// Revoke all tokens for a user
$user->tokens()->update(['revoked_at' => now()]);

// Revoke all tokens of a specific type
$user->tokens()->where('type', 'pk')->update(['revoked_at' => now()]);

// Revoke all test environment tokens
$user->tokens()->where('environment', 'test')->update(['revoked_at' => now()]);

// Revoke entire group
$group->revokeAll();
```

## Next Steps

- **[Audit Logging](#doc-docs-audit-logging)** - Track all revocation and rotation events
- **[Usage Tracking](#doc-docs-usage-tracking)** - Monitor token activity before revoking

<a id="doc-docs-ip-domain-restrictions"></a>

## IP Restrictions

Restrict token to specific IP addresses:

```php
use App\Models\User;
use Cline\Bearer\Facades\Bearer;

$token = Bearer::for($user)
    ->allowedIps(['192.168.1.100', '10.0.0.50'])
    ->issue('sk', 'Office Server Key');
```

Restrict to IP ranges (CIDR notation):

```php
$token = Bearer::for($user)
    ->allowedIps([
        '192.168.1.0/24',    // All IPs in 192.168.1.x
        '10.0.0.0/8',        // All IPs in 10.x.x.x
        '172.16.0.0/12',     // Private network range
    ])
    ->issue('sk', 'Internal Network Key');
```

Mix specific IPs and ranges:

```php
$token = Bearer::for($user)
    ->allowedIps([
        '203.0.113.50',      // Specific production server
        '198.51.100.0/24',   // Staging network
        '2001:db8::/32',     // IPv6 range
    ])
    ->issue('sk', 'Production Key');
```

## Domain Restrictions

Domain restrictions are primarily for Publishable Keys used in client-side applications.

Restrict to specific domains:

```php
$token = Bearer::for($user)
    ->allowedDomains(['example.com', 'www.example.com'])
    ->issue('pk', 'Website Widget Key');
```

Wildcard subdomains:

```php
$token = Bearer::for($user)
    ->allowedDomains([
        '*.example.com',     // All subdomains of example.com
        'example.com',       // Root domain
    ])
    ->issue('pk', 'Multi-site Key');
```

Multiple domains (for SaaS white-label scenarios):

```php
$token = Bearer::for($user)
    ->allowedDomains([
        '*.myapp.com',
        '*.customer1.com',
        '*.customer2.com',
        'localhost',         // For development
        'localhost:3000',    // With port
    ])
    ->issue('pk', 'White-label Key');
```

## Combining IP and Domain Restrictions

Both must pass for the token to be valid:

```php
$token = Bearer::for($user)
    ->allowedIps(['192.168.1.0/24'])
    ->allowedDomains(['*.internal.example.com'])
    ->issue('pk', 'Internal Dashboard Key');
```

## Stripe-Style Patterns

Secret keys: IP restricted, no domain restriction:

```php
$secretKey = Bearer::for($user)
    ->allowedIps(['production-server-ip'])
    ->environment('live')
    ->issue('sk', 'Production Server');
```

Publishable keys: Domain restricted, no IP restriction:

```php
$publishableKey = Bearer::for($user)
    ->allowedDomains(['checkout.example.com', '*.example.com'])
    ->environment('live')
    ->issue('pk', 'Checkout Widget');
```

Restricted keys: Both restrictions for microservices:

```php
$restrictedKey = Bearer::for($user)
    ->allowedIps(['10.0.0.0/8']) // Internal network only
    ->environment('live')
    ->issue('rk', 'Payment Microservice');
```

## Updating Restrictions

Add IPs to existing token:

```php
$token->accessToken->update([
    'allowed_ips' => array_merge(
        $token->accessToken->allowed_ips ?? [],
        ['new-ip-address']
    ),
]);
```

Replace all allowed domains:

```php
$token->accessToken->update([
    'allowed_domains' => ['new-domain.com', '*.new-domain.com'],
]);
```

Remove all restrictions:

```php
$token->accessToken->update([
    'allowed_ips' => null,
    'allowed_domains' => null,
]);
```

## Checking Restrictions Programmatically

```php
$token = $user->currentAccessToken();

// Check if token has IP restrictions
if ($token->allowed_ips !== null) {
    $allowedIps = $token->allowed_ips;
}

// Check if token has domain restrictions
if ($token->allowed_domains !== null) {
    $allowedDomains = $token->allowed_domains;
}
```

## Handling Restriction Errors

```php
use Cline\Bearer\Exceptions\IpRestrictionException;
use Cline\Bearer\Exceptions\DomainRestrictionException;

// In app/Exceptions/Handler.php:
$this->renderable(function (IpRestrictionException $e) {
    return response()->json([
        'error' => 'ip_restricted',
        'message' => 'This API key is not allowed from your IP address.',
    ], 403);
});

$this->renderable(function (DomainRestrictionException $e) {
    return response()->json([
        'error' => 'domain_restricted',
        'message' => 'This API key is not allowed from this domain.',
    ], 403);
});
```

## Development & Testing

For development, include localhost:

```php
$token = Bearer::for($user)
    ->allowedDomains([
        'localhost',
        'localhost:3000',
        '127.0.0.1',
        '*.example.com',
    ])
    ->issue('pk', 'Development Key');
```

For testing, you might want no restrictions:

```php
$token = Bearer::for($user)
    ->environment('test')
    ->issue('pk', 'Test Key');
// Test environment tokens often have relaxed or no restrictions by default
```

## Next Steps

- **[Rate Limiting](#doc-docs-rate-limiting)** - Throttling token usage
- **[Authentication](#doc-docs-authentication)** - Middleware for checking restrictions

<a id="doc-docs-rate-limiting"></a>

## Default Rate Limits (Per Token Type)

Each token type can have a default rate limit:

```php
// config/bearer.php
return [
    'types' => [
        'sk' => [
            'class' => SecretTokenType::class,
            // No rate limit for secret keys (server-to-server)
        ],
        'pk' => [
            'prefix' => 'pk',
            'name' => 'Publishable',
            'default_rate_limit' => 100, // 100 requests per minute
        ],
        'rk' => [
            'prefix' => 'rk',
            'name' => 'Restricted',
            'default_rate_limit' => 1000, // Higher for internal services
        ],
    ],
];
```

## Custom Rate Limits Per Token

Override default rate limit for specific token:

```php
use App\Models\User;
use Cline\Bearer\Facades\Bearer;

$user = User::find(1);

$token = Bearer::for($user)
    ->rateLimit(500) // 500 requests per minute
    ->issue('pk', 'High-traffic Widget');
```

No rate limit (null = unlimited):

```php
$unlimitedToken = Bearer::for($user)
    ->rateLimit(null)
    ->issue('sk', 'Internal Service');
```

Very restrictive rate limit:

```php
$restrictedToken = Bearer::for($user)
    ->rateLimit(10) // Only 10 requests per minute
    ->issue('pk', 'Demo Key');
```

## Rate Limit by Environment

Test environment with higher limits:

```php
$testToken = Bearer::for($user)
    ->environment('test')
    ->rateLimit(10000) // Generous limit for testing
    ->issue('pk', 'Development Key');
```

Live environment with production limits:

```php
$liveToken = Bearer::for($user)
    ->environment('live')
    ->rateLimit(100) // Standard production limit
    ->issue('pk', 'Production Key');
```

## Checking Rate Limits

```php
$token = $user->currentAccessToken();

// Get the rate limit for current token
$rateLimit = $token->rate_limit; // null = unlimited

// Check if token has rate limiting enabled
if ($token->rate_limit !== null) {
    echo "Rate limit: {$token->rate_limit} requests/minute";
}
```

## Updating Rate Limits

```php
// Increase rate limit for upgraded customer
$token->accessToken->update([
    'rate_limit' => 1000,
]);

// Remove rate limiting
$token->accessToken->update([
    'rate_limit' => null,
]);
```

## Rate Limit Middleware

Integrate with Laravel's rate limiting:

```php
// app/Providers/RouteServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('bearer', function ($request) {
    $token = $request->user()?->currentAccessToken();

    if (!$token || $token->rate_limit === null) {
        return Limit::none();
    }

    return Limit::perMinute($token->rate_limit)
        ->by($token->id);
});
```

In routes:

```php
// routes/api.php
Route::middleware(['auth:bearer', 'throttle:bearer'])->group(function () {
    Route::get('/data', [DataController::class, 'index']);
});
```

## Handling Rate Limit Exceeded

```php
use Cline\Bearer\Exceptions\RateLimitException;

// app/Exceptions/Handler.php
$this->renderable(function (RateLimitException $e) {
    return response()->json([
        'error' => 'rate_limit_exceeded',
        'message' => 'Too many requests. Please slow down.',
        'retry_after' => $e->retryAfter,
    ], 429)->withHeaders([
        'Retry-After' => $e->retryAfter,
        'X-RateLimit-Limit' => $e->limit,
        'X-RateLimit-Remaining' => 0,
    ]);
});
```

## Custom Rate Limit Keys

Rate limit by token + endpoint combination:

```php
RateLimiter::for('bearer-endpoint', function ($request) {
    $token = $request->user()?->currentAccessToken();

    if (!$token) {
        return Limit::perMinute(60)->by($request->ip());
    }

    $baseLimit = $token->rate_limit ?? 1000;
    $endpoint = $request->route()->getName();

    // Different limits per endpoint
    $multipliers = [
        'api.search' => 0.1,  // 10% of base (expensive operation)
        'api.export' => 0.05, // 5% of base (very expensive)
        'api.read' => 1.0,    // Full limit
    ];

    $multiplier = $multipliers[$endpoint] ?? 1.0;

    return Limit::perMinute((int) ($baseLimit * $multiplier))
        ->by($token->id . '|' . $endpoint);
});
```

## Next Steps

- **[Usage Tracking](#doc-docs-usage-tracking)** - Monitor token activity
- **[Audit Logging](#doc-docs-audit-logging)** - Record rate limit events

<a id="doc-docs-usage-tracking"></a>

## Automatic Usage Tracking

Bearer automatically tracks every authentication event. Unlike Sanctum's simple `last_used_at`, we maintain full history.

Each authentication creates an audit log entry with:
- Event type (Authenticated)
- IP address
- User agent
- Timestamp
- Custom metadata

## Querying Usage History

```php
use Cline\Bearer\Enums\AuditEvent;

$token = $user->currentAccessToken();

// Get all usage (authentications) for a token
$usage = $token->auditLogs()
    ->where('event', AuditEvent::Authenticated->value)
    ->get();

// Get usage count
$totalUses = $token->auditLogs()
    ->where('event', AuditEvent::Authenticated->value)
    ->count();

// Get recent usage
$recentUsage = $token->auditLogs()
    ->where('event', AuditEvent::Authenticated->value)
    ->where('created_at', '>', now()->subDays(7))
    ->latest()
    ->get();

// Get first and last usage
$firstUse = $token->auditLogs()
    ->where('event', AuditEvent::Authenticated->value)
    ->oldest()
    ->first();

$lastUse = $token->auditLogs()
    ->where('event', AuditEvent::Authenticated->value)
    ->latest()
    ->first();
```

## Usage Patterns & Analytics

Daily usage counts:

```php
use Cline\Bearer\Database\Models\TokenAuditLog;

$dailyUsage = TokenAuditLog::query()
    ->where('personal_access_token_id', $token->id)
    ->where('event', AuditEvent::Authenticated->value)
    ->where('created_at', '>', now()->subDays(30))
    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
    ->groupBy('date')
    ->orderBy('date')
    ->get();
```

Hourly distribution:

```php
$hourlyUsage = TokenAuditLog::query()
    ->where('personal_access_token_id', $token->id)
    ->where('event', AuditEvent::Authenticated->value)
    ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
    ->groupBy('hour')
    ->orderBy('hour')
    ->get();
```

Usage by IP address:

```php
$usageByIp = TokenAuditLog::query()
    ->where('personal_access_token_id', $token->id)
    ->where('event', AuditEvent::Authenticated->value)
    ->selectRaw('ip_address, COUNT(*) as count')
    ->groupBy('ip_address')
    ->orderByDesc('count')
    ->get();
```

## User-Level Usage Analytics

```php
use Cline\Bearer\Database\Models\PersonalAccessToken;

$user = User::find(1);

// Total usage across all user's tokens
$totalUserUsage = TokenAuditLog::query()
    ->whereHas('token', function ($query) use ($user) {
        $query->where('tokenable_type', get_class($user))
              ->where('tokenable_id', $user->id);
    })
    ->where('event', AuditEvent::Authenticated->value)
    ->count();

// Most active tokens
$mostActiveTokens = PersonalAccessToken::query()
    ->where('tokenable_type', get_class($user))
    ->where('tokenable_id', $user->id)
    ->withCount(['auditLogs' => function ($query) {
        $query->where('event', AuditEvent::Authenticated->value);
    }])
    ->orderByDesc('audit_logs_count')
    ->limit(10)
    ->get();
```

## Tracking Failures & Security Events

Failed authentication attempts:

```php
$failedAttempts = TokenAuditLog::query()
    ->whereIn('event', [
        AuditEvent::Failed->value,
        AuditEvent::Expired->value,
        AuditEvent::IpBlocked->value,
        AuditEvent::DomainBlocked->value,
        AuditEvent::RateLimited->value,
    ])
    ->where('created_at', '>', now()->subHours(24))
    ->get();
```

Suspicious activity (many failures from same IP):

```php
$suspiciousIps = TokenAuditLog::query()
    ->whereIn('event', [
        AuditEvent::Failed->value,
        AuditEvent::IpBlocked->value,
    ])
    ->where('created_at', '>', now()->subHours(1))
    ->selectRaw('ip_address, COUNT(*) as count')
    ->groupBy('ip_address')
    ->having('count', '>', 10)
    ->get();
```

## Lifecycle Event Tracking

```php
$lifecycleEvents = TokenAuditLog::query()
    ->where('personal_access_token_id', $token->id)
    ->whereIn('event', [
        AuditEvent::Created->value,
        AuditEvent::Rotated->value,
        AuditEvent::Revoked->value,
    ])
    ->orderBy('created_at')
    ->get();
```

## Usage Statistics Dashboard

Build a usage statistics summary:

```php
function getUsageStatistics(User $user, int $days = 30): array
{
    $startDate = now()->subDays($days);

    $tokens = PersonalAccessToken::query()
        ->where('tokenable_type', get_class($user))
        ->where('tokenable_id', $user->id)
        ->get();

    $tokenIds = $tokens->pluck('id');

    $auditLogs = TokenAuditLog::query()
        ->whereIn('personal_access_token_id', $tokenIds)
        ->where('created_at', '>', $startDate);

    return [
        'total_tokens' => $tokens->count(),
        'active_tokens' => $tokens->whereNull('revoked_at')->count(),
        'total_requests' => (clone $auditLogs)
            ->where('event', AuditEvent::Authenticated->value)
            ->count(),
        'unique_ips' => (clone $auditLogs)
            ->where('event', AuditEvent::Authenticated->value)
            ->distinct('ip_address')
            ->count('ip_address'),
        'failed_attempts' => (clone $auditLogs)
            ->whereIn('event', [
                AuditEvent::Failed->value,
                AuditEvent::Expired->value,
                AuditEvent::IpBlocked->value,
            ])
            ->count(),
        'by_environment' => [
            'test' => $tokens->where('environment', 'test')->count(),
            'live' => $tokens->where('environment', 'live')->count(),
        ],
        'by_type' => [
            'sk' => $tokens->where('type', 'sk')->count(),
            'pk' => $tokens->where('type', 'pk')->count(),
            'rk' => $tokens->where('type', 'rk')->count(),
        ],
    ];
}
```

## Performance: Disabling Usage Logging

For high-traffic applications, you may want to disable per-request logging:

```php
// config/bearer.php
'audit' => [
    'log_usage' => false, // Disable per-authentication logging
],
```

This still logs lifecycle events (create, revoke, rotate) but not each authentication request. You can then rely on:
- The `last_used_at` column (updated on each auth)
- External logging systems (CloudWatch, DataDog, etc.)
- Custom middleware for selective logging

## Custom Usage Tracking

Add custom metadata to authentication logs:

```php
use Cline\Bearer\Events\TokenAuthenticated;

Event::listen(TokenAuthenticated::class, function (TokenAuthenticated $event) {
    $event->token->auditLogs()->latest()->first()?->update([
        'metadata' => array_merge(
            $event->token->auditLogs()->latest()->first()?->metadata ?? [],
            [
                'endpoint' => request()->path(),
                'method' => request()->method(),
                'response_time_ms' => defined('LARAVEL_START')
                    ? round((microtime(true) - LARAVEL_START) * 1000)
                    : null,
            ]
        ),
    ]);
});
```

## Next Steps

- **[Audit Logging](#doc-docs-audit-logging)** - Configure audit drivers
- **[Rate Limiting](#doc-docs-rate-limiting)** - Throttle based on usage patterns

<a id="doc-docs-audit-logging"></a>

## Automatic Logging

Bearer automatically logs these events:
- **TokenCreated**: When a new token is issued
- **TokenAuthenticated**: When a token is used to authenticate a request
- **TokenRevoked**: When a token is revoked
- **TokenRotated**: When a token is rotated
- **TokenAuthenticationFailed**: When authentication fails (expired, revoked, IP blocked, etc.)

No manual intervention needed - just use the package normally:

```php
$token = Bearer::for($user)->issue('sk', 'My Token');
// ^ This automatically logs a TokenCreated event
```

## Querying Audit Logs

```php
use Cline\Bearer\Database\Models\TokenAuditLog;
use Cline\Bearer\Enums\AuditEvent;

// Get all audit logs for a token
$logs = $token->accessToken->auditLogs()->get();

// Get logs for specific events
$authLogs = $token->accessToken->auditLogs()
    ->where('event', AuditEvent::Authenticated->value)
    ->get();

// Get recent logs
$recentLogs = $token->accessToken->auditLogs()
    ->where('created_at', '>', now()->subDays(7))
    ->latest()
    ->get();

// Get failed authentication attempts
$failedAttempts = TokenAuditLog::query()
    ->whereIn('event', [
        AuditEvent::Failed->value,
        AuditEvent::Expired->value,
        AuditEvent::IpBlocked->value,
        AuditEvent::DomainBlocked->value,
        AuditEvent::RateLimited->value,
    ])
    ->where('created_at', '>', now()->subHours(24))
    ->get();
```

## Audit Log Data

Each audit log contains:

```php
foreach ($logs as $log) {
    $log->event;      // AuditEvent enum (created, authenticated, revoked, etc.)
    $log->ip_address; // IP address of the request
    $log->user_agent; // User agent string
    $log->metadata;   // Additional JSON data
    $log->created_at; // Timestamp
}
```

## Configuring Audit Drivers

```php
// config/bearer.php
return [
    'audit' => [
        // Default driver
        'driver' => env('BEARER_AUDIT_DRIVER', 'database'),

        // Available drivers
        'drivers' => [
            'database' => [
                'class' => DatabaseAuditDriver::class,
                'connection' => null, // Uses default database connection
            ],
            'spatie' => [
                'class' => SpatieActivityLogDriver::class,
                'log_name' => 'bearer', // Spatie activity log name
            ],
            'null' => [
                'class' => NullAuditDriver::class, // No-op driver
            ],
        ],

        // Enable/disable usage logging (every authentication)
        'log_usage' => true,

        // How long to keep audit logs
        'retention_days' => 90,
    ],
];
```

## Using Spatie Activity Log Driver

If you're using `spatie/laravel-activitylog`:

```env
BEARER_AUDIT_DRIVER=spatie
```

Query logs via Spatie's API:

```php
use Spatie\Activitylog\Models\Activity;

$activities = Activity::inLog('bearer')
    ->forSubject($token->accessToken)
    ->latest()
    ->get();
```

## Creating a Custom Audit Driver

```php
use Cline\Bearer\Contracts\AuditDriver;
use Cline\Bearer\Database\Models\PersonalAccessToken;
use Cline\Bearer\Enums\AuditEvent;
use Illuminate\Support\Collection;

class CloudWatchAuditDriver implements AuditDriver
{
    public function __construct(
        private readonly CloudWatchClient $client,
    ) {}

    public function log(PersonalAccessToken $token, AuditEvent $event, array $context = []): void
    {
        $this->client->putLogEvents([
            'logGroupName' => 'bearer-audit',
            'logStreamName' => date('Y-m-d'),
            'logEvents' => [
                [
                    'timestamp' => now()->getTimestampMs(),
                    'message' => json_encode([
                        'token_id' => $token->id,
                        'event' => $event->value,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'context' => $context,
                    ]),
                ],
            ],
        ]);
    }

    public function getLogsForToken(PersonalAccessToken $token): Collection
    {
        // Query CloudWatch logs...
        return collect();
    }
}
```

Register in a service provider:

```php
use Cline\Bearer\AuditDrivers\AuditDriverRegistry;

$this->app->make(AuditDriverRegistry::class)
    ->register('cloudwatch', new CloudWatchAuditDriver($client));
```

## Pruning Old Audit Logs

Via Artisan command (schedule this daily):

```bash
php artisan bearer:prune-audit-logs --days=90
```

In `app/Console/Kernel.php`:

```php
$schedule->command('bearer:prune-audit-logs')->daily();
```

Or manually:

```php
TokenAuditLog::query()
    ->where('created_at', '<', now()->subDays(90))
    ->delete();
```

## Disabling Audit Logging

For testing or performance, use the null driver:

```env
BEARER_AUDIT_DRIVER=null
```

Or disable only usage logging (still logs create/revoke/rotate):

```php
// config/bearer.php
'audit' => [
    'log_usage' => false,
],
```

## Next Steps

- **[Usage Tracking](#doc-docs-usage-tracking)** - Analyze token usage patterns
- **[Revocation & Rotation](#doc-docs-revocation-rotation)** - Token lifecycle events

<a id="doc-docs-token-generators"></a>

## Built-in Generators

### Seam Generator (Default)

Stripe-style tokens with 24-character random alphanumeric string:

```
sk_test_abc123def456ghijklmn
```

### UUID Generator

UUID v4 tokens for distributed systems:

```
pk_live_550e8400-e29b-41d4-a716-446655440000
```

### Random Generator

Sanctum-style 40-character random string:

```
rk_test_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0
```

## Configuring Generators

```php
// config/bearer.php
return [
    // Default generator for all token types
    'generator' => env('BEARER_GENERATOR', 'seam'),

    'generators' => [
        'seam' => [
            'class' => SeamTokenGenerator::class,
            'length' => 24, // Character length of random portion
        ],
        'uuid' => [
            'class' => UuidTokenGenerator::class,
        ],
        'random' => [
            'class' => RandomTokenGenerator::class,
            'length' => 40,
        ],
    ],

    // Per-type generator override
    'types' => [
        'sk' => [
            'class' => SecretTokenType::class,
            'generator' => 'seam', // Stripe-style for secret keys
        ],
        'pk' => [
            'class' => PublishableTokenType::class,
            'generator' => 'uuid', // UUIDs for publishable keys
        ],
        'rk' => [
            'class' => RestrictedTokenType::class,
            'generator' => 'random', // Sanctum-style for restricted
        ],
    ],
];
```

## Creating a Custom Generator

### Short Token Generator

```php
use Cline\Bearer\Contracts\TokenGenerator;

final class ShortTokenGenerator implements TokenGenerator
{
    public function __construct(
        private readonly int $length = 8,
    ) {}

    public function generate(string $prefix, string $environment): string
    {
        $random = substr(bin2hex(random_bytes(32)), 0, $this->length);

        return "{$prefix}_{$environment}_{$random}";
    }

    public function parse(string $token): array
    {
        $parts = explode('_', $token);

        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid token format');
        }

        return [
            'prefix' => $parts[0],
            'environment' => $parts[1],
            'secret' => $parts[2],
        ];
    }
}
```

### Hash Token Generator

```php
final class HashTokenGenerator implements TokenGenerator
{
    public function generate(string $prefix, string $environment): string
    {
        $timestamp = now()->getTimestampMs();
        $random = bin2hex(random_bytes(16));
        $hash = hash('sha256', $timestamp . $random);

        return "{$prefix}_{$environment}_{$hash}";
    }

    public function parse(string $token): array
    {
        $parts = explode('_', $token);

        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid token format');
        }

        return [
            'prefix' => $parts[0],
            'environment' => $parts[1],
            'secret' => $parts[2],
        ];
    }
}
```

### Base62 Token Generator

Shorter tokens with Base62 encoding:

```php
final class Base62TokenGenerator implements TokenGenerator
{
    private const ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public function __construct(
        private readonly int $length = 22,
    ) {}

    public function generate(string $prefix, string $environment): string
    {
        $random = $this->generateBase62($this->length);

        return "{$prefix}_{$environment}_{$random}";
    }

    public function parse(string $token): array
    {
        $parts = explode('_', $token);

        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid token format');
        }

        return [
            'prefix' => $parts[0],
            'environment' => $parts[1],
            'secret' => $parts[2],
        ];
    }

    private function generateBase62(int $length): string
    {
        $result = '';
        $alphabetLength = strlen(self::ALPHABET);

        for ($i = 0; $i < $length; $i++) {
            $result .= self::ALPHABET[random_int(0, $alphabetLength - 1)];
        }

        return $result;
    }
}
```

## Registering Custom Generators

### Via Config

```php
// config/bearer.php
'generators' => [
    'short' => [
        'class' => App\Bearer\ShortTokenGenerator::class,
        'length' => 8,
    ],
    'hash' => [
        'class' => App\Bearer\HashTokenGenerator::class,
    ],
    'base62' => [
        'class' => App\Bearer\Base62TokenGenerator::class,
        'length' => 22,
    ],
],
```

### Via Service Provider

```php
use Cline\Bearer\TokenGenerators\TokenGeneratorRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->afterResolving(TokenGeneratorRegistry::class, function (TokenGeneratorRegistry $registry) {
            $registry->register('short', new ShortTokenGenerator(8));
            $registry->register('hash', new HashTokenGenerator());
            $registry->register('base62', new Base62TokenGenerator(22));
        });
    }
}
```

## Token Format Examples

| Generator | Example Token |
|-----------|---------------|
| Seam | `sk_test_EXAMPLE1234567890abcd` |
| UUID | `pk_live_00000000-0000-0000-0000-000000000000` |
| Random | `rk_test_EXAMPLE1234567890abcdefghijklmnopqrst` |
| Short | `sk_test_EXAMPLE1` |
| Hash | `sk_test_[64-character-sha256-hash]` |
| Base62 | `sk_test_EXAMPLE1234567890ab` |

## Security Considerations

When creating custom generators, ensure:

1. **Use cryptographically secure random sources**: `random_bytes()`, `random_int()`
2. **Sufficient entropy**: Minimum 128 bits recommended
3. **URL-safe characters only**: Avoid `+`, `/`, `=`
4. **Consistent format**: For parsing
5. **No predictable patterns**: No sequential IDs or timestamp-only tokens

### Bad Practices

```php
// DON'T: Weak/predictable generators
mt_rand();           // Not cryptographically secure
rand();              // Not cryptographically secure
$id++;               // Sequential, predictable
time();              // Timestamp only, predictable
md5($data);          // Without random salt
```

### Good Practices

```php
// DO: Secure generators
random_bytes(32);                    // Cryptographically secure
random_int(0, $max);                 // Cryptographically secure
Str::uuid();                         // 122 bits of randomness
bin2hex(random_bytes(16));           // 128 bits of entropy
```

## Next Steps

- **[Custom Token Types](#doc-docs-custom-token-types)** - Assign generators to token types
- **[Getting Started](#doc-docs-readme)** - Configuration overview

<a id="doc-docs-token-relationships"></a>

## Relationship Model

| Relationship | Purpose | Required | Example |
|-------------|---------|----------|---------|
| **Owner** | Who created/owns the token | Yes | User who generated the API key |
| **Context** | What entity the token acts on behalf of | No | ServiceAccount, Application |
| **Boundary** | Tenant/workspace isolation scope | No | Team, Organization |

## Basic Usage (Owner Only)

Most tokens only need an owner - the entity that created the token:

```php
use Cline\Bearer\Facades\Bearer;

$user = User::find(1);
$token = Bearer::for($user)->issue('sk', 'My API Key');

// Access the owner relationship
$token->accessToken->owner;      // Returns the User model
$token->accessToken->owner_type; // 'App\Models\User' (or morph map alias)
$token->accessToken->owner_id;   // 1
```

## Context Relationship

The context represents what entity the token acts on behalf of. Use this when a user creates tokens for service accounts, applications, or other entities they manage.

### Setting Context

```php
$user = User::find(1);
$serviceAccount = ServiceAccount::find(5);

$token = Bearer::for($user)
    ->context($serviceAccount)
    ->issue('sk', 'Service Account Key');

// Relationships
$token->accessToken->owner;   // User#1 (who created it)
$token->accessToken->context; // ServiceAccount#5 (who it acts for)
```

### Querying by Context

Add the `HasApiTokens` trait to your context model:

```php
use Cline\Bearer\Concerns\HasApiTokens;

class ServiceAccount extends Model
{
    use HasApiTokens;
}
```

Then query tokens by context:

```php
// Get all tokens acting on behalf of this service account
$tokens = $serviceAccount->contextTokens()->get();

// Filter by type
$activeTokens = $serviceAccount->contextTokens()
    ->whereNull('revoked_at')
    ->where('type', 'sk')
    ->get();
```

## Boundary Relationship

The boundary provides tenant/workspace isolation, ensuring tokens can only operate within a specific scope. Essential for multi-tenant applications.

### Setting Boundary

```php
$user = User::find(1);
$team = Team::find(3);

$token = Bearer::for($user)
    ->boundary($team)
    ->issue('sk', 'Team API Key');

// Relationships
$token->accessToken->owner;    // User#1
$token->accessToken->boundary; // Team#3 (tenant scope)
```

### Querying by Boundary

Add the `HasApiTokens` trait to your boundary model:

```php
use Cline\Bearer\Concerns\HasApiTokens;

class Team extends Model
{
    use HasApiTokens;
}
```

Then query tokens by boundary:

```php
// Get all tokens within this team's boundary
$tokens = $team->boundaryTokens()->get();

// Count active tokens in organization
$count = $organization->boundaryTokens()
    ->whereNull('revoked_at')
    ->count();
```

## Full Three-Tier Example

Combine all three relationships for complex scenarios:

```php
$admin = User::find(1);
$serviceAccount = ServiceAccount::find(5);
$team = Team::find(3);

// Admin creates a service account token scoped to a team
$token = Bearer::for($admin)
    ->context($serviceAccount)
    ->boundary($team)
    ->abilities(['api:read', 'api:write'])
    ->environment('live')
    ->rateLimit(1000)
    ->issue('sk', 'Team Service API Key');

// Access all relationships
$token->accessToken->owner;    // User#1 (admin who created)
$token->accessToken->context;  // ServiceAccount#5 (acting on behalf of)
$token->accessToken->boundary; // Team#3 (scoped to this team)
```

## Token Groups with Relationships

Relationships are applied to all tokens in a group:

```php
$group = Bearer::for($user)
    ->context($application)
    ->boundary($organization)
    ->abilities(['*'])
    ->issueGroup(['sk', 'pk'], 'Application Keys');

// All tokens in the group inherit the same context and boundary
foreach ($group->tokens as $token) {
    $token->context;  // Same Application
    $token->boundary; // Same Organization
}
```

## Relationship Preservation

### Token Rotation

When rotating a token, context and boundary are automatically preserved:

```php
$newToken = Bearer::rotate($oldToken);

$newToken->accessToken->owner;    // Same owner
$newToken->accessToken->context;  // Same context (preserved)
$newToken->accessToken->boundary; // Same boundary (preserved)
```

### Token Derivation

When deriving a child token, context and boundary are inherited from the parent:

```php
$derivedToken = Bearer::derive($parentToken)
    ->abilities(['api:read']) // Can only restrict, not expand
    ->issue('Derived Token');

$derivedToken->accessToken->context;  // Inherited from parent
$derivedToken->accessToken->boundary; // Inherited from parent
```

## Morph Map Support

Bearer fully respects Laravel's morph map configuration. Register your morph map aliases:

```php
// In AppServiceProvider or a dedicated provider
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::enforceMorphMap([
    'user' => App\Models\User::class,
    'team' => App\Models\Team::class,
    'service_account' => App\Models\ServiceAccount::class,
    'application' => App\Models\Application::class,
]);
```

With morph maps, the database stores the alias instead of the full class name:

```php
$token->owner_type;    // 'user' instead of 'App\Models\User'
$token->context_type;  // 'service_account' instead of 'App\Models\ServiceAccount'
$token->boundary_type; // 'team' instead of 'App\Models\Team'
```

## Use Cases

### SaaS Multi-Tenancy

```php
// User creates an API key for their organization
$token = Bearer::for($user)
    ->boundary($organization)
    ->issue('sk', 'Organization API Key');
```

### Service Account Delegation

```php
// Admin creates a token that acts as a service account
$token = Bearer::for($admin)
    ->context($serviceAccount)
    ->issue('sk', 'Automated Pipeline Key');
```

### Team-Scoped Service Accounts

```php
// Admin creates a service account token for a specific team
$token = Bearer::for($admin)
    ->context($serviceAccount)
    ->boundary($team)
    ->issue('sk', 'Team CI/CD Key');
```

## Next Steps

- **[Authentication](#doc-docs-authentication)** - Protecting routes and checking permissions
- **[Revocation & Rotation](#doc-docs-revocation-rotation)** - Managing token lifecycle
- **[Derived Keys](#doc-docs-derived-keys)** - Creating child tokens with restricted abilities

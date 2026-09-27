<?php declare(strict_types=1);

namespace Cline\Bearer\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

use function assert;
use function class_exists;
use function config;
use function is_int;
use function is_string;
use function now;
use function sprintf;

/**
 * Console command to prune old audit logs from the database.
 *
 * This command removes token audit log entries that are older than a specified
 * retention period. Regular pruning helps control database growth while
 * maintaining compliance with audit retention policies.
 */
#[Description('Prune old token audit logs from the database')]
#[Signature('bearer:prune-audit-logs
                            {--days= : Prune logs older than this many days (uses config default)}')]
final class PruneAuditLogsCommand extends Command
{
    /**
     * Execute the console command.
     *
     * Removes audit log entries older than the configured retention period,
     * helping control database growth while maintaining audit compliance. The
     * retention period can be overridden via the --days option.
     *
     * @return int Command exit code (0 for success)
     */
    public function handle(): int
    {
        $daysOption = $this->option('days');
        $retentionDays = config('bearer.audit.retention_days', 90);
        assert(is_int($retentionDays));

        $days = $daysOption !== null ? (int) $daysOption : $retentionDays;

        $model = config('bearer.models.access_token_audit_log');
        assert(is_string($model) && class_exists($model));

        /** @var class-string<Model> $model */
        $count = $model::query()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
        assert(is_int($count));

        $this->components->info(sprintf('Pruned %d audit logs older than %d days.', $count, $days));

        return self::SUCCESS;
    }
}

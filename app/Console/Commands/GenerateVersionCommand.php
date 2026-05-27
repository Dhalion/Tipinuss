<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class GenerateVersionCommand extends Command
{
    protected $signature = 'app:generate-version
        {--commit= : Git commit short hash (z.B. aus CI-Variable)}
        {--date= : Commit date (z.B. 2026-05-27, aus CI oder git)}';

    protected $description = 'Generiert config/version.php aus git HEAD oder CI-Argumenten';

    public function handle(): int
    {
        $commit = $this->resolveCommit();
        $date = $this->resolveDate();

        $content = '<?php

return [
    \'commit\' => '.var_export($commit, true).',
    \'date\' => '.var_export($date, true).',
];'.PHP_EOL;

        file_put_contents(config_path('version.php'), $content);

        $this->line("Version: <info>{$commit}</info>");

        if ($date !== '') {
            $this->line("Date:    <info>{$date}</info>");
        }

        return self::SUCCESS;
    }

    private function resolveCommit(): string
    {
        $commit = $this->option('commit');

        if (is_string($commit) && $commit !== '') {
            return $commit;
        }

        exec('git log --pretty="%h" -n 1 HEAD 2>/dev/null', $output, $code);

        if ($code === 0 && isset($output[0]) && $output[0] !== '') {
            return trim($output[0]);
        }

        $this->warn('Kein --commit Argument und kein git HEAD gefunden — verwende "dev".');

        return 'dev';
    }

    private function resolveDate(): string
    {
        $date = $this->option('date');

        if (is_string($date) && $date !== '') {
            return $date;
        }

        exec('git log --pretty="%ad" -n 1 HEAD --date=format:"%Y-%m-%d" 2>/dev/null', $output, $code);

        if ($code === 0 && isset($output[0]) && $output[0] !== '') {
            return trim($output[0]);
        }

        return '';
    }
}

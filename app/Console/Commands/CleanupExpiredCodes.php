<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VerificationCodeService;

class CleanupExpiredCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codes:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove códigos de verificação expirados do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new VerificationCodeService();
        $count = $service->cleanupExpiredCodes();

        $this->info("✅ {$count} código(s) expirado(s) removido(s).");
        
        return Command::SUCCESS;
    }
}

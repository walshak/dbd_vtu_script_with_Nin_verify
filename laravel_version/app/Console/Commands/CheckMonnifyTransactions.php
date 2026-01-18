<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonnifyService;

class CheckMonnifyTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monnify:check-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for new Monnify virtual account transactions and credit user wallets';

    protected $monnifyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MonnifyService $monnifyService)
    {
        parent::__construct();
        $this->monnifyService = $monnifyService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for new Monnify transactions...');
        $this->info('');

        try {
            $result = $this->monnifyService->checkNewTransactions();

            if ($result['success']) {
                $this->info('✓ Transaction check completed successfully');
                $this->info('');
                $this->line("Users checked: {$result['users_checked']}");
                $this->line("Transactions processed: {$result['transactions_processed']}");

                if ($result['errors'] > 0) {
                    $this->warn("Errors encountered: {$result['errors']}");
                }

                if ($result['transactions_processed'] > 0) {
                    $this->success("✓ {$result['transactions_processed']} wallet(s) credited successfully!");
                } else {
                    $this->comment('No new transactions found.');
                }

                return Command::SUCCESS;
            } else {
                $this->error('✗ Transaction check failed');
                $this->error($result['message'] ?? 'Unknown error');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

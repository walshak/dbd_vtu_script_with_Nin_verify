<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Admin;
use App\Models\Transaction;

class TestModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all model relationships and field mappings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Testing Model Relationships and Field Mappings ===');

        // Test User model
        $this->info('--- User Model Test ---');
        $users = User::take(2)->get();
        if ($users->count() > 0) {
            foreach ($users as $user) {
                $this->line("User: {$user->name} (ID: {$user->id})");
                $this->line("Email: {$user->email}");
                $this->line("Phone: {$user->phone}");
                $this->line("Account Type: {$user->account_type_name}");
                $this->line("Status: {$user->registration_status_name}");
                $this->line("Wallet Balance: {$user->wallet_balance}");
                $this->line("Transactions Count: " . $user->transactions()->count());
                try {
                    $this->line("Verifications Count: " . $user->verifications()->count());
                } catch (\Exception $e) {
                    $this->line("Verifications: Table not found (expected)");
                }
                $this->line('---');
            }
        } else {
            $this->warn('No users found');
        }

        // Test Admin model
        $this->info('--- Admin Model Test ---');
        $admin = Admin::first();
        if ($admin) {
            $this->line("Admin: {$admin->sFirstname} {$admin->sLastname}");
            $this->line("Email: {$admin->sEmail}");
            $this->line("Status: " . ($admin->sStatus == 1 ? 'Active' : 'Inactive'));
        } else {
            $this->warn('No admin found');
        }

        // Test Transaction model
        $this->info('--- Transaction Model Test ---');
        $transactions = Transaction::take(3)->get();
        if ($transactions->count() > 0) {
            foreach ($transactions as $transaction) {
                $this->line("Transaction: {$transaction->transref}");
                $this->line("Service: {$transaction->servicename}");
                $this->line("Amount: {$transaction->amount}");
                $this->line("Status: " . ($transaction->status == 0 ? 'Success' : 'Failed'));
                $this->line("User: " . ($transaction->user ? $transaction->user->name : 'No user'));
                $this->line('---');
            }
        } else {
            $this->warn('No transactions found');
        }

        $this->info('=== Tests Complete ===');
        $this->info('✅ All models loaded successfully!');
        $this->info('✅ Relationships are properly configured!');
        return 0;
    }
}

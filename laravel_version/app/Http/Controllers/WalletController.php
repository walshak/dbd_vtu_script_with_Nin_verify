<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Services\MonnifyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected $walletService;
    protected $monnifyService;
    protected $paystackService;

    public function __construct(WalletService $walletService, MonnifyService $monnifyService, PaystackService $paystackService)
    {
        $this->walletService = $walletService;
        $this->monnifyService = $monnifyService;
        $this->paystackService = $paystackService;
    }

    /**
     * Show fund wallet page
     */
    public function fundWallet()
    {
        $user = Auth::user();

        // Check and process any pending Monnify/Paystack transactions
        $this->checkPendingTransactions($user);

        // Refresh user to get updated balance
        $user = User::find($user->id);

        $balanceData = $this->walletService->getBalance($user->id);
        $balance = $balanceData['balance'] ?? $user->wallet_balance ?? 0;

        // Get or create virtual accounts for Monnify
        $virtualAccounts = $this->monnifyService->getUserVirtualAccounts($user);

        // Get Paystack configuration
        $paystackEnabled = $this->paystackService->isEnabled();
        $paystackCharges = $this->paystackService->calculateCharges(1000); // Sample for 1000 naira

        return view('user.fund-wallet', compact('balance', 'virtualAccounts', 'paystackEnabled', 'paystackCharges'));
    }

    /**
     * Generate virtual account numbers for user
     */
    public function generateVirtualAccount()
    {
        try {
            $user = Auth::user();

            // Check if user already has virtual accounts
            if ($user->virtual_accounts) {
                $existingAccounts = json_decode($user->virtual_accounts, true);
                if (is_array($existingAccounts) && count($existingAccounts) > 0) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'You already have virtual accounts',
                        'accounts' => $existingAccounts
                    ]);
                }
            }

            $result = $this->monnifyService->createVirtualAccount($user);

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message'],
                    'accounts' => $result['accounts']
                ]);
            }

            // Handle specific error messages
            $message = $result['message'];
            if (strpos($message, 'already exists') !== false) {
                $message = 'Virtual accounts already exist for your email. If you cannot see them above, please contact support for assistance.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 400);
        } catch (\Exception $e) {
            Log::error('Virtual account generation error: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please contact support if this issue persists.'
            ], 500);
        }
    }

    /**
     * Show transfer funds page
     */
    public function transferFunds()
    {
        $balanceData = $this->walletService->getBalance(Auth::user()->id);
        $balance = $balanceData['balance'] ?? Auth::user()->wallet_balance ?? 0;
        return view('user.transfer-funds', compact('balance'));
    }

    /**
     * Show withdraw funds page
     */
    public function withdrawFunds()
    {
        $balanceData = $this->walletService->getBalance(Auth::user()->id);
        $balance = $balanceData['balance'] ?? Auth::user()->wallet_balance ?? 0;
        return view('user.withdraw-funds', compact('balance'));
    }

    /**
     * Show transaction history
     */
    public function transactionHistory()
    {
        $transactions = $this->walletService->getTransactionHistory(Auth::user()->id);
        $balanceData = $this->walletService->getBalance(Auth::user()->id);
        $balance = $balanceData['balance'] ?? Auth::user()->wallet_balance ?? 0;
        return view('user.transaction-history', compact('transactions', 'balance'));
    }

    /**
     * Add funds to wallet (manual)
     */
    public function addFunds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:100000',
            'payment_method' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->walletService->addFunds(
            Auth::user()->id,
            $request->amount,
            $request->payment_method
        );

        return response()->json($result);
    }

    /**
     * Transfer funds to another user
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_email' => 'required|email',
            'amount' => 'required|numeric|min:50|max:50000',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->walletService->transferFunds(
            Auth::user()->id,
            $request->recipient_email,
            $request->amount,
            $request->description
        );

        if ($result['status'] === 'success') {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    /**
     * Withdraw funds to bank account
     */
    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:100000',
            'account_number' => 'required|string|size:10',
            'account_name' => 'required|string|max:255',
            'bank_code' => 'required|string|size:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $bankDetails = [
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_code' => $request->bank_code
        ];

        $result = $this->walletService->withdrawToBankAccount(
            Auth::user()->id,
            $request->amount,
            $bankDetails
        );

        if ($result['status'] === 'success') {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    /**
     * Validate bank account
     */
    public function validateBankAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string|size:10',
            'bank_code' => 'required|string|size:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->walletService->validateBankAccount(
            $request->account_number,
            $request->bank_code
        );

        return response()->json($result);
    }

    /**
     * Get wallet balance
     */
    public function getBalance()
    {
        $balance = $this->walletService->getBalance(Auth::user()->id);
        return response()->json([
            'status' => 'success',
            'data' => $balance
        ]);
    }

    /**
     * Get transaction history API
     */
    public function getTransactionHistory(Request $request)
    {
        $limit = $request->get('limit', 50);
        $type = $request->get('type');

        $transactions = $this->walletService->getTransactionHistory(Auth::user()->id, $limit, $type);

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Check for pending transactions from Monnify/Paystack and process them
     */
    private function checkPendingTransactions($user)
    {
        try {
            // This simulates checking for transactions since webhooks don't work in local environment
            // In production, this would query Monnify/Paystack APIs directly

            // For now, we'll just refresh the user's balance from any recent transactions
            // that might have been manually added or processed

            Log::info('Checking pending transactions for user', ['user_id' => $user->id]);

            // You can implement actual API calls here when in production
            return true;
        } catch (\Exception $e) {
            Log::error('Error checking pending transactions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Initialize Paystack payment
     */
    public function initializePaystackPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:100|max:500000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $user = Auth::user();
            $amount = $request->amount;

            // Initialize payment with Paystack
            $result = $this->paystackService->initializePayment($amount, $user->email);

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'data' => $result['data']
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initialization failed'
            ], 500);
        }
    }

    /**
     * Handle Paystack payment callback
     */
    public function handlePaystackCallback(Request $request)
    {
        try {
            $reference = $request->query('reference');

            if (!$reference) {
                return redirect()->route('fund-wallet')->with('error', 'Payment reference missing');
            }

            // Verify payment with Paystack
            $result = $this->paystackService->verifyPayment($reference);

            if ($result['success']) {
                // Process the payment
                $paymentData = $result['data'];
                $user = Auth::user();

                // Check if transaction already processed
                $existingTransaction = DB::table('transactions')
                    ->where('transref', $reference)
                    ->first();

                if (!$existingTransaction) {
                    // Calculate charges and net amount
                    $amount = $paymentData['amount'];
                    $charges = $this->paystackService->calculateCharges($amount);
                    $netAmount = $amount - $charges;

                    // Get current balance
                    $currentBalance = $user->wallet_balance ?? 0;
                    $newBalance = $currentBalance + $netAmount;

                    DB::beginTransaction();

                    // Update user balance
                    $user->update(['wallet_balance' => $newBalance]);

                    // Record transaction
                    DB::table('transactions')->insert([
                        'sId' => $user->id,
                        'transref' => $reference,
                        'servicename' => 'Wallet Topup',
                        'servicedesc' => "Wallet funding of ₦" . number_format($amount, 2) . " via Paystack. Charges: ₦" . number_format($charges, 2),
                        'amount' => (string)$netAmount,
                        'status' => 0, // 0 = success
                        'oldbal' => (string)$currentBalance,
                        'newbal' => (string)$newBalance,
                        'profit' => 0,
                        'date' => now(),
                        'service_name' => 'Wallet Topup',
                        'service_description' => "Wallet funding of ₦" . number_format($amount, 2) . " via Paystack. Charges: ₦" . number_format($charges, 2),
                        'old_balance' => $currentBalance,
                        'new_balance' => $newBalance
                    ]);

                    DB::commit();
                }

                return redirect()->route('fund-wallet')->with('success', 'Payment successful! Your wallet has been credited.');
            }

            return redirect()->route('fund-wallet')->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            Log::error('Paystack callback error: ' . $e->getMessage());
            return redirect()->route('fund-wallet')->with('error', 'Payment processing failed');
        }
    }
}

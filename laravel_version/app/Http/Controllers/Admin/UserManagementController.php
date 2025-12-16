<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('reg_status', $request->status);
            }

            if ($request->filled('type')) {
                $query->where('user_type', $request->type);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sort and paginate
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $users = $query->orderBy($sortBy, $sortOrder)
                ->paginate(20)
                ->withQueryString();

            // Get user statistics
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('reg_status', User::REG_STATUS_ACTIVE)->count(),
                'inactive_users' => User::where('reg_status', User::REG_STATUS_PENDING)->count(),
                'blocked_users' => User::where('reg_status', User::REG_STATUS_BLOCKED)->count(),
                'regular_users' => User::where('user_type', User::TYPE_USER)->count(),
                'agents' => User::where('user_type', User::TYPE_AGENT)->count(),
                'vendors' => User::where('user_type', User::TYPE_VENDOR)->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
            ];

            return view('admin.users.index', compact('users', 'stats'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading users: ' . $e->getMessage());
        }
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            // Return JSON for AJAX requests
            if (request()->expectsJson()) {
                return response()->json($user);
            }

            // Get user transactions
            $transactions = Transaction::where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get user statistics
            $userStats = [
                'total_transactions' => Transaction::where('user_id', $id)->count(),
                'successful_transactions' => Transaction::where('user_id', $id)->where('status', 'completed')->count(),
                'failed_transactions' => Transaction::where('user_id', $id)->where('status', 'failed')->count(),
                'total_spent' => Transaction::where('user_id', $id)->where('status', 'completed')->sum('amount'),
                'average_transaction' => Transaction::where('user_id', $id)->where('status', 'completed')->avg('amount'),
                'last_login' => $user->last_activity,
                'registration_date' => $user->created_at,
                'days_active' => $user->created_at ? $user->created_at->diffInDays(now()) : 0
            ];

            // Get monthly transaction summary
            $monthlyTransactions = $this->getUserMonthlyTransactions($id);

            return view('admin.users.show', compact('user', 'transactions', 'userStats', 'monthlyTransactions'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading user details: ' . $e->getMessage());
        }
    }

    /**
     * Show user edit form
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'User not found');
        }
    }

    /**
     * Update user details
     */
    public function update(Request $request, $id = null)
    {
        try {
            // Handle both route parameter and form data
            $userId = $id ?: $request->user_id;
            $user = User::findOrFail($userId);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id, 'id')],
                'phone' => ['required', 'string', Rule::unique('users')->ignore($user->id, 'id')],
                'user_type' => 'required|in:1,2,3',
                'reg_status' => 'required|in:0,1,2,3',
                'transaction_pin' => 'nullable|string|size:4',
                'new_password' => 'nullable|string|min:6|confirmed'
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = $request->user_type;
            $user->reg_status = $request->reg_status;

            if ($request->filled('email_verified_at')) {
                $user->email_verified_at = $request->email_verified_at;
            }

            if ($request->filled('transaction_pin')) {
                $user->transaction_pin = $request->transaction_pin;
            }

            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            }

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating user: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Credit user wallet
     */
    public function creditWallet(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255'
        ]);

        try {
            $user = User::findOrFail($id);
            $oldBalance = $user->wallet_balance;
            $user->wallet_balance += $request->amount;
            $user->save();

            // Record the credit transaction
            Transaction::create([
                'sId' => $user->id,
                'servicename' => 'wallet_credit',
                'amount' => $request->amount,
                'status' => 'Completed',
                'transref' => 'CREDIT_' . time() . rand(1000, 9999),
                'servicedesc' => $request->description,
                'date' => now(),
                'oldbal' => $oldBalance,
                'newbal' => $user->wallet_balance
            ]);

            return back()->with('success', "₦{$request->amount} credited to user's wallet successfully");
        } catch (\Exception $e) {
            return back()->with('error', 'Error crediting wallet: ' . $e->getMessage());
        }
    }

    /**
     * Debit user wallet
     */
    public function debitWallet(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255'
        ]);

        try {
            $user = User::findOrFail($id);

            if ($user->wallet_balance < $request->amount) {
                return back()->with('error', 'User does not have sufficient balance');
            }

            $oldBalance = $user->wallet_balance;
            $user->wallet_balance -= $request->amount;
            $user->save();

            // Record the debit transaction
            Transaction::create([
                'sId' => $user->id,
                'servicename' => 'wallet_debit',
                'amount' => $request->amount,
                'status' => 'Completed',
                'transref' => 'DEBIT_' . time() . rand(1000, 9999),
                'servicedesc' => $request->description,
                'date' => now(),
                'oldbal' => $oldBalance,
                'newbal' => $user->wallet_balance
            ]);

            return back()->with('success', "₦{$request->amount} debited from user's wallet successfully");
        } catch (\Exception $e) {
            return back()->with('error', 'Error debiting wallet: ' . $e->getMessage());
        }
    }

    /**
     * Block user account
     */
    public function blockUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->reg_status = 'blocked';
            $user->save();

            return back()->with('success', 'User account blocked successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error blocking user: ' . $e->getMessage());
        }
    }

    /**
     * Unblock user account
     */
    public function unblockUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->reg_status = 'active';
            $user->save();

            return back()->with('success', 'User account unblocked successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error unblocking user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user account
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Check if user has pending transactions
            $pendingTransactions = Transaction::where('sId', $id)
                ->where('status', 'Pending')
                ->count();

            if ($pendingTransactions > 0) {
                return back()->with('error', 'Cannot delete user with pending transactions');
            }

            // Archive user data before deletion
            $this->archiveUserData($user);

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User account deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:block,unblock,delete,export',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $userIds = $request->user_ids;
            $action = $request->action;
            $count = count($userIds);

            switch ($action) {
                case 'block':
                    User::whereIn('id', $userIds)->update(['reg_status' => 'blocked']);
                    return back()->with('success', "{$count} users blocked successfully");

                case 'unblock':
                    User::whereIn('id', $userIds)->update(['reg_status' => 'active']);
                    return back()->with('success', "{$count} users unblocked successfully");

                case 'delete':
                    // Check for pending transactions
                    $pendingCount = Transaction::whereIn('sId', $userIds)
                        ->where('status', 'Pending')
                        ->count();

                    if ($pendingCount > 0) {
                        return back()->with('error', 'Cannot delete users with pending transactions');
                    }

                    User::whereIn('id', $userIds)->delete();
                    return back()->with('success', "{$count} users deleted successfully");

                case 'export':
                    return $this->exportUsers($userIds);

                default:
                    return back()->with('error', 'Invalid action');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Export users to CSV
     */
    public function exportUsers($userIds = null)
    {
        try {
            $query = User::query();

            if ($userIds) {
                $query->whereIn('id', $userIds);
            }

            $users = $query->get();

            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Name',
                    'Email',
                    'Phone',
                    'Account Type',
                    'Status',
                    'Wallet Balance',
                    'Registration Date'
                ]);

                // Add user data
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->user_type,
                        $user->reg_status,
                        $user->wallet_balance,
                        $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting users: ' . $e->getMessage());
        }
    }

    /**
     * Get user monthly transactions
     */
    private function getUserMonthlyTransactions($userId)
    {
        $months = collect();

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $transactions = Transaction::where('sId', $userId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month);

            $months->push([
                'month' => $month->format('M Y'),
                'count' => $transactions->count(),
                'revenue' => $transactions->where('status', 'Completed')->sum('amount'),
                'successful' => $transactions->where('status', 'Completed')->count(),
                'failed' => $transactions->where('status', 'Failed')->count()
            ]);
        }

        return $months;
    }

    /**
     * Suspend/Block user account
     */
    public function suspendUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->reg_status = User::REG_STATUS_BLOCKED;
            $user->save();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User account suspended successfully'
                ]);
            }

            return back()->with('success', 'User account suspended successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error suspending user: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error suspending user: ' . $e->getMessage());
        }
    }

    /**
     * Activate user account
     */
    public function activateUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->reg_status = User::REG_STATUS_ACTIVE;
            $user->save();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User account activated successfully'
                ]);
            }

            return back()->with('success', 'User account activated successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error activating user: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error activating user: ' . $e->getMessage());
        }
    }

    /**
     * Verify user account
     */
    public function verifyUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->email_verified_at = now();
            $user->save();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User account verified successfully'
                ]);
            }

            return back()->with('success', 'User account verified successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error verifying user: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error verifying user: ' . $e->getMessage());
        }
    }

    /**
     * Archive user data before deletion
     */
    private function archiveUserData($user)
    {
        // This would archive user data to a separate table or backup system
        // For now, we'll just log it
        Log::info('User archived before deletion', [
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'wallet_balance' => $user->wallet_balance,
            'deleted_at' => now()
        ]);
    }
}

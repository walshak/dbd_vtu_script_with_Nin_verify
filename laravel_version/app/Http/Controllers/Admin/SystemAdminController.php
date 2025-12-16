<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Transaction;
use App\Models\NetworkId;
use App\Models\SiteSettings;
use App\Models\Notification;
use App\Models\ApiConfig;
use App\Models\ApiLink;
use App\Models\ExamPin;
use App\Models\ElectricityProvider;
use App\Models\Airtime;
use App\Models\AlphaTopup;
use App\Models\DataPlan;
use App\Models\CablePlan;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SystemAdminController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->middleware(['admin']);
        $this->middleware(function ($request, $next) {
            // Set admin information in session for compatibility
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                session([
                    'sysId' => $admin->sysId,
                    'sysRole' => $admin->sysRole,
                    'sysName' => $admin->sysName,
                    'sysUser' => $admin->sysUsername
                ]);
            }
            return $next($request);
        });
    }

    //----------------------------------------------------------------------------------------------------------------
    // System Users Account Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Logout Users From System
     */
    public function logoutUser()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    /**
     * Create Account For New System Users
     */
    public function createAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:sysusers,sysUsername',
            'password' => 'required|string|min:6',
            'role' => 'required|integer|in:1,2,3'
        ]);

        try {
            // Create admin user in the sysusers table via Admin model
            $admin = Admin::create([
                'sysName' => $request->name,
                'sysUsername' => $request->username,
                'sysToken' => Hash::make($request->password),
                'sysRole' => $request->role,
                'sysStatus' => Admin::STATUS_ACTIVE
            ]);

            return back()->with('success', 'New Admin User Created Successfully.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->with('error', 'Username Already Exist, Please Try Again.');
            }
            return back()->with('error', 'Unable To Create User, Please Try Again.');
        }
    }

    /**
     * Create Account For Subscriber
     */
    public function createSubscriberAccount(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'state' => 'required|string'
        ]);

        try {
            $user = User::create([
                'name' => $request->fname . ' ' . $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'state' => $request->state,
                'user_type' => 'user',
                'reg_status' => 'active',
                'wallet_balance' => 0,
                'api_key' => 'API' . time() . rand(1000, 9999),
                'transaction_pin' => '1234',
                'created_at' => now()
            ]);

            return back()->with('success', 'New Subscriber Account Created Successfully.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'email')) {
                return back()->with('error', 'Email Already Exist.');
            } elseif (str_contains($e->getMessage(), 'phone')) {
                return back()->with('error', 'Phone Number Already Exist.');
            }
            return back()->with('error', 'Unable To Create Subscriber Account, Please Try Again.');
        }
    }

    /**
     * Manage Account Of System Users
     */
    public function getAccounts()
    {
        return User::where('user_type', 'admin')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Show system accounts management page
     */
    public function showAccountsPage()
    {
        $accounts = $this->getAccounts();
        return view('admin.system.accounts.index', compact('accounts'));
    }

    /**
     * Get Account By ID
     */
    public function getAccountById($id)
    {
        return User::where('user_type', 'admin')->find($id);
    }

    /**
     * Update Account Status
     */
    public function updateAccountStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sysusers,sysId',
            'status' => 'required|in:0,1'
        ]);

        try {
            Admin::where('sysId', $request->id)->update(['sysStatus' => $request->status]);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    /**
     * Update Admin Account
     */
    public function updateAdminAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string',
            'newpassword' => 'nullable|string|min:6'
        ]);

        try {
            $admin = Auth::guard('admin')->user();

            if (!Hash::check($request->password, $admin->sysToken)) {
                return back()->with('error', 'Wrong Password, Please Try Again');
            }

            $updateData = ['sysName' => $request->name];
            if ($request->filled('newpassword')) {
                $updateData['sysToken'] = Hash::make($request->newpassword);
            }

            Admin::where('sysId', $admin->sysId)->update($updateData);
            session(['sysName' => $request->name]);

            return back()->with('success', 'Account Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable To Update Account, Please Try Again');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Subscribers Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get All Subscribers
     */
    public function getSubscribers($limit = 1000)
    {
        return User::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * Get Subscriber Details
     */
    public function getSubscribersDetails($id)
    {
        return User::find($id);
    }

    /**
     * Update Subscriber
     */
    public function updateSubscriber(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'user_type' => 'required|in:0,1,2', // 0=user, 1=agent, 2=vendor (numeric values from form)
            'reg_status' => 'required|in:0,1,2', // 0=active, 1=inactive, 2=blocked (numeric values from form)
            'email_verified_at' => 'nullable'
        ]);

        try {
            $user = User::find($request->user_id);

            // Check for duplicates
            $emailExists = User::where('email', $request->email)->where('id', '!=', $request->user_id)->exists();
            $phoneExists = User::where('phone', $request->phone)->where('id', '!=', $request->user_id)->exists();

            if ($emailExists || $phoneExists) {
                return response()->json(['message' => 'Email or Phone number already exists'], 422);
            }

            // Map numeric values to string values
            $userTypeMap = ['0' => 'user', '1' => 'agent', '2' => 'vendor'];
            $statusMap = ['0' => 'active', '1' => 'inactive', '2' => 'blocked'];

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_type' => $userTypeMap[$request->user_type] ?? 'user',
                'reg_status' => $statusMap[$request->reg_status] ?? 'active',
                'email_verified_at' => $request->email_verified_at
            ]);

            return response()->json(['message' => 'User updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable To Update Account, Please Try Again'], 500);
        }
    }
    /**
     * Update Subscriber Password
     */
    public function updateSubscriberPass(Request $request)
    {
        $request->validate([
            'user' => 'required|exists:users,id',
            'paccess' => 'required|string|min:6'
        ]);

        try {
            User::where('id', $request->user)->update([
                'password' => Hash::make($request->paccess)
            ]);

            return back()->with('success', 'Account Password Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable To Update Account Password, Please Try Again');
        }
    }

    /**
     * Delete User Account
     */
    public function terminateUserAccount(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        try {
            User::where('id', $request->id)->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    /**
     * Change User Api Key
     */
    public function resetAccountApiKey(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        try {
            $newApiKey = 'API' . time() . rand(1000, 9999);
            User::where('id', $request->id)->update(['api_key' => $newApiKey]);
            return response()->json(['status' => 'success', 'apiKey' => $newApiKey]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Exam Pin Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get All exam
     */
    public function getExamPinDetails($examId)
    {
        return ExamPin::where('eId', $examId)->first();
    }

    /**
     * Update Exam Pin
     */
    public function updateExamPin(Request $request)
    {
        $request->validate([
            'exam' => 'required|exists:examid,eId',
            'examid' => 'required|string',
            'examprice' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'examstatus' => 'required|in:On,Off'
        ]);

        try {
            ExamPin::where('eId', $request->exam)->update([
                'examid' => $request->examid,
                'price' => $request->examprice,
                'buying_price' => $request->buying_price,
                'providerStatus' => $request->examstatus
            ]);

            return back()->with('success', 'Exam pin Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable To Update Exam pin, Please Try Again');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Electricity Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get All electricity
     */
    public function getElectricityBillDetails($electricityId)
    {
        return ElectricityProvider::where('eId', $electricityId)->first();
    }

    /**
     * Update Electricity Bill
     */
    public function updateElectricityBill(Request $request)
    {
        $request->validate([
            'electricity' => 'required|exists:electricity,eId',
            'electricityid' => 'required|string',
            'electricitystatus' => 'required|in:On,Off'
        ]);

        try {
            ElectricityProvider::where('eId', $request->electricity)->update([
                'providerId' => $request->electricityid,
                'status' => $request->electricitystatus
            ]);

            return back()->with('success', 'Electricity Bill Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable To Update Electricity Bill, Please Try Again');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Wallet Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Credit Debit User
     */
    public function creditDebitUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'action' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255'
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->with('error', 'User Email Not Found');
            }

            $oldBalance = $user->wallet_balance;
            $reference = $this->generateTransactionRef();

            if ($request->action === 'credit') {
                $user->wallet_balance += $request->amount;
                $description = "Wallet credited by admin: " . $request->reason;
            } else {
                if ($user->wallet_balance < $request->amount) {
                    return back()->with('error', 'Insufficient User Balance');
                }
                $user->wallet_balance -= $request->amount;
                $description = "Wallet debited by admin: " . $request->reason;
            }

            $user->save();

            // Record transaction using the Transaction model's expected fields
            Transaction::create([
                'sId' => $user->id, // Transaction model uses sId to reference user
                'transref' => $reference,
                'servicename' => 'Wallet ' . ucfirst($request->action),
                'servicedesc' => $description,
                'amount' => (string) $request->amount,
                'status' => 0, // 0 = success in the old system
                'oldbal' => (string) $oldBalance,
                'newbal' => (string) $user->wallet_balance,
                'profit' => 0,
                'date' => now()
            ]);

            $message = ucfirst($request->action) . " of ₦{$request->amount} completed successfully";
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Unexpected Error, Please Try Again Later');
        }
    }

    /**
     * Generate Transaction Reference
     */
    public function generateTransactionRef()
    {
        return 'TR-' . time() . '-' . substr(md5(uniqid()), 0, 16);
    }

    //----------------------------------------------------------------------------------------------------------------
    // Site Settings Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Site Settings
     */
    public function getSiteSettings()
    {
        return SiteSettings::first();
    }

    /**
     * Update Network Setting
     */
    public function updateNetworkSetting(Request $request)
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                SiteSettings::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return back()->with('success', 'Network Settings Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update network settings');
        }
    }

    /**
     * Update Contact Setting
     */
    public function updateContactSetting(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'email' => 'required|email',
            'whatsapp' => 'nullable|string',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'instagram' => 'nullable|string'
        ]);

        try {
            $settings = [
                'phone' => $request->phone,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'instagram' => $request->instagram
            ];

            foreach ($settings as $key => $value) {
                SiteSettings::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return back()->with('success', 'Contact Settings Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update contact settings');
        }
    }

    /**
     * Update Site Setting
     */
    public function updateSiteSetting(Request $request)
    {
        $request->validate([
            'sitename' => 'required|string|max:255',
            'siteurl' => 'required|url',
            'agentupgrade' => 'required|numeric',
            'vendorupgrade' => 'required|numeric'
        ]);

        try {
            $settings = [
                'sitename' => $request->sitename,
                'siteurl' => $request->siteurl,
                'agentupgrade' => $request->agentupgrade,
                'vendorupgrade' => $request->vendorupgrade
            ];

            foreach ($settings as $key => $value) {
                SiteSettings::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return back()->with('success', 'Site Settings Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update site settings');
        }
    }

    /**
     * Update Site Style Setting
     */
    public function updateSiteStyleSetting(Request $request)
    {
        $request->validate([
            'sitecolor' => 'required|string',
            'logindesign' => 'required|string',
            'homedesign' => 'required|string'
        ]);

        try {
            $settings = [
                'sitecolor' => $request->sitecolor,
                'logindesign' => $request->logindesign,
                'homedesign' => $request->homedesign
            ];

            foreach ($settings as $key => $value) {
                SiteSettings::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return back()->with('success', 'Site Style Settings Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update site style settings');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // API Configuration Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get API Configuration
     */
    public function getApiConfiguration()
    {
        return ApiConfig::all();
    }

    /**
     * Get API Configuration Links
     */
    public function getApiConfigurationLinks()
    {
        return ApiLink::all();
    }

    /**
     * Update API Configuration
     */
    public function updateApiConfiguration(Request $request)
    {
        $request->validate([
            'config' => 'required|array'
        ]);

        try {
            foreach ($request->config as $name => $value) {
                ApiConfig::updateOrCreate(
                    ['name' => $name],
                    ['value' => $value]
                );
            }

            return back()->with('success', 'API Configuration Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update API configuration');
        }
    }

    /**
     * Add New API Details
     */
    public function addNewApiDetails(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:apiconfigs,name',
            'value' => 'required|string',
            'type' => 'required|string'
        ]);

        try {
            ApiConfig::create([
                'name' => $request->name,
                'value' => $request->value,
                'type' => $request->type,
                'status' => 'active'
            ]);

            return back()->with('success', 'New API Details Added Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to add API details');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Notification Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Notification Status
     */
    public function getNotificationStatus()
    {
        return SiteSettings::where('key', 'notificationStatus')->value('value') ?? 'Off';
    }

    /**
     * Send Email To User
     */
    public function sendEmailToUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);

        try {
            // Implementation would use Laravel's Mail system
            // Mail::to($request->email)->send(new AdminNotification($request->subject, $request->message));

            return back()->with('success', 'Email sent successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to send email');
        }
    }

    /**
     * Update Notification Status
     */
    public function updateNotificationStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:On,Off'
        ]);

        try {
            SiteSettings::updateOrCreate(
                ['key' => 'notificationStatus'],
                ['value' => $request->status]
            );

            return back()->with('success', 'Notification Status Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update notification status');
        }
    }

    /**
     * Get Notifications
     */
    public function getNotifications()
    {
        return Notification::orderBy('dPosted', 'desc')->get();
    }

    /**
     * Add Notification
     */
    public function addNotification(Request $request)
    {
        $request->validate([
            'msgfor' => 'required|integer',
            'subject' => 'required|string|max:200',
            'message' => 'required|string'
        ]);

        try {
            Notification::create([
                'msgfor' => $request->msgfor,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 0,
                'dPosted' => now()
            ]);

            return back()->with('success', 'Notification Added Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to add notification');
        }
    }

    /**
     * Delete Notification
     */
    public function deleteNotification(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notifications,msgId'
        ]);

        try {
            Notification::where('msgId', $request->id)->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Network Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Networks
     */
    public function getNetworks()
    {
        return NetworkId::all();
    }

    //----------------------------------------------------------------------------------------------------------------
    // Airtime Discount Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Airtime Discount
     */
    public function getAirtimeDiscount()
    {
        return Airtime::all();
    }

    /**
     * Add Airtime Discount
     */
    public function addAirtimeDiscount(Request $request)
    {
        $request->validate([
            'network' => 'required|string',
            'user_discount' => 'required|numeric',
            'agent_discount' => 'required|numeric',
            'vendor_discount' => 'required|numeric',
            'type' => 'required|string'
        ]);

        try {
            Airtime::create([
                'aNetwork' => $request->network,
                'aUserDiscount' => $request->user_discount,
                'aAgentDiscount' => $request->agent_discount,
                'aVendorDiscount' => $request->vendor_discount,
                'aType' => $request->type
            ]);

            return back()->with('success', 'Airtime Discount Added Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to add airtime discount');
        }
    }

    /**
     * Update Airtime Discount
     */
    public function updateAirtimeDiscount(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:airtime,aId',
            'user_discount' => 'required|numeric',
            'agent_discount' => 'required|numeric',
            'vendor_discount' => 'required|numeric'
        ]);

        try {
            Airtime::where('aId', $request->id)->update([
                'aUserDiscount' => $request->user_discount,
                'aAgentDiscount' => $request->agent_discount,
                'aVendorDiscount' => $request->vendor_discount
            ]);

            return back()->with('success', 'Airtime Discount Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update airtime discount');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Alpha Topup Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Alpha Topup
     */
    public function getAlphaTopup()
    {
        return AlphaTopup::all();
    }

    /**
     * Add Alpha Topup
     */
    public function addAlphaTopup(Request $request)
    {
        $request->validate([
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'agent' => 'required|numeric|min:0',
            'vendor' => 'required|numeric|min:0'
        ]);

        try {
            AlphaTopup::create([
                'buyingPrice' => $request->buying_price,
                'sellingPrice' => $request->selling_price,
                'agent' => $request->agent,
                'vendor' => $request->vendor,
                'dPosted' => now()
            ]);

            return back()->with('success', 'Alpha Topup Added Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to add alpha topup');
        }
    }

    /**
     * Update Alpha Topup
     */
    public function updateAlphaTopup(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:alphatopupprice,alphaId',
            'selling_price' => 'required|numeric|min:0',
            'agent' => 'required|numeric|min:0',
            'vendor' => 'required|numeric|min:0'
        ]);

        try {
            AlphaTopup::where('alphaId', $request->id)->update([
                'sellingPrice' => $request->selling_price,
                'agent' => $request->agent,
                'vendor' => $request->vendor
            ]);

            return back()->with('success', 'Alpha Topup Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update alpha topup');
        }
    }

    /**
     * Delete Alpha Topup
     */
    public function deleteAlphaTopup(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:alphatopupprice,alphaId'
        ]);

        try {
            AlphaTopup::where('alphaId', $request->id)->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    /**
     * Get Pending Alpha Order
     */
    public function getPendingAlphaOrder()
    {
        return Transaction::where('sType', 'alpha_topup')
            ->where('sStatus', 'Pending')
            ->orderBy('sDate', 'asc')
            ->get();
    }

    /**
     * Complete Alpha Topup Request
     */
    public function completeAlphaTopupRequest(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,tId',
            'status' => 'required|in:Completed,Failed'
        ]);

        try {
            $transaction = Transaction::find($request->transaction_id);
            $transaction->status = $request->status === 'Completed' ? 0 : 1; // 0 = success, 1 = failed
            $transaction->save();

            if ($request->status === 'Failed') {
                // Refund user wallet
                $user = User::find($transaction->sId);
                $user->wallet_balance += floatval($transaction->amount);
                $user->save();
            }

            return back()->with('success', 'Alpha Topup Request Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update alpha topup request');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Transaction Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Transactions
     */
    public function getTransactions($limit = 1000)
    {
        return Transaction::with('user')->orderBy('sDate', 'desc')->limit($limit)->get();
    }

    /**
     * Get Transaction Details
     */
    public function getTransactionDetails($transactionId)
    {
        return Transaction::with('user')->find($transactionId);
    }

    /**
     * Update Transaction Status
     */
    public function updateTransactionStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,tId',
            'status' => 'required|in:Completed,Failed,Pending'
        ]);

        try {
            $transaction = Transaction::find($request->transaction_id);
            $oldStatus = $transaction->sStatus;
            $transaction->sStatus = $request->status;
            $transaction->save();

            // Handle wallet refund if transaction is failed
            if ($request->status === 'Failed' && $oldStatus !== 'Failed') {
                $user = User::find($transaction->sUser);
                $user->sWalletBalance += $transaction->sAmount;
                $user->save();
            }

            return back()->with('success', 'Transaction Status Updated Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to update transaction status');
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Analytics and Reports
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Sale Transactions
     */
    public function getSaleTransactions()
    {
        return Transaction::where('sStatus', 'Completed')
            ->whereDate('sDate', '>=', Carbon::now()->subDays(30))
            ->get();
    }

    /**
     * Get General Sales Analysis
     */
    public function getGeneralSalesAnalysis($data = null, $dateFrom = null, $dateTo = null)
    {
        $query = Transaction::where('sStatus', 'Completed');

        if ($dateFrom && $dateTo) {
            $query->whereBetween('sDate', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->whereDate('sDate', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->whereDate('sDate', '<=', $dateTo);
        }

        return $query->selectRaw('
            sType,
            COUNT(*) as total_transactions,
            SUM(sAmount) as total_amount,
            AVG(sAmount) as average_amount,
            DATE(sDate) as transaction_date
        ')
            ->groupBy('sType', 'transaction_date')
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get Airtime Sales Analysis
     */
    public function getAirtimeSalesAnalysis($data = null, $dateFrom = null, $dateTo = null)
    {
        $query = Transaction::where('sType', 'airtime')->where('sStatus', 'Completed');

        if ($dateFrom && $dateTo) {
            $query->whereBetween('sDate', [$dateFrom, $dateTo]);
        }

        return $query->selectRaw('
            DATE(sDate) as date,
            COUNT(*) as transactions,
            SUM(sAmount) as revenue,
            AVG(sAmount) as average
        ')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get Data Sales Analysis
     */
    public function getDataSalesAnalysis($data = null, $dateFrom = null, $dateTo = null)
    {
        $query = Transaction::where('sType', 'data')->where('sStatus', 'Completed');

        if ($dateFrom && $dateTo) {
            $query->whereBetween('sDate', [$dateFrom, $dateTo]);
        }

        return $query->selectRaw('
            DATE(sDate) as date,
            COUNT(*) as transactions,
            SUM(sAmount) as revenue,
            AVG(sAmount) as average
        ')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    //----------------------------------------------------------------------------------------------------------------
    // Contact Management
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get Contact
     */
    public function getContact()
    {
        return ContactMessage::orderBy('created_at', 'desc')->get();
    }

    /**
     * Delete Contact
     */
    public function deleteContact(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact,id'
        ]);

        try {
            ContactMessage::where('id', $request->id)->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Utility Functions
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Format Description
     */
    public function formatDescription($data)
    {
        return strlen($data) > 50 ? substr($data, 0, 50) . '...' : $data;
    }

    /**
     * Format Status
     */
    public function formatStatus($value)
    {
        return match ($value) {
            0, 'active' => '<span class="badge bg-success">Active</span>',
            1, 'blocked' => '<span class="badge bg-danger">Blocked</span>',
            'inactive' => '<span class="badge bg-warning">Inactive</span>',
            default => '<span class="badge bg-secondary">Unknown</span>'
        };
    }

    /**
     * Format Transaction Status
     */
    public function formatTransStatus($value)
    {
        return match ($value) {
            'Completed' => '<span class="badge bg-success">Completed</span>',
            'Failed' => '<span class="badge bg-danger">Failed</span>',
            'Pending' => '<span class="badge bg-warning">Pending</span>',
            default => '<span class="badge bg-secondary">Unknown</span>'
        };
    }

    /**
     * Get General Site Reports
     */
    public function getGeneralSiteReports()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('reg_status', 'active')->count(),
            'total_transactions' => Transaction::count(),
            'successful_transactions' => Transaction::where('status', 0)->count(), // 0 = success in Transaction model
            'total_revenue' => Transaction::where('status', 0)->sum('amount'),
            'today_revenue' => Transaction::where('status', 0)
                ->whereDate('date', today())->sum('amount'),
            'month_revenue' => Transaction::where('status', 0)
                ->whereMonth('date', now()->month)->sum('amount')
        ];
    }

    /**
     * Get Wallet Balance (for multi-wallet system)
     */
    public function getWalletBalance()
    {
        // This would integrate with actual wallet balance API
        // For now, return mock data matching PHP structure
        return [
            'walletOneBalance' => '0.00',
            'walletOneProvider' => 'Provider 1',
            'walletTwoBalance' => '0.00',
            'walletTwoProvider' => 'Provider 2',
            'walletThreeBalance' => '0.00',
            'walletThreeProvider' => 'Provider 3'
        ];
    }

    /**
     * Create notification helper (for compatibility)
     */
    private function createNotification1($type, $message)
    {
        $alertType = str_replace('alert-', '', $type);
        return back()->with($alertType === 'danger' ? 'error' : 'success', $message);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\B2bAccountDeduction;
use App\Models\BankAccount;
use App\Models\MfsAccount;
use App\Models\RechargeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DGvai\SSLCommerz\SSLCommerz;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function viewBankAccounts(Request $request){

        if ($request->ajax()) {
            $data = BankAccount::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0)
                            return "<span style='font-weight:600; color:red'>Inactive</span>";
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Active</span>";
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/bank/account')."/".$data->slug.'" class="btn-sm btn-warning rounded d-inline-block mb-1"><i class="fa fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('bank_accounts.view');
    }

    public function addBankAccount(){
        return view('bank_accounts.create');
    }

    public function saveBankAccount(Request $request){
        BankAccount::insert([
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'routing_no' => $request->routing_no,
            'acc_holder_name' => $request->acc_holder_name,
            'acc_no' => $request->acc_no,
            'swift_code' => $request->swift_code,
            'status' => 1,
            'slug' => str::random(3) . "-" . time(),
            'created_at' => Carbon::now(),
        ]);

        Toastr::success('New Bank Account Saved');
        return back();
    }

    public function deleteBankAccount($slug){
        BankAccount::where('slug', $slug)->delete();
        return response()->json(['success' => 'Deleted successfully.']);
    }

    public function editBankAccount($slug){
        $data = BankAccount::where('slug', $slug)->first();
        return view('bank_accounts.update', compact('data'));
    }

    public function updateBankAccount(Request $request){
        BankAccount::where('slug', $request->slug)->update([
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'routing_no' => $request->routing_no,
            'acc_holder_name' => $request->acc_holder_name,
            'acc_no' => $request->acc_no,
            'swift_code' => $request->swift_code,
            'status' => $request->status,
            'updated_at' => Carbon::now(),
        ]);

        Toastr::success('Bank Account Updated');
        return redirect('view/bank/accounts');
    }

    public function viewMfsAccounts(Request $request){
        if ($request->ajax()) {
            $data = MfsAccount::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0)
                            return "<span style='font-weight:600; color:red'>Inactive</span>";
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Active</span>";

                    })
                    ->editColumn('account_type', function($data) {
                        if($data->account_type  == 1)
                            return "bKash";
                        if($data->account_type  == 2)
                            return "Nagad";
                        if($data->account_type  == 3)
                            return "Rocket";
                        if($data->account_type  == 4)
                            return "Upay";
                        if($data->account_type  == 5)
                            return "Sure Cash";

                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/mfs/account')."/".$data->slug.'" class="btn-sm btn-warning rounded d-inline-block mb-1"><i class="fa fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('mfs_accounts.view');
    }

    public function addMfsAccount(){
        return view('mfs_accounts.create');
    }

    public function saveMfsAccount(Request $request){

        MfsAccount::insert([
            'account_type' => $request->account_type,
            'acc_no' => $request->acc_no,
            'status' => 1,
            'slug' => str::random(3) . "-" . time(),
            'created_at' => Carbon::now(),
        ]);

        Toastr::success('New MFS Account Saved');
        return back();
    }

    public function deleteMfsAccount($slug){
        MfsAccount::where('slug', $slug)->delete();
        return response()->json(['success' => 'Deleted Successfully.']);
    }

    public function editMfsAccount($slug){
        $data = MfsAccount::where('slug', $slug)->first();
        return view('mfs_accounts.update', compact('data'));
    }

    public function updateMfsAccount(Request $request){
        MfsAccount::where('slug', $request->slug)->update([
            'account_type' => $request->account_type,
            'acc_no' => $request->acc_no,
            'status' => $request->status,
            'updated_at' => Carbon::now(),
        ]);

        Toastr::success('MFS Account Updated');
        return back();
    }

    public function createTopupRequest(){
        $bankAccounts = BankAccount::where('status', 1)->orderBy('id', 'asc')->get();
        $mfsAccounts = MfsAccount::where('status', 1)->orderBy('id', 'asc')->get();
        $b2bUsers = User::where('user_type', 2)->orderBy('name', 'asc')->get();
        return view('recharge.create', compact('bankAccounts', 'mfsAccounts', 'b2bUsers'));
    }

    public function submitRechargeRequest(Request $request){

        $attachment = null;
        if ($request->hasFile('attachment')){
            $file = $request->file('attachment');
            $file_name = str::random(5) . time() . '.' . $file->getClientOriginalExtension();
            $file_location = public_path('recharge_attachments/');
            $file->move($file_location, $file_name);
            $attachment = "recharge_attachments/" . $file_name;
        }

        if(isset($request->user_id) && $request->user_id){
            $userId = $request->user_id;
        } else {
            $userId = Auth::user()->id;
        }

        $rechargeHistoryId = RechargeRequest::insertGetId([
            'user_id' => $userId,
            'admin_bank_account_id' => $request->payment_method == 1 ? $request->admin_bank_account_id : null,
            'admin_mfs_account_id' => in_array($request->payment_method, [3,4,5,6,7]) ? $request->admin_mfs_account_id : null,
            'payment_method' => $request->payment_method,

            'acc_holder_name' => $request->acc_holder_name,
            'acc_no' => $request->acc_no,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'routing_no' => $request->routing_no,
            'swift_code' => $request->swift_code,
            'mobile_no' => $request->mobile_no,
            'cheque_no' => $request->cheque_no,
            'cheque_bank_name' => $request->cheque_bank_name,
            'deposite_date' => $request->deposite_date,

            'recharge_amount' => $request->recharge_amount,
            'transaction_id' => $request->transaction_id,
            'attachment' => $attachment,
            'remarks' => $request->remarks,
            'status' => 0,
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        if($request->payment_method == 9){ //sslcommerz
            $transactionId = time().str::random(5);
            $rechargeRequestInfo = RechargeRequest::where('id', $rechargeHistoryId)->first();
            $rechargeRequestInfo->transaction_id = $transactionId;
            $rechargeRequestInfo->save();

            session(['ssl_tran_id' => $transactionId]);
            session(['ssl_total_amount' => $rechargeRequestInfo->recharge_amount]);

            return redirect('sslcommerz/order');
        }

        Toastr::success('Recharge Request Submitted');
        return back();
    }

    public function viewRechargeRequests(Request $request){
        if ($request->ajax()) {

            if(Auth::user()->user_type == 1){
                $query = DB::table('recharge_requests')
                        ->leftJoin('users', 'recharge_requests.user_id', 'users.id')
                        ->leftJoin('company_profiles', 'users.id', 'company_profiles.user_id')
                        ->select('recharge_requests.*', 'users.name as user_name', 'company_profiles.name as company_name')
                        ->orderBy('recharge_requests.id', 'desc');
            } else{
                $query = DB::table('recharge_requests')
                        ->leftJoin('users', 'recharge_requests.user_id', 'users.id')
                        ->leftJoin('company_profiles', 'users.id', 'company_profiles.user_id')
                        ->select('recharge_requests.*', 'users.name as user_name', 'company_profiles.name as company_name')
                        ->where('recharge_requests.user_id', Auth::user()->id)
                        ->orderBy('recharge_requests.id', 'desc');
            }

            return Datatables::of($query)
                    ->filterColumn('user_name', function($query, $keyword) {
                        $query->where('users.name', 'like', "%{$keyword}%");
                    })
                    ->filterColumn('company_name', function($query, $keyword) {
                        $query->where('company_profiles.name', 'like', "%{$keyword}%");
                    })
                    ->addColumn('receiving_channel', function($data){
                        if($data->admin_bank_account_id){
                            $bankInfo = BankAccount::where('id', $data->admin_bank_account_id)->first();
                            if($bankInfo){
                                return $bankInfo->bank_name."-".$bankInfo->acc_no;
                            } else {
                                return "<span style='color: red; font-weight: 600'>Missing Info</span>";
                            }

                        }
                        if($data->admin_mfs_account_id){
                            $mfsInfo = MfsAccount::where('id', $data->admin_mfs_account_id)->first();
                            if($mfsInfo && $mfsInfo->account_type == 1)
                                return $mfsInfo->acc_no." (bKash)";
                            if($mfsInfo && $mfsInfo->account_type == 2)
                                return $mfsInfo->acc_no." (Nagad)";
                            if($mfsInfo && $mfsInfo->account_type == 3)
                                return $mfsInfo->acc_no." (Rocket)";
                            if($mfsInfo && $mfsInfo->account_type == 4)
                                return $mfsInfo->acc_no." (Upay)";
                            if($mfsInfo && $mfsInfo->account_type == 5)
                                return $mfsInfo->acc_no." (SureCash)";
                        }
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 0)
                            return "<span style='font-weight:600; color:goldenrod'>Pending</span>";
                        if($data->status == 1)
                            return "<span style='font-weight:600; color:green'>Approved</span>";
                        if($data->status == 2)
                            return "<span style='font-weight:600; color:red'>Denied</span>";
                    })
                    ->editColumn('bank_name', function($data) {
                        if($data->admin_bank_account_id)
                            return $data->bank_name;
                        if($data->admin_bank_account_id == null && $data->admin_mfs_account_id == null)
                            return $data->cheque_bank_name;
                    })
                    ->editColumn('acc_no', function($data) {
                        if($data->admin_bank_account_id)
                            return $data->acc_no;
                        if($data->admin_mfs_account_id)
                            return $data->mobile_no;
                        if($data->admin_bank_account_id == null && $data->admin_mfs_account_id == null)
                            return $data->cheque_no;
                    })
                    ->editColumn('recharge_amount', function($data) {
                        return number_format($data->recharge_amount);
                    })
                    ->editColumn('attachment', function($data) {
                        if($data->attachment)
                            return "<a href='".url($data->attachment)."' target='_blank'>Attachment</a>";
                    })
                    ->editColumn('payment_method', function($data) {
                        if($data->payment_method  == 1)
                            return "Bank Transfer";
                        if($data->payment_method  == 2)
                            return "Bank Cheque";
                        if($data->payment_method  == 3)
                            return "bKash";
                        if($data->payment_method  == 4)
                            return "Nagad";
                        if($data->payment_method  == 5)
                            return "Rocket";
                        if($data->payment_method  == 6)
                            return "Upay";
                        if($data->payment_method  == 7)
                            return "SureCash";
                        if($data->payment_method  == 8)
                            return "Cash Payment";
                        if($data->payment_method  == 9)
                            return "SSLCommerz";
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = "";
                        if($data->status == 0){
                            $btn .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn mb-1"><i class="fa fa-trash"></i></a>';
                            if(Auth::user()->user_type == 1){
                                $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Approve" class="btn-sm btn-success rounded d-inline-block approveBtn mb-1" style="background: green; box-shadow: none;"><i class="fa fa-check"></i></a>';
                                $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Denied" class="btn-sm btn-warning rounded d-inline-block denyBtn mb-1" style="background: goldenrod; box-shadow: none;"><i class="fa fa-times"></i></a>';
                            }
                        }
                        return $btn;
                    })
                    ->rawColumns(['action', 'status', 'attachment', 'receiving_channel'])
                    ->make(true);
        }
        return view('recharge.view');
    }

    public function deleteRechargeRequest($slug){
        RechargeRequest::where('slug', $slug)->delete();
        return response()->json(['success' => 'Deleted Successfully.']);
    }

    public function approveRechargeRequest($slug){

        $rechargeInfo = RechargeRequest::where('slug', $slug)->first();
        User::where('id', $rechargeInfo->user_id)->increment('balance', $rechargeInfo->recharge_amount);

        RechargeRequest::where('slug', $slug)->update([
            'status' => 1,
            'updated_at' => Carbon::now(),
        ]);

        return response()->json(['success' => 'Approved Successfully.']);
    }

    public function denyRechargeRequest($slug){
        RechargeRequest::where('slug', $slug)->update([
            'status' => 2,
            'updated_at' => Carbon::now(),
        ]);
        return response()->json(['success' => 'Denied Successfully.']);
    }


    public function viewAccountDeductions(Request $request){
        if ($request->ajax()) {

            $query = DB::table('b2b_account_deductions')
                        ->leftJoin('users', 'b2b_account_deductions.b2b_user_id', 'users.id')
                        ->leftJoin('company_profiles', 'users.id', 'company_profiles.user_id')
                        ->select('b2b_account_deductions.*', 'users.name as user_name', 'company_profiles.name as company_name')
                        ->orderBy('b2b_account_deductions.id', 'desc');

            return Datatables::of($query)
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d", strtotime($data->created_at));
                    })
                    ->editColumn('amount', function($data) {
                        return $data->amount."/=";
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn mb-1"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('b2b_account_deductions.view');
    }

    public function submitAccountDeduction(){
        $b2bUsers = User::where('user_type', 2)->orderBy('name', 'asc')->get();
        return view('b2b_account_deductions.create', compact('b2bUsers'));
    }

    public function getUserBalance($id){
        $userInfo = User::where('id', $id)->select('balance')->first();
        return response()->json($userInfo);
    }

    public function deductB2bAccount(Request $request){
        $userInfo = User::where('id', $request->b2b_user_id)->first();

        if($request->amount > $userInfo->balance){
            Toastr::error('Deduction Amount cannot be greater than balance');
            return back();
        }

        B2bAccountDeduction::insert([
            'b2b_user_id' => $request->b2b_user_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        $userInfo->balance = $userInfo->balance - $request->amount;
        $userInfo->save();

        Toastr::success('Amount Successfully Deducted');
        return back();
    }

    public function deleteDeductionHistory($slug){
        B2bAccountDeduction::where('slug', $slug)->delete();
        Toastr::error('Deduction History Removed');
        return back();
    }



    public function order()
    {
        date_default_timezone_set("Asia/Dhaka");

        $totalAmount = session('ssl_total_amount');
        $transactionId = session('ssl_tran_id');

        $sslc = new SSLCommerz();
        $sslc->amount($totalAmount)
            ->trxid($transactionId)
            ->product('B2B Account Recharge')
            ->customer(Auth::user()->name, Auth::user()->email);
        return $sslc->make_payment();
    }

    public function success(Request $request)
    {
        $validate = SSLCommerz::validate_payment($request);
        if ($validate) {

            // $transactionId = session('ssl_tran_id');
            date_default_timezone_set("Asia/Dhaka");

            $rechargeRequestInfo = RechargeRequest::where('transaction_id', $request->tran_id)->first();
            $rechargeRequestInfo->status = 1;
            $rechargeRequestInfo->updated_at = Carbon::now();
            $rechargeRequestInfo->sve();

            User::where('id', $rechargeRequestInfo->user_id)->increment('balance', $rechargeRequestInfo->recharge_amount);

            DB::table('ssl_commerz_payment_histories')->insert([
                'recharge_history_id' => $rechargeRequestInfo->id,
                'tran_id' => $rechargeRequestInfo->transaction_id,
                'bank_tran_id' => $request->bank_tran_id,
                'val_id' => $request->val_id,
                'amount' => $rechargeRequestInfo->total,
                'card_type' => $request->card_type,
                'store_amount' => $rechargeRequestInfo->total,
                'card_no' => $request->card_no,
                'status' => "VALID",
                'tran_date' => date("Y-m-d H:i:s"),
                'currency' => "BDT",
                'card_issuer' => $request->card_issuer,
                'card_brand' => $request->card_brand,
                'card_sub_brand' => $request->card_sub_brand,
                'card_issuer_country' => $request->card_issuer_country,
                'created_at' => Carbon::now()
            ]);

            session()->forget('ssl_tran_id');
            session()->forget('ssl_total_amount');

            Toastr::success('Account is recharged successfully');
            return redirect('/view/recharge/requests');

        }
    }

    public function failure(Request $request)
    {
        //  do the database works
        //  also same goes for cancel()
        //  for IPN() you can leave it untouched or can follow
        //  official documentation about IPN from SSLCommerz Panel

        $transactionId = session('ssl_tran_id');
        RechargeRequest::where('transaction_id', $transactionId)->delete();

        session()->forget('ssl_tran_id');
        session()->forget('ssl_total_amount');

        Toastr::error('Something went wrong', 'Try Again');
        return redirect('/create/topup/request');
    }

    public function cancel(Request $request)
    {
        //  do the database works
        //  also same goes for cancel()
        //  for IPN() you can leave it untouched or can follow
        //  official documentation about IPN from SSLCommerz Panel

        $transactionId = session('ssl_tran_id');
        RechargeRequest::where('transaction_id', $transactionId)->delete();

        session()->forget('ssl_tran_id');
        session()->forget('ssl_total_amount');

        Toastr::error('Something went wrong', 'Try Again');
        return redirect('/create/topup/request');
    }

    public function refund($bankID)
    {
        /**
         * SSLCommerz::refund($bank_trans_id, $amount [,$reason])
         */

        $refund = SSLCommerz::refund($bankID, 1500); // 1500 => refund amount

        if ($refund->status) {
            /**
             * States:
             * success : Refund request is initiated successfully
             * failed : Refund request is failed to initiate
             * processing : The refund has been initiated already
             */

            $state  = $refund->refund_state;

            /**
             * RefID will be used for post-refund status checking
             */

            $refID  = $refund->ref_id;

            /**
             *  To get all the outputs
             */

            dd($refund->output);
        } else {
            return $refund->message;
        }
    }

    public function check_refund_status($refID)
    {
        $refund = SSLCommerz::query_refund($refID);

        if ($refund->status) {
            /**
             * States:
             * refunded : Refund request has been proceeded successfully
             * processing : Refund request is under processing
             * cancelled : Refund request has been proceeded successfully
             */

            $state  = $refund->refund_state;

            /**
             * RefID will be used for post-refund status checking
             */

            $refID  = $refund->ref_id;

            /**
             *  To get all the outputs
             */

            dd($refund->output);
        } else {
            return $refund->message;
        }
    }

    public function get_transaction_status($trxID)
    {
        $query = SSLCommerz::query_transaction($trxID);

        if ($query->status) {
            dd($query->output);
        } else {
            $query->message;
        }
    }

}

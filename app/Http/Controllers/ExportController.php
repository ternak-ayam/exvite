<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\OrderJasaResult;
// use App\Exports\Transactions;
use PDF;
use Lang;
use File;
use Storage;
use Response;
use Excel;
use App\Jobs\VerifyEmailJobs;

class ExportController extends Controller
{

    public function __construct() {
        // $this->middleware('auth');
    }

    public function all_transaction() {
        $data = WalletController::index()->credited;
        $wallet = WalletController::index()->balance;
        $credited = Transaction::with('creditedwallet.walletusers')
        ->where('wal_credited_wallet', $wallet->wallet_id)
        ->count();
        $debited = Transaction::with('debitedwallet.walletusers')
        ->where('wal_debited_wallet', $wallet->wallet_id)
        ->count();
        $debitedBank = Transaction::with('debitedwallet.walletusers')
        ->where('wal_debited_wallet', $wallet->wallet_id)
        ->where('wal_credited_wallet', $wallet->wallet_id)
        ->count();
        $debited = $debited - $debitedBank;
        $pdf = PDF::loadview('pdf.trans_history', ['history' => $data, 'wallet' => $wallet,
        'credited' => $credited, 'debited' => $debited])->setPaper('a4', 'landscape');
        return $pdf->download(Lang::get('wallet.history.title').'.pdf');
        // return response()->json($data);
        // return view('/pdf.trans_history', ['history' => $data, 'wallet' => $wallet,
        // 'credited' => $credited, 'debited' => $debited]);
    }
    public function download(Request $request) {
        return Storage::download($request->invoice);
        // return response()->json([$request->invoice]);
    }

    public function download_orders($type, $id) {
        switch($type) {
            case "orderan":
                $data = OrderJasaResult::where('order_id', $id)->first();
                return Storage::download($data->path);
            break;
            default:
                abort(404);
        }
        // return response()->json([$request->invoice]);
    }

    public function view($path) {
        $id = base64_decode($path);
        if (!File::exists($id)) {
            abort(404);
        }
        $file = File::get($id);
        $type = File::mimeType($id);
        $response = Response::make($file, 200);

        $response->header("Content-Type", $type);
        return $response;
    }

}

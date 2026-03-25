<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\bill_not_recvd;
use App\Models\pur_not_paid;


class DashboardBillNotRecievedTabController extends Controller
{
    public function BillNotRecvd(Request $request)
    {
        $bill_not_recvd = bill_not_recvd::select(
            'bill_not_recvd.sale_prefix',
            'bill_not_recvd.Sal_inv_no',
            'bill_not_recvd.bill_date',
            'bill_not_recvd.bill_amount',
            'bill_not_recvd.ttl_jv_amt',
            'bill_not_recvd.remaining_amount',
            'sales.pur_ord_no as sales_pur_ord_no', 
            'sales.Cash_pur_name',
            'tsales.Cash_name',
            'tsales.pur_ord_no as tsales_pur_ord_no'
        )
        ->leftJoin('sales', function($join) {
            $join->on('bill_not_recvd.sale_prefix', '=', 'sales.prefix')
                 ->on('bill_not_recvd.Sal_inv_no', '=', 'sales.Sal_inv_no');
        })
        ->leftJoin('tsales', function($join) {
            $join->on('bill_not_recvd.sale_prefix', '=', 'tsales.prefix')
                 ->on('bill_not_recvd.Sal_inv_no', '=', 'tsales.Sal_inv_no');
        })
        ->where('bill_not_recvd.remaining_amount', '<>', 0)
        ->where('bill_not_recvd.account_name', '=', 6)
        ->get();




        $pur_not_paid = pur_not_paid::select(
            'pur_not_paid.sale_prefix',
            'pur_not_paid.Sal_inv_no',
            'pur_not_paid.bill_date',
            'pur_not_paid.bill_amount',
            'pur_not_paid.ttl_jv_amt',
            'pur_not_paid.remaining_amount',
            'pur_not_paid.remarks',
            'purchase.pur_bill_no as sales_pur_ord_no',
            'purchase.cash_saler_name as Cash_pur_name',
            'tpurchase.Cash_pur_name as Cash_name',
            'tpurchase.pur_ord_no as tsales_pur_ord_no'
        )
        ->leftJoin('purchase', function ($join) {
            $join->on('pur_not_paid.sale_prefix', '=', 'purchase.prefix')
                ->on('pur_not_paid.Sal_inv_no', '=', 'purchase.pur_id');
        })
        ->leftJoin('tpurchase', function ($join) {
            $join->on('pur_not_paid.sale_prefix', '=', 'tpurchase.prefix')
                ->on('pur_not_paid.Sal_inv_no', '=', 'tpurchase.Sale_inv_no');
        })
        ->where('pur_not_paid.remaining_amount', '<>', 0)
        ->where('pur_not_paid.account_name', 7)
        ->get();




        return response()->json([
            'bill_not_recvd' => $bill_not_recvd,
            'pur_not_paid' => $pur_not_paid

        ]);
    }
}

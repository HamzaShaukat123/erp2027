<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sales_ageing;
use App\Models\purchase_ageing;
use App\Models\unadjusted_sales_ageing_jv2;
use App\Models\unadjusted_purchase_ageing_jv2;
use App\Models\vw_sale_lager_ageing_mismatch;
use App\Models\vw_purchase_lager_ageing_mismatch;
use App\Models\sales_days;
use App\Models\pur_days;
use App\Models\ac;

class DashboardUnAdjustedVouchersTabController extends Controller
{
    public function UV(Request $request)
    {
        $sales_ageing = sales_ageing::leftJoin('ac', 'ac.ac_code', '=', 'sales_ageing.acc_name')
            ->where('sales_ageing.status', 0)
            ->get(['jv2_id', 'sales_prefix', 'sales_id', 'ac_name', 'amount']);

        $vw_sale_lager_ageing_mismatch = vw_sale_lager_ageing_mismatch::leftJoin('ac as ac1', 'ac1.ac_code', '=', 'vw_sale_lager_ageing_mismatch.acc_name')
        ->leftJoin('ac as ac2', 'ac2.ac_code', '=', 'vw_sale_lager_ageing_mismatch.account_cod')
        ->get([
            'vw_sale_lager_ageing_mismatch.jv2_id',
            'ac1.ac_name as acc1',
            'ac2.ac_name as acc2',
        ]);

        $vw_purchase_lager_ageing_mismatch = vw_purchase_lager_ageing_mismatch::leftJoin('ac as ac1', 'ac1.ac_code', '=', 'vw_purchase_lager_ageing_mismatch.acc_name')
        ->leftJoin('ac as ac2', 'ac2.ac_code', '=', 'vw_purchase_lager_ageing_mismatch.account_cod')
        ->get([
            'vw_purchase_lager_ageing_mismatch.jv2_id',
            'ac1.ac_name as acc1',
            'ac2.ac_name as acc2',
        ]);


   

        $purchase_ageing = purchase_ageing::leftJoin('ac', 'ac.ac_code', '=', 'purchase_ageing.acc_name') // Corrected the table name here
            ->where('purchase_ageing.status', 0)
            ->get(['jv2_id', 'sales_prefix', 'sales_id', 'ac_name', 'amount']);


        $unadjusted_sales_ageing_jv2 = unadjusted_sales_ageing_jv2::where('unadjusted_sales_ageing_jv2.remaining_amount_after_rtn', '!=', 0)
            ->where('unadjusted_sales_ageing_jv2.SumCredit', '!=', 0)
            ->orderBy('jv_date')
            ->get(['jv2_id', 'prefix','ac_name', 'SumCredit','jv_date','pur_age_amount','remaining_amount','remaining_amount_after_rtn','rtn_amount']);

        $unadjusted_purchase_ageing_jv2 = unadjusted_purchase_ageing_jv2::where('unadjusted_purchase_ageing_jv2.remaining_amount_after_rtn', '!=', 0)
            ->whereNotIn('unadjusted_purchase_ageing_jv2.account_cod', [25, 11])
            ->where('unadjusted_purchase_ageing_jv2.SumDebit', '!=', 0)
            ->orderBy('jv_date')
            ->get(['jv2_id', 'prefix','ac_name', 'SumDebit','jv_date','pur_age_amount','remaining_amount','remaining_amount_after_rtn','rtn_amount']);

        $editedsale = sales_days::leftjoin('ac', 'ac.ac_code', '=', 'sales_days.account_name')
            ->select('sales_days.*', 'ac.ac_name as ac_nam', 'ac.remarks as ac_remarks')
            ->where('bill_amount', '<>', 0)
            ->where('remaining_amount', '<', 0)
            ->orderBy('bill_date', 'asc')
            ->orderBy('sale_prefix', 'asc')
            ->get();


        $editedpur = pur_days::leftjoin('ac', 'ac.ac_code', '=', 'pur_days.account_name')
            ->select('pur_days.*', 'ac.ac_name  as ac_nam', 'ac.remarks as ac_remarks')
            ->where('bill_amount', '<>', 0)
            ->where('remaining_amount', '<', 0)
            ->orderBy('bill_date','asc')
            ->orderBy('sale_prefix','asc')
            ->get();




        return response()->json([
            'sales_ageing' => $sales_ageing,
            'vw_sale_lager_ageing_mismatch' => $vw_sale_lager_ageing_mismatch,
            'vw_purchase_lager_ageing_mismatch' => $vw_purchase_lager_ageing_mismatch,
            'purchase_ageing' => $purchase_ageing,
            'unadjusted_sales_ageing_jv2' => $unadjusted_sales_ageing_jv2,
            'unadjusted_purchase_ageing_jv2' => $unadjusted_purchase_ageing_jv2,
            'editedsale' => $editedsale,
            'editedpur' => $editedpur
        ]);
    }

}

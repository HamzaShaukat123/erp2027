<?php

namespace App\Exports;

use App\Models\Item_entry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemEntry1Export implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'it_cod',
            'item_name',
            'item_group',
            'item_remark',
            'opp_qty',
            'OPP_qty_cost',
            'pur_rate_date',
            'sale_rate_date',
            'sales_price',
            'opp_date',
            'stock_level', 
            'labourprice', 
            'qty',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'status',
        ];
    }
}

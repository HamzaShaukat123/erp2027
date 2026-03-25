<?php

namespace App\Exports;

use App\Models\AC;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChartOfAccountExport implements FromCollection, WithHeadings
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
            'AC Code',
            'AC Name',
            'Recvable',
            'Paybale',
            'Opp Date',
            'Remarks',
            'Address',
            'City',
            'Area',
            'Phone',
            'Group Code', 
            'Acc Type', 
            'Credit Limit',
            'Days Limit',
            'Created By',
            'Created At',
            'Updated At',
            'Status',
            'Updated By',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\financial_accounts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class financial_accounts_Export implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return financial_accounts::get(['account_number','name','is_parent','debtor_opening','creditor_opening','debtor_current','creditor_current']);
    }

    public function headings() :array
    {
        return [__('home.account_number') , __('home.acount_name'),__('home.tybe'),__('home.Master'),__('home.depit_oping'),__('home.credit_oping'),__('home.credit'),__('home.debit')];
    }
}

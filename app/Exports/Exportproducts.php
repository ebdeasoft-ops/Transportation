<?php

namespace App\Exports;

use App\Models\products;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Exportproducts implements FromCollection,WithHeadings
{
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  products::where('branchs_id',Auth()->user()->branchs_id)->get(['id','product_name','Product_Code','Product_Location','numberofpice']);
        ;
    }
    public function headings() :array
    {
        return ["id", "name", "barcode","Product_Location", "All QUENTITY"];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProductsDamage;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;


class ProductsDamageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        return view('reports.product_damage');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductsDamage  $productsDamage
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        app()->setLocale(LaravelLocalization::getCurrentLocale());

        //
      //  return $request;
        if ($request->branch == '-') {
            $products=[];
            if($request->productNo=="-"){
                $products = ProductsDamage::whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            }
            else{
                $products = ProductsDamage::where('product_id',$request->productNo)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            }
            return view('reports.product_damage', compact('products'));

        }
        else {
            $products=[];
            if($request->productNo=="-"){
                $products = ProductsDamage::where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            }
            else{
                $products = ProductsDamage::where('product_id',$request->productNo)->where('branchs_id', $request->branch)->whereDate('created_at', '>=', $request->start_at)->whereDate('created_at', '<=', $request->end_at)->get();

            }
            
            return view('reports.product_damage', compact('products'));
       
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductsDamage  $productsDamage
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductsDamage $productsDamage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductsDamage  $productsDamage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductsDamage $productsDamage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductsDamage  $productsDamage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductsDamage $productsDamage)
    {
        //
    }
}

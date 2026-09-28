<?php

use App\Http\Controllers\SupllierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\FinancialAccountsController;
use App\Http\Controllers\AcountsTypeController;
use App\Http\Controllers\ProductsDamageController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization as LaravelLocalization;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\AcountesController;
use App\Http\Controllers\CredittransactionsController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BranchsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Spatie\Permission\Models\role_has_permissions;
use App\Http\Controllers\SupprocessesController;
use App\Http\Controllers\AvtController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductMovementAnotherBranchController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\DeliveryProductToTheCustomerController;
use App\Http\Controllers\CashWithdrawalFromTheBankController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\LoansController;
use App\Models\resource_purchases;
use App\Models\User;
use App\Models\system_setting;
use App\Http\Controllers\DliveryController;
use App\Http\Controllers\ProductsMixController;
use App\Models\settings;
use App\Http\Controllers\WaybillController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/{page}', [App\Http\Controllers\AdminController::class,'index']);

//products



$setting = settings::find(1);

define('postal_number', $setting->postal_number);
define('street_name', $setting->street_name);
define('building_number', $setting->building_number);
define('plot_identification', $setting->plot_identification);
define('region', $setting->region);
define('city', $setting->city);


$system_setting = system_setting::find(1);
define('PAGINATION_COUNT', 20);
define('serviceCost', $system_setting->serviceCost);


define('bank_acount_iban', $system_setting->bank_acount_iban);
define('bank_acount_number', $system_setting->bank_acount_number);
define('bankname', $system_setting->bankname);
define('Namear', $system_setting->name_ar);
define('describtionar', $system_setting->descriptionarbic);
define('STar', ' س . ت  :' . $system_setting->SR);
define('Taxar', '  الرقم الضريبي : ' . $system_setting->Tax);
define('TaxQrCode', $system_setting->Tax);
define('sallerQrCode', $system_setting->name_ar);


define('Nameen', $system_setting->name_en);
define('describtionen', $system_setting->descriptionenglish);
define('STen', '  C.R : ' . $system_setting->SR);
define('Taxen', 'VAT Number : ' . $system_setting->Tax);
define('addressar', $system_setting->address_ar);
define('addressen', $system_setting->address_en);
define('camplogo', $system_setting->logo);

    Route::get('get_all_kid_yaomy_jax', [AcountesController::class, 'get_all_kid_yaomy_jax']);
    Route::get('search_by_decoumentNo_kid_yomy/{id}', [AcountesController::class, 'search_by_decoumentNo_kid_yomy']);

    Route::get('/updateofficebyidforupdate/{id}', [ProductsController::class, 'updateofficebyidforupdate']);

Route::get('/export_Liquidation/{id}', [TransactionsController::class, 'export_Liquidation']);
Route::get('/delete_Liquidation/{id}', [TransactionsController::class, 'delete_Liquidation']);
Route::get('/delete_transction/{id}', [TransactionsController::class, 'delete_transction']);
Route::get('/get_liquitation_bystauts/{branch}/{id}', [TransactionsController::class, 'get_liquitation_bystauts']);
Route::get('/getTransction_purchasebystauts/{branch}/{id}', [TransactionsController::class, 'getTransction_purchasebystauts']);
Route::post( '/save_transaction_detailsform',  [TransactionsController::class, 'save_transaction_detailsform']);

    Route::get('getAndUpdate_delyrecord/{id}', [CredittransactionsController::class, 'getAndUpdate_delyrecord']);

Route::post('/liquidation', [ReportController::class, 'search_liquidation']);

Route::get('/liquidation', [ReportController::class, 'liquidation']);
Route::get('/create_purchase', [TransactionsController::class, 'create_purchase']);
Route::get('/update_datials__liquidation/{id}', [TransactionsController::class, 'update_data_shipment']);
Route::get('/update_data_shipment/{id}', [TransactionsController::class, 'update_data_shipment']);
Route::get('/liquidation_ajax/{id}', [TransactionsController::class, 'ajax_liquidation']);
Route::get('/create_transfer',  [TransactionsController::class, 'index']);
Route::get('/create_liquidation',  [TransactionsController::class, 'complet_data']);
Route::get('/complet_data',  [TransactionsController::class, 'complet_data']);
Route::get('/recent_liquidation', [TransactionsController::class, 'previous_liquidations'])
    ->name('recent_liquidation');
    Route::get('/confirm_liquidation_from_owner/{id}', [TransactionsController::class, 'confirm_liquidation_from_owner']);
Route::get('/confirm_data_shipment/{id}', [TransactionsController::class, 'confirm_data_shipment']);
Route::get('/searchaboutTransctionByIdfunction/{id}/{branch}', [TransactionsController::class, 'searchaboutTransctionByIdfunction']);
Route::get('/getTransctionbyBransh/{branch}', [TransactionsController::class, 'getTransctionbyBransh']);
Route::get('/searchTransctionbyDate/{id}/{start_at}/{end_at}', [TransactionsController::class, 'searchTransctionbyDate']);
Route::get('/print_transfers_after_full/{id}', [TransactionsController::class, 'print_transfers_after_full']);
Route::get('/getAndUpdatetransfer/{id}', [TransactionsController::class, 'getAndUpdatetransfer']);
Route::get('/confirm_cancel_Liquidation/{id}', [TransactionsController::class, 'confirm_cancel_Liquidation']);
Route::get('/confirm_cancel_purtchase/{id}', [TransactionsController::class, 'confirm_cancel_purtchase']);
Route::get('/create_transfer', [TransactionsController::class, 'index']);
Route::get('/getpreviousTransfersajax', [TransactionsController::class, 'getpreviousTransfersajax']);
Route::get('/getprevious_liquidationsajax', [TransactionsController::class, 'getprevious_liquidationsajax']);
Route::get('/previousTransfers', [TransactionsController::class, 'previousTransfers']);
Route::get('/show_transction_Recent/{id}', [TransactionsController::class, 'show_transction_Recent']);
Route::post('/add_describtion',  [TransactionsController::class, 'add_describtion']);
Route::post( '/update_describtion',  [TransactionsController::class, 'update_describtion']);
Route::post( '/save_transaction_details',  [TransactionsController::class, 'save_transaction_details']);
Route::post( '/make_update_data_shipment',  [TransactionsController::class, 'make_update_data_shipment']);
Route::post('/create_transfer', [TransactionsController::class, 'create_transfer']);
Route::post('/print_transfers', [TransactionsController::class, 'print_transfers']);
Route::post('/update_transfer_to_mantop', [TransactionsController::class, 'update_transfer_to_mantop']);
Route::get('/search_liquidation_byDate/{invoiceId}/{branchId}', [TransactionsController::class, 'search_liquidation_byDate']);
Route::get('/get_liquidation_byBransh/{invoiceId}', [TransactionsController::class, 'get_liquidation_byBransh']);
Route::get('/searchaboutliquidationByIdfunction/{invoiceId}/{branch}', [TransactionsController::class, 'searchaboutliquidationByIdfunction']);
Route::get('/liqutation_content_search/{invoiceId}/{branch}/{seachtext}', [TransactionsController::class, 'liqutation_content_search']);
Route::post('/purchase_liquidation', [TransactionsController::class, 'purchase_liquidation']);
Route::post('/update_purchase_liquidation', [TransactionsController::class, 'update_purchase_liquidation']);
Route::post('/save_liquidation_data', [TransactionsController::class, 'save_liquidation_data']);
Route::post('/save_liquidation_PURCHASES_data', [TransactionsController::class, 'save_liquidation_PURCHASES_data']);
Route::get('/privvious_purchase_liquidation', [TransactionsController::class, 'privvious_purchase_liquidation']);

Route::get('/search_liquidation_purchase_byByIdfunction/{invoiceId}/{branchId}', [TransactionsController::class, 'search_liquidation_purchase_byByIdfunction']);
Route::get('/getTransction_purchasebyBransh/{invoiceId}', [TransactionsController::class, 'getTransction_purchasebyBransh']);
Route::get('/liquidation_purchase_ajax/{invoiceId}', [TransactionsController::class, 'liquidation_purchase_ajax']);
Route::get('/print_full_purchase/{invoiceId}', [TransactionsController::class, 'print_full_purchase']);
Route::get('/update_data_purchase/{invoiceId}', [TransactionsController::class, 'update_data_purchase']);
Route::get('/confirm_data_purchases/{invoiceId}', [TransactionsController::class, 'confirm_data_purchases']);
Route::get('/search_liquidation_purchase_byDate/{invoiceId}/{branchId}', [TransactionsController::class, 'search_liquidation_purchase_byDate']);


Route::post('save_invoice_qutation', [ProductsController::class, 'save_invoice_qutation']);

Route::get('/shipments_report', [ReportController::class, 'shipments_report']);

Route::post( '/shipments_report',  [ReportController::class, 'search_hipments_report']);








Route::get('/account-balance/{id}', [ReportController::class, 'getAccountBalance'])
    ->name('account.balance');
Route::get('/accountStatement/{supplierId}', [ReportController::class, 'accountStatement']);
    
    

Route::get('/PreviousQuotes', [InvoicesController::class, 'PreviousQuotes']);
Route::get('/searchpreviousquotes/{id}', [InvoicesController::class, 'searchPreviousQuotes']);
Route::get('/getquotebycustomer/{id}', [InvoicesController::class, 'getquotebycustomer']);


Route::get('dwonloadxml/{id}', [InvoicesController::class, 'dwonloadxml']);
Route::get('sent_to_zatca_return_items/{id}', [InvoicesController::class, 'sent_to_zatca_return_items']);
Route::get('sent_to_zatca/{id}', [InvoicesController::class, 'sent_to_zatca']);
Route::get('sendzatca_fromsale/{id}', [InvoicesController::class, 'sendzatca_fromsale']);


Route::post('/posttestajax', [BranchsController::class, 'posttestajax']);


Route::get('replaceproducts/{branch_id}/{productId}', [ProductsController::class, 'replaceproducts']);


Route::get('/', function () {

    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {


    //role


    Route::group(['middleware' => ['auth']], function () {

        Route::resource('roles', RoleController::class);

        Route::resource('users', UserController::class);
    });


    Route::get('/updatepaymentconfirmpayment_in_quotation/{inviceId}/{cashamount}/{bankamount}/{creaditamount}/{Bank_transfer}/{payment}', [InvoicesController::class, 'updatepaymentconfirmpayment_in_quotation']);

    Route::post('/product_branchs_id_ajax', [ProductsController::class, 'product_branchs_id_ajax']);

    Route::get('/createnewcustomers', [SupprocessesController::class, 'createnewcustomers']);




    Route::get('/get_all_customer', [InvoicesController::class, 'get_all_customer']);
    Route::get('/update_customer_name/{id}/{name}', [InvoicesController::class, 'update_customer_name']);

    /*      ═══════ ೋღ   start  ProductsMixController   ღೋ ═══════             */
    Route::get('/getmixproduct/{code}', [ProductsMixController::class, 'getmixproduct']);
    Route::post('/Addmixproduct', [ProductsMixController::class, 'store']);
    Route::post('/updateproduct_mix_Increase', [ProductsMixController::class, 'updateproduct_mix_Increase']);
    Route::post('/updateproduct_mix_decrease', [ProductsMixController::class, 'updateproduct_mix_decrease']);

    /*      ═══════ ೋღ  end DliveryController  ღೋ ═══════              */

    Route::get('/low_sell', [ReportController::class, 'low_sell']);
    Route::get('/profit_and_lost', [ReportController::class, 'profit_and_lost']);
    Route::post('/profit_and_lost', [ReportController::class, 'search_profit_and_lost']);
    Route::post('/low_sell', [ReportController::class, 'low_sell_search']);

    Route::get('/cost_center', [ReportController::class, 'cost_center']);
    Route::get('/sales_and_return', [ReportController::class, 'sales_and_return']);
    Route::post('/sales_and_return', [ReportController::class, 'search_sales_and_return']);

    Route::get('/Customer_debt_restructuring', [ReportController::class, 'Customer_debt_restructuring']);
    Route::get('/Supplier_debt_restructuring', [ReportController::class, 'Supplier_debt_restructuring']);
    Route::post('/Customer_debt_restructuring', [ReportController::class, 'search_Customer_debt_restructuring']);
    Route::post('/cost_center_search', [ReportController::class, 'cost_center_search']);
    Route::post('/Supplier_debt_restructuring', [ReportController::class, 'search_Supplier_debt_restructuring']);




    /*      ═══════ ೋღ   start  deliver_to_anoter_supplier   ღೋ ═══════             */
    Route::get('/account_type', [AcountsTypeController::class, 'index']);

    /*      ═══════ ೋღ  end DliveryController  ღೋ ═══════              */


    /*      ═══════ ೋღ   start  FinancialAccountsController   ღೋ ═══════             */
    Route::get('/financial_accounts', [FinancialAccountsController::class, 'index']);
    Route::get('/tree', [FinancialAccountsController::class, 'tree']);
    Route::get('/create_acount', [FinancialAccountsController::class, 'create_new_acount']);
    Route::get('/getAllaccountsajax', [FinancialAccountsController::class, 'ajax_choose_account']);

    Route::get('/update_acount/{id}', [FinancialAccountsController::class, 'update_acount']);
    Route::post('/add_new_acount_finance', [FinancialAccountsController::class, 'add_new_acount_finance']);
    Route::get('/getfinancialaccount/{id}', [FinancialAccountsController::class, 'getfinancialaccount']);
    Route::get('/searchaboutaccountByname_numberfunction/{text}', [FinancialAccountsController::class, 'searchaboutaccountByname_numberfunction']);
    Route::get('/searchaboutaccountBytype_function/{text}', [FinancialAccountsController::class, 'searchaboutaccountBytype_function']);
    Route::get('/searchMaster_account_function/{text}', [FinancialAccountsController::class, 'searchMaster_account_function']);

    /*      ═══════ ೋღ  end DliveryController  ღೋ ═══════              */

    //end role
    /*      ═══════ ೋღ   start  deliver_to_anoter_supplier   ღೋ ═══════             */
    Route::get('/deliver_to_anoter_supplier', [DliveryController::class, 'index']);
    Route::get('/confirmdelivery', [DliveryController::class, 'confirmdelivery']);
    Route::post('/Addproduct_to_dlivery_supplier', [DliveryController::class, 'store']);
    Route::post('/updateproductallDatadelivery', [DliveryController::class, 'updateproductallDatadelivery']);
    Route::post('/print_delivery_to_anoter_supplier', [DliveryController::class, 'print_delivery_to_anoter_supplier']);
    Route::post('/print_delivery_invoice', [DliveryController::class, 'print_delivery_invoice']);
    Route::get('/deleteitemdelivery/{id}', [DliveryController::class, 'deleteitemdelivery']);
    Route::get('/getcustomerproductsdelivery/{id}', [DliveryController::class, 'getcustomerproductsdelivery']);
    Route::get('/getitems/{id}', [DliveryController::class, 'getitems']);
    Route::get('/deleteitemdeliveryconfirm/{id}', [DliveryController::class, 'deleteitemdeliveryconfirm']);

    /*      ═══════ ೋღ  end DliveryController  ღೋ ═══════              */

    Route::get('/deleteitem/{id}', [ProductsController::class, 'deleteitem']);
    Route::post('/product_group_ajax', [ProductsController::class, 'product_group_ajax']);
    Route::post('/product_sale_group_ajax', [ProductsController::class, 'product_sale_group_ajax']);
    Route::post('/updateproductallDataofferprice', [ProductsController::class, 'updateproductallDataofferprice']);



    /*      ═══════ ೋღ   start  System setting    ღೋ ═══════             */
    Route::get('/systemSetting', [SystemSettingController::class, 'index']);
    Route::get('/onbourding', [SystemSettingController::class, 'onbourding']);
    Route::post('/onbourding', [SystemSettingController::class, 'store']);
    Route::post('/updateCamData', [SystemSettingController::class, 'update']);





    /*      ═══════ ೋღ  end System setting  ღೋ ═══════              */


    /*      ═══════ ೋღ   start   LoansController    ღೋ ═══════             */
    Route::get('/Loans', [LoansController::class, 'index']);
    Route::get('/delete_Loans/{id}', [LoansController::class, 'destroy']);
    Route::post('/Loans', [LoansController::class, 'store']);

    Route::post('/update_Loans', [LoansController::class, 'edit']);




    /*      ═══════ ೋღ  end LoansController   ღೋ ═══════              */

    //Confirm product delivery

    Route::get('confirm_delivery', [DeliveryProductToTheCustomerController::class, 'index']);
    Route::get('confirm_sales', [DeliveryProductToTheCustomerController::class, 'confirm_sales']);
    Route::post('search_confirm_delievery', [DeliveryProductToTheCustomerController::class, 'store']);
    Route::post('confirm_delivery_all', [DeliveryProductToTheCustomerController::class, 'edit']);
    Route::post('search_confirm_sales', [DeliveryProductToTheCustomerController::class, 'search_confirm_sales']);
    Route::post('confirm_sales_delivery_all', [DeliveryProductToTheCustomerController::class, 'confirm_sales_delivery_all']);


    //end confirm_delivery
    //ProductsDamage

    Route::get('ProductsDamageReport', [ProductsDamageController::class, 'index']);
    Route::post('ProductsDamageReport', [ProductsDamageController::class, 'show']);



    //end ProductsDamage
//product_movement_another_branch

    Route::get('sendProduct', [ProductMovementAnotherBranchController::class, 'index']);
    Route::get('reciveProduct', [ProductMovementAnotherBranchController::class, 'show']);
    Route::get('deleteproduct/{id}', [ProductMovementAnotherBranchController::class, 'destroy']);
    Route::get('findinvoiceMovmevt/{id}', [ProductMovementAnotherBranchController::class, 'findinvoiceMovmevt']);
    Route::post('create_sendProduct', [ProductMovementAnotherBranchController::class, 'create']);


    Route::post('reciveNewF', [ProductMovementAnotherBranchController::class, 'reciveNewF']);
    Route::get('deleteproductrecive/{id}', [ProductMovementAnotherBranchController::class, 'deleteproduct']);


    Route::post('create_reciveProduct', [ProductMovementAnotherBranchController::class, 'store']);
    Route::post('print_Transfer_items', [ProductMovementAnotherBranchController::class, 'print_Transfer_items']);
    Route::post('print_Recive_items', [ProductMovementAnotherBranchController::class, 'print_Recive_items']);

    Route::get('/detproductbycode/{idInvoice}/{branch}', [ProductsController::class, 'detproductbycode']);

    Route::get('getallpurshasesfromsupplier/{id}', [ProductsController::class, 'getallpurshasesfromsupplier']);

    Route::get('/updateinvoicebyid/{id}', [InvoicesController::class, 'updateinvoicebyid']);
    Route::get('/updateinvoicebyidforsaleupdate/{id}', [InvoicesController::class, 'updateinvoicebyidforsaleupdate']);
    Route::get('/generate_pdf_qoute/{id}', [ProductsController::class, 'generate_pdf_qoute']);

    Route::get('/updatepurchasesbyid/{id}', [ProductsController::class, 'updatepurchasesbyid']);
    //end product_movement_another_branch
    //products
    Route::get('getAllinvicesapurchasesjax', [ProductsController::class, 'getAllinvicesapurchasesjax']);
    Route::get('/updatepaymentconfirmpaymentpurchases/{inviceId}/{cashamount}/{bankamount}/{creaditamount}/{Bank_transfer}/{payment}', [InvoicesController::class, 'updatepaymentconfirmpaymentpurchases']);
    Route::get('searchaboutinvoiceByIdfunctionpurchases/{date}', [ProductsController::class, 'searchaboutinvoiceByIdfunctionpurchases']);
    Route::get('getinvoicesbycustomer/{date}', [ProductsController::class, 'getinvoicesbycustomer']);
    Route::get('goToSale', [ProductsController::class, 'goToSale']);
    Route::get('goToSaleBypage', [ProductsController::class, 'goToSaleByPage']);
    Route::get('searchaboutproduct/{searchtext}', [ProductsController::class, 'searchaboutproduct']);
    Route::get('/updatequtation/{id}', [ProductsController::class, 'updatequtation']);


    Route::get('product_mix', [ProductsController::class, 'product_mix']);
    Route::get('showAllproductpaginate', [ProductsController::class, 'showAllproductpaginate']);
    Route::get('searchAllproductpaginate/{searchtext}', [ProductsController::class, 'searchAllproductpaginate']);
    Route::get('searchAllproductpaginatenew/{searchtext}', [ProductsController::class, 'searchAllproductpaginatenew']);
    Route::post('searchAllproductpaginatenew_by_post', [ProductsController::class, 'searchAllproductpaginatenew_by_post']);
    Route::get('searchAllInvoicespaginatenew/{date}', [ProductsController::class, 'searchAllInvoicespaginatenew']);
    Route::get('searchaboutinvoiceByIdfunction/{date}', [ProductsController::class, 'searchaboutinvoiceByIdfunction']);
    Route::get('searchaboutReciptByIdfunction/{date}', [ProductsController::class, 'searchaboutReciptByIdfunction']);
    Route::get('searchAllRecieptspaginatenew/{date}', [ProductsController::class, 'searchAllRecieptspaginatenew']);
    Route::get('Allproductpaginatenew', [ProductsController::class, 'Allproductpaginatenew']);
    Route::get('getAllinvicesajax', [ProductsController::class, 'getAllinvicesajax']);
    Route::get('getAllRecieptsjax', [ProductsController::class, 'getAllRecieptsjax']);
    Route::get('searchChooseProductpaginatenew/{searchtext}/{branch_id}', [ProductsController::class, 'searchChooseProductpaginatenew']);
    Route::get('ChooseProductpaginatenew/{branch_id}', [ProductsController::class, 'ChooseProductpaginatenew']);

    Route::get('searchChooseProductpaginatenewSale/{searchtext}/{branch_id}', [ProductsController::class, 'searchChooseProductpaginatenewSale']);
    Route::post('searchChooseProductpaginatenewSaleBypost', [ProductsController::class, 'searchChooseProductpaginatenewSaleBypost']);
    Route::post('searchChooseProductpaginatenewpurchaseBypost', [ProductsController::class, 'searchChooseProductpaginatenewpurchaseBypost']);
    Route::get('ChooseProductpaginatenewSale/{branch_id}', [ProductsController::class, 'ChooseProductpaginatenewSale']);

    Route::get('showAllproductpaginatepurchase/{branchId}', [ProductsController::class, 'showAllproductpaginatepurchase']);
    Route::get('searchAllproductpaginatepurchase/{branchId}/{searchtext}', [ProductsController::class, 'searchAllproductpaginatepurchase']);


    Route::get('searchaboutproductwithBranchId/{searchtext}/{branchId}', [ProductsController::class, 'searchaboutproductwithBranchId']);
    Route::post('ChooseProductpaginatenewupdate', [ProductsController::class, 'ChooseProductpaginatenewupdate']);

    Route::get('printReturnpurchases/{id}', [ProductsController::class, 'printReturnpurchases']);

    Route::get('ShowAllNotifications', [ProductsController::class, 'ShowAllNotifications']);
    Route::get('profile', [ProductsController::class, 'profile']);
    Route::get('showAllProducts', [ProductsController::class, 'showAllProducts']);
    Route::get('previousPurchasesInvoices', [ProductsController::class, 'previousPurchasesInvoices']);
    Route::get('previousSalesInvoices', [ProductsController::class, 'previousSalesInvoices']);
    Route::get('previousRecieptInvoices', [ProductsController::class, 'previousRecieptInvoices']);
    Route::get('printOrderPriceFromSupplier/{id}', [ProductsController::class, 'printOrderPriceFromSupplier']);
    Route::post('printOrderPriceFromSupplier', [ProductsController::class, 'printOrderPriceFromSupplierBypost']);
    Route::get('getproductsquntitytocustomer', [ProductsController::class, 'index']);
    Route::get('getproductspricetocustomer', [ProductsController::class, 'showProductsPrice']);
    Route::get('getproductsprice', [ProductsController::class, 'getProductsPriceFromSupplier']);
    Route::get('getproduct/{id}', [ProductsController::class, 'show']);
    Route::get('savepurchase/{id}/{payment}/{supplier}/{shipping}/{date}/{another_bank}', [ProductsController::class, 'savepurchase']);
    Route::get('getProductdJsonDecode/{id}', [ProductsController::class, 'getProductdJsonDecode']);
    Route::get('updateorder_purchase/{id}', [ProductsController::class, 'updateorder_purchase']);
    Route::get('Purchase_returns', [ProductsController::class, 'Purchase_returns']);
    Route::get('purchases', [ProductsController::class, 'purchases']);
    Route::post('printavaliableproduct', [ProductsController::class, 'create']);
    Route::get('printavaliableproductprice', [ProductsController::class, 'printProductPriceToCustomer']);
    Route::post('printproductprice', [ProductsController::class, 'printProductPrice']);
    Route::post('print_all_products_price', [ProductsController::class, 'print_all_products_price']);
    Route::post('AddproducttoSupllier', [ProductsController::class, 'AddproducttoSupllier']);
    Route::post('Addproducttopurchases', [ProductsController::class, 'Addproducttopurchases']);
    Route::post('purchaseproduct_update', [ProductsController::class, 'update']);
    Route::post('purchaseproduct_delete', [ProductsController::class, 'destroy']);
    Route::get('goToReceipt', [ProductsController::class, 'goToReceipt']);
    Route::get('update_offer_price_supplier/{id}', [ProductsController::class, 'update_offer_price_supplier']);
    Route::post('order_price_from_suppliers', [ProductsController::class, 'order_price_from_suppliers']);
    Route::post('report_offer_price_customer', [ProductsController::class, 'AddproductPriceToCustomer']);
    Route::post('AddproductPriceToCustomer', [ProductsController::class, 'AddproductPriceToCustomer']);
    Route::post('print_order_perice_to_customer', [ProductsController::class, 'print_order_perice_to_customerByPost']);
    Route::get('report_offer_price_customer', [ProductsController::class, 'print_order_perice_to_customer']);
    Route::get('set_customer_quotation/{id}/{customer}', [ProductsController::class, 'set_customer_quotation']);
    Route::post('updatePurchase', [ProductsController::class, 'updatePurchase']);
    Route::post('updatePurchaseOrder', [ProductsController::class, 'updatePurchaseOrder']);
    Route::post('updatePurchaseOrderToIncrease', [ProductsController::class, 'updatePurchaseOrderToIncrease']);
    Route::get('/makeTotalDiscontpurchases/{idInvoice}/{discountvalue}', [ProductsController::class, 'makeTotalDiscontpurchases']);
    Route::get('/makeTotalDiscontOferprice/{idInvoice}/{discountvalue}', [ProductsController::class, 'makeTotalDiscontOferprice']);
    Route::get('/cancelInvoiceDiscontpurcgases/{idInvoice}', [ProductsController::class, 'cancelInvoiceDiscontpurcgases']);

    Route::post('increasePurchase', [ProductsController::class, 'increasePurchase']);
    Route::post('uploadfilepurchases', [ProductsController::class, 'uploadfilepurchases']);
    Route::post('updateproductalldatapurchases', [ProductsController::class, 'updateproductalldatapurchases']);

    Route::get('get_all_products_in_orderto_supplier/{order_id}', [ProductsController::class, 'get_all_products_in_orderto_supplier']);


    Route::get('getinvoicesbyspplluer/{order_id}', [ProductsController::class, 'getinvoicesbyspplluer']);



    Route::get('changePaymethodPurchase/{id}/{paymendMethod}', [ProductsController::class, 'changePaymethodIPurchases']);




    //end product

    //start transfer money to main branch

    Route::post('Transfertomainbranch', [TransferMoneyToMainbranchController::class, 'store']);
    Route::post('updateTransfertomainbranch', [TransferMoneyToMainbranchController::class, 'updateTransfertomainbranch']);
    Route::post('updateTransfertomainbranchnotconfirm', [TransferMoneyToMainbranchController::class, 'updateTransfertomainbranchnotconfirm']);
    Route::get('confirmTransfarToMainBranch/{id}', [TransferMoneyToMainbranchController::class, 'show']);
    Route::get('rejectTransfarToMainBranch/{id}', [TransferMoneyToMainbranchController::class, 'rejectTransfarToMainBranch']);
    Route::get('pendingtransfers', [TransferMoneyToMainbranchController::class, 'pendingtransfers']);
    Route::post('print_Transfer_Main_Branch', [TransferMoneyToMainbranchController::class, 'print_Transfer_Main_Branch']);




    //end 


    //++++++++++++++++++
    //accountes
    Route::post('Cash_withdrawal_from_the_bank', [CashWithdrawalFromTheBankController::class, 'Cash_withdrawal_from_the_bank']);
    Route::post('printwithdrawal_from_the_bank', [CashWithdrawalFromTheBankController::class, 'printwithdrawal_from_the_bank']);
    Route::get('Cash_withdrawal_from_the_bank', [AcountesController::class, 'Cash_withdrawal_from_the_bank']);




    Route::get('voncher', [AcountesController::class, 'voncher']);
    Route::get('get_all_send_kabd_jax', [AcountesController::class, 'get_all_send_kabd_jax']);
    Route::get('get_all_send_serf_jax', [AcountesController::class, 'get_all_send_serf_jax']);
    Route::get('opining_balnce_ajax', [AcountesController::class, 'opining_balnce_ajax']);
    Route::get('convertcashboxToBank', [AcountesController::class, 'convertcashboxToBank']);
    Route::get('Transfertomainbranch', [AcountesController::class, 'transferMainBranch']);
    Route::get('confirmTransfertomainbranch', [AcountesController::class, 'confirmTransfertomainbranch']);
    Route::get('cashEcprnse', [AcountesController::class, 'cashEcprnse']);
    Route::get('income', [AcountesController::class, 'income']);
    Route::get('go_to_bank', [AcountesController::class, 'go_to_bank']);
    Route::post('Add_blance_from_bank', [AcountesController::class, 'Add_blance_from_bank']);
    Route::post('updateAdd_blance_from_bank', [AcountesController::class, 'updateAdd_blance_from_bank']);
    Route::post('convertcashboxToBank', [AcountesController::class, 'SearchconvertcashboxToBank']);
    Route::post('printconvertcashboxToBank', [AcountesController::class, 'printconvertcashboxToBank']);

    Route::get('reciept_decoument', [AcountesController::class, 'reciept_decoument']);



    Route::get('Transfer_cash_to_next_day', [AcountesController::class, 'Transfer_cash_to_next_day']);
    Route::get('Transfer_cash_to_next_day', [AcountesController::class, 'Transfer_cash_to_next_day']);
    Route::post('/Transfercashto_the_next_day', [AcountesController::class, 'Transfercashto_the_next_day']);
    Route::post('/updatedecoumentcashNextDay', [AcountesController::class, 'updatedecoumentcashNextDay']);



    Route::post('/print_reciept', [AcountesController::class, 'print_voucher']);
    Route::post('/print_reciept_ducoument', [AcountesController::class, 'print_reciept_ducoument']);
    Route::post('/print_expansedecoument', [AcountesController::class, 'print_expansedecoument']);


    Route::get('/generate_pdf_reciept_ducoument/{id}', [AcountesController::class, 'generate_pdf']);



    //end accountes






    //++++++++++++++++++++
    //Credittransactions
    Route::get('Opening_entry', [CredittransactionsController::class, 'Opening_entry']);
    Route::get('search_by_decoumentNo_send_abd/{id}', [CredittransactionsController::class, 'search_by_decoumentNo_send_abd']);
    Route::get('search_by_decoumentNo_send_serf/{id}', [CredittransactionsController::class, 'search_by_decoumentNo_send_serf']);
    Route::post('create_daily_record', [CredittransactionsController::class, 'daily_record']);
    Route::post('updatedelyrecord', [CredittransactionsController::class, 'updatedelyrecord']);
    Route::post('create_Opening_entry', [CredittransactionsController::class, 'create_Opening_entry']);
    Route::post('update_oping_blance', [CredittransactionsController::class, 'update_oping_blance']);
    Route::get('Daily_record', [CredittransactionsController::class, 'index']);
    Route::get('save_Daily_record/{id}', [CredittransactionsController::class, 'save_Daily_record']);
    Route::get('save_Opening_entry/{id}', [CredittransactionsController::class, 'save_Opening_entry']);
    Route::get('getAndUpdatevoncher/{id}', [CredittransactionsController::class, 'getAndUpdatevoncher']);
    Route::get('getAndUpdate_reciptdecument/{id}', [CredittransactionsController::class, 'getAndUpdate_reciptdecument']);
    Route::post('Credittransactions', [CredittransactionsController::class, 'create']);
    Route::post('updateVoncher', [CredittransactionsController::class, 'updateVoncher']);
    Route::post('reciepttransactions', [CredittransactionsController::class, 'store']);
    Route::post('updaterecieptdecoument', [CredittransactionsController::class, 'updaterecieptdecoument']);
    Route::post('print_daily_record', [CredittransactionsController::class, 'print_daily_record']);
    Route::post('print_Opening_entry', [CredittransactionsController::class, 'print_Opening_entry']);



    //endCredittransactions




    //+++++++++++++++++++
    //Expenses

    Route::get('getAndUpdateExpenses/{id}', [ExpensesController::class, 'getAndUpdateExpenses']);
    Route::post('Expenses', [ExpensesController::class, 'store']);
    Route::post('updateExpenses', [ExpensesController::class, 'updateExpenses']);
    Route::post('ExpensesOwner', [ExpensesController::class, 'ExpensesOwner']);



    //end Expenses




    //+++++++++++++++++++
    //invoices
    Route::get('/generate_pdf/{id}', [InvoicesController::class, 'generate_pdf']);
    Route::get('/generate_pdf_customer_list', [InvoicesController::class, 'generate_pdf_customer_list']);
    Route::get('/generate_return_sale_pdf/{id}', [InvoicesController::class, 'generate_return_sale_pdf']);

    Route::get('operationproducts/{branch_id}/{productId}', [ProductsController::class, 'operationproducts']);
    Route::get('openfile/{path}', [ProductsController::class, 'openfilefile']);

    Route::post('AddInvoices', [InvoicesController::class, 'store']);
    Route::post('Receipt', [InvoicesController::class, 'Receipt']);
    Route::post('EditInvoices', [InvoicesController::class, 'edit']);
    Route::post('updateproductallDataInvoices', [InvoicesController::class, 'updateproductallDataInvoices']);
    Route::post('editRecipt', [InvoicesController::class, 'editRecipt']);
    Route::post('returnAll', [InvoicesController::class, 'returnAll']);

    Route::post('increaseProduct', [InvoicesController::class, 'increaseProduct']);
    Route::get('/makeTotalDiscont/{idInvoice}/{discountvalue}', [InvoicesController::class, 'makeTotalDiscont']);


    Route::get('/makenoteoninvoice/{idInvoice}/{notecontent}', [InvoicesController::class, 'makenoteoninvoice']);



    Route::get('/confirmpaymentconfirmpayment/{inviceId}/{cashamount}/{bankamount}/{creaditamount}/{Bank_transfer}/{payment}/{customerId}/{numbershowstatus}/{date}/{anotherbank}/{p_o}/{shippingfee}', [InvoicesController::class, 'confirmpaymentconfirmpayment']);
    Route::get('/updatepaymentconfirmpayment/{inviceId}/{cashamount}/{bankamount}/{creaditamount}/{Bank_transfer}/{payment}/{another_bank}', [InvoicesController::class, 'updatepaymentconfirmpayment']);
    Route::get('/updatepaymentconfirmpaymentReciept/{inviceId}/{cashamount}/{bankamount}/{creaditamount}/{Bank_transfer}/{payment}', [InvoicesController::class, 'updatepaymentconfirmpaymentReciept']);
    Route::get('/cancelInvoiceDiscont/{idInvoice}', [InvoicesController::class, 'cancelInvoiceDiscont']);
    Route::get('/getproductbyCode/{code}', [InvoicesController::class, 'getproductbyCode']);
    Route::post('/getByCode', [InvoicesController::class, 'getByCode']);

    Route::get('printInvoice/{id}', [InvoicesController::class, 'printInvoice']);
    Route::get('saveInvoice/{id}', [InvoicesController::class, 'saveInvoice']);
    Route::get('printreturnInvoice/{id}', [InvoicesController::class, 'printreturnInvoice']);
    Route::get('returnsalesprinter/{id}', [InvoicesController::class, 'returnsalesprinter']);
    Route::get('showInvoice/{id}', [InvoicesController::class, 'showInvoice']);
    Route::get('showInvoiceRecent/{id}', [InvoicesController::class, 'showInvoiceRecent']);
    Route::get('showRecieptRecent/{id}', [InvoicesController::class, 'showRecieptRecent']);
    Route::get('getlastprice/{productId}/{customerId}', [InvoicesController::class, 'getlastprice']);
    Route::get('getlastprice_offer_price/{productId}/{customerId}', [InvoicesController::class, 'getlastprice_offer_price']);



    //update 17/7/2023

    Route::post('printInvoice', [InvoicesController::class, 'printInvoice']);
    Route::post('updateReciept', [InvoicesController::class, 'updateReciept']);

    //end





    Route::post('return_sale', [InvoicesController::class, 'return_sale']);
    Route::get('return_sale', [InvoicesController::class, 'index']);
    Route::post('update_return_sale', [InvoicesController::class, 'update_return_Sale']);
    Route::post('printReceiptToStorehouse', [InvoicesController::class, 'printReceiptToStorehouse']);
    Route::get('changePaymethodIninvoice/{id}/{paymendMethod}', [InvoicesController::class, 'changePaymethodIninvoice']);
    Route::get('changechustomer/{id}/{paymendMethod}', [InvoicesController::class, 'changechustomerInInvoice']);
    Route::post('updatecustomerDataInvoice', [InvoicesController::class, 'updatecustomerDataInvoice']);
    Route::post('updatecustomerDataRecipt', [InvoicesController::class, 'updatecustomerDataRecipt']);





    //endinvoices





    //++++++++++++++++++++++++++++
    //supllier


    Route::get('Purchase_order_of_resources', [SupllierController::class, 'index']);
    Route::get('getsupllier/{id}', [SupllierController::class, 'show']);
    Route::get('purchasesShow/{id}', [SupllierController::class, 'purchasesShow']);
    Route::post('printProductToSupllier', [SupllierController::class, 'edit']);
    Route::post('printProductToSupllierOrder', [SupllierController::class, 'printProductToSupllierOrder']);
    Route::get('printProductToSupllierOrder_pdf/{id}', [SupllierController::class, 'printProductToSupllierOrder_pdf']);

    Route::get('printProductToSupllier/{id}', [SupllierController::class, 'prindorderToSupplier']);
    Route::post('Purchase_returns_Data', [ProductsController::class, 'Purchase_returns_Data']);





    //end supplier
    //+++++++++++++++++++++++++++
    //users

    Route::get('getallusers', [AdminController::class, 'show']);
    Route::get('updateuser/{id}', [AdminController::class, 'edit']);
    Route::get('deleteuser/{id}', [AdminController::class, 'destroy']);


    //end user





    //+++++++++++++++++++++++++++
    //customer


    Route::get('/getcustomer/{id}', [CustomersController::class, 'show']);




    //endcustomer



    //+++++++++++++++
    //Reports

    Route::get('/account_statement', [ReportController::class, 'account_statement']);
    Route::get('/our_backup_database', [ReportController::class, 'serverDBBackup']);
    Route::get('/Daily_record_report', [ReportController::class, 'Daily_record_report']);
    Route::get('/product_sales_purchases', [ReportController::class, 'product_sales_purchases']);
    Route::post('/account_statement', [ReportController::class, 'search_account_statement']);
    Route::post('/product_sales_purchases', [ReportController::class, 'search_product_sales_purchases']);
    Route::post('/search_Daily_record_report', [ReportController::class, 'search_Daily_record_report']);



    Route::get('/Bank_Statement', [ReportController::class, 'Bank_Statement']);
    Route::post('/bankDecument', [ReportController::class, 'searchbankDecument']);


    Route::get('/ConvertBoxtobankReport', [ReportController::class, 'ConvertBoxtobankReport']);
    Route::post('/ConvertBoxtobankReport', [ReportController::class, 'searchConvertBoxtobankReport']);
    Route::get('/transactionsToMasterBranch', [ReportController::class, 'transactionsToMasterBranch']);
    Route::post('/searchtransactionsToMasterBranch', [ReportController::class, 'searchtransactionsToMasterBranch']);
    Route::get('/Bank_Transfer', [ReportController::class, 'Bank_Transfer']);
    Route::post('/Bank_Transfer', [ReportController::class, 'search_Bank_Transfer']);
    Route::get('/print_Bank_Transfer/{branch}/{start}/{end}', [ReportController::class, 'print_Bank_Transfer']);
    Route::get('/print_products_Transfer/{branchfrom}/{branchto}/{start}/{end}', [ReportController::class, 'print_products_Transfer']);
    Route::get('/Transfer_products', [ReportController::class, 'products_Transfer']);
    Route::post('/search_Transfer_products', [ReportController::class, 'search_products_Transfer']);
    Route::get('/print_Transfer_products/{invoiceId}', [ReportController::class, 'print_Transfer_products']);
 
    Route::get('/print_sales_and_purchases/{invoiceId}/{start}/{end}', [ReportController::class, 'print_sales_and_purchases']);

    Route::post('/stockquantity', [ReportController::class, 'search_stockquantity']);
    Route::get('/search_stockquantityPagination/{searchtext}/{branchId}', [ReportController::class, 'search_stockquantityPagination']);
    Route::get('/stockquantityPagination/{branchId}/{chooseOperation}/{quantity}', [ReportController::class, 'stockquantityPagination']);


    Route::get('/updatestockquentity', [ReportController::class, 'updatestockquentity']);
    Route::post('/updatestockquentity', [ReportController::class, 'search_updatestockquentity']);

    Route::get('/generate_customer_statment_pdf/{customerId}/{start}/{end}', [ReportController::class, 'generate_customer_statment_pdf']);


    Route::get('/employeeـsales', [ReportController::class, 'employeeـsales']);
    Route::get('/showallBranchs', [ReportController::class, 'showallBranchs']);
    Route::get('/salesـprofits', [ReportController::class, 'salesـprofits']);
    Route::post('/salesReport', [ReportController::class, 'salesReportsearch']);
    Route::post('/salesـprofits', [ReportController::class, 'salesـprofitssearch']);
    Route::get('/salesReport', [ReportController::class, 'salesReport']);
    Route::get('/Show_return_Sales_Details/{invoiceId}', [ReportController::class, 'Show_return_Sales_Details']);
    Route::get('/printInvoicesReportdetails/{branch}/{paymethod}/{startat}/{endat}', [ReportController::class, 'printReportsaleswithoud_deatails']);

    Route::post('/search_Requestـoffersـfromـsuppliers', [ReportController::class, 'search_Requestـoffersـfromـsuppliers']);
    Route::post('/employeeSalesSearch', [ReportController::class, 'employeeSalesSearch']);
    Route::get('/printInvoicesReport/{branch}/{paymethod}/{startat}/{endat}', [ReportController::class, 'printInvoicesReport']);
    Route::get('/printInvoicesAllItemsWithReturned/{id}', [ReportController::class, 'printInvoicesAllItemsWithReturned']);
    Route::get('/report_returns_sale', [ReportController::class, 'report_returns_sale']);
    Route::post('/search_report_returns_sale', [ReportController::class, 'search_report_returns_sale']);
    Route::get('/printreturnInvoicesReport/{branch}/{startat}/{endat}', [ReportController::class, 'print_return_Report']);
    Route::get('/printReportProductSales/{branch}/{productId}/{startat}/{endat}', [ReportController::class, 'printReportProductSales']);
    Route::get('/printReportemployeeSales/{userId}/{startat}/{endat}', [ReportController::class, 'printReportemployeeSales']);
    Route::get('/printReportProfitSales/{branch_id}/{userId}/{startat}/{endat}', [ReportController::class, 'printReportProfitSales']);
    Route::get('/print_report_order_from_supplier/{SupplierId}/{startat}/{endat}', [ReportController::class, 'print_report_order_from_supplier']);
    Route::get('/printReportoffer_price_customer/{SupplierId}/{startat}/{endat}', [ReportController::class, 'printReportoffer_price_customer']);
    Route::get('/Requestـoffersـfromـsuppliers', [ReportController::class, 'Requestـoffersـfromـsuppliers']);
    Route::get('/product_sales', [ReportController::class, 'product_sales']);
    Route::post('/product_sales', [ReportController::class, 'search_product_sales']);
    Route::get('/report_offer_price_customer', [ReportController::class, 'report_offer_price_customer']);
    Route::post('/show_offer_price_customer', [ReportController::class, 'show_offer_price_customer']);

    Route::get('/Delivery_notes', [ReportController::class, 'Delivery_notes']);
    Route::post('/Delivery_notes', [ReportController::class, 'search_Delivery_notes']);
    Route::get('/printDelivery_notes/{orderId}/{startat}/{endat}', [ReportController::class, 'printDelivery_notes']);


    Route::get('/Requestـaـquoteـfromـtheـsupplier', [ReportController::class, 'Requestـaـquoteـfromـtheـsupplier']);
    Route::post('/Requestـaـquoteـfromـtheـsupplier', [ReportController::class, 'search_Requestـaـquoteـfromـtheـsupplier']);
    Route::get('/print_Requestـaـquoteـfromـtheـsupplier/{branchId}/{supplier}/{startat}/{endat}', [ReportController::class, 'print_Requestـaـquoteـfromـtheـsupplier']);

    Route::get('/Purchasesـfromـsuppliers', [ReportController::class, 'Purchasesـfromـsuppliers']);
    Route::post('/Purchasesـfromـsuppliers', [ReportController::class, 'search_Purchasesـfromـsuppliers']);





    Route::get('/print_Purchasesـfromـsuppliers/{branch}/{pay}/{supplierId}/{startat}/{endat}', [ReportController::class, 'print_Purchasesـfromـsuppliers']);
    Route::get('Refundـofـresourceـpurchases', [ReportController::class, 'Refundـofـresourceـpurchases']);

    Route::post('/Refundـofـresourceـpurchases', [ReportController::class, 'search_Refundـofـresourceـpurchases']);
    Route::get('/print_Refundـofـresourceـpurchases/{branch_id}/{startat}/{endat}', [ReportController::class, 'print_Refundـofـresourceـpurchases']);


    Route::get('/purchasereports', [ReportController::class, 'purchasereports']);
    Route::post('/purchasereports', [ReportController::class, 'search_purchasereports']);
    Route::get('/print_purchasereports/{productId}/{startat}/{endat}', [ReportController::class, 'print_purchasereports']);

    Route::get('/customerـpurchases', [ReportController::class, 'customerـpurchases']);
    Route::get('/purchasproducttocustomer', [ReportController::class, 'purchasproducttocustomer']);
    Route::post('/customerـpurchases', [ReportController::class, 'search_customerـpurchases']);
    Route::post('/purchasproducttocustomer', [ReportController::class, 'searchpurchasproducttocustomer']);
    Route::get('/print_customerـpurchases/{branchId}/{customerId}/{startat}/{endat}', [ReportController::class, 'print_customerـpurchases']);

    Route::get('/credit_collection', [ReportController::class, 'credit_collection']);
    Route::post('/credit_collection', [ReportController::class, 'search_credit_collection']);
    Route::get('/print_credit_collection/{userId}/{startat}/{endat}', [ReportController::class, 'print_credit_collection']);



    Route::get('/supplierlist', [ReportController::class, 'supplierList']);
    Route::get('/Customerlist', [ReportController::class, 'Customerlist']);
    Route::get('/print_supplierlist/{userId}/{startat}/{endat}', [ReportController::class, 'print_supplierlist']);
    Route::get('/print_customeList', [ReportController::class, 'print_customeList']);
    Route::get('/Stocktaking', [ReportController::class, 'Stocktaking']);


    Route::get('/customerslist_export', [ReportController::class, 'customerslist_export']);
    Route::get('/supplierlist_export', [ReportController::class, 'supplierlist_export']);
    Route::get('/Stocktakingpdf', [ReportController::class, 'Stocktakingpdf']);

    Route::get('/print_SupplierList', [ReportController::class, 'print_supplierList']);


    Route::get('/Supplier_account_statement', [ReportController::class, 'Supplier_account_statement']);
    Route::post('/Supplier_account_statement', [ReportController::class, 'search_Supplier_account_statement']);


    Route::get('/Customer_account_statement', [ReportController::class, 'Customer_account_statement']);
    Route::post('/Customer_account_statement', [ReportController::class, 'search_Customer_account_statement']);
    Route::get('/TransFerCashTothenNextDay', [ReportController::class, 'TransFerCashTothenNextDay']);
    Route::post('/TransFerCashTothenNextDay', [ReportController::class, 'search_TransFerCashTothenNextDay']);






    Route::get('/Supplier_credit_payment', [ReportController::class, 'Supplier_credit_payment']);
    Route::post('/Supplier_credit_payment', [ReportController::class, 'search_Supplier_credit_payment']);
    Route::get('/print_Supplier_credit_payment/{supplierId}/{startat}/{endat}', [ReportController::class, 'print_Supplier_credit_payment']);

    Route::get('/shift_detailes', [ReportController::class, 'shift_detailes']);
    Route::post('/shift_detailes', [ReportController::class, 'search_shift_detailes']);
    Route::get('/print_shift_detailes/{branch_id}/{paumethod}/{startat}/{endat}', [ReportController::class, 'print_shift_detailes']);


    Route::get('/Expensesreport', [ReportController::class, 'Expenses']);
    Route::post('/Expensesreport', [ReportController::class, 'search_Expenses']);
    Route::get('/printExpensesReport/{branch_id}/{reson}/{startat}/{endat}', [ReportController::class, 'printExpensesReportlast']);

    Route::get('/financial_accounts_Export', [ReportController::class, 'financial_accounts_Export']);
    Route::get('/financial_accounts_Export_CSV', [ReportController::class, 'financial_accounts_Export_CSV']);
    Route::get('/stockquantity', [ReportController::class, 'stockquantity']);
    Route::get('/printstockquantity/{branch_id}/{display}/{quantity}/{loction}', [ReportController::class, 'printstockquantity']);



    Route::get('/Best_selling_products', [ReportController::class, 'Best_selling_products']);
    Route::post('/Best_selling_products', [ReportController::class, 'search_Best_selling_products']);
    Route::get('/printBest_selling_products/{branch_id}/{startat}/{endat}', [ReportController::class, 'printBest_selling_products']);


    Route::get('/VAT', [ReportController::class, 'VAT']);
    Route::post('/VAT', [ReportController::class, 'search_VAT']);
    Route::get('/print_VAT/{branch_id}/{startat}/{endat}', [ReportController::class, 'print_VAT']);




    Route::get('/Customersـexceededـgraceـperiod', [ReportController::class, 'Customersـexceededـgraceـperiod']);

    Route::get('/budgetsheet', [ReportController::class, 'budgetsheet']);
    Route::post('/budgetsheet', [ReportController::class, 'search_budgetsheet']);


    //endReport

    //supProcesses
    Route::post('reciptprinter', [InvoicesController::class, 'reciptprinter']);


    Route::get('/show_groups', [SupprocessesController::class, 'show_groups']);
    Route::get('/addnewProduct', [SupprocessesController::class, 'index']);
    Route::post('/addnewProduct', [SupprocessesController::class, 'create_addnewProduct']);
    Route::post('/create_products_group', [SupprocessesController::class, 'create_products_group']);
    Route::post('/addnewProductajax', [SupprocessesController::class, 'addnewProductajax']);
    Route::post('/createnewcustomerajax', [SupprocessesController::class, 'createnewcustomerajax']);



    Route::get('/addnewcustomer', [SupprocessesController::class, 'addnewcustomer']);
    Route::post('/addnewcustomer', [SupprocessesController::class, 'create_addnewcustomer']);

    Route::get('/updatecustomer', [SupprocessesController::class, 'Goupdatecustomer']);
    Route::post('/updatecustomer', [SupprocessesController::class, 'updatecustomer']);
    Route::get('getcustomer/{id}', [SupprocessesController::class, 'getcustomerdata']);


    Route::get('/addnewsupplier', [SupprocessesController::class, 'addnewsupplier']);
    Route::post('/addnewsupplier', [SupprocessesController::class, 'create_addnewsupplier']);
    Route::post('/create_addnewsupplierajax', [SupprocessesController::class, 'create_addnewsupplierajax']);


    Route::get('/updatesupplier', [SupprocessesController::class, 'Goupdatesupplier']);
    Route::post('/updatesupplier', [SupprocessesController::class, 'updatesupplier']);
    Route::get('getsupplier/{id}', [SupprocessesController::class, 'getsupplierdata']);


    Route::get('/expenses_reason', [SupprocessesController::class, 'expenses_reason']);
    Route::post('/expenses_reason', [SupprocessesController::class, 'create_expenses_reason']);


    Route::get('/stockAdjastment', [SupprocessesController::class, 'stockAdjastment']);
    Route::post('/stockAdjastment', [SupprocessesController::class, 'stock_update']);


    Route::get('/product_movement', [SupprocessesController::class, 'product_movement']);
    Route::get('/product_damage', [SupprocessesController::class, 'product_damage']);
    Route::post('/product_damage_add', [SupprocessesController::class, 'product_damage_add']);
    Route::post('/product_movement', [SupprocessesController::class, 'update_product_movement']);


    //end Supprocesses


    //usersAndBranch

    Route::get('/addbranch', [BranchsController::class, 'index']);
    Route::post('/addbranch', [BranchsController::class, 'create']);











    //endUsersAndBranch


    //show branches

    Route::get('/showbranches', [BranchsController::class, 'show']);
    Route::post('/updatebranch', [BranchsController::class, 'updatebranch']);



    //end branches

    //AVT
    Route::get('/avt', [AvtController::class, 'index']);
    Route::post('/update_vat', [AvtController::class, 'update']);
    Route::post('/New_avt', [AvtController::class, 'store']);
    Route::post('/destory_avt', [AvtController::class, 'destroy']);





    //END avt

    //Hr

    Route::get('/createNewEmployee', [EmployeeController::class, 'index']);
    Route::post('/createNewEmployee', [EmployeeController::class, 'create']);
    Route::get('/allEmployees', [EmployeeController::class, 'show']);


    Route::get('/addnewDepartment', [EmployeeController::class, 'addnewDepartment']);
    Route::post('/addnewDepartment', [EmployeeController::class, 'store']);

    Route::get('/updateEmployee/{id}', [EmployeeController::class, 'updateEmployee']);
    Route::post('/updateEmployee', [EmployeeController::class, 'update']);

    Route::get('/Increaseـor_deduction', [EmployeeController::class, 'Increaseـor_deduction']);

    Route::post('/Increaseـor_deduction', [EmployeeController::class, 'Increaseـor_deduction_add']);

    Route::get('/salarydecoument', [EmployeeController::class, 'salarydecoument']);

    Route::post('/print_decument_salary', [EmployeeController::class, 'print_decument_salary']);




    //end Hr







    // ============ بوليصة الشحن ============
    Route::get('/waybills', [WaybillController::class, 'index']);
    Route::get('/waybills/create', [WaybillController::class, 'create']);
    Route::post('/waybills/store', [WaybillController::class, 'store']);
    Route::get('/waybills/edit/{id}', [WaybillController::class, 'edit']);
    Route::get('/waybills/print/{id}', [WaybillController::class, 'print']);
    Route::post('/waybills/delete/{id}', [WaybillController::class, 'destroy']);
    Route::post('/waybills/drivers', [WaybillController::class, 'storeDriver']);
    Route::post('/waybills/trucks', [WaybillController::class, 'storeTruck']);
    Route::post('/waybills/customers', [WaybillController::class, 'storeCustomer']);
    // ============ نهاية بوليصة الشحن ============
    // ============ الإشعارات ============
    Route::get('/notifications', [\App\Http\Controllers\AppNotificationController::class, 'index']);
    Route::get('/notifications/feed', [\App\Http\Controllers\AppNotificationController::class, 'feed']);
    Route::get('/notifications/{id}/open', [\App\Http\Controllers\AppNotificationController::class, 'open']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\AppNotificationController::class, 'readAll']);
    Route::post('/push/subscribe', [\App\Http\Controllers\AppNotificationController::class, 'pushSubscribe']);
    Route::post('/push/unsubscribe', [\App\Http\Controllers\AppNotificationController::class, 'pushUnsubscribe']);
    Route::post('/push/test', [\App\Http\Controllers\AppNotificationController::class, 'pushTest']);

    // ============ حركة الشاحنات (الأحمال) ============
    Route::get('/trucks/board', [\App\Http\Controllers\TruckTripController::class, 'board']);
    Route::get('/trucks/report', [\App\Http\Controllers\TruckTripController::class, 'report']);
    Route::post('/trucks/add', [\App\Http\Controllers\TruckTripController::class, 'storeTruck']);
    Route::get('/trucks/drivers', [\App\Http\Controllers\TruckTripController::class, 'drivers']);
    Route::post('/trucks/drivers', [\App\Http\Controllers\TruckTripController::class, 'storeDriver']);
    Route::post('/trucks/customers', [\App\Http\Controllers\TruckTripController::class, 'storeCustomer']);
    Route::post('/trucks/trips', [\App\Http\Controllers\TruckTripController::class, 'load']);
    Route::post('/trucks/trips/{id}/unload', [\App\Http\Controllers\TruckTripController::class, 'unload']);
    Route::post('/trucks/trips/{id}/cancel', [\App\Http\Controllers\TruckTripController::class, 'cancel']);
    Route::get('/trucks/trips/{id}/edit', [\App\Http\Controllers\TruckTripController::class, 'edit']);
    Route::post('/trucks/trips/{id}/update', [\App\Http\Controllers\TruckTripController::class, 'update']);
    Route::post('/trucks/{id}/region', [\App\Http\Controllers\TruckTripController::class, 'setRegion']);
    Route::get('/trucks/{id}/edit', [\App\Http\Controllers\TruckTripController::class, 'truckEdit']);
    Route::post('/trucks/{id}/update', [\App\Http\Controllers\TruckTripController::class, 'truckUpdate']);


    // Home Screen
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/{page}', function ($page) {

        if (view()->exists($page)) {
            return view($page);
        } else {
            return view('404');
        }
    });
});

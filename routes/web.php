<?php

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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');
//Route::post('/user/login',array('uses'=>'LoginController@postLogin'));
/*Route::get('login','LoginController@index');
Route::post('user/login','LoginController@postLogin');
Route::get('successlogin','LoginController@successlogin');
Route::get('logout','LoginController@logout');*/
Route::get('pagenotfound',['as'=>'notfound', 'uses'=>'HomeController@pagenotfound']);
Auth::routes();


//Route::get('/home', 'HomeController@index')->name('home')->middleware('authenticated');
Route::group(['middleware'=>'authenticated'], function (){
    Route::get('/home', 'HomeController@index')->name('home');
    //Route::post('/home', 'HomeController@search')->name('search');
    Route::get('/home/item', 'HomeController@search')->name('search');
    Route::get('/home/{id}/item', 'HomeController@suggest');
    Route::get('/home/salesreport/{from}/{to}', 'HomeController@show_mgt');
    /*Admin*/
    Route::get('/employee', ['uses' => 'EmployeeController@index'])->name('employee');
    Route::post('/employee/register', 'EmployeeController@registerEmp')->name('register');
    Route::resource('branch','BranchsController');
    Route::get('/employee/{id}/edit','EmployeeController@edit');
    Route::post('/employee/{id}','EmployeeController@update');
    Route::get('/logs','LogsController@index');
    Route::get('/logs/data','LogsController@show');
    //Profile
    Route::get('/profile','EmployeeController@profile');
    Route::post('/update_password','EmployeeController@update_password');
    /*Purchasing*/
    Route::resource('inventory','InventoryController');
    Route::get('inventory/show/inactive','InventoryController@inactive_list');
    Route::resource('branch_inventory','BranchInventoryController');
    Route::resource('supplier','SupplierController');
    Route::post('branch_inventory/replicate','BranchInventoryController@replicate');
    Route::get('/po/create','PO_Controller@index');
    Route::get('/po/{id}','PO_Controller@show_item');
    Route::get('/po/sup/{id}','PO_Controller@show_sup');
    Route::post('/po','PO_Controller@store');
    Route::get('/printpo','PO_Controller@printPOindex');
    Route::get('/searchPO','PO_Controller@searchPO');
    Route::post('/po_pdf','PO_PDF@printPO');
    Route::get('/receiving/create','ReceivingController@index');
    Route::get('/receiving/{id}','ReceivingController@show_item');
    Route::get('/receiving/{id}/{code}','ReceivingController@show_qty');
    Route::get('/receiving/update/{id}/{cost}','ReceivingController@update_cost');
    Route::post('/receiving','ReceivingController@store');
    Route::get('/transfer/create','TransferController@index');
    Route::get('/transfer/{id}','TransferController@show_branch');
    Route::get('/transfer/create/{id}','TransferController@show_item');
    Route::get('/transfer/{id}/{code}','TransferController@item_data');
    Route::post('/transfer','TransferController@store');
    Route::get('/sales_import/create','ImportSalesController@index');
    Route::post('/sales_import','ImportSalesController@store');
    Route::get('/saleslogsreport','SalesReport\SalesLogsController@index');
    Route::post('/saleslogsreport','SalesReport\SalesLogsController@show');
    Route::post('/saleslogsreport/print','SalesReport\SalesLogsController@print_report');

    Route::get('/receiving_list','ReceivingListController@index');
    Route::post('/receiving_list','ReceivingListController@show');
    Route::get('/receiving_list/{id}','ReceivingListController@print_pdf');

    Route::get('/payment','PaymentController@index');
    Route::get('/payment/{id}','PaymentController@show');
    Route::post('/payment/','PaymentController@save_payment');

    Route::get('/import/create','ImportInventoryController@index');
    Route::post('/import','ImportInventoryController@import');
    Route::get('/import/create/branch','ImportInventoryController@branch_index');
    Route::post('/import/branch','ImportInventoryController@branch_import');
    Route::get('/import/update/branch','ImportInventoryController@branch_update_index');
    Route::post('/import/branch_update','ImportInventoryController@branch_update_import');

    Route::get('/import_invupdate/create','ImportInvUpdtController@index');
    Route::post('/import_invupdate','ImportInvUpdtController@import');

    Route::get('/import_invupdate/create','ImportInvUpdtController@index');
    Route::post('/import_invupdate','ImportInvUpdtController@import');

    Route::get('/transfer_history','TransHistory\TransHistoryController@index');
    Route::post('/transfer_history','TransHistory\TransHistoryController@show');
    Route::post('/transfer_history/print','TransHistory\TransHistoryController@print_report');
    //picklist
    Route::post('/replenish','PR_Controller@show');
    Route::get('/replenish/{id}','PR_Controller@show_item');
    Route::get('/replenish/{id}/{code}','PR_Controller@item_data');
    Route::post('/replenish/store','PR_Controller@store');

    Route::get('/pr_view','PR_NotifyController@pr_view');
    Route::get('/pr_view/{id}','PR_NotifyController@show_pr');
    Route::post('/pr_view','PR_NotifyController@print_pr');

    Route::get('/miscellaneous','MiscController@index');
    Route::get('/miscellaneous/{id}','MiscController@show_branch');
    Route::get('/miscellaneous/create/{id}','MiscController@show_item');
    Route::get('/miscellaneous/{id}/{code}','MiscController@item_data');
    Route::post('/miscellaneous','MiscController@store');

    Route::get('/picklist','PL_Report\PL_Controller@index');
    Route::post('/picklist','PL_Report\PL_Controller@show');
    Route::post('/picklist/print','PL_Report\PL_Controller@print_report');

    Route::get('/pl_print','PR_PDFController@index');
    Route::get('/pl_print/{id}','PR_PDFController@show');
    Route::post('/pl_print/print','PR_PDFController@print_request');

    Route::get('/bi_records','Bi_RepController@index');
    Route::get('/bi_records/{date}/{branch}','Bi_RepController@show');
    Route::post('/bi_records/print','Bi_RepController@print_report');


    //Management
    Route::get('/notification/po','NotificationController@po_index');
    Route::get('/notification/transfer','NotificationController@tf_index');
    Route::get('/notification/{id}','NotificationController@show_po');
    Route::get('/notification/transfer/{id}','NotificationController@show_tf');
    Route::post('/notification','NotificationController@save_po');
    Route::post('notification/transfer/','NotificationController@save_tf');
    Route::get('/salesreport','SalesReport\SalesReportController@index');
    Route::post('/salesreport','SalesReport\SalesReportController@show');
    Route::post('/salesreport/print','SalesReport\SalesReportController@print_report');
    Route::get('/receiving_report','RecReport\RecReportController@index');
    Route::post('/receiving_report','RecReport\RecReportController@show');
    Route::post('/receiving_report/print','RecReport\RecReportController@print_report');
    Route::get('/transfer_report','TransReport\TransReportController@index');
    Route::post('/transfer_report','TransReport\TransReportController@show');
    Route::post('/transfer_report/print','TransReport\TransReportController@print_report');
    Route::get('/salesreturn_report','SrReport\SrReportController@index');
    Route::post('/salesreturn_report','SrReport\SrReportController@show');
    Route::post('/salesreturn_report/print','SrReport\SrReportController@print_report');
    Route::get('/inventory_analysis','InvAnalysis\InvAnaylsisController@index');
    // For viewing products in Auditor user
    Route::get('/inventory_analysis/{id}','InvAnalysis\InvAnaylsisController@show_items');
    // end of viewing products in Auditor user
    Route::post('/inventory_analysis','InvAnalysis\InvAnaylsisController@show');
    Route::post('/inventory_analysis/print','InvAnalysis\InvAnaylsisController@print_report');
    Route::get('/purchase_report','PurchaseReport\PurchaseReportController@index');
    Route::post('/purchase_report','PurchaseReport\PurchaseReportController@show');
    Route::post('/purchase_report/print','PurchaseReport\PurchaseReportController@print_report');
    Route::get('/profit','Profit\ProfitController@index');
    Route::post('/profit','Profit\ProfitController@show');
    Route::post('/profit/print','Profit\ProfitController@print_report');
    Route::get('/payable','Payable\PayableController@index');
    Route::post('/payable','Payable\PayableController@show');
    Route::post('/payable/print','Payable\PayableController@print_report');

    Route::get('/pr_approval','PR_NotifyController@pr_index');
    Route::get('/pr_approval/{id}','PR_NotifyController@show_item');
    Route::post('/pr_approval','PR_NotifyController@store');

    Route::get('/category_analysis','InvAnalysis\CatAnalysisController@index');
    Route::get('/category_analysis/{id}','InvAnalysis\CatAnalysisController@brand_show');
    Route::post('/category_analysis','InvAnalysis\CatAnalysisController@show');
    Route::post('/category_analysis/print','InvAnalysis\CatAnalysisController@print_report');
    
    Route::get('/erp_report','ERP_Report\ERP_Controller@index');
    Route::post('/erp_report','ERP_Report\ERP_Controller@show');
    Route::post('/erp_report/print','ERP_Report\ERP_Controller@print_report');
    
    Route::get('/act_erp','ERP_Report\ActErpController@index');
    Route::post('/act_erp','ERP_Report\ActErpController@show');
    Route::post('/act_erp/print','ERP_Report\ActErpController@print_report');
    
    Route::get('/miscellaneous_report','Misc_Report\MiscReportController@index');
    Route::post('/miscellaneous_report','Misc_Report\MiscReportController@show');
    Route::post('/miscellaneous_report/print','Misc_Report\MiscReportController@print_report');
    
    Route::get('/misc_out','Misc_Report\MiscOutController@index');
    Route::post('/misc_out','Misc_Report\MiscOutController@show');
    Route::post('/misc_out/print','Misc_Report\MiscOutController@print_report');
    
    Route::get('/perf_report','PerfMeasure\PerfMeasureController@index');
    Route::post('/perf_report','PerfMeasure\PerfMeasureController@show');
    Route::post('/perf_report/print','PerfMeasure\PerfMeasureController@print_report');
    
    Route::get('/gen_pr','GenPRController@index');
    Route::post('/gen_pr','GenPRController@show');
    Route::post('/gen_pr/print','GenPRController@print_report');
    
    Route::get('/trans_in','TransReport\TransInController@index');
    Route::post('/trans_in','TransReport\TransInController@show');
    Route::post('/trans_in/print','TransReport\TransInController@print_report');

    Route::get('/trans_out','TransReport\TransOutController@index');
    Route::post('/trans_out','TransReport\TransOutController@show');
    Route::post('/trans_out/print','TransReport\TransOutController@print_report');

    Route::get('/stock_return','TransReport\StockReturnController@index');
    Route::post('/stock_return','TransReport\StockReturnController@show');
    Route::post('/stock_return/print','TransReport\StockReturnController@print_report');

    //Salesman
    Route::resource('so','SO_Controller');
    Route::get('so/{id}/{si}','SO_Controller@show_item');
    Route::get('/salesman/inventory','SmInventory\SmInventoryController@index');
    Route::get('/salesman/inventory/{id}','SmInventory\SmInventoryController@show');
    Route::get('/salesman/inventory/edit/{id}','SmInventory\SmInventoryController@edit');
    //Route::get('/salesman/inventory/edit/{id}','SmInventory\SmInventoryController@edit');
    Route::get('/salesman/inventory/print/{id}','SmInventory\SmInventoryController@print_inventory');
    Route::post('/salesman/inventory/update','SmInventory\SmInventoryController@update');
    Route::get('/mechanic','MechAccController@index');
    Route::post('/mechanic','MechAccController@store');
    Route::post('/mechanic/{id}','MechAccController@update');
    Route::get('/mechanic/{id}/edit','MechAccController@edit');
    Route::resource('sr','SR_Controller');
    Route::get('/transferred/list','TransferredListController@index');
    Route::get('/transferred/list/{id}','TransferredListController@show');
    Route::get('/transferred/list/pdf/{id}','TransferredListController@print_transferred');


    //Partsman
    Route::get('/transferred/print','TransferPrintController@index');
    Route::get('/transferred/print/{id}','TransferPrintController@show');
    Route::get('/transferred/print/pdf/{id}','TransferPrintController@print_transferred');
    Route::get('/request_item/request','NotifyRequestController@index');
    Route::get('/request_item/request/{id}','NotifyRequestController@show');
    Route::post('/request_item/print','PR_PDFController@print_request');
    Route::post('/request_item/store','NotifyRequestController@store');
    Route::get('/view_po','NotifyPOController@po_index');
    Route::get('/view_po/{id}','NotifyPOController@show_po');
    Route::post('/view_po/print','NotifyPOController@printPO');


    //NEW REPORTS
    Route::get('/nr_salesreport','New_Reports\SalesReportController@index');
    Route::post('/nr_salesreport','New_Reports\SalesReportController@show');
    Route::post('/nr_salesreport/print','New_Reports\SalesReportController@print_report');

    Route::get('/nr_purchase_report','New_Reports\PurchaseReportController@index');
    Route::post('/nr_purchase_report','New_Reports\PurchaseReportController@show');
    Route::post('/nr_purchase_report/print','New_Reports\PurchaseReportController@print_report');//nr_salesreturn_report

    Route::get('/nr_salesreturn_report','New_Reports\SrReportController@index');
    Route::post('/nr_salesreturn_report','New_Reports\SrReportController@show');
    Route::post('/nr_salesreturn_report/print','New_Reports\SrReportController@print_report');

    Route::get('/nr_transfer_report','New_Reports\TransReportController@index');
    Route::post('/nr_transfer_report','New_Reports\TransReportController@show');
    Route::post('/nr_transfer_report/print','New_Reports\TransReportController@print_report');

    Route::get('/nr_trans_in','New_Reports\TransInController@index');
    Route::post('/nr_trans_in','New_Reports\TransInController@show');
    Route::post('/nr_trans_in/print','New_Reports\TransInController@print_report');

    Route::get('/nr_trans_out','New_Reports\TransOutController@index');
    Route::post('/nr_trans_out','New_Reports\TransOutController@show');
    Route::post('/nr_trans_out/print','New_Reports\TransOutController@print_report');

    Route::get('/nr_stock_return','New_Reports\StockReturnController@index');
    Route::post('/nr_stock_return','New_Reports\StockReturnController@show');
    Route::post('/nr_stock_return/print','New_Reports\StockReturnController@print_report');

    Route::get('/nr_transfer_history','New_Reports\TransHistoryController@index');
    Route::post('/nr_transfer_history','New_Reports\TransHistoryController@show');
    Route::post('/nr_transfer_history/print','New_Reports\TransHistoryController@print_report');

    Route::get('/nr_profit','New_Reports\ProfitController@index');
    Route::post('/nr_profit','New_Reports\ProfitController@show');
    Route::post('/nr_profit/print','New_Reports\ProfitController@print_report');

    Route::get('/nr_erp_report','New_Reports\ERP_Controller@index');
    Route::post('/nr_erp_report','New_Reports\ERP_Controller@show');
    Route::post('/nr_erp_report/print','New_Reports\ERP_Controller@print_report');

    Route::get('/cw_erp','New_Reports\CWERP_Controller@index');
    Route::post('/cw_erp','New_Reports\CWERP_Controller@show');
    Route::post('/cw_erp/print','New_Reports\CWERP_Controller@print_report');

    Route::get('/pl_perf','PL_Perf\PL_PerfController@index');
    Route::post('/pl_perf','PL_Perf\PL_PerfController@show');
    Route::post('/pl_perf/print','PL_Perf\PL_PerfController@print_report');


});



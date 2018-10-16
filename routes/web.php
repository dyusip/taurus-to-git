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
    Route::resource('branch_inventory','BranchInventoryController');
    Route::post('branch_inventory/replicate','BranchInventoryController@replicate');
    Route::get('/po/create','PO_Controller@index');
    Route::get('/po/{id}','PO_Controller@show_item');
    Route::post('/po','PO_Controller@store');
    Route::get('/printpo','PO_Controller@printPOindex');
    Route::get('/searchPO','PO_Controller@searchPO');
    Route::post('/po_pdf','PO_PDF@printPO');
    Route::get('/receiving/create','ReceivingController@index');
    Route::get('/receiving/{id}','ReceivingController@show_item');
    Route::get('/receiving/{id}/{code}','ReceivingController@show_qty');
    Route::post('/receiving','ReceivingController@store');
    Route::get('/transfer/create','TransferController@index');
    Route::get('/transfer/{id}','TransferController@show_branch');
    Route::get('/transfer/create/{id}','TransferController@show_item');
    Route::get('/transfer/{id}/{code}','TransferController@item_data');
    Route::post('/transfer','TransferController@store');

    Route::get('/import/create','ImportInventoryController@index');
    Route::post('/import','ImportInventoryController@import');
    Route::get('/import/create/branch','ImportInventoryController@branch_index');
    Route::post('/import/branch','ImportInventoryController@branch_import');

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
    Route::post('/inventory_analysis','InvAnalysis\InvAnaylsisController@show');
    Route::post('/inventory_analysis/print','InvAnalysis\InvAnaylsisController@print_report');
    Route::get('/purchase_report','PurchaseReport\PurchaseReportController@index');
    Route::post('/purchase_report','PurchaseReport\PurchaseReportController@show');
    Route::post('/purchase_report/print','PurchaseReport\PurchaseReportController@print_report');
    Route::get('/profit','Profit\ProfitController@index');
    Route::post('/profit','Profit\ProfitController@show');
    Route::post('/profit/print','Profit\ProfitController@print_report');

    //Salesman
    Route::resource('so','SO_Controller');
    Route::get('/salesman/inventory','SmInventory\SmInventoryController@index');
    Route::get('/salesman/inventory/{id}','SmInventory\SmInventoryController@show');
    Route::get('/salesman/inventory/edit/{id}','SmInventory\SmInventoryController@edit');
    Route::get('/salesman/inventory/edit/{id}','SmInventory\SmInventoryController@edit');
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


});



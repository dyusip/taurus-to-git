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
    /*Admin*/
    Route::get('/employee', ['uses' => 'EmployeeController@index'])->name('employee');
    Route::post('/employee/register', 'EmployeeController@registerEmp')->name('register');
    Route::resource('branch','BranchsController');
    Route::get('/employee/{id}/edit','EmployeeController@edit');
    Route::post('/employee/{id}','EmployeeController@update');
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

    //Salesman
    Route::resource('so','SO_Controller');
});



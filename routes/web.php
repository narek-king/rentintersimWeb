<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use App\User;
use App\Models\Order;
use App\Models\Activation;
use Carbon\Carbon;
use App\Jobs\CacheUsersTree;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\Phone;


Route::get('/', function () {
//    if (env('APP_ENV')=='local')
//    return view('errors.503');
//    else
        return redirect('/home');
});

Auth::routes();


Route::get('/dashboard', 'HomeController@dashboard');

Route::group(['namespace' => 'Api', 'middleware'=> 'auth'], function () {

//    Super admin
    /*****Activate - Deactivate******/
    Route::get('activate/{id}', 'OrderController@activate');
    Route::get('deactivate/{id}', 'OrderController@deactivate');

    /*****Auth Routes******/
    Route::get('imitate', 'UserController@showImitation');
    Route::post('imitate', 'UserController@imitate');

//    Super admin end
    Route::get('/home', 'OrderController@index');

    /* Resource Routes */
    Route::resource('user', 'UserController');
    Route::resource('order', 'OrderController');
    Route::resource('number', 'PhoneController');
    Route::resource('sim', 'SIMController');
    Route::resource('type', 'PackageController');
    Route::resource('price-list', 'PriceListController');
    Route::resource('report', 'ReportController');

    /*****Get new Number******/
    Route::get('get-number/{orderId}', 'OrderController@getNumberExternal');

    /*Filter Routes*/
    Route::get('filter-orderlist/{filter}', 'OrderController@filter');
    Route::get('filter-numberlist/{filter}', 'PhoneController@filter');
    Route::get('filter-simlist/{filter}', 'SIMController@filter');
    Route::get('filter-packagelist/{filter}', 'PackageController@filter');

    /***Ajax requests***/
    Route::get('user-tree', 'UserController@getUserTree');
    Route::get('user-flat-tree', 'UserController@getFlatTree');
    Route::get('user-id-tree', 'UserController@getIdTree');
    Route::get('user-by-level/{level}', 'UserController@getByLevel');
    Route::get('type-provider/{providerId}', 'PackageController@typeofProvider');

    /******Export Import routes******/
    Route::get('exportsims', 'SIMController@export');
    Route::post('import-sim', 'SIMController@import');
    Route::get('exporttypes', 'PackageController@export');
    Route::post('import-type', 'PackageController@import');
    Route::get('exportnumber', 'PhoneController@export');
    Route::post('import-number', 'PhoneController@import');
    Route::get('exportorders', 'OrderController@export');

    /******Search  routes******/
    Route::get('search/sim', 'SIMController@search');
    Route::get('search/type', 'PackageController@search');
    Route::get('search/number', 'PhoneController@search');
    Route::get('search/order', 'OrderController@search');
    Route::get('search/report', 'ReportController@search');

    Route::get('send-mail/{orderID}', 'OrderController@sendMail');

//    Route::get('report', 'ReportController@index');
    Route::get('phone/specials/{packageID}', 'PhoneController@specials');

    /**** Recover Routes ****/
    Route::post('sim/recover/{id}', 'SIMController@recover');
    Route::post('number/recover/{id}', 'PhoneController@recover');

    Route::get('/api/order', 'OrderController@orderTable');
    Route::get('/api/number', 'PhoneController@numberTable');
    Route::get('/api/sim', 'SIMController@simTable');
    Route::get('/api/type', 'PackageController@packageTable');
    Route::get('/api/report', 'ReportController@generateReport');

    /****** CLI Check ******/
    Route::get('/cli', 'PhoneController@cli');
    Route::post('/cli-check', 'PhoneController@cliCheck');

    /***** Price Lists *****/
    Route::get('/pl-users/{id}', 'PriceListController@showUsers');
    Route::post('/price-list/copy', 'PriceListController@copyPriceList');
    Route::post('/price-list/attache_user', 'PriceListController@attacheUser');



});




Route::get('/test', function (){

    $phones = Phone::where([['is_active', 1], ['package_id', 21], ['state', 'not in use'], ['is_special', '0']])->get();
    dd(!$phones->isEmpty());

});

Route::get('/test2', function (){
    $orders = Order::where('status', 'pending')->orWhere('status', 'waiting')->orderBy('from', 'asc')->get();
    foreach ($orders as $order){
        $date = Carbon::createFromFormat('d/m/Y H:i', $order->landing)->subHours(1);
        if ($date->hour >= 2 && $date->hour <= 6){
            $date->setTime(1,0);
        }
//        $order->from = $date->timestamp;
//        $order->save();
        echo "id: ".$order->id." | activation time: ". Carbon::createFromTimestamp($order->from)->format('d/m/Y H:i'). " |  user chois: ". $order->landing ." | Status: " . $order->status. "<br>";
    }

});

Route::get('test-delays', function (){
    $acts = Activation::take(30)->skip(135)->get();
        echo '<table border="1"><tr><td>id</td><td>CLI</td><td>SIM</td><td>call</td><td>First Answer</td><td>order id</td><td>check delay</td><td>data</td></tr>';
    foreach ($acts as $item){
        echo'<tr>';
        echo "<td> $item->id </td><td> $item->phone_number</td> <td>$item->sim_number</td><td> $item->call</td><td> $item->answer</td><td>$item->order_id</td><td>$item->check_status</td><td>$item->created_at</td>";
        echo '</tr>';
    }
    echo '</table>';
});

Route::get('/test-user-queue', function (){
    dispatch(new CacheUsersTree(new Rentintersimrepo\users\UserManager()));
});

Route::get('/test-deactivation', function (){
    $orders = Order::where('status', 'pending')->get();
    foreach ($orders as $order){
//        $date = Carbon::createFromFormat('d/m/Y H:i', $order->departure)->addHours(3);
////        if ($date->hour >= 2 && $date->hour <= 6){
////            $date->setTime(1,0);
////        }
//        $order->to = $date->timestamp;
//        $order->save();
        echo "id: ".$order->id." | deactivation time: ". Carbon::createFromTimestamp($order->to)->format('d/m/Y H:i'). " |  user chois: ". $order->departure ." | Status: " . $order->status. "<br>";
    }

});

Route::get('string-to-timestamp', function (){
    $date = Carbon::createFromFormat('d/m/Y H:i', request()->input('string'));
    echo request()->input('string'). "\n";
    return response($date->timestamp);
});



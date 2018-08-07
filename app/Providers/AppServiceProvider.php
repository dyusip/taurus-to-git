<?php

namespace App\Providers;
use App\PoHeader;
use App\TransferHeaders;
use View;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        /*$notify = PoHeader::where(['status' => 'PD']);
        $count = $notify->count();
        $data = $notify->paginate('3');
        View::share('notify',$count);
        View::share('notify_data',$data);*/
        $po = PoHeader::where(['status' => 'PD'])->count();
        $transfer = TransferHeaders::where(['tf_status' => 'PD'])->count();
        $count = $po + $transfer;
        View::share('notify',$count);
        View::share('po_count',$po);
        View::share('tf_count',$transfer);
       //View::share('notify_data',$data)


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

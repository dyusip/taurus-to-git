<?php
/**
 * Created by PhpStorm.
 * User: INFOZ-PC01
 * Date: 8/18/2018
 * Time: 10:33 AM
 */
namespace App\Http\ViewComposers;

use App\PoHeader;
use App\TransferHeaders;
use Illuminate\View\View;
class NotificationComposer
{
    public function compose(View $view){
        $po = PoHeader::where(['status' => 'PD'])->count();
        $transfer = TransferHeaders::where(['tf_status' => 'PD'])->count();
        $count = $po + $transfer;
        $view->with('notify',$count);
        $view->with('po_count',$po);
        $view->with('tf_count',$transfer);
    }
}
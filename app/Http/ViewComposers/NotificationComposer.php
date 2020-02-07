<?php
/**
 * Created by PhpStorm.
 * User: INFOZ-PC01
 * Date: 8/18/2018
 * Time: 10:33 AM
 */
namespace App\Http\ViewComposers;

use App\PoHeader;
use App\PrHeader;
use App\ReqHeader;
use App\TransferHeaders;
use Illuminate\View\View;
class NotificationComposer
{
    public function compose(View $view){
        $po = PoHeader::where(['status' => 'PD'])->count();
        $transfer = TransferHeaders::where(['tf_status' => 'PD'])->count();
        $pr = PrHeader::where(['pr_status' => 'PD'])->count();
        $count = $po + $transfer + $pr;
        $view->with('notify',$count);
        $view->with('po_count',$po);
        $view->with('tf_count',$transfer);//Management
        $view->with('pr',$pr);

        $request = ReqHeader::where(['req_status' => 'PD'])->count();
        $app_po = PoHeader::where(['status' => 'AP'])->count();
        $transfer_user = TransferHeaders::where(['tf_status' => 'PD'])->where('from_branch','!=','TR-BR00001')->count();
        $pm_count = $request + $app_po + $transfer_user;
        $view->with('pm_count',$pm_count);//Partsman
        $view->with('transfer_user',$transfer_user);//Partsman and Purchasing
        $view->with('req_count',$request);
        $view->with('app_po',$app_po);

        $pr_view = PrHeader::where(['pr_status' => 'AP'])->count();
        $pur_count = $transfer_user + $pr_view;
        $view->with('pur_count',$pur_count);
        $view->with('pr_view',$pr_view);
    }
}
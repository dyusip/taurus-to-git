<?php

namespace App\Console\Commands;

use App\Branch_Inventory;
use App\InvPosition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InventoryCapture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:capture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture Inventory Position every midnight';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $items = Branch_Inventory::select(DB::raw('branch_code, SUM(cost * quantity) as total_cost, SUM(price * quantity) as total_srp'))
            ->groupBy('branch_code')
            ->get();
        //$item
        foreach($items as $item){
            InvPosition::create([
                'ip_branch_code' => $item->branch_code,
                'ip_cost' => $item->total_cost,
                'ip_srp' => $item->total_srp,
                'ip_date' => date('Y-m-d'),
                ]);
        }
    }
}
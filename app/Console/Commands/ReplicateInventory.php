<?php

namespace App\Console\Commands;

use App\BiReplicate;
use App\Branch_Inventory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReplicateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replicate:inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replicate Inventory every midnight';

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
        /*$bis = Branch_Inventory::all();
        $data = array();
        foreach ($bis as $bi)
        {
            $data[] =[
                'bir_branch_code' => $bi->branch_code,
                'bir_prod_code' => $bi->prod_code,
                'bir_cost' => $bi->cost,
                'bir_price' => $bi->price,
                'bir_quantity' => $bi->quantity,
                'bir_date' => date('Y-m-d'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
        }
        foreach (array_chunk($data, 1000) as $chunk)
        {
            BiReplicate::insert($chunk);
        }*/
        DB::table('branch__inventories')->orderBy('branch_code')->chunk(1000, function ($users) {
            $data = array();
            foreach ($users as $user) {
                $data[] =[
                    'bir_branch_code' => $user->branch_code,
                    'bir_prod_code' => $user->prod_code,
                    'bir_cost' => $user->cost,
                    'bir_price' => $user->price,
                    'bir_quantity' => $user->quantity,
                    'bir_date' => date('Y-m-d'),
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ];
            }
            foreach (array_chunk($data, 1000) as $chunk)
            {
                BiReplicate::insert($chunk);
            }
        });
    }
}

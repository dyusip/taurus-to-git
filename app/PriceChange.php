<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceChange extends Model
{
    //
    protected $fillable = [
        'pc_branch_code','pc_prod_code', 'pc_cost', 'pc_srp', 'pc_status', 'pc_date'
    ];

}

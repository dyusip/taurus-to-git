<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BiReplicate extends Model
{
    //
    protected $fillable = [
        'bir_prod_code', 'bir_branch_code', 'bir_cost', 'bir_price', 'bir_quantity','bir_date'
    ];
    public function inventory()
    {
        return $this->belongsTo(Inventory::class,'bir_prod_code','code')->where(['status'=>'AC']);
    }

}

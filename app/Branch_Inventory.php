<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelMPK;

class Branch_Inventory extends ModelMPK
{
    //
    protected $primaryKey = ['prod_code','branch_code'];
    public $incrementing = false;
    protected $fillable = [
        'prod_code', 'branch_code', 'cost', 'price', 'quantity'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class,'prod_code','code')->where(['status'=>'AC']);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_code','code');
    }
}

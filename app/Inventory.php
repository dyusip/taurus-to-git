<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'code', 'name', 'desc', 'uom','pqty','status'
    ];
    //
   /* public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_code','code');
    }*/
    public function branch_inventory()
    {
        return $this->hasMany(Branch_Inventory::class,'prod_code','code');
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
    public function setDescAttribute($value)
    {
        $this->attributes['desc'] = strtoupper($value);
    }
    public function setUomcAttribute($value)
    {
        $this->attributes['uom'] = strtoupper($value);
    }
}

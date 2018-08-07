<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'code', 'name', 'address', 'contact','status'
    ];

    protected $dates = ['delete_at'];
   /* public function setCodeAttribute($value)
    {
       if(Branch::count()<1){
            $num = "TR-BR00001";
        }else{
            $num = Branch::max('code');
            ++$num;
        }
        $this->attributes['code'] = (isset($value))?$num:'';
    }*/
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
   /* public function inventory()
    {
        return $this->hasMany(Inventory::class,'branch_code','code');
    }*/
    public function Branch_inventory()
    {
        return $this->hasMany(Branch_Inventory::class,'branch_code','code');
    }
    public function user()
    {
        return $this->hasMany(User::class,'branch','code');
    }
    public function tf_to_branch_header()
    {
        return $this->hasMany(TransferHeaders::class,'to_branch','code');
    }
    public function tf_fr_branch_header()
    {
        return $this->hasMany(TransferHeaders::class,'fron_branch','code');
    }
}

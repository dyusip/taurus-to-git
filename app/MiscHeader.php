<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MiscHeader extends Model
{
    //
    protected $fillable = [
        'msh_code', 'msh_branch_code', 'msh_prep_by', 'msh_date', 'msh_total', 'msh_status'
    ];
    public function setMshDateAttribute($value)
    {
        $this->attributes['msh_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function getMshDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');

    }
    public function misc_detail()
    {
        return $this->hasMany(MiscDetail::class,'msd_code','msh_code');
    }
}

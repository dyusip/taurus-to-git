<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvPosition extends Model
{
    //
    protected $fillable = [
        'ip_branch_code','ip_cost','ip_srp','ip_date','ip_status'
    ];
}

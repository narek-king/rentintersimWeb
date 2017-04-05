<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    //
    protected $dates = ['created_at', 'updated_at'];
    public function report()
    {
        return $this->belongsTo('App\Models\Report');
    }

}

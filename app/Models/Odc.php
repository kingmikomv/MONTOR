<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Olt;
use App\Models\Pon;
use App\Models\Odp;
class Odc extends Model
{
    protected $table = "odcs";
    protected $guarded = [];
     public function olt() { return $this->belongsTo(Olt::class); }
    public function pon() { return $this->belongsTo(Pon::class); }
    public function odps() { return $this->hasMany(Odp::class); }
}

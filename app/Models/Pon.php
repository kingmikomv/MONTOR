<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Odc;
use App\Models\Olt;
use App\Models\Converter;

class Pon extends Model
{
    protected $table = "pons";
    protected $guarded = [];
    public function olt() { return $this->belongsTo(Olt::class); }
    public function odcs() { return $this->hasMany(Odc::class); }
    public function converters() { return $this->hasMany(Converter::class); }
}

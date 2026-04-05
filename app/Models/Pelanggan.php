<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Odp;
use App\Models\Converter;
class Pelanggan extends Model
{
    protected $table = "pelanggans";
    protected $guarded = [];

    public function odp() { return $this->belongsTo(Odp::class); }
    public function converter() { return $this->belongsTo(Converter::class); }
}

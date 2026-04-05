<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pon;
use App\Models\Pelanggan;
class Converter extends Model
{
    protected $table = "converters";
    protected $guarded = [];

    public function pon() { return $this->belongsTo(Pon::class); }
    public function pelanggans() { return $this->hasMany(Pelanggan::class); }
}

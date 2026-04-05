<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Server;
use App\Models\Pon;
use App\Models\Odc;
class Olt extends Model
{
    protected $table = "olts";
    protected $guarded = [];
    public function server() { return $this->belongsTo(Server::class); }
    public function pons() { return $this->hasMany(Pon::class); }
    public function odcs() { return $this->hasMany(Odc::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Olt;
class Server extends Model
{
    protected $table = "servers";
    protected $guarded = [];
    public function olts() { return $this->hasMany(Olt::class); }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pelanggan;
use App\Models\Odc;

class Odp extends Model
{
    protected $table = "odps";
    protected $guarded = [];

    // relasi ke ODC
    public function odc()
    {
        return $this->belongsTo(Odc::class);
    }

    // ODP sumber (parent)
    public function parent()
    {
        return $this->belongsTo(Odp::class, 'parent_odp_id');
    }

    // ODP turunan
    public function children()
    {
        return $this->hasMany(Odp::class, 'parent_odp_id');
    }

    // pelanggan di ODP
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }

    public function getOdcNameAttribute()
{
    if ($this->odc) {
        return $this->odc->nama;
    }

    if ($this->parent && $this->parent->odc) {
        return $this->parent->odc->nama;
    }

    return '-';
}

public function childrenRecursive()
{
    return $this->hasMany(Odp::class, 'parent_odp_id')
                ->with('childrenRecursive');
}
}
<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pelanggan([
            'odp_id'       => $row['odp_id'] ?? null,
            'converter_id' => $row['converter_id'] ?? null,
            'nama'         => $row['nama'],
            'username_pppoe' => $row['username_pppoe'] ?? null,
            'onu_sn'       => $row['onu_sn'] ?? null,
            'port'         => $row['port'],
            'alamat'       => $row['alamat'] ?? null,
            'lat'          => $row['lat'] ?? null,
            'lng'          => $row['lng'] ?? null,
            'status'       => $row['status'] ?? 'nonaktif',
        ]);
    }
}
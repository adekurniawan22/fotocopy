<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Spki;
use App\Models\User;
use App\Models\Tool;
use App\Models\Organization;

class SpkiSeeder extends Seeder
{
    public function run()
    {
        $org = Organization::first();
        if (!$org) return;

        $users = User::where('organization_id', $org->organization_id)->limit(5)->get();
        $tools = Tool::where('organization_id', $org->organization_id)->limit(3)->get();

        if ($users->count() < 2) return;

        $userAsman = $users[0];
        $userStaff = $users[1];

        $bulanRomawi = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        $bln = $bulanRomawi[date('n')];
        $thn = date('Y');

        $listPelaksana = [['user_id' => $userStaff->user_id, 'nama' => $userStaff->name]];
        $listPeralatan = $tools->count() > 0 
            ? [['tool_id' => $tools->first()->tool_id, 'nama' => $tools->first()->nama, 'jumlah' => 1, 'satuan' => 'Set']] 
            : [];

        Spki::create([
            'organization_id'       => $org->organization_id,
            'nomor_spki'            => "NO.001/PDKB-TT/$bln/$thn",
            'dari'                  => 'Assistant Manager',
            'kepada'                => $userStaff->user_id,
            'macam_pekerjaan'       => 'Inspeksi Rutin Gardu',
            'lokasi_pekerjaan'      => 'Gardu Induk A',
            'mulai_pelaksanaan'     => Carbon::tomorrow(),
            'selesai_pelaksanaan'   => Carbon::tomorrow(),
            
            'penanggung_jawab_id'   => $userAsman->user_id,
            'penanggung_jawab_nama' => $userAsman->name,
            'pengawas_pekerjaan_id' => $userStaff->user_id,
            'pengawas_pekerjaan_nama' => $userStaff->name,
            'pengawas_k3_id'        => null,
            'pengawas_k3_nama'      => '-',
            
            'pelaksana'             => $listPelaksana,
            'peralatan'             => $listPeralatan,
            'uraian_pekerjaan'      => "Melakukan pengecekan visual dan termovisi pada trafo.",
            'catatan'               => null,
            
            'status'                => 'DRAFT',
            'approved_by'           => null,
            'approved_at'           => null,
            'created_by'            => $userAsman->user_id,
            'created_at'            => Carbon::now(),
        ]);

        Spki::create([
            'organization_id'       => $org->organization_id,
            'nomor_spki'            => "NO.002/PDKB-TT/$bln/$thn",
            'dari'                  => 'Assistant Manager',
            'kepada'                => $userStaff->user_id,
            'macam_pekerjaan'       => 'Perbaikan Terminasi',
            'lokasi_pekerjaan'      => 'Feeder Bintaro',
            'mulai_pelaksanaan'     => Carbon::now()->addDays(3),
            'selesai_pelaksanaan'   => Carbon::now()->addDays(3),
            
            'penanggung_jawab_id'   => $userAsman->user_id,
            'penanggung_jawab_nama' => $userAsman->name,
            'pengawas_pekerjaan_id' => $userStaff->user_id,
            'pengawas_pekerjaan_nama' => $userStaff->name,
            'pengawas_k3_id'        => $userStaff->user_id,
            'pengawas_k3_nama'      => $userStaff->name,
            
            'pelaksana'             => $listPelaksana,
            'peralatan'             => $listPeralatan,
            'uraian_pekerjaan'      => "Penggantian material terminasi yang korosi.",
            'catatan'               => "Pastikan grounding terpasang.",
            
            'status'                => 'DRAFT',
            'approved_by'           => null,
            'approved_at'           => null,
            'created_by'            => $userAsman->user_id,
            'created_at'            => Carbon::now(),
        ]);
    }
}
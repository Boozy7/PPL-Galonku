<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use Illuminate\Http\Request;

class DinkesDashboardController extends Controller
{
    /**
     * Tampilkan Halaman Utama Verifikasi Dokumen & Pengawasan Dinkes (SCRUM-67)
     */
    public function index()
    {
        // Ambil depot yang baru daftar atau mengajukan pembaruan sertifikat lab (PROSES_RENEWAL / BELUM_TERSERTIFIKASI)
        $pendingDepots = Depot::whereIn('certification_status', ['PROSES_RENEWAL', 'BELUM_TERSERTIFIKASI'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Ambil depot yang sudah tersertifikasi aktif (untuk tabel monitoring)
        $certifiedDepots = Depot::where('certification_status', 'TERSERTIFIKASI')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dinkes.dashboard', compact('pendingDepots', 'certifiedDepots'));
    }

    /**
     * Setujui Permohonan Sertifikasi / Terbitkan SLHS (SCRUM-83)
     */
    public function approveDepot($id)
    {
        $depot = Depot::findOrFail($id);

        $depot->update([
            'certification_status' => 'TERSERTIFIKASI',
            'slhs_number' => 'SLHS/SBY/' . date('Y') . '/' . str_pad($depot->id, 4, '0', STR_PAD_LEFT),
            'expiry_date' => now()->addYears(3)->format('Y-m-d'),
            'certificate_notes' => null,
        ]);

        return back()->with('success', "Sertifikat Laik Higiene Sanitasi (SLHS) untuk depot {$depot->name} berhasil diterbitkan!");
    }

    /**
     * Tolak Permohonan Sertifikasi
     */
    public function rejectDepot(Request $request, $id)
    {
        $depot = Depot::findOrFail($id);

        $depot->update([
            'certification_status' => 'DITOLAK',
            'certificate_notes' => $request->input('rejection_reason', 'Dokumen tidak memenuhi syarat.'),
        ]);

        return back()->with('error', "Pengajuan sertifikasi depot {$depot->name} ditolak.");
    }
}

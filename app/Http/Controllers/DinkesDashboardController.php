<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
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

        $complaints = Complaint::with('depot')->orderBy('created_at', 'desc')->get();
        return view('dinkes.dashboard', compact('pendingDepots', 'certifiedDepots', 'complaints'));
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
    /**
     * Respon & Update Status Laporan Pengaduan Air (SCRUM-85)
     */
    public function updateComplaint(Request $request, $id)
    {
        $complaint = Complaint::with('depot')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:SEDANG_INVESTIGASI,INSPEKSI_LAPANGAN,SELESAI',
            'dinkes_notes' => 'required|string|max:1000',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'revoke_badge' => 'nullable|boolean',
        ]);

        $complaint->status = $request->status;
        $complaint->dinkes_notes = $request->dinkes_notes;

        // Simpan foto bukti inspeksi lapangan Dinkes jika ada
        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('complaints_proofs', 'public');
            $complaint->proof_image_path = '/storage/' . $path;
        }

        $complaint->save();

        // SCRUM-68: Cabut badge resmi jika depot terbukti bermasalah
        $badgeRevoked = false;
        if ($request->boolean('revoke_badge') && $complaint->depot) {
            $depot = $complaint->depot;
            $depot->is_certified = false;
            $depot->certification_status = 'BELUM_TERSERTIFIKASI';
            $depot->certificate_notes = 'Badge SLHS dicabut sementara oleh Dinkes Surabaya akibat aduan warga terbukti bermasalah (Tiket: ' . $complaint->ticket_number . ').';
            $depot->save();
            $badgeRevoked = true;
        }

        $message = "Laporan {$complaint->ticket_number} berhasil diperbarui menjadi status " . str_replace('_', ' ', $complaint->status) . ".";
        if ($badgeRevoked) {
            $message .= " Badge resmi SLHS untuk Depot '{$complaint->depot->name}' telah resmi dicabut!";
        }

        return back()->with('success', $message);
    }

    /**
     * Tindakan Cepat: Cabut Badge Resmi SLHS Depot Bermasalah
     */
    public function revokeBadge(Request $request, $depotId)
    {
        $depot = Depot::findOrFail($depotId);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $depot->is_certified = false;
        $depot->certification_status = 'BELUM_TERSERTIFIKASI';
        $depot->certificate_notes = 'Badge SLHS dicabut oleh Tim Pengawas Dinkes: ' . $request->reason;
        $depot->save();

        return back()->with('success', "Badge SLHS Depot '{$depot->name}' telah berhasil dicabut.");
    }
}
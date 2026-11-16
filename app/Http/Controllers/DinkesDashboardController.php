<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Depot;
use Illuminate\Http\Request;

class DinkesDashboardController extends Controller
{
    /**
     * Tampilkan Portal Dinkes: Monitoring Kota (SCRUM-69), Verifikasi (SCRUM-67), & Respon Aduan (SCRUM-68)
     */
    public function index()
    {
        $prioritySort = function ($d) {
            $email = $d->user?->email ?? '';
            if ($email === 'mitra@galonku.com') {
                return [0, 0, $d->name];
            }
            if ($email !== '' && !str_ends_with($email, '@galonku.id')) {
                return [1, $d->id, $d->name];
            }
            return [2, 0, $d->name];
        };

        // 1. Pendaftaran Depot Baru yang Menunggu ACC Dinkes (is_approved = false)
        $pendingRegistrations = Depot::with('user')
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Permohonan Perpanjangan / Verifikasi Ulang Dokumen Lab (is_approved = true & status belum AKTIF)
        $pendingRenewals = Depot::with('user')
            ->where('is_approved', true)
            ->whereIn('certification_status', ['PROSES_RENEWAL', 'BELUM_TERSERTIFIKASI'])
            ->get()
            ->sortBy($prioritySort)
            ->values();

        // 3. Semua Depot Terdaftar (untuk Tabel Manajemen Status & Monitoring Kota SCRUM-69)
        $allApprovedDepots = Depot::with(['user', 'complaints'])
            ->where('is_approved', true)
            ->get()
            ->sortBy($prioritySort)
            ->values();

        // 4. Daftar Aduan Warga
        $complaints = Complaint::with('depot')->orderBy('created_at', 'desc')->get();

        // 5. Statistik Tingkat Kota (SCRUM-69)
        $healthyCount = 0;
        $problematicCount = 0;

        $monitoringMapDepots = $allApprovedDepots->map(function ($d) use (&$healthyCount, &$problematicCount) {
            $activeComplaints = $d->complaints->where('status', '!=', 'SELESAI')->count();
            $isHealthy = $d->is_certified && $d->certification_status === 'AKTIF' && $activeComplaints === 0;

            if ($isHealthy) {
                $healthyCount++;
                $category = 'healthy';
            } elseif ($d->certification_status === 'PROSES_RENEWAL' && $activeComplaints === 0) {
                $problematicCount++;
                $category = 'renewal';
            } else {
                $problematicCount++;
                $category = 'problematic';
            }

            return [
                'id' => $d->id,
                'name' => $d->name,
                'district' => $d->district,
                'address' => $d->address,
                'lat' => (float) $d->lat,
                'lng' => (float) $d->lng,
                'category' => $category,
                'is_certified' => (bool) $d->is_certified,
                'certification_status' => $d->certification_status,
                'slhs_number' => $d->slhs_number ?? '-',
                'tds_ppm' => $d->tds_ppm,
                'ph_level' => $d->ph_level,
                'active_complaints' => $activeComplaints,
                'show_url' => route('depots.show', $d->slug),
            ];
        });

        $stats = [
            'total_depots' => $allApprovedDepots->count(),
            'healthy_depots' => $healthyCount,
            'problematic_depots' => $problematicCount,
            'pending_registrations' => $pendingRegistrations->count(),
            'pending_renewals' => $pendingRenewals->count(),
            'active_complaints' => $complaints->where('status', '!=', 'SELESAI')->count(),
            'resolved_complaints' => $complaints->where('status', 'SELESAI')->count(),
            'avg_tds' => round($allApprovedDepots->avg('tds_ppm') ?? 0, 1),
        ];

        // 6. Rekapitulasi Analitik per Kecamatan (SCRUM-69)
        $districtStats = $allApprovedDepots->groupBy('district')->map(function ($items, $district) {
            $healthy = $items->filter(function ($d) {
                return $d->is_certified && $d->certification_status === 'AKTIF' && $d->complaints->where('status', '!=', 'SELESAI')->count() === 0;
            })->count();

            return [
                'district' => $district,
                'total' => $items->count(),
                'healthy' => $healthy,
                'problematic' => $items->count() - $healthy,
                'avg_tds' => round($items->avg('tds_ppm') ?? 0, 1),
            ];
        })->sortByDesc('problematic')->values();

        return view('dinkes.dashboard', compact(
            'pendingRegistrations',
            'pendingRenewals',
            'allApprovedDepots',
            'complaints',
            'monitoringMapDepots',
            'stats',
            'districtStats'
        ));
    }

    /**
     * Setujui Pendaftaran Depot Baru / Perpanjangan SLHS (SCRUM-67 / SCRUM-83)
     */
    public function approveDepot($id)
    {
        $depot = Depot::findOrFail($id);

        $depot->is_approved = true;
        $depot->is_certified = true;
        $depot->is_lab_passed = true;
        $depot->certification_status = 'AKTIF';

        if (empty($depot->slhs_number)) {
            $depot->slhs_number = 'SLHS-3578/' . str_pad($depot->id, 3, '0', STR_PAD_LEFT) . '/DINKES-SBY/' . date('Y');
        }

        $depot->issued_date = date('d F Y');
        $depot->expiry_date = date('d F Y', strtotime('+3 years'));
        $depot->certificate_notes = 'Disetujui dan diverifikasi oleh Tim Pengawas Sanitasi Dinkes Surabaya pada ' . date('d/m/Y H:i') . ' WIB.';
        $depot->save();

        return back()->with('success', "Depot '{$depot->name}' telah disetujui! Akun aktif di peta warga dan badge resmi SLHS telah diterbitkan.");
    }

    /**
     * Tolak Permohonan Sertifikasi / Uji Lab
     */
    public function rejectDepot(Request $request, $id)
    {
        $depot = Depot::findOrFail($id);

        $depot->is_certified = false;
        $depot->certification_status = 'BELUM_TERSERTIFIKASI';
        $depot->certificate_notes = 'Ditolak Dinkes: ' . $request->input('rejection_reason', 'Dokumen belum memenuhi syarat baku mutu Permenkes.');
        $depot->save();

        return back()->with('error', "Pengajuan sertifikasi depot '{$depot->name}' ditolak.");
    }

    /**
     * Ubah Status Sertifikasi Depot Secara Langsung (Terverifikasi -> Belum Diperpanjang / Nonaktif / Aktif)
     */
    public function updateDepotStatus(Request $request, $id)
    {
        $depot = Depot::findOrFail($id);

        $validated = $request->validate([
            'certification_status' => 'required|in:AKTIF,PROSES_RENEWAL,BELUM_TERSERTIFIKASI',
        ]);

        $status = $validated['certification_status'];
        $depot->certification_status = $status;
        $depot->is_certified = ($status === 'AKTIF');

        if ($status === 'AKTIF') {
            if (empty($depot->slhs_number)) {
                $depot->slhs_number = 'SLHS-3578/' . str_pad($depot->id, 3, '0', STR_PAD_LEFT) . '/DINKES-SBY/' . date('Y');
            }
            $depot->expiry_date = date('d F Y', strtotime('+3 years'));
            $depot->certificate_notes = 'Status diaktifkan kembali sebagai Terverifikasi Dinkes.';
        } elseif ($status === 'PROSES_RENEWAL') {
            $depot->certificate_notes = 'Status diubah Dinkes menjadi Belum Diperpanjang / Perlu Uji Lab Ulang.';
        } else {
            $depot->certificate_notes = 'Sertifikasi dicabut / Belum Tersertifikasi oleh Dinkes Surabaya.';
        }

        $depot->save();

        return back()->with('success', "Status sertifikasi '{$depot->name}' diperbarui menjadi: {$status}.");
    }

    /**
     * Respon & Update Status Laporan Pengaduan Air (SCRUM-68 / SCRUM-85)
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

        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('complaints_proofs', 'public');
            $complaint->proof_image_path = '/storage/' . $path;
        }

        $complaint->save();

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

<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Depot;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    /**
     * Tampilkan formulir pengaduan kualitas air (SCRUM-70)
     */
    public function create(Request $request)
    {
        $depots = Depot::with('user')
            ->where('is_approved', true)
            ->orderBy('name')
            ->get()
            ->sortBy(function ($d) {
                $email = $d->user?->email ?? '';
                if ($email === 'mitra@galonku.com') {
                    return [0, 0, $d->name];
                }
                if ($email !== '' && !str_ends_with($email, '@galonku.id')) {
                    return [1, $d->id, $d->name];
                }
                return [2, 0, $d->name];
            })->values();
        $selectedDepotId = $request->query('depot_id');

        return view('complaints.create', [
            'depots' => $depots,
            'selectedDepotId' => $selectedDepotId,
        ]);
    }

    /**
     * Simpan laporan pengaduan & terbitkan tiket resmi Dinkes
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'reporter_name' => 'required|string|max:100',
            'reporter_phone' => 'required|string|max:20',
            'issue_type' => 'required|in:keruh,berbau,berasa,lumut,serangga,lainnya',
            'purchase_date' => 'nullable|date',
            'description' => 'required|string|max:1500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // Maks 5MB
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('complaints', 'public');
            $photoPath = '/storage/' . $path;
        }

        $issueTitles = [
            'keruh' => 'Air Keruh / Mengandung Partikel Melayang',
            'berbau' => 'Air Berbau (Anyir / Tanah / Klorin Menyengat)',
            'berasa' => 'Rasa Air Aneh / Pahit / Asam',
            'lumut' => 'Ditemukan Lumut / Endapan Kotoran di Galon',
            'serangga' => 'Ditemukan Serangga / Hewan di Dalam Galon',
            'lainnya' => 'Keluhan Higienitas Sanitasi & Kebersihan Alat',
        ];

        $issueTitle = $issueTitles[$validated['issue_type']] ?? 'Laporan Kualitas Air Bermasalah';
        $purchaseDate = $validated['purchase_date'] ?? now()->toDateString();

        // Format nomor tiket resmi: DINKES-SBY-2026-XXXXXX
        $ticketNumber = 'DINKES-SBY-' . date('Y') . '-' . strtoupper(Str::random(6));

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'depot_id' => $validated['depot_id'],
            'reporter_name' => $validated['reporter_name'],
            'reporter_phone' => $validated['reporter_phone'],
            'issue_type' => $validated['issue_type'],
            'issue_title' => $issueTitle,
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'purchase_date' => $purchaseDate,
            'status' => 'TERKIRIM_KE_DINKES',
        ]);

        return redirect()->route('complaints.success', ['ticket' => $complaint->ticket_number])
            ->with('success', 'Laporan aduan Anda berhasil dikirim ke pengawas Dinas Kesehatan Kota Surabaya!');
    }

    /**
     * Tampilan detail tiket pengaduan setelah berhasil submit
     */
    public function success(string $ticket)
    {
        $complaint = Complaint::with('depot')->where('ticket_number', $ticket)->firstOrFail();

        return view('complaints.success', compact('complaint'));
    }
}

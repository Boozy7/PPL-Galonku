<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\Depot;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_end_to_end_demo_flow_works_without_errors(): void
    {
        $this->seed();
        Storage::fake('public');

        // 0. Pastikan seluruh halaman GET publik/login bisa dibuka (tidak 500)
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/depot/login')->assertStatus(200);
        $this->get('/depot/register')->assertStatus(200);
        $this->get('/dinkes/login')->assertStatus(200);
        $this->get('/lapor')->assertStatus(200);
        $this->get('/antrean')->assertStatus(200);

        // 1. Warga Login
        $res = $this->post('/authenticate', [
            'email' => 'warga@example.com',
            'password' => 'password',
            'type' => 'user',
        ]);
        $res->assertRedirect('/');
        $this->assertAuthenticated();

        // 2. Warga Buka List DAMIU -> Depot Demo #1 (mitra@galonku.com) harus di urutan paling atas
        $depotsPage = $this->get('/depots');
        $depotsPage->assertStatus(200);
        $depotsPage->assertSee('Lapor Air');
        $depotsPage->assertSee('Cek Antrean');
        $this->get('/api/depots')->assertStatus(200);
        $viewDepots = $depotsPage->viewData('depots');
        $this->assertNotEmpty($viewDepots);
        $firstDepot = $viewDepots->first();
        $this->assertEquals('mitra@galonku.com', $firstDepot->user->email);

        // 3. Warga Lihat Profil DAMIU
        $this->get('/depots/' . $firstDepot->slug)->assertStatus(200);

        // 4. Warga Pesan Refill Pickup
        $product = $firstDepot->products()->firstOrFail();
        $orderRes = $this->post('/order', [
            'depot_id' => $firstDepot->id,
            'customer_name' => 'Warga Surabaya',
            'customer_phone' => '081234567890',
            'pickup_time' => '15-30 Menit Lagi',
            'gallon_action' => 'bawa_sendiri',
            'products' => [
                ['id' => $product->id, 'qty' => 2],
            ],
        ]);
        $order = Order::latest('id')->first();
        $this->assertNotNull($order);
        $orderRes->assertRedirect('/order/track/' . $order->order_number);

        $this->get('/order/track/' . $order->order_number)->assertStatus(200);
        $this->get('/api/orders/' . $order->order_number . '/status')->assertStatus(200)->assertJson(['status' => 'DITERIMA']);

        // 5. Pindah ke Akun Depot (mitra@galonku.com) & Trigger Status Pesanan + PIN
        $this->post('/logout');
        $this->post('/authenticate', [
            'email' => 'mitra@galonku.com',
            'password' => 'password',
            'type' => 'depot',
        ])->assertRedirect('/depot');

        $depotPage = $this->get('/depot');
        $depotPage->assertStatus(200);
        $depotPage->assertDontSee('Lapor Air');
        $depotPage->assertDontSee('Cek Antrean');
        $this->get('/depot/report/export')->assertStatus(200);

        // DITERIMA -> SEDANG_DIISI
        $this->post("/depot/order/{$order->id}/status", ['status' => 'SEDANG_DIISI'])->assertSessionHas('success');
        $this->assertEquals('SEDANG_DIISI', $order->fresh()->status);

        // SEDANG_DIISI -> SIAP_DIAMBIL
        $this->post("/depot/order/{$order->id}/status", ['status' => 'SIAP_DIAMBIL'])->assertSessionHas('success');
        $this->assertEquals('SIAP_DIAMBIL', $order->fresh()->status);

        // Tes PIN salah -> gagal
        $this->post("/depot/order/{$order->id}/finish", ['pin' => '0000'])->assertSessionHasErrors('pin');
        $this->assertEquals('SIAP_DIAMBIL', $order->fresh()->status);

        // Tes PIN benar -> SELESAI
        $this->post("/depot/order/{$order->id}/finish", ['pin' => $order->pickup_pin])->assertSessionHas('success');
        $this->assertEquals('SELESAI', $order->fresh()->status);

        // 6. Depot Upload Ulang Dokumen Perpanjangan Sertifikasi
        $this->post('/depot/cert/upload', [
            'certificate' => UploadedFile::fake()->create('hasil_lab.pdf', 200, 'application/pdf'),
            'lab_name' => 'BBLK Surabaya',
            'test_date' => date('Y-m-d'),
            'notes' => 'Perpanjangan berkala 6 bulan',
        ])->assertSessionHas('success');
        $this->assertEquals('PROSES_RENEWAL', $firstDepot->fresh()->certification_status);

        // 7. Warga Memberi Ulasan & Melaporkan Air Bermasalah ke Dinkes
        $this->post('/logout');
        $this->post('/authenticate', [
            'email' => 'warga@example.com',
            'password' => 'password',
            'type' => 'user',
        ]);

        $this->post("/depots/{$firstDepot->id}/reviews", [
            'user_name' => 'Warga Surabaya',
            'rating' => 4,
            'clarity_rating' => 4,
            'taste_rating' => 4,
            'cleanliness_rating' => 5,
            'service_rating' => 5,
            'comment' => 'Pelayanan cepat, galon bersih.',
        ])->assertSessionHas('review_success');

        $validPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        $complaintRes = $this->post('/lapor', [
            'depot_id' => $firstDepot->id,
            'reporter_name' => 'Warga Surabaya',
            'reporter_phone' => '081234567890',
            'issue_type' => 'keruh',
            'description' => 'Air sedikit keruh saat dituang.',
            'photo' => UploadedFile::fake()->createWithContent('bukti.png', $validPng),
        ]);
        $complaint = Complaint::latest('id')->first();
        $this->assertNotNull($complaint);
        $complaintRes->assertRedirect('/lapor/sukses/' . $complaint->ticket_number);
        $this->get('/lapor/sukses/' . $complaint->ticket_number)->assertStatus(200);

        // 8. Pindah ke Akun Dinkes (admin@dinkes.surabaya.go.id)
        $this->post('/logout');
        $this->post('/authenticate', [
            'email' => 'admin@dinkes.surabaya.go.id',
            'password' => 'password',
            'type' => 'dinkes',
        ])->assertRedirect('/dinkes');

        $dinkesPage = $this->get('/dinkes');
        $dinkesPage->assertStatus(200);
        $dinkesPage->assertDontSee('Lapor Air');
        $dinkesPage->assertDontSee('Cek Antrean');
        $dinkesPage->assertSee('Dashboard');

        // Depot Demo #1 ada di paling atas daftar pendingRenewals dan allApprovedDepots
        $this->assertEquals($firstDepot->id, $dinkesPage->viewData('pendingRenewals')->first()->id);
        $this->assertEquals($firstDepot->id, $dinkesPage->viewData('allApprovedDepots')->first()->id);

        // ACC Perpanjangan Depot #1
        $this->post("/dinkes/depot/{$firstDepot->id}/approve")->assertSessionHas('success');
        $this->assertEquals('AKTIF', $firstDepot->fresh()->certification_status);
        $this->assertTrue($firstDepot->fresh()->is_certified);

        // Ubah Status Depot jadi Belum Diperpanjang (PROSES_RENEWAL) lalu aktifkan lagi
        $this->post("/dinkes/depot/{$firstDepot->id}/status", ['certification_status' => 'PROSES_RENEWAL'])->assertSessionHas('success');
        $this->assertEquals('PROSES_RENEWAL', $firstDepot->fresh()->certification_status);
        $this->post("/dinkes/depot/{$firstDepot->id}/status", ['certification_status' => 'AKTIF'])->assertSessionHas('success');
        $this->assertEquals('AKTIF', $firstDepot->fresh()->certification_status);

        // Respon Aduan Warga & Cabut Badge
        $this->post("/dinkes/complaint/{$complaint->id}/update", [
            'status' => 'SELESAI',
            'dinkes_notes' => 'Sudah dilakukan pembinaan dan uji ulang.',
            'revoke_badge' => '1',
        ])->assertSessionHas('success');
        $this->assertEquals('SELESAI', $complaint->fresh()->status);
        $this->assertFalse($firstDepot->fresh()->is_certified);

        // Aktifkan kembali badge depot #1 agar rapi
        $this->post("/dinkes/depot/{$firstDepot->id}/approve")->assertSessionHas('success');
        $this->assertTrue($firstDepot->fresh()->is_certified);

        // 9. Pendaftaran Depot Baru (Foto Toko + Sertifikat + Map Pin) -> Belum muncul di warga sebelum ACC Dinkes
        $this->post('/logout');
        $regRes = $this->post('/depot/register', [
            'owner_name' => 'Pemilik Depot Baru',
            'email' => 'depotbaru@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'depot_name' => 'Depot Air Segar Baru',
            'district' => 'Gubeng',
            'address' => 'Jl. Airlangga No. 10',
            'phone' => '081999888777',
            'lat' => '-7.2741280',
            'lng' => '112.7553420',
            'cover_image' => UploadedFile::fake()->createWithContent('toko.png', $validPng),
            'certificate' => UploadedFile::fake()->createWithContent('slhs.png', $validPng),
        ]);
        $regRes->assertRedirect('/depot');
        $newDepot = Depot::where('name', 'Depot Air Segar Baru')->firstOrFail();
        $this->assertFalse($newDepot->is_approved);

        // Pastikan BELUM tampil di daftar warga sebelum di-ACC
        $wargaCheckBefore = $this->get('/depots')->viewData('depots');
        $this->assertFalse($wargaCheckBefore->contains('id', $newDepot->id));

        // Dinkes Login & ACC Depot Baru
        $this->post('/logout');
        $this->post('/authenticate', [
            'email' => 'admin@dinkes.surabaya.go.id',
            'password' => 'password',
            'type' => 'dinkes',
        ]);
        $this->post("/dinkes/depot/{$newDepot->id}/approve")->assertSessionHas('success');
        $this->assertTrue($newDepot->fresh()->is_approved);
        $this->assertTrue($newDepot->fresh()->is_certified);

        // Pastikan SEKARANG tampil di daftar warga di URUTAN KE-2 (index 1) setelah Depot #1 (index 0)!
        $wargaCheckAfter = $this->get('/depots')->viewData('depots');
        $this->assertEquals($firstDepot->id, $wargaCheckAfter[0]->id);
        $this->assertEquals($newDepot->id, $wargaCheckAfter[1]->id);
    }
}

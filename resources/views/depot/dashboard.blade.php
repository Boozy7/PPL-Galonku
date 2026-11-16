                    <label class="block text-xs font-bold text-slate-700 mb-1">Volume *</label>
                    <input type="text" id="editVolume" name="volume" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Isi Ulang (Rp) *</label>
                <input type="number" id="editPrice" name="price" required min="1000" step="500" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 font-mono">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditProduk').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition">Perbarui Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditProductModal(id, name, type, price, volume) {
        const form = document.getElementById('formEditProduk');
        form.action = "{{ url('/depot/products') }}/" + id;
        document.getElementById('editName').value = name;
        document.getElementById('editType').value = type;
        document.getElementById('editPrice').value = price;
        document.getElementById('editVolume').value = volume;
        document.getElementById('modalEditProduk').classList.remove('hidden');
    }
</script>

<!-- STATISTIK RINGKASAN REKAPITULASI (SCRUM-66) -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Hari Ini</span>
              <span class="text-2xl font-black text-slate-900 font-mono mt-1 block">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</span>
              <span class="text-[10px] text-emerald-600 font-bold">Total transaksi terbayar</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
              💰
          </div>
      </div>

      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Galon Terjual</span>
              <span class="text-2xl font-black text-brand-700 font-mono mt-1 block">{{ $todayGallonsSold ?? 0 }} <span class="text-sm text-slate-500 font-normal">Galon</span></span>
              <span class="text-[10px] text-slate-400">Volume isi ulang hari ini</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-lg">
              💧
          </div>
      </div>

      <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pesanan Selesai</span>
              <span class="text-2xl font-black text-slate-900 font-mono mt-1 block">{{ $completedOrdersCount ?? 0 }} <span class="text-sm text-slate-500 font-normal">Order</span></span>
              <span class="text-[10px] text-emerald-600 font-semibold">Terselesaikan hari ini</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
              ✅
          </div>
      </div>
  </div>

  <!-- TABEL REKAPITULASI PENJUALAN (SCRUM-66) -->
  <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80 mt-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
          <div>
              <h2 class="text-base font-extrabold text-slate-900">Rekapitulasi Penjualan & Transaksi</h2>
              <p class="text-xs text-slate-500 mt-0.5">Riwayat transaksi pemesanan galon masuk</p>
          </div>

          <!-- Tombol Unduh Laporan CSV -->
          <a href="{{ route('depot.report.export') }}"
             class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition self-start sm:self-auto">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span>Unduh Laporan (CSV)</span>
          </a>
      </div>

      <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
              <thead>
                  <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                      <th class="pb-3 pl-2">Waktu</th>
                      <th class="pb-3">No. Antrean</th>
                      <th class="pb-3">Nama Pemesan</th>
                      <th class="pb-3">Produk & Qty</th>
                      <th class="pb-3">Metode</th>
                      <th class="pb-3">Total Biaya</th>
                      <th class="pb-3 text-right pr-2">Status</th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                  @forelse($recentTransactions ?? [] as $tx)
                      <tr class="hover:bg-slate-50/80 transition">
                          <td class="py-3 pl-2 text-slate-500 font-mono">{{ $tx->created_at->format('H:i') }}</td>
                          <td class="py-3 font-mono font-bold text-slate-900">{{ $tx->queue_number }}</td>
                          <td class="py-3 font-semibold text-slate-800">{{ $tx->customer_name }}</td>
                          <td class="py-3 text-slate-600">{{ $tx->quantity }}x {{ $tx->water_product_name }}</td>
                          <td class="py-3">
                              <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $tx->delivery_type === 'delivery' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                  {{ $tx->delivery_type === 'delivery' ? 'Diantar' : 'Pickup' }}
                              </span>
                          </td>
                          <td class="py-3 font-bold text-brand-700 font-mono">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                          <td class="py-3 text-right pr-2">
                              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
                                  {{ $tx->status }}
                              </span>
                          </td>
                      </tr>
                  @empty
                      <tr>
                          <td colspan="7" class="py-6 text-center text-slate-400">Belum ada riwayat transaksi tercatat.</td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
  </div>
@endsection

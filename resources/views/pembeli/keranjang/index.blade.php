@extends('layouts.public')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <!-- Page Header -->
    <div class="mb-8 border-b border-slate-200 pb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-sm">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                Keranjang Belanja
            </h1>
            <p class="mt-2 text-sm text-slate-500">Periksa dan kelola produk pilihan Anda secara langsung.</p>
        </div>
        <a href="{{ route('kategori') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-slate-200 shadow-sm text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            Lanjut Belanja
        </a>
    </div>

    <!-- Live Toast Notification Container -->
    <div id="cart-toast" class="fixed bottom-24 right-6 z-50 transform translate-y-10 opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-sm font-semibold"></div>

    <!-- Alert Messages (Flash) -->
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-8 rounded-r-xl shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-500 mr-3"></i>
                <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Cart Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden" id="cart-wrapper">
        <div id="empty-cart-state" class="{{ $keranjangs->isEmpty() ? '' : 'hidden' }} text-center py-20 px-6">
            <div class="w-24 h-24 bg-indigo-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-indigo-100">
                <i class="fas fa-shopping-cart text-4xl text-indigo-400"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Keranjang Anda Kosong</h3>
            <p class="text-slate-500 mb-8 max-w-sm mx-auto text-sm leading-relaxed">Sepertinya Anda belum menambahkan produk apapun. Yuk, temukan produk mangga dan olahan UMKM favorit Anda!</p>
            <a href="{{ route('kategori') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-shopping-bag mr-2"></i> Mulai Belanja Sekarang
            </a>
        </div>

        @if (!$keranjangs->isEmpty())
            <div class="overflow-x-auto" id="cart-table-container">
                <table class="w-full text-left border-collapse min-w-max">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider font-bold">
                            <th class="p-4 sm:pl-6">Produk</th>
                            <th class="p-4">Harga Satuan</th>
                            <th class="p-4 text-center">Jumlah (Realtime)</th>
                            <th class="p-4">Subtotal</th>
                            <th class="p-4 sm:pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm" id="cart-items-body">
                        @foreach ($keranjangs as $item)
                            @if ($item->produk)
                                <tr class="hover:bg-slate-50/60 transition-colors duration-150 cart-row" id="cart-item-{{ $item->id }}" data-item-id="{{ $item->id }}" data-stok="{{ $item->produk->stok }}">
                                    <td class="p-4 sm:pl-6">
                                        <div class="flex items-center gap-4">
                                            @if ($item->produk->gambar)
                                                <img src="{{ asset('storage/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama }}" class="w-16 h-16 rounded-2xl object-cover bg-slate-100 border border-slate-200 shadow-sm">
                                            @else
                                                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                                    <i class="fas fa-image text-2xl"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('pembeli.produk.show', $item->produk->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors text-base block mb-1">
                                                    {{ $item->produk->nama }}
                                                </a>
                                                
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-slate-400 font-medium">Toko: {{ $item->produk->umkm->nama_toko ?? 'Mitra UMKM' }}</span>
                                                    @if ($item->produk->diskon && now()->between($item->produk->diskon->tanggal_mulai, $item->produk->diskon->tanggal_berakhir))
                                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-md border border-red-200">
                                                            <i class="fas fa-tag"></i> Diskon {{ $item->produk->diskon->persen_diskon }}%
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Stock Warning badge (Realtime updated) -->
                                                <div id="stock-warning-{{ $item->id }}" class="stock-warning {{ $item->jumlah > $item->produk->stok ? '' : 'hidden' }} text-amber-700 text-xs mt-2 flex items-center gap-1.5 font-medium bg-amber-50 w-fit px-2.5 py-1 rounded-lg border border-amber-200">
                                                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                                    <span>Stok hanya tersisa <strong class="max-stock-val">{{ $item->produk->stok }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        @if (isset($item->harga_setelah_diskon) && $item->harga_setelah_diskon < $item->produk->harga)
                                            <div class="text-slate-400 line-through text-xs mb-0.5">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</div>
                                            <div class="text-indigo-600 font-bold">Rp {{ number_format($item->harga_setelah_diskon, 0, ',', '.') }}</div>
                                        @else
                                            <div class="font-bold text-slate-800">Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <!-- Realtime Stepper Counter -->
                                        <div class="flex items-center justify-center">
                                            <div class="inline-flex items-center border border-slate-200 rounded-2xl bg-slate-50/80 p-1 shadow-sm gap-1 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition">
                                                <button type="button" 
                                                    onclick="changeQuantity({{ $item->id }}, -1)"
                                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-white hover:bg-slate-100 active:scale-95 text-slate-600 font-bold border border-slate-200/60 shadow-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
                                                    id="btn-minus-{{ $item->id }}"
                                                    {{ $item->jumlah <= 1 ? 'disabled' : '' }}
                                                    title="Kurangi">
                                                    <i class="fas fa-minus text-[10px]"></i>
                                                </button>
                                                
                                                <input type="number" 
                                                    id="qty-input-{{ $item->id }}" 
                                                    value="{{ $item->jumlah }}" 
                                                    min="1" 
                                                    max="{{ $item->produk->stok }}" 
                                                    oninput="handleQtyInput({{ $item->id }})"
                                                    class="w-12 bg-transparent text-center font-bold text-slate-800 text-sm outline-none border-none p-0 focus:ring-0">
                                                
                                                <button type="button" 
                                                    onclick="changeQuantity({{ $item->id }}, 1)"
                                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-white hover:bg-slate-100 active:scale-95 text-slate-600 font-bold border border-slate-200/60 shadow-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
                                                    id="btn-plus-{{ $item->id }}"
                                                    {{ $item->jumlah >= $item->produk->stok ? 'disabled' : '' }}
                                                    title="Tambah">
                                                    <i class="fas fa-plus text-[10px]"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <div class="font-extrabold text-slate-900 text-base" id="subtotal-{{ $item->id }}">
                                            @if (isset($item->subtotal_setelah_diskon) && $item->harga_setelah_diskon < $item->produk->harga)
                                                Rp {{ number_format($item->subtotal_setelah_diskon, 0, ',', '.') }}
                                            @else
                                                Rp {{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 sm:pr-6 text-right">
                                        <!-- Realtime Delete Button -->
                                        <button type="button" 
                                            onclick="deleteCartItem({{ $item->id }})" 
                                            class="w-9 h-9 inline-flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" 
                                            title="Hapus dari Keranjang">
                                            <i class="fas fa-trash-can text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Footer -->
            <div id="cart-summary-footer" class="bg-slate-50/70 border-t border-slate-200/80 p-6 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="text-xs sm:text-sm text-slate-500 flex items-center gap-2">
                    <i class="fas fa-shield-check text-emerald-600 text-lg"></i>
                    <span>Perubahan jumlah produk tersimpan otomatis secara <strong>real-time</strong>.</span>
                </div>
                
                @php
                    $totalKeranjang = 0;
                    $stokAman = true;
                    foreach ($keranjangs as $item) {
                        if ($item->produk) {
                            $harga = $item->produk->harga;
                            if ($item->produk->diskon && now()->between($item->produk->diskon->tanggal_mulai, $item->produk->diskon->tanggal_berakhir)) {
                                $harga = $harga - ($harga * $item->produk->diskon->persen_diskon / 100);
                            }
                            $totalKeranjang += $harga * $item->jumlah;
                            
                            if ($item->jumlah > $item->produk->stok) {
                                $stokAman = false;
                            }
                        }
                    }
                @endphp

                <div class="flex flex-col sm:flex-row items-center gap-6 w-full sm:w-auto">
                    <div class="text-center sm:text-right w-full sm:w-auto">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Keranjang</p>
                        <p id="total-keranjang-display" class="text-2xl sm:text-3xl font-extrabold text-indigo-600 transition-all">
                            Rp {{ number_format($totalKeranjang, 0, ',', '.') }}
                        </p>
                    </div>

                    <a href="{{ route('pembeli.order') }}" 
                       id="checkout-all-btn"
                       class="w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-base transition-all transform shadow-md flex items-center justify-center gap-2.5 {{ !$stokAman ? 'bg-slate-200 text-slate-400 border border-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-600/20 hover:-translate-y-0.5' }}"
                       @if(!$stokAman) onclick="return false;" @endif>
                        <i class="fas fa-bag-shopping"></i>
                        <span>Checkout Semua</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const updateUrlPattern = "{{ route('pembeli.keranjang.update', ':id') }}";
    const destroyUrlPattern = "{{ route('pembeli.keranjang.destroy', ':id') }}";
    const debounceTimers = {};

    function showToast(message, type = 'success') {
        const toast = document.getElementById('cart-toast');
        if (!toast) return;

        if (type === 'success') {
            toast.className = 'fixed bottom-24 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-semibold transform transition-all duration-300';
            toast.innerHTML = `<i class="fas fa-check-circle text-emerald-600 text-base"></i><span>${message}</span>`;
        } else {
            toast.className = 'fixed bottom-24 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl border border-red-200 bg-red-50 text-red-800 text-sm font-semibold transform transition-all duration-300';
            toast.innerHTML = `<i class="fas fa-exclamation-circle text-red-600 text-base"></i><span>${message}</span>`;
        }

        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        toast.style.pointerEvents = 'auto';

        clearTimeout(toast.timer);
        toast.timer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.pointerEvents = 'none';
        }, 2500);
    }

    function updateHeaderCartBadge(count) {
        // Find cart badges in header & mobile dock
        const badges = document.querySelectorAll('a[href*="keranjang"] span.rounded-full');
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
    }

    function changeQuantity(itemId, delta) {
        const input = document.getElementById(`qty-input-${itemId}`);
        if (!input) return;

        const max = parseInt(input.getAttribute('max')) || 9999;
        let currentVal = parseInt(input.value) || 1;
        let newVal = currentVal + delta;

        if (newVal < 1) newVal = 1;
        if (newVal > max) {
            newVal = max;
            showToast(`Maksimal stok tercapai (${max} item)`, 'error');
        }

        input.value = newVal;
        triggerQuantitySync(itemId, newVal);
    }

    function handleQtyInput(itemId) {
        const input = document.getElementById(`qty-input-${itemId}`);
        if (!input) return;

        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) {
            val = 1;
        }

        const max = parseInt(input.getAttribute('max')) || 9999;
        if (val > max) {
            val = max;
            input.value = val;
            showToast(`Jumlah melebihi stok (${max} item)`, 'error');
        }

        clearTimeout(debounceTimers[itemId]);
        debounceTimers[itemId] = setTimeout(() => {
            triggerQuantitySync(itemId, val);
        }, 300);
    }

    function triggerQuantitySync(itemId, quantity) {
        const row = document.getElementById(`cart-item-${itemId}`);
        const subtotalEl = document.getElementById(`subtotal-${itemId}`);
        const btnMinus = document.getElementById(`btn-minus-${itemId}`);
        const btnPlus = document.getElementById(`btn-plus-${itemId}`);
        const warningEl = document.getElementById(`stock-warning-${itemId}`);
        const input = document.getElementById(`qty-input-${itemId}`);
        const maxStok = parseInt(input.getAttribute('max')) || 9999;

        // Visual stepper buttons state
        if (btnMinus) btnMinus.disabled = (quantity <= 1);
        if (btnPlus) btnPlus.disabled = (quantity >= maxStok);

        if (subtotalEl) {
            subtotalEl.style.opacity = '0.5';
        }

        const url = updateUrlPattern.replace(':id', itemId);

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ jumlah: quantity })
        })
        .then(res => res.json())
        .then(data => {
            if (subtotalEl) subtotalEl.style.opacity = '1';

            if (data.success) {
                if (subtotalEl) subtotalEl.textContent = data.subtotal_formatted;
                
                const totalKeranjangEl = document.getElementById('total-keranjang-display');
                if (totalKeranjangEl) {
                    totalKeranjangEl.textContent = data.total_keranjang_formatted;
                }

                updateHeaderCartBadge(data.total_count);

                // Update warning
                if (warningEl) {
                    if (data.jumlah > data.max_stok) {
                        warningEl.classList.remove('hidden');
                    } else {
                        warningEl.classList.add('hidden');
                    }
                }

                // Update checkout button state
                const checkoutBtn = document.getElementById('checkout-all-btn');
                if (checkoutBtn) {
                    if (data.stok_aman) {
                        checkoutBtn.className = 'w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-base transition-all transform shadow-md flex items-center justify-center gap-2.5 bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-600/20 hover:-translate-y-0.5 cursor-pointer';
                        checkoutBtn.onclick = null;
                    } else {
                        checkoutBtn.className = 'w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-base transition-all transform shadow-md flex items-center justify-center gap-2.5 bg-slate-200 text-slate-400 border border-slate-300 cursor-not-allowed';
                        checkoutBtn.onclick = (e) => { e.preventDefault(); showToast('Stok tidak mencukupi untuk checkout.', 'error'); return false; };
                    }
                }

                showToast('Keranjang diperbarui', 'success');
            } else {
                showToast(data.message || 'Gagal memperbarui jumlah', 'error');
            }
        })
        .catch(err => {
            if (subtotalEl) subtotalEl.style.opacity = '1';
            showToast('Koneksi terputus. Silakan coba lagi.', 'error');
        });
    }

    function deleteCartItem(itemId) {
        if (!confirm('Hapus produk ini dari keranjang?')) return;

        const row = document.getElementById(`cart-item-${itemId}`);
        if (row) {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '0.3';
        }

        const url = destroyUrlPattern.replace(':id', itemId);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (row) {
                    row.style.transform = 'scale(0.95)';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();

                        // Check if no items left
                        const remainingRows = document.querySelectorAll('.cart-row');
                        if (remainingRows.length === 0 || data.is_empty) {
                            const tableContainer = document.getElementById('cart-table-container');
                            const footerContainer = document.getElementById('cart-summary-footer');
                            const emptyState = document.getElementById('empty-cart-state');
                            if (tableContainer) tableContainer.remove();
                            if (footerContainer) footerContainer.remove();
                            if (emptyState) emptyState.classList.remove('hidden');
                        }
                    }, 300);
                }

                const totalKeranjangEl = document.getElementById('total-keranjang-display');
                if (totalKeranjangEl) {
                    totalKeranjangEl.textContent = data.total_keranjang_formatted;
                }

                updateHeaderCartBadge(data.total_count);
                showToast('Produk dihapus dari keranjang', 'success');
            } else {
                if (row) row.style.opacity = '1';
                showToast(data.message || 'Gagal menghapus produk', 'error');
            }
        })
        .catch(err => {
            if (row) row.style.opacity = '1';
            showToast('Terjadi kesalahan saat menghapus.', 'error');
        });
    }
</script>
@endpush
@endsection
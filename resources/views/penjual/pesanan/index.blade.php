@extends('layouts.app')

@section('page_title', 'Daftar Pesanan Toko')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Custom High-Contrast Styling for Flatpickr */
    .flatpickr-calendar {
        background: #ffffff !important;
        border-radius: 1.25rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        padding: 0.75rem !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        width: 320px !important;
        color: #0f172a !important;
    }
    .flatpickr-months {
        margin-bottom: 0.5rem !important;
    }
    .flatpickr-months .flatpickr-month {
        color: #0f172a !important;
        fill: #0f172a !important;
        height: 36px !important;
    }
    .flatpickr-current-month {
        font-size: 0.95rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        padding-top: 4px !important;
    }
    .flatpickr-current-month .cur-month {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-current-month input.cur-year {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-weekdays {
        margin-bottom: 0.5rem !important;
    }
    span.flatpickr-weekday {
        color: #475569 !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
    }
    .flatpickr-days {
        width: 100% !important;
    }
    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        justify-content: space-around !important;
    }
    .flatpickr-day {
        color: #0f172a !important;
        border-radius: 0.65rem !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        height: 38px !important;
        line-height: 38px !important;
        max-width: 38px !important;
        margin: 2px 0 !important;
        border: 1px solid transparent !important;
    }
    .flatpickr-day:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.today {
        border-color: #10b981 !important;
        color: #047857 !important;
        font-weight: 900 !important;
        background: #ecfdf5 !important;
    }
    .flatpickr-day.selected, 
    .flatpickr-day.startRange, 
    .flatpickr-day.endRange {
        background: #059669 !important;
        color: #ffffff !important;
        border-color: #059669 !important;
        font-weight: 800 !important;
    }
    .flatpickr-day.inRange {
        background: #d1fae5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
        box-shadow: -5px 0 0 #d1fae5, 5px 0 0 #d1fae5 !important;
    }
    .flatpickr-day.prevMonthDay, 
    .flatpickr-day.nextMonthDay {
        color: #94a3b8 !important;
        font-weight: 500 !important;
    }
    .flatpickr-prev-month svg, 
    .flatpickr-next-month svg {
        fill: #0f172a !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Daftar Pesanan Toko</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola pesanan yang masuk, proses pengiriman, dan pantau statusnya</p>
        </div>
    </div>

    <!-- 📅 Dynamic Calendar Range Filter Bar -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl p-4 sm:p-6">
        <form id="sellerOrderFilterForm" method="GET" action="{{ route('penjual.pesanan.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <input type="hidden" name="period" id="sellerOrderPeriod" value="{{ $period ?? 'all' }}">
            <input type="hidden" name="start_date" id="sellerOrderStartDate" value="{{ $startDateInput ?? '' }}">
            <input type="hidden" name="end_date" id="sellerOrderEndDate" value="{{ $endDateInput ?? '' }}">

            <div class="flex flex-wrap items-center gap-3 flex-1">
                
                <!-- Dynamic Interactive Calendar Range Input -->
                <div class="relative min-w-[260px] sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-calendar-days text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="flatpickrSellerOrderCalendar" 
                        placeholder="Pilih Rentang Tanggal Pesanan..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer"
                        readonly
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        @if(($period ?? 'all') !== 'all' || request('start_date'))
                            <a href="{{ route('penjual.pesanan.index') }}" title="Reset Filter" class="text-slate-400 hover:text-rose-500 transition text-xs">
                                <i class="fas fa-circle-xmark"></i>
                            </a>
                        @else
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
                        @endif
                    </div>
                </div>

                <!-- Quick Filter Period Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="setSellerOrderPeriod('all')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Waktu
                    </button>
                    <button type="button" onclick="setSellerOrderPeriod('today')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setSellerOrderPeriod('7days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '7days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setSellerOrderPeriod('30days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '30days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        30 Hari Terakhir
                    </button>
                    <button type="button" onclick="setSellerOrderPeriod('this_month')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_month' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setSellerOrderPeriod('this_year')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_year' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Tahun Ini
                    </button>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs font-bold">
                    <div class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></div>
                    <span class="text-slate-500 font-normal">Periode:</span>
                    <strong class="font-extrabold text-emerald-800">{{ $activePeriodLabel }}</strong>
                </div>

                @if(($period ?? 'all') !== 'all' || request('start_date'))
                    <a href="{{ route('penjual.pesanan.index') }}" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fas fa-rotate-left text-[10px]"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Pesanan Selesai Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Pesanan Masuk & Selesai</h3>
                    <p class="text-xs text-slate-500">Pesanan terverifikasi yang perlu diproses atau sudah selesai</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{{ $pesananComplete->count() }} Pesanan</span>
        </div>

        @if($pesananComplete->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-slate-300"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Belum Ada Pesanan</h4>
                <p class="text-sm text-slate-500">Semua pesanan pada periode {{ $activePeriodLabel }} akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                            <th class="px-6 py-4 whitespace-nowrap">Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap">Pembeli</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Jumlah</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Total Harga</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pesananComplete as $key => $order)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $order->produk->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ $order->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {{ $order->jumlah }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-brand-700">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center space-y-2">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ str_replace('_', ' ', $order->status_pesanan) }}
                                        </span>
                                    </div>
                                    @if($order->status_pesanan === 'diterima')
                                        <div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                                Diterima
                                            </span>
                                        </div>
                                    @elseif($order->status_pesanan === 'belum_diterima')
                                        <div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider">
                                                Belum Diterima
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <a href="{{ route('penjual.pesanan.create', $order->id) }}" class="inline-flex items-center justify-center gap-1.5 w-28 px-2.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                                            <i class="fas fa-truck-fast text-[10px]"></i> Proses / Resi
                                        </a>
                                        <a href="{{ route('penjual.pesanan.shipping-label', $order->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 w-28 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] rounded-lg transition border border-emerald-200" title="Cetak Label Resi Thermal A6">
                                            <i class="fas fa-barcode text-[10px]"></i> Label A6
                                        </a>
                                        <a href="{{ route('penjual.invoice.show', $order->id) }}" class="inline-flex items-center justify-center gap-1.5 w-28 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-[11px] rounded-lg transition">
                                            <i class="fas fa-file-invoice text-[10px]"></i> Faktur
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Pesanan Dibatalkan Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Pesanan Dibatalkan</h3>
                    <p class="text-xs text-slate-500">Riwayat pesanan yang telah dibatalkan</p>
                </div>
            </div>
            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-3 py-1 rounded-full">{{ $pesananCancel->count() }} Pesanan</span>
        </div>

        @if($pesananCancel->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-ban text-2xl text-slate-300"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Tidak Ada Pesanan Dibatalkan</h4>
                <p class="text-sm text-slate-500">Semua pesanan yang dibatalkan akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                            <th class="px-6 py-4 whitespace-nowrap">Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap">Pembeli</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Jumlah</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Total Harga</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pesananCancel as $key => $order)
                            <tr class="hover:bg-slate-50/50 transition opacity-80 hover:opacity-100">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $order->produk->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ $order->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {{ $order->jumlah }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-700 line-through">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center space-y-2">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider">
                                            {{ str_replace('_', ' ', $order->status_pesanan) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-slate-400 font-medium text-xs">-</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    function setSellerOrderPeriod(periodVal) {
        document.getElementById('sellerOrderPeriod').value = periodVal;
        document.getElementById('sellerOrderStartDate').value = '';
        document.getElementById('sellerOrderEndDate').value = '';
        document.getElementById('sellerOrderFilterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('flatpickrSellerOrderCalendar');
        const initialStart = "{{ $startDateInput ?? '' }}";
        const initialEnd = "{{ $endDateInput ?? '' }}";
        let defaultDates = [];
        if (initialStart && initialEnd) {
            defaultDates = [initialStart, initialEnd];
        } else if (initialStart) {
            defaultDates = [initialStart];
        }

        if (calendarEl && typeof flatpickr !== 'undefined') {
            flatpickr(calendarEl, {
                mode: "range",
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                altInputClass: "w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer",
                defaultDate: defaultDates,
                showMonths: 1,
                animate: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startStr = instance.formatDate(selectedDates[0], "Y-m-d");
                        const endStr = instance.formatDate(selectedDates[1], "Y-m-d");

                        document.getElementById('sellerOrderPeriod').value = 'custom';
                        document.getElementById('sellerOrderStartDate').value = startStr;
                        document.getElementById('sellerOrderEndDate').value = endStr;
                        document.getElementById('sellerOrderFilterForm').submit();
                    } else if (selectedDates.length === 1) {
                        const singleStr = instance.formatDate(selectedDates[0], "Y-m-d");
                        document.getElementById('sellerOrderPeriod').value = 'custom';
                        document.getElementById('sellerOrderStartDate').value = singleStr;
                        document.getElementById('sellerOrderEndDate').value = singleStr;
                        document.getElementById('sellerOrderFilterForm').submit();
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection

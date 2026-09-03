@extends('layouts.app')

@section('page_title', 'Ringkasan Pendapatan Produk')

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
    .flatpickr-next-month svg,
    .flatpickr-calendar svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
        display: inline-block !important;
        fill: #0f172a !important;
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Ringkasan Pendapatan</h2>
            <p class="text-sm text-slate-500 mt-1">Laporan pendapatan penjualan berdasarkan produk toko Anda</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penjual.pendapatan.export.summary.excel', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-xl transition shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengekspor...'; setTimeout(() => { this.innerHTML='<i class=\'fas fa-file-excel\'></i> Export Excel'; }, 3000);">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('penjual.pendapatan.export.summary.pdf', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-xl transition shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengekspor...'; setTimeout(() => { this.innerHTML='<i class=\'fas fa-file-pdf\'></i> Export PDF'; }, 3000);">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- 📅 Dynamic Calendar Range Filter Bar -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl p-4 sm:p-6">
        <form id="pendapatanFilterForm" method="GET" action="{{ route('penjual.pendapatan.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <input type="hidden" name="filter" id="filterInput" value="{{ $filter ?? 'bulan' }}">
            <input type="hidden" name="start_date" id="filterStartDate" value="{{ $startDateInput ?? '' }}">
            <input type="hidden" name="end_date" id="filterEndDate" value="{{ $endDateInput ?? '' }}">

            <div class="flex flex-wrap items-center gap-3 flex-1">
                
                <!-- Dynamic Interactive Calendar Range Input -->
                <div class="relative min-w-[260px] sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-calendar-days text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="flatpickrPendapatanCalendar" 
                        placeholder="Pilih Rentang Tanggal..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer"
                        readonly
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        @if(request('start_date') || (request('filter') && request('filter') !== 'bulan'))
                            <a href="{{ route('penjual.pendapatan.index') }}" title="Reset Filter" class="text-slate-400 hover:text-rose-500 transition text-xs">
                                <i class="fas fa-circle-xmark"></i>
                            </a>
                        @else
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
                        @endif
                    </div>
                </div>

                <!-- Quick Filter Period Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="setPendapatanPeriod('all')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($filter ?? '') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Waktu
                    </button>
                    <button type="button" onclick="setPendapatanPeriod('today')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($filter ?? '') === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setPendapatanPeriod('7days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($filter ?? '') === '7days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setPendapatanPeriod('30days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($filter ?? '') === '30days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        30 Hari Terakhir
                    </button>
                    <button type="button" onclick="setPendapatanPeriod('bulan')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($filter ?? 'bulan') === 'bulan' && !request('start_date') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setPendapatanPeriod('tahun')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($filter ?? '') === 'tahun' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Tahun Ini
                    </button>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs font-bold">
                    <div class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></div>
                    <span class="text-slate-500 font-normal">Rentang:</span>
                    <strong class="font-extrabold text-emerald-800">{{ $periodText }}</strong>
                </div>

                @if(request('start_date') || (request('filter') && request('filter') !== 'bulan'))
                    <a href="{{ route('penjual.pendapatan.index') }}" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fas fa-rotate-left text-[10px]"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quick Stats & Previous Month Info -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Pendapatan Bulan Lalu -->
        <div class="lg:col-span-1">
            <div class="card bg-slate-50 border border-slate-200/80 shadow-sm rounded-3xl p-6 h-full flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-2xl bg-slate-200 text-slate-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-600 leading-tight">Pendapatan Bulan Lalu</h3>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-slate-900 font-display">
                        {{ isset($totalPendapatanBulanLalu) ? 'Rp ' . number_format($totalPendapatanBulanLalu, 0, ',', '.') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Period Stats -->
        @if(!$pendapatanPerProduk->isEmpty())
            @php
                $totalPendapatan = $pendapatanPerProduk->sum('total_pendapatan');
                $totalTerjual = $pendapatanPerProduk->sum('total_terjual');
                $totalProduk = $pendapatanPerProduk->count();
            @endphp
            
            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 relative z-10">Total Pendapatan ({{ $periodText }})</h3>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 font-display relative z-10">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
                
                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-brand-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 relative z-10">Total Terjual</h3>
                    <p class="text-2xl sm:text-3xl font-extrabold text-brand-700 font-display relative z-10">{{ $totalTerjual }} <span class="text-sm text-slate-500 font-medium">kg / pack</span></p>
                </div>

                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 relative z-10">Produk Terjual</h3>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-700 font-display relative z-10">{{ $totalProduk }} <span class="text-sm text-slate-500 font-medium">varian</span></p>
                </div>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden mt-6">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Rincian per Produk</h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $pendapatanPerProduk->count() }} Produk</span>
        </div>
        
        @if($pendapatanPerProduk->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-2xl text-slate-300"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Belum Ada Pendapatan</h4>
                <p class="text-sm text-slate-500">Belum ada pendapatan dari produk pada periode {{ $periodText }}.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                            <th class="px-6 py-4 whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Terjual</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Total Pendapatan</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Sisa Stok</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendapatanPerProduk as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ $item->nama_produk }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {{ $item->total_terjual ?? 0 }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold">
                                        Rp {{ number_format($item->total_pendapatan ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    <span class="{{ $item->stok < 5 ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $item->stok }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('penjual.pendapatan.detail', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs rounded-lg transition">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
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
    function setPendapatanPeriod(periodName) {
        document.getElementById('filterInput').value = periodName;
        document.getElementById('filterStartDate').value = '';
        document.getElementById('filterEndDate').value = '';
        document.getElementById('pendapatanFilterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('flatpickrPendapatanCalendar');
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
                animate: false,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startStr = instance.formatDate(selectedDates[0], "Y-m-d");
                        const endStr = instance.formatDate(selectedDates[1], "Y-m-d");

                        document.getElementById('filterInput').value = 'custom';
                        document.getElementById('filterStartDate').value = startStr;
                        document.getElementById('filterEndDate').value = endStr;
                        document.getElementById('pendapatanFilterForm').submit();
                    } else if (selectedDates.length === 1) {
                        const singleStr = instance.formatDate(selectedDates[0], "Y-m-d");
                        document.getElementById('filterInput').value = 'custom';
                        document.getElementById('filterStartDate').value = singleStr;
                        document.getElementById('filterEndDate').value = singleStr;
                        document.getElementById('pendapatanFilterForm').submit();
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection

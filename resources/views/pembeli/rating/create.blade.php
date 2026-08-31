@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
            <i class="fas fa-star text-yellow-500"></i>
            Beri Ulasan Produk
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Bagikan pengalaman Anda tentang produk ini untuk membantu pembeli lain.
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Product Information -->
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-xl bg-white border border-gray-200 overflow-hidden flex-shrink-0 shadow-sm">
                @if($produk->gambar)
                    <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover" alt="{{ $produk->nama }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <i class="fas fa-box text-4xl"></i>
                    </div>
                @endif
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $produk->nama }}</h3>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-sm text-gray-600">
                    <span class="flex items-center justify-center sm:justify-start gap-2">
                        <i class="fas fa-receipt text-gray-400"></i>
                        Pesanan: <span class="font-medium text-gray-900">{{ $order->invoice ?? 'INV-' . $order->id }}</span>
                    </span>
                    <span class="hidden sm:inline text-gray-300">|</span>
                    <span class="flex items-center justify-center sm:justify-start gap-2">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                        Diterima: <span class="font-medium text-gray-900">{{ $order->updated_at->format('d M Y') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <form action="{{ route('pembeli.rating.store') }}" method="POST" class="p-6 sm:p-8">
            @csrf
            <input type="hidden" name="orders_id" value="{{ $order->id }}">
            <input type="hidden" name="produks_id" value="{{ $produk->id }}">

            <!-- Star Rating -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
                    Rating Produk
                </label>
                
                <div class="flex flex-col items-center sm:items-start">
                    <div class="flex flex-row-reverse justify-end gap-2 star-rating">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="bintang" value="{{ $i }}" class="peer hidden" {{ old('bintang') == $i ? 'checked' : '' }} required>
                            <label for="star{{ $i }}" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400 peer-hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">★</label>
                        @endfor
                    </div>
                    
                    <div id="ratingValue" class="mt-3 text-sm font-medium text-gray-600 flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-lg border border-gray-100">
                        <i class="fas fa-info-circle text-indigo-400"></i>
                        <span id="ratingText">Pilih rating dengan mengklik bintang di atas</span>
                    </div>
                </div>
                
                @error('bintang')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Review Text -->
            <div class="mb-8">
                <label for="ulasan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide flex justify-between">
                    <span>Ulasan Anda</span>
                    <span class="text-gray-400 font-normal text-xs normal-case"><span id="charCount">0</span> karakter</span>
                </label>
                <textarea id="ulasan" name="ulasan" rows="5" 
                    class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 border focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors sm:text-sm resize-y"
                    placeholder="Bagikan pengalaman Anda menggunakan produk ini. Apa yang Anda sukai? Apakah ada saran untuk perbaikan?" required>{{ old('ulasan') }}</textarea>
                
                @error('ulasan')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('pembeli.rating.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-gray-400"></i>
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* CSS logic for star rating hover effects using sibling selectors */
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #facc15 !important; /* text-yellow-400 */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const starInputs = document.querySelectorAll('.star-rating input');
        const starLabels = document.querySelectorAll('.star-rating label');
        const ratingText = document.getElementById('ratingText');
        const ratingValueContainer = document.getElementById('ratingValue');
        const textarea = document.getElementById('ulasan');
        const charCount = document.getElementById('charCount');

        // Update character count
        textarea.addEventListener('input', function () {
            charCount.textContent = this.value.length;
        });

        // Initialize character count
        charCount.textContent = textarea.value.length;

        // Star rating labels
        const ratingLabels = {
            1: 'Tidak Puas - Produk tidak sesuai harapan',
            2: 'Kurang Puas - Ada beberapa kekurangan',
            3: 'Cukup - Produk sesuai ekspektasi',
            4: 'Puas - Produk bagus dan memuaskan',
            5: 'Sangat Puas - Produk luar biasa dan melebihi ekspektasi'
        };

        function updateRatingText(value) {
            if (value) {
                ratingText.textContent = `${value}/5 - ${ratingLabels[value]}`;
                ratingValueContainer.classList.add('bg-indigo-50', 'text-indigo-700', 'border-indigo-100');
                ratingValueContainer.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-100');
                ratingValueContainer.querySelector('i').className = 'fas fa-star text-yellow-400';
            } else {
                ratingText.textContent = 'Pilih rating dengan mengklik bintang di atas';
                ratingValueContainer.classList.remove('bg-indigo-50', 'text-indigo-700', 'border-indigo-100');
                ratingValueContainer.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-100');
                ratingValueContainer.querySelector('i').className = 'fas fa-info-circle text-indigo-400';
            }
        }

        // Handle selection
        starInputs.forEach(star => {
            star.addEventListener('change', function () {
                updateRatingText(this.value);
            });
        });

        // Handle hover
        starLabels.forEach(label => {
            label.addEventListener('mouseenter', function() {
                const value = this.getAttribute('data-rating');
                ratingText.textContent = `${value}/5 - ${ratingLabels[value]}`;
                ratingValueContainer.querySelector('i').className = 'fas fa-star text-yellow-400';
            });
            
            label.addEventListener('mouseleave', function() {
                const checkedStar = document.querySelector('.star-rating input:checked');
                updateRatingText(checkedStar ? checkedStar.value : null);
            });
        });

        // Initialize with checked value if present
        const initialChecked = document.querySelector('.star-rating input:checked');
        if (initialChecked) {
            updateRatingText(initialChecked.value);
        }
    });
</script>
@endsection
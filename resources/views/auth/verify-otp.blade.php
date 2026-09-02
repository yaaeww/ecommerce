<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-cream">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode OTP — Juragan Pelem</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAFAF7;
        }
        .font-display {
            font-family: 'Outfit', sans-serif;
        }
        .bg-brand-green { background-color: #1B4D3E !important; }
        .bg-brand-green:hover { background-color: #143D31 !important; }
        .text-brand-green { color: #1B4D3E !important; }
        .text-brand-amber { color: #F59E0B !important; }
        .bg-brand-cream { background-color: #FAFAF7 !important; }
        .text-brand-slate { color: #0F172A !important; }

        .otp-input {
            width: 48px;
            height: 56px;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            border-radius: 12px;
            border: 2px solid #E2E8F0;
            background-color: #FFFFFF;
            transition: all 0.2s ease;
            outline: none;
        }
        .otp-input:focus {
            border-color: #1B4D3E;
            box-shadow: 0 0 0 3px rgba(27, 77, 62, 0.15);
            background-color: #F0FDF4;
        }
    </style>
</head>

<body class="h-full flex min-h-[100dvh]">
    <div class="flex-1 flex flex-col lg:flex-row min-h-full">
        
        <!-- Left Side: Brand Showcase Panel -->
        <div class="hidden lg:flex lg:w-1/2 p-8 xl:p-12 text-white flex-col justify-between gap-8 relative overflow-hidden" style="background: linear-gradient(135deg, #0d281e 0%, #1b4d3e 45%, #064e3b 100%) !important; color: #ffffff !important;">
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(52, 211, 153, 0.15);"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(245, 158, 11, 0.15);"></div>

            <!-- Top Header -->
            <div class="relative z-10">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-white shadow-sm p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                    </div>
                    <div>
                        <span class="text-2xl font-bold font-display text-white tracking-tight">Juragan<span class="text-brand-amber">Pelem</span></span>
                        <span class="block text-[10px] tracking-wider uppercase font-semibold text-emerald-200/80 -mt-1">Agro-Commerce Indramayu</span>
                    </div>
                </a>
            </div>

            <!-- Narrative -->
            <div class="space-y-5 relative z-10 max-w-lg my-auto py-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur border border-white/20 text-emerald-200 text-xs font-bold uppercase tracking-wider">
                    <i class="fas fa-shield-halved text-brand-amber"></i> Keamanan Akun 2 Langkah
                </div>

                <h1 class="text-3xl xl:text-4xl font-extrabold font-display leading-tight tracking-tight text-white">
                    Verifikasi Kode <br>
                    <span class="text-amber-300">OTP Resmi</span>.
                </h1>

                <p class="text-emerald-100/90 text-xs sm:text-sm leading-relaxed">
                    Demi keamanan akun Anda, kami telah mengirimkan 6 digit kode verifikasi rahasia ke email Anda. Masukkan kode tersebut untuk melanjutkan proses reset kata sandi.
                </p>

                <!-- Value Props -->
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur text-xs text-emerald-100 space-y-2">
                    <p class="font-bold text-white flex items-center gap-2">
                        <i class="fas fa-envelope-circle-check text-brand-amber"></i> Cek Kotak Masuk / Spam
                    </p>
                    <p class="text-[11px] text-emerald-200/80">Jika email belum muncul dalam 1 menit, periksa folder <em>Spam / Promosi</em> atau klik tombol kirim ulang kode.</p>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="p-4.5 rounded-2xl bg-white/10 border border-white/15 backdrop-blur relative z-10">
                <div class="text-[11px] text-emerald-100 leading-relaxed">
                    <i class="fas fa-lock text-brand-amber mr-1"></i> Jangan bagikan kode OTP ini kepada siapa pun untuk melindungi akun dan saldo transaksi Anda.
                </div>
            </div>
        </div>

        <!-- Right Side: OTP Input Form -->
        <div class="flex-1 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-brand-cream overflow-y-auto">
            
            <div class="flex items-center justify-between">
                <a href="{{ route('password.request') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-green transition">
                    <i class="fas fa-arrow-left"></i> Ganti Email
                </a>
                
                <a href="{{ route('landing') }}" class="flex lg:hidden items-center gap-2">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-8 w-auto">
                    <span class="font-display font-bold text-brand-green text-lg">Juragan<span class="text-brand-amber">Pelem</span></span>
                </a>
            </div>

            <div class="max-w-md w-full mx-auto my-auto py-6">
                
                <div class="mb-6 text-center sm:text-left">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-brand-green flex items-center justify-center text-xl mb-4 mx-auto sm:mx-0 shadow-sm">
                        <i class="fas fa-key"></i>
                    </div>
                    <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight font-display">
                        Masukkan Kode OTP
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                        Kode verifikasi 6 digit telah dikirimkan ke:<br>
                        <strong class="text-slate-800 font-bold">{{ $maskedEmail }}</strong>
                    </p>
                </div>

                <!-- Alert Status -->
                @if(session('status'))
                    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-brand-green rounded-2xl flex items-start gap-3 text-xs font-medium shadow-sm">
                        <i class="fas fa-circle-check text-base text-emerald-500 mt-0.5 shrink-0"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if(isset($devOtpHint) && $devOtpHint)
                    <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-laptop-code text-amber-600 text-base"></i>
                            <span>Kode OTP (Mode Uji Coba): <strong class="text-sm font-mono tracking-widest text-amber-800">{{ $devOtpHint }}</strong></span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-medium shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-circle-exclamation text-red-500"></i>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- OTP Form -->
                <form method="POST" action="{{ route('password.otp.verify') }}" id="otpForm" class="space-y-6">
                    @csrf

                    <!-- Hidden complete OTP string -->
                    <input type="hidden" name="otp" id="otpValue" value="">

                    <!-- 6 Digit Boxes -->
                    <div class="flex justify-center sm:justify-between items-center gap-2 sm:gap-3 py-2">
                        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" id="digit-1" autofocus autocomplete="off">
                        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" id="digit-2" autocomplete="off">
                        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" id="digit-3" autocomplete="off">
                        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" id="digit-4" autocomplete="off">
                        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" id="digit-5" autocomplete="off">
                        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" id="digit-6" autocomplete="off">
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full py-3.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold text-sm rounded-xl transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <i class="fas fa-shield-check"></i> Verifikasi Kode OTP
                    </button>
                </form>

                <!-- Resend Form & Countdown -->
                <div class="text-center mt-6 pt-5 border-t border-slate-200/80 space-y-2">
                    <p class="text-xs text-slate-500">
                        Tidak menerima kode OTP?
                    </p>
                    
                    <form method="POST" action="{{ route('password.otp.resend') }}" id="resendForm">
                        @csrf
                        <button 
                            type="submit" 
                            id="resendBtn" 
                            disabled
                            class="text-xs font-bold text-slate-400 disabled:cursor-not-allowed enabled:text-brand-green enabled:hover:underline transition"
                        >
                            Kirim Ulang Kode (<span id="timer">60</span>s)
                        </button>
                    </form>
                </div>

            </div>

            <div class="text-center text-[11px] text-slate-400 mt-4">
                &copy; {{ date('Y') }} Juragan Pelem Indramayu. Seluruh hak cipta dilindungi.
            </div>

        </div>

    </div>

    <!-- OTP Auto-Focus & Paste Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenOtp = document.getElementById('otpValue');
            const form = document.getElementById('otpForm');

            // Handle Input & Forward Focus
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const val = e.target.value.replace(/\D/g, '');
                    e.target.value = val ? val[0] : '';

                    if (val && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    updateHiddenOtp();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            // Handle Paste event on any box
            inputs[0].addEventListener('paste', handlePaste);
            inputs.forEach(input => input.addEventListener('paste', handlePaste));

            function handlePaste(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/\D/g, '');
                if (pasteData.length >= 6) {
                    for (let i = 0; i < 6; i++) {
                        inputs[i].value = pasteData[i] || '';
                    }
                    inputs[5].focus();
                    updateHiddenOtp();
                }
            }

            function updateHiddenOtp() {
                let code = '';
                inputs.forEach(inp => code += inp.value);
                hiddenOtp.value = code;
            }

            form.addEventListener('submit', (e) => {
                updateHiddenOtp();
                if (hiddenOtp.value.length !== 6) {
                    e.preventDefault();
                    alert('Silakan masukkan lengkap 6 digit kode OTP!');
                }
            });

            // Resend Countdown Timer (60s)
            let countdown = 60;
            const timerSpan = document.getElementById('timer');
            const resendBtn = document.getElementById('resendBtn');

            const interval = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    timerSpan.textContent = countdown;
                } else {
                    clearInterval(interval);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-rotate-right mr-1"></i> Kirim Ulang Kode Sekarang';
                }
            }, 1000);
        });
    </script>
</body>

</html>

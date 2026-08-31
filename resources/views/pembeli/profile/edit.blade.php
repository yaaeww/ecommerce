@extends('layouts.public')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
            <i class="fas fa-user-edit text-indigo-600"></i>
            Edit Profil
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Perbarui informasi profil dan preferensi akun Anda
        </p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center gap-3 text-green-700">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('pembeli.profile.update') }}" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-100">
            @csrf
            @method('PATCH')
            
            <div class="p-6 sm:p-8 space-y-8">
                <!-- Avatar Upload Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">
                        <i class="fas fa-image text-gray-400 mr-2"></i>Foto Profil
                    </label>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        @if ($user->avatar)
                            <div class="relative shrink-0">
                                <img id="avatar-preview-image" src="{{ asset('storage/' . $user->avatar) }}" 
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md ring-2 ring-indigo-100" 
                                     alt="Foto Profil Saat Ini">
                            </div>
                        @else
                            <div id="avatar-placeholder" class="w-24 h-24 shrink-0 rounded-full bg-indigo-50 border-4 border-white shadow-md ring-2 ring-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-3xl text-indigo-300"></i>
                            </div>
                        @endif

                        <div class="flex-grow w-full">
                            <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-indigo-500 hover:bg-indigo-50 transition-colors group cursor-pointer" id="drop-zone">
                                <input type="file" name="avatar" id="avatar" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                                <div class="text-center" id="upload-content">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-indigo-500 mb-2 transition-colors"></i>
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors" id="file-name-display">
                                        Klik untuk unggah atau seret file ke sini
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Info Section -->
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Nama Lengkap
                        </label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', $user->name) }}" 
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 border focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors sm:text-sm"
                               placeholder="Masukkan nama lengkap Anda" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>Alamat Email
                        </label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email', $user->email) }}" 
                               class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 border focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors sm:text-sm"
                               placeholder="Masukkan alamat email Anda" required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 sm:px-8">
                <a href="{{ route('pembeli.profile.show') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('avatar');
        const dropZone = document.getElementById('drop-zone');
        const fileNameDisplay = document.getElementById('file-name-display');
        const form = document.querySelector('form');
        const previewImage = document.getElementById('avatar-preview-image');
        
        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            handleFiles(this.files);
        });
        
        // Handle drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
            dropZone.classList.remove('border-gray-300');
        }
        
        function unhighlight(e) {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            dropZone.classList.add('border-gray-300');
        }
        
        dropZone.addEventListener('drop', function(e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            
            // Assign files to the input element
            fileInput.files = files;
            handleFiles(files);
        });
        
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                const fileSize = file.size / 1024 / 1024; // MB
                
                if (fileSize > 2) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    fileInput.value = '';
                    fileNameDisplay.textContent = 'Klik untuk unggah atau seret file ke sini';
                    return;
                }
                
                fileNameDisplay.textContent = 'File dipilih: ' + file.name;
                dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
                
                // Read and show preview if image
                if (file.type.startsWith('image/') && previewImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('image/')) {
                    // Create preview if it doesn't exist
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const placeholder = document.getElementById('avatar-placeholder');
                        if (placeholder) {
                            const newImage = document.createElement('div');
                            newImage.className = 'relative shrink-0';
                            newImage.innerHTML = `<img id="avatar-preview-image" src="${e.target.result}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md ring-2 ring-indigo-100" alt="Preview">`;
                            placeholder.parentNode.replaceChild(newImage, placeholder);
                        }
                    }
                    reader.readAsDataURL(file);
                }
            } else {
                fileNameDisplay.textContent = 'Klik untuk unggah atau seret file ke sini';
                dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            }
        }
        
        // Final validation before submit
        form.addEventListener('submit', function(e) {
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 2) {
                    e.preventDefault();
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                }
            }
        });
    });
</script>
@endsection
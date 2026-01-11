<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pementor - BOM-PAI UNISBA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-brand-mist text-brand-ink">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 p-4" x-data="{ showSuccessModal: {{ session()->has('success') ? 'true' : 'false' }} }">
        <div class="w-full max-w-4xl px-6 py-12 bg-white shadow-md overflow-hidden sm:rounded-lg">
            
            <div x-show="!showSuccessModal">
                <div class="flex flex-col items-center mb-8">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-ink text-white backdrop-blur mb-4">
                        <img src="/images/bompai.png" alt="Logo" class="h-14 w-14 object-contain">
                    </div>
                    <h2 class="text-3xl font-heading font-semibold text-center">Formulir Pendaftaran Pementor</h2>
                    <p class="text-slate-600 mt-2 text-center">Bergabunglah bersama kami untuk membina kepribadian Islami mahasiswa UNISBA.</p>
                </div>

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('mentor.register.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Form fields remain the same -->
                         <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- NPM -->
                    <div>
                        <x-input-label for="npm" :value="__('NPM (Nomor Pokok Mahasiswa)')" />
                        <x-text-input id="npm" class="block mt-1 w-full" type="text" name="npm" :value="old('npm')" required />
                        <x-input-error :messages="$errors->get('npm')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Faculty -->
                    <div>
                        <x-input-label for="faculty_id" :value="__('Fakultas')" />
                        <x-select-input id="faculty_id" name="faculty_id" class="mt-1 block w-full" required>
                            <option value="">Pilih Fakultas</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
                    </div>

                     <!-- Gender -->
                    <div>
                        <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                        <x-select-input id="gender" name="gender" class="mt-1 block w-full" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki (Ikhwan)</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan (Akhwat)</option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div></div> <!-- Spacer -->

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    </div>

                    <!-- BTAQ History -->
                    <div class="md:col-span-2">
                        <x-input-label for="btaq_history" :value="__('Riwayat BTAQ (Baca Tulis Al-Quran)')" />
                        <x-textarea-input id="btaq_history" name="btaq_history" class="mt-1 block w-full" rows="4" required>{{ old('btaq_history') }}</x-textarea-input>
                        <p class="text-sm text-slate-500 mt-1">Jelaskan pengalaman Anda dalam belajar atau mengajar Al-Quran.</p>
                        <x-input-error :messages="$errors->get('btaq_history')" class="mt-2" />
                    </div>

                    <!-- CV -->
                    <div class="md:col-span-2">
                        <x-input-label for="cv" :value="__('Upload CV (PDF, max: 2MB)')" />
                        <input id="cv" type="file" name="cv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-sky/20 file:text-brand-teal hover:file:bg-brand-sky/30 mt-1" required>
                        <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                    </div>

                    <!-- Recording -->
                    <div class="md:col-span-2">
                        <x-input-label for="recording" :value="__('Upload Rekaman Bacaan (MP3, WAV, M4A, max: 10MB)')" />
                        <input id="recording" type="file" name="recording" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-sky/20 file:text-brand-teal hover:file:bg-brand-sky/30 mt-1" required>
                         <p class="text-sm text-slate-500 mt-1">Silakan rekam bacaan QS. Al-Baqarah ayat 1-5.</p>
                        <x-input-error :messages="$errors->get('recording')" class="mt-2" />
                    </div>
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                            {{ __('Sudah punya akun?') }}
                        </a>

                        <x-primary-button class="ml-4">
                            {{ __('Daftar Sebagai Pementor') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Success Modal -->
            <div x-show="showSuccessModal" style="display: none;" class="text-center">
                 <div class="flex flex-col items-center mb-8">
                    <svg class="h-16 w-16 text-green-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-3xl font-heading font-semibold text-center">Pendaftaran Berhasil!</h2>
                    <p class="text-slate-600 mt-2 text-center max-w-md">
                        Pendaftaran Anda telah kami terima. Aplikasi Anda akan kami review terlebih dahulu. Silakan menunggu, Anda akan diinformasikan melalui email jika aplikasi Anda disetujui.
                    </p>
                    <div class="mt-8">
                        <a href="{{ url('/') }}" class="rounded-md bg-brand-teal px-6 py-3 text-base font-semibold text-white shadow-lg transition hover:brightness-90">
                            Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>

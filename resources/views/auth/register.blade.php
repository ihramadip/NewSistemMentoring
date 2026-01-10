<x-guest-layout>
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <!-- Logo -->
        <a href="/" class="flex items-center justify-center gap-3 mb-8">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100">
                <img src="/images/bompai.png" alt="Logo" class="h-10 w-10 object-contain">
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-black/60">BOM-PAI UNISBA</p>
                <h1 class="font-heading text-lg font-semibold text-brand-ink">Daftar Akun Baru</h1>
            </div>
        </a>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" value="Alamat Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center bg-brand-teal shadow-lg shadow-brand-teal/30 transition hover:brightness-90">
                    Daftar
                </x-primary-button>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-teal hover:underline">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>

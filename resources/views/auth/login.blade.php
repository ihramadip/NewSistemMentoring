<x-guest-layout>
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <!-- Logo -->
        <a href="/" class="flex items-center justify-center gap-3 mb-8">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100">
                <img src="/images/bompai.png" alt="Logo" class="h-10 w-10 object-contain">
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-black/60">BOM-PAI UNISBA</p>
                <h1 class="font-heading text-lg font-semibold text-brand-ink">Portal Mentoring</h1>
            </div>
        </a>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Alamat Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-teal shadow-sm focus:ring-brand-teal" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-teal" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center bg-brand-teal shadow-lg shadow-brand-teal/30 transition hover:brightness-90">
                    Masuk
                </x-primary-button>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-brand-teal hover:underline">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>

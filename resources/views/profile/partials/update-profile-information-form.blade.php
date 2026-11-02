<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>
        
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>
    
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}
                        
                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            
            @if (session('status') === 'profile-updated')
                <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
    
    <!-- ========================================== -->
    <!-- NOWA SEKCJA: GENEROWANIE RAPORTU SPRZEDAŻY -->
    <!-- ========================================== -->
    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <header>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <span>🍺</span> {{ __('Raport Sprzedaży Piwa') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Wgraj wyeksportowany plik CSV, aby przejść do interaktywnej prezentacji z wykresami.') }}
            </p>
        </header>
        
        <form method="post" action="{{ route('beer.report.upload') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            
            <div>
                <x-input-label for="csv_file" :value="__('Wybierz plik CSV ze sprzedażą')" />
                <input id="csv_file"
                       name="csv_file"
                       type="file"
                       accept=".csv,.txt"
                       required
                       class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-xs file:font-semibold
                              file:bg-amber-500 file:text-white
                              hover:file:bg-amber-600
                              cursor-pointer focus:outline-none dark:file:bg-amber-600 dark:hover:file:bg-amber-500" />
                <x-input-error class="mt-2" :messages="$errors->get('csv_file')" />
            </div>
            
            <div class="flex items-center gap-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    📊 {{ __('Generuj Raport Piwa') }}
                </button>
            </div>
        </form>
    </div>
</section>
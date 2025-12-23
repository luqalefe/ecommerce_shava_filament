<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                Salvar Alterações
            </x-filament::button>
        </div>
    </form>

    {{-- Preview Section --}}
    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            📺 Preview Atual
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Logo Preview --}}
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Logo Principal</p>
                @php
                    $logoPath = \App\Models\SiteSetting::get('logo');
                @endphp
                @if($logoPath)
                    <img 
                        src="{{ str_starts_with($logoPath, 'site-settings/') ? asset('storage/' . $logoPath) : asset($logoPath) }}" 
                        alt="Logo" 
                        class="h-16 mx-auto object-contain bg-white dark:bg-gray-800 p-2 rounded-lg"
                    >
                @else
                    <div class="h-16 flex items-center justify-center text-gray-400">
                        Sem logo
                    </div>
                @endif
            </div>

            {{-- Video Desktop Preview --}}
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Vídeo Desktop</p>
                @php
                    $videoDesktop = \App\Models\SiteSetting::get('hero_video_desktop');
                @endphp
                @if($videoDesktop)
                    <video 
                        src="{{ str_starts_with($videoDesktop, 'site-settings/') ? asset('storage/' . $videoDesktop) : asset($videoDesktop) }}" 
                        class="w-full h-24 object-cover rounded-lg"
                        muted
                        autoplay
                        loop
                        playsinline
                    ></video>
                @else
                    <div class="h-24 flex items-center justify-center text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-lg">
                        Sem vídeo
                    </div>
                @endif
            </div>

            {{-- Video Mobile Preview --}}
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Vídeo Mobile</p>
                @php
                    $videoMobile = \App\Models\SiteSetting::get('hero_video_mobile');
                @endphp
                @if($videoMobile)
                    <video 
                        src="{{ str_starts_with($videoMobile, 'site-settings/') ? asset('storage/' . $videoMobile) : asset($videoMobile) }}" 
                        class="w-full h-24 object-cover rounded-lg"
                        muted
                        autoplay
                        loop
                        playsinline
                    ></video>
                @else
                    <div class="h-24 flex items-center justify-center text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-lg">
                        Sem vídeo
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>

{{-- resources/views/layouts/partials/footer.blade.php --}}
{{-- REFATORADO para Organic Light Theme --}}
<footer class="relative overflow-hidden bg-[#F5F5F4] border-t border-[#E7E5E4]" style="color: #44403C;">
    
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        
        {{-- Grid principal --}}
        <div class="grid grid-cols-12 gap-8">

            {{-- Coluna 1: Logo e Descrição (Mobile: Full, Medium: Full, Large: 1/3) --}}
            <div class="col-span-12 lg:col-span-4 text-center lg:text-left">
                @php
                    $logoPath = \App\Models\SiteSetting::get('logo_footer') ?: \App\Models\SiteSetting::get('logo');
                    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('images/logo_shava.png');
                @endphp
                <img src="{{ $logoUrl }}" alt="Logo Shava Haux" class="h-12 mx-auto lg:mx-0 mb-4 opacity-90">
                <p class="text-sm text-[var(--sh-text-light)] leading-relaxed">Expandindo a consciência através de aromas e sensações.</p>
            </div>

            {{-- Coluna 2: Navegação (Mobile: 1/2, Medium: 1/3, Large: 1/6) --}}
            <div class="col-span-6 md:col-span-4 lg:col-span-2">
                <h6 class="font-semibold text-sm uppercase tracking-wider text-[var(--sh-dark-text)] mb-4">Navegue</h6>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">Início</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">Produtos</a></li>
                    <li><a href="{{ route('about') }}" class="text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">Sobre Nós</a></li>
                </ul>
            </div>

            {{-- Coluna 3: Suporte (Mobile: 1/2, Medium: 1/3, Large: 1/4) --}}
            <div class="col-span-6 md:col-span-4 lg:col-span-3">
                <h6 class="font-semibold text-sm uppercase tracking-wider text-[var(--sh-dark-text)] mb-4">Suporte</h6>
                <ul class="space-y-3">
                    <li><a href="{{ route('privacy-policy') }}" class="text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">Política de Privacidade</a></li>
                    <li><a href="{{ route('terms-of-use') }}" class="text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">Termos de Uso</a></li>
                </ul>
            </div>

            {{-- Coluna 4: Contato (Mobile: Full, Medium: 1/3, Large: 1/4) --}}
            <div class="col-span-12 md:col-span-4 lg:col-span-3">
                <h6 class="font-semibold text-sm uppercase tracking-wider text-[var(--sh-dark-text)] mb-4">Contato</h6>
                <ul class="space-y-3">
                    {{-- Ícone WhatsApp (SVG) --}}
                    <li>
                        <a href="https://wa.me/5568999028113" target="_blank" class="flex items-center text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M19.05 4.91A9.816 9.816 0 0012.04 2C6.54 2 2 6.53 2 12.02c0 1.74.45 3.42 1.27 4.91L2 22l5.05-1.29c1.47.78 3.12 1.21 4.84 1.21h.01c5.49 0 9.98-4.48 9.98-9.97 0-2.73-1.1-5.21-2.88-7.01zm-7.01 15.26c-1.5 0-2.96-.4-4.23-1.15l-.3-.18-3.12.8.82-3.05-.2-.31a8.2 8.2 0 01-1.26-4.38c0-4.54 3.7-8.23 8.26-8.23 2.22 0 4.28.86 5.84 2.42 1.56 1.56 2.42 3.61 2.42 5.83.02 4.54-3.68 8.23-8.23 8.23zm4.4-6.81c-.22-.11-1.3-.64-1.5-.72-.2-.07-.35-.11-.49.11-.15.22-.57.72-.7 1.07-.13.35-.27.4-.49.34-.22-.06-1.02-.37-1.94-1.2-.72-.65-1.2-1.45-1.34-1.7-.14-.25-.01-.38.1-.5.1-.11.22-.28.33-.42.11-.14.15-.25.22-.41.07-.17.04-.31-.01-.42-.05-.11-.49-1.18-.68-1.62-.18-.43-.36-.37-.49-.37h-.45c-.14 0-.35.04-.54.22-.19.18-.73.7-1.14 1.73-.41 1.03-.41 1.9.05 2.99.46 1.09 1.57 2.7 3.82 4.02l.46.26c2.09 1.16 2.45 1.3 2.86 1.4.6.14 1.13.13 1.54-.08.45-.24 1.3-.64 1.48-1.25.18-.61.18-1.13.13-1.25-.05-.12-.19-.18-.41-.29z"></path>
                            </svg>
                            (68) 99902-8113
                        </a>
                    </li>
                    {{-- Ícone Instagram (SVG) --}}
                    <li>
                        <a href="#" target="_blank" class="flex items-center text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                               <path fill-rule="evenodd" d="M12.315 2.064c-3.14 0-3.518.012-4.743.068-1.224.056-2.07.248-2.796.521a3.86 3.86 0 00-1.42 1.06C2.28 4.79 2.088 5.637 2.032 6.862c-.056 1.225-.068 1.603-.068 4.743s.012 3.518.068 4.743c.056 1.225.257 2.07.521 2.796a3.86 3.86 0 001.06 1.42c.677.41 1.57.61 2.796.668 1.225.056 1.603.068 4.743.068s3.518-.012 4.743-.068c1.225-.058 2.07-.257 2.796-.668a3.86 3.86 0 001.42-1.06c.41-.677.61-1.57.668-2.796.056-1.225.068-1.603.068-4.743s-.012-3.518-.068-4.743c-.058-1.225-.257-2.07-.668-2.796a3.86 3.86 0 00-1.06-1.42c-.677-.41-1.57-.61-2.796-.521-1.225-.056-1.603-.068-4.743-.068zm0 1.8c3.063 0 3.425.012 4.63.067 1.095.05 1.67.24 2.112.42a2.062 2.062 0 01.918.918c.18.442.37 1.017.42 2.112.055 1.205.067 1.567.067 4.63s-.012 3.425-.067 4.63c-.05 1.095-.24 1.67-.42 2.112a2.062 2.062 0 01-.918.918c-.442.18-1.017.37-2.112.42-1.205.055-1.567.067-4.63.067s-3.425-.012-4.63-.067c-1.095-.05-1.67-.24-2.112-.42a2.062 2.062 0 01-.918-.918c-.18-.442-.37-1.017-.42-2.112-.055-1.205-.067-1.567-.067-4.63s.012-3.425.067-4.63c.05-1.095.24-1.67.42-2.112a2.062 2.062 0 01.918-.918c.442-.18 1.017-.37 2.112-.42 1.205-.055 1.567-.067 4.63-.067zm0 2.903a5.232 5.232 0 100 10.464 5.232 5.232 0 000-10.464zM12 15.4a3.4 3.4 0 110-6.8 3.4 3.4 0 010 6.8zm4.33-8.08a1.237 1.237 0 100 2.474 1.237 1.237 0 000-2.474z" clip-rule="evenodd" />
                            </svg>
                            Instagram
                        </a>
                    </li>
                    {{-- Ícone Email (SVG) --}}
                    <li>
                        <a href="mailto:shavahaux@gmail.com" class="flex items-center text-[var(--sh-text-light)] hover:text-[var(--sh-muted-gold)] transition-colors text-sm">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 5-8-5h16zm0 12H4V8l8 5 8-5v10z"></path>
                            </svg>
                            shavahaux@gmail.com
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Barra Inferior --}}
        <div class="mt-12 border-t border-[var(--sh-border)] pt-8 text-center">
            <p class="text-sm text-[var(--sh-text-light)]">&copy; {{ date('Y') }} Shava Haux. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
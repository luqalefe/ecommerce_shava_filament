<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteAppearance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = 'Aparência do Site';
    protected static ?string $title = 'Configurações de Aparência';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.site-appearance';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'logo' => SiteSetting::get('logo'),
            'logo_footer' => SiteSetting::get('logo_footer'),
            'favicon' => SiteSetting::get('favicon'),
            'hero_video_desktop' => SiteSetting::get('hero_video_desktop'),
            'hero_video_mobile' => SiteSetting::get('hero_video_mobile'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identidade Visual')
                    ->description('Gerencie o logo e favicon do site')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo Principal')
                            ->helperText('Exibido no cabeçalho do site. Recomendado: PNG com fundo transparente.')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('site-settings')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml']),

                        FileUpload::make('logo_footer')
                            ->label('Logo Footer')
                            ->helperText('Exibido no rodapé. Deixe vazio para usar o mesmo do cabeçalho.')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('site-settings')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml']),

                        FileUpload::make('favicon')
                            ->label('Favicon')
                            ->helperText('Ícone da aba do navegador. Recomendado: 32x32px ou 64x64px.')
                            ->image()
                            ->disk('public')
                            ->directory('site-settings')
                            ->visibility('public')
                            ->maxSize(512)
                            ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/ico']),
                    ])
                    ->columns(3),

                Section::make('Vídeos do Hero/Banner')
                    ->description('Vídeos de fundo exibidos na página inicial')
                    ->icon('heroicon-o-video-camera')
                    ->schema([
                        FileUpload::make('hero_video_desktop')
                            ->label('Vídeo Desktop')
                            ->helperText('Exibido em telas maiores que 768px. Formato: WebM ou MP4.')
                            ->disk('public')
                            ->directory('site-settings')
                            ->visibility('public')
                            ->maxSize(51200) // 50MB
                            ->acceptedFileTypes(['video/webm', 'video/mp4']),

                        FileUpload::make('hero_video_mobile')
                            ->label('Vídeo Mobile')
                            ->helperText('Exibido em telas menores que 768px. Formato: WebM ou MP4.')
                            ->disk('public')
                            ->directory('site-settings')
                            ->visibility('public')
                            ->maxSize(30720) // 30MB
                            ->acceptedFileTypes(['video/webm', 'video/mp4']),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Se é array (FileUpload retorna array), pega o primeiro valor
            if (is_array($value)) {
                $value = !empty($value) ? reset($value) : null;
            }

            // Só atualiza se o valor não for nulo
            if ($value !== null) {
                SiteSetting::set($key, $value);
            }
        }

        // Limpa cache
        SiteSetting::clearCache();

        Notification::make()
            ->title('Configurações salvas!')
            ->body('As alterações de aparência foram aplicadas ao site.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Salvar Alterações')
                ->submit('save'),
        ];
    }
}

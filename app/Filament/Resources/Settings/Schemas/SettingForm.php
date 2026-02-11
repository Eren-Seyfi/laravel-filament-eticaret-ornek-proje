<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Enums\SocialPlatform;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        $imageCropHelperText =
            'Kırpma oranları: '
            . '- Serbest (free crop) '
            . '- 21:9 (çok geniş yatay / hero) '
            . '- 16:9 (standart yatay / banner) '
            . '- 4:3 (klasik yatay) '
            . '- 4:1 (ince yatay şerit) '
            . '- 9:16 (dikey / story) '
            . '- 3:4 (dikey klasik / poster) '
            . '- 1:2 (ince dikey şerit) '
            . '- 1:1 (kare) '
            . '- 4:5 (afiş / Instagram)';

        $aspectRatios = [
            '21:9',  // Çok geniş (Hero / Üst bant)
            '16:9',  // Standart yatay
            '4:3',   // Klasik yatay
            '4:1',   // İnce yatay bant
            '9:16',  // Dikey (Story / Reel)
            '3:4',   // Dikey klasik
            '1:2',   // İnce dikey
            '1:1',   // Kare
            '4:5',   // Afiş / Instagram
            null,    // Serbest
        ];

        return $schema->components([
            Section::make('Site Ayarları')
                ->description('Sitenin temel kimliği, medya ve SEO ayarları.')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->schema([
                    TextInput::make('site_name')
                        ->label('Site Adı')
                        ->helperText('Sitenin görünen adıdır. Header, tarayıcı sekmesi ve bazı SEO alanlarında kullanılır.')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('site_tagline')
                        ->label('Slogan')
                        ->helperText('Kısa açıklama / slogan. Ana sayfa veya footer’da gösterilebilir.')
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpanFull(),

                    Grid::make()
                        ->columns(3)
                        ->schema([
                            FileUpload::make('logo_path')
                                ->label('Logo')
                                ->helperText($imageCropHelperText)
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatioOptions($aspectRatios)
                                ->disk('public_root')
                                ->directory('images/settings')
                                ->visibility('public')
                                ->maxSize(5120)
                                ->nullable(),

                            FileUpload::make('favicon_path')
                                ->label('Favicon')
                                ->helperText('Favicon için öneri: 1:1 (kare) veya serbest. Genelde küçük boyut kullanılır (32x32 / 48x48).')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatioOptions([
                                    '1:1',
                                    null, // serbest
                                ])
                                ->disk('public_root')
                                ->directory('images/settings')
                                ->visibility('public')
                                ->maxSize(1024)
                                ->nullable(),

                            FileUpload::make('og_image_path')
                                ->label('OG Görsel')
                                ->helperText('Link paylaşımlarında görünen önizleme görseli. Öneri: 1200x630 (yaklaşık 1.91:1). Filament’te en sorunsuz oran: 16:9 veya serbest.')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatioOptions([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                    null, // serbest
                                ])
                                ->disk('public_root')
                                ->directory('images/settings')
                                ->visibility('public')
                                ->maxSize(5120)
                                ->nullable(),

                        ])
                        ->columnSpanFull(),

                    TextInput::make('seo_title')
                        ->label('SEO Başlık')
                        ->helperText('Arama sonuçlarında görünen başlık (title). Boşsa genelde site adı kullanılır.')
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpanFull(),

                    Textarea::make('seo_description')
                        ->label('SEO Açıklama')
                        ->helperText('Meta description. 150–160 karakter civarı idealdir.')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),

                    TextInput::make('seo_keywords')
                        ->label('SEO Anahtar Kelimeler (virgülle)')
                        ->helperText('Virgülle ayır. Etkisi az ama bazı sistemlerde kullanılabilir.')
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpanFull(),

                    TextInput::make('contact_email')
                        ->label('E-Posta')
                        ->helperText('Footer/iletişim sayfasında gösterilebilir. mailto linkinde kullanılır.')
                        ->email()
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpanFull(),

                    TextInput::make('contact_phone')
                        ->label('Telefon')
                        ->helperText('Mobilde tel: linki ile arama başlatmak için kullanılabilir.')
                        ->tel()
                        ->maxLength(50)
                        ->nullable()
                        ->columnSpanFull(),

                    TextInput::make('contact_address')
                        ->label('Adres')
                        ->helperText('Firma / site adresi. Footer veya iletişim sayfasında gösterilebilir.')
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpanFull(),

                    Repeater::make('social_links')
                        ->label('Sosyal Linkler')
                        ->helperText('Sosyal hesaplarını seçip link ekle. Platform seçince ikon otomatik belirlenir ve DB’ye kaydolur.')
                        ->schema([
                            Select::make('platform')
                                ->label('Platform')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options(SocialPlatform::options())
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // ✅ platform seçilince icon otomatik set edilir (DB’ye de kaydolacak)
                                    $set('icon', SocialPlatform::iconFor($state));
                                })
                                ->helperText(function ($get) {
                                    $platform = $get('platform');
                                    $enum = $platform ? SocialPlatform::tryFrom($platform) : null;

                                    if (!$enum) {
                                        return 'Platform seçince ikon otomatik belirlenecek.';
                                    }

                                    return 'İkon: ' . $enum->label() . ' (' . $enum->iconClass() . ')';
                                }),

                            TextInput::make('url')
                                ->label('URL')
                                ->helperText('Örn: https://instagram.com/kullaniciadi')
                                ->url()
                                ->required()
                                ->maxLength(2048),

                            // ✅ icon DB’de saklanacak alan
                            Hidden::make('icon')
                                ->dehydrated(true) // kaydet
                                ->default(null),
                        ])
                        ->columns(2)
                        ->default([])
                        ->itemLabel(function (array $state): ?string {
                            $platform = $state['platform'] ?? null;
                            $enum = $platform ? SocialPlatform::tryFrom($platform) : null;
                            return $enum?->label() ?? ($platform ?: 'Sosyal Link');
                        })
                        ->columnSpanFull(),


                ])
                ->columnSpanFull(),

            Section::make('Ekstra Ayarlar')
                ->description('İleri seviye ayarlar: robots.txt, indexleme, özel scriptler ve bakım modu. Genelde dokunmana gerek yoktur.')
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->collapsed()
                ->schema([
                    Toggle::make('search_engine_indexing')
                        ->label('Arama motorları indexlesin mi?')
                        ->helperText('Kapalıysa genelde noindex/nofollow ve robots.txt Disallow: / kullanılır (dev/kapalı beta için).')
                        ->default(true)
                        ->columnSpanFull(),

                    Toggle::make('robots_txt_enabled')
                        ->label('robots.txt aktif olsun mu?')
                        ->helperText('Açıksa /robots.txt servis edilir ve botlara tarama kuralları verilir.')
                        ->default(true)
                        ->columnSpanFull(),

                    Toggle::make('robots_txt_custom')
                        ->label('robots.txt içeriğini ben yazacağım (custom)')
                        ->helperText('Açıksa robots.txt içeriğini aşağıdaki alana kendin yazarsın.')
                        ->default(false)
                        ->live()
                        ->columnSpanFull(),

                    Textarea::make('robots_txt_content')
                        ->label('robots.txt İçeriği')
                        ->helperText('Custom açıksa buraya direkt robots.txt metnini yaz.')
                        ->rows(10)
                        ->placeholder("User-agent: *\nDisallow:\n\nSitemap: https://site.com/sitemap.xml")
                        ->nullable()
                        ->hidden(fn($get) => !(bool) $get('robots_txt_custom'))
                        ->columnSpanFull(),

                    TextInput::make('sitemap_url')
                        ->label('Sitemap URL')
                        ->helperText('robots.txt içine sitemap satırı eklemek için. Örn: https://site.com/sitemap.xml')
                        ->placeholder('https://site.com/sitemap.xml')
                        ->url()
                        ->maxLength(2048)
                        ->nullable()
                        ->hidden()
                        ->columnSpanFull(),

                    Textarea::make('header_scripts')
                        ->label('Header Scripts')
                        ->helperText('HEAD içine eklenecek kodlar (GA, Pixel, doğrulama etiketleri). Yanlış kod siteyi bozabilir.')
                        ->rows(6)
                        ->nullable()
                        ->columnSpanFull(),

                    Textarea::make('footer_scripts')
                        ->label('Footer Scripts')
                        ->helperText('Body kapanışından önce eklenecek scriptler. Performans için genelde burası tercih edilir.')
                        ->rows(6)
                        ->nullable()
                        ->columnSpanFull(),

                    Toggle::make('maintenance_mode')
                        ->label('Bakım Modu')
                        ->helperText('Açıksa site bakımda sayfası gösterebilir (senin uygulama mantığına göre).')
                        ->default(false)
                        ->columnSpanFull(),

                    RichEditor::make('maintenance_message')
                        ->label('Bakım Mesajı')
                        ->helperText('Bakım modunda gösterilecek mesaj. Görsel/ek dosya eklenebilir. JSON saklanır, güvenli render edilir.')
                        ->json()
                        ->fileAttachmentsDisk('public_root')
                        ->fileAttachmentsDirectory('attachments/maintenance')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['table', 'attachFiles'],
                            ['undo', 'redo'],
                        ])
                        ->columnSpanFull()
                        ->nullable(),

                ])
                ->columnSpanFull(),
        ]);
    }
}

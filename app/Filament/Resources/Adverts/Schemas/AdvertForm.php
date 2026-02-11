<?php

namespace App\Filament\Resources\Adverts\Schemas;

use App\Enums\AdvertPlacement;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;



class AdvertForm
{
    private static function onForeverUpdated(?bool $state, $set): void
    {
        // ✅ Süresiz açıldıysa: bitişi otomatik null yap
        if ($state) {
            $set('ends_at', null);
        }
    }

    private static function isForever($get): bool
    {
        return (bool) ($get('is_forever') ?? true);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Temel Bilgiler')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(255),

                    Select::make('placement')
                        ->label('Konum')
                        ->required()
                        ->searchable()
                        ->options(AdvertPlacement::options()),

                    Textarea::make('content')
                        ->label('İçerik')
                        ->rows(6)
                        ->nullable()
                        ->columnSpanFull(),

                    TextInput::make('external_url')
                        ->label('Yönlendirilecek Site')
                        ->url()
                        ->maxLength(2048)
                        ->nullable()
                        ->suffixIcon(Heroicon::GlobeAlt)
                        ->columnSpanFull(),
                ]),

            Section::make('Görünürlük / Geçerlilik')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),

                    Toggle::make('is_forever')
                        ->label('Süresiz Yayında')
                        ->default(true)
                        ->live()
                        ->afterStateUpdated(fn(?bool $state, $set) => self::onForeverUpdated($state, $set)),

                    DateTimePicker::make('starts_at')
                        ->label('Başlangıç')
                        ->seconds(false)
                        ->nullable(),

                    DateTimePicker::make('ends_at')
                        ->label('Bitiş')
                        ->seconds(false)
                        ->nullable()
                        ->afterOrEqual('starts_at')
                        ->disabled(fn($get) => self::isForever($get))
                        ->dehydrated(fn($get) => !self::isForever($get)),

                    TextInput::make('priority')
                        ->label('Öncelik')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                ]),

            Section::make('Medya')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    FileUpload::make('image')
                        ->label('Reklam Görselleri (Carousel)')
                        ->multiple()
                        ->reorderable()      // sıralama: carousel sırası
                        ->appendFiles()      // yeni eklenenler üstüne eklenir
                        ->helperText(
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
                            . '- 4:5 (afiş / Instagram)'
                        )
                        ->image()
                        ->imageEditor()
                        ->disk('public_root')
                        ->directory('images/adverts')
                        ->visibility('public')
                        ->maxSize(10240) // 10MB
                        ->imageEditorAspectRatioOptions([
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
                        ])
                        ->nullable()
                        ->columnSpanFull(),

                    FileUpload::make('video')
                        ->label('Video')
                        ->hidden()
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                        ->disk('public_root')
                        ->directory('videos/adverts')
                        ->visibility('public')
                        ->maxSize(307200) // 300MB
                        ->nullable(),
                ])

        ]);
    }
}

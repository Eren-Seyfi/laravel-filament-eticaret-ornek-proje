<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\CurrencyCode;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductsForm
{
    private static function onAvailabilityForeverUpdated(?bool $state, $set): void
    {
        // ✅ Sürekli açıldıysa: bitişi otomatik null yap
        if ($state) {
            $set('availability_ends_at', null);
        }
    }

    private static function isAvailabilityForever($get): bool
    {
        return (bool) ($get('availability_forever') ?? true);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Temel Bilgiler')
                ->columnSpanFull()
                ->columns(2)
                ->schema([


                    Select::make('category_id')
                        ->label('Kategori')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(
                            fn() => Category::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all()
                        ),

                    TextInput::make('name')
                        ->label('Ürün Adı')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Açıklama')
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

            Section::make('Fiyatlandırma')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('price')
                        ->label('Fiyat')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->step('0.01')
                        ->default(0),

                    Select::make('currency')
                        ->label('Para Birimi')
                        ->required()
                        ->options(self::currencyOptions())
                        ->default(CurrencyCode::TRY ->value),
                ]),

            Section::make('Görünürlük / Geçerlilik')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),

                    Toggle::make('availability_forever')
                        ->label('Sürekli Yayında')
                        ->default(true)
                        ->live()
                        ->afterStateUpdated(fn(?bool $state, $set) => self::onAvailabilityForeverUpdated($state, $set)),

                    DateTimePicker::make('availability_starts_at')
                        ->label('Başlangıç')
                        ->seconds(false)
                        ->nullable(),

                    DateTimePicker::make('availability_ends_at')
                        ->label('Bitiş')
                        ->seconds(false)
                        ->nullable()
                        ->afterOrEqual('availability_starts_at')
                        ->disabled(fn($get) => self::isAvailabilityForever($get))
                        ->dehydrated(fn($get) => !self::isAvailabilityForever($get)),

                    Select::make('rating_stars')
                        ->label('Puan (0-5)')
                        ->options([
                            0 => '0',
                            1 => '1',
                            2 => '2',
                            3 => '3',
                            4 => '4',
                            5 => '5',
                        ])
                        ->default(0)
                        ->required(),

                    TextInput::make('sort_order')
                        ->label('Sıra')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ]),

            Section::make('Medya')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    FileUpload::make('image')
                        ->label('Ürün Görseli')
                        ->helperText(
                            'Öneri: Ürünlerde en iyi sonuç için 1:1 (kare) kullan. '
                            . '- 1:1 (standart ürün kartı) '
                            . '- 4:5 (afiş/instagram, daha şık) '
                            . '- 3:4 (katalog/poster) '
                            . '- 16:9 (banner/kapak) '
                            . '- Serbest (ihtiyaç olursa)'
                        )
                        ->image()
                        ->imageEditor()
                        ->disk('public_root')
                        ->directory('images/products')
                        ->visibility('public')
                        ->maxSize(10240) // 10MB
                        ->imageEditorAspectRatioOptions([
                            '1:1',   // Standart ürün kartı
                            '4:5',   // Afiş / Instagram
                            '3:4',   // Katalog
                            '16:9',  // Banner / kapak
                            null,    // Serbest
                        ])
                        ->nullable()
                        ->columnSpanFull(),

                    FileUpload::make('video')
                        ->label('Video')
                        ->hidden()
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                        ->disk('public_root')
                        ->directory('videos/products')
                        ->visibility('public')
                        ->maxSize(307200) // 300MB
                        ->nullable(),
                ]),

        ]);
    }

    private static function currencyOptions(): array
    {
        return collect(CurrencyCode::cases())
            ->mapWithKeys(fn(CurrencyCode $c) => [$c->value => $c->value])
            ->all();
    }
}

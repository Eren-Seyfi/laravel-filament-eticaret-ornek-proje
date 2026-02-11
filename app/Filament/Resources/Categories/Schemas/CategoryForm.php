<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kategori Bilgileri')
                ->description(
                    'Buradan kategori adını ve sıralamasını belirleyebilirsin. ' .
                    'Kategori aktif/pasif durumunu ve anasayfada görünüp görünmeyeceğini bu ekrandan yönetirsin.'
                )
                ->icon(Heroicon::OutlinedTag)
                ->aside()
                ->columnSpanFull()
                ->schema([

                    TextInput::make('name')
                        ->label('Ad')
                        ->required()
                        ->maxLength(150)
                        ->helperText(
                            'Kategori adı burada yazılır. Örnek: "Elektronik", "Giyim", "Hizmetler". ' .
                            'Bu alan zorunludur ve kullanıcıların ekranda göreceği isim burasıdır.'
                        ),

                    TextInput::make('slug')
                        ->label('URL')
                        ->placeholder('ornek: elektronik-urunler')
                        ->maxLength(180)
                        ->helperText(
                            'URL; kategoriye ait sayfanın adresinde kullanılacak kısa metindir. ' .
                            'Örnek: "elektronik", "giyim", "hizmetler", "elektronik-urunler". ' .
                            'Bu alanı boş bırakırsanız, sistem kategori adına göre otomatik bir URL oluşturacaktır. ' .
                            'Yazarsanız kayıt sırasında otomatik olarak uygun formata çevrilir (boşluklar "-" olur, Türkçe karakterler dönüştürülür).'
                        )
                        ->hint('URL benzersiz olmalıdır. Aynı URL ile ikinci bir kategori eklenemez. Türkçe karakterler içermemelidir.')
                        ->hintColor('danger')
                        ->dehydrateStateUsing(fn(?string $state) => filled($state) ? Str::slug($state) : null),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->helperText(
                            'Aktif/Pasif durumu kategori kullanımını kontrol eder.' . "\n" .
                            '• Aktif (Açık): Kategori sistemde kullanılabilir (listeleme, filtreleme, ürün bağlama vb.).' . "\n" .
                            '• Pasif (Kapalı): Kategori görünür olsa bile kullanım dışı sayılabilir; genelde kullanıcıya göstermemek veya seçimlerden kaldırmak için kullanılır.' . "\n" .
                            'Not: Pasif kategorileri anasayfada göstermek istesen bile (aşağıdaki seçenek açık olsa bile) uygulama tarafında genelde gösterilmez. ' .
                            'Yani iyi pratik: Anasayfada görünecek kategori mutlaka aktif olmalı.'
                        ),

                    Toggle::make('show_on_homepage')
                        ->label('Anasayfada Göster')
                        ->default(false)
                        ->helperText(
                            'Bu seçenek, kategorinin anasayfada listelenip listelenmeyeceğini belirler.' . "\n" .
                            '• Açık: Anasayfada kategoriler listesinde görünür (ör: kullanıcı giriş sayfasında, vitrin sayfasında).' . "\n" .
                            '• Kapalı: Kategori sistemde vardır ama anasayfada vitrin/listede görünmez.' . "\n" .
                            'Öneri: Çok kalabalık olmasın diye sadece ana/önemli kategorilerde açık tut.'
                        ),

                    TextInput::make('sort_order')
                        ->label('Sıralama')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required()
                        ->helperText(
                            'Kategori liste sırasını belirler. Küçük sayı daha üstte görünür.' . "\n" .
                            'Örnek: 10, 20, 30 şeklinde artan değerler verirsen sonradan araya kategori eklemek kolay olur (15 gibi).' . "\n" .
                            'Bu alan hem panel listesinde hem de anasayfa/kategori listelerinde sıralama için kullanılır.'
                        ),
                ]),
        ]);
    }
}

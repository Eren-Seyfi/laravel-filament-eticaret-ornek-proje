<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use App\Models\TrackEvent;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kategori Bilgileri')
                ->description('Kategorinin temel bilgileri.')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')
                        ->label('Ad')
                        ->placeholder('-'),

                    TextEntry::make('slug')
                        ->label('Slug')
                        ->placeholder('-'),

                    TextEntry::make('is_active')
                        ->label('Durum')
                        ->formatStateUsing(fn($state) => $state ? 'Aktif' : 'Pasif')
                        ->badge()
                        ->color(fn(bool $state): string => $state ? 'success' : 'danger'),

                    TextEntry::make('show_on_homepage')
                        ->label('Anasayfa')
                        ->formatStateUsing(fn($state) => $state ? 'Göster' : 'Gizli')
                        ->badge()
                        ->color(fn(bool $state): string => $state ? 'primary' : 'gray'),

                    TextEntry::make('sort_order')
                        ->label('Sıralama')
                        ->numeric()
                        ->placeholder('0'),

                    TextEntry::make('created_at')
                        ->label('Oluşturma')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label('Güncelleme')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('-'),

                    TextEntry::make('deleted_at')
                        ->label('Silinme')
                        ->dateTime('d.m.Y H:i')
                        ->visible(fn(Category $record): bool => $record->trashed())
                        ->placeholder('-'),
                ]),

            Section::make('Günlük İstatistik')
                ->description('Bugün (gün içinde) kategorinin aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_daily_views')
                        ->label('Günlük Görüntülenme')
                        ->state(fn(Category $record): int => TrackEvent::countViewsToday($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_daily_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Category $record): int => TrackEvent::countUniqueViewsToday($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_daily_clicks')
                        ->label('Günlük Tıklama')
                        ->state(fn(Category $record): int => TrackEvent::countClicksToday($record))
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Haftalık İstatistik')
                ->description('Bu hafta (Pazartesi–Pazar) kategorinin aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_weekly_views')
                        ->label('Haftalık Görüntülenme')
                        ->state(fn(Category $record): int => TrackEvent::countViewsThisWeek($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_weekly_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Category $record): int => TrackEvent::countUniqueViewsThisWeek($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_weekly_clicks')
                        ->label('Haftalık Tıklama')
                        ->state(fn(Category $record): int => TrackEvent::countClicksThisWeek($record))
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Aylık İstatistik')
                ->description('Bu ay (ayın 1’i–ayın sonu) kategorinin aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_monthly_views')
                        ->label('Aylık Görüntülenme')
                        ->state(fn(Category $record): int => TrackEvent::countViewsThisMonth($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_monthly_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Category $record): int => TrackEvent::countUniqueViewsThisMonth($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_monthly_clicks')
                        ->label('Aylık Tıklama')
                        ->state(fn(Category $record): int => TrackEvent::countClicksThisMonth($record))
                        ->badge()
                        ->color('warning'),
                ]),
        ]);
    }
}

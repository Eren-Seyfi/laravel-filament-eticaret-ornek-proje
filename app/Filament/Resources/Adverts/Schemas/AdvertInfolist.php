<?php

namespace App\Filament\Resources\Adverts\Schemas;

use App\Models\Advert;
use App\Models\TrackEvent;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdvertInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Reklam Bilgileri')
                ->description('Reklamın temel bilgileri.')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->label('Başlık')
                        ->placeholder('-')
                        ->visible(fn(Advert $record): bool => filled(data_get($record, 'title'))),

                    TextEntry::make('name')
                        ->label('Ad')
                        ->placeholder('-')
                        ->visible(fn(Advert $record): bool => filled(data_get($record, 'name'))),

                    TextEntry::make('external_url')
                        ->label('Dış Bağlantı')
                        ->placeholder('-')
                        ->visible(fn(Advert $record): bool => filled(data_get($record, 'external_url'))),

                    ImageEntry::make('image')
                        ->label('Görsel')
                        ->visible(fn(Advert $record): bool => filled(data_get($record, 'image'))),

                    IconEntry::make('is_active')
                        ->label('Durum')
                        ->boolean()
                        ->visible(fn(Advert $record): bool => data_get($record, 'is_active') !== null),

                    TextEntry::make('created_at')
                        ->label('Oluşturma')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label('Güncelleme')
                        ->dateTime('d.m.Y H:i')
                        ->placeholder('-'),
                ]),

            Section::make('Günlük İstatistik')
                ->description('Bugün (gün içinde) reklamın aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_daily_views')
                        ->label('Günlük Görüntülenme')
                        ->state(fn(Advert $record): int => TrackEvent::countViewsToday($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_daily_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Advert $record): int => TrackEvent::countUniqueViewsToday($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_daily_clicks')
                        ->label('Günlük Tıklama')
                        ->state(fn(Advert $record): int => TrackEvent::countClicksToday($record))
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Haftalık İstatistik')
                ->description('Bu hafta (Pazartesi–Pazar) reklamın aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_weekly_views')
                        ->label('Haftalık Görüntülenme')
                        ->state(fn(Advert $record): int => TrackEvent::countViewsThisWeek($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_weekly_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Advert $record): int => TrackEvent::countUniqueViewsThisWeek($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_weekly_clicks')
                        ->label('Haftalık Tıklama')
                        ->state(fn(Advert $record): int => TrackEvent::countClicksThisWeek($record))
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Aylık İstatistik')
                ->description('Bu ay (ayın 1’i–ayın sonu) reklamın aldığı görüntülenme ve tıklama verileri.')
                ->columns(3)
                ->schema([
                    TextEntry::make('stats_monthly_views')
                        ->label('Aylık Görüntülenme')
                        ->state(fn(Advert $record): int => TrackEvent::countViewsThisMonth($record))
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('stats_monthly_unique_views')
                        ->label('Benzersiz Ziyaretçi')
                        ->state(fn(Advert $record): int => TrackEvent::countUniqueViewsThisMonth($record))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('stats_monthly_clicks')
                        ->label('Aylık Tıklama')
                        ->state(fn(Advert $record): int => TrackEvent::countClicksThisMonth($record))
                        ->badge()
                        ->color('warning'),
                ]),
        ]);
    }
}

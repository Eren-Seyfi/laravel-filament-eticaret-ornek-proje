<?php

namespace App\Enums;

enum TrackEventType: string
{
      case View = 'view';
      case Click = 'click';

      // Sayfa görüntüleme (homepage gibi)
      case PageView = 'page_view';

      // İstersen kalma süresi için ping
      case Heartbeat = 'heartbeat';

      public function label(): string
      {
            return match ($this) {
                  self::View => 'Görüntüleme',
                  self::Click => 'Tıklama',
                  self::PageView => 'Sayfa Görüntüleme',
                  self::Heartbeat => 'Kalma Süresi (Ping)',
            };
      }

      public static function options(): array
      {
            $out = [];
            foreach (self::cases() as $case) {
                  $out[$case->value] = $case->label();
            }
            return $out;
      }
}

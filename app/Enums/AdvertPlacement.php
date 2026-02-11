<?php

namespace App\Enums;

enum AdvertPlacement: string
{
      case Front = 'front';   // Ön (hero / vitrin)
      case Top = 'top';     // Üst
      case Bottom = 'bottom';  // Alt
      case Left = 'left';    // Sol
      case Right = 'right';   // Sağ
      case Popup = 'popup';   // Popup

      public function label(): string
      {
            return match ($this) {
                  self::Front => 'Özel Slider',
                  self::Top => 'Üst Slider',
                  self::Bottom => 'Alt Slider',
                  self::Left => 'Sol Slider',
                  self::Right => 'Sağ Slider',
                  self::Popup => 'Popup',
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

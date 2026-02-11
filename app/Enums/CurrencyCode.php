<?php

namespace App\Enums;

enum CurrencyCode: string
{
      case TRY = 'TRY';
      case USD = 'USD';
      case EUR = 'EUR';

      public function label(): string
      {
            return match ($this) {
                  self::TRY => 'Türk Lirası (TRY)',
                  self::USD => 'US Dollar (USD)',
                  self::EUR => 'Euro (EUR)',
            };
      }

      /** Filament Select için */
      public static function options(): array
      {
            $out = [];
            foreach (self::cases() as $case) {
                  $out[$case->value] = $case->label();
            }
            return $out;
      }

}

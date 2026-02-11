<?php

namespace App\Enums;

enum SocialPlatform: string
{
      case Instagram = 'instagram';
      case X = 'x';
      case Facebook = 'facebook';
      case YouTube = 'youtube';
      case TikTok = 'tiktok';
      case LinkedIn = 'linkedin';
      case GitHub = 'github';
      case Telegram = 'telegram';
      case WhatsApp = 'whatsapp';
      case Discord = 'discord';
      case Pinterest = 'pinterest';
      case Snapchat = 'snapchat';
      case Threads = 'threads';
      case Medium = 'medium';
      case Twitch = 'twitch';
      case Reddit = 'reddit';
      case Website = 'website';

      public function label(): string
      {
            return match ($this) {
                  self::Instagram => 'Instagram',
                  self::X => 'X (Twitter)',
                  self::Facebook => 'Facebook',
                  self::YouTube => 'YouTube',
                  self::TikTok => 'TikTok',
                  self::LinkedIn => 'LinkedIn',
                  self::GitHub => 'GitHub',
                  self::Telegram => 'Telegram',
                  self::WhatsApp => 'WhatsApp',
                  self::Discord => 'Discord',
                  self::Pinterest => 'Pinterest',
                  self::Snapchat => 'Snapchat',
                  self::Threads => 'Threads',
                  self::Medium => 'Medium',
                  self::Twitch => 'Twitch',
                  self::Reddit => 'Reddit',
                  self::Website => 'Website',
            };
      }

      public function iconClass(): string
      {
            return match ($this) {
                  self::Instagram => 'fa-brands fa-instagram',
                  self::X => 'fa-brands fa-x-twitter',
                  self::Facebook => 'fa-brands fa-facebook',
                  self::YouTube => 'fa-brands fa-youtube',
                  self::TikTok => 'fa-brands fa-tiktok',
                  self::LinkedIn => 'fa-brands fa-linkedin',
                  self::GitHub => 'fa-brands fa-github',
                  self::Telegram => 'fa-brands fa-telegram',
                  self::WhatsApp => 'fa-brands fa-whatsapp',
                  self::Discord => 'fa-brands fa-discord',
                  self::Pinterest => 'fa-brands fa-pinterest',
                  self::Snapchat => 'fa-brands fa-snapchat',
                  self::Threads => 'fa-brands fa-threads',
                  self::Medium => 'fa-brands fa-medium',
                  self::Twitch => 'fa-brands fa-twitch',
                  self::Reddit => 'fa-brands fa-reddit',
                  self::Website => 'fa-solid fa-globe',
            };
      }

      /** Filament Select::options için */
      public static function options(): array
      {
            $out = [];
            foreach (self::cases() as $case) {
                  $out[$case->value] = $case->label();
            }
            return $out;
      }

      public static function iconFor(?string $platform, ?string $fallback = 'fa-solid fa-link'): string
      {
            $enum = $platform ? self::tryFrom($platform) : null;
            return $enum?->iconClass() ?? ($fallback ?? 'fa-solid fa-link');
      }
}

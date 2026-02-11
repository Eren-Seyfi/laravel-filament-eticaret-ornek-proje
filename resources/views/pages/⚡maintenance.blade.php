<?php

use Livewire\Component;
use App\Models\Setting;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

new class extends Component {
    public ?string $messageHtml = null;

    public function mount(): void
    {
        $settings = Setting::cached();

        $this->messageHtml = null;

        $value = $settings->maintenance_message;

        if (empty($value)) {
            return;
        }

        // ✅ JSON (array) ise RichContentRenderer
        if (is_array($value)) {
            $html = RichContentRenderer::make($value)
                ->fileAttachmentsDisk('public_root')
                ->fileAttachmentsVisibility('public')
                ->toHtml();

            $plain = trim(strip_tags($html ?? ''));
            $this->messageHtml = $plain !== '' ? $html : null;

            return;
        }

        // ✅ HTML string ise sanitize + boş kontrolü
        if (is_string($value)) {
            if (trim($value) === '') {
                $this->messageHtml = null;
                return;
            }

            $html = str($value)->sanitizeHtml()->toString();

            $plain = trim(strip_tags($html ?? ''));
            $this->messageHtml = $plain !== '' ? $html : null;
        }
    }
};
?>

@php
    $fallback = 'Şu anda bakım modundayız. Lütfen daha sonra tekrar deneyin.';
@endphp

<div class="maintenance-page app-theme-bg d-flex align-items-center justify-content-center py-5">
    <div class="container px-3 px-sm-4" style="max-width: 920px;">
        <div class="app-surface p-4 p-md-5 shadow-sm">

            <div class="maintenance-head d-flex align-items-center gap-3 mb-3">
                <div class="maintenance-badge d-inline-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="h4 mb-1">Bakım Modu</div>
                    <div class="text-body-secondary small">
                        Sistem kısa süreliğine bakımda. Yakında tekrar yayındayız.
                    </div>
                </div>
            </div>

            <div class="maintenance-divider my-4"></div>

            <div class="fi-prose max-w-none maintenance-prose">
                {!! $messageHtml ?: e($fallback) !!}
            </div>

        </div>
    </div>
</div>

<style>
    /* Sayfa alanı: senin arkaplan tokenlarını kullanır */
    .maintenance-page {
        min-height: 100vh;
    }

    /* küçük ikonlu badge */
    .maintenance-badge {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg,
                rgb(var(--accent-2-rgb) / 0.18),
                rgb(var(--accent-rgb) / 0.18));
        border: 1px solid rgb(0 0 0 / 0.06);
    }

    [data-bs-theme="dark"] .maintenance-badge {
        border-color: rgb(255 255 255 / 0.10);
    }

    .maintenance-badge i {
        font-size: 18px;
        color: var(--accent);
    }

    /* ince ayraç */
    .maintenance-divider {
        height: 1px;
        width: 100%;
        background: linear-gradient(90deg,
                transparent,
                rgb(var(--accent-2-rgb) / 0.22),
                rgb(var(--accent-rgb) / 0.22),
                transparent);
        opacity: .9;
    }

    /* Prose yazı uyumu */
    .maintenance-prose :where(p, li) {
        line-height: 1.6;
    }

    .maintenance-prose {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .maintenance-prose a {
        color: var(--accent);
        text-decoration: none;
        border-bottom: 1px dashed rgb(var(--accent-rgb) / 0.45);
    }

    .maintenance-prose a:hover {
        border-bottom-style: solid;
    }

    /* Rich editor bazen boş <p> üretiyor, boşlukları toparlar */
    .maintenance-prose p:empty {
        display: none;
    }

    /* Prose taşmalarını engelle (mobilde özellikle) */
    .maintenance-prose img,
    .maintenance-prose video,
    .maintenance-prose iframe {
        max-width: 100%;
        height: auto;
    }

    .maintenance-prose table {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* =========================
       MOBILE TUNING
       ========================= */
    @media (max-width: 576px) {
        .maintenance-page {
            padding-top: 1.25rem !important;
            padding-bottom: 1.25rem !important;
        }

        .maintenance-page .app-surface {
            padding: 1rem !important;
            border-radius: 1rem;
        }

        /* üst satır mobilde dikey */
        .maintenance-head {
            flex-direction: column;
            align-items: flex-start !important;
            gap: .75rem !important;
        }

        .maintenance-badge {
            width: 48px;
            height: 48px;
            border-radius: 16px;
        }

        .maintenance-page .h4 {
            font-size: 1.15rem;
            margin-bottom: .25rem;
        }

        .maintenance-prose {
            font-size: 0.98rem;
        }
    }
</style>
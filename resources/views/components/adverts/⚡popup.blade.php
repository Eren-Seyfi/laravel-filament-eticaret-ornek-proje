<?php

use Livewire\Component;
use App\Models\Advert;
use App\Enums\AdvertPlacement;

new class extends Component {
    /** @var array{advert_id:int,img:string,external_url:?string}|null */
    public ?array $popup = null;

    public function mount(): void
    {
        $slides = Advert::slidesForPlacement(AdvertPlacement::Popup);

        if (!empty($slides)) {
            $first = $slides[0];

            $this->popup = [
                'advert_id' => (int) ($first['advert_id'] ?? 0),
                'img' => (string) ($first['img'] ?? ''),
                'external_url' => $first['external_url'] ?? null,
            ];
        }
    }
};
?>

@php
    $hasPopup = !empty($popup) && !empty($popup['img']);

    $modalId = $hasPopup
        ? ('advertPopupModal_' . (($popup['advert_id'] ?? 0) ?: uniqid()))
        : ('advertPopupModal_' . uniqid());

    $imgUrl = $hasPopup ? asset($popup['img']) : null;
    $externalUrl = $hasPopup ? ($popup['external_url'] ?? null) : null;

    // ✅ kullanıcı kapatınca bir daha gösterme key’i
    $storageKey = '_advert_popup_closed_v1';
@endphp

<div class="advert-popup-root" wire:ignore.self>
    <style>
        .advert-popup-modal .modal-dialog {
            max-width: 720px;
        }

        .advert-popup-modal .modal-content {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            background: transparent;
            border: 0;
            box-shadow: 0 18px 60px rgba(0, 0, 0, .30);
        }

        .advert-popup-img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }

        .advert-popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .22);
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            cursor: pointer;
            transition: transform .15s ease, background-color .15s ease;
        }

        @media (hover:hover) and (pointer:fine) {
            .advert-popup-close:hover {
                transform: scale(1.03);
                background: rgba(0, 0, 0, .55);
            }
        }

        .advert-popup-close:active {
            transform: scale(.98);
        }

        [data-bs-theme="dark"] .advert-popup-close {
            border-color: rgba(255, 255, 255, .18);
            background: rgba(0, 0, 0, .55);
        }
    </style>

    @if ($hasPopup)
        <div class="modal fade advert-popup-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" data-popup-modal="1"
            data-popup-storage-key="{{ $storageKey }}">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <button type="button" class="advert-popup-close" data-bs-dismiss="modal" aria-label="Kapat">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    @if (!empty($externalUrl))
                        <a href="{{ $externalUrl }}" target="_blank" rel="nofollow noopener" class="d-block">
                            <img src="{{ $imgUrl }}" alt="popup advert" class="advert-popup-img" loading="lazy"
                                decoding="async">
                        </a>
                    @else
                        <img src="{{ $imgUrl }}" alt="popup advert" class="advert-popup-img" loading="lazy" decoding="async">
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>
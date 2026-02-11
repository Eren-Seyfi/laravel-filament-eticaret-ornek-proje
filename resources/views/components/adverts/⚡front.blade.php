<?php

use Livewire\Component;
use App\Models\Advert;

new class extends Component {
    /** @var array<int, array{advert_id:int,title:?string,external_url:?string,uid:string,slides:array<int,array{img:string}>}> */
    public array $sliders = [];

    public function mount(): void
    {
        $this->sliders = Advert::frontSliders();
    }
};
?>

<style>
    /* ✅ Alt alta slider blokları arası boşluk */
    .front-stack {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* ✅ Tema tokenlarıyla uyumlu “glass card” */
    .front-item {
        position: relative;
        border-radius: 18px;
        padding: 14px;

        background: var(--surface-bg);
        border: 1px solid var(--surface-border);

        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        box-shadow: 0 14px 36px rgba(0, 0, 0, .08);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    /* ✅ Sol vurgu şeridi (tema rengi) */
    .front-item::before {
        content: "";
        position: absolute;
        left: 10px;
        top: 12px;
        bottom: 12px;
        width: 4px;
        border-radius: 999px;
        background: var(--accent);
        box-shadow: 0 0 18px rgba(22, 199, 132, .18);
        opacity: .9;
    }

    /* Hover daha “premium” */
    @media (hover:hover) and (pointer:fine) {
        .front-item:hover {
            transform: translateY(-1px);
            border-color: rgba(22, 199, 132, .35);
            box-shadow: 0 18px 44px rgba(0, 0, 0, .12);
        }

        [data-bs-theme="dark"] .front-item:hover {
            border-color: rgba(22, 199, 132, .45);
            box-shadow: 0 18px 52px rgba(0, 0, 0, .45);
        }
    }

    /* ✅ Kart başlığı */
    .front-item-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        padding-left: 18px;
        /* sol şeritten dolayı içerik içeri girsin */
        margin: 2px 2px 12px 2px;

        font-weight: 800;
        font-size: .95rem;
        line-height: 1.1;
    }

    .front-title-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    /* ✅ Badge tema uyumlu */
    .front-item-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;

        padding: .28rem .6rem;
        border-radius: 999px;

        border: 1px solid rgba(22, 199, 132, .35);
        background: rgba(22, 199, 132, .12);

        font-size: .75rem;
        font-weight: 900;
        white-space: nowrap;
    }

    [data-bs-theme="dark"] .front-item-badge {
        border-color: rgba(22, 199, 132, .45);
        background: rgba(22, 199, 132, .14);
    }

    /* ✅ Başlık taşmasın */
    .front-item-name {
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /* ✅ sağ tarafta küçük meta */
    .front-item-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .75rem;
        font-weight: 800;
        color: var(--bs-secondary-color);
        white-space: nowrap;
    }

    .front-meta-pill {
        padding: .22rem .5rem;
        border-radius: 999px;
        border: 1px solid var(--surface-border);
        background: rgba(255, 255, 255, .25);
    }

    [data-bs-theme="dark"] .front-meta-pill {
        background: rgba(255, 255, 255, .06);
    }

    .front-swiper-wrap {
        width: 100%;
        padding-left: 18px;
    }

    /* sol şerit hizası */

    .front-swiper-wrap .swiper {
        width: 100%;
    }

    /* ✅ slide kart görünümü */
    .front-swiper-wrap .swiper-slide {
        border-radius: 16px;
        overflow: hidden;
        background: rgba(0, 0, 0, .04);

        /* ✅ daha yüksek görünüm */
        aspect-ratio: 4 / 3;
        border: 1px solid var(--surface-border);
    }

    @media (min-width:768px) {
        .front-swiper-wrap .swiper-slide {
            aspect-ratio: 3 / 2;
        }
    }

    @media (min-width:1200px) {
        .front-swiper-wrap .swiper-slide {
            aspect-ratio: 16 / 9;
        }
    }

    .front-swiper-wrap .swiper-slide img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    /* okları gizle */
    .front-swiper-wrap .swiper-button-next,
    .front-swiper-wrap .swiper-button-prev {
        display: none !important;
    }

    /* pagination kapalı */
    .front-swiper-wrap .swiper-pagination {
        display: none !important;
    }

    /* ✅ Link focus tema uyumlu */
    .front-swiper-wrap a:focus {
        outline: 0;
        box-shadow: var(--focus-ring);
        border-radius: 16px;
    }
</style>

<div class="front-stack">
    @forelse ($sliders as $slider)
        @php
            $uid = $slider['uid'];
            $slides = $slider['slides'] ?? [];
            $slideCount = is_array($slides) ? count($slides) : 0;
            $externalUrl = $slider['external_url'] ?? null;
            $title = $slider['title'] ?? null;
            $advertId = (int) ($slider['advert_id'] ?? 0);
        @endphp

        <div class="front-item">
            <div class="front-item-title">
                <div class="front-title-left">
                    <span class="front-item-badge">
                        <span
                            style="width:8px;height:8px;border-radius:999px;background:var(--accent);display:inline-block;"></span>
                        {{ $title ?: ('#' . $advertId) }}
                    </span>
                </div>

            </div>

            <div class="front-swiper-wrap" wire:ignore.self data-swiper-front="1" data-swiper-uid="{{ $uid }}"
                data-swiper-count="{{ $slideCount }}" data-swiper-delay="2200">

                @if ($slideCount > 0)
                    <div id="{{ $uid }}" class="swiper">
                        <div class="swiper-wrapper">
                            @foreach ($slides as $s)
                                <div class="swiper-slide">
                                    @if (!empty($externalUrl))
                                        <a href="{{ $externalUrl }}" target="_blank" rel="nofollow noopener"
                                            class="d-block w-100 h-100">
                                            <img src="{{ asset($s['img']) }}" alt="advert" loading="lazy" decoding="async">
                                        </a>
                                    @else
                                        <img src="{{ asset($s['img']) }}" alt="advert" loading="lazy" decoding="async">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @empty
        <div class="text-body-secondary small"></div>
    @endforelse
</div>
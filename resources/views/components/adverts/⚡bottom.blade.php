<!-- resources/views/components/adverts/⚡bottom.blade.php -->
<?php

use Livewire\Component;
use App\Models\Advert;
use App\Enums\AdvertPlacement;

new class extends Component {
    public array $slides = [];
    public string $uid = '';

    public function mount(): void
    {
        $this->uid = 'bottomSwiper_' . uniqid();
        $this->slides = Advert::slidesForPlacement(AdvertPlacement::Bottom);
    }
};
?>

<style>
    .bottom-swiper-wrap {
        width: 100%;
    }

    /* ✅ sabit height kaldırıldı (resme göre otomatik) */
    .bottom-swiper-wrap .swiper {
        width: 100%;
        height: auto;
    }

    /* ✅ slide artık img yüksekliğine göre büyür + yuvarlak köşe */
    .bottom-swiper-wrap .swiper-slide {
        border-radius: 14px;
        overflow: hidden;
        background: rgba(0, 0, 0, .04);
    }

    /* ✅ img kendi oranıyla aksın (yükseklik otomatik) */
    .bottom-swiper-wrap .swiper-slide img {
        display: block;
        width: 100%;
        height: auto;
        /* kritik */
        object-fit: contain;
        /* kırpmasın */
        border-radius: 14px;
    }

    /* ✅ okları gizle */
    .bottom-swiper-wrap .swiper-button-next,
    .bottom-swiper-wrap .swiper-button-prev {
        display: none !important;
    }

    /* ✅ pagination kapalı */
    .bottom-swiper-wrap .swiper-pagination {
        display: none !important;
    }
</style>

@php
    $slideCount = is_array($slides) ? count($slides) : 0;
@endphp

<div class="bottom-swiper-wrap" wire:ignore.self data-swiper-top="1" data-swiper-uid="{{ $uid }}"
    data-swiper-count="{{ $slideCount }}" data-swiper-delay="2200">
    @if ($slideCount > 0)
        <div id="{{ $uid }}" class="swiper mySwiper">
            <div class="swiper-wrapper">
                @foreach ($slides as $slide)
                    <div class="swiper-slide">
                        @if (!empty($slide['external_url']))
                            <a href="{{ $slide['external_url'] }}" target="_blank" rel="nofollow noopener" class="d-block w-100">
                                <img src="{{ asset($slide['img']) }}" alt="advert" loading="lazy" decoding="async">
                            </a>
                        @else
                            <img src="{{ asset($slide['img']) }}" alt="advert" loading="lazy" decoding="async">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
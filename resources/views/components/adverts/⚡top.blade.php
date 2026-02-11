<!-- resources/views/components/adverts/⚡top.blade.php -->
<?php

use Livewire\Component;
use App\Models\Advert;
use App\Enums\AdvertPlacement;

new class extends Component {
    public array $slides = [];
    public string $uid = '';

    public function mount(): void
    {
        $this->uid = 'topSwiper_' . uniqid();
        $this->slides = Advert::slidesForPlacement(AdvertPlacement::Top);
    }
};
?>

<style>
    .top-swiper-wrap {
        width: 100%;
    }

    /* ✅ yükseklik otomatik (resme göre) */
    .top-swiper-wrap .swiper {
        width: 100%;
        height: auto;
    }

    /* ✅ slide img yüksekliğine göre büyür + yuvarlak köşe */
    .top-swiper-wrap .swiper-slide {
        border-radius: 14px;
        overflow: hidden;
        background: rgba(0, 0, 0, .04);
    }

    /* ✅ resim oranını korur, yükseklik otomatik */
    .top-swiper-wrap .swiper-slide img {
        display: block;
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 14px;
    }

    /* ✅ okları gizle */
    .top-swiper-wrap .swiper-button-next,
    .top-swiper-wrap .swiper-button-prev {
        display: none !important;
    }

    /* ✅ pagination kapalı */
    .top-swiper-wrap .swiper-pagination {
        display: none !important;
    }
</style>

@php
    $slideCount = is_array($slides) ? count($slides) : 0;
@endphp

<div class="top-swiper-wrap" wire:ignore.self data-swiper-top="1" data-swiper-uid="{{ $uid }}"
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
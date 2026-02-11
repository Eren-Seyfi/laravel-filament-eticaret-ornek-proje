<?php

use Livewire\Component;
use App\Models\Advert;
use App\Enums\AdvertPlacement;

new class extends Component {
    public array $slides = [];
    public string $uid = '';

    public function mount(): void
    {
        $this->uid = 'leftSwiper_' . uniqid();
        $this->slides = Advert::slidesForPlacement(AdvertPlacement::Left);
    }
};
?>

<style>
    .left-slider { height: 100%; }
    .left-slider .swiper { width: 100%; height: 100%; }
    .left-slider .swiper-slide { height: 100%; }

    .left-slider .ad-carousel-frame { width: 100%; height: 100%; }
    .left-slider .ad-carousel-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .left-slider .swiper-button-next,
    .left-slider .swiper-button-prev { display: none !important; }
</style>

@php $slideCount = is_array($slides) ? count($slides) : 0; @endphp

<div class="left-slider h-100" wire:ignore.self
     data-swiper-fade="1"
     data-swiper-uid="{{ $uid }}"
     data-swiper-count="{{ $slideCount }}"
     data-swiper-delay="4500"
     data-swiper-speed="2000">
    @if ($slideCount > 0)
        <div id="{{ $uid }}" class="swiper swiper-fade h-100">
            <div class="swiper-wrapper h-100">
                @foreach ($slides as $slide)
                    <div class="swiper-slide h-100">
                        <div class="ad-carousel-frame">
                            @if (!empty($slide['external_url']))
                                <a href="{{ $slide['external_url'] }}" target="_blank" rel="nofollow noopener"
                                class="d-block w-100 h-100">
                                    <img src="{{ asset($slide['img']) }}" class="ad-carousel-img" loading="lazy" decoding="async" alt="advert">
                                </a>
                            @else
                                <img src="{{ asset($slide['img']) }}" class="ad-carousel-img" loading="lazy" decoding="async" alt="advert">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

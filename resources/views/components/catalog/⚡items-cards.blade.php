<?php

use Livewire\Component;
use Illuminate\Support\Str;

new class extends Component {
    public $categories;

    public function mount($categories): void
    {
        $this->categories = $categories;
    }
};
?>

<div class="catalog-items-cards">

    <style>
        /* =========================
           SECTION (same as table component – theme-native)
           ========================= */
        .catalog-card-section {
            border: 1px solid var(--surface-border);
            background: var(--surface-bg);
            border-radius: 1rem;
            padding: 0.85rem;

            transition:
                border-color .15s ease,
                background-color .15s ease,
                box-shadow .15s ease,
                transform .15s ease;
        }

        .catalog-card-section+.catalog-card-section {
            margin-top: 0.85rem;
        }

        @media (hover:hover) and (pointer:fine) {
            .catalog-card-section:hover {
                transform: translateY(-1px);
                border-color: rgb(var(--accent-rgb) / 0.55);
                background: rgb(var(--accent-rgb) / 0.08);
                box-shadow: 0 0 0.9rem rgb(var(--accent-rgb) / 0.14);
            }

            [data-bs-theme="dark"] .catalog-card-section:hover {
                background: rgb(var(--accent-rgb) / 0.12);
                box-shadow: 0 0 1rem rgb(var(--accent-rgb) / 0.16);
            }
        }

        .catalog-card-head .btn {
            border-radius: 999px;
        }

        .catalog-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--accent);
            display: inline-block;
            box-shadow: 0 0 0.7rem rgb(var(--accent-rgb) / 0.25);
        }

        [data-bs-theme="dark"] .catalog-dot {
            box-shadow: 0 0 0.85rem rgb(var(--accent-rgb) / 0.35);
        }

        /* =========================
           CARD (glass like chips/offcanvas)
           ========================= */
        .catalog-item-card {
            height: 100%;
            border-radius: 1rem;
            overflow: hidden;

            border: 1px solid var(--surface-border);
            background: var(--surface-bg);

            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            transition:
                transform .15s ease,
                box-shadow .15s ease,
                border-color .15s ease,
                background-color .15s ease;
        }

        @media (hover:hover) and (pointer:fine) {
            .catalog-item-card:hover {
                transform: translateY(-2px);
                border-color: rgb(var(--accent-2-rgb) / 0.35);
                box-shadow: 0 0 1rem rgb(var(--accent-2-rgb) / 0.14);
            }

            [data-bs-theme="dark"] .catalog-item-card:hover {
                border-color: rgb(var(--accent-2-rgb) / 0.40);
                box-shadow: 0 0 1.1rem rgb(var(--accent-2-rgb) / 0.16);
            }
        }

        /* Media area */
        .catalog-item-media {
            aspect-ratio: 16 / 9;
            width: 100%;
            background: rgb(var(--accent-2-rgb) / 0.06);
            border-bottom: 1px solid var(--surface-border);

            display: flex;
            align-items: center;
            justify-content: center;
        }

        [data-bs-theme="dark"] .catalog-item-media {
            background: rgb(var(--accent-2-rgb) / 0.08);
        }

        .catalog-item-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Price chip */
        .catalog-price {
            font-weight: 800;
        }

        /* Primary action (same gradient language) */
        .btn-catalog-primary {
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg,
                    rgb(var(--accent-rgb) / 0.95),
                    rgb(var(--accent-2-rgb) / 0.95));
            color: #fff !important;
            box-shadow: 0 0 0.9rem rgb(var(--accent-2-rgb) / 0.14);

            transition:
                transform .15s ease,
                box-shadow .15s ease,
                filter .15s ease;
        }

        @media (hover:hover) and (pointer:fine) {
            .btn-catalog-primary:hover {
                transform: translateY(-1px);
                filter: brightness(1.02);
                box-shadow: 0 0 1rem rgb(var(--accent-2-rgb) / 0.16);
            }
        }

        .btn-catalog-primary:focus {
            outline: 0;
            box-shadow: var(--focus-ring);
        }

        /* Empty state */
        .catalog-empty {
            border: 1px dashed rgb(var(--accent-2-rgb) / 0.35);
            background: rgb(var(--accent-2-rgb) / 0.06);
            border-radius: 0.9rem;
            padding: 0.75rem 0.9rem;
        }

        [data-bs-theme="dark"] .catalog-empty {
            background: rgb(var(--accent-2-rgb) / 0.08);
        }
    </style>

    @foreach($categories as $cat)
        <div class="catalog-card-section">

            <div
                class="catalog-card-head d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 mb-2">
                <div class="fw-semibold d-flex align-items-center gap-2">
                    <span class="catalog-dot" aria-hidden="true"></span>
                    <span>{{ $cat->name }}</span>
                </div>

                <a class="btn btn-sm btn-outline-secondary align-self-start align-self-sm-center"
                    href="{{ url('/category/' . $cat->slug) }}" wire:navigate>
                    Kategoriye git <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>


            @if($cat->products->isEmpty())
                <div class="catalog-empty d-flex align-items-start gap-2">
                    <i class="fa-regular fa-circle-info mt-1"></i>
                    <div class="small">
                        <div class="fw-semibold">Ürün yok</div>
                        <div class="text-body-secondary">Bu kategoride gösterilecek ürün bulunamadı.</div>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach($cat->products as $p)
                        @php
                            $img = $p->image ? asset($p->image) : null;
                            $price = number_format((float) $p->price, 2, ',', '.');
                            $currency = $p->currency?->value ?? 'TRY';
                            $desc = $p->description ? Str::limit(strip_tags($p->description), 110) : null;
                        @endphp

                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="catalog-item-card">

                                <div class="catalog-item-media">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $p->name }}" loading="lazy" decoding="async">
                                    @else
                                        <div class="text-body-secondary">
                                            <i class="fa-regular fa-image fa-lg"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-3">
                                    <div class="fw-semibold mb-1">{{ $p->name }}</div>

                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="catalog-price">
                                            {{ $price }}
                                            <span class="text-body-secondary small">{{ $currency }}</span>
                                        </div>

                                        <span class="badge text-bg-warning-subtle text-warning-emphasis">
                                            {{ (int) $p->rating_stars }}/5
                                        </span>
                                    </div>

                                    @if($desc)
                                        <div class="small text-body-secondary">
                                            {{ $desc }}
                                        </div>
                                    @endif
                                </div>

                                <div class="px-3 pb-3">
                                    @if($p->external_url)
                                        <a class="btn btn-sm btn-catalog-primary w-100" href="{{ $p->external_url }}" target="_blank"
                                            rel="nofollow noopener">
                                            İncele <i class="fa-solid fa-up-right-from-square ms-1"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary w-100" type="button" disabled>
                                            Link yok
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endforeach

</div>
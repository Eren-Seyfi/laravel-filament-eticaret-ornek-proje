<?php

use Livewire\Component;

new class extends Component {
    public $categories;

    public function mount($categories): void
    {
        $this->categories = $categories;
    }
};
?>

<div class="catalog-items-table">

    <style>
        /* =========================
           THEME-ALIGNED TOKENS
           ========================= */
        .catalog-items-table {
            --cat-hover-bg: rgb(var(--accent-rgb) / 0.08);
            --cat-hover-border: rgb(var(--accent-rgb) / 0.55);
            --cat-hover-shadow: 0 0 0.9rem rgb(var(--accent-rgb) / 0.14);

            --row-hover-bg: rgb(var(--accent-2-rgb) / 0.06);

            --thead-a: rgb(var(--accent-2-rgb) / 0.10);
            --thead-b: rgb(var(--accent-rgb) / 0.08);

            --star-on: rgb(var(--accent-warn-rgb) / 0.95);
            --star-off: rgb(0 0 0 / 0.18);
        }

        [data-bs-theme="dark"] .catalog-items-table {
            --cat-hover-bg: rgb(var(--accent-rgb) / 0.12);
            --cat-hover-border: rgb(var(--accent-rgb) / 0.65);
            --cat-hover-shadow: 0 0 1rem rgb(var(--accent-rgb) / 0.16);

            --row-hover-bg: rgb(var(--accent-2-rgb) / 0.08);

            --thead-a: rgb(var(--accent-2-rgb) / 0.12);
            --thead-b: rgb(var(--accent-rgb) / 0.10);

            --star-off: rgb(255 255 255 / 0.22);
        }

        /* =========================
           SECTION
           ========================= */
        .catalog-section {
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

        .catalog-section+.catalog-section {
            margin-top: 0.85rem;
        }

        @media (hover: hover) and (pointer: fine) {
            .catalog-section:hover {
                transform: translateY(-1px);
                border-color: var(--cat-hover-border);
                background: var(--cat-hover-bg);
                box-shadow: var(--cat-hover-shadow);
            }
        }

        .catalog-section-head .btn {
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
           TABLE
           ========================= */
        .catalog-table-wrap {
            border-radius: 0.9rem;
            overflow: hidden;
            border: 1px solid var(--surface-border);
            background: var(--surface-bg);
        }

        .catalog-table {
            margin-bottom: 0;
        }

        .catalog-table td,
        .catalog-table th {
            border-color: var(--surface-border);
        }

        .catalog-table thead th {
            font-weight: 800;
            letter-spacing: .2px;
            background: linear-gradient(90deg, var(--thead-a), var(--thead-b));
        }

        @media (hover: hover) and (pointer: fine) {
            .catalog-table tbody tr:hover td {
                background: var(--row-hover-bg);
            }
        }

        /* =========================
           THUMB
           ========================= */
        .catalog-thumb,
        .catalog-thumb-ph {
            width: 56px;
            height: 56px;
            border-radius: 0.85rem;
        }

        .catalog-thumb {
            object-fit: cover;
            display: block;
        }

        .catalog-thumb-ph {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--surface-border);
            background: var(--surface-bg);
        }

        /* =========================
           STARS (FontAwesome)
           ========================= */
        .rating-stars {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            line-height: 1;
            white-space: nowrap;
        }

        .rating-stars i {
            font-size: 0.95rem;
            color: var(--star-off);
        }

        .rating-stars i.is-on {
            color: var(--star-on);
            text-shadow: 0 0 0.75rem rgb(var(--accent-warn-rgb) / 0.22);
        }

        .rating-stars .rating-num {
            margin-left: 6px;
            font-size: 0.85rem;
            color: var(--bs-body-color);
            opacity: .75;
        }

        /* =========================
           PRIMARY ACTION
           ========================= */
        .btn-catalog-primary {
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg,
                    rgb(var(--accent-rgb) / 0.95),
                    rgb(var(--accent-2-rgb) / 0.95));
            color: #fff !important;
            box-shadow: 0 0 0.9rem rgb(var(--accent-2-rgb) / 0.14);

            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        }

        @media (hover: hover) and (pointer: fine) {
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

        /* =========================
           EMPTY
           ========================= */
        .catalog-empty {
            border: 1px dashed rgb(var(--accent-2-rgb) / 0.35);
            background: rgb(var(--accent-2-rgb) / 0.06);
            border-radius: 0.9rem;
            padding: 0.75rem 0.9rem;
        }

        [data-bs-theme="dark"] .catalog-empty {
            background: rgb(var(--accent-2-rgb) / 0.08);
        }

        /* =========================
           MOBILE: table -> real card rows (robust)
           ========================= */
        @media (max-width: 576px) {
            .catalog-section {
                padding: 0.75rem;
            }

            /* thead gizle */
            .catalog-table thead {
                display: none;
            }

            /* ✅ table sistemini block'a çevir (mobilde kırılmasın) */
            .catalog-table,
            .catalog-table tbody {
                display: block;
                width: 100%;
            }

            /* table-responsive mobilde yatay scroll yapmasın */
            .catalog-table-wrap.table-responsive {
                overflow-x: visible;
            }

            /* ✅ satırı kart gibi */
            .catalog-table tbody tr {
                display: grid;
                width: 100%;
                grid-template-columns: 64px 1fr;
                gap: 10px;

                padding: 10px;
                border-bottom: 1px solid var(--surface-border);
            }

            .catalog-table tbody tr:last-child {
                border-bottom: 0;
            }

            .catalog-table tbody td {
                display: block;
                width: 100%;
                border: 0 !important;
                padding: 0 !important;
                background: transparent !important;
            }

            /* thumb */
            .catalog-table tbody td:first-child {
                grid-row: 1 / span 3;
                grid-column: 1;
                align-self: start;
            }

            /* ürün adı/açıklama */
            .catalog-table tbody td:nth-child(2) {
                grid-column: 2;
                min-width: 0;
                /* ✅ truncate düzgün */
            }

            /* desktop fiyat/puan kolonları gizli */
            .catalog-table tbody td:nth-child(3),
            .catalog-table tbody td:nth-child(4) {
                display: none !important;
            }

            /* aksiyon */
            .catalog-table tbody td:nth-child(5) {
                grid-column: 2;
                margin-top: 8px;
                text-align: left !important;
            }

            .btn-catalog-primary {
                width: 100%;
            }
        }
    </style>

    @foreach($categories as $cat)
        <div class="catalog-section">

            <div
                class="catalog-section-head d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 mb-2">
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
                <div class="catalog-table-wrap table-responsive">
                    <table class="table catalog-table align-middle">
                        <thead>
                            <tr class="small text-body-secondary">
                                <th style="width:72px;">Görsel</th>
                                <th>Ürün</th>
                                <th style="width:140px;">Fiyat</th>
                                <th style="width:170px;">Puan</th>
                                <th style="width:170px;" class="text-end">İşlem</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($cat->products as $p)
                                @php
                                    $img = $p->image ? asset($p->image) : null;
                                    $price = number_format((float) $p->price, 2, ',', '.');
                                    $currency = $p->currency?->value ?? 'TRY';
                                    $stars = max(0, min(5, (int) $p->rating_stars));
                                @endphp

                                <tr>
                                    <td>
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $p->name }}" class="catalog-thumb">
                                        @else
                                            <div class="catalog-thumb-ph">
                                                <i class="fa-regular fa-image text-body-secondary"></i>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fw-medium">{{ $p->name }}</div>

                                        @if($p->description)
                                            <div class="small text-body-secondary text-truncate" style="max-width: 720px;">
                                                {{ strip_tags($p->description) }}
                                            </div>
                                        @endif

                                        {{-- Mobil detay (fiyat + yıldız) --}}
                                        <div class="d-sm-none mt-2 small text-body-secondary">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div>
                                                    <span class="fw-semibold">Fiyat:</span> {{ $price }} {{ $currency }}
                                                </div>
                                                <div class="rating-stars" aria-label="Puan {{ $stars }} / 5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fa-solid fa-star {{ $i <= $stars ? 'is-on' : '' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="d-none d-sm-table-cell">
                                        <span class="fw-semibold">{{ $price }}</span>
                                        <span class="text-body-secondary small">{{ $currency }}</span>
                                    </td>

                                    <td class="d-none d-sm-table-cell">
                                        <div class="rating-stars" aria-label="Puan {{ $stars }} / 5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $stars ? 'is-on' : '' }}"></i>
                                            @endfor
                                            <span class="rating-num">{{ $stars }}/5</span>
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        @if($p->external_url)
                                            <a class="btn btn-sm btn-catalog-primary" href="{{ $p->external_url }}" target="_blank"
                                                rel="nofollow noopener">
                                                İncele <i class="fa-solid fa-up-right-from-square ms-1"></i>
                                            </a>
                                        @else
                                            <span class="text-body-secondary small">Link yok</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            @endif

        </div>
    @endforeach

</div>
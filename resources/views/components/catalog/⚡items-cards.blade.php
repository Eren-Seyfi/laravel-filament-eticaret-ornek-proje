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

<div class="catalog-items-cards">

    <style>
        /* =========================
           SECTION
           ========================= */
        .catalog-card-section {
            border: 1px solid var(--surface-border);
            background: var(--surface-bg);
            border-radius: 1rem;
            padding: 0.85rem;
        }

        .catalog-card-section+.catalog-card-section {
            margin-top: 0.85rem;
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
        }

        /* =========================
           GRID: web = sığdığı kadar
           ========================= */
        .catalog-grid {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        /* =========================
           IMAGE ONLY (NO CARD, NO HOVER)
           ========================= */
        .catalog-grid a {
            display: block;
            text-decoration: none;
        }

        .catalog-grid img {
            width: 100%;
            height: auto;
            display: block;

            object-fit: contain;
            /* kırpma yok */
            background: transparent;
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

        /* =========================
           MOBILE: sadece 3 yan yana
           ========================= */
        @media (max-width: 575.98px) {
            .catalog-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }
        }
    </style>

    @foreach($categories as $cat)
        <div class="catalog-card-section">

            <!--             <div class="catalog-card-head d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold d-flex align-items-center gap-2">
                        <span class="catalog-dot"></span>
                        <span>{{ $cat->name }}</span>
                    </div>

                    <a class="btn btn-sm btn-outline-secondary" href="{{ url('/category/' . $cat->slug) }}" wire:navigate>
                        Kategoriye git
                    </a>
                </div> -->

            @php
                $items = $cat->products
                    ->filter(fn($p) => $p->external_url && $p->image)
                    ->values();
            @endphp

            @if($items->isEmpty())
                <div class="catalog-empty">
                    <strong>Ürün yok</strong>
                </div>
            @else
                <div class="catalog-grid">
                    @foreach($items as $p)
                        <a href="{{ $p->external_url }}" target="_blank" rel="nofollow noopener" aria-label="{{ $p->name }}">
                            <img src="{{ asset($p->image) }}" alt="{{ $p->name }}" loading="lazy" decoding="async">
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    @endforeach

</div>
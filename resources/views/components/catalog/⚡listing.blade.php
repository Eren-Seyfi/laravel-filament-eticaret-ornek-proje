<?php

use Livewire\Component;
use App\Models\Category;

new class extends Component {
    public $categories;

    public bool $onlyHomepage = false;
    public bool $onlyActive = true;

    public ?int $categoryId = null;

    public function mount(
        bool $onlyHomepage = false,
        bool $onlyActive = true,
        ?int $categoryId = null,
    ): void {
        $this->onlyHomepage = $onlyHomepage;
        $this->onlyActive = $onlyActive;
        $this->categoryId = $categoryId;

        $this->loadData();
    }

    private function loadData(): void
    {
        $q = Category::query();

        if (!is_null($this->categoryId)) {
            $q->whereKey($this->categoryId);
        }

        if ($this->onlyActive) {
            $q->where('is_active', true);
        }

        if ($this->onlyHomepage) {
            $q->where('show_on_homepage', true);
        }

        $this->categories = $q
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with([
                'products' => function ($q) {
                    if ($this->onlyActive) {
                        $q->where('is_active', true);
                    }

                    $q->orderBy('sort_order')
                        ->orderBy('name');
                }
            ])
            ->get();
    }
};
?>

<div class="catalog-listing app-surface p-3 p-lg-4">

    <style>
        /* =========================
           LISTING (theme-native like chips/offcanvas)
           ========================= */
        .catalog-title {
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .catalog-title-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(135deg,
                    rgb(var(--accent-2-rgb) / 0.95),
                    rgb(var(--accent-rgb) / 0.95));
            box-shadow: 0 0 0 6px rgb(var(--accent-rgb) / 0.10);
        }

        /* Empty (theme like) */
        .catalog-empty {
            border: 1px dashed rgb(var(--accent-2-rgb) / 0.35);
            background: rgb(var(--accent-2-rgb) / 0.06);
            border-radius: 0.9rem;
            padding: 0.85rem 1rem;
        }

        [data-bs-theme="dark"] .catalog-empty {
            background: rgb(var(--accent-2-rgb) / 0.08);
        }

        /* Mobil */
        @media (max-width: 576px) {
            .catalog-title-dot {
                display: none;
            }
        }
    </style>

    <div
        class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <div class="catalog-title h5 mb-1">
                <span class="catalog-title-dot" aria-hidden="true"></span>

                @if($categoryId)
                    <span>Kategori Ürünleri</span>
                @elseif($onlyHomepage)
                    <span>Anasayfa</span>
                @else
                    <span>Katalog</span>
                @endif
            </div>

            <div class="text-body-secondary small">
                @if($categoryId)
                    Bu kategoriye ait ürünler listelenir.
                @elseif($onlyHomepage)
                    Sadece anasayfada gösterilecek kategoriler ve ürünler listelenir.
                @else
                    Kategoriler ve ürünler listelenir.
                @endif
            </div>
        </div>
    </div>

    @if(empty($categories) || $categories->isEmpty())
        <div class="catalog-empty d-flex align-items-start gap-2">
            <i class="fa-regular fa-circle-info mt-1"></i>
            <div class="small">
                <div class="fw-semibold">İçerik bulunamadı</div>
                <div class="text-body-secondary">Bu alanda listelenecek kategori/ürün yok.</div>
            </div>
        </div>
    @else
        {{-- ✅ Sadece kart görünümü --}}
        <livewire:catalog.items-cards :categories="$categories" />
    @endif

</div>
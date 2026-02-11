<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Enums\SocialPlatform;

$settings = Setting::cached();

$siteName = $settings->site_name ?: config('app.name');
$tagline = $settings->site_tagline ?: null;

$pageTitle = $title ?? ($settings->seo_title ?: $siteName);
$metaDescription = $description ?? ($settings->seo_description ?: $tagline);
$metaKeywords = $keywords ?? ($settings->seo_keywords ?: null);

$currentUrl = url()->current();
$canonicalUrl = $canonical ?? $currentUrl;

$noindex = (bool) ($settings->seo_noindex || !$settings->search_engine_indexing);
$nofollow = (bool) ($settings->seo_nofollow || !$settings->search_engine_indexing);
$robots = ($noindex ? 'noindex' : 'index') . ',' . ($nofollow ? 'nofollow' : 'follow');

/** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
$disk = Storage::disk('public_root');

$faviconUrl = $settings->favicon_path ? $disk->url($settings->favicon_path) : null;
$defaultOgImg = $settings->og_image_path ? $disk->url($settings->og_image_path) : null;

$ogImageUrl = $og_image ?? ($defaultOgImg ?: null);
$ogType = $og_type ?? 'website';

$isAdminArea = request()->is('admin*') || request()->is('filament*') || request()->is('livewire*');
$shouldShowMaintenancePage = (bool) $settings->maintenance_mode && !$isAdminArea;

if ($shouldShowMaintenancePage) {
    $pageTitle = 'Bakım Modu - ' . $siteName;
    $robots = 'noindex,nofollow';
}

$logoUrl = !empty($settings->logo_path) ? $disk->url($settings->logo_path) : null;

/** @var array<int, array{platform?: string, url?: string, icon?: string}> $socialLinks */
$socialLinks = is_array($settings->social_links) ? $settings->social_links : [];

$categories = Category::query()
    ->where('is_active', true)
    ->orderBy('sort_order')
    ->orderBy('name')
    ->get(['name', 'slug']);
    @endphp

    <meta name="robots" content="{{ $robots }}">
    <title>{{ $pageTitle }}</title>
    <link rel="canonical" href="{{ $canonicalUrl }}">

    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif

    @if (!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if (!empty($metaKeywords))
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif

    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    @if (!empty($metaDescription))
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
        <meta property="og:image:alt" content="{{ $pageTitle }}">
    @endif

    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    @if (!empty($metaDescription))
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif

    @if (!empty($settings->header_scripts))
        {!! $settings->header_scripts !!}
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />


    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100 bg-body text-body app-theme-bg">
    @if ($shouldShowMaintenancePage)

        @livewire('pages::maintenance')

    @else

        {{-- ✅ NAVBAR --}}
        <header class="border-bottom bg-body">
            <div class="container py-2 d-flex align-items-center justify-content-between gap-2">

                {{-- Mobile toggler --}}
                <button class="btn btn-outline-secondary nav-toggle-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#categoriesOffcanvas" aria-controls="categoriesOffcanvas" aria-label="Kategoriler">
                    <i class="fa-solid fa-bars"></i>
                </button>

                {{-- Brand --}}
                <a href="{{ url('/') }}" wire:navigate
                    class="d-flex align-items-center gap-2 text-decoration-none text-body">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" style="height: 32px;">
                    @else
                        <span class="fw-semibold">{{ $siteName }}</span>
                    @endif
                </a>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ url('/admin') }}" class="btn admin-btn d-inline-flex align-items-center gap-2"
                            aria-label="Admin Paneli" title="Admin Paneli">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span class="d-none d-md-inline">Admin</span>
                        </a>
                    @endauth

                    <button type="button" class="btn theme-btn" id="themeToggleBtn" aria-label="Tema">
                        <i class="fa-solid fa-circle-half-stroke" id="themeToggleIcon"></i>
                    </button>

                    @if (!empty($socialLinks))
                        <div class="d-none d-sm-flex align-items-center gap-1">
                            @foreach ($socialLinks as $item)
                                @php
            $url = $item['url'] ?? null;
            $platform = $item['platform'] ?? null;
            $icon = $item['icon'] ?? SocialPlatform::iconFor($platform);
                                @endphp
                                @if ($url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="social-icon-link"
                                        aria-label="{{ $platform ? ucfirst($platform) : 'Link' }}"
                                        title="{{ $platform ? ucfirst($platform) : 'Link' }}">
                                        <i class="{{ $icon }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- Desktop category chips --}}
            <div class="border-top d-none d-lg-block">
                <div class="container py-2">
                    @if ($categories->isNotEmpty())
                        <ul class="navbar-nav flex-row flex-wrap gap-2">
                            @foreach ($categories as $cat)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/category/' . $cat->slug) }}" wire:navigate>
                                        <span class="nav-dot" aria-hidden="true"></span>
                                        <span>{{ $cat->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-body-secondary small">Henüz kategori yok</div>
                    @endif
                </div>
            </div>



        </header>

        {{-- ✅ OFFCANVAS --}}
        <div class="offcanvas offcanvas-start" tabindex="-1" id="categoriesOffcanvas"
            aria-labelledby="categoriesOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="categoriesOffcanvasLabel">Kategoriler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
            </div>

            <div class="offcanvas-body">
                @if ($categories->isNotEmpty())
                    <div class="list-group">
                        @foreach ($categories as $cat)
                            <a href="{{ url('/category/' . $cat->slug) }}" wire:navigate data-close-offcanvas="1"
                                class="list-group-item list-group-item-action bg-body text-body">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-body-secondary small">Henüz kategori yok</div>
                @endif

                {{-- Social (mobile/offcanvas) --}}
                @if (!empty($socialLinks))
                    <hr class="my-4">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($socialLinks as $item)
                            @php
            $url = $item['url'] ?? null;
            $platform = $item['platform'] ?? null;
            $icon = $item['icon'] ?? SocialPlatform::iconFor($platform);
                            @endphp
                            @if ($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="social-icon-link"
                                    aria-label="{{ $platform ? ucfirst($platform) : 'Link' }}"
                                    title="{{ $platform ? ucfirst($platform) : 'Link' }}">
                                    <i class="{{ $icon }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ✅ PAGE GRID (left / center / right) --}}
        <main class="py-3">
            <div class="container-fluid">
                <div class="row g-3 align-items-start">

                    {{-- LEFT --}}
                    <aside class="d-none d-lg-block col-lg-2">
                        <div class="sidebar-sticky">
                            <div class="advert-slot">
                                <livewire:adverts.left />
                            </div>
                        </div>
                    </aside>

                    {{-- CENTER --}}
                    <section class="col-12 col-lg-8">

                        {{-- ✅ TOP SLOT center içinde --}}
                        <div class="mb-3">
                            <livewire:adverts.top />
                        </div>

                        {{-- ✅ FRONT SLOT center içinde --}}
                        <div class="mb-3">
                            <livewire:adverts.front />
                        </div>

                        {{-- ✅ CONTENT --}}
                        <div class=" p-3 p-lg-4 mb-3">
                            {{ $slot }}
                        </div>

                        {{-- ✅ BOTTOM SLOT center içinde --}}
                        <div class="mb-3">
                            <livewire:adverts.bottom />
                        </div>

                    </section>

                    {{-- RIGHT --}}
                    <aside class="d-none d-lg-block col-lg-2">
                        <div class="sidebar-sticky">
                            <div class="advert-slot">
                                <livewire:adverts.right />
                            </div>
                        </div>
                    </aside>

                </div>
            </div>
        </main>

        {{-- ✅ POPUP (overlay olduğu için body sonunda ve main dışı en temiz) --}}
        
        @persist('adverts-popup')
        <livewire:adverts.popup />
        @endpersist

        {{-- ✅ FOOTER --}}
        <footer class="mt-auto border-top bg-body">
            <div
                class="container py-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="small text-body-secondary">
                    © {{ now()->year }} <span class="fw-semibold text-body">{{ $siteName }}</span>
                    @if ($tagline)
                        <span class="d-none d-md-inline"> — {{ $tagline }}</span>
                    @endif
                </div>

                @if (!empty($socialLinks))
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($socialLinks as $item)
                            @php
            $url = $item['url'] ?? null;
            $platform = $item['platform'] ?? null;
            $icon = $item['icon'] ?? SocialPlatform::iconFor($platform);
                            @endphp
                            @if ($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="social-icon-link"
                                    aria-label="{{ $platform ? ucfirst($platform) : 'Link' }}"
                                    title="{{ $platform ? ucfirst($platform) : 'Link' }}">
                                    <i class="{{ $icon }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </footer>

    @endif

    @if (!empty($settings->footer_scripts))
        {!! $settings->footer_scripts !!}
    @endif

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php

use Livewire\Component;
use App\Models\Category;

new class extends Component {
    public Category $category;

    public function mount(string $slug): void
    {
        $this->category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        // ✅ Layout’a $title ve diğerlerini verir
        return $this->view()
            ->title($this->category->name)
            ->with([
                // ✅ Layout’ta kullandığın override değişkenleri
                'description' => $this->category->name . ' kategorisindeki içerikleri keşfedin.',
                'keywords' => $this->category->name, // istersen genişletirsin
                'canonical' => route('category', $this->category->slug),
            ]);
    }
};
?>

<div class="mx-auto max-w-6xl px-4 py-6">
    <livewire:catalog.listing :categoryId="$category->id" />
</div>
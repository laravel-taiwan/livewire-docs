* [分頁資料](#paginating-data)
* [篩選資料後重置分頁](#resetting-pagination)
* [同一頁面上使用多個分頁器](#multiple-paginators)
* [使用 Bootstrap 分頁主題](#bootstrap-theme)
* [使用自訂分頁視圖](#custom-pagination-view)

Livewire 提供了在元件內部對結果進行分頁的功能。此功能與 Laravel 的原生分頁功能相連，因此對您來說應該感覺像是一個隱形功能。

## 分頁資料 {#paginating-data}

假設您有一個 `show-posts` 元件，但您希望將結果限制為每頁 10 篇文章。

您可以使用 Livewire 提供的 `WithPagination` 特性來對結果進行分頁。

@component('components.code-component')
@slot('class')
@verbatim
use Livewire\WithPagination;

class ShowPosts extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => Post::paginate(10),
        ]);
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    @foreach ($posts as $post)
        ...
    @endforeach

    {{ $posts->links() }}
</div>
@endverbatim
@endslot
@endcomponent

現在在您的文章底部將呈現不同頁面的 HTML 連結，並且結果將被分頁。

## 篩選資料後重置分頁 {#resetting-pagination}

在篩選分頁結果集時的常見模式是在應用篩選時將當前頁面重置為 "1"。

例如，如果使用者訪問資料集的第 "4" 頁，然後在搜尋欄位中輸入以縮小結果，通常希望將頁面重置為 "1"。

Livewire 的 `WithPagination` 特性公開了一個 `->resetPage()` 方法來實現此目的。

此方法可以與 `updating/updated` 生命週期鉤子結合使用，以在更新某些元件資料時重置頁面。

如果分頁名稱設置為除 `page` 以外的任何其他值，則可以傳遞一個可選的頁面名稱參數。

```php
class ShowPosts extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => Post::where('title', 'like', '%'.$this->search.'%')->paginate(10),
        ]);
    }
}
@endverbatim
@endcomponent

## 同一頁面上有多個分頁器 {#multiple-paginators}

因為 Livewire 在 `WithPagination` 特性中硬編碼了 `$page` 屬性，所以無法在同一頁面上擁有兩個不同的分頁器，因為每個分頁器都會競爭 URL 欄中相同的屬性名稱。

以下是同一頁面上可能存在的兩個不同元件的示例。通過為第二個元件（評論元件）指定名稱，Livewire 將會識別並適當處理。

@component('components.code', ['lang' => 'php'])
class ShowPosts extends Livewire\Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => Post::paginate(10),
        ]);
    }
}
@endcomponent

@component('components.code', ['lang' => 'php'])
class ListPostComments extends Livewire\Component
{
    use WithPagination;

    public Post $post;

    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => $post->comments()->paginate(10, ['*'], 'commentsPage'),
        ]);
    }
}
@endcomponent

現在在查詢字串中，兩個分頁器將如下所示表示：

@component('components.code', ['lang' => 'html'])
?page=2&commentsPage=3
@endcomponent

要重置特定分頁器，您可以使用 `->resetPage()` 方法並傳遞您的自定義頁面名稱，如 `WithPagination` 特性中所示。

@component('components.code', ['lang' => 'php'])
@verbatim
class ListPostComments extends Livewire\Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage('commentsPage');
    }
```

```php
    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => $post->comments()->where('title', 'like', '%'.$this->search.'%')->paginate(10, ['*'], 'commentsPage'),
        ]);
    }
}
```

## 使用 Bootstrap 分頁主題 {#bootstrap-theme}
與 Laravel 一樣，Livewire 的預設分頁視圖使用 Tailwind 類來進行樣式設定。如果您在應用程式中使用 Bootstrap，您可以在組件上使用 `$paginationTheme` 屬性啟用 Bootstrap 主題以供分頁視圖使用。

```php
class ShowPosts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
}
```

## 使用自訂分頁視圖 {#custom-pagination-view}

Livewire 提供了 3 種自訂分頁連結 Blade 視圖的方法，當調用 `$results->links()` 時會呈現。

**方法 A**：直接將視圖名稱傳遞給 `->links()` 方法。

```php
<div>
    @foreach ($posts as $post)
        ...
    @endforeach

    {{ $posts->links('custom-pagination-links-view') }}
</div>
```

**方法 B**：在您的組件中覆寫 `paginationView()` 方法。

```php
class ShowPosts extends Component
{
    use WithPagination;

    ...

    public function paginationView()
    {
        return 'custom-pagination-links-view';
    }

    ...
}
```

**方法 C**：發佈 Livewire 分頁視圖。

您可以使用以下 artisan 命令將 Livewire 分頁視圖發佈到 <code>resources/views/vendor/livewire</code>：

```bash
php artisan livewire:publish --pagination
```

**注意**：不幸的是，Livewire 將會覆蓋您在服務提供者中使用 <code>Paginator::defaultView()</code> 定義的自訂視圖。

在使用任一方法時，您應該在分頁組件中使用 `wire:click` 處理程序，而不是錨點標籤，使用以下方法：

- `nextPage` 用於導航到下一頁
- `previousPage` 用於導航到上一頁
- `gotoPage($page)` 用於導航到特定頁面。

請參見下面的示例，了解預設的 Livewire 分頁器的工作原理。

@component('components.code', ['lang' => 'php'])
@verbatim
<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
            <span>
                {{-- 上一頁連結 --}}
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif
            </span>

            <span>
                {{-- 下一頁連結 --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>
@endverbatim
@endcomponent

Please paste the Markdown content you'd like me to translate into traditional Chinese.

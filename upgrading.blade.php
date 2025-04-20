* [V2 正式推出！🎉](#v2-is-here)
* [更新您的 Composer 版本](#update-your-composer-version)
* [更新您的 Alpine 版本](#update-your-alpine-verion)
* [更新您的應用程式代碼](#update-your-application-code)
    * [已更新：將 `$updatesQueryString` 改為 `$queryString`](#query-string)
    * [已移除：Route::livewire()](#route-livewire)
* [已移除：Turbolinks 支援](#turbolinks)
* [已更改：`assertSet()`](#assert-set)
* [已移除：屬性轉換器](#casters)
* [已更新：分頁檢視](#pagination)
* [已更新：JavaScript 鉤子](#hooks)
* [已更新：VueJS 支援](#vuejs)
* [簽出](#signing-off)

## V2 正式推出！🎉 {#v2-is-here}

在我們深入技術升級的內容之前，您可能會對這些變更背後的哲學基礎感興趣。

* **Livewire 是聲明式的。** Livewire 不是提供無盡的前端交互工具，而是旨在使前端交互成為您狀態（即組件屬性）的“副作用”。例如，使用新的 `$queryString` API，您不需要提供手動從後端更新瀏覽器查詢字串的方法，而是使用 `$queryString` 屬性聲明您希望在前端查詢字串中反映的組件屬性。
* **Livewire 反對樣板代碼。** 通過允許開發人員將 Eloquent 模型設置為屬性並直接將 `wire:model`（綁定）到它們，我們能夠刪除大量樣板代碼。為了進一步減少樣板代碼，在 V2 中，組件參數現在通過匹配其名稱自動分配給公共屬性。現在，`mount()` 方法僅用於必須使用的事項，而不僅僅是將參數轉發給屬性。消除噪音。
* **Livewire 本質上是一個後端接口**。 `wire:click` 等內容只是使界面易於使用的糖。隨著 `$wire` 的添加，底層功能現在變得明顯：Livewire 允許您直接且聲明性地與後端代碼進行交互，無需使用像 axios.post()、RESTful 端點、控制器等命令式/樣板化模式...
* **Livewire 使用起來簡單**。 在我所持有的所有理念中，這是我最堅定的。Livewire 應始終保持極其易於使用。我的目標是您可以輕鬆記住並幾乎猜測其 API。在引入任何功能之前，我會仔細查看 Laravel 中現有的模式和 API，看看 Livewire 是否可以利用這些共享知識作為新採用者的槓桿。一個小例子是新的 `$rules` 屬性。我可以給它取任何名字，但為什麼我不將其命名為除了 `$rules` 之外的任何名字（這是 Laravel 中 Request 物件設定的先例）？如果我認為一個 API 不易用、直觀和清晰，我會等待該功能，讓它慢慢浸漬，直到出現清晰且美麗的東西。（或者至少這是我的目標。）

## 更新您的 Composer 版本 {#update-your-composer-version}

1. 將您的 `composer.json` 檔案中的 `livewire/livewire` 依賴更新為 `^2.0`
2. 執行 `composer update livewire/livewire`
3. 執行 `php artisan view:clear`
4. 執行 `php artisan livewire:publish --assets`（如果您之前已發佈過資源）

## 更新您的 Alpine 版本 {#update-your-alpine-verion}

如果您正在使用 [AlpineJS](https://github.com/alpinejs/alpine) 與 Livewire V2，請確保您的版本為 `2.7.0` 或更高。

**例如：**
@component('components.code', ['lang' => 'blade'])
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.x/dist/alpine.min.js" defer></script>
@endcomponent

## 更新您的應用程式代碼 {#update-your-application-code}

以下是破壞性更改及其升級說明，按影響程度排序：

1. [已更新：`$updatesQueryString` 至 `$queryString`](#query-string)
1. [已移除：Route::livewire()](#route-livewire)
1. [已移除：Turbolinks 支援](#turbolinks)
1. [已更改：`assertSet()`](#assert-set)
1. [已移除：屬性轉換器](#casters)
1. [已更新：分頁視圖](#pagination)
1. [已更新：JavaScript 鉤子](#hooks)
1. [已更新：VueJs 支援](#vuejs)

### 已更新：`$updatesQueryString` 至 `$queryString` {#query-string}
Livewire 1.x 具有一個更基本的實用工具，用於根據屬性值操作瀏覽器的查詢字串。在 V2 中，有一個更先進的工具，用於操作查詢字串。

第一個破壞性更改是將 `$updatesQueryString` 更改為 `$queryString`：

@component('components.code', ['lang' => 'php'])
@verbatim
class Search extends Component
{
    // 之前
    protected $updatesQueryString = ['search']

    // 現在
    protected $queryString = ['search']
}
@endverbatim
@endcomponent

除了新的屬性名稱外，內部運作有兩個重大變化：

1. 現在屬性值在頁面加載時會自動設置為查詢字串中的初始值
1. 查詢字串系統現在使用瀏覽器的 `history.pushState` API 而不是 `history.replaceState`（這意味著您現在可以點擊瀏覽器的返回按鈕以重新訪問舊的查詢字串更改）

因為查詢字串系統現在會自動設置初始值，所以在 `mount()` 方法中不再需要這樣做：

@component('components.code', ['lang' => 'php'])
@verbatim
class Search extends Component
{
    ...

    public function mount()
    {
        // 不再需要像這樣的程式碼。
        // 現在將自動設置搜尋屬性。
        $this->search = request()->query('search', '');
    }
}
@endverbatim
@endcomponent

### 移除：Route::livewire() {#route-livewire}
Livewire 1.x 允許您使用 `Route::livewire()` 方法在整個頁面的路由中註冊一個組件。Livewire 2.0 現在允許您直接將 Livewire 組件傳遞到路由中，使用標準的 `Route::get()` 方法和完全合格的命名空間。

@component('components.code', ['lang' => 'php'])
@verbatim
// 之前
Route::livewire('/post', 'show-posts');

// 現在
Route::get('/post', \App\Http\Livewire\ShowPosts::class);
@endverbatim
@endcomponent

首先要注意的是，如果您使用的是 Laravel 7，您需要從 `app/Providers/RouteServiceProvider.php` 中刪除 `namespace(...)` 行：

@component('components.code', ['lang' => 'php'])
@verbatim
protected function mapWebRoutes()
{
    Route::middleware('web')
        ->namespace($this->namespace) // 刪除我
        ->group(base_path('routes/web.php'));
}
@endverbatim
@endcomponent

這在 Laravel 8 中是默認的，但如果您使用的是 Laravel 7，您需要刪除這個才能將 Livewire 類傳遞給 `Route::get()`。否則，Laravel 將在傳遞給 `Route::get()` 的所有類前面加上一個命名空間。

在 1.x 中，Livewire 默認使用位於 `resources/layouts/app.blade.php` 中的傳統 Blade 佈局來呈現您的頁面級組件。在 2.0 中，Livewire 使用相同的佈局文件作為默認，但現在預期您在佈局中使用新的 Blade 組件 `$slot` 語法。例如：

@component('components.code', ['lang' => 'blade'])
@verbatim
<!-- 之前 -->
<html>
    <body>
        @yield('content')

```html
<html>
    <body>
        {{ $slot }}

        @livewireScripts
    </body>
</html>
@endverbatim
@endcomponent

如果您在路由文件中手動配置了路由的佈局，`->layout()` 方法現在已移至一個名為 `->extends()` 的新方法中，並放置在 render 函數中。

@component('components.code', ['lang' => 'php'])
@verbatim
// 之前
Route::livewire('/post', ShowPosts::class)
    ->layout('layouts.base')
    ->section('body');

// 之後
class ShowPosts extends Component
{
    public function render()
    {
        return view('livewire.show-posts')
            ->extends('layouts.base')
            ->section('body');
    }
}
@endverbatim
@endcomponent

如果您希望將手動配置的佈局更新為新的 `$slot` 語法，您可以使用新的 `->layout()` 方法指定它們。此方法將默認使用 `$slot`，但您也可以使用 `->slot()` 方法配置組件以呈現到具名插槽：

@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPosts extends Component
{
    public function render()
    {
        return view('livewire.show-posts')
            ->layout('layouts.base')
            ->slot('body');
    }
}
@endverbatim
@endcomponent

## 已移除：Turbolinks 支援 {#turbolinks}
Livewire 不再直接支持 Turbolinks。

如果您希望在 Livewire 應用中繼續使用 Turbolinks，您將需要在 Livewire 的 JavaScript 資源旁邊包含 [Turbolinks adapter](https://github.com/livewire/turbolinks)：

@component('components.code', ['lang' => 'blade'])
@verbatim
...
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false"></script>
</body>
@endverbatim
@endcomponent

由於這個適配器是新的，如果遇到與 Turbolinks 功能相關的問題，請在 [適配器的存儲庫](https://github.com/livewire/turbolinks) 上提交問題。
```

## 已更改：`assertSet()` {#assert-set}

在 Livewire V1 中，測試方法 `assertSet('property', 'value')` 是針對 JavaScript 安全的 Livewire 載荷中的數據進行測試，而不是針對實際 Livewire 組件的 PHP 實例上屬性的值進行斷言。這使得無法從 `assertSet()` 中測試計算屬性。

在 V2 中，`assertSet()` 現在的行為符合您的預期：對實際 PHP 實例中的數據進行斷言，如果您想對載荷數據進行斷言，現在可以使用新的 `assertPayloadSet()`。

對於大多數人來說，這不會有任何變化。但是，如果在升級過程中您的測試套件中出現了關於 `assertSet()` 的失敗，您應該重新設計您的測試，或者使用 `assertPayloadSet()`。

## 已移除：屬性轉換器 {#casters}
在 Livewire V2 中已刪除屬性轉換器。這個決定有三個原因：

1. 大多數人主要用於 `Collection` 和 `DateTime` 類型的屬性。這些現在已經自動轉換
1. 沒有多少用戶使用（甚至知道）這個功能
1. 有其他方法可以實現完全相同的功能

以下是一些示例：
@component('components.code', ['lang' => 'php'])
@verbatim
// 之前
public $foo;

protected $casts = ['foo' => 'collection'];

public function mount()
{
    $this->foo = collect(['foo', 'bar']);
}

// 現在
// (現在自動轉換集合)
public $foo;

public function mount()
{
    $this->foo = collect(['foo', 'bar']);
}
@endverbatim
@endcomponent

@component('components.code', ['lang' => 'php'])
@verbatim
// 之前
class AllCaps implements Castable {
    public function cast($value)
    {
        return strtoupper($value);
    }

    public function uncast($value)
    {
        return strtolower($value);
    }
}

class SomeComponent extends Component
{
    public $foo;

    protected $casts = ['foo' => AllCaps::class];

    ....
}

// 現在
class SomeComponent extends Component
{
    public $foo;

    public function hydrateFoo($value)
    {
        $this->foo = strtoupper($value);
    }

```php
    public function dehydrateFoo($value)
    {
        $this->foo = strtolower($value);
    }

    ....
}
```

## 更新：分頁視圖 {#pagination}
如果您已經將結果分頁並將 `WithPagination` 添加到組件中，並依賴於使用 `$posts->links()` 顯示默認的 Livewire 分頁連結視圖，則視圖已從 Bootstrap-4 更新為 Tailwind。

Livewire V2 仍然支持 Bootstrap-4 分頁，但您必須在組件上使用 `$paginationTheme` 屬性進行配置：

```php
class ShowPosts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    ...
}
```

即使 V2 仍然支持 Bootstrap-4，分頁視圖已更新以匹配 Laravel 8。因此，它與 V1 中先前使用的視圖略有不同。要使用從 V1 中精確的視圖：

1. 從 GitHub 複製視圖源碼 [from GitHub](https://raw.githubusercontent.com/livewire/livewire/1.x/src/views/pagination-links.blade.php)
2. 將其粘貼到任何您認為合適的新 blade 文件中。例如，我們將說：`resources/views/pagination-links.blade.php`
3. 現在通過將其傳遞給 `->links()` 方法在您的 Blade 視圖中引用它：

```php
{{ $posts->links('pagination-links') }}
```

## 更新：JavaScript 鉤子 {#hooks}
V2 提供了與 V1 相同的 JavaScript 鉤子，但有三個明顯的更新：

1. 它們的名稱不同
2. 參數順序已更新以更一致
3. 在傳遞“DomElement”包裝器實例的地方，現在傳遞了本機 DOM 元素

這裡是用於比較的鉤子用法：

| V1 名稱 | V2 名稱 / 用法 |
| --- | --- |
| `livewire.hook('componentInitialized', (component) => {})` | `Livewire.hook('component.initialized', (component) => {})` |
| `livewire.hook('elementInitialized', (el, component) => {})` | `Livewire.hook('element.initialized', (el, component) => {})` |
| `livewire.hook('beforeElementUpdate', (from, to, component) => {})` | `Livewire.hook('element.updating', (fromEl, toEl, component) => {})` |
| `livewire.hook('afterElementUpdate', (node, component) => {})` | `Livewire.hook('element.updated', (el, component) => {})` |
| `livewire.hook('elementRemoved', (el, component) => {})` | `Livewire.hook('element.removed', (el, component) => {})` |
| `livewire.hook('messageSent', (component, message) => {})` | `Livewire.hook('message.sent', (message, component) => {})` |
| `livewire.hook('messageFailed', (component) => {})` | `Livewire.hook('message.failed', (message, component) => {})` |
| `livewire.hook('responseReceived', (component, response) => {})` | `Livewire.hook('message.received', (message, component) => {})` |
| `livewire.hook('afterDomUpdate', (component) => {})` | `Livewire.hook('message.processed', (message, component) => {})` |
| `livewire.hook('beforeDomUpdate', (component) => {})` | `Livewire.hook('message.received', (message, component) => {})` |
```

## 更新：VueJS 支援 {#vuejs}

如果您的 Livewire 目前依賴於 [vue-plugin](https://github.com/livewire/vue)，您將需要從版本 `0.2.x` 升級到 `0.3.x`

@component('components.code', ['lang' => 'blade'])
@verbatim
    ...
    @livewireScripts

    // Before
    <script src="https://cdn.jsdelivr.net/gh/livewire/vue@v0.2.x/dist/livewire-vue.js"></script>

    // After
    <script src="https://cdn.jsdelivr.net/gh/livewire/vue@v0.3.x/dist/livewire-vue.js"></script>
</body>
@endverbatim
@endcomponent

## 簽退 {#signing-off}
希望這次升級對您的影響不大。

如果您有任何問題或對本文檔進行更正，請在存儲庫上[提交 GitHub 問題。](https://github.com/livewire/livewire/issues/new/choose)

一如既往，感謝您的支持，並感謝您使用 Livewire！

- Caleb

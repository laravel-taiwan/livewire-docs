* [內嵌元件](#inline-components)
  * [參數](#parameters)
* [全頁元件](#page-components)
  * [配置佈局元件](#custom-layout)
  * [路由參數](#route-params)
  * [路由模型綁定](#route-model-binding)
* [渲染方法](#render-method)
  * [返回 Blade 視圖](#returning-blade)
  * [返回模板字串](#returning-strings)

## 內嵌元件 {#inline-components}

在頁面上呈現 Livewire 元件的最基本方式是使用 `<livewire:` 標籤語法：

@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    <livewire:show-posts />
</div>
@endverbatim
@endcomponent

或者您可以使用 `@@livewire` Blade 指令：

@component('components.code', ['lang' => 'blade'])
@verbatim
    @livewire('show-posts')
@endverbatim
@endcomponent

如果您有一個位於子文件夾中並具有自己命名空間的元件，您必須使用以該命名空間為前綴的點（`.`）。

例如，如果我們在 `app/Http/Livewire/Nav` 文件夾中有一個 `ShowPosts` 元件，我們應該這樣指定：

@component('components.code', ['lang' => 'blade'])
@verbatim
<livewire:nav.show-posts />
@endverbatim
@endcomponent

### 參數 {#parameters}

#### 傳遞參數

您可以通過將額外參數傳遞給 <code><livewire:</code> 標籤來將數據傳遞給元件。

例如，假設我們有一個 `show-post` 元件。這是如何傳遞 `$post` 模型的方法。

@component('components.code', ['lang' => 'blade'])
@verbatim
<livewire:show-post :post="$post">
@endverbatim
@endcomponent

或者，這是使用 Blade 指令傳遞參數的方法。

@component('components.code', ['lang' => 'blade'])
@verbatim
@livewire('show-post', ['post' => $post])
@endverbatim
@endcomponent

#### 接收參數

Livewire 將自動將參數分配給匹配的公共屬性。

例如，在 @verbatim `<livewire:show-post :post="$post">` @endverbatim 的情況下，如果 `show-post` 元件有一個名為 `$post` 的公共屬性，它將被自動分配：

```php
@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPost extends Component
{
    public $post;

    ...
}
@endverbatim
@endcomponent

如果由於某種原因，這種自動行為對您不起作用，您可以使用 `mount()` 方法來截取參數：

@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPost extends Component
{
    public $title;
    public $content;

    public function mount($post)
    {
        $this->title = $post->title;
        $this->content = $post->content;
    }

    ...
}
@endverbatim
@endcomponent

@component('components.tip')
在 Livewire 元件中，您使用 <code>mount()</code> 代替類構造函數 <code>__construct()</code>，這可能是您習慣的方式。

注意：只有在元件首次安裝時才會調用 <code>mount()</code>，即使在刷新或重新渲染元件時也不會再次調用。
@endcomponent

像控制器一樣，您可以通過在傳入參數之前添加類型提示的參數來注入依賴項。

@component('components.code', ['lang' => 'php'])
@verbatim
use \Illuminate\Session\SessionManager;

class ShowPost extends Component
{
    public $title;
    public $content;

    public function mount(SessionManager $session, $post)
    {
        $session->put("post.{$post->id}.last_viewed", now());

        $this->title = $post->title;
        $this->content = $post->content;
    }

    ...
}
@endverbatim
@endcomponent

## 全頁元件 {#page-components}

如果頁面的主要內容是 Livewire 元件，您可以將該元件直接傳遞給 Laravel 路由，就像它是一個控制器一樣。

@component('components.code', ['lang' => 'php'])
@verbatim
Route::get('/post', ShowPosts::class);
@endverbatim
@endcomponent

默認情況下，Livewire 將 `ShowPosts` 元件呈現到位於 `resources/views/layouts/app.blade.php` 的 blade 佈局元件的 `@{{ $slot }}` 中。

@component('components.code-component')
@slot('view')
@verbatim
<head>
    @livewireStyles
</head>
<body>
    {{ $slot }}
```

```markdown
    @livewireScripts
</body>
@endverbatim
@endslot
@endcomponent

如需更多關於Laravel元件的資訊，請[查看Laravel文件](https://laravel.com/docs/blade#components)。

### 配置佈局元件 {#custom-layout}

如果您想要指定除了 `layouts.app` 之外的預設佈局，您可以覆蓋 `livewire.layout` 組態選項。
@component('components.code', ['lang' => 'php'])
    'layout' => 'app.other_default_layout'
@endcomponent

如果您需要更多控制，您可以在從 `render()` 返回的視圖實例上使用 `->layout()` 方法。
@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPosts extends Component
{
    ...
    public function render()
    {
        return view('livewire.show-posts')
            ->layout('layouts.base');
    }
}
@endverbatim
@endcomponent

如果您的佈局有相關的類檔案，您將需要引用該檔案以進行任何自訂邏輯或屬性。
@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPosts extends Component
{
    ...
    public function render()
    {
        return view('livewire.show-posts')
            ->layout(\App\View\Components\BaseLayout::class);
    }
}
@endverbatim
@endcomponent

如果您在元件中使用非預設的插槽，您也可以鏈接 `->slot()`：

@component('components.code', ['lang' => 'php'])
public function render()
{
    return view('livewire.show-posts')
        ->layout('layouts.base')
        ->slot('main');
}
@endcomponent

或者，Livewire支援使用傳統的Blade佈局檔案與 `@@extends`。

給定以下佈局檔案：

@component('components.code')
@verbatim
<head>
    @livewireStyles
</head>
<body>
    @yield('content')

    @livewireScripts
</body>
@endverbatim
@endcomponent

您可以配置Livewire使用 `->extends()` 而不是 `->layout()` 來參考它：

@component('components.code', ['lang' => 'php'])
public function render()
{
    return view('livewire.show-posts')
        ->extends('layouts.app');
}
@endcomponent
```

如果您需要配置`@@section`以供元件使用，您也可以使用`->section()`方法進行配置：

@component('components.code', ['lang' => 'php'])
public function render()
{
    return view('livewire.show-posts')
        ->extends('layouts.app')
        ->section('body');
}
@endcomponent

如果您需要將數據從您的元件傳遞到您的佈局，您可以將數據與佈局方法一起傳遞：

@component('components.code', ['lang' => 'php'])
public function render()
{
    return view('livewire.show-posts')
        ->layout('layouts.base', ['title' => 'Show Posts'])
}
@endcomponent

在某些情況下，您不需要傳遞您的佈局名稱，或者您想要單獨傳遞佈局數據，您可以使用`layoutData`方法：

@component('components.code', ['lang' => 'php'])
public function render()
{
    return view('livewire.show-posts')
        ->layoutData(['title' => 'Show Posts'])
}
@endcomponent

### 路由參數 {#route-params}

通常您需要在控制器方法中訪問路由參數。由於我們不再使用控制器，Livewire 通過其`mount`方法試圖模擬此行為。例如：

@component('components.code', ['lang' => 'php'])
Route::get('/post/{id}', ShowPost::class);
@endcomponent

@component('components.code', ['lang' => 'php'])
class ShowPost extends Component
{
    public $post;

    public function mount($id)
    {
        $this->post = Post::find($id);
    }

    ...
}
@endcomponent

正如您所看到的，Livewire 元件中的`mount`方法在參數方面的行為類似於控制器方法。如果您訪問 `/post/123`，傳遞給`mount`方法的`$id`變量將包含值`123`。

### 路由模型綁定 {#route-model-binding}

就像您所期望的那樣，Livewire 元件實現了您在控制器中熟悉的所有功能，包括路由模型綁定。例如：

@component('components.code', ['lang' => 'php'])
Route::get('/post/{post}', ShowPost::class);
@endcomponent

@component('components.code', ['lang' => 'php'])
class ShowPost extends Component
{
    public $post;

```php
    public function mount(Post $post)
    {
        $this->post = $post;
    }
}
@endcomponent

如果您使用 PHP 7.4，您也可以對類屬性進行型別提示，Livewire 將自動將路由模型綁定到它們。以下組件的 `$post` 屬性將自動注入，無需 `mount()` 方法。

@component('components.code', ['lang' => 'php'])
class ShowPost extends Component
{
    public Post $post;
}
@endcomponent

## 渲染方法 {#render-method}

Livewire 組件的 `render` 方法在初始頁面加載時和每次後續組件更新時都會被調用。

@component('components.tip')
在簡單組件中，您不需要自己定義 `render` 方法。基礎 Livewire 組件類中已包含一個動態的 `render` 方法。
@endcomponent

### 返回 Blade 視圖 {#returning-blade}
`render()` 方法預期返回一個 Blade 視圖，因此，您可以將其比作編寫控制器方法。這是一個示例：

@component('components.warning')
確保您的 Blade 視圖只有一個根元素。
@endcomponent

@component('components.code-component')
@slot('class')
@verbatim

class ShowPosts extends Component
{
    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => Post::all(),
        ]);
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    @foreach ($posts as $post)
        @include('includes.post', $post)
    @endforeach
</div>
@endverbatim
@endslot
@endcomponent

### 返回模板字符串 {#returning-strings}
除了 Blade 視圖外，您還可以從 `render()` 中選擇性返回 Blade 模板字符串。

@component('components.code-component')
@slot('class')
@verbatim
class DeletePost extends Component
{
    public Post $post;

    public function delete()
    {
        $this->post->delete();
    }

    public function render()
    {
        return <<<'blade'
            <div>
                <button wire:click="delete">Delete Post</button>
            </div>
        blade;
    }
}
@endverbatim
@endslot
@endcomponent
```

@component('components.tip')
對於像上面這樣的內嵌元件，您應該在創建時使用<code>--inline</code>標誌：<code>artisan make:livewire delete-post --inline</code>
@endcomponent

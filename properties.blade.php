* [簡介](#introduction)
  * [重要提示](#important-notes)
* [初始化屬性](#initializing-properties)
* [資料綁定](#data-binding)
  * [綁定巢狀資料](#binding-nested-data)
  * [去抖動輸入](#debouncing-input)
  * [延遲更新](#lazy-updating)
  * [延後更新](#deferred-updating)
* [直接綁定到模型屬性](#binding-models)
* [自定義（可線性）屬性](#wireable-properties)
* [計算屬性](#computed-properties)

## 簡介 {#introduction}

Livewire元件將資料存儲並追踪為元件類別上的公共屬性。

@component('components.code', ['lang' => 'php'])
@verbatim
class HelloWorld extends Component
{
    public $message = 'Hello World!';
    ...
@endverbatim
@endcomponent

Livewire中的公共屬性會自動提供給視圖。無需明確將它們傳遞到視圖中（雖然如果需要的話也可以）。

@component('components.code-component')
@slot('class')
class HelloWorld extends Component
{
    public $message = 'Hello World!';
}
@endslot
@slot('view')
@verbatim
<div>
    <h1>{{ $message }}</h1>
    <!-- 將輸出 "Hello World!" -->
</div>
@endverbatim
@endslot
@endcomponent

### 重要提示 {#important-notes}

在開始Livewire之旅之前，以下是三個關於公共屬性的重要事項：

1. 屬性名稱不能與Livewire保留的屬性名稱衝突（例如`rules`或`messages`）
2. 存儲在公共屬性中的資料將對前端JavaScript可見。因此，您不應在其中存儲敏感資料。
3. 屬性只能是JavaScript友好的資料類型（`string`、`int`、`array`、`boolean`），或以下PHP類型之一：`Stringable`、`Collection`、`DateTime`、`Model`、`EloquentCollection`。

@component('components.warning')
<code>protected</code>和<code>private</code>屬性在Livewire更新之間不會持久存在。一般來說，您應該避免使用它們來存儲狀態。<br>
您還應該注意，雖然<code>null</code>資料類型是JavaScript友好的，但設置為<code>null</code>的<code>public</code>屬性在Livewire更新之間不會持久存在。
@endcomponent

## 初始化屬性 {#initializing-properties}

您可以使用組件的 `mount` 方法來初始化屬性。

@component('components.code-component')
@slot('class')
class HelloWorld extends Component
{
    public $message;

    public function mount()
    {
        $this->message = 'Hello World!';
    }
}
@endslot
@endcomponent

此外，Livewire 還為您提供了 `$this->fill()` 方法，用於在您需要設置大量屬性並希望消除視覺噪音的情況下使用。

@component('components.code-component')
@slot('class')
public function mount()
{
    $this->fill(['message' => 'Hello World!']);
}
@endslot
@endcomponent

此外，Livewire 還提供了 `$this->reset()` 和 `$this->resetExcept()` 方法，用於以程式方式將公共屬性值重置為其初始狀態。這對於在執行操作後清理輸入字段非常有用。

@component('components.code-component')
@slot('class')
public $search = '';
public $isActive = true;

public function resetFilters()
{
    $this->reset('search');
    // 只會重置 search 屬性。

    $this->reset(['search', 'isActive']);
    // 會重置 search 和 isActive 屬性。

    $this->resetExcept('search');
    // 只會重置 isActive 屬性（除了 search 屬性之外的任何屬性）。
}
@endslot
@endcomponent

## 資料綁定 {#data-binding}
如果您已經使用過像 Vue 或 Angular 這樣的前端框架，您可能已經熟悉這個概念。但是，如果您對這個概念還不熟悉，Livewire 可以將某個 HTML 元素的當前值與組件中的特定屬性“綁定”（或“同步”）。

@component('components.code-component')
@slot('class')
@verbatim
class HelloWorld extends Component
{
    public $message;
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    <input wire:model="message" type="text">

    <h1>{{ $message }}</h1>
</div>
@endverbatim
@endslot
@endcomponent

當用戶在文本字段中輸入內容時，`$message` 屬性的值將自動更新。

在內部，Livewire 將監聽元素上的 `input` 事件，當觸發時，它將發送 AJAX 請求以使用新數據重新渲染組件。

@component('components.tip')
您可以將<code>wire:model</code>添加到任何觸發<code>input</code>事件的元素上。甚至是自定義元素或第三方JavaScript庫。
@endcomponent

常見的使用`wire:model`的元素包括：

元素標籤 |
--- |
`<input type="text">` |
`<input type="radio">` |
`<input type="checkbox">` |
`<select>` |
`<textarea>` |

### 綁定巢狀資料 {#binding-nested-data}

Livewire支持使用點記號綁定陣列內的巢狀資料：

@component('components.code')
<input type="text" wire:model="parent.message">
@endcomponent

### 輸入去抖 {#debouncing-input}

預設情況下，Livewire對文字輸入應用了150毫秒的去抖。這樣可以避免在使用者輸入到文字欄位時發送過多的網絡請求。

如果您希望覆蓋此默認值（或將其添加到非文字輸入中），Livewire提供了一個“debounce”修飾符。如果您想對輸入應用半秒的去抖，您可以像這樣包含修飾符：

@component('components.code')
<input type="text" wire:model.debounce.500ms="name">
@endcomponent

### 懶惰更新 {#lazy-updating}

預設情況下，Livewire在每次`input`事件（或在某些情況下的`change`事件）後向服務器發送請求。這對於像`<select>`元素這樣通常不會觸發快速更新的情況來說通常是可以接受的，但對於隨著用戶輸入而更新的文字欄位來說，這通常是不必要的。

在這些情況下，使用`lazy`指示詞修飾符來監聽本機的`change`事件。

@component('components.code')
<input type="text" wire:model.lazy="message">
@endcomponent

現在，只有當用戶從輸入欄位中點擊離開時，`$message`屬性才會被更新。

### 延遲更新 {#deferred-updating}
在您不需要即時發生數據更新的情況下，Livewire具有`.defer`修飾符，可以將數據更新與下一個網絡請求批處理。

例如，考慮以下組件：

@component('components.code', ['lang' => 'blade'])
<input type="text" wire:model.defer="query">
<button wire:click="search">Search</button>
@endcomponent

當使用者在 `<input>` 欄位中輸入時，不會發送任何網路請求。即使使用者從輸入欄位點擊到頁面上的其他欄位，也不會發送請求。

當使用者按下 "搜尋" 時，Livewire 將發送一個包含新的 "query" 狀態和要執行的 "search" 操作的網路請求。

這可以在不需要時大幅減少網路使用量。

## 直接綁定到模型屬性 {#binding-models}

如果在 Livewire 元件中將 Eloquent 模型存儲為公共屬性，則可以直接綁定到其屬性。以下是一個範例元件：

@component('components.code-component')
@slot('class')
use App\Post;

class PostForm extends Component
{
    public Post $post;

    protected $rules = [
        'post.title' => 'required|string|min:6',
        'post.content' => 'required|string|max:500',
    ];

    public function save()
    {
        $this->validate();

        $this->post->save();
    }
}
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="save">
    <input type="text" wire:model="post.title">

    <textarea wire:model="post.content"></textarea>

    <button type="submit">儲存</button>
</form>
@endverbatim
@endslot
@endcomponent

注意在上述元件中，我們直接綁定到 "title" 和 "content" 模型屬性。Livewire 將負責在請求之間使用當前的、未持久化的數據來填充和解析模型。

@component('components.warning')
注意：為使此功能正常運作，您需要在 `$rules` 屬性中為要綁定的任何模型屬性設置驗證條目。否則將拋出錯誤。
@endcomponent

此外，您還可以在 Eloquent 集合中綁定到模型。

@component('components.code-component')
@slot('class')
use App\Post;

class PostForm extends Component
{
    public $posts;

    protected $rules = [
        'posts.*.title' => 'required|string|min:6',
        'posts.*.content' => 'required|string|max:500',
    ];

    public function mount()
    {
        $this->posts = auth()->user()->posts;
    }

```php
    public function save()
    {
        $this->validate();

        foreach ($this->posts as $post) {
            $post->save();
        }
    }
}
```
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="save">
    @foreach ($posts as $index => $post)
        <div wire:key="post-field-{{ $post->id }}">
            <input type="text" wire:model="posts.{{ $index }}.title">

            <textarea wire:model="posts.{{ $index }}.content"></textarea>
        </div>
    @endforeach

    <button type="submit">Save</button>
</form>
@endverbatim
@endslot
@endcomponent

Livewire 也支援對 Eloquent 模型上的關聯進行綁定，如下所示：

@component('components.code-component')
@slot('class')
class EditUsersPosts extends Component
{
    public User $user;

    protected $rules = [
        'user.posts.*.title'
    ];

    public function save()
    {
		$this->validate();

        $this->user->posts->each->save();
    }
}
@endslot
@slot('view')
@verbatim
<div>
    @foreach ($user->posts as $i => $post)
        <input type="text" wire:model="user.posts.{{ $i }}.title" />

        <span class="error">
            @error('user.posts.'.$i.'.title') {{ $message }} @enderror
        </span>
    @endforeach

    <button wire:click="save">Save</button>
</div>
@endverbatim
@endslot
@endcomponent

## 自訂（可線上綁定）屬性 {#wireable-properties}

有時您可能希望將組件屬性設置為應用程序內可用的非模型對象，例如 DTO（數據傳輸對象）。

例如，假設我們的應用程序中有一個名為 `Settings` 的自定義對象。我們可以使用方便的包裝對象或 DTO，如 `Settings`，將與此數據關聯的行為附加到此數據上，而不僅僅將設置數據存儲為 Livewire 組件上的普通數組：

@component('components.code-component')
@slot('class')
class Settings implements Livewire\Wireable
{
    public $items = [];

    public function __construct($items)
    {
        $this->items = $items;
    }

    ...

    public function toLivewire()
    {
        return $this->items;
    }

```php
    public static function fromLivewire($value)
    {
        return new static($value);
    }
}
@endslot
@endcomponent

現在您可以自由地將此物件用作組件的公共屬性，只要該物件實現了 `Livewire\Wireable` 介面並且屬性被類型提示如下：

@component('components.code-component')
@slot('class')
class SettingsComponent extends Livewire\Component
{
    public Settings $settings;

    public function mount()
    {
        $this->settings = new Settings([
            'foo' => 'bar',
        ]);
    }

    public function changeSetting()
    {
        $this->settings->foo = 'baz';
    }
}
@endslot
@endcomponent

正如您所看到的，由於使用 `Wireable`，組件的更改在請求之間是持久的，Livewire 知道如何在您的組件上“脫水”和“再水化”此屬性。

> 如果在 Livewire 的上下文中，“脫水”或“再水化”等詞對您來說有點模糊，[快速閱讀此文章](https://calebporzio.com/livewire-isnt-actually-live)。

## 計算屬性 {#computed-properties}

Livewire 提供了一個 API 來訪問動態屬性。這對於從數據庫或其他持久存儲（如緩存）中派生屬性特別有用。

@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPost extends Component
{
    // 計算屬性
    public function getPostProperty()
    {
        return Post::find($this->postId);
    }
@endverbatim
@endcomponent

現在，您可以從組件的類或 Blade 視圖中訪問 `$this->post`：

@component('components.code-component')
@slot('class')
class ShowPost extends Component
{
    public $postId;

    public function getPostProperty()
    {
        return Post::find($this->postId);
    }

    public function deletePost()
    {
        $this->post->delete();
    }
}
@endslot
@slot('view')
@verbatim
<div>
    <h1>{{ $this->post->title }}</h1>
    ...
    <button wire:click="deletePost">Delete Post</button>
</div>
@endverbatim
@endslot
@endcomponent

@component('components.tip')
計算屬性在個別 Livewire 請求生命週期中被緩存。這意味著，如果您在組件的 Blade 視圖中多次調用 `$this->post`，它不會每次進行單獨的數據庫查詢。
@endcomponent
```

Please paste the Markdown content for translation.

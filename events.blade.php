* [觸發事件](#firing-events)
  * [從模板中觸發](#from-template)
  * [從元件中觸發](#from-component)
  * [從全域 JavaScript 中觸發](#from-js)
* [事件監聽器](#event-listeners)
* [傳遞參數](#passing-parameters)
* [事件範圍](#scoping-events)
  * [限定到父監聽器](#scope-to-parents)
  * [按名稱限定到元件](#scope-by-name)
  * [限定到自身](#scope-to-self)
* [在 JavaScript 中監聽事件](#in-js)
* [分派瀏覽器事件](#browser)

Livewire 元件可以通過全域事件系統彼此通訊。只要兩個 Livewire 元件存在於同一頁面上，它們就可以使用事件和監聽器進行通訊。

## 觸發事件 {#firing-events}

從 Livewire 元件中觸發事件有多種方法。

### 方法 A: 從模板中觸發 {#from-template}

@component('components.code')
<button wire:click="$emit('postAdded')">
@endcomponent

### 方法 B: 從元件中觸發 {#from-component}

@component('components.code', ['lang' => 'php'])
$this->emit('postAdded');
@endcomponent

### 方法 C: 從全域 JavaScript 中觸發 {#from-js}

@component('components.code', ['lang' => 'javascript'])
<script>
    Livewire.emit('postAdded')
</script>
@endcomponent

## 事件監聽器 {#event-listeners}
事件監聽器在您的 Livewire 元件的 `$listeners` 屬性中註冊。

監聽器是一個鍵值對，其中鍵是要監聽的事件，值是要在元件上調用的方法。

@component('components.code', ['lang' => 'php'])
class ShowPosts extends Component
{
    public $postCount;

    protected $listeners = ['postAdded' => 'incrementPostCount'];

    public function incrementPostCount()
    {
        $this->postCount = Post::count();
    }
}
@endcomponent

現在，當頁面上的任何其他元件發出 `postAdded` 事件時，此元件將接收到並執行自身的 `incrementPostCount` 方法。

@component('components.tip')
如果事件的名稱和您要調用的方法匹配，則可以省略鍵。例如：<code>protected $listeners = ['postAdded'];</code> 將在發出 <code>postAdded</code> 事件時調用 <code>postAdded</code> 方法。
@endcomponent

如果您需要動態命名事件監聽器，您可以將 `$listeners` 屬性替換為元件上的 `getListeners()` 受保護方法：

@component('components.code-component')
@slot('class')
class ShowPosts extends Component
{
    public $postCount;

    protected function getListeners()
    {
        return ['postAdded' => 'incrementPostCount'];
    }

    ...
}
@endslot
@endcomponent

@component('components.warning')
<code>getListeners()</code> 只會在元件掛載時動態生成監聽器的名稱。一旦監聽器設置完成，就無法更改。
@endcomponent

## 傳遞引數 {#passing-parameters}

您也可以在事件發射時傳遞引數。

@component('components.code', ['lang' => 'php'])
$this->emit('postAdded', $post->id);
@endcomponent

@component('components.code-component')
@slot('class')
class ShowPosts extends Component
{
    public $postCount;
    public $recentlyAddedPost;

    protected $listeners = ['postAdded'];

    public function postAdded(Post $post)
    {
        $this->postCount = Post::count();
        $this->recentlyAddedPost = $post;
    }
}
@endslot
@endcomponent

## 事件範圍 {#scoping-events}

### 限定到父監聽器 {#scope-to-parents}
在處理 [巢狀元件](nesting-components) 時，有時您可能只想將事件發射給父元件，而不是子元件或同層元件。

在這些情況下，您可以使用 `emitUp` 功能：

@component('components.code', ['lang' => 'php'])
$this->emitUp('postAdded');
@endcomponent

@component('components.code')
<button wire:click="$emitUp('postAdded')">
@endcomponent

### 限定到特定元件 {#scope-by-name}
有時您可能只想將事件發射給相同類型的其他元件。

在這些情況下，您可以使用 `emitTo`：

@component('components.code', ['lang' => 'php'])
$this->emitTo('counter', 'postAdded');
@endcomponent

@component('components.code')
<button wire:click="$emitTo('counter', 'postAdded')">
@endcomponent

(現在，如果按下按鈕，"postAdded" 事件將只發射到 `counter` 元件)


### 將範圍限定為自身 {#scope-to-self}
有時您可能只想在觸發事件的元件上發出事件。

在這些情況下，您可以使用 `emitSelf`：

@component('components.code', ['lang' => 'php'])
$this->emitSelf('postAdded');
@endcomponent

@component('components.code')
<button wire:click="$emitSelf('postAdded')">
@endcomponent

（現在，如果按下按鈕，"postAdded" 事件將僅發送到發出該事件的元件實例。）

## 在 JavaScript 中監聽事件 {#in-js}

Livewire 允許您像這樣在 JavaScript 中註冊事件監聽器：

@component('components.code', ['lang' => 'javascript'])
<script>
Livewire.on('postAdded', postId => {
    alert('已新增帖子，其 ID 為：' + postId);
})
</script>
@endcomponent

@component('components.tip')
實際上，這個功能非常強大。例如，您可以註冊一個監聽器，在 Livewire 執行某些操作時在應用程式內顯示一個彈出式視窗。這是將 PHP 與 JavaScript 之間的差距彌合的眾多方法之一。
@endcomponent

## 發送瀏覽器事件 {#browser}

Livewire 允許您像這樣觸發瀏覽器視窗事件：

@component('components.code', ['lang' => 'php'])
$this->dispatchBrowserEvent('name-updated', ['newName' => $value]);
@endcomponent

您可以使用 JavaScript 監聽此視窗事件：

@component('components.code', ['lang' => 'javascript'])
<script>
window.addEventListener('name-updated', event => {
    alert('名稱已更新為：' + event.detail.newName);
})
</script>
@endcomponent

AlpineJS 允許您在 HTML 內輕鬆監聽這些視窗事件：

@component('components.code', ['lang' => 'blade'])
<div x-data="{ open: false }" @name-updated.window="open = false">
    <!-- 具有 Livewire 名稱更新表單的模態對話框 -->
</div>
@endcomponent

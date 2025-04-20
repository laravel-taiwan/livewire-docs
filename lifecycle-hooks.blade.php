* [類別掛勾](#class-hooks)
* [Javascript 掛勾](#js-hooks)

## 類別掛勾 {#class-hooks}

每個 Livewire 元件都有一個生命週期。生命週期掛勾允許您在元件的生命週期的任何部分運行代碼，或在特定屬性更新之前運行代碼。

@component('components.table')
掛勾 | 說明
--- | ---
boot | 在每個請求上運行，立即在元件實例化後運行，但在調用任何其他生命週期方法之前運行
booted | 在每個請求上運行，在元件掛載或填充後運行，但在調用任何更新方法之前運行
mount | 只運行一次，在元件實例化後立即運行，但在調用 `render()` 之前運行。這僅在初始頁面加載時調用一次，即使在元件刷新時也不會再次調用
hydrate | 在每個後續請求上運行，在元件填充後運行，但在執行操作或調用 `render()` 之前運行
hydrateFoo | 在名為 `$foo` 的屬性填充後運行
dehydrate | 在每個後續請求上運行，在元件被填充後但在調用 `render()` 之前運行
dehydrateFoo | 在名為 `$foo` 的屬性被填充前運行
updating | 在更新 Livewire 元件數據之前運行（使用 `wire:model`，不直接在 PHP 內部運行）
updated | 在更新 Livewire 元件數據後運行（使用 `wire:model`，不直接在 PHP 內部運行）
updatingFoo | 在更新名為 `$foo` 的屬性之前運行。陣列屬性在此函數中有一個額外的 `$key` 參數，用於指定更改陣列內的元素，例如 `updatingArray($value, $key)`
updatedFoo | 在更新名為 `$foo` 的屬性後運行。陣列屬性具有上述額外的 `$key` 參數
updatingFooBar | 在更新 `$foo` 屬性上的嵌套屬性 `bar` 或多字詞屬性（如 `$fooBar` 或 `$foo_bar`）之前運行
updatedFooBar | 在更新 `$foo` 屬性上的嵌套屬性 `bar` 或多字詞屬性（如 `$fooBar` 或 `$foo_bar`）後運行
@endcomponent

@component('components.warning')
請注意，在 Livewire 元件類別內直接變異屬性不會觸發任何更新/已更新掛勾。
@endcomponent

```php
class HelloWorld extends Component
{
    public $foo;

    public function boot()
    {
        //
    }

    public function booted()
    {
        //
    }

    public function mount()
    {
        //
    }

    public function hydrateFoo($value)
    {
        //
    }

    public function dehydrateFoo($value)
    {
        //
    }

    public function hydrate()
    {
        //
    }

    public function dehydrate()
    {
        //
    }

    public function updating($name, $value)
    {
        //
    }

    public function updated($name, $value)
    {
        //
    }

    public function updatingFoo($value)
    {
        //
    }

    public function updatedFoo($value)
    {
        //
    }

    public function updatingFooBar($value)
    {
        //
    }

    public function updatedFooBar($value)
    {
        //
    }
}
```

## Javascript Hooks {#js-hooks}

Livewire 提供了在特定事件期間執行 JavaScript 的機會。

```plaintext
Hooks | Description
--- | ---
component.initialized | 當 Livewire 在頁面上初始化組件時調用
element.initialized | 當 Livewire 初始化單個元素時調用
element.updating | 在 Livewire 在網絡往返後的 DOM-diff 循環期間更新元素之前調用
element.updated | 在 Livewire 在網絡往返後的 DOM-diff 循環期間更新元素後調用
element.removed | 在 Livewire 在 DOM-diff 循環期間刪除元素後調用
message.sent | 當 Livewire 更新觸發通過 AJAX 發送到服務器的消息時調用
message.failed | 如果消息發送因某種原因失敗時調用
message.received | 當消息完成其往返時調用，但在 Livewire 更新 DOM 之前調用
message.processed | 在 Livewire 從消息處理所有副作用（包括 DOM-diffing）後調用
```


```js
<script>
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.hook('component.initialized', (component) => {})
        Livewire.hook('element.initialized', (el, component) => {})
        Livewire.hook('element.updating', (fromEl, toEl, component) => {})
        Livewire.hook('element.updated', (el, component) => {})
        Livewire.hook('element.removed', (el, component) => {})
        Livewire.hook('message.sent', (message, component) => {})
        Livewire.hook('message.failed', (message, component) => {})
        Livewire.hook('message.received', (message, component) => {})
        Livewire.hook('message.processed', (message, component) => {})
    });
</script>
```

Please paste the Markdown content you need to be translated into traditional Chinese.

如果您已經使用過像 Angular、React 或 Vue 這樣的前端框架，您對這個概念應該已經很熟悉。但是，如果您對這個概念還不熟悉，讓我來演示一下。

@component('components.code-component')
@slot('class')
@verbatim
class MyNameIs extends Component
{
    public $name;
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    <input type="text" wire:model="name">

    Hi! My name is {{ $name }}
</div>
@endverbatim
@endslot
@endcomponent

當用戶在文本字段中輸入內容時，`$name` 屬性的值將自動更新。Livewire 會根據 `wire:model` 指示詞來跟踪提供的名稱。

在內部，Livewire 監聽元素上的 "input" 事件並使用元素的值更新類屬性。因此，您可以將 `wire:model` 應用於任何發出 `input` 事件的元素。

@component('components.tip')
默認情況下，Livewire 對文本輸入應用了 150 毫秒的防彈跳。您可以像這樣覆蓋此默認值：<code>&lt;input type="text" wire:model.debounce.0ms="name"&gt;</code>
@endcomponent

常用的應用 `wire:model` 的元素包括：

元素標籤 |
--- |
`<input type="text">` |
`<input type="radio">` |
`<input type="checkbox">` |
`<select>` |
`<textarea>` |

## 嵌套數據綁定 {#nested-binding}

Livewire 支持使用點表示法進行嵌套數據綁定：

@component('components.code')
<input type="text" wire:model="form.name">
@endcomponent

## 防彈跳輸入 {#debouncing}

當使用 `wire:model` 時，Livewire 提供了 "debounce" 修飾符。如果您想對輸入應用 1 秒的防彈跳，您可以這樣包含修飾符：

@component('components.code')
<input type="text" wire:model.debounce.1000ms="name">

<!-- 您也可以指定以秒為單位的時間： -->
<input type="text" wire:model.debounce.1s="name">
@endcomponent

## 延遲更新 {#lazilly-updating}

默認情況下，Livewire 在每次 "input" 事件後向服務器發送請求。對於不經常更新的 `<select>` 元素來說，這通常是可以接受的，但對於隨著用戶輸入而更新的文本字段來說，這通常是不必要的。

在這些情況下，使用 `lazy` 指令修飾符來監聽原生的 "change" 事件。

@component('components.code-component')
@slot('class')
@verbatim
class MyNameIs extends Component
{
    public $name;
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    <input type="text" wire:model.lazy="name">

    My name is chica-chica {{ $name }}
</div>
@endverbatim
@endslot
@endcomponent

現在，當用戶從輸入欄中點擊離開時，`$name` 屬性將只會被更新。

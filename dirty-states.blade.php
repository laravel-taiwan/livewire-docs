* [在“髒”元素上切換類](#toggling-classes)
* [切換元素](#toggling-elements)
* [在其他元素上切換類](#toggling-classes)

有些情況下，提供反饋以指示內容已更改並尚未與後端 Livewire 元件同步可能很有用。

對於使用 `wire:model` 或 `wire:model.lazy` 的輸入，您可能希望顯示該字段在 Livewire 完全更新之前是“髒”的。

## 在“髒”元素上切換類 {#toggling-classes}

具有 `wire:dirty` 指示詞的元素將監視前端值與上次返回的 Livewire 數據值之間的差異。

添加 `class` 修飾符使您可以在元素“髒”時添加一個類。

@component('components.code', ['lang' => 'blade'])
<div>
    <input wire:dirty.class="border-red-500" wire:model.lazy="foo">
</div>
@endcomponent

現在，當用戶修改輸入值時，該元素將收到 `border-red-500` 類。如果輸入值恢復到原始狀態，或者 Livewire 元件更新，則該類將再次被移除。

您也可以執行相反操作，通過添加 `.remove` 修飾符來刪除類，類似於 `wire:loading` 的工作方式。

@component('components.code', ['lang' => 'blade'])
<div>
    <input wire:dirty.class.remove="bg-green-200" class="bg-green-200" wire:model.lazy="foo">
</div>
@endcomponent

當元素“髒”時，`bg-green-200` 類將從輸入中被移除。

## 切換元素 {#toggling-elements}

`wire:dirty` 指示詞的默認行為（無修飾符）是該元素將在“髒”之前被隱藏。如果在輸入本身上使用，這可能會造成一個悖論，但與加載狀態一樣，`dirty` 指示詞可以用於使用 `wire:target` 切換其他元素的外觀。

@component('components.code', ['lang' => 'blade'])
<div>
    <span wire:dirty wire:target="foo">更新中...</span>
    <input wire:model.lazy="foo">
</div>
@endcomponent

在此示例中，`span` 默誵情況下將被隱藏，僅在輸入元素“髒”時可見。

## 切換其他元素的類別 {#toggling-classes}

類別和屬性修改器可以以相同的方式用於參考元素

@component('components.code', ['lang' => 'blade'])
<div>
    <label wire:dirty.class="text-red-500" wire:target="foo">全名</label>
    <input wire:model.lazy="foo">
</div>
@endcomponent

現在，當 `input` 變為 dirty 時，標籤文字將獲得 `text-red-500` 類別。

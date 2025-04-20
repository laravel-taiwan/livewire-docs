* [切換元素](#toggling-elements)
* [切換類別](#toggling-classes)
* [切換屬性](#toggling-attributes)

有時候通知使用者失去網路連線是很重要的。Livewire 提供了有用的工具，可以根據使用者的「離線」狀態執行動作。

## 切換元素 {#toggling-elements}

您可以通過添加 `wire:offline` 屬性，在使用者「離線」時在頁面上顯示一個元素。

@component('components.code')
<div wire:offline>
    您現在已離線。
</div>
@endcomponent

這個 `<div>` 預設會自動隱藏，並在瀏覽器離線時顯示給使用者。

## 切換類別 {#toggling-classes}

添加 `class` 修飾符允許您在「離線」時向元素添加一個類別。

@component('components.code', ['lang' => 'blade'])
<div wire:offline.class="bg-red-300"></div>
@endcomponent

現在，當瀏覽器離線時，該元素將收到 `bg-red-300` 類別。一旦使用者恢復在線，該類別將被移除。

您也可以執行相反操作，通過添加 `.remove` 修飾符來移除類別，類似於 `wire:loading` 的作用方式。

@component('components.code', ['lang' => 'blade'])
<div wire:offline.class.remove="bg-green-300" class="bg-green-300"></div>
@endcomponent

當離線時，`bg-green-300` 類別將從 `<div>` 中移除。

## 切換屬性 {#toggling-attributes}

添加 `attr` 修飾符允許您在「離線」時向元素添加一個屬性。

@component('components.code', ['lang' => 'blade'])
<button wire:offline.attr="disabled">提交</button>
@endcomponent

現在，當瀏覽器離線時，按鈕將被禁用。

您也可以執行相反操作，通過添加 `.remove` 修飾符來移除屬性。

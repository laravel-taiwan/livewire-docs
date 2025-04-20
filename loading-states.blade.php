* [簡介](#introduction)
* [在“載入”狀態下切換元素](#toggling-elements)
* [延遲載入指示器](#delaying-loading)
* [針對特定操作](#targeting-actions)
* [針對模型](#targeting-models)
* [切換類別](#toggling-classes)
* [切換屬性](#toggling-attributes)

## 簡介 {#introduction}

由於 Livewire 每次在頁面上觸發操作時都會與伺服器來回傳輸，因此在某些情況下，頁面可能無法立即對使用者事件（如點擊）做出反應。Livewire 允許您輕鬆顯示載入狀態，這可以使您的應用程式感覺更具有回應性。

## 在“載入”狀態下切換元素 {#toggling-elements}

具有 `wire:loading` 指示詞的元素僅在等待操作完成時可見（網路請求）。

@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="checkout">結帳</button>

    <div wire:loading>
        處理付款中...
    </div>
</div>
@endcomponent

當點擊“結帳”按鈕時，將顯示“處理付款中...”訊息。當操作完成時，該訊息將消失。

預設情況下，Livewire 將載入元素的 “display” CSS 屬性設置為 “inline-block”。如果您希望 Livewire 使用 “flex” 或 “grid”，您可以使用以下修飾符。

@component('components.code', ['lang' => 'blade'])
<div wire:loading.block>...</div>
<div wire:loading.flex>...</div>
<div wire:loading.inline-flex>...</div>
<div wire:loading.grid>...</div>
<div wire:loading.inline>...</div>
<div wire:loading.table>...</div>
@endcomponent

您還可以使用 `.remove` 修飾符在載入狀態下“隱藏”元素。

@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="checkout">結帳</button>

    <div wire:loading.remove>
        載入時隱藏我...
    </div>
</div>
@endcomponent

## 延遲載入指示器 {#delaying-loading}

如果您想要避免閃爍，因為載入非常快，您可以添加 `.delay` 修飾符，只有在載入時間超過 `200ms` 時才會顯示。

```blade
@component('components.code', ['lang' => 'blade'])
<div wire:loading.delay>...</div>
@endcomponent

如果需要，您可以使用以下修飾符自定義延遲時間：

@component('components.code', ['lang' => 'blade'])
<div wire:loading.delay.shortest>...</div> <!-- 50ms -->
<div wire:loading.delay.shorter>...</div>  <!-- 100ms -->
<div wire:loading.delay.short>...</div>    <!-- 150ms -->
<div wire:loading.delay>...</div>          <!-- 200ms -->
<div wire:loading.delay.long>...</div>     <!-- 300ms -->
<div wire:loading.delay.longer>...</div>   <!-- 500ms -->
<div wire:loading.delay.longest>...</div>  <!-- 1000ms -->
@endcomponent

## 針對特定操作進行定位 {#targeting-actions}

上面介紹的方法適用於簡單的元件。對於更複雜的元件，您可能希望僅針對特定操作顯示加載指示器。

@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="checkout">結帳</button>
    <button wire:click="cancel">取消</button>

    <div wire:loading wire:target="checkout">
        付款處理中...
    </div>
</div>
@endcomponent

在上面的示例中，當點擊“結帳”按鈕時，將顯示加載指示器，但當點擊“取消”按鈕時不會顯示。

`wire:target` 可以接受以逗號分隔的多個參數，格式如下：`wire:target="foo, bar"`。

您也可以針對具有特定參數的操作進行定位。
@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="update('bob')">更新</button>

    <div wire:loading wire:target="update('bob')">
        更新 Bob...
    </div>
</div>
@endcomponent

如果您希望在陣列的任何屬性更改時觸發加載指示器，您可以簡單地對整個陣列進行定位：

@component('components.code', ['lang' => 'blade'])
<div>
    <input type="text" wire:model="post.title">
    <input type="text" wire:model="post.author">
    <input type="text" wire:model="post.content">

    <div wire:loading wire:target="post">
        更新文章中...
    </div>
</div>
@endcomponent
```

## 定位模型 {#targeting-models}
除了動作之外，您還可以在`synchronized`時定位`wire:model`。

@component('components.code', ['lang' => 'blade'])
<div>
    <input wire:model="quantity">

    <div wire:loading wire:target="quantity">
        更新數量中...
    </div>
</div>
@endcomponent

## 切換類別 {#toggling-classes}

您可以在加載狀態期間向元素添加或移除類別，只需將`.class`修飾符添加到`wire:loading`指示詞中。

@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="checkout" wire:loading.class="bg-gray">
        結帳
    </button>
</div>
@endcomponent

現在，當點擊“結帳”按鈕時，背景將變為灰色，同時網絡請求正在處理。

您還可以執行相反操作，通過添加`.remove`修飾符來移除類別。

@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="checkout" wire:loading.class.remove="bg-blue" class="bg-blue">
        結帳
    </button>
</div>
@endcomponent

現在，在加載時，按鈕將移除`bg-blue`類別。

## 切換屬性 {#toggling-attributes}

與類別類似，HTML屬性可以在加載狀態期間添加或移除元素：

@component('components.code', ['lang' => 'blade'])
<div>
    <button wire:click="checkout" wire:loading.attr="disabled">
        結帳
    </button>
</div>
@endcomponent

現在，當點擊“結帳”按鈕時，元素將添加`disabled="true"`屬性。

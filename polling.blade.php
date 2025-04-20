* [簡介](#introduction)
* [背景中的輪詢](#polling-background)
* [僅當元素可見時進行輪詢](#polling-element-visible)

## 簡介 {#introduction}

Livewire 提供了一個名為 `wire:poll` 的指示詞，當添加到元素時，將每 `2s` 刷新組件。

@component('components.tip')
通過 Ajax 進行更改的輪詢是 Laravel Echo、Pusher 或任何 WebSocket 策略的一個輕量、簡單的替代方案。
@endcomponent

@component('components.code')
@verbatim
<div wire:poll>
    當前時間：{{ now() }}
</div>
@endverbatim
@endcomponent

您可以通過傳遞指示詞修改器如 `750ms` 來自定義頻率。例如：

@component('components.code')
@verbatim
<div wire:poll.750ms>
    當前時間：{{ now() }}
</div>
@endverbatim
@endcomponent

您還可以通過將值傳遞給 `wire:poll` 來指定在輪詢間隔上觸發的特定操作：

@component('components.code')
@verbatim
<div wire:poll="foo">
    當前時間：{{ now() }}
</div>
@endverbatim
@endcomponent

現在，組件上的 `foo` 方法將每 2 秒被調用。


## 背景中的輪詢 {#polling-background}

Livewire 在瀏覽器標籤在背景時減少輪詢，以免不必要地使伺服器因 ajax 請求而變慢。
僅保留預期輪詢請求的約 5%。

如果您希望即使標籤在背景中時也保持正常速率進行輪詢，您可以使用 `keep-alive` 修改器：

@component('components.code')
@verbatim
<div wire:poll.keep-alive>
    當前時間：{{ now() }}
</div>
@endverbatim
@endcomponent

## 僅當元素可見時進行輪詢 {#polling-element-visible}

如果您的組件在瀏覽器的視口中並非始終可見（例如在頁面下方），您可以選擇僅在元素可見時通過將 `.visible` 修改器添加到 `wire:poll` 來向伺服器輪詢。例如：

@component('components.code')
@verbatim
<div wire:poll.visible></div>
@endverbatim
@endcomponent

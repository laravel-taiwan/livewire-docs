* [簡介](#introduction)

## 簡介 {#introduction}

Livewire 提供了在滑鼠懸停時“預取”動作結果的能力。切換顯示內容是一個常見的使用案例。

@component('components.warning')
這在以下情況下很有用：當一個動作並不執行（例如寫入到 session 或資料庫）副作用時。如果您正在“預取”的動作具有副作用，則這些副作用將不可預測地被執行。
@endcomponent

將 `prefetch` 修飾符添加到動作中以啟用此行為：

@component('components.code')
@verbatim
<button wire:click.prefetch="toggleContent">顯示內容</button>

@if ($contentIsVisible)
    <span>一些內容...</span>
@endif
@endverbatim
@endcomponent

現在，當滑鼠進入“顯示內容”按鈕時，Livewire 將在後台提取“toggleContent”動作的結果。如果實際點擊按鈕，它將在頁面上顯示內容，而無需發送另一個網絡請求。如果未單擊按鈕，則預取的響應將被丟棄。

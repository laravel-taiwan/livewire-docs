* [簡介](#introduction)
* [使用 `@verbatim@js@endverbatim` 指示詞](#using-js-directive)
* [存取 JavaScript 元件實例](#accessing-javascript-component-instance)

## 簡介 {#introduction}

Livewire 建議您大部分的 JavaScript 需求使用 AlpineJS，但它也支援直接在元件視圖中使用 `<script>` 標籤。

@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    <!-- 您的元件 HTML -->

    <script>
        document.addEventListener('livewire:load', function () {
            // 在此放置您的 JS
        })
    </script>
</div>
@endverbatim
@endcomponent

@component('components.warning')
請注意，您的腳本將僅在元件首次渲染時運行一次。如果您需要稍後運行 JavaScript 函數 - 請從元件發出事件並在 JavaScript 中監聽，如<a href="https://laravel-livewire.com/docs/events/">此處</a>所述。
@endcomponent

您還可以直接從 Livewire 元件將腳本推送到 Blade 堆疊中：

@component('components.code', ['lang' => 'javascript'])
@verbatim
<!-- 您的元件視圖這裡 -->

@push('scripts')
<script>
    // 在此放置您的 JS
</script>
@endpush
@endverbatim
@endcomponent

## 使用 `@verbatim@js@endverbatim` 指示詞 {#using-js-directive}

如果您需要將 PHP 資料輸出供 JavaScript 使用，現在可以使用 `@verbatim@js@endverbatim` 指示詞。

@component('components.code', ['lang' => 'blade'])
@verbatim
<script>
    let posts = @js($posts)
    
    // "posts" 現在將是來自 PHP 的文章資料的 JavaScript 陣列。
</script>
@endverbatim
@endcomponent

## 存取 JavaScript 元件實例 {#accessing-javascript-component-instance}

因為 Livewire 同時具有 PHP 和 JavaScript 部分，每個元件也有一個 JavaScript 物件。您可以在元件視圖中使用特殊的 `@@this` blade 指示詞來存取此物件。

這是一個範例：

@component('components.code', ['lang' => 'javascript'])
@verbatim
<script>
    document.addEventListener('livewire:load', function () {
        // 獲取 "count" 屬性的值
        var someValue = @this.count

```html
        // 設定 "count" 屬性的值
        @this.count = 5

        // 呼叫增加元件行為
        @this.increment()

        // 當從此元件發出事件 ("foo") 時運行回呼
        @this.on('foo', () => {})
    })
</script>
@endverbatim
@endcomponent

> 注意：`@@this` 指示詞編譯為以下字串，供 JavaScript 解釋使用："Livewire.find([component-id])"
```

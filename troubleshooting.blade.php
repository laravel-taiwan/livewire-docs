* [DOM差異問題](#dom-diffing-issues)
    * [症狀](#dom-diffing-symptoms)
    * [治療方法](#dom-diffing-cures)
* [校驗和校驗和校驗問題](#checksum-issues)
* [查詢字串問題](#query-string-issues)
    * [症狀](#query-string-symptoms)
    * [治療方法](#query-string-cures)
* [根元素問題](#root-element-issues)
    * [症狀](#root-element-symptoms)
    * [治療方法](#root-element-cures)


## DOM差異問題 {#dom-diffing-issues}

Livewire使用者最常遇到的問題之一與Livewire的DOM差異/修補系統有關。這是一個系統，它在每次元件更新後選擇性地更新已更改、新增或移除的元素。

在大多數情況下，這個系統是可靠的，但有些情況下，Livewire無法正確追踪變化。當發生這種情況時，希望會拋出一個有用的錯誤，您可以根據以下指南進行調試。

### 症狀 {#dom-diffing-symptoms}
* 輸入元素失去焦點
* 元素或一組元素突然消失
* 先前互動的元素停止響應用戶輸入
* 載入指示器誤發
* 用戶動作不再起作用

### 治療方法 {#dom-diffing-cures}
* 確保您的元件具有單層根元素
* 在循環內的元素上添加 `wire:key`（`wire:key` 的值必須在頁面上是唯一的）：
@component('components.code')
@verbatim
<ul>
    @foreach ($items as $item)
        <li wire:key="item-{{ $item->id }}">{{ $item }}</li>
    @endforeach
</ul>
@endverbatim
@endcomponent

* 在循環中的嵌套元件上添加 `key()`/`wire:key`
@component('components.code')
@verbatim
<ul>
    @foreach ($items as $item)
        @livewire('view-item', ['item' => $item], key('item-'.$item->id))

        <!-- key() 使用 Laravel 7 的標籤語法 -->
        <livewire:view-item :item="$item" :wire:key="'item-'.$item->id">
    @endforeach
</ul>
@endverbatim
@endcomponent

* 將Blade條件式（`@if`、`@error`、`@auth`）包裹在一個元素中
@component('components.code')
@verbatim
<input type="text" wire:model="name">
<div> @error('name'){{ $message }}@enderror </div>
@endverbatim
@endcomponent

* 添加 `wire:key`。作為最後的措施，添加 `wire:key` 將直接告訴 Livewire 如何追蹤 DOM 元素。過度使用此屬性是一種異味，但對於這種性質的問題非常有用且強大。

@component('components.warning')
@verbatim
您傳遞給 <code>wire:key</code> 的值必須在該頁面上完全唯一。這意味著您應該添加前綴，如 <code>wire:key="item-{{ $item->id }}"</code>，並避免使用 <code>$loop->index</code> 來追蹤個別元素。
@endverbatim
@endcomponent

@component('components.code')
<div wire:key="foo">...</div>
<div wire:key="bar">...</div>
@endcomponent

## 校驗和問題 {#checksum-issues}

在每個請求中，Livewire 都會進行 "[校驗](https://laravel-livewire.com/docs/security)"，但在某些情況下，對於陣列，即使陣列內的資料相同，也可能會拋出異常。

因為在 PHP 中，一個陣列可以同時具有字母數字和數字鍵，並且可以以任何順序排列，但是 Javascript 會將其轉換為物件，因為它不支援具有字母數字鍵的陣列。當 Javascript 創建物件時，它還會重新排序鍵，將數字鍵放在字母數字鍵之前。

這導致一個問題，當 JSON 被發送回來時，因為 "[校驗](https://laravel-livewire.com/docs/security)" 將看起來不同。

某些類型（Point、LineString、Polygon 和 Multi- 變體）也會導致校驗失敗。

因此，確保當您有一個公共屬性是一個陣列時，數字鍵應該在字母數字字符鍵之前。
@component('components.code', ['lang' => 'php'])
@verbatim
class HelloWorld extends Component
{
    public $list = [
        '123' => 456,
        'foo' => 'bar'
    ];
    ...
@endverbatim
@endcomponent

## 查詢字串問題 {#query-string-issues}

當設置查詢字串時，Livewire 使用站點的 `referrer` 資訊。這可能會導致當您通過 `referrer-policy` 向應用程式添加安全標頭時出現衝突。

### 症狀 {#query-string-symptoms}

* 查詢字串根本沒有更新。
* 當值為空時，查詢字串也不會更新。

### 治療方法 {#query-string-cures}

如果您設置了安全標頭，請確保 `referrer-policy` 的值設置為 `same-origin`。

## 根元素問題 {#root-element-issues}

Livewire 要求在元件 blade 視圖的根部只有一個 HTML 元素。

擁有多個根元素可能意味著您的視圖的某些部分無法正確地與 Livewire 一起運作，甚至根本無法運作。

### 症狀 {#root-element-symptoms}

* 按鈕無法觸發 `wire:click`
* 輸入資料到輸入框不會觸發網路請求
* 您的視圖的某些部分未正確更新（也可能是 [Dom Diffing 問題](#dom-diffing-issues)，請參見上文）
* 您在瀏覽器控制台中收到一個錯誤，說明 `Livewire: Multiple root elements detected. This is not supported.`
* 請參見下方一個無法運作的按鈕範例：

@component('components.code', ['lang' => 'blade'])
<div>
    Some content
</div>

<!-- 這個按鈕無法運作 -->
<button wire:click="doSomething">Do Something</button>
@endcomponent

### 治療方法 {#root-element-cures}

解決方法是確保只有一個根 HTML 元素，例如 `<div>`。如果有多個元素，則將所有內容包裹在一個 `<div>` 或適合您的版面配置的其他元素中。

因此，在上面的示例中，我們將所有內容包裹在一個 `<div>` 中，使按鈕運作：

@component('components.code', ['lang' => 'blade'])
<div> <!-- 添加這個包裹 div -->
    <div>
        Some content
    </div>

    <button wire:click="doSomething">Do Something</button>
</div> <!-- 添加這個包裹 div 的結尾標籤 -->
@endcomponent

另一個原因可能是在 Livewire 類別或 Trait 內部使用 __construct()。

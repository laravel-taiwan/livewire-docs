* [安裝](#installation)
* [在 Livewire 內使用 Alpine](#alpine-in-livewire)
* [提取可重複使用的 Blade 元件](#extracting-blade-components)
* [從 Alpine 與 Livewire 互動：`$wire`](#interacting-with-livewire-from-alpine)
* [在 Livewire 與 Alpine 之間共享狀態：@verbatim`@entangle`@endverbatim](#sharing-state)
* [使用 `@verbatim@js@endverbatim` 指示詞](#js-directive)
* [從 Blade 元件存取 Livewire 指示詞](#livewire-directives-from-blade-components)
* [建立日期選擇器元件](#creating-a-datepicker)
* [轉發 `wire:model` `input` 事件](#forwarding-wire-model-input-events)
* [忽略 DOM 變更（使用 `wire:ignore`）](#ignoring-dom-changes)

有許多情況下，頁面互動並不需要完整的伺服器往返，例如切換模態對話框。

對於這些情況，AlpineJS 是 Livewire 的完美搭檔。

它允許您以一種聲明性/反應性的方式將 JavaScript 行為直接灑在您的標記中，這應該感覺非常類似於 VueJS（如果您習慣使用它）。

## 安裝 {#installation}

您必須安裝 Alpine 才能與 Livewire 一起使用。

要在您的專案中安裝 Alpine，請將以下腳本標籤添加到您的版面檔案的 `<head>` 部分。

@component('components.code', ['lang' => 'blade', 'id' => 'js-inject-alpine-version'])
<head>
    ...
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- "defer" 屬性很重要，以確保 Alpine 等待 Livewire 先載入。 -->
</head>
@endcomponent

有關更多安裝信息，請參閱[Alpine 文件](https://alpinejs.dev/essentials/installation)。

## 在 Livewire 內使用 Alpine {#alpine-in-livewire}

以下是在 Livewire 元件視圖內使用 AlpineJS 進行「下拉」功能的示例。

@component('components.code', ['lang' => 'blade'])
<div>
    ...

    <div x-data="{ open: false }">
        <button @click="open = true">顯示更多...</button>

        <ul x-show="open" @click.outside="open = false">
            <li><button wire:click="archive">歸檔</button></li>
            <li><button wire:click="delete">刪除</button></li>
        </ul>
    </div>
</div>
@endcomponent

## 提取可重複使用的 Blade 元件 {#extracting-blade-components}

如果您尚未熟悉每個工具本身，混合兩者的語法可能會有點混亂。

因此，如果可能的話，您應該將 Alpine 部分提取為可重複使用的 Blade 元件，以便在 Livewire 內（以及應用程式中的任何地方）使用。

這裡是一個範例（使用 Laravel 7 Blade 元件標記語法）。

**Livewire 檢視：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    ...

    <x-dropdown>
        <x-slot name="trigger">
            <button>Show More...</button>
        </x-slot>

        <ul>
            <li><button wire:click="archive">Archive</button></li>
            <li><button wire:click="delete">Delete</button></li>
        </ul>
    </x-dropdown>
</div>
@endverbatim
@endcomponent

**可重複使用的 "dropdown" Blade 元件：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<div x-data="{ open: false }">
    <span @click="open = true">{{ $trigger }}</span>

    <div x-show="open" @click.outside="open = false">
        {{ $slot }}
    </div>
</div>
@endverbatim
@endcomponent

現在，Livewire 和 Alpine 的語法完全分開，而且您有一個可從其他元件中使用的可重複使用的 Blade 元件。

## 從 Alpine 與 Livewire 互動：`$wire` {#interacting-with-livewire-from-alpine}

從 Livewire 元件內的任何 Alpine 元件，您可以訪問一個神奇的 `$wire` 物件，以訪問和操作 Livewire 元件。

為了展示其用法，我們將在 Alpine 中創建一個完全在 Livewire 內部使用的 "counter" 元件：

@component('components.code-component')
@slot('class')
@verbatim
class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    <!-- Alpine 計數器元件 -->
    <div x-data>
        <h1 x-text="$wire.count"></h1>

        <button x-on:click="$wire.increment()">Increment</button>
    </div>
</div>
@endverbatim
@endslot
@endcomponent

當使用者點擊「增加」時，標準的 Livewire 回程將觸發，Alpine 將反映 Livewire 的新 `$count` 值。

因為 `$wire` 在幕後使用 [JavaScript Proxy](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy)，您可以訪問其上的屬性並調用其上的方法，這些操作將被轉發到 Livewire。除了這個功能外，`$wire` 還有一些標準的內建方法可供您使用。

以下是 `$wire` 的完整 API：

@component('components.code', ['lang' => 'javascript'])
// 存取 Livewire 屬性
$wire.foo

// 呼叫 Livewire 方法
$wire.someMethod(someParam)

// 呼叫 Livewire 方法並處理其結果
$wire.someMethod(someParam)
    .then(result => { ... })

// 呼叫 Livewire 方法並使用 async/await 儲存其回應
let foo = await $wire.getFoo()

// 發送名為 "some-event" 的 Livewire 事件，帶有兩個參數
$wire.emit('some-event', 'foo', 'bar')

// 監聽名為 "some-event" 的 Livewire 事件
$wire.on('some-event', (foo, bar) => {})

// 取得 Livewire 屬性
$wire.get('property')

// 將 Livewire 屬性設置為特定值
$wire.set('property', value)

// 延遲將 Livewire 屬性設置為特定值
$wire.set('property', value, true)

// 呼叫 Livewire 動作
$wire.call('someMethod', param)

// 上傳檔案並設置 Livewire 屬性
$wire.upload(
    'property',
    file,
    finishCallback = (uploadedFilename) => {},
    errorCallback = () => {},
    progressCallback = (event) => {}
)

// 上傳多個檔案並設置 Livewire 屬性
$wire.uploadMultiple(
    'property',
    files,
    finishCallback = (uploadedFilenames) => {},
    errorCallback = () => {},
    progressCallback = (event) => {}
)

// 移除已上傳的檔案之一並更新 Livewire 屬性
$wire.removeUpload(
    'property',
    uploadedFilename,
    finishCallback = (uploadedFilename) => {},
    errorCallback = () => {}
)

// 存取底層 Livewire 元件的 JavaScript 實例
$wire.__instance
@endcomponent

## 在 Livewire 和 Alpine 之間共享狀態：@verbatim`@entangle`@endverbatim {#sharing-state}
Livewire 具有一個非常強大的功能，稱為 "entangle"，允許您將 Livewire 和 Alpine 的屬性 "entangle" 在一起。通過 entanglement，當一個值變化時，另一個值也會跟著變化。

為了演示，考慮之前的下拉示例，但現在將其 `showDropdown` 屬性在 Livewire 和 Alpine 之間進行了 entangle。通過使用 entanglement，我們現在能夠從 Alpine 和 Livewire 同時控制下拉的狀態。

@component('components.code-component')
@slot('class')
@verbatim
class Dropdown extends Component
{
    public $showDropdown = false;

    public function archive()
    {
        ...
        $this->showDropdown = false;
    }

    public function delete()
    {
        ...
        $this->showDropdown = false;
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div x-data="{ open: @entangle('showDropdown') }">
    <button @click="open = true">顯示更多...</button>

    <ul x-show="open" @click.outside="open = false">
        <li><button wire:click="archive">歸檔</button></li>
        <li><button wire:click="delete">刪除</button></li>
    </ul>
</div>
@endverbatim
@endslot
@endcomponent

現在用戶可以立即使用 Alpine 切換下拉，但當他們點擊 Livewire 的 "歸檔" 等操作時，下拉將從 Livewire 那裡被告知關閉。Alpine 和 Livewire 都可以操縱各自的屬性，另一方將自動更新。

有時，不需要在每次 Alpine 更改時更新 Livewire，您可能更願意將更改與下一個 Livewire 請求捆綁在一起。在這些情況下，您可以像這樣鏈接一個 `.defer` 屬性：

@component('components.code', ['lang' => 'javascript'])
@verbatim
<div x-data="{ open: @entangle('showDropdown').defer }">
    ...
@endverbatim
@endcomponent

現在，當用戶打開和關閉下拉時，不會發送 Livewire 的 AJAX 請求，但是當從像 "archive" 或 "delete" 這樣的按鈕觸發 Livewire 操作時，"showDropdown" 的新狀態將與請求一起捆綁。

如果您在遵循這個差異時遇到困難。打開您瀏覽器的開發者工具，觀察添加`.defer`和不添加`.defer`時的XHR請求的差異。

## 使用 `@verbatim@js@endverbatim` 指示詞 {#js-directive}

如果您需要輸出 PHP 數據以供 Alpine 使用，現在可以使用 `@verbatim@js@endverbatim` 指示詞。

@component('components.code', ['lang' => 'blade'])
@verbatim
<div x-data="{ posts: @js($posts) }">
    ...
</div>
@endverbatim
@endcomponent

## 從 Blade 元件中訪問 Livewire 指示 {#livewire-directives-from-blade-components}
在 Livewire 應用程序中提取可重用的 Blade 元件是一種基本模式。

在 Livewire 上下文中實現 Blade 元件時可能遇到的一個困難是從元件內部訪問像 `wire:model` 這樣的屬性值。

例如，您可以像這樣創建一個文本輸入 Blade 元件：

@component('components.code', ['lang' => 'blade'])
@verbatim
<!-- 使用 -->
<x-inputs.text wire:model="foo"/>

<!-- 定義 -->
<div>
    <input type="text" {{ $attributes }}>
</div>
@endverbatim
@endcomponent

這樣一個簡單的 Blade 元件將完美地運作。Laravel 和 Blade 將自動將添加到元件的任何額外屬性（在這種情況下是 `wire:model`）轉發並放置在 `<input>` 標籤上，因為我們輸出了屬性包（`$attributes`）。

但是，有時您可能需要提取有關傳遞給元件的 Livewire 屬性的更詳細信息。

對於這些情況，Livewire 提供了一個 `$attributes->wire()` 方法來幫助處理這些任務。

考慮以下 Blade 元件的使用情況：

@component('components.code', ['lang' => 'blade'])
@verbatim
<x-inputs.text wire:model.defer="foo" wire:loading.class="opacity-25"/>
@endverbatim
@endcomponent

您可以這樣從 Blade 的 `$attribute` 包中訪問 Livewire 指示信息：

@component('components.code', ['lang' => 'php'])
@verbatim
$attributes->wire('model')->value(); // "foo"
$attributes->wire('model')->modifiers(); // ["defer"]
$attributes->wire('model')->hasModifier('defer'); // true

```php
$attributes->wire('loading')->hasModifier('class'); // true
$attributes->wire('loading')->value(); // "opacity-25"
```

您也可以單獨“轉發”這些 Livewire 指示詞。例如：

```blade
<!-- Given -->
<x-inputs.text wire:model.defer="foo" wire:loading.class="opacity-25"/>

<!-- 您可以像這樣轉發“wire:model.defer="foo" 指示詞： -->
<input type="text" {{ $attributes->wire('model') }}>

<!-- 輸出將是： -->
<input type="text" wire:model.defer="foo">
```

有很多不同的方法可以使用此實用工具，但一個常見的示例是與上述的 `@entangle` 指示詞一起使用：

```blade
<!-- 使用 -->
<x-dropdown wire:model="show">
    <x-slot name="trigger">
        <button>Show</button>
    </x-slot>

    Dropdown Contents
</x-dropdown>

<!-- 定義 -->
<div x-data="{ open: @entangle($attributes->wire('model')) }">
    <span @click="open = true">{{ $trigger }}</span>

    <div x-show="open" @click.outside="open = false">
        {{ $slot }}
    </div>
</div>
```

> 注意：如果通過 `wire:model.defer` 傳遞了 `.defer` 修飾符，`@entangle` 指示詞將自動識別它並在幕後添加 `@entangle('...').defer` 修飾符。

## 創建日期選擇器元件 {#creating-a-datepicker}

在 Livewire 中使用 JavaScript 的常見用例是自定義表單輸入。像日期選擇器、顏色選擇器等通常對您的應用程序至關重要。

通過使用上面的相同模式（並添加一些額外的功能），我們可以利用 Alpine 輕鬆地與這些類型的 JavaScript 元件進行交互。

讓我們創建一個名為 `date-picker` 的可重複使用的 Blade 元件，我們可以在 Livewire 中使用 `wire:model` 將一些數據綁定到其中。

這是我們將如何使用它的方式：

```blade
<form wire:submit.prevent="schedule">
    <label for="title">活動標題</label>
    <input wire:model="title" id="title" type="text">
```  

```html
<label for="date">活動日期</label>
<x-date-picker wire:model="date" id="date"/>

<button>安排活動</button>
</form>
@endverbatim
@endcomponent

對於這個元件，我們將使用[Pikaday](https://github.com/Pikaday/Pikaday)函式庫。

根據文件，套件的最基本用法（在包含資源檔之後）如下所示：

@component('components.code', ['lang' => 'blade'])
@verbatim
<input type="text" id="datepicker">

<script>
    new Pikaday({ field: document.getElementById('datepicker') })
</script>
@endverbatim
@endcomponent

您只需要一個`<input>`元素，Pikaday將為您添加所有額外的日期選擇器行為。

現在讓我們看看如何為這個函式庫編寫一個可重複使用的Blade元件。

**`date-picker`可重複使用的Blade元件：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<input
    x-data
    x-ref="input"
    x-init="new Pikaday({ field: $refs.input })"
    type="text"
    {{ $attributes }}
>
@endverbatim
@endcomponent

> 注意：@verbatim {{ $attributes }} @endverbatim 表達式是Laravel 7及以上版本中的一種機制，用於轉發在元件標籤上聲明的額外HTML屬性。

## 轉發`wire:model` `input`事件 {#forwarding-wire-model-input-events}

在幕後，`wire:model`會添加一個事件監聽器，以便在元素上或下派發`input`事件時每次更新屬性。另一種在Livewire和Alpine之間通信的方式是使用Alpine在具有`wire:model`的元素中或上派發帶有某些數據的`input`事件。

讓我們創建一個虛構的例子，當用戶點擊第一個按鈕時，一個名為`$foo`的屬性設置為`bar`，當用戶點擊第二個按鈕時，`$foo`設置為`baz`。

**在Livewire元件的視圖中：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    <div wire:model="foo">
        <button x-data @click="$dispatch('input', 'bar')">設置為"bar"</button>
        <button x-data @click="$dispatch('input', 'baz')">設置為"baz"</button>
    </div>
</div>
@endverbatim
@endcomponent
```

**使用Color-picker元件：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    <x-color-picker wire:model="color"/>
</div>
@endverbatim
@endcomponent

對於元件定義，我們將使用一個名為[Vanilla Picker](https://vanilla-picker.js.org/)的第三方顏色選擇器庫。

此示例假設您已在頁面上載入它。

**Color-picker Blade元件定義（未註解）：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<div
    x-data="{ color: '#ffffff' }"
    x-init="
        picker = new Picker($refs.button);
        picker.onDone = rawColor => {
            color = rawColor.hex;
            $dispatch('input', color)
        }
    "
    wire:ignore
    {{ $attributes }}
>
    <span x-text="color" :style="`background: ${color}`"></span>
    <button x-ref="button">Change</button>
</div>
@endverbatim
@endcomponent

**Color-picker Blade元件定義（已註解）：**
@component('components.code', ['lang' => 'blade'])
@verbatim
<div
    x-data="{ color: '#ffffff' }"
    x-init="
        // 連接以在點擊“Change”按鈕時顯示選擇器。
        picker = new Picker($refs.button);
        // 每次選擇新顏色時運行此回調函式。
        picker.onDone = rawColor => {
            // 設置Alpine的“color”屬性。
            color = rawColor.hex;
            // 發送顏色屬性以供“wire:model”接收。
            $dispatch('input', color)
        }
    "
    // Vanilla Picker將在此元素內附加自己的DOM，因此我們需要
    // 添加`wire:ignore`以告訴Livewire跳過對其進行DOM差異比較。
    wire:ignore
    // 轉發添加到元件標記的任何屬性，如`wire:model=color`
    {{ $attributes }}
>
    <!-- 顯示當前顏色值，背景顏色設置為所選顏色。 -->
    <span x-text="color" :style="`background: ${color}`"></span>
    <!-- 點擊此按鈕時，將顯示顏色選擇對話框。 -->
    <button x-ref="button">Change</button>
</div>
@endverbatim
@endcomponent

## 忽略 DOM 變更（使用 `wire:ignore`） {#ignoring-dom-changes}

幸運的是，像 Pikaday 這樣的函式庫會將其額外的 DOM 添加到頁面末尾。許多其他函式庫在初始化時立即操控 DOM，並在與其互動時持續變更 DOM。

當發生這種情況時，Livewire 很難追蹤您希望在元件更新時保留的 DOM 操作，以及您希望丟棄的 DOM 操作。

要告訴 Livewire 忽略元件內部某個 HTML 子集的變更，您可以添加 `wire:ignore` 指示詞。

Select2 函式庫就是其中一個接管其 DOM 部分的函式庫（它會用大量自定義標記替換您的 `<select>` 標記）。

以下是在 Livewire 元件中使用 Select2 函式庫的示例，以演示 `wire:ignore` 的使用。

@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    <div wire:ignore> {{-- [tl! highlight] --}}
        <select class="select2" name="state">
            <option value="AL">Alabama</option>
            <option value="WY">Wyoming</option>
        </select>

        <!-- Select2 將在此處插入其 DOM。 -->
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush
@endverbatim
@endcomponent

@component('components.tip')
此外，有時忽略對元素的更改但不忽略其子元素是有用的。如果是這種情況，您可以將 `self` 修飾符添加到 `wire:ignore` 指示詞中，如下所示：`wire:ignore.self`。
@endcomponent

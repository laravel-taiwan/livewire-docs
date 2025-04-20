* [簡介](#introduction)
* [安全措施](#security-measures)
    * [校驗和驗證](#the-checksum)
    * [持久中介層](#persistent-middleware)

## 簡介 {#introduction}

對於新的 Livewire 開發者來說，這種體驗有些神奇。當頁面加載時，你的 Livewire 元件就好像在伺服器上運行，監聽來自瀏覽器的更新並實時響應它們。

這與其他類似工具如 [Phoenix LiveView](https://dockyard.com/blog/2018/12/12/phoenix-liveview-interactive-real-time-apps-no-need-to-write-javascript) 的工作方式並沒有太大差異。

儘管 Livewire 感覺相似，但它有著完全不同的內部運作方式，具有自己的一套優缺點和安全性影響。

Livewire 元件感覺“有狀態”，但它們完全“無狀態”。伺服器上沒有長時間運行的 Livewire 實例等待瀏覽器交互。每次交互都是全新的請求/回應。

為了更全面地理解這種心智模型，讓我們以以下簡單的“計數器”元件作為起點。

@component('components.code-component', [
    'className' => 'app/Http/Livewire/Counter.php',
    'viewName' => 'resources/views/livewire/counter.blade.php',
])
@slot('class')
@verbatim
class Counter extends Component
{
    public $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    <h1>{{ $count }}</h1>

    <button wire:click="increment">+</button>
</div>
@endverbatim
@endslot
@endcomponent

從用戶的角度來看，使用這個“計數器”的體驗如下：用戶加載頁面，看到數字“1”，點擊“+”按鈕，現在看到數字“2”。

下面是 Livewire 實際運作以實現此效果的可視化。

<img src="/img/lifecycle_timeline.svg" />

總結一下，當用戶訪問包含“計數器”元件的頁面時，會像訪問其他頁面一樣向伺服器發送正常請求。該頁面渲染“計數器”的初始視圖，就像渲染任何普通 Blade 元件一樣，但除了渲染 HTML 外，Livewire 還會“脫水化”或“序列化”元件的狀態（公共屬性）並將其傳遞給前端。

現在前端具有元件狀態後，當觸發更新（在這種情況下是點擊 "+"）時，會向伺服器發送請求，包括最後已知的元件狀態。伺服器會從該狀態"水合"或"反序列化"元件並執行任何更新。

元件現在再次被"脫水"，以提供瀏覽器新渲染的 HTML 和更新後的狀態，供稍後互動請求使用。

以下是在這些請求期間實際元件生命週期的更深入視覺化。

<img src="/img/lifecycle_flow.svg" />

希望現在您已經採用了更準確的心智模型，了解 Livewire 在幕後的運作方式。這將使您能更智能地除錯問題，並理解使用 Livewire 的性能和安全性影響。

## 安全措施 {#security-measures}

就像您上面所學的，每個 Livewire 請求在某種意義上是"無狀態"的，即沒有長時間運行的伺服器實例來維護狀態。狀態存儲在瀏覽器中，並在請求之間與伺服器來回傳遞。

由於狀態存儲在瀏覽器中，容易受到前端操控的影響。如果沒有設置安全措施，惡意人士很容易在請求之間操控元件的狀態。

在我們的"計數器"範例中，操控像"計數"這樣微不足道且短暫的東西並沒有真正的負面影響，但在一個有更多風險的元件中，例如帶有刪除按鈕的"編輯文章"元件，就需要設置安全措施。

### 校驗碼 {#the-checksum}

Livewire 的基本安全基礎是一個"校驗碼"，隨著請求/回應一起傳遞，用於驗證伺服器的狀態在瀏覽器中未被篡改。

進一步解釋，考慮上面的"計數器"元件。Livewire 不僅僅將 <span style="white-space: nowrap">`{ count: 1 }`</span> 傳遞給瀏覽器，而是將使用安全金鑰生成該有效載荷的哈希（校驗碼），並將其與狀態一起傳遞。

一個更真實的 Livewire 載荷表示 "counter" 的方式可能如下所示：

@component('components.code', ['lang' => 'js'])
{
    state: { count: 1 },
    checksum: "A6jHn359Ku3lFc82arW8",
}
@endcomponent

現在，如果一個惡意人士在請求之間在瀏覽器中篡改了狀態，在 Livewire 處理組件更新之前，它將看到載荷的哈希值與校驗和不匹配，並拋出錯誤。

### 持久中介層 {#persistent-middleware}

Livewire 實施的第二個安全措施是 "持久中介層"。這意味著 Livewire 將捕獲在 "初始請求" 過程中使用的任何認證/授權中介層，並重新應用到後續請求中。

如果沒有這個措施，Livewire 的後續請求可能會在用戶登出應用程序並不再應該訪問這些代碼路徑之後被捕獲並重播。

默認情況下，Livewire 重新應用每個 Laravel 應用程序都附帶的開箱即用的認證和授權中介層。以下是一些默認值：

@component('components.code', ['lang' => 'php'])
[
    ...
    \Illuminate\Auth\Middleware\Authenticate::class,
    \Illuminate\Auth\Middleware\Authorize::class,
]
@endcomponent

如果您希望添加自己的中介層以便在存在時被捕獲並重新應用，您可以在應用的服務提供者中使用以下 API：

@component('components.code', ['lang' => 'php'])
Livewire::addPersistentMiddleware([
    YourOwnMiddleware::class,
]);
@endcomponent

現在，您添加的任何中介層將在後續的 Livewire 請求中重新應用，如果該中介層被分配給加載組件的原始路由。

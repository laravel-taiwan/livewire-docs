* [模板指令](#template-directives)
* [Alpine 元件物件 (`$wire`)](#alpine-component-object)
* [全域 Livewire JavaScript 物件](#global-livewire-js)
* [JavaScript 鉤子](#js-hooks)
* [元件類別生命週期鉤子](#component-class-lifecycle)
* [元件類別保護屬性](#component-class-protected-properties)
* [元件類別特性](#component-class-traits)
* [類別方法](#class-methods)
* [PHP 測試方法](#php-testing-methods)
* [Artisan 指令](#artisan-commands)
* [PHP 生命週期鉤子](#php-lifecycle-hooks)

已經熟悉 Livewire 並想跳過冗長的文件？這裡有 Livewire 中所有可用功能的巨大清單。

### 模板指令 {#template-directives}
這些是添加到 Livewire 元件模板中元素的指令。

@component('components.code', ['lang' => 'blade'])
<button wire:click="save">...</button>
@endcomponent

@component('components.table')
指令 | 說明
--- | ---
`wire:key="foo"` | 作為 Livewire 的 DOM 差異系統的參考點。用於添加/移除元素，以及跟踪清單。
`wire:click="foo"` | 監聽 "click" 事件，並在元件中觸發 "foo" 方法。
`wire:click.prefetch="foo"` | 監聽 "mouseenter" 事件，並 "預取" 元件中 "foo" 方法的結果。然後，如果被點擊，將交換 "預取" 的結果（無需額外請求），如果未被點擊，將捨棄快取的結果。
`wire:keydown.enter="foo"` | 監聽 `enter` 鍵的 keydown 事件，觸發元件中的 "foo" 方法。
`wire:foo="bar"` | 監聽名為 "foo" 的瀏覽器事件。（您可以監聽 *任何* 瀏覽器 DOM 事件 - 不僅僅是 Livewire 觸發的事件）。
`wire:model="foo"` | 假設 `$foo` 是元件類別上的公共屬性，每當更新具有此指令的輸入元素時，該屬性與其值同步。
`wire:model.debounce.100ms="foo"` | 每 100 毫秒阻止元素發出的 `input` 事件。
`wire:model.lazy="foo"` | 在休息時懶惰地將輸入與其對應的元件屬性同步。
`wire:model.defer="foo"` | 推遲將輸入與 Livewire 屬性同步，直到執行 "動作" 為止。這在節省服務器往返時有很大幫助。
`wire:poll.500ms="foo"` | 每 500 毫秒在元件類別上運行 "foo" 方法。
`wire:init="foo"` | 在元件渲染在頁面上後立即運行 "foo" 方法。
`wire:loading` | 默認情況下隱藏元素，並在網絡請求傳輸時顯示。
`wire:loading.class="foo"` | 在網絡請求傳輸時將 `foo` 類添加到元素。
`wire:loading.class.remove="foo"` | 在網絡請求傳輸時刪除 `foo` 類。
`wire:loading.attr="disabled"` | 在網絡請求傳輸時添加 `disabled="true"` 屬性。
`wire:dirty` | 默認情況下隱藏元素，並在元素狀態為 "dirty"（與後端存在的不同）時顯示。
`wire:dirty.class="foo"` | 在元素為 dirty 時將 `foo` 類添加到元素。
`wire:dirty.class.remove="foo"` | 在元素為 dirty 時刪除 `foo` 類。
`wire:dirty.attr="disabled"` | 在元素為 dirty 時添加 `disabled="true"` 屬性。
`wire:target="foo"` | 將 `wire:loading` 和 `wire:dirty` 功能範圍限定為特定操作。
`wire:ignore` | 指示 Livewire 在從服務器請求更新 DOM 時不更新元素或其子元素。在 Livewire 元件中使用第三方 JavaScript 函式庫時很有用。
`wire:ignore.self` | "self" 修飾符限制更新僅限於元素本身，但允許修改其子元素。
@endcomponent

### Alpine 元件物件 (`$wire`) {#alpine-component-object}

這些是提供給 Livewire 模板中 Alpine 元件的 `$wire` 物件上可用的方法和屬性。[閱讀完整文件](/docs/2.x/alpine-js)

@component('components.code', ['lang' => 'blade'])
<div x-data>
    <span x-show="$wire.showMessage">...</span>

    <button x-on:click="$wire.toggleShowMessage()">...</button>
</div>
@endcomponent

@component('components.table')
API | 說明
--- | ---
`$wire.foo` | 取得 Livewire 元件上 "foo" 屬性的值
`$wire.foo = 'bar'` | 設定 Livewire 元件上 "foo" 屬性的值
`$wire.bar(..args)` | 呼叫 Livewire 元件上 "bar" 方法（帶參數）
`let baz = await $wire.bar(..args)` | 呼叫 "bar" 方法，但等待回應並將 `baz` 設為其值
`$wire.on('foo', (..args) => {})` | 當 "foo" 事件被觸發時呼叫函式
`$wire.emit('foo', ...args)` | 對所有 Livewire 元件發送 "foo" 事件
`$wire.emitUp('foo', ...args)` | 對父元件發送 "foo" 事件
`$wire.emitSelf('foo', ...args)` | 只對此元件發送 "foo" 事件
`$wire.get('foo')` | 取得 "foo" 屬性
`$wire.set('foo', 'bar')` | 在元件上設定 "foo" 屬性
`$wire.set('foo', 'bar', true)` | 延遲設定元件上的 "foo" 屬性
`$wire.call('foo', ..args)` | 呼叫元件上帶參數的 "foo" 方法
`x-data="{ foo: $wire.entangle('foo') }"` | 在 Livewire 和 Alpine 之間綁定 "foo" 的值
`$wire.entangle('foo').defer` | 只在下次 Livewire 請求觸發時更新 Livewire 的 "foo"
@endcomponent

### 全域 Livewire JavaScript 物件 {#global-livewire-js}

這些是前端中 `window.Livewire` 物件上可用的方法。這些方法用於更深入的 Livewire 互動和自定義。

@component('components.table')
方法 | 說明
--- | ---
`Livewire.first()` | 取得頁面上第一個 Livewire 元件的 JS 物件
`Livewire.find(componentId)` | 透過 ID 取得 Livewire 元件
`Livewire.all()` | 取得頁面上所有 Livewire 元件
`Livewire.directive(directiveName, (el, directive, component) => {})` | 註冊新的 Livewire 指示詞 (`wire:custom-directive`)
`Livewire.hook(hookName, (...) => {})` | 當 JS 生命週期鉤子觸發時呼叫方法。[閱讀更多](#js-hooks)
`Livewire.onLoad(() => {})` | 當 Livewire 首次在頁面上完成載入時觸發
`Livewire.onError((message, statusCode) => {})` | 當 Livewire 請求失敗時觸發。您可以在回調函式中 `return false` 以防止 Livewire 的預設行為
`Livewire.onPageExpired((response, message) => {})` | 當頁面或會話已過期時，執行回調函式而不是 Livewire 的 [頁面過期對話框](/docs/2.x/deployment#page-expired-dialog)
`Livewire.emit(eventName, ...params)` | 對所有頁面上監聽 Livewire 元件發送事件
`Livewire.emitTo(componentName, eventName, ...params)` | 對特定元件名稱發送事件
`Livewire.on(eventName, (...params) => {})` | 監聽從元件發送的事件
`Livewire.start()` | 在頁面上啟動 Livewire（透過 `@@livewireScripts` 自動完成）
`Livewire.stop()` | 從頁面上拆除 Livewire
`Livewire.restart()` | 停止，然後重新啟動頁面上的 Livewire
`Livewire.rescan()` | 重新掃描 DOM 以尋找新增的 Livewire 元件
@endcomponent

### JavaScript Hooks {#js-hooks}

這些是您可以在 JavaScript 中監聽的 "hooks"。這些允許您鉤入 Livewire 元件的 JavaScript 生命週期的非常特定部分，以供第三方套件或深度自定義使用。這裡解鎖的功能是巨大的。Livewire 的核心部分使用這些 hooks 的一個重要部分來提供功能。

@component('components.code', ['lang' => 'javascript'])
Livewire.hook('component.initialized', component => {
    //
})
@endcomponent

@component('components.table')
名稱 | 參數 | 說明
--- | --- | ---
`component.initialized` | `(component)` | 已初始化新元件
`element.initialized` | `(el, component)` | 已初始化新元素
`element.updating` | `(fromEl, toEl, component)` | 在 Livewire 請求後，元素即將更新
`element.updated` | `(el, component)` | 元素剛從 Livewire 請求中更新
`element.removed` | `(el, component)` | 元素已在 Livewire 請求後移除
`message.sent` | `(message, component)` | 新的 Livewire 訊息剛發送到伺服器
`message.failed` | `(message, component)` | Livewire ajax 請求 (訊息) 失敗
`message.received` | `(message, component)` | 已接收訊息 (但尚未影響 DOM)
`message.processed` | `(message, component)` | 訊息已完全接收並實施 (DOM 更新等等...)
@endcomponent

### 元件類別生命週期 Hooks {#component-class-lifecycle}

這些是您可以在 Livewire 元件類別中聲明的方法，以在後端生命週期的特定時間運行代碼。[閱讀完整文件](/docs/2.x/lifecycle-hooks)

@component('components.code', ['lang' => 'php'])
class ShowPost extends Component
{
    public function mount()
    {
        //
    }
}
@endcomponent

@component('components.table')
名稱 | 說明
--- | ---
`boot()` | 在所有請求上調用，在元件實例化後立即調用，但在調用任何其他生命週期方法之前
`booted()` | 在所有請求上調用，在元件掛載或填充後，但在調用任何更新方法之前
`mount(...$params)` | 當 Livewire 元件新建時調用 (類似於建構子)
`hydrate()` | 在元件被填充後的後續 Livewire 請求上調用，但在發生任何其他操作之前
`hydrateFoo()` | 在名為 $foo 的屬性被填充後運行
`dehydrate()` | 在 `render()` 後調用，但在元件被脫水化並發送到前端之前
`dehydrateFoo()` | 在名為 $foo 的屬性被脫水化前運行
`updating()` | 在更新 Livewire 元件數據之前運行 (使用 wire:model，不直接在 PHP 內部)
`updated($field, $newValue)` | 在屬性更新後調用
`updatingFoo()` | 在名為 $foo 的屬性更新前運行
`updatedFoo($newValue)` | 在 "foo" 屬性更新後調用
`updatingFooBar()` | 在更新 $foo 屬性上的嵌套屬性 bar 前運行
`updatedFooBar($newValue)` | 在 "foo" 屬性上的嵌套 "bar" 鍵更新後調用
`render()` | 在 "脫水" 之前調用並為元件渲染 Blade 視圖
@endcomponent

### 元件類別保護屬性 {#component-class-protected-properties}

Livewire 透過元件類別的保護屬性提供核心功能。如果您更喜歡透過方法返回值，而不是將它們聲明為屬性，則大多數這些屬性都有相應的同名方法。

@component('components.code', ['lang' => 'php'])
class ShowPost extends Component
{
    protected $rules = ['foo' => 'required|min:6'];
}
@endcomponent

@component('components.table')
名稱 | 說明
--- | ---
`$queryString` | 聲明要與查詢字串“綁定”的屬性。[閱讀文件](/docs/2.x/query-string)
`$rules` | 指定在調用 `$this->validate()` 時應用於屬性的驗證規則。[閱讀文件](/docs/2.x/input-validation)
`$listeners` | 指定您希望聆聽其他元件發出的事件。[閱讀文件](/docs/2.x/events)
`$paginationTheme` | 指定您希望使用 Tailwind 或 Bootstrap 作為分頁主題。[閱讀文件](/docs/2.x/pagination)
@endcomponent

### 元件類別特性 {#component-class-traits}

這些是在 Livewire 元件中解鎖附加功能的特性。通常用於被視為“選擇加入”的功能。

@component('components.code', ['lang' => 'php'])
class ShowPost extends Component
{
    use WithPagination;
}
@endcomponent

@component('components.table')
名稱 | 說明
--- | ---
`WithPagination` | 此特性啟用基於 Livewire 的分頁，而不是 Laravel 的標準分頁系統。[閱讀文件](/docs/2.x/pagination)
`WithFileUploads` | 此特性啟用將 `wire:model` 添加到 `type="file"` 的輸入。[閱讀文件](/docs/2.x/file-uploads)
@endcomponent

### 類別方法 {#class-methods}

@component('components.code', ['lang' => 'php'])
class PostForm extends Component
{
    public function save()
    {
        ...

        $this->emit('post-saved');
    }
}
@endcomponent

@component('components.table')
名稱 | 說明
--- | ---
`$this->emit($eventName, ...$params)` | 向頁面上的其他元件發送事件
`$this->emit($eventName, ...$params)->up()` | 向頁面上的父元件發送事件
`$this->emit($eventName, ...$params)->self()` | 僅向此元件發送事件
`$this->emit($eventName, ...$params)->to($componentName)` | 向與提供的名稱匹配的任何元件發送事件
`$this->dispatchBrowserEvent($eventName, ...$params)` | 從此元件的根元素發送瀏覽器事件
`$this->validate()` | 根據 `$rules` 屬性中提供的驗證規則運行對公共元件屬性的驗證
`$this->validate($rules, $messages)` | 根據提供的驗證規則運行對公共屬性的驗證
`$this->validateOnly($propertyName)` | 對提供的特定屬性運行 `$rules` 屬性驗證，而不是其他屬性
`$this->validateOnly($propertyName, $rules, $messages)` | 對特定屬性名運行提供的驗證規則
`$this->redirect($url)` | 當 Livewire 請求完成並到達前端時，重定向到新的 URL
`$this->redirectRoute($routeName)` | 重定向到特定路由名
`$this->skipRender()` | 跳過當前請求的 `->render()` 方法運行（通常出於性能原因）
`$this->addError($name, $error)` | 手動將特定錯誤名稱和值添加到元件的錯誤包中
`$this->resetValidation()` | 重置當前存儲的驗證錯誤（清除它們）
`$this->fill([...$propertyData])` | 批量將公共屬性名設置為提供的值
`$this->reset()` | 將所有公共屬性重置為它們的初始（掛載前）狀態
`$this->reset($field)` | 將特定公共屬性重置為其掛載前狀態
`$this->reset([...$fields])` | 重置多個特定屬性
`$this->all()` | 返回屬性數據的鍵->值對
`$this->only([...$propertyNames])` | 僅返回特定一組屬性名的屬性數據的鍵->值對
`$this->except([...$propertyNames])` | 返回除特定一組屬性名之外的屬性數據的鍵->值對
@endcomponent

### PHP 測試方法 {#php-testing-methods}

這些是 Livewire 測試輔助工具上可用的方法。[閱讀完整文檔](/docs/2.x/testing)

@component('components.code', ['lang' => 'php'])
public function test()
{
    Livewire::test(ShowPost::class)
        ->assertDontSee('bar')
        ->set('foo', 'bar')
        ->assertSee('bar');
}
@endcomponent

@component('components.table')
名稱 |
--- |
`->assertSet($propertyName, $value)` |
`->assertNotSet($propertyName, $value)` |
`->assertCount($propertyName, $value)` |
`->assertPayloadSet($propertyName, $value)` |
`->assertPayloadNotSet($propertyName, $value)` |
`->assertSee($string)` |
`->assertDontSee($string)` |
`->assertSeeHtml($string)` |
`->assertDontSeeHtml($string)` |
`->assertSeeHtmlInOrder([$firstString, $secondString])` |
`->assertSeeInOrder([$firstString, $secondString])` |
`->assertEmitted($eventName)` |
`->assertNotEmitted($eventName)` |
`->assertDispatchedBrowserEvent($eventName)` |
`->assertHasErrors($propertyName)` |
`->assertHasErrors($propertyName, ['required', 'min:6'])` |
`->assertHasNoErrors($propertyName)` |
`->assertHasNoErrors($propertyName, ['required', 'min:6'])` |
`->assertRedirect()` |
`->assertRedirect($url)` |
`->assertViewHas($viewDataKey)` |
`->assertViewHas($viewDataKey, $expectedValue)` |
`->assertViewHas($viewDataKey, function ($dataValue) {})` |
`->assertViewIs('livewire.some-view-name')` |
`->assertFileDownloaded($filename)` |
@endcomponent

同時還有 Laravel 測試回應輔助工具可用於檢查在給定頁面上組件的存在。

@component('components.table')
名稱 |
--- |
`$response->assertSeeLivewire('some-component')` |
`$response->assertDontSeeLivewire('some-component')` |
@endcomponent

### Artisan 指令 {#artisan-commands}

這些是 Livewire 提供的 `artisan` 指令，可讓您更輕鬆地執行頻繁任務，如創建組件。

@component('components.table')
名稱 | 參數 | 描述
--- | --- | ---
`artisan make:livewire` | 創建新組件
`artisan livewire:make` | 創建新組件
`artisan livewire:copy` | 複製組件
`artisan livewire:move` | 移動組件
`artisan livewire:delete` | 刪除組件
`artisan livewire:touch` | `livewire:make` 的別名
`artisan livewire:cp` | `livewire:copy` 的別名
`artisan livewire:mv` | `livewire:move` 的別名
`artisan livewire:rm` | `livewire:delete` 的別名
`artisan livewire:stubs` | 發布 Livewire 樣板（用於上述指令中）以進行本地修改
`artisan livewire:publish` | 將 Livewire 的配置文件發布到您的專案（`config/livewire.php`）
`artisan livewire:publish --assets` | 將 Livewire 的配置文件和前端資源發布到您的專案
`artisan livewire:configure-s3-upload-cleanup` | 配置您的雲磁碟驅動器的 S3 存儲桶以在 24 小時後清除臨時上傳
@endcomponent

### PHP 生命周期掛勾 {#php-lifecycle-hooks}

這些是 Livewire 在 PHP 中提供的掛勾，用於在全局層面監聽生命週期事件（而不是在組件層面）。這些在內部使用，為 Livewire 的核心功能提供了重要部分，並且可以在 ServiceProviders 中使用來進一步擴展 Livewire。

@component('components.code', ['lang' => 'php'])
Livewire::listen('component.hydrate', function ($component, $request) {
    //
});
@endcomponent

@component('components.table')
名稱 | 參數 | 描述
--- | ---
`component.hydrate` | `($component, $request)` | 在每次組件水合時運行
`component.hydrate.initial` | `($component, $request)` | 僅在初始水合時運行（當組件首次加載時）
`component.hydrate.subsequent` | `($component, $request)` | 僅在初始組件請求後運行
`component.dehydrate` | `($component, $response)` | 在每次組件脫水時運行
`component.dehydrate.initial` | `($component, $response)` | 僅在初始脫水時運行（當組件首次加載時）
`component.dehydrate.subsequent` | `($component, $response)` | 在初始組件請求後運行脫水
`property.hydrate` | `($name, $value, $component, $request)` | 當特定屬性被水合時運行
`property.dehydrate` | `($name, $value, $component, $response)` | 當特定屬性被脫水時運行
@endcomponent

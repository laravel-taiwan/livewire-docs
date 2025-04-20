* [簡介](#introduction)
* [傳遞行為引數](#action-parameters)
* [事件修飾符](#event-modifiers)
  * [按鍵按下修飾符](#keydown-modifiers)
* [魔法行為](#magic-actions)

## 簡介 {#introduction}

Livewire 中的行為目標是能夠輕鬆地監聽頁面互動，並在 Livewire 元件上調用方法（重新呈現元件）。

以下是基本用法：

@component('components.code-component')
@slot('class')
class ShowPost extends Component
{
    public Post $post;

    public function like()
    {
        $this->post->addLikeBy(auth()->user());
    }
}
@endslot
@slot('view')
@verbatim
<div>
    <button wire:click="like">Like Post</button>
</div>
@endverbatim
@endslot
@endcomponent

目前 Livewire 提供了一些指示詞，使監聽瀏覽器事件變得輕鬆。它們的通用格式是：`wire:[觸發的瀏覽器事件]="[行為]"`。

以下是您可能需要監聽的一些常見事件：

@component('components.table')
事件 | 指示詞
--- | ---
click | `wire:click`
keydown | `wire:keydown`
submit | `wire:submit`
@endcomponent

以下是在 HTML 中的一些示例：

@component('components.code')
<button wire:click="doSomething">Do Something</button>
@endcomponent

@component('components.code')
<input wire:keydown.enter="doSomething">
@endcomponent

@component('components.code')
<form wire:submit.prevent="save">
    ...

    <button>Save</button>
</form>
@endcomponent

@component('components.tip')
您可以監聽綁定到的元素發送的任何事件。假設您有一個元素發送名為 "foo" 的瀏覽器事件，您可以這樣監聽該事件：<code>&lt;button wire:foo="someAction"&gt;</code>
@endcomponent

@component('components.warning')
像上面的例子一樣，直接在表單開始標記處使用 `wire:submit.prevent` 將在請求期間為表單內的所有 HTML 元素生成 "readonly" 屬性。
@endcomponent

## 傳遞行為引數 {#action-parameters}

您可以直接在表達式中將額外的參數傳遞給 Livewire 行為，如下所示：


@component('components.code')
@verbatim

<button wire:click="addTodo({{ $todo->id }}, '{{ $todo->name }}')">
    新增待辦事項
</button>

@endverbatim
@endcomponent

對動作傳遞的額外參數，將作為標準的 PHP 參數傳遞到元件方法中：

@component('components.code', ['lang' => 'php'])
@verbatim

public function addTodo($id, $name)
{
    ...
}

@endverbatim
@endcomponent

動作參數還可以直接使用類型提示來解析模型的鍵。

@component('components.code', ['lang' => 'php'])
@verbatim

public function addTodo(Todo $todo, $name)
{
    ...
}

@endverbatim
@endcomponent

如果您的動作需要通過 Laravel 的依賴注入容器解析的任何服務，您可以在動作的簽名中列出它們，放在任何額外參數之前：

@component('components.code', ['lang' => 'php'])
@verbatim

public function addTodo(TodoService $todoService, $id, $name)
{
    ...
}

@endverbatim
@endcomponent

## 事件修飾符 {#event-modifiers}

就像您在 **keydown** 範例中看到的那樣，Livewire 指令有時會提供 "修飾符" 來為事件添加額外功能。以下是可與任何事件一起使用的可用修飾符。

@component('components.table')
修飾符 | 說明
--- | ---
stop | 等同於 `event.stopPropagation()`
prevent | 等同於 `event.preventDefault()`
self | 只有在事件是在自身上觸發時才觸發動作。這可以防止外部元素捕獲從子元素觸發的事件。(就像在模態框背景上註冊監聽器時經常發生的情況)
debounce.150ms | 對動作的處理添加 X 毫秒的防彈跳。
@endcomponent

### Keydown 修飾符 {#keydown-modifiers}

要在 **keydown** 事件上聆聽特定按鍵，您可以將按鍵的名稱作為修飾符傳遞。您可以直接使用 [KeyboardEvent.key](https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent.key/Key_Values) 公開的任何有效按鍵名稱，將它們轉換為 kebab-case。

這裡是您可能需要的一些常見事項的快速清單：

@component('components.table')
原生瀏覽器事件 | Livewire 修改器
--- | ---
Backspace | backspace
Escape | escape
Shift | shift
Tab | tab
ArrowRight | arrow-right
@endcomponent

@component('components.code')
<input wire:keydown.page-down="foo">
@endcomponent

在上面的示例中，只有當 `event.key` 等於 'PageDown' 時，處理程序才會被調用。

## 魔法操作 {#magic-actions}
在 Livewire 中，通常以 "$" 符號為前綴的一些 "魔法" 操作：

@component('components.table')
函式 | 描述
--- | ---
$refresh | 將重新渲染組件，而不觸發任何操作
$set('_property_', _value_) | 更新屬性值的快捷方式
$toggle('_property_') | 切換布爾屬性的快捷方式
$emit('_event_', _...params_) | 將在全局事件總線上發出事件，並提供參數
$event | 一個 _特殊_ 變數，保存觸發操作的事件值。示例用法：`wire:change="setSomeProperty($event.target.value)"`
@endcomponent

您可以將這些作為事件監聽器的值傳遞，以在 Livewire 中執行特殊操作。

讓我們以 `$set()` 為例。它可用於手動設置組件屬性的值。考慮 `Counter` 組件的視圖。

**之前**

@component('components.code', ['lang' => 'php'])
@verbatim
<div>
    {{ $message }}
    <button wire:click="setMessageToHello">Say Hi</button>
</div>
@endverbatim
@endcomponent

**之後**

@component('components.code', ['lang' => 'php'])
@verbatim
<div>
    {{ $message }}
    <button wire:click="$set('message', 'Hello')">Say Hi</button>
</div>
@endverbatim
@endcomponent

請注意，我們不再調用 `setMessageToHello` 函式，而是直接指定我們要設置的數據。

它也可以在後端用於監聽事件。例如，如果您有一個發出事件的組件，像這樣：

@component('components.code', ['lang' => 'php'])
@verbatim
$this->emit('some-event');
@endverbatim
@endcomponent

然後在另一個元件中，您可以使用一個魔法動作，例如 `$refresh()`，而不必指向一個方法的監聽器：

@component('components.code', ['lang' => 'php'])
@verbatim
protected $listeners = ['some-event' => '$refresh'];
@endverbatim
@endcomponent

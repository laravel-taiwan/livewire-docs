* [安裝 Livewire](#install-livewire)
* [建立元件](#create-a-component)
* [包含元件](#include-the-component)
* [在瀏覽器中檢視](#view-in-browser)
* [新增 "counter" 功能](#add-counter)
* [在瀏覽器中檢視](#view-in-browser-finally)

## 安裝 Livewire {#install-livewire}

包含 PHP。

@component('components.code', ['lang' => 'shell'])
composer require livewire/livewire
@endcomponent

包含 JavaScript（在每個將使用 Livewire 的頁面上）。

@component('components.code', ['lang' => 'blade'])
@verbatim
    ...
    @livewireStyles
</head>
<body>
    ...

    @livewireScripts
</body>
</html>
@endverbatim
@endcomponent

## 建立元件 {#create-a-component}

執行以下命令以生成名為 `counter` 的新 Livewire 元件。

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire counter
@endcomponent

執行此命令將生成以下兩個文件：

@component('components.code-component', [
    'className' => 'app/Http/Livewire/Counter.php',
    'viewName' => 'resources/views/livewire/counter.blade.php',
])
@slot('class')
@verbatim
namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
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
    ...
</div>
@endverbatim
@endslot
@endcomponent

讓我們在視圖中添加一些文本，以便在瀏覽器中看到一些具體的內容。

@component('components.tip')
Livewire 元件必須具有單一根元素。
@endcomponent

@component('components.code-component', [
    'viewName' => 'resources/views/livewire/counter.blade.php',
])
@slot('view')
@verbatim
<div>
    <h1>Hello World!</h1>
</div>
@endverbatim
@endslot
@endcomponent

## 包含元件 {#include-the-component}
@verbatim
將 Livewire 元件視為 Blade 包含。您可以在 Blade 視圖中的任何位置插入 `<livewire:some-component />`，它將呈現。
@endverbatim

@component('components.code', ['lang' => 'blade'])
@verbatim
<head>
    ...
    @livewireStyles
</head>
<body>
    <livewire:counter /> {{-- [tl! highlight] --}}


## 在瀏覽器中查看 {#view-in-browser}

在瀏覽器中載入您包含 Livewire 的頁面。您應該會看到 "Hello World!"。

## 添加 "counter" 功能 {#add-counter}

將 `counter` 組件類別和視圖的生成內容替換為以下內容：

@component('components.code-component', [
    'className' => 'app/Http/Livewire/Counter.php',
    'viewName' => 'resources/views/livewire/counter.blade.php',
])
@slot('class')
@verbatim
class Counter extends Component
{
    public $count = 0;

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
<div style="text-align: center">
    <button wire:click="increment">+</button>
    <h1>{{ $count }}</h1>
</div>
@endverbatim
@endslot
@endcomponent

## 在瀏覽器中查看 {#view-in-browser-finally}

現在在瀏覽器中重新載入頁面，您應該會看到 `counter` 組件已呈現。如果您點擊 "+" 按鈕，頁面應該會在無需重新載入頁面的情況下自動更新。魔法 🧙‍♂️。

@component('components.tip')
一般來說，像 "counter" 這樣的事情更適合使用 AlpineJS 之類的工具，但這是最容易理解 Livewire 工作方式的最佳方式之一。
@endcomponent

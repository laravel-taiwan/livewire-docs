* [執行 `make` 指令](#make-command)
    * [修改 Stubs](#modifying-stubs)
* [執行 `move` 指令](#move-command)
* [執行 `copy` 指令](#copy-command)
* [執行 `delete` 指令](#delete-command)

## 執行 `make` 指令 {#make-command}

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire foo
# 建立 Foo.php & foo.blade.php

php artisan make:livewire foo-bar
# 建立 FooBar.php & foo-bar.blade.php

php artisan make:livewire Foo
# 建立 Foo.php & foo.blade.php

php artisan make:livewire FooBar
# 建立 FooBar.php & foo-bar.blade.php

php artisan make:livewire foo.bar
# 建立 Foo/Bar.php & foo/bar.blade.php

php artisan make:livewire foo --inline
# 僅建立 Foo.php

php artisan make:livewire foo --test
# 建立 Foo.php, foo.blade.php, & FooTest.php
@endcomponent

建立後，您可以在 Blade 檔案中使用 <code>&#64;livewire('component-name')</code> blade 指示詞來呈現您的元件。

將 Livewire 元件視為 Blade 包含。您可以在 Blade 檢視中的任何位置插入 <code>&#64;livewire</code> 並呈現。

@component('components.code', ['lang' => 'php'])
@verbatim
@livewire('foo')
@livewire('foo-bar')
@livewire('foo.bar')
@livewire(Package\Livewire\Foo::class)
@endverbatim
@endcomponent

如果您使用 Laravel 7 或更高版本，您可以使用標籤語法。

@component('components.code', ['lang' => 'blade'])
@verbatim
<livewire:foo />
@endverbatim
@endcomponent

### 修改 Stubs {#modifying-stubs}

您可以使用 `livewire:stubs` 指令自訂 Livewire 用於建立新元件類別和檢視的 Stubs（模板）。

@component('components.code', ['lang' => 'shell'])
php artisan livewire:stubs
@endcomponent

上述指令將建立三個檔案：

* `stubs/livewire.stub`
* `stubs/livewire.view.stub`
* `stubs/livewire.inline.stub`

現在，當您執行 `make:livewire` 指令時，Livewire 將使用上述 stub 檔案作為模板。

## 執行 `move` 指令 {#move-command}

`php artisan livewire:move` 指令將移動/重新命名元件類別、blade 檢視和元件測試（如果存在），並處理命名空間和路徑

這是使用範例：

@component('components.code', ['lang' => 'shell'])
php artisan livewire:move foo bar.baz
# Foo.php|foo.blade.php|FooTest.php -> Bar/Baz.php|bar/baz.blade.php|Bar/BazTest.php
@endcomponent

@component('components.tip')
為了方便起見，<code>livewire:move</code> 別名為 <code>livewire:mv</code>
@endcomponent

## `copy` 指令 {#copy-command}

`php artisan livewire:copy` 指令將建立元件類別和 blade 檢視的複本，並處理命名空間和路徑

以下是一些使用範例：

@component('components.code', ['lang' => 'shell'])
php artisan livewire:copy foo bar
# 複製 Foo.php 和 foo.blade.php 到 Bar.php 和 bar.blade.php

php artisan livewire:copy foo bar --force
# 覆寫現有的 "bar" 元件

php artisan livewire:copy foo bar --test
# 複製 Foo.php 和 foo.blade.php 和 FooTest.php 到 Bar.php 和 bar.blade.php 和 BarTest.php
@endcomponent

@component('components.tip')
為了方便起見，<code>livewire:copy</code> 別名為 <code>livewire:cp</code>
@endcomponent

## `delete` 指令 {#delete-command}

`php artisan livewire:delete` 指令將移除元件類別和 blade 檢視。

以下是一些使用範例：

@component('components.code', ['lang' => 'shell'])
php artisan livewire:delete foo
# 移除 Foo.php 和 foo.blade.php

php artisan livewire:delete foo --force
# 不需確認提示即可移除

php artisan livewire:delete foo --test
# 移除 Foo.php 和 foo.blade.php 和 FooTest.php
@endcomponent

@component('components.tip')
為了方便起見，<code>livewire:delete</code> 別名為 <code>livewire:rm</code>
@endcomponent

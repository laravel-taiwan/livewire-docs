* [簡介](#introduction)
* [內嵌元件](#inline-components)

## 簡介 {#introduction}

執行以下 Artisan 指令以建立新的 Livewire 元件：

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire ShowPosts
@endcomponent

Livewire 也支援新元件的 "kebab" 表記法。

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire show-posts
@endcomponent

在您的專案中建立了兩個新檔案：

* `app/Http/Livewire/ShowPosts.php`
* `resources/views/livewire/show-posts.blade.php`

如果您希望在子資料夾中建立元件，您可以使用以下不同的語法：

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire Post\\Show
php artisan make:livewire Post/Show
php artisan make:livewire post.show
@endcomponent

現在，兩個建立的檔案將位於子資料夾中：

* `app/Http/Livewire/Post/Show.php`
* `resources/views/livewire/post/show.blade.php`

### 產生測試 {#generating-tests}

選擇性地，在建立元件時，您可以包含 `--test` 標誌，這樣也會為您建立一個測試檔案。

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire ShowPosts --test
@endcomponent

## 內嵌元件 {#inline-components}
如果您希望建立內嵌元件（沒有 `.blade.php` 檔案的元件），您可以在指令中加入 `--inline` 標誌：

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire ShowPosts --inline
@endcomponent

現在，只會建立一個檔案：

* `app/Http/Livewire/ShowPosts.php`

這是它的樣子：

@component('components.code', ['lang' => 'php'])
@verbatim
class ShowPosts extends Component
{
    public function render()
    {
        return <<<'blade'
            <div></div>
        blade;
    }
}
@endverbatim
@endcomponent

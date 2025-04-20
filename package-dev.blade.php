* [註冊自訂元件](#registering-components)

## 註冊自訂元件 {#registering-components}

您可以使用 `Livewire::component` 方法手動註冊元件。
如果您想要從一個 composer 套件提供 Livewire 元件，這將會很有用。
通常應該在服務提供者的 `boot` 方法中執行此操作。

@component('components.code', ['lang' => 'php'])
class YourPackageServiceProvider extends ServiceProvider {
    public function boot() {
        Livewire::component('some-component', SomeComponent::class);
    }
}
@endcomponent

現在，安裝了您的套件的應用程式可以在其視圖中像這樣使用您的元件：

@component('components.code', ['lang' => 'blade'])
@verbatim
<div>
    @livewire('some-component')
</div>
@endverbatim
@endcomponent

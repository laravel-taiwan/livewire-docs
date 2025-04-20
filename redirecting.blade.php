* [簡介](#introduction)

## 簡介 {#introduction}

您可能希望從 Livewire 元件內部重新導向到應用程式中的另一個頁面。Livewire 支援您在 Laravel 控制器中習慣使用的標準重新導向回應語法。

@component('components.code-component')
@slot('class')
@verbatim
class ContactForm extends Component
{
    public $email;

    public function addContact()
    {
        Contact::create(['email' => $this->email]);

        return redirect()->to('/contact-form-success');
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    電子郵件： <input wire:model="email">

    <button wire:click="addContact">提交</button>
</div>
@endverbatim
@endslot
@endcomponent

現在，當使用者點擊「提交」並將其聯絡資料新增到資料庫後，他們將被重新導向到成功頁面 (`/contact-form-success`)。

@component('components.tip')
由於 Livewire 與 Laravel 的重新導向系統配合工作，您可以使用任何您習慣的表示法，如 <code>redirect('/foo')</code>、<code>redirect()->to('/foo')</code>、<code>redirect()->route('foo')</code>。
@endcomponent

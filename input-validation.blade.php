* [簡介](#introduction)
* [即時確認](#real-time-validation)
* [使用`$rules`屬性之外的規則進行驗證](#validating-with-other-rules)
* [自訂錯誤訊息和屬性](#customize-error-message-and-attributes)
* [直接錯誤訊息操作](#error-bag-manipulation)
* [存取驗證器實例](#access-validator-instance)
* [測試驗證](#testing-validation)
* [自訂驗證器](#custom-validators)

## 簡介 {#introduction}

在 Livewire 中進行驗證應該感覺類似於 Laravel 中的標準表單驗證。簡而言之，Livewire 提供了一個`$rules`屬性，用於在每個元件上設置驗證規則，以及一個`$this->validate()`方法，用於使用這些規則驗證元件的屬性。

以下是在 Livewire 中驗證表單的簡單示例。

@component('components.code-component')
@slot('class')
@verbatim
class ContactForm extends Component
{
    public $name;
    public $email;

    protected $rules = [
        'name' => 'required|min:6',
        'email' => 'required|email',
    ];

    public function submit()
    {
        $this->validate();

        // 如果驗證失敗，執行不會到達這裡。

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="submit">
    <input type="text" wire:model="name">
    @error('name') <span class="error">{{ $message }}</span> @enderror

    <input type="text" wire:model="email">
    @error('email') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">儲存聯絡人</button>
</form>
@endverbatim
@endslot
@endcomponent

如果驗證失敗，將拋出標準的`ValidationException`（並被 Livewire 捕獲），並且標準的`$errors`物件將在元件的視圖中可用。因此，您在應用程式其他部分處理驗證的現有程式碼，可能是 Blade 包含，也將適用於這裡。

您還可以將自訂的鍵/訊息對新增到錯誤包中。
@component('components.code', ['lang' => 'php'])
@verbatim
    $this->addError('key', 'message')
@endverbatim
@endcomponent

如果您需要動態定義規則，您可以將組件中的 `$rules` 屬性替換為 `rules()` 方法：

@component('components.code', ['lang' => 'php'])
@verbatim
class ContactForm extends Component
{
    public $name;
    public $email;

    protected function rules()
    {
        return [
            'name' => 'required|min:6',
            'email' => ['required', 'email', 'not_in:' . auth()->user()->email],
        ];
    }
}
@endverbatim
@endcomponent

## 即時驗證 {#real-time-validation}

有時在用戶輸入時對表單字段進行驗證是很有用的。Livewire 通過 `$this->validateOnly()` 方法使“即時”驗證變得簡單。

要在每次更新後驗證輸入字段，我們可以使用 Livewire 的 `updated` 鉤子：

@component('components.code-component')
@slot('class')
@verbatim
class ContactForm extends Component
{
    public $name;
    public $email;

    protected $rules = [
        'name' => 'required|min:6',
        'email' => 'required|email',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveContact()
    {
        $validatedData = $this->validate();

        Contact::create($validatedData);
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="saveContact">
    <input type="text" wire:model="name">
    @error('name') <span class="error">{{ $message }}</span> @enderror

    <input type="text" wire:model="email">
    @error('email') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">Save Contact</button>
</form>
@endverbatim
@endslot
@endcomponent

讓我們逐步解釋此示例中發生的事情：

* 用戶在“name”字段中輸入
* 當用戶輸入名稱時，如果名稱少於 6 個字符，將顯示驗證消息
* 用戶可以切換到輸入電子郵件，並且名稱的驗證消息仍然顯示
* 當用戶提交表單時，將進行最終驗證檢查，並且數據將被保存。

如果你想知道，“為什麼我需要 `validateOnly`？不能只用 `validate` 嗎？”。原因是，否則，對任何字段的每次更新都會驗證所有字段。這可能會給用戶帶來不愉快的體驗。想像一下，如果你在表單的第一個字段中輸入一個字符，突然間每個字段都有一個驗證消息。`validateOnly` 可以防止這種情況，只驗證當前正在更新的字段。

## 使用 `$rules` 屬性之外的規則進行驗證 {#validating-with-other-rules}
如果出於任何原因，您想使用 `$rules` 屬性中未定義的規則進行驗證，您可以通過直接將規則傳遞給 `validate()` 和 `validateOnly()` 方法來實現。

@component('components.code-component')
@slot('class')
@verbatim
class ContactForm extends Component
{
    public $name;
    public $email;

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'name' => 'min:6',
            'email' => 'email',
        ]);
    }

    public function saveContact()
    {
        $validatedData = $this->validate([
            'name' => 'required|min:6',
            'email' => 'required|email',
        ]);

        Contact::create($validatedData);
    }
}
@endverbatim
@endslot
@endcomponent

## 自定義錯誤消息和屬性 {#customize-error-message-and-attributes}

如果您希望自定義 Livewire 組件使用的驗證消息，您可以使用 `$messages` 屬性來實現。

如果您想保留默認的 Laravel 驗證消息，但只想自定義消息中的 `:attribute` 部分，您可以使用 `$validationAttributes` 屬性來指定自定義屬性名稱。

@component('components.code-component')
@slot('class')
@verbatim
class ContactForm extends Component
{
    public $email;

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'The Email Address cannot be empty.',
        'email.email' => 'The Email Address format is not valid.',
    ];

```php
    protected $validationAttributes = [
        'email' => '電子郵件地址'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveContact()
    {
        $validatedData = $this->validate();

        Contact::create($validatedData);
    }
}
@endverbatim
@endslot
@endcomponent

您可以將 `$messages` 屬性替換為元件上的 `messages()` 方法。

如果您沒有使用全域 `$rules` 驗證屬性，則可以直接將自訂訊息和屬性傳遞給 `validate()`。

@component('components.code-component')
@slot('class')
@verbatim
class ContactForm extends Component
{
    public $email;

    public function saveContact()
    {
        $validatedData = $this->validate(
            ['email' => 'required|email'],
            [
                'email.required' => '必須填寫 :attribute。',
                'email.email' => ':attribute 格式無效。',
            ],
            ['email' => '電子郵件地址']
        );

        Contact::create($validatedData);
    }
}
@endverbatim
@endslot
@endcomponent


## 直接錯誤訊息操作 {#error-bag-manipulation}

`validate()` 和 `validateOnly()` 方法應該處理大多數情況，但有時您可能希望直接控制 Livewire 的內部 ErrorBag。

Livewire 提供了一些方法，讓您可以直接操作 ErrorBag。

在 Livewire 元件類別的任何地方，您可以調用以下方法：

@component('components.code', ['lang' => 'php'])
@verbatim
// 快速將驗證訊息添加到錯誤訊息包。
$this->addError('email', '電子郵件欄位無效。');

// 這兩個方法做相同的事情，它們清除錯誤訊息包。
$this->resetErrorBag();
$this->resetValidation();

// 如果您只想清除一個鍵的錯誤，您可以使用：
$this->resetValidation('email');
$this->resetErrorBag('email');

// 這將讓您完全訪問錯誤訊息包。
$errors = $this->getErrorBag();
// 有了這個錯誤訊息包實例，您可以做像這樣的事情：
$errors->add('some-key', '一些訊息');
@endverbatim
@endcomponent```

## 存取驗證器實例 {#access-validator-instance}

有時您可能希望存取 Livewire 在 `validate()` 和 `validateOnly()` 方法中使用的驗證器實例。這可以使用 `withValidator` 方法來實現。您提供的閉包將接收完全構建的驗證器作為參數，允許您在實際評估驗證規則之前調用其任何方法。

@component('components.code-component')
@slot('class')
@verbatim
use Illuminate\Validation\Validator;

class ContactForm extends Component
{
    public function save()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                if ($this->somethingElseIsInvalid()) {
                    $validator->errors()->add('field', '這個欄位有問題！');
                }
            });
        })->validate();
    }
}
@endverbatim
@endslot
@endcomponent

## 驗證測試 {#testing-validation}

Livewire 提供了用於驗證方案的有用測試工具。讓我們為原始的 "聯絡表單" 元件撰寫一個簡單的測試。

@component('components.code', ['lang' => 'php'])
/** @test */
public function name_and_email_fields_are_required_for_saving_a_contact()
{
    Livewire::test('contact-form')
        ->set('name', '')
        ->set('email', '')
        ->assertHasErrors(['name', 'email']);
}
@endcomponent

這很有用，但我們可以更進一步，實際針對特定的驗證規則進行測試：

@component('components.code', ['lang' => 'php'])
/** @test */
public function name_and_email_fields_are_required_for_saving_a_contact()
{
    Livewire::test('contact-form')
        ->set('name', '')
        ->set('email', '')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'required',
        ]);
}
@endcomponent

Livewire 還提供了 `assertHasErrors` 的相反操作 -> `assertHasNoErrors()`：

@component('components.code', ['lang' => 'php'])
/** @test */
public function name_field_is_required_for_saving_a_contact()
{
    Livewire::test('contact-form')
        ->set('name', '')
        ->set('email', 'foo')
        ->assertHasErrors(['name' => 'required'])
        ->assertHasNoErrors(['email' => 'required']);
}
@endcomponent

有關這兩種方法支援的語法示例，請參閱[測試文件](testing)。

## 自訂驗證器 {#custom-validators}

如果您希望在 Livewire 中使用自己的驗證系統，這並不是問題。Livewire 將捕獲 `ValidationException` 並將錯誤提供給視圖，就像使用 `$this->validate()` 一樣。

例如：
@component('components.code-component')
@slot('class')
@verbatim
use Illuminate\Support\Facades\Validator;

class ContactForm extends Component
{
    public $email;

    public function saveContact()
    {
        $validatedData = Validator::make(
            ['email' => $this->email],
            ['email' => 'required|email'],
            ['required' => 'The :attribute field is required'],
        )->validate();

        Contact::create($validatedData);
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    Email: <input wire:model.lazy="email">

    @if($errors->has('email'))
        <span>{{ $errors->first('email') }}</span>
    @endif

    <button wire:click="saveContact">Save Contact</button>
</div>
@endverbatim
@endslot
@endcomponent

@component('components.warning')
您可能會想知道是否可以使用 Laravel 的 "FormRequest"。由於 Livewire 的性質，連接到 HTTP 請求並沒有意義。目前，這個功能是不可能的，也不建議使用。
@endcomponent

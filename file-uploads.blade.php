* [基本檔案上傳](#basic-upload)
  * [儲存上傳的檔案](#storing-files)
* [處理多個檔案](#multiple-files)
* [檔案驗證](#file-validation)
  * [即時驗證](#real-time-validation)
* [臨時預覽網址](#preview-urls)
* [測試檔案上傳](#testing-uploads)
* [直接上傳至 Amazon S3](#upload-to-s3)
  * [設定自動檔案清理](#auto-cleanup)
* [載入指示器](#loading-indicators)
* [進度指示器（以及所有 JavaScript 事件）](#js-hooks)
* [JavaScript 上傳 API](#js-api)
* [組態設定](#configuration)
  * [全域驗證](#global-validation)
  * [全域中介層](#global-middleware)
  * [臨時上傳目錄](#temporary-upload-directory)

## 基本檔案上傳 {#basic-upload}

> 注意：您的 Livewire 版本必須 >= 1.2.0 才能使用此功能。

Livewire 讓上傳和儲存檔案變得非常容易。

首先，將 `WithFileUploads` 特性新增至您的元件。現在您可以在檔案輸入上使用 `wire:model`，就像它們是任何其他輸入類型一樣，Livewire 會為您處理其餘事項。

以下是一個處理上傳照片的簡單元件範例：

@component('components.code-component')
@slot('class')
@verbatim
use Livewire\WithFileUploads;

class UploadPhoto extends Component
{
    use WithFileUploads;

    public $photo;

    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB 最大
        ]);

        $this->photo->store('photos');
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="save">
    <input type="file" wire:model="photo">

    @error('photo') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">儲存照片</button>
</form>
@endverbatim
@endslot
@endcomponent

從開發者的角度來看，處理檔案輸入與處理任何其他輸入類型沒有任何不同：將 `wire:model` 加入到 `<input>` 標籤中，其他所有事情都會為您處理。

然而，在幕後有更多事情發生，以使檔案上傳在 Livewire 中運作。當使用者選擇要上傳的檔案時，以下是一個簡要概述：

1. 當選擇新檔案時，Livewire 的 JavaScript 會向伺服器的元件發出初始請求，以獲取臨時的「簽署」上傳 URL。
2. 一旦收到 URL，JavaScript 接著將實際的「上傳」操作執行到簽署的 URL，將上傳存儲在由 Livewire 指定的臨時目錄中，並返回新臨時檔案的唯一哈希 ID。
3. 當檔案上傳完成並生成唯一的哈希 ID 時，Livewire 的 JavaScript 會向伺服器的元件發出最終請求，告訴它將所需的公共屬性「設置」為新的臨時檔案。
4. 現在公共屬性（在這種情況下為 `$photo`）已設置為臨時檔案上傳，並隨時準備存儲或驗證。

### 儲存上傳的檔案 {#storing-files}

前面的範例展示了最基本的儲存情境：將臨時上傳的檔案移動到應用程式預設檔案系統磁碟上的「photos」目錄中。

然而，您可能希望自訂儲存檔案的檔名，甚至指定特定的儲存「磁碟」以將檔案存儲在其中（例如，可能是在 S3 存儲桶中）。

Livewire 遵守 Laravel 用於儲存上傳檔案的相同 API，因此請隨時查閱 [Laravel 的文件](https://laravel.com/docs/filesystem#file-uploads)。不過，這裡有一些常見的儲存情境供您參考：

@component('components.code', ['lang' => 'php'])
// 將上傳的檔案儲存在預設檔案系統磁碟上的「photos」目錄中。
$this->photo->store('photos');

// 在配置的「s3」存儲桶中的「photos」目錄中儲存。
$this->photo->store('photos', 's3');

// 在「photos」目錄中以檔名「avatar.png」儲存。
$this->photo->storeAs('photos', 'avatar');

// 在配置的「s3」存儲桶中的「photos」目錄中以檔名「avatar.png」儲存。
$this->photo->storeAs('photos', 'avatar', 's3');

// 在「photos」目錄中以「public」可見性在配置的「s3」存儲桶中儲存。
$this->photo->storePublicly('photos', 's3');

// 在配置的「s3」存儲桶中以「public」可見性將檔案儲存在「photos」目錄中，並命名為「avatar.png」。
$this->photo->storePubliclyAs('photos', 'avatar', 's3');
@endcomponent

上述方法應該提供足夠的靈活性，以便按照您的需求準確存儲上傳的文件。

## 處理多個文件 {#multiple-files}
Livewire 通過檢測 `<input>` 標籤上的 `multiple` 屬性，自動處理多個文件上傳。

以下是處理多個上傳的文件示例：

@component('components.code-component')
@slot('class')
@verbatim
use Livewire\WithFileUploads;

class UploadPhotos extends Component
{
    use WithFileUploads;

    public $photos = [];

    public function save()
    {
        $this->validate([
            'photos.*' => 'image|max:1024', // 1MB 最大
        ]);

        foreach ($this->photos as $photo) {
            $photo->store('photos');
        }
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="save">
    <input type="file" wire:model="photos" multiple>

    @error('photos.*') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">保存照片</button>
</form>
@endverbatim
@endslot
@endcomponent

## 文件驗證 {#file-validation}
就像您在之前的示例中看到的那樣，使用 Livewire 驗證文件上傳與從標準 Laravel 控制器處理文件上傳完全相同。

> 注意：許多與文件相關的 Laravel 驗證規則需要訪問文件。如果您正在[直接上傳到 S3](#upload-to-s3)，這些驗證規則將在對象不是公開訪問時失敗。

有關 Laravel 文件驗證工具的更多信息，[請參閱文檔](https://laravel.com/docs/validation#available-validation-rules)。

### 實時驗證 {#real-time-validation}
在用戶按下“提交”之前，實時驗證用戶的上傳是可能的。

同樣，您可以像在 Livewire 中處理任何其他輸入類型一樣完成這個操作：

@component('components.code-component')
@slot('class')
@verbatim
use Livewire\WithFileUploads;

class UploadPhoto extends Component
{
    use WithFileUploads;

    public $photo;

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB 最大
        ]);
    }

```php
    public function save()
    {
        // ...
    }
}
```

現在，當用戶選擇文件（在 Livewire 上傳文件到臨時目錄後），文件將被驗證，用戶將在提交表單之前收到錯誤。

## 臨時預覽網址 {#preview-urls}
用戶選擇文件後，您可能希望在用戶提交表單並實際存儲文件之前向他們顯示該文件的預覽。

Livewire 通過上傳文件的 `->temporaryUrl()` 方法輕鬆實現此功能。

> 注意：出於安全原因，僅支持圖像上傳的臨時 URL。

以下是帶有圖像預覽的文件上傳示例：

```php
use Livewire\WithFileUploads;

class UploadPhotoWithPreview extends Component
{
    use WithFileUploads;

    public $photo;

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024',
        ]);
    }

    public function save()
    {
        // ...
    }
}
```

Livewire 將臨時文件存儲在非公共目錄中，因此，沒有簡單的方法可以向用戶公開臨時的公共 URL 以供圖像預覽。

Livewire 處理了這種複雜性，通過提供一個臨時的簽名 URL，假裝是上傳的圖像，以便您的頁面可以向用戶顯示某些內容。

當然，這個 URL 受保護，以防止顯示臨時目錄上方目錄中的文件，並且因為它是臨時簽名的，用戶無法濫用此 URL 來預覽系統中的其他文件。```

@component('components.tip')
    如果您已配置 Livewire 使用 S3 進行臨時文件存儲，調用 <code>->temporaryUrl()</code> 將直接從 S3 生成臨時簽名 URL，這樣您就完全不需要在 Laravel 應用伺服器上進行此預覽。
@endcomponent

## 測試文件上傳 {#testing-uploads}
在 Livewire 中測試文件上傳非常簡單，使用 Laravel 的文件上傳測試輔助工具。

以下是使用 Livewire 測試 "UploadPhoto" 元件的完整示例。

@component('components.code-component', [
    'className' => 'UploadPhotoTest.php',
])
@slot('class')
@verbatim
/** @test */
public function can_upload_photo()
{
    Storage::fake('avatars');

    $file = UploadedFile::fake()->image('avatar.png');

    Livewire::test(UploadPhoto::class)
        ->set('photo', $file)
        ->call('upload', 'uploaded-avatar.png');

    Storage::disk('avatars')->assertExists('uploaded-avatar.png');
}
@endverbatim
@endslot
@endcomponent

以下是使前面的測試通過所需的 "UploadPhoto" 元件片段：

@component('components.code-component', [
    'className' => 'UploadPhoto.php',
])
@slot('class')
@verbatim
class UploadPhoto extends Component
{
    use WithFileUploads;

    public $photo;

    // ...

    public function upload($name)
    {
        $this->photo->storeAs('/', $name, $disk = 'avatars');
    }
}
@endverbatim
@endslot
@endcomponent

有關測試文件上傳的更多具體信息，請參考 [Laravel 的文件上傳測試文檔](https://laravel.com/docs/http-tests#testing-file-uploads)。

## 直接上傳到 Amazon S3 {#upload-to-s3}
如前所述，Livewire 將所有文件上傳存儲在臨時目錄中，直到開發人員選擇永久存儲文件。

默認情況下，Livewire 使用默認的文件系統磁碟配置（通常是 `local`），並將文件存儲在名為 `livewire-tmp/` 的文件夾中。

這意味著文件上傳始終會命中您的伺服器；即使您稍後選擇將它們存儲在 S3 存儲桶中。

如果您希望繞過此系統，而是將 Livewire 的臨時上傳存儲在 S3 存儲桶中，您可以輕鬆配置該行為：

在您的 `config/livewire.php` 檔案中，將 `livewire.temporary_file_upload.disk` 設置為 `s3`（或使用 `s3` 驅動程序的其他自定義磁碟）：

@component('components.code-component')
@slot('class')
return [
    ...
    'temporary_file_upload' => [
        'disk' => 's3',
        ...
    ],
];
@endslot
@endcomponent

現在，當用戶上傳文件時，文件實際上不會傳遞到您的伺服器。它將直接上傳到您的 S3 存儲桶，位於子目錄：`livewire-tmp/`。

### 配置自動文件清理 {#auto-cleanup}
這個臨時目錄將很快填滿文件，因此，重要的是配置 S3 以清理 24 小時前的文件。

要配置此行為，只需從已配置 S3 存儲桶的環境運行以下 artisan 命令。

@component('components.code', ['lang' => 'shell'])
php artisan livewire:configure-s3-upload-cleanup
@endcomponent

現在，任何超過 24 小時的臨時文件將由 S3 自動清理。

@component('components.tip')
如果您未使用 S3，Livewire 將自動處理文件清理。無需運行此命令。
@endcomponent

## 載入指示器 {#loading-indicators}
雖然文件上傳的 `wire:model` 在底層的工作方式與其他 `wire:model` 輸入類型不同，但顯示載入指示器的界面保持不變。

您可以這樣顯示與文件上傳相關的載入指示器：

@component('components.code', ['lang' => 'blade'])
<input type="file" wire:model="photo">

<div wire:loading wire:target="photo">正在上傳...</div>
@endcomponent

現在，在文件上傳時將顯示 "正在上傳..." 消息，並在上傳完成時隱藏。

這與整個 Livewire [載入狀態 API](loading-states) 一起使用。

## 進度指示器（以及所有 JavaScript 事件） {#js-hooks}
Livewire 中的每個文件上傳都會在 `<input>` 元素上發送 JavaScript 事件，以供自定義 JavaScript 監聽。

以下是發送的事件：

事件 | 說明
--- | ---
`livewire-upload-start` | 上傳開始時發送
`livewire-upload-finish` | 如果上傳成功完成則發送
`livewire-upload-error` | 如果上傳失敗則發送
`livewire-upload-progress` | 隨著上傳進度發送包含上傳進度百分比的事件

這是一個示例，將 Livewire 檔案上傳包裝在 AlpineJS 元件中，以顯示進度條：

@component('components.code', ['lang' => 'blade'])
<div
    x-data="{ isUploading: false, progress: 0 }"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    <!-- 檔案輸入 -->
    <input type="file" wire:model="photo">

    <!-- 進度條 -->
    <div x-show="isUploading">
        <progress max="100" x-bind:value="progress"></progress>
    </div>
</div>
@endcomponent

## JavaScript 上傳 API {#js-api}
與第三方檔案上傳庫整合通常需要比簡單的 `<input type="file">` 標籤更精細的控制。

對於這些情況，Livewire 提供了專用的 JavaScript 函數。

@verbatim
這些函數存在於 JavaScript 元件物件上，可以使用方便的 Blade 指示詞 `@this` 來訪問。如果您之前沒有看過 `@this`，您可以在[這裡](inline-scripts)閱讀更多相關資訊。
@endverbatim

@component('components.code', ['lang' => 'blade'])
@verbatim
<script>
    let file = document.querySelector('input[type="file"]').files[0]

    // 上傳檔案：
    @this.upload('photo', file, (uploadedFilename) => {
        // 成功回呼函式。
    }, () => {
        // 錯誤回呼函式。
    }, (event) => {
        // 進度回呼函式。
        // event.detail.progress 包含一個介於 1 到 100 之間的數字，表示上傳進度。
    })

    // 上傳多個檔案：
    @this.uploadMultiple('photos', [file], successCallback, errorCallback, progressCallback)

    // 從多個已上傳的檔案中移除單一檔案
    @this.removeUpload('photos', uploadedFilename, successCallback)
</script>
@endverbatim
@endcomponent

## 組態設定 {#configuration}
因為 Livewire 在開發者有機會驗證或儲存檔案之前暫時存儲所有檔案上傳，Livewire 假設對所有檔案上傳進行了一些默認處理。

### 全域確認 {#global-validation}
預設情況下，Livewire 將使用以下規則對所有臨時檔案上傳進行確認：`file|max:12288`（檔案大小必須小於 12MB）。

如果您希望自訂此設定，您可以在 `config/livewire.php` 內確切配置所有臨時檔案上傳應運行的確認規則：

@component('components.code-component')
@slot('class')
return [
    ...
    'temporary_file_upload' => [
        ...
        'rules' => 'file|mimes:png,jpg,pdf|max:102400', //（最大 100MB，僅限 png、jpg 和 pdf 檔案）
        ...
    ],
];
@endslot
@endcomponent

### 全域中介層 {#global-middleware}
臨時檔案上傳端點預設具有節流中介層。您可以使用以下配置變數自訂此端點使用的中介層：

@component('components.code-component')
@slot('class')
return [
    ...
    'temporary_file_upload' => [
        ...
        'middleware' => 'throttle:5,1', // 每位使用者每分鐘僅允許上傳 5 次。
    ],
];
@endslot
@endcomponent

### 臨時上傳目錄 {#temporary-upload-directory}
臨時檔案上傳至指定磁碟上的 `livewire-tmp/` 目錄。您可以使用以下配置鍵自訂此設定：

@component('components.code-component')
@slot('class')
return [
    ...
    'temporary_file_upload' => [
        ...
        'directory' => 'tmp',
    ],
];
@endslot
@endcomponent

### 最大上傳時間
如果檔案上傳時間超過 5 分鐘，將自動使其失效。您可以使用以下配置鍵自訂此設定：

@component('components.code-component')
@slot('class')
return [
    ...
    'temporary_file_upload' => [
        ...
        'max_upload_time' => 5,
    ],
];
@endslot
@endcomponent

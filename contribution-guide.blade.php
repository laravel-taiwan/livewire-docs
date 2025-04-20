* [在本地設置 Livewire](#setup-livewire-locally)
    * [分叉 Livewire](#fork-livewire)
    * [在本地克隆您的分叉](#clone-fork)
    * [安裝相依性](#install-dependencies)
    * [配置 dusk](#configure-dusk)
    * [運行測試](#setup-run-tests)
* [錯誤修復/功能開發](#bug-fix-feature-development)
    * [建立分支](#create-a-branch)
    * [新增失敗測試](#add-failing-tests)
    * [新增正確代碼](#add-working-code)
    * [運行測試](#development-run-tests)
    * [提交 PR](#submit-pr)
    * [感謝您的貢獻！ 🙌](#thanks)

在 Livewire，我們感激並歡迎所有貢獻！

如果您對此感興趣，我們建議您在開始之前先閱讀這份貢獻指南。

## 在本地設置 Livewire {#setup-livewire-locally}

第一步是創建 Livewire 的分叉並在本地設置。您應該只需要在第一次這樣做。

### 分叉 Livewire {#fork-livewire}

前往 [GitHub 上的 Livewire 存儲庫](https://github.com/livewire/livewire) 並分叉 Livewire 存儲庫。

![在 GitHub 上分叉 Livewire](/img/docs/github-fork.png) {.border}

### 在本地克隆您的分叉 {#clone-fork}

在 GitHub 上找到您的分叉，點擊 "code" 按鈕，並複製提供的 URL。

![在 GitHub 上克隆 Livewire](/img/docs/github-clone.png) {.border}

然後在本地終端運行 `git clone`，並傳遞您的 URL 和您想要將 Livewire 克隆到的目錄名稱。

@component('components.code', ['lang' => 'shell'])
git clone git@github.com:username/livewire.git ~/packages/livewire
@endcomponent

完成後，`cd` 進入您的本地 Livewire 目錄。

@component('components.code', ['lang' => 'shell'])
cd ~/packages/livewire
@endcomponent

### 安裝相依性 {#install-dependencies}

運行以下命令安裝 composer 相依性：

@component('components.code', ['lang' => 'shell'])
composer install
@endcomponent

運行以下命令安裝 npm 相依性：

@component('components.code', ['lang' => 'shell'])
npm install
@endcomponent

### 配置 dusk {#configure-dusk}

許多 Livewire 的測試使用 `orchestral/testbench-dusk`，該工具在 Google Chrome 中運行瀏覽器測試（因此您需要安裝 Chrome）。

要讓 `orchestral/testbench-dusk` 運行，您需要執行以下命令來安裝最新的 Chrome 驅動程式：

@component('components.code', ['lang' => 'shell'])
./vendor/bin/dusk-updater detect --auto-update
@endcomponent

### 執行測試 {#setup-run-tests}

一旦一切都配置好了，運行所有測試以確保一切運作正常並通過。

要做到這一點，運行 `phpunit` 並確認一切運行正常。

@component('components.code', ['lang' => 'shell'])
phpunit
@endcomponent

如果 dusk 測試未運行並出現錯誤，請確保您已執行上面 [配置 dusk](#configure-dusk) 部分中的命令。

如果您仍然遇到錯誤，第一次嘗試運行 dusk 測試時，您可能還需要關閉您可能已經打開的任何 Google Chrome 實例，然後再次嘗試運行測試。之後，您應該能夠在運行測試時保持 Chrome 開啟。


## Bug 修復/功能開發 {#bug-fix-feature-development}

現在是時候開始處理您的 bug 修復或新功能了。

### 創建分支 {#create-a-branch}

要開始開發新功能或修復 bug，您應該始終在您的分支中使用您的功能或修復的名稱創建一個新分支。

@component('components.tip')
始終為您的功能或修復創建一個新分支。
@endcomponent

不要使用您的分支的主分支，因為維護者無法修改從分支的主分支提交的 PR。

@component('components.warning')
從主分支提交的任何 PR 將被關閉。
@endcomponent

### 添加失敗測試 {#add-failing-tests}

下一步是為您的代碼添加失敗測試。

Livewire 同時具有 Dusk 瀏覽器測試和標準 PHPUnit 單元測試，您可以在 `tests/Browser` 和 `tests/Unit` 中找到它們。

Livewire 同時運行 PHP 和 Javascript 代碼，因此建議使用 Dusk 瀏覽器測試來確保一切按預期運作，並根據需要支持單元測試。

以下是 Livewire Dusk 測試應該結構化的範例：

@component('components.code', ['lang' => 'php'])
/** @test */
public function it_can_run_foo_action
{
    $this->browse(function ($browser) {
        Livewire::visit($browser, FooComponent::class)
            /**
             * 基本動作（點擊）。
             */
            ->waitForLivewire()->click('@foo')
            ->assertSeeIn('@output', 'foo')
            ;
    });
}
@endcomponent

您可以在 [Laravel 文件](https://laravel.com/docs/8.x/dusk) 中查看如何使用 Dusk，並查看 Livewire 現有的瀏覽器測試以獲得更多範例。

### 添加可運行的程式碼 {#add-working-code}

Livewire 同時具有 PHP 和 JavaScript 代碼，您可以在 `src` 目錄中找到 PHP 代碼，並在 `js` 目錄中找到 JavaScript 代碼。

根據需要更改程式碼以修復錯誤或添加新功能，但請儘量保持更改最小化。如有需要，考慮拆分為多個 PR。

@component('components.warning')
進行過多更改或無關更改的 PR 可能會被關閉。
@endcomponent

如果您已更新 Livewire 的 JavaScript 代碼，則需要重新編譯資源檔。
要執行此操作，運行 `npm run build`，或者您可以使用 `npm run watch` 開始一個監視器。

編譯後的 JavaScript 資源應與您的更改一起提交。

@component('components.tip')
如果您更新了任何 JavaScript，請確保重新編譯資源檔並提交它們。
@endcomponent

完成編寫程式碼後，請進行審查，以確保您沒有留下任何調試程式碼，並且格式與現有樣式相符。

### 執行測試 {#development-run-tests}

在提交之前的最後一步是運行所有測試，以確保您的更改沒有影響其他任何內容。

要執行此操作，運行 `phpunit` 並確認一切運行正常。

@component('components.code', ['lang' => 'shell'])
phpunit
@endcomponent

如果 Dusk 瀏覽器測試無法運行，請參見上面設置部分的 [執行測試](#setup-run-tests) 以獲取更多詳細信息。

### 提交 PR {#submit-pr}

一旦所有測試通過，請將您的分支推送到 GitHub 並提交您的 PR。

在您的 PR 描述中，請確保提供您的 PR 做了什麼的小例子，以及改進的詳細描述和其有用之處的原因。
請添加任何相關的問題或討論的連結，以便進一步了解詳情。

@component('components.tip')
對於第一次貢獻者，測試不會自動運行，因此需要由維護者啟動。
@endcomponent

### 感謝您的貢獻！ 🙌 {#thanks}

就是這樣了！

維護者將審查您的 PR，並根據需要提供反饋。

感謝您對 Livewire 的貢獻！

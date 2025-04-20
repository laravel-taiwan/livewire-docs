* [簡介](#introduction)
* [測試檔案下載](#testing-file-downloads)

## 簡介 {#introduction}

Livewire支援使用簡單直觀的API為使用者觸發檔案下載。

要觸發檔案下載，您可以從任何元件動作返回Laravel檔案下載。

@component('components.code-component')
@slot('class')
@verbatim
class ExportButton extends Component
{
    public function export()
    {
        return Storage::disk('exports')->download('export.csv');
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<button wire:click="export">
    下載檔案
</button>
@endverbatim
@endslot
@endcomponent

Livewire應該處理Laravel會處理的任何檔案下載。這裡有一些您可能會使用的其他工具:

@component('components.code', ['lang' => 'php'])
@verbatim
return response()->download(storage_path('exports/export.csv'));
@endverbatim
@endcomponent

@component('components.code', ['lang' => 'php'])
@verbatim
return response()->streamDownload(function () {
    echo 'CSV 內容...';
}, 'export.csv');
@endverbatim
@endcomponent

## 測試檔案下載 {#testing-file-downloads}
使用livewire進行檔案下載的測試很簡單。

以下是測試上面元件並確保已下載匯出的範例。

@component('components.code-component', [
    'className' => 'ExportDownloadedTest.php',
])
@slot('class')
@verbatim
/** @test */
public function can_download_export()
{
    Livewire::test(ExportButton::class)
        ->call('download')
        ->assertFileDownloaded('export')
    ;

}
@endverbatim
@endslot
@endcomponent

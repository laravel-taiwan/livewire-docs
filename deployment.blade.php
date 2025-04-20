* [Livewire 變更](#livewire-changes)
* [過期頁面對話框和鉤子](#page-expired-dialog-and-hook)
    * [過期頁面對話框](#page-expired-dialog)
    * [過期頁面鉤子](#page-expired-hook)

## Livewire 變更 {#livewire-changes}

偶爾會有 Livewire 內部方法簽名的變更，這將需要刷新目前在瀏覽器中運行的任何組件（我們盡量將這些變更保持最少）。

為了實現這一點，Livewire 使用內部部署雜湊並跟踪它是否已更改。

如果 Livewire 的部署雜湊已更改，它將觸發[過期頁面對話框或鉤子](#page-expired-dialog-and-hook)。

## 過期頁面對話框和鉤子 {#page-expired-dialog-and-hook}

### 過期頁面對話框 {#page-expired-dialog}

默認情況下，如果部署雜湊不匹配（請參見上文）或用戶會話已過期，那麼 Livewire 將顯示一個確認對話框，提示用戶刷新頁面。

![過期頁面對話框](/img/docs/page-expired-dialog.png) {.border.w-full}

### 過期頁面鉤子 {#page-expired-hook}

如果默認的過期頁面對話框不適用，您可以通過使用過期頁面鉤子來實現通知用戶的自定義解決方案。

為此，您將傳遞一個處理通知用戶的 javascript 回調函式給 `Livewire.onPageExpired()`。

@component('components.code', ['lang' => 'js'])
Livewire.onPageExpired((response, message) => {})
@endcomponent

@component('components.tip')
您可以從過期頁面回調中發送一個瀏覽器事件，Alpine 可以監聽以顯示自定義對話框模態，提示用戶刷新頁面。
@endcomponent

您需要將 `Livewire.onPageExpired()` 調用放在您的佈局文件中 Livewire 腳本之後
@component('components.code', ['lang' => 'blade'])
@verbatim
<livewire:scripts />
<script>
    Livewire.onPageExpired((response, message) => {})
</script>
@endverbatim
@endcomponent

或者將其包裹在等待 Livewire 載入的事件監聽器中

@component('components.code', ['lang' => 'blade'])
<script>
    document.addEventListener('livewire:load', () => {
        Livewire.onPageExpired((response, message) => {})
    })
</script>
@endcomponent

Please paste the Markdown content you need to be translated into traditional Chinese.

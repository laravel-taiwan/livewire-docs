* [需求](#requirements)
* [安裝套件](#install-package)
* [包含資源檔](#include-js)
* [發佈組態檔](#publishing-config)
* [發佈前端資源](#publish-assets)
* [設定資源基礎 URL](#configuring-the-asset-base-url)

## 需求 {#requirements}

1. PHP 7.2.5 或更高版本
2. Laravel 7.0 或更高版本

請查看 [Github 上的 composer.json 檔案](https://github.com/livewire/livewire/blob/master/composer.json) 以獲取完整的套件需求清單。

## 安裝套件 {#install-package}

@component('components.code', ['lang' => 'shell'])
composer require livewire/livewire
@endcomponent

## 包含資源檔 {#include-js}
在您的模板中，在 `head` 標籤中和結束 `body` 標籤之前添加以下 Blade 指示詞。

@component('components.code')
@verbatim
<html>
<head>
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

您也可以使用標籤語法。

@component('components.code')
@verbatim
<livewire:styles />
...
<livewire:scripts />
@endverbatim
@endcomponent

就是這樣！這就是您開始使用 Livewire 所需的全部。本頁面上的其他內容都是可選的。

## 發佈組態檔 {#publishing-config}

Livewire 旨在實現“開箱即用”的“零配置”，但有些用戶需要更多的配置選項。

您可以使用以下 artisan 命令發佈 Livewire 的組態檔：

@component('components.code', ['lang' => 'shell'])
@verbatim
php artisan livewire:publish --config
@endverbatim
@endcomponent

## 發佈前端資源 {#publish-assets}

如果您希望將 JavaScript 資源通過您的 Web 伺服器提供而不是通過 Laravel，請使用 `livewire:publish` 命令：

@component('components.code', ['lang' => 'shell'])
@verbatim
php artisan livewire:publish --assets
@endverbatim
@endcomponent

為了使資源保持最新並避免未來更新中的問題，**強烈建議**將該命令添加到您的 `composer.json` 檔案中的 `post-autoload-dump` 腳本中：

```json
{
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi",
            "@php artisan vendor:publish --force --tag=livewire:assets --ansi"
        ]
    }
}
```

## 配置資源基礎 URL {#configuring-the-asset-base-url}

預設情況下，Livewire 從您的應用程式中的以下路由提供其 JavaScript 部分（`livewire.js`）：`/livewire/livewire.js`。

實際生成的腳本標籤預設為：<br> `<script src="/livewire/livewire.js"></script>`

有兩種情況會導致此預設行為失效：

1. 您發佈了 Livewire 資源，現在從子文件夾（如 "assets"）提供它們。

2. 您的應用程式託管在您域名上的非根路徑上。例如：`https://your-laravel-app.com/application`。在這種情況下，實際資源將從 `/application/livewire/livewire.js` 提供，但生成的腳本標籤將嘗試擷取 `/livewire/livewire.js`。

要解決這兩個問題中的任何一個，您可以在 `config/livewire.php` 中配置 "asset_url"，以自定義要添加到 `src=""` 屬性的內容。

例如，在發佈 Livewire 的組態檔後，以下是可以解決上述兩個問題的設置：

1. `'asset_url' => '/assets'`
2. `'asset_url' => '/application'`
```

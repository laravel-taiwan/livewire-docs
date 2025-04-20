* [簡介](#introduction)
* [在迴圈中追蹤元件](#keyed-components)
    * [迴圈中的同層元件](#sibling-components-in-a-loop)

## 簡介 {#introduction}

Livewire支援巢狀元件。元件巢狀化可以是一種非常強大的技術，但有一些值得一提的地方：

1. 巢狀元件可以從其父元件接受資料參數，但它們不像Vue元件中的props那樣具有反應性。
2. Livewire元件不應該用於將Blade片段提取到單獨的檔案中。對於這些情況，Blade包含或元件更為適合。

這是從另一個Livewire元件視圖中呼叫的一個巢狀元件`add-user-note`的範例。

@component('components.code-component')
@slot('class')
@verbatim
class UserDashboard extends Component
{
    public User $user;
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div>
    <h2>使用者詳細資訊：</h2>
    姓名：{{ $user->name }}
    電子郵件：{{ $user->email }}

    <h2>使用者備註：</h2>
    <div>
        @livewire('add-user-note', ['user' => $user])
    </div>
</div>
@endverbatim
@endslot
@endcomponent

## 在迴圈中追蹤元件 {#keyed-components}

與VueJs類似，如果您在迴圈中渲染元件，Livewire無法追蹤哪個是哪個。為了解決這個問題，Livewire提供了一個特殊的“key”語法：

@component('components.code-component')
@slot('view')
@verbatim
<div>
    @foreach ($users as $user)
        @livewire('user-profile', ['user' => $user], key($user->id))
    @endforeach
</div>
@endverbatim
@endslot
@endcomponent

如果您使用的是Laravel 7或更高版本，您可以使用標籤語法。

@component('components.code-component')
@slot('view')
@verbatim
<div>
    @foreach ($users as $user)
        <livewire:user-profile :user="$user" :wire:key="$user->id">
    @endforeach
</div>
@endverbatim
@endslot
@endcomponent

### 迴圈中的同層元件 {#sibling-components-in-a-loop}

在某些情況下，您可能需要在迴圈中擁有同層元件，這種情況需要對`wire:key`值進行額外考慮。

每個元件都需要有自己獨特的 `wire:key`，但使用上述方法將導致兩個同級元件具有相同的鍵，這將導致意想不到的問題。為了解決這個問題，您可以通過在元件名稱前加上前綴來確保每個 `wire:key` 是唯一的，例如：

@component('components.code', ['lang' => 'blade'])
@verbatim
<!-- user-profile 元件 -->
<div>
    // 不好
    <livewire:user-profile-one :user="$user" :wire:key="$user->id">
    <livewire:user-profile-two :user="$user" :wire:key="$user->id">

    // 好
    <livewire:user-profile-one :user="$user" :wire:key="'user-profile-one-'.$user->id">
    <livewire:user-profile-two :user="$user" :wire:key="'user-profile-two-'.$user->id">
</div>
@endverbatim
@endcomponent

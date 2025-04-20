Livewire 提供了一個權限，用於訪問動態屬性。這對於從數據庫或其他持久存儲（如快取）中派生屬性特別有幫助。

@component('components.code', ['lang' => 'php'])
@verbatim
class FooComponent extends Component
{
    // Computed Property
    public function getFooProperty()
    {
        return 'foo';
    }
@endverbatim
@endcomponent

現在，您可以從組件的類別或 Blade 視圖中訪問 `$this->foo`：

@component('components.code-component')
@slot('view')
@verbatim
<div>
    <span>{{ $this->foo }}</span>
</div>
@endverbatim
@endslot
@endcomponent

@component('components.tip')
計算屬性在個別 Livewire 請求生命週期中被快取。這意味著，如果您在組件的 Blade 視圖中調用 `$this->post` 5 次，它不會每次都進行單獨的數據庫查詢。
@endcomponent

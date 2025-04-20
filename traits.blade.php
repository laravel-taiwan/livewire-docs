* [簡介](#introduction)

## 簡介 {#introduction}

PHP Traits 是在多個 Livewire 元件之間重複使用功能的絕佳方式。

例如，您可能在應用程式中有多個 "資料表" 元件，它們都共享相同的排序邏輯。

與在每個元件中重複以下排序樣板不同：

@component('components.code-component')
@slot('class')
@verbatim
class ShowPosts extends Component
{
    public $sortBy = '';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortBy === $field
            ? $this->reverseSort()
            : 'asc';

        $this->sortBy = $field;
    }

    public function reverseSort()
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }

    ...
}
@endverbatim
@endslot
@endcomponent

您可以將此行為提取到一個可重複使用的 trait 中，名為 `WithSorting`：

@component('components.code-component')
@slot('class')
@verbatim
class ShowPosts extends Component
{
    use WithSorting;

    ...
}
@endverbatim
@endslot
@endcomponent

@component('components.code-component')
@slot('class')
@verbatim
trait WithSorting
{
    public $sortBy = '';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortBy === $field
            ? $this->reverseSort()
            : 'asc';

        $this->sortBy = $field;
    }

    public function reverseSort()
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }
}
@endverbatim
@endslot
@endcomponent

此外，如果您想在 trait 內部使用 Livewire 的生命週期鉤子，但仍然能夠在元件內部使用它們，Livewire 提供了一種語法，允許您這樣做：

@component('components.code-component')
@slot('class')
@verbatim
trait WithSorting
{
    ...

    public function bootWithSorting()
    {
        //
    }

    public function bootedWithSorting()
    {
        //
    }

```php
public function mountWithSorting()
{
    //
}

public function updatingWithSorting($name, $value)
{
    //
}

public function updatedWithSorting($name, $value)
{
    //
}

public function hydrateWithSorting()
{
    //
}

public function dehydrateWithSorting()
{
    //
}

public function renderingWithSorting()
{
    //
}

public function renderedWithSorting($view)
{
    //
}
}
```

Livewire 提供了用於查詢字串的鉤子。

```php
trait WithSorting
{
    ...

    protected $queryStringWithSorting = [
        'sortBy' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
    ];

    // or as a method

    public function queryStringWithSorting()
    {
        return [
            'sortBy' => ['except' => 'id'],
            'sortDirection' => ['except' => $this->defaultSortDirection()],
        ];
    }
}
```

請注意，您可以在組件類中覆蓋任何查詢字串。
```

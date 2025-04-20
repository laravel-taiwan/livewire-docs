* [簡介](#introduction)
    * [保持乾淨的查詢字串](#clean-query-string)
    * [查詢字串別名](#query-string-aliases)

## 簡介 {#introduction}

有時候在元件狀態改變時更新瀏覽器的查詢字串是很有用的。

例如，如果您正在建立一個"搜索文章"的元件，並希望查詢字串反映當前的搜索值，如下所示：

`https://your-app.com/search-posts?search=some-search-string`

這樣，當用戶點擊返回按鈕或將頁面加入書籤時，您可以從查詢字串中獲取初始狀態，而不是每次重置元件。

在這些情況下，您可以將屬性名稱添加到`protected $queryString`，Livewire將在屬性值更改時更新查詢字串，並在查詢字串更改時也更新屬性。

@component('components.code-component')
@slot('class')
class SearchPosts extends Component
{
    public $search;

    protected $queryString = ['search'];

    public function render()
    {
        return view('livewire.search-posts', [
            'posts' => Post::where('title', 'like', '%'.$this->search.'%')->get(),
        ]);
    }
}
@endslot
@slot('view')
@verbatim
<div>
    <input wire:model="search" type="search" placeholder="Search posts by title...">

    <h1>Search Results:</h1>

    <ul>
        @foreach($posts as $post)
            <li>{{ $post->title }}</li>
        @endforeach
    </ul>
</div>
@endverbatim
@endslot
@endcomponent

### 保持乾淨的查詢字串 {#clean-query-string}

在上面的情況下，當搜索屬性為空時，查詢字串將如下所示：

`?search=`

還有其他情況，您可能只想在查詢字串中表示一個值，如果它不是默認設置。

例如，如果您有一個`$page`屬性來跟踪元件中的分頁，您可能希望當用戶在第一頁時從查詢字串中刪除`page`屬性。

在這些情況下，您可以使用以下語法：

@component('components.code-component')
@slot('class')
class SearchPosts extends Component
{
    public $foo;
    public $search = '';
    public $page = 1;

```php
    protected $queryString = [
        'foo',
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    ...
}
@endslot
@endcomponent

### 查詢字串別名 {#query-string-aliases}

此外，如果您想修改屬性在 URL 中的表示方式，Livewire 提供了一個簡單的語法來為查詢字串設定別名。

例如，如果您想要縮短 URL，將 page 屬性表示為 `p`，search 表示為 `s`，您可以使用 `as` 修飾符來達到這個效果。

@component('components.code-component')
@slot('class')
class SearchPosts extends Component
{
    public $search = '';
    public $page = 1;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    ...
}
@endslot
@endcomponent

現在 URL 可以看起來像這樣：

`?s=Livewire%20is%20awesome&p=2`
```

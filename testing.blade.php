* [簡介](#introduction)
* [測試元件存在性](#testing-component-presence)
* [使用查詢字串參數進行測試](#testing-querystring)
* [使用傳遞資料進行測試元件](#testing-passed-data)
* [生成測試](#generating-tests)
* [所有可用的測試方法](#all-testing-methods)

## 簡介 {#introduction}

Livewire 提供了一套強大的工具來測試您的元件。

這裡有一個 Livewire 元件和相應的測試，以展示基本功能。

@component('components.code-component')
@slot('class')
@verbatim
class CreatePost extends Component
{
    public $title;

    protected $rules = [
        'title' => 'required',
    ];

    public function create()
    {
        auth()->user()->posts()->create(
            $this->validate()
        );

        return redirect()->to('/posts');
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="create">
    <input wire:model="title" type="text">

    <button>Create Post</button>
</form>
@endverbatim
@endslot
@endcomponent

@component('components.code', ['lang' => 'php'])
class CreatePostTest extends TestCase
{
    /** @test */
    function can_create_post()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreatePost::class)
            ->set('title', 'foo')
            ->call('create');

        $this->assertTrue(Post::whereTitle('foo')->exists());
    }

    /** @test */
    function can_set_initial_title()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreatePost::class, ['initialTitle' => 'foo'])
            ->assertSet('title', 'foo');
    }

    /** @test */
    function title_is_required()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreatePost::class)
            ->set('title', '')
            ->call('create')
            ->assertHasErrors(['title' => 'required']);
    }

    /** @test */
    function is_redirected_to_posts_page_after_creation()
    {
        $this->actingAs(User::factory()->create());

```php
Livewire::test(CreatePost::class)
    ->set('title', 'foo')
    ->call('create')
    ->assertRedirect('/posts');
}
```

## 測試元件存在性 {#testing-component-presence}

Livewire 註冊了方便的 PHPUnit 方法，用於測試頁面上元件的存在性。

```php
class CreatePostTest extends TestCase
{
    /** @test */
    function post_creation_page_contains_livewire_component()
    {
        $this->get('/posts/create')->assertSeeLivewire('create-post');
    }

    /** @test */
    function post_creation_page_doesnt_contain_livewire_component()
    {
        $this->get('/posts/create')->assertDontSeeLivewire('edit-post');
    }
}
```

或者，您可以將元件的類別名稱傳遞給 `assertSeeLivewire` 和 `assertDontSeeLivewire` 方法。

```php
use App\Http\Livewire\CreatePost;
use App\Http\Livewire\EditPost;

class CreatePostTest extends TestCase
{
    /** @test */
    function post_creation_page_contains_livewire_component()
    {
        $this->get('/posts/create')->assertSeeLivewire(CreatePost::class);
    }

    /** @test */
    function post_creation_page_doesnt_contain_livewire_component()
    {
        $this->get('/posts/create')->assertDontSeeLivewire(EditPost::class);
    }
}
```

## 使用查詢字串參數進行測試 {#testing-querystring}

要測試 Livewire 的 `$queryString` 功能，您可以使用 Livewire 的 `::withQueryParams` 測試工具。

```php
class CreatePostTest extends TestCase
{
    /** @test */
    function post_creation_page_contains_livewire_component()
    {
        Livewire::withQueryParams(['foo' => 'bar'])
            ->test(ShowFoo::class)
            ->assertSet('foo', 'bar')
            ->assertSee('bar');
    }
}
```

## 測試帶有傳遞資料的元件 {#testing-passed-data}

```php
<livewire:show-foo foo="bar">
```

```php
class CreatePostTest extends TestCase
{
    /** @test */
    function has_data_passed_correctly()
    {
        Livewire::test(ShowFoo::class, ['foo' => 'bar'])
            ->assertSet('foo', 'bar')
            ->assertSee('bar');
    }
}
```

## 生成測試 {#generating-tests}

在建立元件時，您可以包含 `--test` 標誌，系統將為您創建一個測試檔案。

@component('components.code', ['lang' => 'shell'])
php artisan make:livewire ShowPosts --test
@endcomponent

@component('components.code-component', ['lang' => 'php', 'className' => 'tests/Feature/Livewire/ShowPostsTest.php'])
@slot('class')
class ShowPostsTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(ShowPosts::class);

        $component->assertStatus(200);
    }
}
@endslot
@endcomponent

## 所有可用的測試方法 {#all-testing-methods}

@component('components.code', ['lang' => 'php'])
Livewire::actingAs($user);
// 將提供的使用者設置為測試中的會話登錄使用者

Livewire::withQueryParams(['foo' => 'bar']);
// 將查詢參數 "foo" 設置為 "bar"，以便 Livewire 元件的 `$queryString` 屬性可以接收

Livewire::test('foo', ['bar' => $bar]);
// 使用 "bar" 作為參數測試 "foo" 元件

->set('foo', 'bar');
// 將 "foo" 屬性 (`public $foo`) 設置為值: "bar"

->toggle('foo');
// 在 true 和 false 之間切換 "foo" 屬性 (`public $foo`)

->call('foo');
// 呼叫 "foo" 方法

->call('foo', 'bar', 'baz');
// 呼叫 "foo" 方法，並傳遞 "bar" 和 "baz" 參數

->emit('foo');
// 觸發 "foo" 事件

->emit('foo', 'bar', 'baz');
// 觸發 "foo" 事件，並傳遞 "bar" 和 "baz" 參數

->assertSet('foo', 'bar');
// 斷言 "foo" 屬性設置為值 "bar"（包括計算屬性）

->assertNotSet('foo', 'bar');
// 斷言 "foo" 屬性未設置為值 "bar"（包括計算屬性）

->assertCount('foo', 1);
// 斷言 "foo" 屬性（陣列）計數為 1（包括計算屬性）

->assertPayloadSet('foo', 'bar');
// 斷言 Livewire 返回的 JavaScript 載荷中的 "foo" 屬性設置為值 "bar"

->assertPayloadNotSet('foo', 'bar');
// 斷言 Livewire 返回的 JavaScript 載荷中的 "foo" 屬性未設置為值 "bar"

```plaintext
->assertViewIs('foo');
// 斷言視圖 "foo" 是目前渲染的視圖

->assertViewHas('foo', 'bar');
// 斷言渲染的視圖具有鍵為 "foo" 且值為 "bar"

->assertSee('foo');
// 斷言字串 "foo" 存在於元件目前渲染的內容中

->assertDontSee('foo');
// 斷言字串 "foo" 不存在於元件目前渲染的內容中

->assertSeeHtml('<h1>foo</h1>');
// 斷言字串 "<h1>foo</h1>" 存在於元件目前渲染的 HTML 中

->assertDontSeeHtml('<h1>foo</h1>');
// 斷言字串 "<h1>foo</h1>" 不存在於元件目前渲染的 HTML 中

->assertSeeInOrder(['foo', 'bar']);
// 斷言字串 "foo" 存在於元件目前渲染的內容中且在 "bar" 之前

->assertSeeHtmlInOrder(['<h1>foo</h1>', '<h1>bar</h1>']);
// 斷言字串 "<h1>foo</h1>" 存在於元件目前渲染的內容中且在 "<h1>bar</h1>" 之前

->assertEmitted('foo');
// 斷言已發出 "foo" 事件

->assertEmitted('foo', 'bar', 'baz');
// 斷言已發出 "foo" 事件並帶有 "bar" 和 "baz" 參數

->assertNotEmitted('foo');
// 斷言未發出 "foo" 事件

->assertEmittedTo('bar','foo');
// 斷言已發出 "foo" 事件至 "bar" 元件

->assertHasErrors('foo');
// 斷言 "foo" 屬性具有驗證錯誤

->assertHasErrors(['foo', 'bar']);
// 斷言 "foo" 和 "bar" 屬性具有驗證錯誤

->assertHasErrors(['foo' => 'required']);
// 斷言 "foo" 屬性具有 "required" 驗證規則錯誤

->assertHasErrors(['foo' => ['required', 'min']]);
// 斷言 "foo" 屬性具有 "required" 和 "min" 驗證規則錯誤

->assertHasNoErrors('foo');
// 斷言 "foo" 屬性沒有驗證錯誤

->assertHasNoErrors(['foo', 'bar']);
// 斷言 "foo" 和 "bar" 屬性沒有驗證錯誤

->assertNotFound();
// 斷言元件內部錯誤導致狀態碼為 404 的錯誤
```

```php
->assertRedirect('/some-path');
// 斷言從元件觸發了重新導向

->assertNoRedirect();
// 斷言從元件未觸發重新導向

->assertUnauthorized();
// 斷言元件內部錯誤導致狀態碼為 401 的錯誤

->assertForbidden();
// 斷言元件內部錯誤導致狀態碼為 403 的錯誤

->assertStatus(500);
// 斷言元件內部錯誤導致狀態碼為 500 的錯誤

->assertDispatchedBrowserEvent('event', $data);
// 斷言使用 (->dispatchBrowserEvent(...)) 從元件派發了瀏覽器事件

->assertNotDispatchedBrowserEvent('event');
// 斷言使用 (->dispatchBrowserEvent(...)) 從元件未派發瀏覽器事件

->assertFileDownloaded($filename)
// 斷言返回了具有特定名稱的下載文件
@endcomponent
```

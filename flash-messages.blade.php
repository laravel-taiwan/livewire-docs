* [簡介](#introduction)

## 簡介 {#introduction}

在需要向使用者“快閃”成功或失敗訊息時，Livewire 支援 Laravel 的快閃資料到 Session 的系統。

以下是一個常見的使用範例：

@component('components.code-component')
@slot('class')
@verbatim
class UpdatePost extends Component
{
    public Post $post;

    protected $rules = [
        'post.title' => 'required',
    ];

    public function update()
    {
        $this->validate();

        $this->post->save();

        session()->flash('message', '文章已成功更新。');
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<form wire:submit.prevent="update">
    <div>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>

    標題: <input wire:model="post.title" type="text">

    <button>儲存</button>
</form>
@endverbatim
@endslot
@endcomponent

現在，當使用者點擊“儲存”並更新他們的文章後，他們將在頁面上看到“文章已成功更新”。

如果您希望將快閃資料添加到重新導向並在目的地頁面上顯示訊息，Livewire 足夠智能，可以將快閃資料持續保存一個請求。例如：

@component('components.code-component')
@slot('class')
@verbatim
public function update()
{
    $this->validate();

    $this->post->save();

    session()->flash('message', '文章已成功更新。');

    return redirect()->to('/posts');
}
@endverbatim
@endslot
@endcomponent

現在當使用者“儲存”一篇文章時，他們將被重新導向到“/posts”端點並在那裡看到快閃訊息。這假設“/posts”頁面有適當的 Blade 片段來顯示快閃訊息。

* [簡介](#introduction)

## 簡介 {#introduction}

在 Livewire 中授權操作，您可以在任何元件中使用 `AuthorizesRequests` 取值器，然後像在控制器中一樣呼叫 `$this->authorize()`。例如：

@component('components.code', ['lang' => 'php'])
@verbatim
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EditPost extends \Livewire\Component
{
    use AuthorizesRequests;

    public $post;

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function save()
    {
        $this->authorize('update', $this->post);

        $this->post->update(['title' => $this->title]);
    }
}
@endverbatim
@endcomponent

如果您使用不同的警衛來驗證您的使用者，那麼也將一個條目添加到 livewire 組態檔中的 middleware_group：
@component('components.code', ['lang' => 'php'])
@verbatim
...
'middleware_group' => ['web', 'auth:otherguard'],
...
@endverbatim
@endcomponent

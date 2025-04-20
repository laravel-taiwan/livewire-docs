* [簡介](#introduction)

## 簡介 {#introduction}

Livewire提供了`wire:init`指示詞，以便在元件呈現時立即執行動作。這在您不想阻止整個頁面載入，但又想在頁面載入後立即加載一些資料的情況下非常有用。

@component('components.code-component')
@slot('class')
@verbatim
class ShowPost extends Component
{
    public $readyToLoad = false;

    public function loadPosts()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.show-posts', [
            'posts' => $this->readyToLoad
                ? Post::all()
                : [],
        ]);
    }
}
@endverbatim
@endslot
@slot('view')
@verbatim
<div wire:init="loadPosts">
    <ul>
        @foreach ($posts as $post)
            <li>{{ $post->title }}</li>
        @endforeach
    </ul>
</div>
@endverbatim
@endslot
@endcomponent

`loadPosts`動作將在Livewire元件在頁面上呈現後立即執行。

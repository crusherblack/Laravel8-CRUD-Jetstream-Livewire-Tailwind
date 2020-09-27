<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class Posts extends Component
{
    use WithPagination;

    public $search;
    public $postId,$title,$description;
    public $isOpen = 0;

    public function render()
    {       
        $searchParams = '%'.$this->search.'%';
        return view('livewire.posts', [
            'posts' => Post::where('title','like', $searchParams)->latest()->paginate(5)
        ]);
    }

    public function showModal() {
        $this->isOpen = true;
    }

    public function hideModal() {
        $this->isOpen = false;
    }

    public function store(){
        $this->validate(
            [
                'title' => 'required',
                'description' => 'required',
            ]
        );

        Post::updateOrCreate(['id' => $this->postId], [
            'title' => $this->title,
            'description' => $this->description
        ]);

        $this->hideModal();

        session()->flash('info', $this->postId ? 'Post Update Successfully' : 'Post Created Successfully' );

        $this->postId = '';
        $this->title = '';
        $this->description = '';
    }

    public function edit($id){
        $post = Post::findOrFail($id);
        $this->postId = $id;
        $this->title = $post->title;
        $this->description = $post->description;

        $this->showModal();
    }

    public function delete($id){
        Post::find($id)->delete();
        session()->flash('delete','Post Successfully Deleted');
    }
}

<?php

namespace App\Http\Livewire\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Listing extends Component
{
    public $model;

    public Collection $commentList;

    public string $newComment = '';

    public bool $confirmingDelete = false;

    public ?int $toDelete;

    protected $listeners = ['posted' => '$refresh', 'deleted' => '$refresh'];

    public function hydrate()
    {
        $this->populateCommentList();
    }

    public function mount()
    {
        $this->populateCommentList();
    }

    public function post()
    {
        $comment = new Comment(['comment' => $this->newComment]);
        $comment->commentable()->associate($this->model);
        $comment->user()->associate(Auth::user());
        $comment->save();

        $this->newComment = '';

        $this->emitSelf('posted');
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDelete = true;
        $this->toDelete = $id;
    }

    public function delete()
    {
        Comment::find($this->toDelete)->delete();

        $this->confirmingDelete = false;
        $this->toDelete = null;

        $this->emitSelf('deleted');
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function render()
    {
        return view('comments.listing');
    }

    private function populateCommentList()
    {
        $this->commentList = $this->model->comments()->orderByDesc('id')->get();
    }
}

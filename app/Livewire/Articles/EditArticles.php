<?php

namespace App\Livewire\Articles;

use Livewire\Component;
use App\Models\Article;
class EditArticles extends Component
{
    public $title;
    public $author;
    public $content;
    public $status;
    public $id;
    public function update()
    {
        $this->validate([
            'title'=>'required|min:5',
            'author'=>'required|min:5',
        ]);
        $article =  Article::find($this->id);
        $article->title = $this->title;
        $article->author = $this->author;
        $article->content = $this->content;
        $article->status = $this->status;
        $article->save();
        session()->flash('success','Article update successfully.');
        $this->redirect('/articles');
    }
    public function mount($id)
    {
        $article = Article::findOrFail($id);
        $this->title = $article->title;
        $this->author = $article->author;
        $this->content = $article->content;
        $this->status = $article->status;
        $this->id = $article->id;
    }
    public function render()
    {
        return view('livewire.articles.edit-articles');
    }
}

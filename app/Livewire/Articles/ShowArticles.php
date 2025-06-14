<?php

namespace App\Livewire\Articles;

use Livewire\Component;
use App\Models\Article;
use Livewire\WithPagination;
class ShowArticles extends Component
{
    use WithPagination;
    protected $queryString =['keyword'];
    public $keyword='';
    public $textInput='';
    public function delete($id)
    {
        $article = Article::find($id);
        $article->delete();
        session()->flash('success','Article deleted successfully.');
        $this->redirect('/articles');

    }
    public function render()
    {
        $articles = Article::orderBy('id','DESC')
                        ->where('title','like','%'.$this->keyword.'%')
                        ->orWhere('author','like','%'.$this->keyword.'%')
                        ->paginate(5);
        return view('livewire.articles.show-articles',compact('articles')); 
    }
    public function search(){
        $this->keyword=$this->textInput;
    }
}

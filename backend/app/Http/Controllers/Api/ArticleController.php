<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    public function index(){
        return ArticleResource::collection(Article::published()->latest()->paginate(2));
    }

    //store article
    public function store(StoreArticleRequest $request){
        if($request->validated()){
            //save the image get the file name
            $file = $request->file('image');
            $file_name = $this->saveImage($file);

            //save article
            $article = Article::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'body' => $request->body,
                'excerpt' => $request->excerpt,
                'image' => 'storage/articles/images'.$file_name,
                'user_id' => $request->user()->id
            ]);
            //store article's tags
            if(!empty($request->tags)){
                $tags = expode(',', $request->tags);
                $article->tags()->sync($tags);
            }
            return response()->json([
                'message'=>'Article has been saved successfully and will be published soon!'
            ]);
        }
    }

    //save images in storage
    public function saveImage($file){
        $file_name = time().'_'.'article'.'_'.$file->getClientOriginalName();
        $file->storeAs('articles/image', $file_name, 'public');
        return $file_name;
    }

    //showing articles
    public function show(Article $article){
        //if article does not exists return 404
        if(!$article->published){
            abort(404);
        }
        //return article
        return ArticleResource::make($article);
    }

    //update article
    public function update(UpdateArticleRequest $request, $article){
        if($request->validated()){
            if($request->has('image')){
                //remove prev article image
              if(File::exists($article->image)){
                File::delete($article->image);
              }  
            //save the new article image get the file name
            $file = $request->file('image');
            $article->image = 'storage/articles/images/'.$this->saveImage($file);
            }           

            //save article
            $article->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'body' => $request->body,
                'excerpt' => $request->excerpt,                
                'user_id' => $request->user()->id,
                'published' => 0
            ]);
            //update article's tags
            if(!empty($request->tags)){
                $tags = explode(',', $request->tags);
                $article->tags()->sync($tags);
            }
            return response()->json([
                'user' => $request->user(),
                'message'=>'Article has been updated successfully and will be published soon!'
            ]);
        }
    }

    //delete article
    public function delete(Request $request, $article){
        if($aticle->user_id === $request->user()->id){            
                //remove article image
              if(File::exists($article->image)){
                File::delete($article->image);
            }
            $article->delete();           
            return response()->json([
                'user' => $request->user(),
                'message'=>'Article has been deleted successfully!' 
                ]);
        }else{
            return response()->json([
                'user' => $request->user(),
                'message'=>'Somthing went wrong!'  
            ]);
        }
    }

 //increment article claps count
 public function articleClap($article){
    //Incrment article's claps count by 1
    $article->increment('clapsCount');
    return ArticleResource::make($article);
 }

 //fetch articles by tags
 public function fetchByTag($tag){    
    return ArticleResource::collection($tag->articles()->paginate(2));
 }

 //fetch followings articles
 public function fetchFollowingArticles(Request $request){
    $article = Article::whereIn('user_id', $request->user()->followings->pluck('id'))
    ->published()->paginate(2);    
    return ArticleResource::collection($articles);
 }

 //find articles
 public function fetchByTerm(Request $request){
    $searchTerm = $request->searchTerm;
    $article = Article::where('title', 'like', '%'.$searchTerm.'%')
    ->published()->get();    
    return ArticleResource::collection($articles);
 }


}

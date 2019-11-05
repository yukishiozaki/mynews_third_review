<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// 以下を追記することでNews Modelが扱えるようになる
use App\News;

class NewsController extends Controller
{
  public function add()
  {
      return view('admin.news.create');
  }

  public function create(Request $request)
  {

      // 以下を追記
      // Varidationを行う
        $validatedData = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'image',
        ]);

        $news = new News();
        $news->title = $validatedData['title'];
        $news->body = $validatedData['body'];

        if (isset($validatedData['image'])) {
            $path = $validatedData['image']->store('images');
            $news->image_path = basename($path);
        }
        $news->save();
        return redirect('admin/news/create');
  }
  
  public function index(Request $request)
  {
    $cond_title = $request->cond_title;
    if ($cond_title != '') {
      $posts = News::where('title', $cond_title)->get();
    
    } else {
      $posts = News::all();
    }
    return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }
  
  public function edit(Request $request) 
  {
    
    $news = News::find($request->id);
    if (empty($news)) {
      abort(404);
    }
    return view('admin.news.edit', ['news_form' => $news]);
  }
  
  public function update(Request $request)
  {
    
    // Validationをかける
      $this->validate($request, News::$rules);
      // News Modelからデータを取得する
      $news = News::find($request->id);
      // 送信されてきたフォームデータを格納する
      $news_form = $request->all();
      if (isset($news_form['image'])) {
        $path = $request->file('image')->store('public/image');
        $news->image_path = basename($path);
        unset($news_form['image']);
      } elseif (isset($request->remove)) {
        $news->image_path = null;
        unset($news_form['remove']);
      }
      unset($news_form['_token']);
      // 該当するデータを上書きして保存する
      $news->fill($news_form)->save();

      return redirect('admin/news');
  }
  
  public function delete(Request $request)
  {
    
    $news = News::find($request->id);
    
    $news->delete();
    return redirect('admin/news/');
  }
}

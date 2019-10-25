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

}
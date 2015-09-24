<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Http\Controllers\AdminController;
use App\Article;
use App\ArticleCategory;
use App\Image;
use App\Language;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\Admin\ArticleRequest;
use Illuminate\Support\Facades\Auth;
use Datatables;
use Log;
use Exception;

class ArticleController extends AdminController {

    public function __construct()
    {
        view()->share('type', 'article');
    }
     /*
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index()
    {
        // Show the page
        return view('admin.article.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $languages = Language::lists('name', 'id')->toArray();
        $articlecategories = ArticleCategory::lists('title', 'id')->toArray();
        $types = ['text' => 'Text', 'photo' => 'Photo'];
        return view('admin.article.create_edit', compact('languages', 'articlecategories', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ArticleRequest $request)
    {
        $article = new Article($request->except('image'));
        $article -> user_id = Auth::id();

        $picture = "";
        if(Input::hasFile('image'))
        {
            $file = Input::file('image');
            $filename = $file->getClientOriginalName();
            $extension = $file -> getClientOriginalExtension();
            $picture = sha1($filename . time()) . '.' . $extension;
        }
        $article -> picture = $picture;
        $article -> save();

        if(Input::hasFile('image'))
        {
            $destinationPath = public_path() . '/images/article/'.$article->id.'/';
            Input::file('image')->move($destinationPath, $picture);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Article $article)
    {
        $languages = Language::lists('name', 'id')->toArray();
        $articlecategories = ArticleCategory::lists('title', 'id')->toArray();
        $types = ['text' => 'Text', 'photo' => 'Photo'];
        return view('admin.article.create_edit',compact('article','languages','articlecategories','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(ArticleRequest $request, Article $article)
    {
        try {
            $article->user_id = Auth::id();
            $picture = "";
            $pictureUploadedFile = Input::file('picture');
            if ($pictureUploadedFile) {
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $pictureUploadedFile;
                if ($file->getError() == 1)
                    throw new Exception(sprintf(
                        'The file "%s" exceeds your upload maximum filesize (limit is %d KiB).',
                        $file->getClientOriginalName(), $file->getMaxFilesize() / 1024));
                if ($file->getError()) throw new Exception("Uploaded file error: " . $file->getErrorMessage());
                $mimeType = $file->getMimeType();
                $allowedMimeTypes = ['jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png'];
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    throw new Exception("Not allowed mime type of uploaded file: $mimeType");
                }
                $extension = array_search($mimeType, $allowedMimeTypes);
                $image = new Image();
                $image->extension = $extension;
                $image->save();
                Log::info('Uploaded image id: ' . $image->id);
                $picture = $image->id . '.' . $extension;
            }
            $article->picture = $picture;
            $article->update($request->except('files', 'picture'));

            $destinationPath = public_path() . '/appfiles/article/' . $article->id . '/';
            if (Input::hasFile('files')) {
                $file = Input::file('files');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $picture = sha1($filename . time()) . '.' . $extension;
                Input::file('files')->move($destinationPath, $picture);
            }
            if (Input::hasFile('picture')) {
                Input::file('picture')->move($destinationPath, $picture);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */

    public function delete(Article $article)
    {
        return view('admin.article.delete', compact('article'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
    }


    /**
     * Show a list of all the languages posts formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function data()
    {
        $article = Article::join('languages', 'languages.id', '=', 'articles.language_id')
            ->join('article_categories', 'article_categories.id', '=', 'articles.article_category_id')
            ->select(array('articles.id','articles.title','article_categories.title as category'/*, 'languages.name'*/,
                'articles.created_at'));

        return Datatables::of($article)
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/article/\' . $id . \'/edit\' ) }}}" class="btn btn-success btn-sm iframe" ><span class="glyphicon glyphicon-pencil"></span>  {{ trans("admin/modal.edit") }}</a>
                    <a href="{{{ URL::to(\'admin/article/\' . $id . \'/delete\' ) }}}" class="btn btn-sm btn-danger iframe"><span class="glyphicon glyphicon-trash"></span> {{ trans("admin/modal.delete") }}</a>
                    <input type="hidden" name="row" value="{{$id}}" id="row">')
            ->remove_column('id')

            ->make();
    }
}

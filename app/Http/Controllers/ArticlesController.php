<?php

namespace App\Http\Controllers;

use App\Article;

class ArticlesController extends Controller {

    public function index()
    {
        $articles = Article::paginate(5);
        $articles->setPath('articles/');

        return view('article.index', compact('articles'));
    }

	public function show($slug, $needPage = 1)
	{
        /** @var Article $article */
		$article = Article::findBySlugOrId($slug);
        $content = $article->content;
        $newContent = '';
        $curPage = 1;
        foreach (explode("\n", $content) as $line) {
            if (strpos($line, '<!--nextpage-->') !== false) {
                $curPage++;
                continue;
            }
            if ($curPage == $needPage) $newContent .= $line . "\n";
        }
        $pages = [];
        if ($content != $newContent) {
            $article->content = $newContent;
            $pages['previous'] = ($needPage == 1) ? false : $needPage - 1;
            $pages['next'] = ($needPage == $curPage) ? false : $needPage + 1;
        }
		return view('article.view', compact('article', 'pages'));
	}

}

<?php

namespace App\Http\Controllers;

use App\Article;
use ChrisKonnertz\OpenGraph\OpenGraph;

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
        // Paginator section
        $content = $article->content;
        $newContent = '';
        $curPage = 1;
        foreach (explode("\n", $content) as $line) {
            $nextPagePos = strpos($line, '<!--nextpage-->');
            if ($nextPagePos !== false) {
                if ($curPage == $needPage) $newContent .= substr($line, 0, $nextPagePos) . "\n";
                $curPage++;
                $line = substr($line, $nextPagePos + 15);
            }
            if ($curPage == $needPage) $newContent .= $line . "\n";
        }
        $pages = [];
        if ($content != $newContent) {
            $article->content = $newContent;
            $pages['previous'] = ($needPage == 1) ? false : $needPage - 1;
            $pages['next'] = ($needPage == $curPage) ? false : $needPage + 1;
            $pages['last'] = $curPage;
            $pages['current'] = $needPage;
            $pages['photo'] = $article->type == 'photo';
            $maxLinks = 7;
            $maxBegin = $pages['last'] - ($maxLinks - 1);
            if ($maxBegin < 1) $maxBegin = 1;
            $pages['begin'] = $pages['current'] - floor($maxLinks / 2);
            if ($pages['begin'] < 1) $pages['begin'] = 1;
            if ($pages['begin'] > $maxBegin) $pages['begin'] = $maxBegin;
            $pages['end'] = $pages['begin'] + ($maxLinks - 1);
            if ($pages['end'] > $pages['last']) $pages['end'] = $pages['last'];
        }
        // ./ Paginator section

        $og = new OpenGraph();
        $og->title($article->title)
            ->type('article')
            ->image('http:' . $article->getPictureUrl('200x200'))
            ->description($article->introduction)
            ->url()
            ->article(['author' => $article->author->name,
                'published_time' => $article->created_at,
                'modified_time' => $article->updated_at,
                'section' => $article->category->title
            ]);
        if ($article->tags) {
            foreach (preg_split('/,/', $article->tags) as $tag) {
                $og->article(['tag' => trim($tag)]);
            }
        }
		return view('article.view', compact('article', 'pages', 'og'));
	}

}

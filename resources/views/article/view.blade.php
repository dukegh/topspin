@extends('layouts.app')
{{-- Web site Title --}}
@section('title') {!!  $article->title !!} :: @parent @stop

@section('meta_keywords')
    <meta name="keywords" content="{!! $article->tags !!}"/>
@stop

@section('meta_author')
    <meta name="author" content="{!! $article->author->name !!}"/>
@stop

@section('meta_description')
    <meta name="description" content="{!! preg_replace("/\r|\n/", '', strip_tags($article->introduction)) !!}"/>
    {!! $og->renderTags() !!}
@stop

@section('pager')
    <div class="text-center">
    <ul class="pagination">
        <li class="first{{$pages['current'] == 1 ? ' disabled' : ''}}"><a href="{{ URL::to('article/'.$article->slug.'/1') }}" title="Start"><span class="fa fa-fast-backward"></span></a></li>
        <li class="prev{{$pages['current'] == 1 ? ' disabled' : ''}}"><a id="previousPage" href="{{ URL::to('article/'.$article->slug.'/' . ($pages['previous'] ?: $pages['last'])) }}" title="Prev"><span class="fa fa-step-backward"></span></a></li>
        @for ($i = $pages['begin']; $i <= $pages['end']; $i++)
            <li class="{{$i == $pages['current'] ? 'active' : ''}}"><a href="{{ URL::to('article/'.$article->slug.'/' . $i) }}">{{$i}}</a></li>
        @endfor
        <li class="next{{$pages['current'] == $pages['last'] ? ' disabled' : ''}}"><a id="nextPage" href="{{ URL::to('article/'.$article->slug.'/' . $pages['next']) }}" title="Next"><span class="fa fa-step-forward"></span></a></li>
        <li class="last{{$pages['current'] == $pages['last'] ? ' disabled' : ''}}"><a href="{{ URL::to('article/'.$article->slug.'/' . $pages['last']) }}" title="Last"><span class="fa fa-fast-forward"></span></a></li>
    </ul>
    </div>
@stop

@section('photopager')
    <div id="photonavl"></div>
    <div id="photonavr"></div>
    <style>
        #photonavl, #photonavr {
            position: absolute;
            top: 50%;
            height: 46px;
            width: 30px;
            background: transparent url('http://www.jssor.com/img/a12.png');
            cursor: pointer;
            margin-top: -23px;
            display: block;
            overflow: hidden;
        }
        #photonavl {
            background-position: -16px -37px;
            left: 0;
        }
        #photonavr {
            background-position: -75px -37px;
            right: 0;
        }
    </style>
    <script type="application/javascript">
        $(function() {
            /*$('ul.pager').hide();*/
            var img = $('#content').find('img');
            if (img.length > 0) {
                img.first().wrap( '<div id="photoframe" style="position: relative;"></div>' );
                $('#photoframe').width(img.width())
                        .append($('#photonavl').click(function(){ window.location = $('a#previousPage').attr('href'); }))
                        .append($('#photonavr').click(function(){ window.location = $('a#nextPage').attr('href'); }));
            }
        });
    </script>
@stop

{{-- Content --}}
@section('content')
    <h3>{{ $article->title }}</h3><span class="glyphicon glyphicon-user"></span> by <span class="muted">{{ $article->author->name }}</span>
    {{--<p>{!! $article->introduction !!}</p>
    @if($article->picture!="")
        <img alt="{{$article->picture}}"
             src="{!! URL::to('appfiles/article/'.$article->id.'/'.$article->picture) !!}"/>
    @endif--}}
    <div id="content">{!! $article->content !!}</div>
    @if ($pages['next'] || $pages['previous'])
        @yield('pager')
        @if ($pages['photo'])
            @yield('photopager')
        @endif
    @endif
    <div>
        <span class="badge badge-info">Posted {!!  $article->created_at !!} </span>
    </div>
@stop

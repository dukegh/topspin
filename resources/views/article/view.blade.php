@extends('layouts.app')
{{-- Web site Title --}}
@section('title') {!!  $article->title !!} :: @parent @stop

@section('meta_author')
    <meta name="author" content="{!! $article->author->username !!}"/>
@stop

@section('pager')
    <ul class="pager">
        <li class="{{$pages['previous'] ? '' : 'disabled'}}"><a id="previousPage" href="{{ URL::to('article/'.$article->slug.'/' . ($pages['previous'] ?: $pages['last'])) }}">Previous</a></li>
        <li class="{{$pages['next'] ? '' : 'disabled'}}"><a id="nextPage" href="{{ URL::to('article/'.$article->slug.'/' . $pages['next']) }}">Next</a></li>
    </ul>
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
    {{--<p>{!! $article->introduction !!}</p>--}}
    @if($article->picture!="")
        <img alt="{{$article->picture}}"
             src="{!! URL::to('appfiles/article/'.$article->id.'/'.$article->picture) !!}"/>
    @endif
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

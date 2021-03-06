@extends('admin.layouts.modal')
{{-- Content --}}
@section('content')
@if (isset($article))
<h3 id="articleFormTitle">Edit article</h3>
{!! Form::model($article, array('url' => URL::to('admin/article') . '/' . $article->id, 'method' => 'put','id'=>'fupload', 'class' => 'bf', 'files'=> true)) !!}
@else
<h3 id="articleFormTitle">Add article</h3>
{!! Form::open(array('url' => URL::to('admin/article'), 'method' => 'post', 'class' => 'bf','id'=>'fupload', 'files'=> true)) !!}
@endif
        <!-- Tabs Content -->
<div class="tab-content">
    <!-- General tab -->
    <div class="tab-pane active" id="tab-general">
        {{--<div class="form-group  {{ $errors->has('language_id') ? 'has-error' : '' }}">
            {!! Form::label('language_id', trans("admin/admin.language"), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::select('language_id', $languages, @isset($article)? $article->language_id : 'default', array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('language_id', ':message') }}</span>
            </div>
        </div>--}}
        <input type="hidden" name="language_id" value="1">
        <div style="width: 100%">
            <div style="width: 80%;display: inline-block;float: left">
                <div class="form-group  {{ $errors->has('title') ? 'has-error' : '' }}">
                    {!! Form::label('title', trans("admin/modal.title"), array('class' => 'control-label')) !!}
                    <div class="controls">
                        {!! Form::text('title', null, array('class' => 'form-control')) !!}
                        <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                    </div>
                </div>

                <div class="form-group  {{ $errors->has('introduction') ? 'has-error' : '' }}">
                    {!! Form::label('introduction', trans("admin/article.introduction"), array('class' => 'control-label')) !!}
                    <div class="controls">
                        {!! Form::textarea('introduction', null, array('class' => 'form-control', 'rows' => 3)) !!}
                        <span class="help-block">{{ $errors->first('introduction', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group  {{ $errors->has('content') ? 'has-error' : '' }}">
                    {!! Form::label('content', trans("admin/article.content"), array('class' => 'control-label')) !!}
                    <div class="controls">
                        {!! Form::textarea('content', null, array('class' => 'form-control')) !!}
                        <span class="help-block">{{ $errors->first('content', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div style="width: 18%;display: inline-block;clear: both;margin-left: 2%">
                <div class="form-group  {{ $errors->has('type') ? 'has-error' : '' }}">
                    {!! Form::label('type', trans("admin/article.type"), array('class' => 'control-label')) !!}
                    <div class="controls">
                        {!! Form::select('type', $types, @isset($article)? $article->type : 'default', array('class' => 'form-control')) !!}
                        <span class="help-block">{{ $errors->first('type', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group  {{ $errors->has('article_category_id') ? 'has-error' : '' }}">
                    {!! Form::label('language_id', trans("admin/article.category"), array('class' => 'control-label')) !!}
                    <div class="controls">
                        {!! Form::select('article_category_id', $articlecategories, @isset($article)? $article->article_category_id : '1', array('class' => 'form-control')) !!}
                        <span class="help-block">{{ $errors->first('article_category_id', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group  {{ $errors->has('tags') ? 'has-error' : '' }}">
                    {!! Form::label('tags', trans("admin/article.tags"), array('class' => 'control-label')) !!}
                    <div class="controls">
                        {!! Form::textarea('tags', null, array('class' => 'form-control', 'rows' => 3)) !!}
                        <span class="help-block">{{ $errors->first('article_category_id', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group {!! $errors->has('picture') ? 'error' : '' !!} ">
                    <div class="col-lg-12">
                        {!! Form::label('picture', trans("admin/article.picture"), array('class' => 'control-label')) !!}
                        <img src="{{$picture}}" id="article-img">
                        {!! Form::hidden('picture') !!}
                        <span class="btn btn-success fileinput-button" id="uploadImageButton" style="margin-top: 10px;">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Select file...</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="form-group  {{ $errors->has('source') ? 'has-error' : '' }}">
            {!! Form::label('source', trans("admin/article.source"), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('source', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('source', ':message') }}</span>
            </div>
        </div>--}}
        <!-- ./ general tab -->
    </div>
    <!-- ./ tabs content -->
</div>

<!-- Form Actions -->

<div class="form-group">
    <div class="col-md-12">
        <button type="reset" class="btn btn-sm btn-warning close_popup">
            <span class="glyphicon glyphicon-ban-circle"></span> {{
						trans("admin/modal.cancel") }}
        </button>
        <button type="reset" class="btn btn-sm btn-default">
            <span class="glyphicon glyphicon-remove-circle"></span> {{
						trans("admin/modal.reset") }}
        </button>
        <button type="submit" class="btn btn-sm btn-success">
            <span class="glyphicon glyphicon-ok-circle"></span>
            @if	(isset($article))
                {{ trans("admin/modal.edit") }}
            @else
                {{trans("admin/modal.create") }}
            @endif
        </button>
    </div>
</div>
<!-- ./ form actions -->
{!! Form::close() !!}
{!! Form::open(['method' => 'POST', 'class' => 'hide']) !!}
    <input type="hidden" name="storage" value="article">
    <input type="hidden" name="uid" value="{{isset($article) ? $article->id : 1}}">
    <input type="hidden" name="filetype" value="image">
    <input id="fileupload" type="file" name="file" data-url="/admin/fileupload">
{!! Form::close() !!}

<script type="application/javascript">
    $(function () {
        $('#fileupload').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $('#article-img').attr('src', data.result.url);
                $('input[name="picture"]').attr('value', data.result.filename);
            },
            fail: function (e, data) {
                if (data.textStatus == 'error') {
                    var jsonResponse = jQuery.parseJSON(data.jqXHR.responseText);
                    alert(jsonResponse.error);
                }
                $('#article-img').attr('src', self.prevImgSrc);
            },
            start: function () {
                var articleImg = $('#article-img');
                self.prevImgSrc = articleImg.attr('src');
                articleImg.attr('src', '/img/loading124.gif');
            }
        });
        $('#uploadImageButton').click(function(){
            $('#fileupload').click();
        });
    });
</script>
@stop

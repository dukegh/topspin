<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@section('title') Administration @show</title>
    @section('meta_keywords')
        <meta name="keywords" content="your, awesome, keywords, here"/>
    @show @section('meta_author')
        <meta name="author" content="Jon Doe"/>
    @show @section('meta_description')
        <meta name="description"
              content="Lorem ipsum dolor sit amet, nihil fabulas et sea, nam posse menandri scripserit no, mei."/>
    @show
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin.js') }}"></script>
    @yield('styles')
</head>
<body>
<!-- Container -->
<div class="container">
    <div class="page-header">
        &nbsp;
        <div class="pull-right">
            <button class="btn btn-primary btn-xs close_popup">
                <span class="glyphicon glyphicon-backward"></span> {!! trans('admin/admin.back')!!}
            </button>
        </div>
    </div>
    <!-- Content -->
    @yield('content')
            <!-- ./ content -->
    <script type="text/javascript">
        $(function () {
            $('textarea[name="content"]').summernote({
                height: 250,
                onImageUpload: function(files) {
                    modalTools.showPleaseWait();
                    sendFile(files[0])
                }});
            function sendFile(file) {
                data = new FormData();
                data.append("file", file);
                data.append("storage", 'article');
                data.append("uid", $('input[name="uid"]').attr('value'));
                data.append("filetype", 'image');
                data.append("_token", $('input[name="_token"]').get(0).value);
                $.ajax({
                    data: data,
                    type: "POST",
                    url: "/admin/fileupload",
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json'
                }).success( function(data) {
                        var img = $('<img>');
                        img.attr('src', data.url);
                        $('textarea[name="content"]').summernote('insertNode', img.get(0));
                        modalTools.hidePleaseWait();
                }).fail(function (jqXHR/*, textStatus, errorThrown*/) {
                    var textResponse = jqXHR.responseText;
                    var alertText = "One of the following conditions is not met:<br><br>";
                    var jsonResponse = jQuery.parseJSON(textResponse);

                    $.each(jsonResponse, function (n, elem) {
                        alertText = alertText + elem + "<br>";
                    });
                    modalTools.hidePleaseWait();
                    var alertDialog = $('#alertDialog');
                    alertDialog.find('.modal-content p').html(alertText);
                    alertDialog.modal()
                });

            }
            $('form').submit(function (event) {
                event.preventDefault();
                var form = $(this);

                if (form.attr('id') == '' || form.attr('id') != 'fupload') {
                    $.ajax({
                        type: form.attr('method'),
                        url: form.attr('action'),
                        data: form.serialize()
                    }).success(function () {
                        setTimeout(function () {
                            parent.$.colorbox.close();
                        }, 10);
                    }).fail(function (jqXHR/*, textStatus, errorThrown*/) {
                        // Optionally alert the user of an error here...
                        var textResponse = jqXHR.responseText;
                        var alertText = "One of the following conditions is not met:\n\n";
                        var jsonResponse = jQuery.parseJSON(textResponse);

                        $.each(jsonResponse, function (n, elem) {
                            alertText = alertText + elem + "\n";
                        });
                        alert(alertText);
                    });
                }
                else {
                    var formData = new FormData(this);
                    $.ajax({
                        type: form.attr('method'),
                        url: form.attr('action'),
                        data: formData,
                        mimeType: "multipart/form-data",
                        contentType: false,
                        cache: false,
                        processData: false
                    }).success(function () {
                        setTimeout(function () {
                            parent.$.colorbox.close();
                        }, 10);

                    }).fail(function (jqXHR/*, textStatus, errorThrown*/) {
                        // Optionally alert the user of an error here...
                        var textResponse = jqXHR.responseText;
                        var alertText = "One of the following conditions is not met:\n\n";
                        var jsonResponse = jQuery.parseJSON(textResponse);

                        $.each(jsonResponse, function (n, elem) {
                            alertText = alertText + elem + "\n";
                        });

                        alert(alertText);
                    });
                }
            });

            $('.close_popup').click(function () {
                parent.$.colorbox.close();
            });
        });
        var modalTools = (function () {
                    return {
                        showPleaseWait: function() {
                            $('#pleaseWaitDialog').modal();
                        },
                        hidePleaseWait: function () {
                            $('#pleaseWaitDialog').modal('hide');
                        }
                    };
                })();
    </script>
    @yield('scripts')
</div>
<div class="modal" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-body">
        <div class="progress">
            <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar"
                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">Uploading file ...
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alertDialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Error</h4>
            </div>
            <div class="modal-body">
                <p>One fine body&hellip;</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>
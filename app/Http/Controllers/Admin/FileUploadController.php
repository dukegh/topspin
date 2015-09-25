<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Image;
use Config;
use Exception;
use Input;
use Log;
use Request;
use Response;

class FileUploadController extends AdminController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $destinationPath = $filename = "";
        try {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = Input::file('file');
            if ($file) {
                if ($file->getError() == 1)
                    throw new Exception(sprintf(
                        'The file "%s" exceeds your upload maximum filesize (limit is %d KiB).',
                        $file->getClientOriginalName(), $file->getMaxFilesize() / 1024));
                if ($file->getError()) throw new Exception("Uploaded file error: " . $file->getErrorMessage());
                $mimeType = $file->getMimeType();
                $filetype = Input::get('filetype');
                if (! in_array($filetype, ['image']))
                    throw new Exception("Not allowed type of uploaded file: $filetype");
                //$destinationPath = public_path() . '/appfiles/article/' . $article->id . '/';
                if ($filetype == 'image') {
                    $allowedMimeTypes = ['jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png'];
                    if (!in_array($mimeType, $allowedMimeTypes)) {
                        throw new Exception("Not allowed mime type of uploaded file: $mimeType");
                    }
                    $extension = array_search($mimeType, $allowedMimeTypes);
                    $image = new Image();
                    $image->extension = $extension;
                    $image->save();
                    $filename = $image->id . '.' . $extension;
                }
                if (Input::get('storage') == 'article') $destinationPath .= "/article/";
                $uid = intval(Input::get('uid'));
                if ($uid) $destinationPath .= "$uid/";
                if ($filename) $file->move(public_path() . '/appfiles' . $destinationPath, $filename);
            }
            if (! $filename) throw new Exception('No file uploaded');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 503);
        }
        return response()->json([
            'url' => '//' . Config::get('topspin.imageHost') . $destinationPath . $filename,
            'filename' => $filename
        ]);
    }

}
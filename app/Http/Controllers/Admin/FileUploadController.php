<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Image;
use Config;
use Exception;
use Input;
use Log;
use Response;

class FileUploadController extends AdminController
{
    protected $allowedFileTypes = ['image'];
    protected $allowedImageMimeTypes = ['jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png'];

    /**
     * Store a newly created resource in storage.
     *
     * @//param Request $request
     * @return Response
     */
    public function store(/*Request $request*/)
    {
        $destinationPath = $filename = "";
        try {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = Input::file('file');
            // Проверяем, была ли осуществелена попытка заливки файла
            if ($file) {
                // Был ли заливаемый файл больше максимального допустимого размера
                if ($file->getError() == 1)
                    throw new Exception(sprintf(
                        'The file "%s" exceeds your upload maximum filesize (limit is %d KiB).',
                        $file->getClientOriginalName(), $file->getMaxFilesize() / 1024));
                // Были ли другие ошибки при заливке файла
                if ($file->getError()) throw new Exception("Uploaded file error: " . $file->getErrorMessage());
                $mimeType = $file->getMimeType();
                // Проверяем по полю формы filetype тип заливаемого файла
                $filetype = Input::get('filetype');
                // Был ли указан недопустимый тип файла
                if (! in_array($filetype, $this->allowedFileTypes))
                    throw new Exception("Not allowed type of uploaded file: $filetype");
                // Если заливаемый файл - картинка
                if ($filetype == 'image') {
                    // Если залитый файл имеет неподдерживаемый Mime Type
                    if (! in_array($mimeType, $this->allowedImageMimeTypes)) {
                        throw new Exception("Not allowed mime type of uploaded file: $mimeType");
                    }
                    // Определяем расширение файла по его Mime Type
                    $extension = array_search($mimeType, $this->allowedImageMimeTypes);
                    // Определяем размер изображения
                    list($width, $height) = getimagesize(Input::file('file'));
                    $imageMinWidth = Config::get('topspin.imageMinWidth');
                    $imageMinHeight = Config::get('topspin.imageMinHeight');
                    // Если размер меньше минимального
                    if ($width < $imageMinWidth || $height < $imageMinHeight)
                        throw new Exception("Too small image size: {$width}x{$height}. " .
                            "You should upload image more then {$imageMinWidth}x{$imageMinHeight}");
                    // Сохраняем объект изображения в базу и получаем его id
                    $image = new Image();
                    $image->extension = $extension;
                    $image->save();
                    $filename = $image->id . '.' . $extension;
                }
                // Формируем путь для сохранения залитого файла на основе переменных формы storage и uid
                if (Input::get('storage') == 'article') $destinationPath = "/article/";
                $uid = intval(Input::get('uid'));
                if ($uid) $destinationPath .= "$uid/";
                if ($filename) $file->move(public_path() . '/appfiles' . $destinationPath, $filename);
            }
            if (! $filename) throw new Exception('No file uploaded');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 503);
        }
        // Возвращаем ссыку на залитый файл
        return response()->json([
            'url' => '//' . Config::get('topspin.imageHost') . $destinationPath . $filename,
            'filename' => $filename
        ]);
    }

}
<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait FileUploadTrait{
    public function uploadFile(Request $request, $fileInputName, $directory = 'uploads'){
        if($request->hasFile($fileInputName) && $request->file($fileInputName)->isValid()) {
            $file = $request->file($fileInputName);
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/' . $directory, $fileName);
            return $fileName;
        }

        return '';
    }

    public function deleteFile($filePath){
        if (Storage::exists('public/' . $filePath)) {
            return Storage::delete('public/' . $filePath);
        }
        return false;
    }
}
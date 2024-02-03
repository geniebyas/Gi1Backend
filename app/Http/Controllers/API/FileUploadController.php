<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function fromApi(Request $request){
        $name = $request->name;
        $file = $request->file("$name");
        $result = $file->store("uploads/image");
        return $result;
    }

}

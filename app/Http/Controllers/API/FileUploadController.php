<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function fromApi(Request $request) {
        $uid = $request->header('uid');
        $name = $request->name;
        $dir = $request->dir;
        if (!empty($request->file())) {
            $file = $request->file($name); // Get array of files

            if (is_null($file)) {
                $response = [
                    'message' => 'File not found',
                    'status' => 0,
                    'data'  => null,
                ];
                return response()->json($response, 404);
            }

            // Generate a unique filename based on the provided name and timestamp
            $filename = $name . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Store the file in the specified directory
            $result = $file->move(public_path() ."/uploads/$dir", $filename);
            $imageUrl = asset("public/uploads/$dir/$filename");


            $fDb = new File();
            $fDb->name = $name;
            $fDb->path = $imageUrl;
            $fDb->extension = $file->getClientOriginalExtension();
            $fDb->size = filesize($result);
            $fDb->type = $dir;
            $fDb->by = $uid;
        
            $fDb->save();

            // You can customize the response according to your needs
            $response = [
                'message' => 'File uploaded successfully',
                'status' => 1,
                'data' =>$fDb,
            ];

            return response()->json($response, 200);
        }else{
            $response = [
                'message' => 'File not found',
                'status' => 0,
                'data'  => null,
            ];
            return response()->json($response, 404);
        }
    }

    public function deleteFile($data) {
        $filename = $data['filename'];
        $dir = $data['dir'];
    
        // Get the file record from the database
        $file = File::where('name', $filename)->first();
    
        if (!$file) {
            // File not found in the database
            $response = [
                'message' => 'File not found',
                'status' => 0,
                'data'  => null,
            ];
            return response()->json($response, 404);
        }
    
        // Delete the file from the storage
        $filePath = public_path("uploads/$dir/$filename");
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }
    
        // Delete the file record from the database
        $file->delete();
    
        // Respond with success message
        $response = [
            'message' => 'File deleted successfully',
            'status' => 1,
            'data' => null,
        ];
    
        return response()->json($response, 200);
    }
    
}

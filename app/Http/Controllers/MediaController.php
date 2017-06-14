<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class MediaController extends Controller
{
    public function index()
    {
        $request = request();

        if(!in_array('create', $request->attributes->get('scope'))) {
            abort(401);
        }

        if(!$request->hasFile('file')){
            abort(400);
        }

        $file = $request->file('file');

        if(!$file->isValid()){
            abort(400);
        }

        $path = Storage::putFile('public', $file, 'public');
        
        //$path = $file->store('public');

        //$path = preg_replace('/^public/', 'storage/', $path);

        //$path = asset($path);
        $path = Storage::url($path);

        return response($path, 201)
            ->header('Location', $path);
    }


}

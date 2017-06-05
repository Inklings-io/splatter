<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class MediaController extends Controller
{
    public function index()
    {
        $request = request();

        if(!$request->attributes->get('user') == 'TODO' ){ //TODO
            abort(401);
        }

        if(!in_array('create', $request->attributes->get('scope')) {
            abort(401);
        }

        if(!$request->hasFile('file')){
            abort(400);
        }

        $file = $request->file('file');

        if(!$file->isValid()){
            abort(400);
        }

        $path = $file->store('uploads');

        //TODO does path need to be modified?

        return response($path)
            ->header('Location', $path);
    }


}

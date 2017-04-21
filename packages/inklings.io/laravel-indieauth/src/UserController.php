<?php
namespace Inklings.io\IndieAuth;
 
use App\Http\Controllers\Controller;
 
class UserController extends Controller
{
 
    public function index($url)
    {
        $url = trim($url);
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }
        echo $url;
    }

    public function standardize($url)
    {
        $url = trim($url);
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }
        echo $url;
    }
 
}

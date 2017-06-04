<?php
namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Log;

//require_once base_path('vendor/indieauth/client/src/IndieAuth/Client.php');
//
//require_once DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
//require_once DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
//require_once DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';

class MicropubController extends Controller
{
    public function get_index()
    {
        $request = request();

        if($request->input('q') == 'config'){
            $json = array();
            $json['media-endpoint'] = config('app.url') . '/api/media';
            return $json;

            //TODO query for source
        } else {

            return "<html><body>This is a Micropub Endpoint.  Normally no human should see this.  So if you are, something was done wrong.  See the spec for micropub at <a href='https://www.w3.org/TR/micropub'>w3.org/TR/micropub</a></body></html>";
            //$json['user'] = $request->attributes->get('user');
            //TODO: make a template
        }
    }
    public function post_index()
    {

    }
 




}

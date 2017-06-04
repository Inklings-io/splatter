<?php
namespace App\Http\Controllers;

use DB;
use App\Token;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

//require_once base_path('vendor/indieauth/client/src/IndieAuth/Client.php');
//
//require_once DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
//require_once DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
//require_once DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';

class TokenController extends Controller
{
    public function index(Request $request)
    {
        if (isset($request->input('code')) &&
            isset($request->input('me')) &&
            isset($request->input('redirect_uri'))) {
            $post_data = http_build_query(array(
                'code'          => $request->input('code'),
                'me'            => $request->input('me'),
                'redirect_uri'  => $request->input('redirect_uri'),
                'client_id'     => $request->input('client_id'),
                'state'         => $request->input('state')
            ));

            $auth_endpoint = \IndieAuth\Client::discoverAuthorizationEndpoint($request->input('me'));

            $ch = curl_init($auth_endpoint);

            if (!$ch) {
                $this->log->write('error with curl_init');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

            $response = curl_exec($ch);

            $results = array();
            parse_str($response, $results);

            if ($results['me']) {
                $user = $results['me'];
                $scope = $results['scope'];
                $client_id = $request->input('client_id');

                $token = new Token;

                $checksum = md5($user . $scope . $client_id . date('Y_z_H_i_s') . config('app.url') . config('app.key'));
                $token->checksum = $checksum;
                $token->scope = $scope;
                $token->client_id = $client_id;
                $token->user = $user;
                $token->last_used = Carbon::now();

                $token->save();

                $token_id = $token->id;

                $joined_token = $token_id . ',' . $token->checksum;


                return(http_build_query(array(
                    'access_token' => $joined_token,
                    'scope' => $scope,
                    'me' => $user)));
            } else {
                abort(400);
                //header('HTTP/1.1 400 Bad Request');
                //exit();
            }
        } else {
            abort(400);
            //header('HTTP/1.1 400 Bad Request');
            //exit();
        }
    }
 




}

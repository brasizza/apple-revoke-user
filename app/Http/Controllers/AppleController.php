<?php

namespace App\Http\Controllers;

use App\Helpers\GuzzleHelper;
use App\Traits\ApiResponser;
use Exception;
use Firebase\JWT\JWT;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class AppleController extends Controller
{

    use ApiResponser;
    protected $client;


    public function revoke(Request $request , $grant_type = 'authorization_code')
    {
        try {

            $this->client = $request->all();
            $jwt = $this->generateJWT();
            $jwtRevoke = $this->getRevokeJWT($grant_type);
            $url_revoke = 'https://appleid.apple.com/auth/revoke';
            $data = [

                'client_id' => env('APPLE_BUNDLE'),
                'client_secret' => $jwt,
                'token' => $jwtRevoke
            ];

             GuzzleHelper::makeRequest('POST',$url_revoke, $data);

            return $this->successResponse('Account revoked!');
        } catch (Exception $e) {

            return $this->errorResponse($e->getMessage(), 500);
            throw $e;
        }
    }

    private function getRevokeJWT($grant_type)
    {
        $url_apple = 'https://appleid.apple.com/auth/token';
        $jwt = $this->generateJWT();
        $dados = [
            'grant_type' => $grant_type,
            'client_id' => env('APPLE_BUNDLE'),
            'client_secret' => $jwt,
            'refresh_token' => $this->client['token'],
            'code' => $this->client['code'],
        ];

        try {
            $token =  json_decode(GuzzleHelper::makeRequest('POST',$url_apple, $dados), true);
        } catch (ClientException $e) {

            throw  new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
        return $token['access_token'];
    }

    private function generateJWT()
    {
        $payload = [
            "iss" => env('APPLE_TEAM_ID'),
            "iat" => time(),
            "exp" => time() + 3600,
            "aud" => env('APPLE_AUD_URL'),
            "sub" => env('APPLE_BUNDLE')
        ];
        $jwt = JWT::encode($payload, env('APPLE_KEY_VALUE'), 'ES256', env('APPLE_KID'));
        return $jwt;
    }
}

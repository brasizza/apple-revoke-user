<?php

namespace App\Helpers;


class GuzzleHelper
{

    private $timeout = 20;



    public static function makeRequestJson($req, $url, $json = "[]", $timeout = 10)
    {

        $client = new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/json'],
            'verify' => false

        ]);

        $response = $client->request('GET', $url, [
            'connect_timeout' => $timeout,
            'body' => $json
        ]);
        $retorno = $response->getBody()->getContents();
        return json_decode($retorno, true);
    }

    public static function makeRequest($req, $url, $body, $timeout = 10, $headers = null)
    {

        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'headers' => $headers
        ]);

        $reponse =  $client->request($req, $url, [
            'form_params' => $body,
            'connect_timeout' => $timeout,
        ]);
        $result = $reponse->getBody();
        return $result;
    }






    public static function makeRequestWithBearer($req, $url, $body, $bearer, $timeout = 10)
    {

        $client = new \GuzzleHttp\Client([
            'headers' => [

                'authorization' => $bearer,
                'Content-Type' => 'application/json'
            ]
        ]);
        $reponse =  $client->request($req, $url, [
            'form_params' => $body,
            'connect_timeout' => $timeout,
        ]);
        $result = $reponse->getBody()->getContents();
        return $result;
    }
}

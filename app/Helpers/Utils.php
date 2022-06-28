<?php

namespace App\Helpers;

use App\Models\Traducao;
use Google\Cloud\Translate\V2\TranslateClient;

class Utils
{

    static  $translate = null;

    public static function decrypt($password, $code)
    {
        $string =    GuzzleHelper::makePost(
            env('ENCRYPT_URL') . 'decrypt',

            [
                'text' => $code,
                'password' => base64_encode(str_pad($password, 32, 0, STR_PAD_LEFT))


            ]
        );


        return $string;
    }



    public static function encrypt($password, $code)
    {
        $string =    GuzzleHelper::makePost(
            env('ENCRYPT_URL') . 'encrypt',
            [
                'text' => $code,
                'password' => base64_encode(str_pad($password, 32, 0, STR_PAD_LEFT))


            ]
        );


        return $string;
    }


    public static function encryptKey( $code)
    {
        $string =    GuzzleHelper::makePost(
            env('ENCRYPT_URL') . 'encryptV2',
            [
                'text' => $code,

            ]
        );


        return $string;
    }


    public static function remove_emoji($string) {

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }


    public static function dateUS2BR($date){
        $tmp = explode("-",$date);
        return "{$tmp[2]}/{$tmp[1]}/{$tmp[0]}";
    }

    public static function dateBR2US($date){
        $tmp = explode("/",$date);
        return "{$tmp[2]}-{$tmp[0]}-{$tmp[1]}";
    }



    public static function generateQrCodeString($prefix, $string, $encrypt = null) {


        if ($encrypt != null) {


            $string =    GuzzleHelper::makePost(
                env('ENCRYPT_URL') . 'encrypt',

                [
                    'text' => $string,
                    'password' => base64_encode(str_pad($encrypt, 32, 0, STR_PAD_LEFT))


                    ]
                );
                $string = str_replace('/', '$', ($string));
            }

            return  "$prefix/$string";


            }



            public static function removerAcentos($texto){
                return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$texto);
                }

    public static function translate($text,$langTo){

        if($langTo == 'pt'){
               return ['text' =>$text];
        }

        // dd($langTo);
        if($langTo == 'en_'){
            $langTo = 'en_US';
        }
        // return ['text' =>$text];
        if(self::$translate == null){
            self::$translate  = new TranslateClient([
                 'key' => env('GOOGLE_KEY_TRANSLATE')
             ]);
         }
         $text = self::remove_emoji($text);
        $trans = Traducao::where('texto', $text)->where('lingua_traduzida', $langTo)->first();
        if($trans == null){
            $texto_traduzir = str_replace('&' , 'e' , $text);
            $texto_traduzir = str_replace('#' , '' , $texto_traduzir);

                  $url = "https://translation.googleapis.com/language/translate/v2?q={$texto_traduzir}&sourceLanguage=pt&target={$langTo}&key=".env('GOOGLE_KEY_TRANSLATE');
        $ret = GuzzleHelper::makeGetJson($url);
        $traduzido = json_decode($ret,true);
       if($traduzido){

        $traducao = new Traducao();
        $traducao['texto'] = $text;
        $traducao['lingua_traduzida'] = $langTo;
        $traducao['texto_traduzido'] = $traduzido['data']['translations'][0]['translatedText'];
        $traducao->save();
          // dd($traduzido['data']['translations'][0]['translatedText']);
        return ['text' =>$traduzido['data']['translations'][0]['translatedText']];
        }
    }else{
        return ['text' =>$trans['texto_traduzido']];

    }
        // return ['text' =>$text];

        // $result = self::$translate->translate($text, [
        //     'target' => $langTo
        // ]);
        // return $result;
    //     $url = "https://translation.googleapis.com/language/translate/v2?q={$text}&sourceLanguage=pt&target={$langTo}&key=".env('GOOGLE_KEY_TRANSLATE');
    //     $ret = GuzzleHelper::makeGetJson($url);
    //    return json_decode($ret,true);
    }


    public static function somenteNumero($str, $replace = "") {
        return preg_replace("/[^0-9]/", $replace, "$str");
    }

    public static function translateArray($text,$langTo){

        if(self::$translate == null){
           self::$translate  = new TranslateClient([
                'key' => env('GOOGLE_KEY_TRANSLATE')
            ]);
        }
        $result = self::$translate->translateBatch($text, [
            'target' => $langTo
        ]);
        return $result;
    //     $url = "https://translation.googleapis.com/language/translate/v2?q={$text}&sourceLanguage=pt&target={$langTo}&key=".env('GOOGLE_KEY_TRANSLATE');
    //     $ret = GuzzleHelper::makeGetJson($url);
    //    return json_decode($ret,true);
    }
}

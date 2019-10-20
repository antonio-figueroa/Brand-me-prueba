<?php

namespace App\Http\Controllers\Traits;

/*
        Trait que contiene la conexión con el api de facebook
*/

trait ConectionFacebookTrait {

    /*
        Función que obtiene el token a partir de los parámetros
    */

    public function getTokenUser($conectionUrl, $params)
    {
        // abrimos la sesión cURL
        $conection = curl_init();

        // definimos la URL a la que hacemos la petición
        curl_setopt($conection, CURLOPT_URL, $conectionUrl);

        // indicamos el tipo de petición: POST
        curl_setopt($conection, CURLOPT_POST, TRUE);

        // definimos los parámetros que tendrá
        curl_setopt($conection, CURLOPT_POSTFIELDS, http_build_query($params));

        // indicamos que queremos recibir respuesta
        curl_setopt($conection, CURLOPT_RETURNTRANSFER, true);

        $remote_server_response = curl_exec ($conection);

        // cerramos la sesión cURL
        curl_close ($conection);

        //Deodificamos el json
        $response = json_decode($remote_server_response);

        return $response;
    }

    /*
        Obtenemos la url que contiene la información del token
    */
    public function getInfoTokenUrl(){
        return 'https://graph.facebook.com/oauth/access_token_info?';
    }

    /*
        Obtenemos la url que refresca el token
    */
    public function getRefreshTokenUrl(){

        return 'https://graph.facebook.com/v4.0/oauth/access_token?';
    }

}
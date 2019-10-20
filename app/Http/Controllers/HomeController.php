<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ConectionFacebookTrait;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    use ConectionFacebookTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //Obtenemos el usuario autenticado
        $user = Auth::user();

        //Obtenemos la información del token
        $tokenExpires = $this->getTokenInfo();

        //Obtenemos el tiempo de expiración en segundos
        $seconds = $tokenExpires / 1000;

        //Lo pasamos a un timestamp para almacenarlo
        $expires_in = date('Y-m-d H:i:s', time() + $seconds);

        //Mandamos la variable para saber cuánto esperar 
        $sleep = strtotime($expires_in) - strtotime(date('Y-m-d H:i:s'));

        return view('home', compact('user', 'sleep'));
    }

    /*
        Función que regresa el tiempo de vida del token del usuario autenticado
    */
    public function getTokenInfo(){
        //Definimos el info
        $input['access_token'] = Auth::user()->token_access;

        //Obtenemos la url de info del token
        $infoUrl = $this->getInfoTokenUrl();

        //Obtenemos el token
        $response = $this->getTokenUser($infoUrl, $input);

        //Regresmos sólo el tiempo en el que expira
        return $response->expires_in;
    }

    /*
        Función que pide un token de larga duración para el usuario
    */
    public function getExtendedAccessToken(Request $request)
    {
        $user = Auth::user();

        //Armamos el query builder
        $input['grant_type'] = 'fb_exchange_token';
        $input['client_id'] = config('app.facebook_key');
        $input['client_secret'] = config('app.facebook_secret');
        $input['fb_exchange_token'] = $user->token_access;

        //Obtenemos la url de info del token
        $refreshUrl = $this->getRefreshTokenUrl();

        //Obtenemos el token actualizado
        $response = $this->getTokenUser($refreshUrl, $input);

        $user->token_access = $response->token;

        //Si se actualiza el usuario
        if($user->save()){
            //Mandamos correo
            $this->sendMail($user);
        }

        return response()->json($response, 200);
    }

    private function sendMail($user){
        $data['user'] = $user->toArray();
        Mail::send('emails.email', $data, function($message) {
            $message->to(config('app.admin_email'), 'Redes conectadas Brand Me')
                ->subject('Actualización de Token')
                ->from(config('app.username_mail'));
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Socialite;

use App\User;
use App\Http\Controllers\Traits\ConectionFacebookTrait;


class SocialController extends Controller
{
	use ConectionFacebookTrait;

    public function redirect($provider){
     	return Socialite::driver($provider)->redirect();
 	}

 	/*
		Llamada a la inicialización del api con Socialite
 	*/
	public function callback($provider)
	{
		//Obtenemos la información del uuario
	   $infoProvider = Socialite::driver($provider)->user(); 

	   //Creamos su cuenta n la base de datos
	   $user = $this->createUser($infoProvider, $provider); 

	   //Lo logueamos
	   auth()->login($user); 

	   //Lo redirigimos a la pantalla de inicio
	   return redirect()->action('HomeController@index');
	}

	/*
		Función que crea un usuario
	*/
 	private function createUser($info, $provider)
 	{
 		//Evaluamos si existe el usuario
		$user = User::where('provider_id', $info->id)->first();

		//Si el usuario existe, actualizamos el token y su tiempo de expiración
		if ($user){

			//Actualizamos directamente los datos
			$user->token_access = $info->token;
			$user->save();

		}else{ 

			//Si no existe lo creamos
			$user = User::createUser($info, $provider);
		}

		//Retornamos la instancia
		return $user;
	}
}

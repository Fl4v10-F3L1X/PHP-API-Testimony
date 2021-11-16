<?php

namespace App\Controller\Api;


use \App\Model\Entity\User;
use \Firebase\JWT\JWT;

class Auth extends Api{

    /**
     * Método responsável para gerar um Token JWT
     * @param Request $request
     * @return array
     */
    public static function generateToken($request){
        //POST VARS
        $postVars = $request->getPostVars();
        
        //VALIDA OS CAMPOS OBRIGATÒRIOS
        if(!isset($postVars['email']) or !isset($postVars['senha'])){
            throw new \Exception("OS CAMPOS EMAIL E SENHA SÃO OBRIGATORIOS",400);
        }

        //BUSCA USUÀRIO PELO EMAIL
        $obUser = User::getUserByEmail($postVars['email']);
        if(!$obUser instanceof User){
            throw new \Exception("O EMAIL OU SENHA SÃO INVALIDOS",400);
        }

        //VALIDA A SENHA DO USUÁRIO
        if(!password_verify($postVars['senha'],$obUser->senha)){
            throw new \Exception("O EMAIL OU SENHA SÃO INVALIDOS",400);
        }

        //PAYLOAD
        $payload = [
            'email' => $obUser->email
        ];
 
        //RETORNA O TOKEN GERADO
        return [
            'token' => JWT::encode($payload,getenv('JWT_KEY'))
        ];
    }
}
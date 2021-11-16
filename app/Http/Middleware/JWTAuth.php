<?php

namespace App\Http\Middleware;

use \App\Model\Entity\User;
use \Firebase\JWT\JWT;

class JWTAuth{

    /**
     * Método responsável por retornar uma instância de usuário autenticado
     * @param Request $request
     * @return User
     */
    private function getJWTAuthUser($request){
       //HEADERS
       $headers = $request->getHeaders();
    
       //TOKEN PURO EM JWT
       $jwt = isset($headers['Authorization']) ? str_replace('Bearer ','',$headers['Authorization']) : '';

       try {
           //DECODE
        $decode = (array)JWT::decode($jwt,getenv('JWT_KEY'),['HS256']);
        
       } catch (\Exception $e) {
           throw new \Exception("Token Inválido", 403);
           
       }
        
        //EMAIL
        $email = $decode['email'] ?? '';

       //BUSCA O USUÁRIO PELO E-MAIL
       $obUser = User::getUserByEmail($email);
       
       //RETORNA O USUÁRIO
       return $obUser instanceof User ? $obUser : false;

    }

    /**
     * Método responsável por validar o acesso via JWT AUTH
     * @param Request $request
     */
    private function auth($request){
        //VERIFICA O USUÁRIO RECEBIDO
        if($obUser = $this->getJWTAuthUser($request)){
            $request->user = $obUser;
            return true;
        }

        //EMITE O ERRO DE SENHA INVÁLIDA
        throw new \Exception("Acesso Negado", 403);
    }

    /**
     * Método responsável por executar as ações do middleware
     * @param Request $request
     * @param Closoure next
     * @return Response
     */
    public function handle($request, $next){
        //REALIZA A VALIDAÇAO DO ACESSO VIA JWT AUTH
        $this->auth($request);
        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }

}
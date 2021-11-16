<?php

namespace App\Session\Admin;

class Login{

    /**
     * Método responsável por iniciar a sessão 
     */
    private static function init(){
        //VERIFICA SE A SESSÂO NÂO ESTA ATIVA
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Método responsável por criar o login
     * @param User $obUser
     * @return boolean
     */
    public static function login($obUser){
        //INICIA SESSÃO
        self::init();

        //DEFINE A SESSÃO DO USUÁRIO
        $_SESSION['admin']['usuario'] = [
            'id'    => $obUser->id,
            'nome'  => $obUser->nome,
            'email' => $obUser->email
        ];

        //SUCESSO
        return true;
        echo "<pre>";
        print_r($obUser);
        echo "</pre>";exit;
    }

    /**
     * Método responsável por verificar se o usuário está logado
     * @return boolean
     */
    public static function isLogged(){
        //INICIA SESSÃO
        self::init();
        
        //RETORNA A VERIFICAÇÃO
        return isset($_SESSION['admin']['usuario']['id']);
    }

    /**
     * Método responsável por executar o logout do usuário
     * @return boolean
     */
    public static function logout(){
        //INICIA SESSÃO
        self::init();
        
        //DESLOGA O USUÀRIO
        unset($_SESSION['admin']['usuario']);

        //SUCESSO
        return true;
    }
}
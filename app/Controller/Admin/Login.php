<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page{

    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @return string $errorMessage
     * @return string
     */
    public static function getLogin($request,$errorMessage = null){
        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';
        
        //CONTEÙDO DA PÁGINA DE LOGIN
        $content = View::render('admin/login',[
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Login > WDEV',$content);
        
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setLogin($request){
        //POST VARS
        $postVars = $request->getPostVars();
        
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //BUSCA USUARIOS PELO EMAIL
        $obUser = User::getUserByEmail($email);

        if(!$obUser instanceof User){
            return self::getLogin($request,'E-mail ou senha inválidos');
        }

        //VERIFICA A SENHA DO USUÀRIO
        if(!password_verify($senha,$obUser->senha)){
            return self::getLogin($request,'E-mail ou senha inválidos');
        }

        //CRIA A SESSION LOGIN
        SessionAdminLogin::login($obUser);
        
        //REDERICIONA O USUARIO PARA A HOME DO ADMIN
        $request->getRouter()->redirect('/admin');
    }

    /**
     * Método responsável por deslogar o usuário
     * @param Request $request
     */
    public static function setLogout($request){
        
        //DESTROI A SESSION LOGIN
        SessionAdminLogin::logout();
        
        //REDERICIONA O USUARIO PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/admin/login');
    }
}
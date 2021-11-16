<?php

namespace App\Http\Middleware;

use \App\Session\Admin\Login as SessionAdminLogin;

class RequireAdminLogout{

       /**
     * Método responsável por executar as ações do middleware
     * @param Request $request
     * @param Closoure next
     * @return Response
     */
    public function handle($request, $next){
        //VERIFICA SE O USUARIO ESTÁ LOGADO
        if(SessionAdminLogin::isLogged()){
            $request->getRouter()->redirect('/admin');
        }

        //CONTINUA A EXECUÇÂO
        return $next($request);
    }
}
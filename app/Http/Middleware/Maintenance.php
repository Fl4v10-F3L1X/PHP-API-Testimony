<?php

namespace App\Http\Middleware;

class Maintenance{

    /**
     * Método responsável por executar as ações do middleware
     * @param Request $request
     * @param Closoure next
     * @return Response
     */
    public function handle($request, $next){
    //VERIFICA O ESTADO DE MANUTENÇÃO DA PÁGINA
    if(getenv('MAINTENANCE') == 'true'){
        throw new \Exception("Página em Manutenção. Tente novamente mais tarde.", 200);
        
    }
        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }

}
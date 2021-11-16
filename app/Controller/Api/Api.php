<?php

namespace App\Controller\Api;

/**
 * Método responsável por retornar os detalhes da API
 * @param Request $request
 * @return array
 */
class Api{
    public static function getDetails($request){
        return [
            'nome' => 'API - FPRO',
            'versao' => 'v1.0.0',
            'autor' => 'Flávio Fpro',
            'email' => 'flaviofrancisco1802@hotmail.com'
        ];
    }

    /**
     * Método responsável por retornar os detalhes da paginação
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    protected static function getPagination($request,$obPagination){
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();
        
        //PÀGINA
        $pages = $obPagination->getPages();
        
        //RETORNO DOS DADOS
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadePaginas' => !empty($pages) ? count($pages) : 1
        ];
    }
}
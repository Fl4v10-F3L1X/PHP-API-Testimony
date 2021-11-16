<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Page
{
    /**
     * Método responsável por renderizar o topo da pagina
     * @return string
     */
    private static function getHeader()
    {
        return View::render('pages/header');
    }

    /**
     * Método responsável por renderizar o topo da pagina
     * @return string
     */
    private static function getFooter()
    {
        return View::render('pages/footer');
    }

    /**
     * Método responsável por retornar o link da pagination
     * @param array $queryParams
     * @param array $page
     * @param string $url
     * @return
     */
    private static function getPaginationLink($queryParams,$page,$url,$label = null)
    {
        //ALTERA A PÁGINA
        $queryParams['page'] = $page['page'];

        //LINK
        $link = $url . '?' . http_build_query($queryParams);

        //VIEW 
        return View::render('pages/pagination/link', [
            'page' => $label ?? $page['page'],
            'link' => $link,
            'active' => $page['current'] ? 'active' : ''
        ]);
    }

    /**
     * Método responsável por renderizar o layout de paginação
     * @param Request $request
     * @param Pagination $pagination
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        //PAGINAS
        $pages = $obPagination->getPages();

        //VERIFICA A QUANTIDADE DE PÀGINAS
        if (count($pages) <= 1) return '';

        //LINKS
        $links = '';

        //URL ATUAL (SEM GETS)
        $url = $request->getRouter()->getCurrentUrl();

        //GET
        $queryParams = $request->getQueryParams();

        //PÁGINA ATUAL
        $currentPage = $queryParams['page'] ?? 1;

        //LIMITE DE PÁGINAS
        $limit = getenv('PAGINATION_LIMIT');
        
        //MEIO DA PAGINAçÃO
        $middle = ceil($limit/2);
        
        //INICIO DA PAGINAçÂO
        $start = $middle > $currentPage ? 0 : $currentPage - $middle;
       
        //AJUSTA O FINAL DA PAGINAÇÃO
        $limit = $limit + $start;

        //AJUSTA O INICIO DA PAGINAÇÃO
        if($limit > count($pages)){
            $diff = $limit - count($pages);
            $start = $start - $diff;
        }

        //LINK INICIAL
        if($start > 0){
            $links .= self::getPaginationLink($queryParams,reset($pages),$url,'<<');
        }

        //RENDERIZAR OS LINKS
        foreach ($pages as $page) {
            //Verifique o START DA PAGINAÇÃO
            if($page['page'] <= $start) continue;

            //VERIFICA O LIMITE DE PAGINAÇÂO
            if($page['page'] > $limit){
                $links .= self::getPaginationLink($queryParams,end($pages),$url,'>>');
                break;
            }
            $links .= self::getPaginationLink($queryParams,$page,$url);
        }

        //RENDERIZA BOX DE PAGINAÇÃO
        return View::render('pages/pagination/box', [
            'links' => $links
        ]);
    }


    /**
     * Método responsável por retornar o conteúdo (view) da nossa page
     * @return string 
     */
    public static function getPage($title, $content)
    {
        return View::render('pages/page', [
            'title' => $title,
            'header' => self::getHeader(),
            'content' => $content,
            'footer' => self::getFooter()
        ]);
    }
}

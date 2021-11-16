<?php

namespace App\Controller\Admin;

use \App\Utils\View;

class Home extends Page{

    /**
     * Método responsável por renderizar a view da home do painel
     * @param Request $request
     * @return string
     */
    public static function getHome($request){
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/home/index',[]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Home > Fpro', $content,'home');
    }
}
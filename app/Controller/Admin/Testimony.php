<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page{


        /**
         * Método responsável por obter a renderização dos itens de depoimentos para a página
         * @param Request $request
         * @param Pagination $obPagination
         * @return string 
         */
        private static function getTestimonyItems($request,&$obPagination){
            //DEPOIMENTOS
            $itens = '';
            
            //QUANTIDADE TOTAL DE REGISTRO
            $quantidadeTotal = EntityTestimony::getTestimonies(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
            
            //PÁGINA ATUAL
            $queryParams = $request->getQueryParams();
            $paginaAtual = $queryParams['page'] ?? 1;

            //INSTANCIA DE PAGINAÇÃO
            $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
            

            //RESULTADOS DA PÁGINA
            $results = EntityTestimony::getTestimonies(null,'id DESC', $obPagination->getLimit());

            //RENDERIZA O ITEM
            while($obTestimony = $results->fetchObject(EntityTestimony::class)){
                //VIEW DE DEPOIMENTOS
                $itens .= View::render('admin/modules/testimonies/item',[
                    'id' => $obTestimony->id,
                    'nome' => $obTestimony->nome,
                    'mensagem' => $obTestimony->mensagem,
                    'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
                ]);
            }

            //RETORNA OS DEPOIMENTOS
            return $itens;
        }

    /**
     * Método responsável por renderizar a view de listagem de depoimentos
     * @param Request $request
     * @return string
     */
    public static function getTestimonies($request){
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/testimonies/index',[
            'itens' => self::getTestimonyItems($request,$obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Depoimentos > Fpro', $content,'testimonies');
    }

    /**
     * Método responsável por retornar o formulário de cadstro de um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewTestimony($request){
              //CONTEUDO DO FORMULÁRIO
              $content = View::render('admin/modules/testimonies/form',[
                  'title' => 'Cadastrar Depoimento',
                  'nome' => '',
                  'mensagem' => '',
                  'status' => ''
            ]);
            //RETORNA A PÁGINA COMPLETA
            return parent::getPanel('Cadastrar Depoimento > Fpro', $content,'testimonies');
       
    }

      /**
     * Método responsável por retornar o formulário de criar um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function setNewTestimony($request){
        //POST VARS
        $postVars = $request->getPostVars();
       
        //NOVA INSTANCIA DE DEPOIMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'] ?? '';
        $obTestimony->mensagem = $postVars['mensagem'] ?? '';
        $obTestimony->cadastrar();
        
        //REDIRECIONA USUÀRIO
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=created');
    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request){
        //QUERYPARAMS
        $queryParams = $request->getQueryParams();
        
        //STATUS
        if(!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch ($queryParams['status']){
            case 'created':
                return Alert::getSuccess('Depoimento criado com sucesso!');
            break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
            break; 
            case 'deleted':
                return Alert::getSuccess('Depoimento Excluido com sucesso!');
            break;    
        }
    }

    /**
     * Método responsável por retornar o formulário de edição
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditTestimony($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //CONTEUDO DO FORMULÁRIO
        $content = View::render('admin/modules/testimonies/form',[
            'title' => 'Editar Depoimento',
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'status' => self::getStatus($request)
      ]);
      //RETORNA A PÁGINA COMPLETA
      return parent::getPanel('Editar Depoimento > Fpro', $content,'testimonies');
 
    }

    /**
     * Método responsável por retornar o formulário de edição
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditTestimony($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTANCIA
        $obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
        $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
        $obTestimony->atualizar();

        //REDIRECIONA USUÀRIO
        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteTestimony($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //CONTEUDO DO FORMULÁRIO
        $content = View::render('admin/modules/testimonies/delete',[

            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem
            
      ]);
      //RETORNA A PÁGINA COMPLETA
      return parent::getPanel('Excluir Depoimento > Fpro', $content,'testimonies');
 
    }

    /**
     * Método responsável por excluir o formulário de edição
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteTestimony($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);
        
        //VALIDA INSTANCIA
        if(!$obTestimony instanceof EntityTestimony){
            $request->getRouter()->redirect('/admin/testimonies');
        }
        
        //EXCLUI O DEPOIMENTO
        $obTestimony->excluir();
        
        //REDIRECIONA USUÀRIO
        $request->getRouter()->redirect('/admin/testimonies?status=deleted');
    }
}
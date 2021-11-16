<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page{


        /**
         * Método responsável por obter a renderização dos itens de usuários para a página
         * @param Request $request
         * @param Pagination $obPagination
         * @return string 
         */
        private static function getUserItems($request,&$obPagination){
            //Usuarios
            $itens = '';
            
            //QUANTIDADE TOTAL DE REGISTRO
            $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
            

            //PÁGINA ATUAL
            $queryParams = $request->getQueryParams();
            $paginaAtual = $queryParams['page'] ?? 1;

            //INSTANCIA DE PAGINAÇÃO
            $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
            

            //RESULTADOS DA PÁGINA
            $results = EntityUser::getUsers(null,'id DESC', $obPagination->getLimit());

            //RENDERIZA O ITEM
            while($obUser = $results->fetchObject(EntityUser::class)){

                //VIEW DE DEPOIMENTOS
                $itens .= View::render('admin/modules/users/item',[
                    'id' => $obUser->id,
                    'nome' => $obUser->nome,
                    'email' => $obUser->email
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
    public static function getUsers($request){
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/users/index',[
            'itens' => self::getUserItems($request,$obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Usuários > Fpro', $content,'users');
    }

    /**
     * Método responsável por retornar o formulário de cadstro de um novo usuário
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request){
              //CONTEUDO DO FORMULÁRIO
              $content = View::render('admin/modules/users/form',[
                  'title' => 'Cadastrar Usuário',
                  'nome' => '',
                  'email' => '',
                  'status' => self::getStatus($request)
            ]);
            //RETORNA A PÁGINA COMPLETA
            return parent::getPanel('Cadastrar Usuário > Fpro', $content,'users');
       
    }

      /**
     * Método responsável por cadastrar um novo usuário no banco
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request){
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÀRIO
        $obUser = EntityUser::getUserByEmail($email);
       
        if($obUser instanceof EntityUser){
             //REDIRECIONA USUÀRIO
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }
        
        //NOVA INSTANCIA DE USUÀRIO
        $obUser = new EntityUser;
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obUser->cadastrar();
        
        //REDIRECIONA USUÀRIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');
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
                return Alert::getSuccess('Usuário criado com sucesso!');
            break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
            break; 
            case 'deleted':
                return Alert::getSuccess('Usuário Excluido com sucesso!');
            break;  
            case 'duplicated':
                return Alert::getError('O email digitado já existe no banco!');
            break;   
        }
    }

    /**
     * Método responsável por retornar o formulário de edição
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);
        
        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //CONTEUDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form',[
            'title' => 'Editar Usuário',
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
      ]);
      //RETORNA A PÁGINA COMPLETA
      return parent::getPanel('Editar Usuário > Fpro', $content,'users');
 
    }

    /**
     * Método responsável por retornar o formulário de edição
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);
        
        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÀRIO
        $obUserEmail = EntityUser::getUserByEmail($email);
        if($obUserEmail instanceof EntityUser && $obUserEmail->id != $id){
            //REDIRECIONA USUÀRIO
           $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
       }

        //ATUALIZA A INSTANCIA
        $obUser->nome =$nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        //REDIRECIONA USUÀRIO
        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request,$id){
        //OBTÈM O DEPOIMENTO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);
        
        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }

        //CONTEUDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/delete',[

            'nome' => $obUser->nome,
            'email' => $obUser->email
            
      ]);
      //RETORNA A PÁGINA COMPLETA
      return parent::getPanel('Excluir Usuário > Fpro', $content,'users');
 
    }

    /**
     * Método responsável por excluir o formulário de edição
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request,$id){
        //OBTÈM O USUÀRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);
        
        //VALIDA INSTANCIA
        if(!$obUser instanceof EntityUser){
            $request->getRouter()->redirect('/admin/users');
        }
        
        //EXCLUI O USUÀRIO
        $obUser->excluir();
        
        //REDIRECIONA USUÀRIO
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}
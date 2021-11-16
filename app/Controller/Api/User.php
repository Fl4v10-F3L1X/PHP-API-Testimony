<?php

namespace App\Controller\Api;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;
        
class User extends Api{

    /**
         * Método responsável por obter a renderização dos itens de depoimentos para a página
         * @param Request $request
         * @param Pagination $obPagination
         * @return string 
         */
        private static function getUserItems($request,&$obPagination){
            //DEPOIMENTOS
            $itens = [];
            
            //QUANTIDADE TOTAL DE REGISTRO
            $quantidadeTotal = EntityUser::getUsers(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;
            
            //PÁGINA ATUAL
            $queryParams = $request->getQueryParams();
            $paginaAtual = $queryParams['page'] ?? 1;

            //INSTANCIA DE PAGINAÇÃO
            $obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);
            

            //RESULTADOS DA PÁGINA
            $results = EntityUser::getUsers(null,'id ASC', $obPagination->getLimit());

            //RENDERIZA O ITEM
            while($obUser = $results->fetchObject(EntityUser::class)){
  
                //VIEW DE User
                $itens[] = [
                    'id' => (int)$obUser->id,
                    'nome' => $obUser->nome,
                    'email' => $obUser->email
                   
                ];
            }

            //RETORNA OS USUáRIos
            return $itens;
        }
    /**
     * Método responsável por retornar os Usuários Cadastrados
     * @param Request $request
     * @return array
     */
    public static function getUsers($request){
        return [
            'usuarios' => self::getUserItems($request,$obPagination),
            'paginacao'   => parent::getPagination($request,$obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhe de um depoimento
     * @param Request $request
     * @param integer $id
     * @return array
     */
    public static function getUser($request,$id){
        //VALIDA O ID DO USUÁRIO
        if(!is_numeric($id)){
            throw new \Exception("O id '".$id."' não é valido", 400);
        }
        //BUSCA USUÁRIO
        $obUser = EntityUser::getUserById($id);
        
        //VALIDA SE O USUÁRIO EXISTE
        if(!$obUser instanceof EntityUser){
            throw new \Exception("O Usuário ".$id." não foi encontrado", 404);
        }

        //RETORNA OS DETALHES DO USUÁRIO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Método responsavel por retornar o usuário atual
     * @param Request $request
     * @return array
     */
    public static function getCurrentUser($request){
        //USUARIO ATUAL
        $obUser = $request->user;
        //RETORNA OS DETALHES DO USUÁRIO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Método Responsável por cadastrar um novo depoimento
     * @param Request $request
     */
    public static function setNewUser($request){
        //POST VARS
        $postVars = $request->getPostVars();
       
       //VALIDA OS CAMPOS OBRIGATÓRIOS
       if(!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])){
           throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);
       } 

       //VALIDA A DUPLICAçÂO DE USUARIOS
       $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
       if($obUserEmail instanceof EntityUser){
           throw new \Exception("O email '".$postVars['email']."' já está em uso", 400);
       }

       //NOVO DEPOIMENTO 
       $obUser = new EntityUser;
       $obUser->nome = $postVars['nome'];
       $obUser->email = $postVars['email'];
       $obUser->senha = password_hash($postVars['senha'],PASSWORD_DEFAULT);
       $obUser->cadastrar();

       //RETORNA OS DETALHES DOS USUARIO CADASTRADO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Método Responsável por atualizar um novo usuário
     * @param Request $request
     */
    public static function setEditUser($request,$id){
        //POST VARS
        $postVars = $request->getPostVars();
       
       //BUSCA O USUARIO
       $obUser = EntityUser::getUserById($id);

       //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            throw new \Exception("O Usuário ".$id." não foi encontrado", 404);
        }

        //VALIDA A DUPLICAçÂO DE USUARIOS
       $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
       if($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id){
           throw new \Exception("O email '".$postVars['email']."' já está em uso", 400);
       }

       //ATUALIZA O USUARIO      
       $obUser->nome = $postVars['nome'];
       $obUser->email = $postVars['email'];
       $obUser->senha = password_hash($postVars['senha'],PASSWORD_DEFAULT);
       $obUser->atualizar();

       //RETORNA OS DETALHES DOS USUARIO CADASTRADO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

     /**
     * Método Responsável por eliminar um novo usuário
     * @param Request $request
     */
    public static function setDeleteUser($request,$id){
      //BUSCA O USUARIO
       $obUser = EntityUser::getUserById($id);

       //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            throw new \Exception("O Usuário ".$id." não foi encontrado", 404);
        }

        //IMPEDE A EXCLUSÂO DO PROPRIO CADASTRO
        if($obUser->id == $request->user->id){
            throw new \Exception("Não é possível excluir o cadastro atualmente conectado", 400);
            
        }
        //EXCLUI O USUARIO
        $obUser->excluir();

       //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso' => true
        ];
    }
}
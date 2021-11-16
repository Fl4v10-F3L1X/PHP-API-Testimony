<?php

    namespace App\Controller\Pages;

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
            $obPagination = new Pagination($quantidadeTotal,$paginaAtual,3);

            //RESULTADOS DA PÁGINA
            $results = EntityTestimony::getTestimonies(null,'id DESC', $obPagination->getLimit());

            //RENDERIZA O ITEM
            while($obTestimony = $results->fetchObject(EntityTestimony::class)){
                //VIEW DE DEPOIMENTOS
                $itens .= View::render('pages/testimony/item',[
                    'nome' => $obTestimony->nome,
                    'mensagem' => $obTestimony->mensagem,
                    'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data))
                ]);
            }

            //RETORNA OS DEPOIMENTOS
            return $itens;
        }

        /**
         * Método responsável por retornar o conteúdo (view) de depoimentos
         * @param Request $request
         * @return string 
         */
        public static function getTestimonies($request){

            
            //VIEW DE DEPOIMENTOS
            $content = View::render('pages/testimonies',[
                'itens' =>self::getTestimonyItems($request,$obPagination),
                'pagination' => parent::getPagination($request,$obPagination)
            ]);

            //RETORNA A VIEW DA PAGINA
            return parent::getPage('DEPOIMENTOS > FPRO', $content);
        }

        /**
         * Método responsável por cadastrar um depoimento
         * @param Request $request
         * @return string
         */
        public static function insertTestimony($request){
            //DADOS DO POST
            $postVars = $request->getPostVars();

            //NOVA INSTANCIA DE DEPOIMENTO
            $obTestimony = new EntityTestimony;
            $obTestimony->nome = $postVars['nome'];
            $obTestimony->mensagem = $postVars['mensagem'];
            $obTestimony->cadastrar();

            //RETORNA A PÁGINA DE LISTAGEM DE DEPOIMENTOS
            return self::getTestimonies($request);
        }
    }


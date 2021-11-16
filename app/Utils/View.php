<?php

    namespace App\Utils;

class View{

    /**
     * Variaveis padrões da View
     * @var array
     */
    private static $vars = [];

    /**
     * Método responsável por definir os dados inicias
     * @param array $vars
     */
    public static function init($vars = []){
        self::$vars = $vars;
    }
        /**
         * Método responsável por retornar o conteudo de uma view  
         * @param string $view
         * @return string
         */
        private static function getContentView($view){
            $file = __DIR__.'/../../resources/view/'.$view.'.html';
            return file_exists($file) ? file_get_contents($file) : 'Erro ao carregar pagina html';
        }

        /**
         * Método responsavel por retornar o conteudo renderizado de uma view
         * @param string $view
         * @param array $vars (string/ numeric)
         * @return string
         */
        public static function render($view, $vars = []){
            //CONTEUDO DA VIEW
            $contentView = self::getContentView($view);

            //MERGE DE VARIAVEIS DA VIEW 
            $vars = array_merge(self::$vars,$vars);

            //CHAVES DO ARRAY DE VARIÁVEIS
            $keys = array_keys($vars);
            $keys = array_map(function($item){
                return '{{'.$item.'}}';
            },$keys);

            //RETORNA O CONTEUDO RENDERIZADOS
            return str_replace($keys,array_values($vars), $contentView);
        }
    }

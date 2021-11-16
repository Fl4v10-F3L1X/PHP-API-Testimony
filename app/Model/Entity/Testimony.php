<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Testimony{

    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário que fez o depoimento
     * @var string 
     */
    public $nome;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $mensagem;

    /**
     * Data de publicação do depoimento
     */
    public $data;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');
        
        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('depoimentos'))->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);

        //SUCESSO
        return true;
    }

        /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function atualizar(){
                
        //ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('depoimentos'))->update('id = '.$this->id,[
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

           /**
     * Método responsável por excluir um depoimento no banco de dados
     * @return boolean
     */
    public function excluir(){
                
        //EXCLUI O DEPOIMENTO DO BANCO DE DADOS
        return (new Database('depoimentos'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar um depoimento com base no seu ID
     * @param integer $id
     * @return Testimony
     */
    public static function getTestimonyById($id){
        return self::getTestimonies('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar Depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('depoimentos'))->select($where,$order,$limit,$fields);
    }
}
<?php

namespace App\Utils\Cache;

class File{

    /**
     * Método responsável por retornar o caminho até o arquivo de cache
     * @param string $hash
     * @return string
     */
    private static function getFilePath($hash){
        //DIRETORIO DE CACHE
        $dir = getenv('CACHE_DIR');
        
        //VERIFICA A EXISTÊNCIA DO DIRETORIO
        if(!file_exists($dir)){
            mkdir($dir,0755,true);
        }

        //REOTORNA O CAMINHO ATÈ O ARQUIVO
        return $dir.'/'.$hash;
    }

    /**
     * Método responsável por guardar informação no cache
     * @param string $hash
     * @param mixed $content
     * @return boolean
     */
    private static function storageCache($hash,$content){
        //SERIALIZA O RETORNO 
        $serialize = serialize($content);
        
        //OBTEM O CAMINHO ATÈ O ARQUIVO DE CACHE
        $cacheFile = self::getFilePath($hash);
        
        //GRAVA AS INFORMAÇÔES NO ARQUIVO
        return file_put_contents($cacheFile,$serialize);
    }

    /**
     * Método responsável por retornar o conteúdo gravado no cache
     * @param string $hash
     * @param integer $expiration
     * @return mixed
     */
    private static function getContentCache($hash,$expiration){
        //OBTEM O CAMINHO DO ARQUIVO
        $cacheFile = self::getFilePath($hash);
        
        //VERIFICA A EXISTÊNCIA DO ARQUIVO
        if(!file_exists($cacheFile)){
            return false;
        }

        //VALIDA A EXPIRAÇÂO DO CACHE
        $createTime = filectime($cacheFile);
        $diffTime = time() - $createTime;
        if($diffTime > $expiration){
            return false;
        }

        //RETORNA O DADO REAL
        $serialize = file_get_contents($cacheFile);
        return unserialize($serialize);
    }

    /**
     * Método responsável por obter uma informação do cache
     * @param string $hash
     * @param integer $expiration
     * @param Closure $function
     * @return mixed
     */
    public static function getCache($hash,$expiration,$function){
        //VERIFICA O CONTEUDO GRAVADO
        if($content = self::getContentCache($hash,$expiration)){
            return $content;
        }

        //EXECUÇÂO DA FUNÇÂO
        $content = $function();

        //GRAVA O RETORNO NO CACHE
        self::storageCache($hash,$content);
        //RETORNA O CONTEÙDO
        return $content;
    }
}
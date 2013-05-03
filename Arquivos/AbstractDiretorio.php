<?php

namespace App\Cp\Edi\Remessa;

/**
 * Classe abstrata para manipulação do diretório
 */
class AbstractDiretorio
{

    /**
     * Coleção de arquivos e opções que não podem ser apagados
     * @var array 
     */
    private $camposProtegidos = array('.', '..');

    /**
     * Endereço do diretório
     * @var string 
     */
    private $diretorio;

    /**
     * Definir lista de arquivos protegidos para não serem excluídos
     * @param string $arquivoProtegidos
     */
    public function setArquivoProtegido($arquivoProtegidos)
    {
        // Verificar nome do arquivo
        if(!is_string($arquivoProtegidos))
        {
            throw new Exception('Nome inválido para o arquivo protegido!');
        }
        
        $this->camposProtegidos[] = $arquivoProtegidos;
    }

    

    /**
     * Obter endereço do diretório
     * @return string
     */
    public function getDiretorio()
    {
        return $this->diretorio;
    }
    /**
     * Definir endereço do diretório
     * @param string $diretorio Endereço do diretório
     * @return string
     * @throws Exception Caso o endereço não seja um diretório
     */
    public function setDiretorio($diretorio)
    {
        // Verificar se o endereço é um diretório
        if(!is_string($diretorio) || !is_dir($diretorio))
        {
            throw new Exception('O endereço não é diretório');
        }
        
        $this->diretorio = $diretorio;
    }

    /**
     * Ler todos arquivos TXT do diretorio
     * @param string $diretorio Diretório a ser lido
     * @return boolean
     */
    public function lerArquivosDoDiretorio($diretorio)
    {
        try
        {
            $arquivos = array();

            // Verifica se o endereço é um diretório
            if(is_dir($diretorio))
            {
                // Abre o diretório para manipulação
                if($handle = opendir($diretorio))
                {
                    // Lendo os arquivos do diretório
                    while(($file = readdir($handle)) !== false)
                    {
                        // Obter extensão do arquivo
                        $extensao = pathinfo($file, PATHINFO_EXTENSION);

                        // Se for arquivo de texto, inserir na lista
                        if($extensao == 'txt')
                        {
                            $arquivos[] = $file;
                        }
                    }
                }
            }
        } catch(Exception $e)
        {
            throw new Exception('Erro ao ler diretório de arquivo ' . $diretorio);
        }
    }

    /**
     * Apaga todos os arquivos do diretório
     * @param type $dir
     * @return boolean
     */
    public function apagarArquivosDoDiretorio($dir)
    {
        try
        {
            // Verifica se o endereço é um diretório
            if(is_dir($dir))
            {
                // Abre o diretório para manipulação
                if($handle = opendir($dir))
                {
                    // Lendo os arquivos do diretório
                    while(($file = readdir($handle)) !== false)
                    {
                        // Se o arquivo não estiver na lista de arquivos protegidos
                        if(!in_array($file, $this->camposProtegidos))
                        {
                            // Verifica se é arquivo
                            if(is_file($dir . $file))
                            {
                                // Apagar arquivo
                                unlink($dir . $file);                            
                            }
                        }
                    }
                }
            }

            return true;
        } catch(\Exception $e)
        {
            return false;
        }
    }

}
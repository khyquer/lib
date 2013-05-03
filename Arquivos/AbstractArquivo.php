<?php

namespace App\Cp\Edi\Remessa;

/**
 * Classe abstrata para controle da remessa de texto
 */
class AbstractArquivo
{

    /**
     * Nome do arquivo da remessa
     * @var string $nomeArquivo
     */
    private $nomeArquivo;

    /**
     * Valor base para geração do nome
     * @var string 
     */
    private $baseNome;
    
    /**
     * Diretório de manipulação do arquivo
     * @var string 
     */
    private $diretorio;

    /**
     * Construtor
     * 
     * @param string $baseNome Base para nome do arquivo
     */
    function __construct($baseNome = '%s')
    {
        // Verificar base do nome
        if(!is_null($baseNome))
        {
            $this->setBaseNome($baseNome);
        }
        
        // Definir diretório padrão
        $this->setDiretorio(APPLICATION_PATH . '/../public/edi/');
    }

    /**
     * Obter base para nome do arquivo
     * @return string
     */
    public function getBaseNome()
    {
        return $this->baseNome;
    }

    /**
     * Obter nome do arquivo inserido na base para nome
     * @return string
     */
    public function getNomeArquivo()
    {
        return @sprintf($this->getBaseNome(), $this->nomeArquivo);
    }

    /**
     * Obter diretório do sistema para salvar/ler arquivos
     * @return string
     */
    public function getDiretorio()
    {
        return $this->diretorio;
    }

    /**
     * Obter endereço do arquivo para salvar/ler
     * @return string
     */
    public function getUrl()
    {
        return $this->getDiretorio() . $this->getNomeArquivo() . '.txt';
    }

    /**
     * Método para salvar texto no arquivo
     * @param string $remessa String da remessa para salvar no arquivo
     * @param string $nomeArquivo Nome do arquivo de texto
     */
    public function salvar($remessa, $nomeArquivo)
    {
        try
        {
            // Definir nome do arquivo
            $this->setNomeArquivo($nomeArquivo);

            // Verifica se já existe um arquivo para aquela remessa
            $url = $this->getUrl();
            if(@file_exists($url) === true)
            {
                throw new \Exception("Arquivo " . $this->getNomeArquivo() . " já existe em $url!");
            }

            // Criar arquivo no endereço abaixo
            $fp = @fopen($url, 'w+');
            @fwrite($fp, $remessa);
            @fclose($fp);

            return true;
        } catch(\Exception $e)
        {
            throw new \Exception("Erro ao salvar arquivo ." . $this->getNomeArquivo() . "! " . $e->getMessage());
        }
    }

    /**
     * Método para ler arquivo existente no diretório
     * @param string $nomeArquivo
     * @return array $remessa Linhas do registro
     * @throws \Exception Caso o arquivo não seja encontrado
     * @throws \Exception Caso ocorra algum erro durante a leitura do arquivo
     */
    public function ler($nomeArquivo)
    {
        try
        {
            $this->setNomeArquivo($nomeArquivo);

            // Verifica se o arquivo existe
            $url = $this->getUrl();
            if(!@file_exists($url))
            {
                throw new \Exception("Arquivo não encontrado em $url!");
            }

            // abre arquivo para leitura
            $fp = @fopen($url, 'r');

            // lê o arquivo até chegar ao final
            while(!@feof($fp))
            {

                // lê linha atual do arquivo
                $l = @fgets($fp);

                // Verifica se não é uma linha vazia
                if(!empty($l))
                {
                    // Obtem tipo de linha (3 primeiros caracteres)
                    $idTipoRegistro = substr($l, 0, 3);
                    
                    // Adicionar linha a remessa, com respectivo tipo
                    $remessa[] = array(
                        'strOutput' => str_replace(PHP_EOL, '', $l),
                        'idTipoRegistro' => $idTipoRegistro
                    );
                }
            }

            // fecha arquivo
            @fclose($fp);

            return $remessa;
        } catch(\Exception $e)
        {
            throw new \Exception("Erro na leitura do arquivo " . $this->getNomeArquivo() . " em " . $this->getUrl() . "!");
        }
    }

    /**
     * Excluir arquivos gerados pelo sistema
     * @param string $nomeArquivo Nome do arquivo a ser excluído
     * @throws \Exception Caso o arquivo não seja encontrado
     * @return boolean (true/false) Indicando a conclusão ou não da ação
     */
    public function removerArquivo($nomeArquivo)
    {
        try
        {
            $this->setNomeArquivo($nomeArquivo);

            // Verifica se o arquivo existe
            $url = $this->getUrl();
            if(!@file_exists($url))
            {
                throw new \Exception("Arquivo não encontrado em $url!");
            }

            // Remover arquivo
            if(!@unlink($url))
            {
                throw new \Exception("Não foi possível remover o arquivo " . $this->getNomeArquivo() . " em " . $this->getUrl() . "!");
            }

            return true;
        } catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Mover arquivo de diretório
     * @param string $nomeArquivo Nome do arquivo para localizá-lo no diretório padrão
     * @param string $diretorioDestino Novo diretório ou subdiretório
     * @return boolean 
     * @throws \Exception Caso não seja encontrado o arquivo no diretório
     * @throws \Exception Caso exista outro arquivo com o mesmo nome no local de destino
     * @throws \Exception Caso não seja possível movê-lo
     */
    public function moverArquivo($nomeArquivo, $diretorioDestino)
    {
        try
        {
            // Definir novo nome
            $this->setNomeArquivo($nomeArquivo);

            // Verifica se o arquivo existe
            $url = $this->getUrl();
            if(!@file_exists($url))
            {
                throw new \Exception('Arquivo não encontrado em ' . $url . '!');
            }

            // Se o endereço de destino não for um diretório
            if(!is_dir($diretorioDestino))
            {
                // Montar subdiretório
                $diretorioDestino = $this->getDiretorio() . $diretorioDestino;
            }
            
            // Definir novo diretório
            $this->setDiretorio($diretorioDestino);
            
            // Obter nova url
            $novaUrl = $this->getUrl();

            // Verifica existe o mesmo nome de arquivo no local de destino
            if(@file_exists($novaUrl))
            {
                throw new \Exception('Existe outro arquivo com o mesmo nome no local de destino (' . $novaUrl . '!');
            }

            // Mover arquivo renomeando-o
            if(!rename($url, $novaUrl))
            {
                throw new \Exception('Não foi possível mover o arquivo ' . $this->getNomeArquivo() . ' em ' . $this->getUrl() . '!');
            }

            return true;
        } catch(\Exception $e)
        {
            throw $e;
        }
    }
    
    /**
     * Definir valor do nome do arquivo
     * @param string $nomeArquivo
     */
    public function setNomeArquivo($nomeArquivo)
    {
        // Verificar nome do arquivo
        if(!is_string($nomeArquivo) && !is_numeric($nomeArquivo))
        {
            throw new \Exception('Nome do arquivo inválido!');
        }

        $this->nomeArquivo = $nomeArquivo;
    }

    /**
     * Definir o base para o nome do arquivo
     * @param string $baseNome
     * @throws \Exception Caso o valor base não contenha uma string de formatação
     */
    public function setBaseNome($baseNome)
    {
        // Verificar nome do arquivo
        if(!is_string($baseNome) || strpos($baseNome, '%s') === false)
        {
            throw new \Exception('A base para o nome precisa de uma string de formatação (%s)!');
        }

        $this->baseNome = $baseNome;
    }
    
    /**
     * Definir diretório do(s) arquivo(s)
     * @param string $diretorio
     * @throws \Exception Caso o diretório não seja válido
     */
    public function setDiretorio($diretorio)
    {
        
        
        // Validar diretório
        if(!is_string($diretorio))
        {
            throw new \Exception('Valor inválido para o diretório!');
        }
        if(substr($diretorio, -1) !== '/')
        {
            $diretorio .= '/';
        }
        if(!is_dir($diretorio))
        {
            throw new \Exception('Endereço ' . $diretorio . ' não é um diretório!');
        }
        
        $this->diretorio = $diretorio;
    }

}

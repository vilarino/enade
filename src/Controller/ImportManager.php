<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 19/02/17
 * Time: 17:25
 */

namespace Controller;


use Doctrine\Common\Collections\ArrayCollection;

class ImportManager
{
    private $app;
    private $csv;
    private $xls;


    public function __construct($app)
    {
        $this->app = $app;
        $this->csv = new CSV();
        $this->xls = new XLS();

        set_time_limit(480);
    }

    /**
     * Cria uma tabela no banco de dados de acordo com a planilha do ENADE
     */
    public function import()
    {
        try {

            $this->app['db']->beginTransaction();

            if (!$this->importColumns()) {
                return "Falha ao importar colunas!";
            }

            if (!$this->importData()) {
                return "Falha ao importar colunas!";
            }

            $this->app['db']->commit();

            return "Arquivo importado com sucesso!";

        } catch (\Exception $exc) {
            $this->app['db']->rollback();

            return $exc->getMessage();
        }
    }

    /**
     * Importa as colunas do arquivo CSV do enade. <br />
     * As colunas do banco de dados terão o mesmo nome das colunas do arquivo CSV
     */
    private function importColumns()
    {
        $columns = $this->csv->getColumns();
        return $this->app['mysql.controller']->createTable('enade', $columns);
    }

    /**
     * Importa os dados para o banco de dados
     * Obs: Insere em blocos para melhorar o desempenho
     *
     * @return bool
     */
    private function importData()
    {
        $columns = $this->csv->getColumns();
        $data = $this->csv->getData();

        $bloc = [];

        $dataCollection = new ArrayCollection($data);

        $quantity = $dataCollection->count();

        for ($i = 0; $i < $quantity; $i++) {
            $bloc[] = $data[$i];

            if ($i % 50 == 0 || $data[$i] == $dataCollection->last()) {
                $response = $this->app['mysql.controller']->insertData($columns, $bloc);
                if (!$response) {
                    return false;
                }
                $bloc = [];
            }
        }

        return true;
    }

    public function importCategory()
    {
        //$file = fopen('/home/rafael/Projetos/enade/web/files/Dicionário de Variáveis.xls', 'r');
    }
}
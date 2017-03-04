<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 01/03/17
 * Time: 20:28
 */

namespace Controller;


class Load
{
    private $colunsImport;

    public function __construct($app)
    {
        $this->app = $app;
        $this->colunsImport = array(
            'nt_ce',
            'co_regiao_curso'
        );

        set_time_limit(960);
    }

    public function run()
    {
        try {
            $this->app['dbs']['enade_dw']->beginTransaction();

            $data = $this->getData();
            $msg = $this->insertDW($data);

            $this->app['dbs']['enade_dw']->commit();
            var_dump($msg);
            exit;

        } catch (\Exception $exc) {
            $this->app['dbs']['enade_dw']->rollback();
            var_dump($exc);
            exit;
        }
    }

    public function getData()
    {
        $strColuns = implode(",", $this->colunsImport);

        if (empty($strColuns)) {
            throw new \Exception("Por favor, especifique as colunas que deseja fazer a carga.");
        }

        $query = "SELECT {$strColuns} FROM enade where tp_pres = 555 and tp_pr_ger = 555 ";

        return $this->app['mysql.controller']->select($query);
    }

    private function insertDW($data)
    {
        $qb = $this->app['dbs']['enade_dw']->createQueryBuilder();

        foreach ($data as $item) {
            $qb->insert('DIM_NOTA')
                ->values(
                    array(
                        'valor' => empty($item['nt_ce']) ? 0 : $item['nt_ce']
                    )
                );

            if (!$qb->execute()) {
                throw new \Exception("Falha ao inserir registro na DIM_NOTA.");
            }

            $id = $this->app['dbs']['enade_dw']->lastInsertId();

            $qb->insert('FATO_DESENPENHO')
                ->values(
                    array(
                        'id_dim_nota' => $id,
                        'id_dim_regiao' => $item['co_regiao_curso'],
                        'nota' => empty($item['nt_ce']) ? 0 : $item['nt_ce']
                    )
                );

            if (!$qb->execute()) {
                throw new \Exception("Falha ao inserir na registros na tabela FATO_DESEMPENHO.");
            }
        }

        return 'registros carregados com sucesso';
    }

}
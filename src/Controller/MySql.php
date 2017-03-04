<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 19/02/17
 * Time: 17:48
 */

namespace Controller;


class MySql
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Cria uma tabela no banco de dados de acordo com os parametros
     *
     * @param $name nome da tabela
     * @param array $columns as colunas do arquivo CSV
     * @return bool
     */
    public function createTable($name, $columns = array())
    {
        if ($this->tableExists($name)) {
            return true;
        }

        $columnsCommand = $this->getColumnsCommand($columns);

        $sql = " CREATE TABLE {$name} ( ";

        $sqlArray = [];
        foreach ($columnsCommand as $column) {
            $sqlArray[] = str_replace("\"", "", $column['name'] . " " . $column['command']);
        }

        $sql .= implode(", ", $sqlArray) . " ) ";

        return $this->exec($sql);
    }

    private function getColumnsCommand($columns)
    {
        $columnsCommand = [];

        $columnsCommand[] = [
            'name' => 'id',
            'command' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY'
        ];

        foreach ($columns as $column) {
            $columnsCommand[] = [
                'name' => $column,
                'command' => 'VARCHAR(100) NULL'
            ];
        }

        return $columnsCommand;
    }

    private function tableExists($name)
    {
        $sql = "SHOW TABLES WHERE Tables_in_enade = '{$name}'";
        $response = $this->app['dbs']['enade']->fetchAll($sql);

        return !empty($response);
    }

    private function exec($sql)
    {

        try {
            $stmt = $this->app['dbs']['enade']->prepare($sql);

            if ($stmt->execute()) {
                return true;
            }

            return false;

        } catch (\Exception $exc) {
            echo $exc->getMessage();
            exit;
        }
    }

    /**
     * Insere os dados do arquivo csv no banco de dados
     *
     * @param $columns
     * @param $data
     * @return bool
     */
    public function insertData($columns, $data)
    {
        $sql = $this->createQuery($columns, $data);

        return $this->exec($sql);
    }

    private function createQuery($columns, $data)
    {
        $strColumns = str_replace("\"", "", implode(",", $columns));

        $sql = "";

        foreach ($data as $item) {
            $sql .= "INSERT INTO enade ({$strColumns}) VALUES ({$item});";
        }

        return $sql;
    }

    public function select($query)
    {
        $sql = $query;
        $items = $this->app['dbs']['enade']->fetchAll($sql);
        return $items;
    }
}
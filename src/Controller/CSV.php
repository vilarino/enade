<?php

namespace Controller;


/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 19/02/17
 * Time: 16:08
 */
class CSV
{

    private $fileName;
    private $filePath;
    private $file;
    private $columns;

    public function __construct()
    {
        $this->fileName = 'microdados_enade_2014.csv';
        $this->filePath = '/home/rafael/Projetos/enade/web/files/' . $this->fileName;
        $this->columms = array();
        $this->file = null;
    }

    /**
     * Retorna um array da primeira linha de um arquivo csv (colunas)
     * @return array
     * @throws \Exception
     */
    public function getColumns()
    {

        if (!empty($this->columns)) {
            return $this->columns;
        }

        $this->openFile();

        if ($lineColumns = fgetcsv($this->file)) {

            $this->columns = explode(";", $lineColumns[0]); // está pegando $lineColumns[0] por que as colunas estão dentro de um array

            return $this->columns;
        }

        throw new \Exception("Falha ao buscar colunas!");
    }

    public function openFile()
    {
        if (!($this->file = fopen($this->filePath, "r"))) {
            throw new \Exception("Falha ao tentar abrir aquivo!");
        }
    }

    /**
     * Retorna array com as linhas também no formato de array
     */
    public function getData()
    {
        if (is_null($this->file)) {
            $this->openFile();
        }

        $lines = [];
        while ($line = $this->nextLine()) {
            $lines[] = $line;
        }

        $this->closeFile();
        return $lines;
    }

    private function nextLine()
    {
        if (feof($this->file)) {
            return false;
        }

        $line = fgetcsv($this->file);

        if (!isset($line[0])) {
            return false;
        }

        $arrayLine = explode(";", $line[0]);

        $this->replaceValues($arrayLine);

        return implode(",", $arrayLine);
    }

    private function replaceValues(&$arrayLine)
    {
        foreach ($arrayLine as &$item) {
            if (empty($item) and $item !== 0 and $item !== "0") {
                $item = 'NULL';
            }
        }
    }

    private function closeFile()
    {
        fclose($this->file);
    }

}
<?php

class Masker
{
    private $cliOptions;
    private $fieldsToBeMasked = [];
    private $db;

    public function __construct($cliOptions)
    {
        $this->cliOptions = $cliOptions;
        $this->dbConnect();
        $this->loadFieldsFromFile();
    }

    private function dbConnect()
    {
        $this->db = new Database($this->cliOptions);
        $this->db->connect();
    }

    private function loadFieldsFromFile()
    {
        $fieldsFilePath = $this->cliOptions['f'];
        if (!is_file($fieldsFilePath))
        {
            echo "ERROR: -f option is missing or the given option is not a valid filename\n\n";
            printHelp();
            exit();
        }

        $this->fieldsToBeMasked = file($fieldsFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function getFieldsToBeMasked()
    {
        return $this->fieldsToBeMasked;
    }

    public function getRules()
    {
        $tables = $this->db->getTables();
        foreach ($tables as $tableName => $tableFields)
        {
            $table = new Table($this, $tableName, $tableFields);
            $rule = $table->getRule();
            if (!empty($rule))
            {
                echo $rule . "\n";
            }
        }

        echo "load mysql query rules to runtime;\n";
    }

    public function getDbName()
    {
        return $this->cliOptions['d'];
    }

    public function getProxySqlUser()
    {
        $proxySqlUser = $this->cliOptions['U'];
        if (empty($proxySqlUser))
        {
            echo "ERROR: -U option is missing\n\n";
            printHelp();
            exit();
        }

        return $proxySqlUser;
    }

}

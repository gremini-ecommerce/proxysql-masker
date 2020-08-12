<?php

class Masker
{
    private $cliOptions;
    private $fieldsToBeMasked = [];
    private $customMasks = [];
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

        foreach ($this->fieldsToBeMasked as &$field)
        {
            $fieldWithRule = explode(':', $field);
            if (count($fieldWithRule) > 1)
            {
                $field = $fieldWithRule[0];
                $this->customMasks[$field] = $fieldWithRule[1];
            }
        }

    }

    public function getFieldsToBeMasked()
    {
        return $this->fieldsToBeMasked;
    }

    public function getCustomMasks()
    {
        return $this->customMasks;
    }

    public function getRules()
    {
        $databases = [];
        $databases[] = $this->cliOptions['d'];

        if (empty($this->cliOptions['d']))
        {
            $databases = $this->db->getDatabases();
        }

        foreach ($databases as $dbName)
        {
            $tables = $this->db->getTables($dbName);
            foreach ($tables as $table)
            {
                $rule = $table->getRule($this);
                if (!empty($rule))
                {
                    echo $rule . "\n";
                }
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

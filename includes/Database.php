<?php

class Database
{
    private $pdo;

    public function __construct($cliOptions)
    {
        $this->cliOptions = $cliOptions;
    }

    public function connect()
    {
        $dsn = $this->getDsn();

        try
        {
            $this->pdo = new PDO($dsn, $this->cliOptions['u'], $this->cliOptions['p']);
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n\n";
            printHelp();
            exit();
        }

    }

    private function getDsn()
    {
        $mappings = [
            'd' => 'dbname',
            'h' => 'host',
            'P' => 'port',
            's' => 'unix_socket'
        ];

        $dsn = [
            'host' => '127.0.0.1',
        ];

        foreach ($mappings as $optKey => $dsnKey)
        {
            if (!empty($this->cliOptions[$optKey]))
            {
                $dsn[$dsnKey] = $this->cliOptions[$optKey];
            }
        }

        $dsnString = 'mysql:';

        foreach ($dsn as $key => $value)
        {
            $dsnString .= $key . '=' . $value . ';';
        }

        return $dsnString;
    }

    public function getTables()
    {
        $tables = [];

        foreach ($this->pdo->query('SHOW TABLES') as $row)
        {
            $tableName = $row[0];
            $tables[$tableName] = [];
            foreach ($this->pdo->query('DESCRIBE `' . $tableName . '`') as $column)
            {
                $tables[$tableName][] = $column['Field'];
            }
        }

        return $tables;
    }

}

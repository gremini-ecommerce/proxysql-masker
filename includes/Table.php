<?php

class Table
{
    private $dbName;
    private $tableName;
    private $tableFields;

    public function __construct($dbName, $tableName, $tableFields)
    {
        $this->dbName = $dbName;
        $this->tableName = $tableName;
        $this->tableFields = $tableFields;
    }

    public function getRule(Masker $masker)
    {
        $customMasks = $masker->getCustomMasks();
        $fieldsToBeMasked = $masker->getFieldsToBeMasked();

        $fieldsNeedMasking = [];

        foreach ($this->tableFields as $fieldName)
        {
            if (in_array($fieldName, $fieldsToBeMasked))
            {
                $fieldsNeedMasking[] = $fieldName;
            }
        }

        if (empty($fieldsNeedMasking))
        {
            return '';
        }

        $tableMask = $this->getMask($fieldsNeedMasking, $customMasks);
        $proxySqlUser = $masker->getProxySqlUser();

        $rule = 'INSERT INTO mysql_query_rules (username, schemaname, match_pattern, replace_pattern, re_modifiers, active) ';
        $rule .= 'VALUES ("' . $proxySqlUser . '", "' . $this->dbName . '", "\* FROM `' . $this->tableName . '`", "' . $tableMask . '", "caseless,global", 1);';

        return $rule;
    }

    private function getMask($fieldsNeedMasking, $customMasks)
    {
        $masks = [];

        foreach ($fieldsNeedMasking as $fieldName)
        {
            $masks[$fieldName] = $this->getMaskForField($fieldName, $customMasks);
        }

        $tableFields = $this->tableFields;

        foreach ($tableFields as &$fieldName)
        {
            if (!empty($masks[$fieldName]))
            {
                $fieldName = $masks[$fieldName];
            }
        }

        return implode(', ', $tableFields) . ' FROM `' . $this->tableName . '`';
    }

    private function getMaskForField($fieldName, $customMasks)
    {
        if (!empty($customMasks[$fieldName]))
        {
            return $customMasks[$fieldName];
        }

        return 'CONCAT(LEFT(`' . $fieldName . '`,2), REPEAT(\'x\',LENGTH(`' . $fieldName . '`)-4), RIGHT(`' . $fieldName . '`, 2)) as `' . $fieldName . '`';
    }

}

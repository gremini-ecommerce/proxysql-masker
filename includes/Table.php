<?php

class Table
{
    private $masker;
    private $tableName;
    private $tableFields;
    private $customMasks;

    public function __construct(Masker $masker, $tableName, $tableFields)
    {
        $this->masker = $masker;
        $this->tableName = $tableName;
        $this->tableFields = $tableFields;
        $this->customMasks = $masker->getCustomMasks();
    }

    public function getRule()
    {
        $fieldsToBeMasked = $this->masker->getFieldsToBeMasked();

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

        $tableMask = $this->getMask($fieldsNeedMasking);
        $proxySqlUser = $this->masker->getProxySqlUser();

        $rule = 'INSERT INTO mysql_query_rules (username, schemaname, match_pattern, replace_pattern, re_modifiers, active) ';
        $rule .= 'VALUES ("' . $proxySqlUser . '", "' . $this->masker->getDbName() . '", "\* FROM `' . $this->tableName . '`", "' . $tableMask . '", "caseless,global", 1);';

        return $rule;
    }

    private function getMask($fieldsNeedMasking)
    {
        $masks = [];

        foreach ($fieldsNeedMasking as $fieldName)
        {
            $masks[$fieldName] = $this->getMaskForField($fieldName);
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

    private function getMaskForField($fieldName)
    {
        if (!empty($this->customMasks[$fieldName]))
        {
            return $this->customMasks[$fieldName];
        }
        
        return 'CONCAT(LEFT(`' . $fieldName . '`,2), REPEAT(\'x\',LENGTH(`' . $fieldName . '`)-4), RIGHT(`' . $fieldName . '`, 2)) as `' . $fieldName . '`';
    }

}

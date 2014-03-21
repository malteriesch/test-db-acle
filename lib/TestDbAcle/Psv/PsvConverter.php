<?php
namespace TestDbAcle\Psv;

class PsvConverter
{

    protected function preFormat($toFormat,$excludedKeys)
    {
        foreach ($toFormat as $index => $rowToFormat) {
            foreach ($rowToFormat as $key => $value) {
                if (is_null($value)) {
                    $toFormat[$index][$key] = "NULL";
                }
                $toFormat[$index][$key] = str_replace("\n", "", $toFormat[$index][$key]);
                $toFormat[$index][$key] = str_replace("\r", "", $toFormat[$index][$key]);
                
                if (in_array($key, $excludedKeys)) {
                    unset($toFormat[$index][$key]);
                }
            }
        }
        return $toFormat;
    }
    function format($toFormat, $excludedKeys = array())
    {
        
        if (count($toFormat) == 0) {
            return "";
        }
        
        $toFormat = $this->preFormat($toFormat, $excludedKeys);

        $keyNamesInRow    = array_keys($toFormat);
        $fieldNames     = array_keys($toFormat[$keyNamesInRow[0]]);
        $maxColumnLengths = array();

        foreach ($fieldNames as $key => $field) {
            if (!isset($maxColumnLengths[$field]) || $maxColumnLengths[$field] < strlen($field))
                $maxColumnLengths[$field] = strlen($field);
        }

        foreach ($toFormat as $rowToFormat) {
            foreach ($rowToFormat as $key => $columnValue) {
                if (!isset($maxColumnLengths[$key]) || $maxColumnLengths[$key] < strlen($columnValue))
                    $maxColumnLengths[$key] = strlen($columnValue);
            }
        }

        foreach ($fieldNames as $key => $field) {
            $fieldNames[$key] = str_pad($field, $maxColumnLengths[$field] + 3);
        }

        $sOut = array();
        $sOut[] = implode("|", $this->trimLastColumn($fieldNames));

        foreach ($toFormat as $rowToFormat) {
            foreach ($rowToFormat as $key => $columnValue) {
                $rowToFormat[$key] = str_pad($columnValue, $maxColumnLengths[$key] + 3);
            }
            $sOut[].= implode("|", $this->trimLastColumn($rowToFormat));
        }
        return trim(implode("\n",$sOut));
    }
    
    protected function trimLastColumn($aRow)
    {
        $keys = array_keys($aRow);
        $lastKey = $keys[count($keys)-1];
        $aRow[$lastKey] = trim($aRow[$lastKey]);
        return $aRow;
    }

}

?>
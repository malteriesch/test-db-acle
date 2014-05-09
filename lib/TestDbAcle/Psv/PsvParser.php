<?php

namespace TestDbAcle\Psv;

class PsvParser implements PsvParserInterface
{
    const SYMBOL_PIPE                      = '|';
    const SYMBOL_COMMENT                   = '#';
    const SYMBOL_OPEN_TABLE_DEFINITION     = '[';
    const SYMBOL_CLOSE_TABLE_DEFINITION    = ']';
    const SYMBOL_OUTER_PARAMETER_SEPERATOR = ';';
    const SYMBOL_INNER_PARAMETER_SEPERATOR = ',';
    const SYMBOL_ASSIGNMENT                = ':';
    
    /**
     *
     * Parses a psv tree such that a structure such as
     *     [expression1|mode:replace;identifiedBy:first_name,last_name]
     *             id  |first_name   |last_name
     *             10  |john         |miller
     *             20  |stu          |Smith
     * 
     *             [expression2]
     *             col1  |col2    |col3
     *             1     |moo     |foo
     *             30    |miaow   |boo 
     *              
     * Into following array
     * 
     *     array("expression1" => array(
     *              'meta' => array(
     *                  'mode'=> 'replace',
     *                   'identifiedBy' => array('first_name','last_name')
     *              ),
     *              'data' => array(
     *                  array("id" => "10",
     *                      "first_name" => "john",
     *                      "last_name" => "miller"),
     *                  array("id" => "20",
     *                      "first_name" => "stu",
     *                      "last_name" => "Smith"))
     *          ),
     *          "expression2" => array(
     *              'meta' => array(),
     *              'data' => array(
     *                  array("col1" => "1",
     *                      "col2" => "moo",
     *                      "col3" => "foo"),
     *                  array("col1" => "30",
     *                      "col2" => "miaow",
     *                      "col3" => "boo"))),
     *          'empty' => array('meta'=>array(),'data'=>array())
     *      )
     * 
     * @param string $psvContent the content to be parsed
     * @return \TestDbAcle\Psv\Table\PsvTree the parsed content
     */
    public function parsePsvTree($psvContent)
    {
        $parsedTree           = array();
        $parsedTree = new \TestDbAcle\Psv\Table\PsvTree();
        $contentSplitByOpeningBracket = preg_split('/\n\s*(?<!\\\\)\[/', $psvContent);

        foreach ($contentSplitByOpeningBracket as $startOfTableContent) {

            if (trim($startOfTableContent) === '') {
                continue;
            }

            list($actualContentForTable, $expression) = $this->extractExpressionAndContent($startOfTableContent);
            list($tableName, $meta)                   = $this->parseIntoTableAndMeta($expression);

            $parsedTree->addTable(new Table\Table($tableName, $this->parsePsv($actualContentForTable), new Table\Meta($meta)));
        }
        return $parsedTree;
    }
    
    

    /**
     *
     * parses a single bit of Psv content such that
     * id  |first_name                        |last_name
     * 10  |john                              |miller
     * #lines starting with # are ignored
     * 20  |stu  #and comments can be inline  |Smith
     *
     * gets parsed into
     *
     * array(
     *    array( "id" => "10",
     *           "first_name" => "john",
     *           "last_name" => "miller" ),
     *    array( "id" => "20",
     *           "first_name" => "stu",
     *           "last_name" => "Smith" ) )
     *
     * @param string $psvTableContent the content to be parsed
     * @return the parsed content
     */
    public function parsePsv($psvTableContent)
    {

        $psvRows       = $this->psvToArray($psvTableContent);
        $psvHeaderLine = $this->extractHeaders($psvRows);
        $headers       = $this->splitByPipe($psvHeaderLine);

        $contentTable = array();

        foreach ($psvRows as $psvRow) {
            if ($this->skipRow($psvRow)) {
                continue;
            }

            $currentRowPsvFields     = $this->splitByPipe($psvRow);
            $psvRowFilteredValueList = array();

            foreach ($headers as $columnIndex => $psvColumnHeader) {
                if ($this->isCommented($psvColumnHeader)) {
                    continue;
                }
                $psvRowFilteredValueList[$psvColumnHeader] = $this->filterColumnValue($currentRowPsvFields[$columnIndex]);
            }

            $contentTable[] = $psvRowFilteredValueList;
        }
        return $contentTable;
    }
    
    
    protected function extractHeaders(&$psvRows)
    {
        return array_shift($psvRows);
    }
    
    protected function psvToArray($psvContent)
    {
        return explode("\n", trim($psvContent));
    }
    
    protected function skipRow($row){
        $trimmedRow = ltrim($row);
        return !$trimmedRow || $trimmedRow[0] == static::SYMBOL_COMMENT;
    }
    
    protected function splitByPipe($row){
        return $this->trimArrayElements(preg_split('/(?<!\\\\)'.preg_quote(static::SYMBOL_PIPE).'/', $row));
    }
    
    protected function isCommented($subject){
        return strpos($subject, static::SYMBOL_COMMENT) === 0;
    }
    
    protected function trimArrayElements(array $row)
    {
        foreach ($row as $index => $column) {
            $row[$index] = trim($column);
        }
        return $row;
    }
    
    protected function filterColumnValue($value)
    {
        $self = $this;//we need to to do this for PHP 5.3 compatability
        $filters = array(
            'stripComments' => function(&$value) use($self){
                if(strpos($value, $self::SYMBOL_COMMENT) !== false){
                    list($valuePart,) = preg_split('/(?<!\\\\)'.$self::SYMBOL_COMMENT.'/', $value);
                    $value = trim($valuePart);
                }
            },
            'convertNulls' => function(&$value){
                if($value == 'NULL'){
                    $value = null;
                }
            },
            'replaceEscapedCharacters' => function(&$value){
                if (!is_null($value)) {
                    $value = str_replace('\[','[',$value);
                    $value = str_replace('\]',']',$value);
                    $value = str_replace('\#','#',$value);
                    $value = str_replace('\|','|',$value);
                }
            }
        );
        
        foreach($filters as $filter){
            $filter($value);
        }
        
        return $value;
    }
    
    protected function extractExpressionAndContent($startOfTableContent)
    {
        $startOfContentSplitByClosingBracklet = preg_split('/(?<!\\\\)'.preg_quote(static::SYMBOL_CLOSE_TABLE_DEFINITION).'/', $startOfTableContent);
        return array($startOfContentSplitByClosingBracklet[1], ltrim($startOfContentSplitByClosingBracklet[0], static::SYMBOL_OPEN_TABLE_DEFINITION . ' '));
    }

    protected function parseIntoTableAndMeta($expression)
    {
        if (strpos($expression, static::SYMBOL_PIPE) !== false) {
            $meta = array();
            list($tableName, $parametersExpression) = explode(static::SYMBOL_PIPE, $expression);
            foreach (explode(static::SYMBOL_OUTER_PARAMETER_SEPERATOR, $parametersExpression) as $parameterExpression) {
                list($key, $value) = explode(static::SYMBOL_ASSIGNMENT, $parameterExpression);
                if (strpos($value, static::SYMBOL_INNER_PARAMETER_SEPERATOR) !== false) {
                    $value = explode(static::SYMBOL_INNER_PARAMETER_SEPERATOR, $value);
                }
                $meta[$key] = $value;
            }
            return array($tableName, $meta);
        } else {
            return array($expression, array());
        }
    }
    
}

?>
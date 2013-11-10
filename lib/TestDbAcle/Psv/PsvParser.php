<?php
namespace TestDbAcle\Psv;

class PsvParser {

    protected function _trimArrayElements( array $row )
    {
        foreach( $row as $index => $column ) {
            $row[ $index ] = trim( $column );
        }
        return $row;
    }
    protected function _filterColumnValue( $value ) {
        if ( $value == 'NULL' ) {
        	return null;
        }
        return $value;
    }

    /**
     *
     * Parses a psv tree such that a structure such as
     * [expression1]
     * id  |first_name   |last_name
     * 10  |john         |miller
     * 20  |stu          |Smith
     *
     * [expression2]
     * col1  |col2    |col3
     * 1     |moo     |foo
     * 30    |miaow     |boo
     *
     * gets parsed into
     * array( "expression1" => array(
     *            array( "id" => "10",
     *                   "first_name" => "john",
     *                   "last_name" => "miller" ),
     *            array( "id" => "20",
     *                   "first_name" => "stu",
     *                   "last_name" => "Smith" ) ),
     *
     *        "expression2" => array(
     *            array( "col1" => "1",
     *                   "col2" => "moo",
     *                   "col3" => "foo" ),
     *            array( "col1" => "30",
     *                   "col2" => "miaow",
     *                   "col3" => "boo" ) ) )
     * @param string $psvContent the content to be parsed
     * @return array the parsed content
     */
	public function parsePsvTree( $psvContent )
	{
		$parsedTree           = array();
        $contentSplitByTables = preg_split( "/\n\s*\[/", $psvContent );

        foreach ( $contentSplitByTables as $tableContentPartStart ){

            if ( trim($tableContentPartStart) === '' ) {
            	continue;
            }

            $contentSplitByTableDelimiter = explode( "]", $tableContentPartStart );
            $expression                    = trim( str_replace( '[', '', $contentSplitByTableDelimiter[0] ) );
            
            list($tableName,$meta) = $this->parseIntoTableAndMeta($expression);
            
            $actualContentForTable        = $contentSplitByTableDelimiter[1];

            $parsedTree[ $tableName ]     = array('meta'=>$meta,"data"=>$this->parsePsv( $actualContentForTable ));

        }
        return $parsedTree;
	}
        
    protected function parseIntoTableAndMeta($expression)
    {
        if (strpos($expression,"|")!==false){
            $meta = array();
            list($tableName,$parametersExpression) = explode("|",$expression);
            foreach(explode(";",$parametersExpression) as $parameterExpression){
                list($key,$value)=explode(":",$parameterExpression);
                if(strpos($value,",")!==false){
                    $value= explode(",",$value);
                }
                $meta[$key]=$value;
            }
            return array($tableName,$meta);
        }else{
            return array($expression,array());
        }
    }
    /**
     *
     * parses a single bit of Psv content such that
     * id  |first_name   |last_name
     * 10  |john         |miller
     * #lines starting with # are ignored
     * 20  |stu          |Smith
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
	public function parsePsv( $psvTableContent ) 
    {

		$psvRowList    = explode( "\n", trim( $psvTableContent ));
		$psvHeaderLine = array_shift ( $psvRowList );
		$headers       = $this->_trimArrayElements( explode( "|", $psvHeaderLine ) );

        $contentTable  = array();

        foreach( $psvRowList as $psvRow ) {
            $trimmedRow = ltrim($psvRow);
            if ($trimmedRow[0]=="#") {
                continue;
            }
            
            $currentRowPsvFields     = $this->_trimArrayElements( explode( "|", $psvRow ) );
            $psvRowFilteredValueList = array();

            foreach ( $headers as $columnIndex =>$psvColumnHeader ) {

                if (strpos( $psvColumnHeader ,'#')===0) {
                	continue;
                }

                $psvRowFilteredValueList[ $psvColumnHeader ] = $this->_filterColumnValue( $currentRowPsvFields[$columnIndex] );
            }

            $contentTable[] = $psvRowFilteredValueList;
        }
        return $contentTable;
	}

}
?>
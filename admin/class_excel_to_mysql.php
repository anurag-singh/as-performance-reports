<?php
/** Error reporting */
global $wpdb;
$wpdb->show_errors();
// For WordPress Multisite, you must define the DIEONDBERROR constant for database errors to display
define( 'DIEONDBERROR', true );
/** Error reporting */

date_default_timezone_set('Asia/Dili');

class Excel2Mysql extends Custom_Filter_For_Excel
{
    public $conn;
    private $excelSheetDataArray;
    private $dbColumns = array('stockID', 'stockName', 'action', 'entryDate', 'entryPrice', 'targetPrice', 'stopLoss', 'exitDate', 'exitPrice');
    private $postType = 'performance_report';
    private $table;

    function __construct() {
        global $wpdb;
        $table = $wpdb->prefix . "report_performance";
        $charset_collate = $wpdb->get_charset_collate();
        $createTable = "CREATE TABLE IF NOT EXISTS $table (
                ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                stockID VARCHAR(12) NOT NULL,
                stockName VARCHAR(50) NOT NULL,
                action VARCHAR(12) NOT NULL,
                entryDate VARCHAR(12) NOT NULL,
                exitDate VARCHAR(12) NOT NULL,
                entryPrice VARCHAR(12) NOT NULL,
                exitPrice VARCHAR(12) NOT NULL,
                targetPrice VARCHAR(12) NOT NULL,
                stopLoss VARCHAR(12) NOT NULL,
                callStartDate TIMESTAMP
            )$charset_collate;";

        // Check if database is created
        if($wpdb->query($createTable) === FALSE)
        {
            echo $wpdb->print_error();
        }
    }

    public function fetch_records_from_excel($sheetname, $inputFileName)
    {
        $inputFileType = 'Excel2007';
        $dbColumns = $this->dbColumns;  // Get value in $dbColumns variable


        /**  Create an Instance of our Read Filter, passing in the cell range  **/
        $filterSubset = new Custom_Filter_For_Excel(2,50,range('A','I'));

        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setLoadSheetsOnly($sheetname);
        $objReader->setReadFilter($filterSubset);


        // if excel file is added then
        if (!empty($inputFileName))
        {
            $objPHPExcel = $objReader->load($inputFileName);
            $excelSheetDataArray = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            //echo '<pre>';
            //print_r($excelSheetDataArray);
            //echo '</pre>';

             $i = 0;
            foreach ($excelSheetDataArray as $excelRow) {
                //print_r($excelRow);
                $excelSheetValues = array_values($excelRow);
                //print_r($excelSheetValues);

                if(!empty ($excelRow['A'])) {
                    $excelKeysValues = array_combine($dbColumns, $excelSheetValues);
                    //echo '<pre>';
                    //print_r($excelKeysValues);
                    //echo '</pre>';
                    $excelRowSanitizedArray[$i] = $excelKeysValues;
                    $i++;
                }
            }
            return $excelRowSanitizedArray;
        }
    }


    /*  Get all records form table  */
    public function fetch_records_from_db()
    {
        global $wpdb;
        $table = $wpdb->prefix . "report_performance";
        $dbColumns = $this->dbColumns;
        $columns = implode(', ', $dbColumns);
        $selectDbRecords = "SELECT $columns
                                FROM $table";

        $getAllRecordsFromDB = $wpdb->get_results($selectDbRecords);

        if ($getAllRecordsFromDB === FALSE)
        {
            echo $wpdb->print_error();
        }
        else
        {
            if(!empty($getAllRecordsFromDB)) {
                return $getAllRecordsFromDB;
            }
            else
            {
                echo "<br>Database is empty.";
            }

        }

    }


    public function get_duplicate_records_from_db($sheetData)
    {
        global $wpdb;
        $table = $wpdb->prefix . "report_performance";
        $dbColumns = $this->dbColumns;

        $colNames = implode(', ', $dbColumns);

        if(isset($sheetData)) {


            foreach ($sheetData as $excelrow) {


                //echo $excelrow['stockId'];
//                echo '<hr>Excel Records - <pre>';
//                print_r($excelrow);
//                echo '</pre>';


                $selectDuplicateRecordsFromDB = "SELECT $colNames FROM $table WHERE stockID = '".$excelrow['stockID']."'";

                $duplicateRowsFromDB = $wpdb->get_row($selectDuplicateRecordsFromDB, ARRAY_A);

                // If query not run successfully
                if($duplicateRowsFromDB === FALSE)
                {
                    echo $wpdb->print_error();
                }
                else
                {

                    if(!empty($duplicateRowsFromDB)) {



                        $dataToUpdateInDB = array_diff_assoc($excelrow, $duplicateRowsFromDB);

                       if(!empty ($dataToUpdateInDB)) {
                            echo '<hr><hr>Excel - <pre>';
                            print_r($excelrow);
                            echo '<pre>';

                            echo 'Duplicate DB Records - <pre>';
                            print_r($duplicateRowsFromDB);
                            echo '<pre>';

                            echo '<hr>diff_assoc array - <pre>';
                            print_r($dataToUpdateInDB);
                            echo '<pre>';

                           $column = $excelrow['stockID'];

                           $where = array('stockID' => $duplicateRowsFromDB['stockID']);

                           $update = $wpdb->update( $table, $dataToUpdateInDB, $where, $format = null, $where_format = null );
                       }
                    }
                    else
                    {

                        $colVals = implode(' ,' , array_values($excelrow));

                        $result = $wpdb->insert($table, $excelrow, array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )  );


                            if (isset($result))
                            {
                                echo "record inserted";
                            }
                            else {
                                echo "NO new records";
                                echo $wpdb->print_error();
                                //echo '<br><b style =color:#f00;>Error - </b>' . $conn->error;
                            }

                    }

                }

        }

        }
    }

}

    /**
     * Create a new table class that will extend the WP_List_Table
     * Ref - http://www.paulund.co.uk/wordpress-tables-using-wp_list_table
     */
    class Example_List_Table extends WP_List_Table
    {
        private $dbColumns = array('stockID', 'stockName', 'action', 'entryDate', 'entryPrice', 'targetPrice', 'stopLoss', 'exitDate', 'exitPrice');


        /**
         * Prepare the items for the table to process
         *
         * @return Void
         */
        public function prepare_items()
        {
            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();

            $data = $this->table_data();

            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $data;
        }

        /**
         * Override the parent columns method. Defines the columns to use in your listing table
         *
         * @return Array
         */
        public function get_columns()
        {
            $dbColumns = $this->dbColumns;
            $tableColumnsName = array('Stock ID', 'Stock Name', 'Action', 'Entry Date', 'Entry Price', 'Target Price', 'Stop Loss', 'Exit Date', 'Exit Price');

            $columns = array_combine($dbColumns, $tableColumnsName);

            return $columns;
        }

        /**
         * Define which columns are hidden
         *
         * @return Array
         */
        public function get_hidden_columns()
        {
            return array('stockID', 'stopLoss');
        }

        /**
         * Define the sortable columns
         *
         * @return Array
         */
        public function get_sortable_columns()
        {
            return array('title' => array('title', false));
        }

        /**
         * Get the table data
         *
         * @return Array
         */
        private function table_data()
        {
            global $wpdb;
            $table = $wpdb->prefix . "report_performance";
            $dbColumns = $this->dbColumns;
            $columns = implode(', ', $dbColumns);
            $selectDbRecords = "SELECT $columns
                                    FROM $table";

            $data = $wpdb->get_results($selectDbRecords, ARRAY_A);


            return $data;




//            $data = array();
//
//            $data[] = array(
//                        'id'          => 1,
//                        'title'       => 'The Shawshank Redemption',
//                        'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
//                        'year'        => '1994',
//                        'director'    => 'Frank Darabont',
//                        'rating'      => '9.3'
//                        );
//
//            $data[] = array(
//                        'id'          => 2,
//                        'title'       => 'The Godfather',
//                        'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
//                        'year'        => '1972',
//                        'director'    => 'Francis Ford Coppola',
//                        'rating'      => '9.2'
//                        );
//
//            $data[] = array(
//                        'id'          => 3,
//                        'title'       => 'The Godfather: Part II',
//                        'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
//                        'year'        => '1974',
//                        'director'    => 'Francis Ford Coppola',
//                        'rating'      => '9.0'
//                        );
//
//            $data[] = array(
//                        'id'          => 4,
//                        'title'       => 'Pulp Fiction',
//                        'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
//                        'year'        => '1994',
//                        'director'    => 'Quentin Tarantino',
//                        'rating'      => '9.0'
//                        );
//
//            $data[] = array(
//                        'id'          => 5,
//                        'title'       => 'The Good, the Bad and the Ugly',
//                        'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
//                        'year'        => '1966',
//                        'director'    => 'Sergio Leone',
//                        'rating'      => '9.0'
//                        );
//
//            $data[] = array(
//                        'id'          => 6,
//                        'title'       => 'The Dark Knight',
//                        'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
//                        'year'        => '2008',
//                        'director'    => 'Christopher Nolan',
//                        'rating'      => '9.0'
//                        );
//
//            $data[] = array(
//                        'id'          => 7,
//                        'title'       => '12 Angry Men',
//                        'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
//                        'year'        => '1957',
//                        'director'    => 'Sidney Lumet',
//                        'rating'      => '8.9'
//                        );
//
//            $data[] = array(
//                        'id'          => 8,
//                        'title'       => 'Schindler\'s List',
//                        'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
//                        'year'        => '1993',
//                        'director'    => 'Steven Spielberg',
//                        'rating'      => '8.9'
//                        );
//
//            $data[] = array(
//                        'id'          => 9,
//                        'title'       => 'The Lord of the Rings: The Return of the King',
//                        'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
//                        'year'        => '2003',
//                        'director'    => 'Peter Jackson',
//                        'rating'      => '8.9'
//                        );
//
//            $data[] = array(
//                        'id'          => 10,
//                        'title'       => 'Fight Club',
//                        'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
//                        'year'        => '1999',
//                        'director'    => 'David Fincher',
//                        'rating'      => '8.8'
//                        );

//            return $data;
        }


        // Used to display the value of the id column
        public function column_id($item)
        {
            return $item['stockID'];
        }

        /**
         * Define what data to show on each column of the table
         *
         * @param  Array $item        Data
         * @param  String $column_name - Current column name
         *
         * @return Mixed
         */
        public function column_default( $item, $column_name )
        {
            switch( $column_name ) {
                case 'stockID':
                case 'title':
                case 'description':
                case 'year':
                case 'director':
                case 'rating':
                    return $item[ $column_name ];

                default:
                    return print_r( $item, true ) ;
            }
        }

    }
?>

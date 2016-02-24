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


?>

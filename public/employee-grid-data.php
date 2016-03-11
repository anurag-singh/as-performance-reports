<?php

/*	Start Custom Functions to make calculations */	
function noOfUnits($entryPrice) {
		return floor(100000/$entryPrice);
	}

function plPerUnit($action, $entryPrice, $exitPrice) {
		if($action == 'BUY') {
			$plPerUnit = $exitPrice - $entryPrice;
		}
		else {
			$plPerUnit =  $entryPrice - $exitPrice;
		}

		$plPerUnit = number_format((float)$plPerUnit, 1, '.', '');

		return $plPerUnit;
	}

function plPerLac($plPerUnit, $noOfUnits) {
		return $plPerUnit*$noOfUnits;
	}

function grossROI($plPerLac, $noOfUnits, $entryPrice) {
		$number = $noOfUnits * $entryPrice;
		if($number>0)
		{
			$grossROI = ($plPerLac/($noOfUnits * $entryPrice))*100;
			return number_format((float)$grossROI, 2, '.', '') . ' %';
		}
		else
		{
			return 0;
		}
	}

function finalResult($grossROI) {
		if($grossROI<=0){
			return 'MISS';
		}
		elseif ($grossROI>0){
			return 'HIT';
		}
		else {
			return 'Pending';
		}
	}	

function finalResultIcon($grossROI) {
		if($grossROI<=0){
			return '<button class="icn-btn"><span class="font-icn">&#xf088;</span></button>';
		}
		elseif ($grossROI>0){
			return '<button class="icn-btn"><span class="font-icn">&#xf087;</span></button>';
		}
		else {
			return '<button class="icn-btn"><span class="font-icn">&#xf256;</span></button>';
		}
	}	

/*	End Custom Functions to make calculations */	


global $wpdb;
/* Database connection start */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wp_rmoney";

$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'ID', 
	1 => 'stockCat',
	2 => 'stockID',
	3 => 'stockName',
	4 => 'action',
	5 => 'entryDate',
	6 => 'exitDate',
	7 => 'entryPrice',
	8 => 'exitPrice',
	9 => 'targetPrice',
	10 => 'stopLoss'
);


// getting total number records without any search
$sql = "SELECT * ";
$sql.=" FROM wp_performance_report ";
$sql.= " WHERE stockCat = 'Trading'";
$query = mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND stockCat = 'Trading' AND ( stockName LIKE '".$requestData['search']['value']."%' ";    
	$sql.=" OR action LIKE '".$requestData['search']['value']."%' ";

	$sql.=" OR entryDate LIKE '".$requestData['search']['value']."%' )";
}
$query = mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");



$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 


	/*	Prepare column data to display on front-end display */
	$plPerUnit = plPerUnit($row["action"], $row["entryPrice"], $row["exitPrice"] );
	$noOfUnits = noOfUnits($row["entryPrice"]);
	$plPerLac = plPerLac($plPerUnit, $noOfUnits);
	$grossROI = grossROI($plPerLac, $noOfUnits, $row["entryPrice"]);
	$finalResult = finalResultIcon($grossROI);
	/*	Prepare column data to display on front-end display */

	$nestedData[] = $row["stockName"];
	$nestedData[] = $row["entryDate"];
	$nestedData[] = $row["action"];
	$nestedData[] = $row["entryPrice"];
	$nestedData[] = $row["targetPrice"];
	$nestedData[] = $row["stopLoss"];
	$nestedData[] = $row["exitPrice"];
	$nestedData[] = $plPerUnit;
	$nestedData[] = $plPerLac;
	$nestedData[] = $grossROI;
	$nestedData[] = $finalResult;
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format

?>

<?php

class Report_Formulas {
		var $sumOfPlPerLac;

		// (Exit date - Enrty Date)
		protected function timePeriod($entryDate, $exitDate){
	    		$entryDate = strtotime($entryDate);
				$exitDate = strtotime($exitDate);

				$datediff = $exitDate - $entryDate;

	     		return (floor($datediff/(60*60*24)));
	    }

	    protected function noOfUnits($entryPrice) {
			return floor(100000/$entryPrice);
		}

		protected function plPerUnit($action, $entryPrice, $exitPrice) {
			if($action == 'BUY') {
				$plPerUnit = $exitPrice - $entryPrice;
			}
			else {
				$plPerUnit =  $entryPrice - $exitPrice;
			}

			$plPerUnit = number_format((float)$plPerUnit, 1, '.', '');

			return $plPerUnit;
		}

		protected function plPerLac($plPerUnit, $noOfUnits) {
			return $plPerUnit*$noOfUnits;
		}

		protected function grossROI($plPerLac, $noOfUnits, $entryPrice) {
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

		// protected function number_format_drop_zero_decimals($n, $n_decimals)
		 //    {
		 //        return ((floor($n) == round($n, $n_decimals)) ? number_format($n) : number_format($n, $n_decimals));
		 //    }

		protected function finalResult($grossROI) {
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

		protected function perCallInvestment($entryPrice, $noOfUnits) {
			return $entryPrice * $noOfUnits;
		}

		protected function perCallProfitLoss($plPerLac) {
			return $plPerLac;
		}

		protected function perCallROIonInvestment($plPerLac, $perCallInvestment) {
			$roi = $plPerLac / $perCallInvestment;
			return number_format((float)$roi, 4, '.', '');
		}

		protected function totalInvestment($perCallInvestment) {
			return round($this->totalInvestment += $perCallInvestment);
		}

		protected function netProfitLoss($plPerLac) {
			return $this->netProfitLoss += $plPerLac;
		}


		protected function totalAverageTimePeriod($totalTimePeriod, $totalCallsgiven) {
			//$totalTimePeriod = $this->totalAverageTimePeriod += $totalTimePeriod;
			if($totalCallsgiven>0) {
				$averageTimePeriod = $totalTimePeriod / $totalCallsgiven;
				return number_format($averageTimePeriod, 2, '.', ' %');
			}
		}

		protected function annualisedROI($netProfitLossTillYet, $totalInvestmentTillYet, $totalAverageTimePeriod) {
			$secondNo =   ($totalInvestmentTillYet * $totalAverageTimePeriod) / 365;
			$annualisedROI = ($netProfitLossTillYet / $secondNo)*100;
			return number_format((float)$annualisedROI, 2, '.', ''). ' %';
		}

		protected function successPercentage($totalCallsgiven, $totalHits) {
			if($totalHits>0){
				$successRate = ($totalHits/($totalCallsgiven - $totalPendings)) * 100;
				return number_format($successRate, 1, '.', ''). ' %';
			}
		}

		protected function sumOfPlPerLac($plPerLac) {
			 return $this->sumOfPlPerLac += $plPerLac;
		}



		protected function totalInvestmentTillYet($entryPrice, $noOfUnits){
			$this->totalInvestmentTillYet += $entryPrice*$noOfUnits;
			return $this->totalInvestmentTillYet;
		}

		protected function netProfitLossTillYet($plPerLac){
			return $this->netProfitLossTillYet += $plPerLac;
		}

		protected function roiOnInvestment($netProfitLossTillYet, $totalInvestmentTillYet) {
			// 
			$this->roiOnInvestment = ($netProfitLossTillYet / $totalInvestmentTillYet)*100;
			return number_format($this->roiOnInvestment, 2 , '.', ''). ' %';
		}

		protected function finalResultIcon($grossROI) {
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

}







class All_Calls_Grid_Data extends Report_Formulas {
	var $servername = "localhost";
	var $username = "root";
	var $password = "";
	var $dbname = "wp_rmoney";
	var $conn;
	var $requestData;

	function __construct() {
		/* Database connection start */
			$this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname) or die("Connection failed: " . mysqli_connect_error());
		/* Database connection end */

		$this->fetch_data_from_db();
	}

	function fetch_data_from_db() {
		global $requestData;

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

		$category = 'Trading';

		if(isset($requestData['cat']) && !empty($requestData['cat']))
		{
			$category = $requestData['cat'];
		}

			// getting total number records without any search
			$sql = "SELECT * FROM wp_performance_report WHERE stockCat = '".$category."'";

			$query = mysqli_query($this->conn, $sql) or die("all-single-call-type-grid-data.php: get all calls");
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


			if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
				$sql.=" AND stockCat = 'Trading' AND ( stockName LIKE '".$requestData['search']['value']."%' ";    
				$sql.=" OR action LIKE '".$requestData['search']['value']."%' ";

				$sql.=" OR entryDate LIKE '".$requestData['search']['value']."%' )";
			}


			$query = mysqli_query($this->conn, $sql) or die("all-single-call-type-grid-data.php: get all calls");
			$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
			$query=mysqli_query($this->conn, $sql) or die("all-single-call-type-grid-data.php: get all calls");

			$this->load_data_grid($query, $totalData, $totalFiltered);
	}

	function load_data_grid ($query, $totalData, $totalFiltered) {
			global $requestData;


			$data = array();
			while( $row=mysqli_fetch_array($query) ) {  // preparing an array
				$nestedData=array(); 


				/*	Prepare column data to display on front-end display */
				$plPerUnit = $this->plPerUnit($row["action"], $row["entryPrice"], $row["exitPrice"] );
				$noOfUnits = $this->noOfUnits($row["entryPrice"]);
				$plPerLac = $this->plPerLac($plPerUnit, $noOfUnits);
				$grossROI = $this->grossROI($plPerLac, $noOfUnits, $row["entryPrice"]);
				$finalResult = $this->finalResultIcon($grossROI);
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
	}

}

$loadData =  new All_Calls_Grid_Data();
?>



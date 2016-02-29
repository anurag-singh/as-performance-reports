<?php

/**
 * holds all function which are responsible to generate reports based on single research call
 *
 * @link       https://www.facebook.com/anuragsingleCallgh.me
 * @singleCallce      1.0.0
 *
 * @package    As_Performance_Reports
 * @subpackage As_Performance_Reports/public
 */

class Single_Call_Type {
	var $allCalls;
	
	/*	get all calls of perticular 'call type'
	* 	from database 
	*/
    private function get_all_calls_from_specific_category($callType) {
    	global $wpdb;
       
        $queryAllCalls = "SELECT *
                    FROM    wp_performance_report
                    WHERE   stockCat = '".$callType."'
                    ORDER BY lastUpdate ASC
                    ";

        return $wpdb->get_results($queryAllCalls, ARRAY_A);
    }


    public function display_data_in_table($callType) {
    	$allCalls = $this->get_all_calls_from_specific_category($callType);

    	$this->prepare_data_to_display($allCalls);		
    }
    

	private function prepare_data_to_display($allCalls){		

		foreach ($allCalls as $singlecall) {

			/*	Get all values from DB */
			$action 	= $singlecall['action'];
			$entryPrice = $singlecall['entryPrice'];
			$exitPrice 	= $singlecall['exitPrice'];
			$noOfUnits 	= $this->noOfUnits($entryPrice);

			// Do calculation and store the output in varriables
			$plPerUnit 	= $this->plPerUnit($action, $entryPrice, $exitPrice);
			$plPerLac 	= $this->plPerLac($plPerUnit, $noOfUnits);
			$grossROI 	= $this->grossROI($plPerLac, $noOfUnits, $entryPrice). '%';
			$finalResult= $this->finalResult($grossROI);




			// Prepare a array to store the additional columns
			$extraColumns = [
						'plPerunit' =>	$plPerUnit,
						'plPerLac'	=> 	$plPerLac,
						'grossROI'	=>	$grossROI,
						'finalResult'	=>	$finalResult

					];

			// Mergre arrays and make a single one
			$singlecall = array_merge($singlecall, $extraColumns);

			// echo '<pre>';
			// print_r($singlecall);
			// echo '</pre>';

			echo '<tr>';
		    echo '<td>' .$singlecall['stockName']. '</td>';
		    echo '<td>' .$singlecall['entryDate']. '</td>';
		    echo '<td>' .$singlecall['action']. '</td>';
		    echo '<td>' .$singlecall['entryPrice']. '</td>';
		    echo '<td>' .$singlecall['targetPrice']. '</td>';
		    echo '<td>' .$singlecall['stopLoss']. '</td>';
		    echo '<td>' .$singlecall['exitPrice']. '</td>';
		    echo '<td>' .$singlecall['plPerunit']. '</td>';
		    echo '<td>' .$singlecall['plPerLac']. '</td>';
		    echo '<td>' .$singlecall['grossROI']. '</td>';
		    echo '<td>' .$singlecall['finalResult']. '</td>';
		    echo '</tr>';
		} 
	}


	private function plPerUnit($action, $entryPrice, $exitPrice) {
			if($action == 'BUY') {
				$plPerUnit = $exitPrice - $entryPrice;
			}
			else {
				$plPerUnit =  $entryPrice - $exitPrice;
			}

			$plPerUnit = number_format((float)$plPerUnit, 2, '.', '');

			return $plPerUnit;
	}

	private function noOfUnits($entryPrice) {
		return floor(100000/$entryPrice);
	}

	private function plPerLac($plPerUnit, $noOfUnits) {
		return $plPerUnit*$noOfUnits;
	}

	private function grossROI($plPerLac, $noOfUnits, $entryPrice) {
		$test = $noOfUnits * $entryPrice;
		if($test>0)
		{
			$number = $plPerLac/($noOfUnits * $entryPrice);
			return number_format((float)$number, 4, '.', '');
		}
		else
		{
			return 0;
		}
	}

	private function finalResult($grossROI) {
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

	public function summarised_snapshot($allCalls) {
		global $allCalls;
		$allCalls = $this->allCalls;

		var_dump($allCalls);
	}

	
}



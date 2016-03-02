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

class Single_Call_Type extends Report_Formulas {
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

    	return $this->prepare_data_to_display($allCalls);		
    }
    

	private function prepare_data_to_display($allCalls) {	
		$totalCallsgiven = 0;

		foreach ($allCalls as $singleCall) {
			$totalCallsgiven ++;

			/*	Get all values from DB */
			$action 	= $singleCall['action'];
			$entryPrice = $singleCall['entryPrice'];
			$exitPrice 	= $singleCall['exitPrice'];
			$noOfUnits 	= $this->noOfUnits($entryPrice);

			// Do calculation and store the output in varriables
			$plPerUnit 	= $this->plPerUnit($action, $entryPrice, $exitPrice);
			$plPerLac 	= $this->plPerLac($plPerUnit, $noOfUnits);
			$grossROI 	= $this->grossROI($plPerLac, $noOfUnits, $entryPrice). '%';
			$finalResult = $this->finalResult($grossROI);
			$finalResultIcon= $this->finalResultIcon($grossROI);

			$successPercentage = $this->successPercentage($totalCallsgiven, $totalHits);
			$totalInvestment = $this->totalInvestment($perCallInvestment);
			$netProfitLoss = $this->netProfitLoss($plPerLac);
			$roiOnInvestment = $this->roiOnInvestment($netProfitLoss, $totalInvestment);
			$annualisedROI = $this->annualisedROI($netProfitLoss, $totalInvestment, $totalAverageTimePeriod);

			
			// Prepare a array to store the additional columns
			$extraColumns = [
						'plPerunit' 	=>	$plPerUnit,
						'plPerLac'		=> 	$plPerLac,
						'grossROI'		=>	$grossROI,
						'finalResult'	=>	$finalResult,	
						'finalResultIcon'	=>	$finalResultIcon,	
					];

			// Mergre arrays and make a single one
			$singleCall = array_merge($singleCall, $extraColumns);

				if($finalResult == 'HIT') {
					 $totalHits++;
				}
				elseif ($finalResult == 'MISS') {
					$totalMisses++;
				}
				else{
					if($totalPendings<=0){
						return $totalPendings = 0;
					}
					$totalPendings++;
				}


			$singleCalls[] = $this->singleCalls($singleCall);


		} 

		// echo '<pre>';
		// print_r($singleCalls);
		// echo '</pre>';

		

		$summarisedSnapshot =	[
							'callsGiven'		=>	$totalCallsgiven,
							'totalHits' 		=>	$totalHits,
							'totalMisses' 		=>	$totalMisses,
							'totalPendings' 	=>	$totalPendings,
							'successPercentage' =>	$successPercentage,
							'roiOnInvestment' 	=>	$roiOnInvestment,
							'annualisedROI' 	=>	$annualisedROI
						];
		
		// echo '<pre>'; 
		// 	print_r($summarisedSnapshot);
		// echo '</pre>';

		// $singleCallTypeData = 	[
		// 							'singleCalls' 			=>		$singleCalls,
		// 							'summarisedSnapshot' 	=>		$summarisedSnapshot
		// 						]; 

		// return $singleCallTypeData;

		
	}

	private function singleCalls($singleCall) {

		$singleCalls = 	[
								'stockName'			=>	$singleCall['stockName'],
								'entryDate'			=>	$singleCall['entryDate'],
								'action'			=>	$singleCall['action'],
								'entryPrice'		=>	$singleCall['entryPrice'],
								'targetPrice'		=>	$singleCall['targetPrice'],
								'stopLoss'			=>	$singleCall['stopLoss'],
								'exitPrice'			=>	$singleCall['exitPrice'],
								'plPerunit'			=>	$singleCall['plPerunit'],
								'plPerLac'			=>	$singleCall['plPerLac'],
								'grossROI'			=>	$singleCall['grossROI'],
								'finalResultIcon'	=>	$singleCall['finalResultIcon'],
							];

			$this->display_all_calls_in_tabular_format($singleCall);
	}

	private function display_all_calls_in_tabular_format($singlecall) {
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
		    echo '<td>' .$singlecall['finalResultIcon']. '</td>';
		    echo '</tr>';
	}

	private function summarisedSnapshot() {

	}



	private function finalResultIcon($grossROI) {
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

	public function summarised_snapshot($callType) {
		$allCalls = $this->get_all_calls_from_specific_category($callType);

		$summarisedSnapshot['callGiven'] = count($allCalls);
		//var_dump($summarisedSnapshot);

		return $summarisedSnapshot;
	}

	
}



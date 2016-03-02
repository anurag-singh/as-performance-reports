<?php

/**
 * holds all function which are responsible to generate last month reports
 *
 * @link       https://www.facebook.com/anuragsingleCallgh.me
 * @singleCallce      1.0.0
 *
 * @package    As_Performance_Reports
 * @subpackage As_Performance_Reports/public
 */



class Last_Month_Report extends Report_Formulas {
	var $totatROI;
	var $netProfitLoss;
	var $totalInvestment;
	var $totalAverageTimePeriod;
	var $totalHits;
	var $totalMisses;
	var $totalPendings;
	var $singleCallType;
	var $allRsearchCalls = ['Trading', 'Positional'];		// Define all research calls in array
	
    public function get_all_calls() {
    	global $wpdb;
        $today = date('Y-m-d');
        $startDate = date('Y-m-d',strtotime($today.'-1 day'));
        $endDate = date('Y-m-d',strtotime($startDate.'-30 day'));

        $queryAllCalls = "SELECT *
                    FROM    wp_performance_report
                    WHERE   DATE(`lastUpdate`) BETWEEN '".$endDate."' AND '".$startDate."'
                    ";

        return $wpdb->get_results($queryAllCalls, ARRAY_A);
    } 

    public function detail_about_calls($category) {
    	$totalCallsgiven = 0;
    	$allCalls = $this->get_all_calls();

    	foreach ($allCalls as $singleCall) {

    		$stockCat 	= $singleCall['stockCat'];
    		$entryPrice = $singleCall['entryPrice'];
    		$exitPrice 	= $singleCall['exitPrice'];
    		$entryDate 	= $singleCall['entryDate'];
    		$exitDate 	= $singleCall['exitDate'];
    		$action 	= $singleCall['action'];

    		// For total calls of category given
    		if($stockCat == $category) {
    			$totalCallsgiven ++;

				//  For time period of each call
				$timePeriod = $this->timePeriod($entryDate, $exitDate);

				// No of Units
				$noOfUnits = $this->noOfUnits($entryPrice);

				// profit or loss Per Unit
				$plPerUnit = $this->plPerUnit($action, $entryPrice, $exitPrice);

				// profit or loss Per Lac
				$plPerLac = $this->plPerLac($plPerUnit, $noOfUnits);

				// Gross ROI
				$grossROI = $this->grossROI($plPerLac, $noOfUnits, $entryPrice);

				// Final result
				$finalResult = $this->finalResult($grossROI);

				// Per call Investment
				$perCallInvestment = $this->perCallInvestment($entryPrice, $noOfUnits);

				// Per call Profit
				$perCallProfitLoss = $this->perCallProfitLoss($plPerLac);

				// Per call ROI on Investment
				$perCallROIonInvestment = $this->perCallROIonInvestment($perCallProfitLoss, $perCallInvestment);

				// Total investment
				$totalInvestment = $this->totalInvestment($perCallInvestment);

				// Net Profit or Loss
				$netProfitLoss = $this->netProfitLoss($plPerLac);

				// ROI on Investment
				$roiOnInvestment = $this->roiOnInvestment($netProfitLoss, $totalInvestment);

				// Total Average Time Period
				$totalAverageTimePeriod = $this->totalAverageTimePeriod($timePeriod, $totalCallsgiven);

				// Annualised ROI
				$annualisedROI = $this->annualisedROI($netProfitLoss, $totalInvestment, $totalAverageTimePeriod);

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


				$success = $this->successPercentage($totalCallsgiven, $totalHits);
    		}

			$allCalltypes[] = 	[
							'action'				=> 	$action,
							'entryPrice'			=> 	$entryPrice,
							'exitPrice'				=> 	$exitPrice,
    						'totalCalls' 			=>	$totalCallsgiven,
    						'timePeriod' 			=>	$timePeriod,
    						'noOfUnits' 			=>	$noOfUnits,
    						'plPerUnit' 			=>	$plPerUnit,
    						'plPerLac' 				=>	$plPerLac,
    						'grossROI' 				=>	$grossROI . ' %',
    						'finalResult' 			=>	$finalResult,
    						'perCallInvestment' 	=>	$perCallInvestment,
    						'perCallProfitLoss' 	=>	$perCallProfitLoss,
    						'perCallROIonInvestment'=>	$perCallROIonInvestment,
    						// 'totalInvestment' 		=>	$totalInvestment,
    						// 'netProfitLoss' 		=>	$netProfitLoss,
    						// 'roiOnInvestment'		=>	$roiOnInvestment,
    						// 'totalAverageTimePeriod'=>	$totalAverageTimePeriod,
    						// 'annualisedROI'			=> $annualisedROI,
    						// 'success'				=> 	$success,
    						// 'totalHits'				=>	$totalHits,
    						// 'totalMisses'			=>	$totalMisses,
    						// 'totalPendings'			=> 	$totalPendings
    					];
    	}

    	$singleCallTypes	= 	[
    								'callsGiven' 			=>	$totalCallsgiven,
    								'totalHits'				=>	$totalHits,
		    						'totalMisses'			=>	$totalMisses,
		    						'totalPendings'			=> 	$totalPendings,
    								'totalInvestment' 		=>	$totalInvestment,
		    						'netProfitLoss' 		=>	$netProfitLoss,
		    						'roiOnInvestment'		=>	$roiOnInvestment,
		    						'totalAverageTimePeriod'=>	$totalAverageTimePeriod,
		    						'annualisedROI'			=>  $annualisedROI,
		    						'success'				=> 	$success,
		    						
    							];

    	
    	$data =[
    			'allCalltypes' 		=> 	$allCalltypes, 
    			'singleCallTypes' 	=> 	$singleCallTypes
    			];

    	return $data;

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';


    }

    public function display_data_in_tabular_format($reportFrontEnd) {
    	
    	$allRsearchCalls = $this->allRsearchCalls;
		
		foreach ($allRsearchCalls as $single ) {
			$singleCallType[] = end($reportFrontEnd->detail_about_calls($single));
		}

		$this->singleCallType = $singleCallType;

		return $singleCallType;

		// echo '<pre>';
		// print_r($singleCallType);
		// echo '</pre>';	
    }

    public function display_overall_call_type_data() {
    	foreach ($this->singleCallType as $singleCall ) {
			
			$OverallAllProducts['callsGiven'] += $singleCall['callsGiven'];
			$OverallAllProducts['totalHits']	+=	$singleCall['totalHits'];
			$OverallAllProducts['totalMisses'] += $singleCall['totalMisses'];
			$OverallAllProducts['totalPendings']	+=	$singleCall['totalPendings'];
			$OverallAllProducts['success'] += $singleCall['success'];
			$OverallAllProducts['roiOnInvestment']	+=	$singleCall['roiOnInvestment'];
			$OverallAllProducts['annualisedROI']	+=	$singleCall['annualisedROI'];	

		}

			return $OverallAllProducts;
			// echo '<pre>';
			// print_r($OverallAllProducts);
			// echo '</pre>';
    }

    






}



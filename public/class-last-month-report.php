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
	var $callCatArray;		// Define all research calls in array

	function __construct() {
		$this->get_unique_call_type();	
	}
	
    
	private function get_unique_call_type() {
		global $wpdb;
		$selectAllCategories = "SELECT DISTINCT stockCat
								FROM	wp_performance_report
								";

		$allUniqueCats = $wpdb->get_results($selectAllCategories, OBJECT);

		foreach ($allUniqueCats as $singleCat) {
			$allCats[] = $singleCat->stockCat;
		}

		$this->callCatArray = $allCats;
	}
		

    public function get_all_calls() {
    	global $wpdb;
        $today = date('Y-m-d');
        // $startDate = date('Y-m-d',strtotime($today.'-1 day'));
        $startDate = date('Y-m-d',strtotime($today));
        $endDate = date('Y-m-d',strtotime($startDate.'-30 day'));

        $queryAllCalls = "SELECT *
                    FROM    wp_performance_report
                    WHERE   DATE(`lastUpdate`) BETWEEN '".$endDate."' AND '".$startDate."'
                    ";

        return $wpdb->get_results($queryAllCalls, ARRAY_A);
    } 

    public function detail_about_calls($category) {
    	$totalCallsgivenTillYet = 0;
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
    			$totalCallsgivenTillYet ++;

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

				// Sum all time periods
				$totalTimePeriod += $timePeriod;

				// Calculate ROI on investment with total netprofitloss and totalInvestment
				$roiOnInvestment = $this->roiOnInvestment($netProfitLossTillYet, $totalInvestmentTillYet);

				$totalAverageTimePeriod =  $this->totalAverageTimePeriod($totalTimePeriod, $totalCallsgivenTillYet);

				$totalInvestmentTillYet = $this->totalInvestmentTillYet($entryPrice, $noOfUnits);
				
				$netProfitLossTillYet = $this->netProfitLossTillYet($plPerLac);			

				$annualisedROI = $this->annualisedROI($netProfitLossTillYet, $totalInvestmentTillYet, $totalAverageTimePeriod);

				if($finalResult == 'HIT') {
					 $totalHits++;
				}
				elseif ($finalResult == 'MISS') {
					$totalMisses++;
				}
				else{
					if($totalPendings <= 0){
						return 0;
					}
					$totalPendings++;
				}


				$success = $this->successPercentage($totalCallsgivenTillYet, $totalHits);
    		}

			$allCalltypes = 	array(
							'action'				=> 	$action,
							'entryPrice'			=> 	$entryPrice,
							'exitPrice'				=> 	$exitPrice,
    						'totalCalls' 			=>	$totalCallsgivenTillYet,
    						'timePeriod' 			=>	$timePeriod,
    						'noOfUnits' 			=>	$noOfUnits,
    						'plPerUnit' 			=>	$plPerUnit,
    						'plPerLac' 				=>	$plPerLac,
    						'grossROI' 				=>	$grossROI . ' %',
    						'finalResult' 			=>	$finalResult . ' %',
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
    					);
    	}

    	$singleCallTypes	= 	array(
    								'callsGiven' 			=>	$totalCallsgivenTillYet,
    								'totalHits'				=>	$totalHits,
		    						'totalMisses'			=>	$totalMisses,
		    						'totalPendings'			=> 	$totalPendings,
    								'totalInvestment' 		=>	$totalInvestment,
		    						'netProfitLoss' 		=>	$netProfitLoss,
		    						'roiOnInvestment'		=>	$roiOnInvestment,
		    						'totalAverageTimePeriod'=>	$totalAverageTimePeriod,
		    						'annualisedROI'			=>  $annualisedROI,
		    						'success'				=> 	$success,
		    						
    							);

    	
    	$data = array(
    			'allCalltypes' 		=> 	$allCalltypes, 
    			'singleCallTypes' 	=> 	$singleCallTypes
    			);

    	return $data;

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';


    }

    public function display_data_in_tabular_format($reportFrontEnd) {
    	
    	$callCatArray = $this->callCatArray;
		
		foreach ($callCatArray as $single ) {
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
			

		// Get the total no of calls from the array
		$totalCallCats = count($this->callCatArray);

		// Get the all the success value and divied them to get average of success
		$OverallAllProducts['totalSuccessPercent'] = $OverallAllProducts['success']/$totalCallCats  . ' %';;

		// Get the all the roiOnInvestment value and divied them to get the average of roiOnInvestment
		$OverallAllProducts['totalRoiOnInvestmentPercent'] = $OverallAllProducts['roiOnInvestment']/$totalCallCats . ' %';

		// Get the all the annualisedROI value and divied them to get the average of annualisedROI
		$OverallAllProducts['totalAnnualisedROIPercent'] = $OverallAllProducts['annualisedROI']/$totalCallCats  . ' %';;

		return $OverallAllProducts;
    }

    






}



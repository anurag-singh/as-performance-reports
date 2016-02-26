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

// WHERE   stockID  LIKE '".$colNameLike."%'

class Last_Month_Report {

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

    private function timePeriod($entryDate, $exitDate){
    		$entryDate = strtotime($entryDate);
			$exitDate = strtotime($exitDate);

			$datediff = $exitDate - $entryDate;

     		return (floor($datediff/(60*60*24)));
    }

    private function noOfUnits($entryPrice) {
			return floor(100000/$entryPrice);			
	}

	private function plPerUnit($action, $entryPrice, $exitPrice) {	
			if($action == 'BUY') {
				return $exitPrice - $entryPrice;
			}
			else {
				return $entryPrice - $exitPrice;
			}		
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
	
	private function number_format_drop_zero_decimals($n, $n_decimals)
    {
        return ((floor($n) == round($n, $n_decimals)) ? number_format($n) : number_format($n, $n_decimals));
    }

	private function finalResult($grossROI) {
		if($grossROI<=0){
			return 'MISS';
		}
		else {
			return 'HIT';
		}
	}

	private function perCallInvestment($entryPrice, $noOfUnits) {
		return $entryPrice * $noOfUnits;
	}

	// private function perCallProfitLoss() {

	// }

	private function perCallROIonInvestment($plPerLac, $perCallInvestment) {
		$roi = $plPerLac / $perCallInvestment;
		return number_format($roi, 4, '.', '');
	}

	private function totalInvestment($entryPrice, $noOfUnits) {
		return round($entryPrice * $noOfUnits);
	}

	private function netProfitLoss($plPerLac) {
		return round($plPerLac += $plPerLac);
	}

	private function roiOnInvestment() {
		return "not defined yet";
	}

	private function totalAverageTimePeriod() {
		return "not defined yet";
	}
	
	private function annualisedROI() {
		return "not defined yet";
	}

	private function success($totalCalls, $totalHit) {
		if($totalHit<0){
			$successRate = ($totalCalls /  $totalHit) * 100;
			return number_format($successRate, 1, '.', ''). ' %';
		}
	}



    public function detail_about_calls($category) {
		$totalCalls = $totalHit = $totalMiss = $totalPending = $i = 0;
    	$allCalls = $this->get_all_calls();
    	
    	foreach ($allCalls as $singleCall) {

    		$stockCat = $singleCall['stockCat'];
    		$entryPrice = $singleCall['entryPrice'];
    		$exitPrice = $singleCall['exitPrice'];
    		$entryDate = $singleCall['entryDate'];
    		$exitDate = $singleCall['exitDate'];
    		$action = $singleCall['action'];


    		


    		// For total calls of category given
    		if($stockCat == $category) {
    			$totalCalls++;
    			
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

				// Total Investment
				$perCallInvestment = $this->perCallInvestment($entryPrice, $noOfUnits);

				// Net Profit or Loss
				//$perCallProfitLoss = $this->perCallProfitLoss($plPerLac);

				// Per call ROI on Investment
				$perCallROIonInvestment = $this->perCallROIonInvestment($perCallProfitLoss, $perCallInvestment);
		

				// ROI on Investment
				$roiOnInvestment = $this->roiOnInvestment();

				// Total Average Time Period
				$totalAverageTimePeriod = $this->totalAverageTimePeriod();

				// Annualised ROI
				$annualisedROI = $this->annualisedROI();


				
				if($finalResult == 'HIT') {
					 $totalHit++;
				}
				elseif ($finalResult == 'MISS') {
					$totalMiss++;
				}
				else{
					$totalPending++;
				}

				// $calls['totalHits'] = $totalHit;
				// $calls['totalMiss'] = $totalMiss;
				// $calls['totalPending'] = $totalPending;




				

    		}

			$calls[$i] = 	[
							'Action'				=> 	$action,
							'Entry Price'			=> 	$entryPrice,
							'Exit Price'			=> 	$exitPrice,
    						'Total Calls' 			=>	$totalCalls,
    						'Time Period' 			=>	$timePeriod,
    						'No Of Units' 			=>	$noOfUnits,
    						'PL Per Unit' 			=>	$plPerUnit,
    						'PL Per Lac' 			=>	$plPerLac,
    						'Gross ROI' 			=>	$grossROI . ' %',
    						'Final Result' 			=>	$finalResult,
    						'Per Call Investment' 	=>	$perCallInvestment,
    						'Per Call Profit/Loss' 	=>	$plPerLac,
    						'Per Call ROI On Investment'=>	$perCallROIonInvestment,
    						'Total Investment' 		=>	$totalInvestment,
    						'Net Profit/Loss' 	=>	$netProfitLoss,
    						'ROI On Investment'		=>	$roiOnInvestment,
    						'Total Average Time Period'=>	$totalAverageTimePeriod,
    						'Annualised ROI'		=> $annualisedROI,
    						'Success'				=> 	$success,
    						'Total Hits'			=>	$totalHits,
    						'Total Misses'			=>	$totalMisses,
    						'Total Pendings'		=> 	$totalPendings
    					];

		    	// Success percentage
				$success = $this->success($totalCalls, $totalHits );	
    		
    		$i++;
    	}
    	return $calls;
    	
    }


    



}



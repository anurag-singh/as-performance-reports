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
	var $totalHit = 0;
	var $totalMiss = 0;
	var $totalPending = 0;

    // function __construct($category) {


    // }

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

	private function totalInvestment($entryPrice, $noOfUnits) {
		return round($entryPrice * $noOfUnits);
	}

	private function netProfitLoss($plPerLac) {
		return round($plPerLac += $plPerLac);
	}

	private function success($totalCalls, $totalHit) {
		$successRate = ($totalCalls /  $totalHit) * 100;
		return number_format($successRate, 1, '.', ''). ' %';
	}



    public function detail_about_calls($category) {
    	$callCounter = 0;
    	 $totalHit = 0;
    	 $totalMiss = 0;
    	 $totalPending = 0;
    	$allCalls = $this->get_all_calls();
    	$i =0;
    	foreach ($allCalls as $singleCall) {

    		$entryPrice = $singleCall['entryPrice'];


    		// For total calls of category given
    		$stockCat = $singleCall['stockCat'];
    		if($stockCat == $category) {
    			$callCounter++;
    			$calls['totalCalls'] = $callCounter;

				//  For time period of each call
				$calls['timePeriod'] = $this->timePeriod($singleCall['entryDate'], $singleCall['exitDate']);

				// No of Units
				$calls['noOfUnits'] = $this->noOfUnits($singleCall['entryPrice']);

				// profit or loss Per Unit
				$calls['plPerUnit'] = $this->plPerUnit($singleCall['action'], $singleCall['entryPrice'], $singleCall['exitPrice']);

				// profit or loss Per Lac
				$calls['plPerLac'] = $this->plPerLac($calls['plPerUnit'], $calls['noOfUnits']);

				// Gross ROI
				$calls['grossROI'] = $this->grossROI($calls['plPerLac'], $calls['noOfUnits'], $entryPrice);

				// Final result
				$calls['finalResult'] = $this->finalResult($calls['grossROI']);  

				// Total Investment
				$calls['totalInvestment'] = $this->totalInvestment($entryPrice, $calls['noOfUnits']);

				// Net Profit or Loss
				$calls['netProfitLoss'] = $this->netProfitLoss($calls['plPerLac']);

				

				


				
				
				if($calls['finalResult'] == 'HIT') {
					 $totalHit++;
				}
				elseif ($calls['finalResult'] == 'MISS') {
					
					$totalMiss++;
				}
				else{
					$totalPending++;
				}

				$calls['totalHits'] = $totalHit;
				$calls['totalMiss'] = $totalMiss;
				$calls['totalPending'] = $totalPending;




				$i++;

    		}

		    	// Success percentage
				$calls['success'] = $this->success($calls['totalCalls'], $calls['totalHits'] );	
    		
    	}
    	return $calls;
    	
    }


    











    private function totalBuy($a, $b) {
    	return $a+$b;
    	// For total Buy
	    		// $action = $singleCall['action'];
	    		// if ($action == 'BUY') {
	    		// 	$buyCounter++;
	    		// 	$calls['totalBuys'] = $buyCounter;
	    		// }
    }





	public function count_calls_per_category($category) {
	       $callCounter = 0;
	       $allCalls = $this->get_all_calls();

	       foreach($allCalls as $singleCall) {
	           $value = $singleCall['stockCat'];

	           if($value == $category) {
	               $callCounter++;
	           }
	       }
	   return  $callCounter;
	}


	public function workingtimePeriod($tradingCalls) {
		foreach ($tradingCalls as $call) {

			echo "<pre>";
			print_r($call);
			echo "</pre>";

			$entryDate = strtotime($call['entryDate']);
			$exitDate = strtotime($call['exitDate']);

			$datediff = $exitDate - $entryDate;

     		return (floor($datediff/(60*60*24)));

		}
	}


	

	

	// public function plPerLac($plPerUnit, $noOfUnits) {
	// 	return $plPerUnit*$noOfUnits;
	// }

	// public function grossROI($plPerUnit, $noOfUnits, $entryPrice) {
	// 	return $plPerUnit/$noOfUnits*$entryPrice;
	// }

	// public function finalResult($grossROI) {
	// 	if($grossROI<0){
	// 		return 'MISS';
	// 	}
	// 	else {
	// 		return 'HIT';
	// 	}
	// }




}



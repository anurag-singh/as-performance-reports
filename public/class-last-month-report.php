<?php

/**
 * holds all function which are responsible to generate last month reports
 *
 * @link       https://www.facebook.com/anuragsingh.me
 * @since      1.0.0
 *
 * @package    As_Performance_Reports
 * @subpackage As_Performance_Reports/public
 */

// WHERE   stockID  LIKE '".$colNameLike."%'

class Last_Month_Report {
	private $callsdetails;

    // function __construct($category) {


    // }

    public function get_array_of_call_type() {
    	global $wpdb;
        $today = date('Y-m-d');
        $startDate = date('Y-m-d',strtotime($today.'-1 day'));
        $endDate = date('Y-m-d',strtotime($startDate.'-30 day'));

        $calls = "SELECT *
                    FROM    wp_performance_report
                    WHERE   DATE(`lastUpdate`) BETWEEN '".$endDate."' AND '".$startDate."'
                    ";

        $callsdetails = $wpdb->get_results($calls, ARRAY_A);

        return $callsdetails;

    }

	public function get_total_calls($callsdetails, $category) {
	       $counter = 0;
	       foreach($callsdetails as $sin) {
	           $value = $sin['stockCat'];

	           if($value == $category) {
	               $counter++;
	           }
	       }
	   return $counter;
	}

	public function date_diffence($entryDate, $exitDate) {
		
     $datediff = $entryDate - $exitDate;
     return floor($datediff/(60*60*24));

	}

	public function timePeriod($tradingCalls) {
		foreach ($tradingCalls as $call) {

			//print_r($call);

			echo $entryDate = strtotime($call['entryDate']);
			echo ' : ';
			echo $exitDate = strtotime($call['exitDate']);
			echo '<br>';

			$datediff = $entryDate - $exitDate;
     		
     		echo (floor($datediff/(60*60*24)));

		}
	}

	public function atimePeriod($start, $end)
	{
		   $start_ts = strtotime($start);
			  $end_ts = strtotime($end);
			  $diff = $end_ts - $start_ts;
			  return round($diff / 86400);
		}


	public function __timePeriod($callsdetails) {
		foreach ($callsdetails as $singlecall) {
			// echo $entryDate = $singlecall['entryDate'];
			// echo " :	 ";
			// echo $exitDate = $singlecall['exitDate'];
			// echo " | ";

			$entryDate = new DateTime($singlecall['entryDate']);
			$exitDate = new DateTime($singlecall['exitDate']);

			$entryDate = date_format($entryDate, 'Y-m-d');
			$exitDate = date_format($exitDate, 'Y-m-d');

			echo $entryDate. '|' . $exitDate . '<br>';


			


			
	echo  date_diff($exitDate, $entryDate);
		}
		
	}


}



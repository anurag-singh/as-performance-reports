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


	public function timePeriod($tradingCalls) {
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




}



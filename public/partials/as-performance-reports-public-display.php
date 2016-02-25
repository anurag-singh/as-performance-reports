<?php

$reportFrontEnd = new Last_Month_Report();

$tradingCalls = $reportFrontEnd->get_array_of_call_type();

// echo '<pre>';
// print_r($tradingCalls);
// echo '<pre>';

// foreach ($tradingCalls as $call) {
// 	print_r($call);
// }

$countTradingcalls = $reportFrontEnd->get_total_calls($tradingCalls, 'Trading');

//print_r($countTradingcalls);

$countPositionalcalls = $reportFrontEnd->get_total_calls($tradingCalls, 'Positional');

echo $reportFrontEnd->timePeriod($tradingCalls);

echo $reportFrontEnd->noOfUnits($tradingCalls);

echo $reportFrontEnd->plPerUnit($tradingCalls);


?>
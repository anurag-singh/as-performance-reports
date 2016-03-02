<?php
	
	class Report_Formulas {

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

		$plPerUnit = number_format((float)$plPerUnit, 2, '.', '');

		return $plPerUnit;
	}

	protected function plPerLac($plPerUnit, $noOfUnits) {
		return $plPerUnit*$noOfUnits;
	}

	protected function grossROI($plPerLac, $noOfUnits, $entryPrice) {
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

	protected function roiOnInvestment($netProfitLoss, $totalInvestment) {
		$roiOnInvestment = $netProfitLoss / $totalInvestment;
		return number_format($roiOnInvestment, 4 , '.', '');
	}

	protected function totalAverageTimePeriod($timePeriod, $totalCallsgiven) {
		$totalTimePeriod = $this->totalAverageTimePeriod += $timePeriod;
		if($totalCallsgiven>0) {
			$average = $totalTimePeriod / $totalCallsgiven;
			return number_format($average, 4, '.', ' %');
		}
	}

	protected function annualisedROI($netProfitLoss, $totalInvestment, $totalAverageTimePeriod) {
		$secondNo =   $totalInvestment * $totalAverageTimePeriod / 365;
		$annualisedROI = $netProfitLoss / $secondNo;
		return number_format((float)$annualisedROI, 4, '.', '');
	}

	protected function successPercentage($totalCallsgiven, $totalHits) {
		if($totalHits>0){
			$successRate = ($totalCallsgiven /  $totalHits) * 100;
			return number_format($successRate, 1, '.', ''). ' %';
		}
	}
}

?>
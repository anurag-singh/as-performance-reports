<?php
/*
Page : page-performance-report.php
*/
get_header();
$reportFrontEnd = new Last_Month_Report();
//print_r($$allcalltype);
$allCallTypes = $reportFrontEnd->display_data_in_tabular_format($reportFrontEnd);
// echo $single.'<pre>';
// print_r($allCallTypes);
// echo '</pre>';
// Get the sum of each column seperatly
//$overallAllCallTypes = $reportFrontEnd->get_list_of_all_call_types();
// echo $single.'<pre>';
// print_r($overallAllCallTypes);
// echo '</pre>';
?>
	<!-- ********************************* -->
	<h2>Our Last 1 month Performance Score Card</h2>
    <div class="tbl-ovr-flo">
        <table bgcolor="#f1f2f2" border="1" bordercolor="#fff" cellpadding="7" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <td> </td>
                    <td>Calls Given</td>
                    <td>Hits</td>
                    <td>Misses</td>
                    <td>Pending Status</td>
                    <td>Success %</td>
                    <td>ROI % on Investment Period</td>
                    <td>Annualised ROI %</td>
                </tr>
                <tr>
                </tr>
            </thead>
            <tbody>
			<?php
			
				foreach ($allCallTypes as $single) {
				?>
				<tr style="background-color: rgb(252, 252, 252);">
					<td><?php echo $single['category']; ?></td>
			        <td><?php echo $single['callsGiven']; ?></td>
			        <td><?php echo $single['totalHits']; ?></td>
			        <td><?php echo $single['totalMisses']; ?></td>
			        <td><?php echo $single['totalPendings']; ?></td>
			        <td><?php echo $single['success']; ?></td>
			        <td><?php echo $single['roiOnInvestment']; ?></td>
			        <td><?php echo $single['annualisedROI']; ?></td>
			    </tr>
			<?php 
			
			}
			?>
			</tbody>
		</table>
		<hr><hr><hr><hr>
	</div>
	<!-- ********************************* -->

	<!-- ********************************* -->   
	<h2>Performance Tracker - Equity Trading Calls</h2>                 
    <div class="tbl-ovr-flo">
		<table width="100%" cellspacing="0" cellpadding="7" bordercolor="#fff" border="1" bgcolor="#f1f2f2">
			<thead>
				<tr>
				  <td>Calls Given</td>
				  <td>Hits</td>
				  <td>Misses</td>
				  <td>Pending Status</td>
				  <td>Success %</td>
				  <td>ROI % on Investment Period</td>
				  <td>Annualised ROI %</td>
				</tr>
			</thead>
			<tbody>				
				<tr style="background-color: rgb(252, 252, 252);">
					<td class="callsGiven"></td>
					<td class="totalHits"></td>
					<td class="totalMisses"></td>
					<td class="totalPendings"></td>
					<td class="successPercentage"></td>
					<td class="roiOnInvestment"></td>
					<td class="annualisedROI"></td>
				</tr>
				<tr></tr>
				<tr>
					<td colspan="7"></td> 
				</tr>
			</tbody>
		</table>
	</div>
	<div class="tbl-ovr-flo _moveHere">
		<table id="all-single-type-calls-grid"  width="100%" cellspacing="0" cellpadding="7" bordercolor="#fff" border="1" bgcolor="#f1f2f2">
				<thead>
					<tr>
						<th>Stocks</th>
		                <th>Date</th>
		                <th>Buy/Sell</th>
		                <th>Entry Price</th>
		                <th>Target Price</th>
		                <th>Stop Loss</th>
		                <th>Exit Price</th>
		                <th>P/L Per Unit</th>
		                <th>P/L Per Lac</th>
		                <th>Gross ROI%</th>
		                <th>Final Result</th>
					</tr>
				</thead>
		</table>
	</div>
	<!-- ********************************* -->

	


<?php
get_footer();
?>
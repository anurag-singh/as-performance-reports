$(document).ready(function($) {
var dataTable = '';

         dataTable = $('#all-single-type-calls-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :"../wp-content/plugins/as-performance-reports/public/class-all-calls-grid-data.php", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".all-single-type-calls-grid-error").html("");
                    $("#all-single-type-calls-grid").append('<tbody class="all-single-type-calls-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#all-single-type-calls-grid_processing").css("display","none");
 
                }
            }

        } ).on('xhr.dt', function ( e, settings, data, xhr ) {
        //alert(data.summarisedSnapshot['callsGiven']);
        $('.callsGiven').html(data.summarisedSnapshot['callsGiven']);
        $('.totalHits').html(data.summarisedSnapshot['totalHits']);
        $('.totalMisses').html(data.summarisedSnapshot['totalMisses']);
        $('.totalPendings').html(data.summarisedSnapshot['totalPendings']);
        $('.successPercentage').html(data.summarisedSnapshot['successPercentage']);
        $('.roiOnInvestment').html(data.summarisedSnapshot['roiOnInvestment']);
        $('.annualisedROI').html(data.summarisedSnapshot['annualisedROI']);
    } );



		/* Add scroll functionality */			
        $('.getCategory').on('click',function(e){
        	$('._moveHere').ScrollTo({
		      duration: 1000,
		      durationMode: 'all'
		    });
		/* Add scroll functionality */			
		    
		var category = $(this).find('a').attr('rel');
		$('.getCategory').removeClass('current-menu-item');
		$(this).addClass('current-menu-item');
		dataTable.destroy();
		if(category!='')
		{
			dataTable = $('#all-single-type-calls-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :"../wp-content/plugins/as-performance-reports/public/class-all-calls-grid-data.php", // json datasource
                type: "post",  // method  , by default get
                data : {cat:category},
                error: function(){  // error handling
                    $(".all-single-type-calls-grid-error").html("");
                    $("#all-single-type-calls-grid").append('<tbody class="all-single-type-calls-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#all-single-type-calls-grid_processing").css("display","none");
 
                	}
           		 }
       		 } ).on('xhr.dt', function ( e, settings, data, xhr ) {
			        //alert(data.summarisedSnapshot['callsGiven']);
			        $('.callsGiven').html(data.summarisedSnapshot['callsGiven']);
			        $('.totalHits').html(data.summarisedSnapshot['totalHits']);
			        $('.totalMisses').html(data.summarisedSnapshot['totalMisses']);
			        $('.totalPendings').html(data.summarisedSnapshot['totalPendings']);
			        $('.successPercentage').html(data.summarisedSnapshot['successPercentage']);
			        $('.roiOnInvestment').html(data.summarisedSnapshot['roiOnInvestment']);
			        $('.annualisedROI').html(data.summarisedSnapshot['annualisedROI']);
			    } );

		}
		return false;
	});



    } );




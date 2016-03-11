(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

$(document).ready(function($) {
var dataTable = '';

         dataTable = $('#all-single-type-calls-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :"../wp-content/plugins/as-performance-reports/public/all-single-call-type-grid-data.php", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".all-single-type-calls-grid-error").html("");
                    $("#all-single-type-calls-grid").append('<tbody class="all-single-type-calls-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#all-single-type-calls-grid_processing").css("display","none");
 
                }
            }
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
                url :"../wp-content/plugins/as-performance-reports/public/all-single-call-type-grid-data.php", // json datasource
                type: "post",  // method  , by default get
                data : {cat:category},
                error: function(){  // error handling
                    $(".all-single-type-calls-grid-error").html("");
                    $("#all-single-type-calls-grid").append('<tbody class="all-single-type-calls-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#all-single-type-calls-grid_processing").css("display","none");
 
                	}
           		 }
       		 } );
		}
		return false;
	});



    } );




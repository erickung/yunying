/* [ ---- Gebo Admin Panel - datatables ---- ] */

var xss = function tt(fnCallback)
{
		alert(fnCallback);
    	return fnCallback;
    


}

	$(document).ready(function() {
		//* basic
		gebo_datatbles.dt_d();
	});
	
	//* calendar
	gebo_datatbles = {
		dt_d: function() {
		
			function fnShowHide( iCol ) {
				/* Get the DataTables object again - this is not a recreation, just a get of the object */
				var oTable = $('#dt_d').dataTable();
				 
				var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
				oTable.fnSetColumnVis( iCol, bVis ? false : true );
			};
			
			$('#dt_d_nav').on('click','li input',function(){
				fnShowHide( $(this).val() );
			});
			
			var oTable = $('#dt_d').dataTable({
				"bPaginate" : false,
				"bInfo" : false,
				"bSort" : false,
				"bFilter": false ,
				/*
				"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
				"sPaginationType": "bootstrap_bms",
				"bFilter": false ,
				"bLengthChange": true,
				//"bProcessing": true,
				"processing": true,
				//"bPaginate" : false,
				//"bInfo" : false,
				"sAjaxSource": "/admin/user/userlist",
				"bStateSave": true ,
				"aoColumns": [
								{ "bSortable": false },
								{ "bSortable": false },
								{ "sType": "string" },
								{ "sType": "string" },
								{ "sType": "string" },
								{ "bSortable": false }
							]
				
				"bStateSave": true ,
				"bProcessing": true,
				"bServerSide": true,
				//"sAjaxSource": "/admin/user/userlist",
					
				"ajax" : {
					 "url": "/admin/user/userlist",
					 //"success" : json,
					
				}
			
				"fnDrawCallback": function() {
					  //bind the click handler script to the newly created elements held in the table
						$('.flagsmileysad').bind('click',flagsmileysadclick);
					}
				
				//"ajax": "/admin/user/userlist"
			    "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) { 
			        oSettings.jqXHR = $.ajax( { 
			          "dataType": 'json', 
			          "type": "POST", 
			          "url": sSource, 
			          "data": aoData, 
			          "success": xss(aoData) 
			        } ); 
			      } 
				  */
			});
		}
	};
<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('_partials/header');
?>
<style type="text/css">
	
.even
{
	background: green;
}
td.custom-cell {
  color: #fff;
  background-color: #37bc6c;
}
.custom-table thead th:nth-child(even),
.custom-table tbody tr:nth-child(odd) th {
  background-color: #d7f1e1;
}
.handsontable td
{
	color: black!important;
}
.MyRow
{
	color: black!important;
}
.backgrn1{
	    background-color: #deffde !important;
	  }
.backgrn2{
	    background-color : #ffe1e5 !important;
	  }
.backgrn3{
	    background-color: #fee0d7 !important;
	  }
</style>
<!-- Main Content -->
<div class="content-page">
	<div class="content">

		<div class="container">
			<div class="row">
				<div class="row">
					<div class="col-xs-12">
						<div class="page-title-box">
							<h4 class="page-title">User Upload</h4>
						
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" id="intraFormRow">
		<input type="hidden" name="insertID" id="insertID" value="<?php echo $id; ?>">
		<input type="hidden" name="year" id="year">
		<input type="hidden" name="month" id="month">
		<input type="hidden" name="user_type" id="user_type" value="<?php echo $this->session->userdata('user_type') ?>">
		<div class="col-lg-12">
			<div class="card-box">
		<div class="col-md-12">
			<div class="col-md-6 text-dark"><i class="fa fa-calendar"></i> Year - <span id="year_name"></span></div>
			<div class="col-md-6 text-dark"><i class="fa fa-calendar"></i> Month - <span id="month_name"></span></div>
			<hr/>
		</div>
	</div>
</div>
		
		<div class="col-lg-12 bg-white">
			<div class="col-md-12">
				<button type="button" id="intraBtn" class="btn btn-primary" style="float: right;margin-bottom: 10px;" onclick="saveCopyIntraData()">Save</button>
			</div>
			<div id="newDiv"></div>
			
		</div>
	</div>
	
	

</div>


</div>
<?php $this->load->view('_partials/footer'); ?>

</div>
<script type="text/javascript">
	var base_url='<?php echo base_url(); ?>';
</script>
<script src="<?php echo base_url();?>assets/js/module/upload_data/financial_data.js"></script>


<script type="text/javascript">
	$(document).ready(function(){
		getIntraInfoById();
		// getBranchList();
		// intraTransactionTable();
		loadEditableTable();
		var user_type=$("#user_type").val();
		// if(user_type!=2)
		// {
		// 	var fromBranch=$("#from_branch_id").val();
		// 	getBranchGlAccount(fromBranch,1);
		// }
	});
	function  getIntraInfoById() {
		$.LoadingOverlay("show");
		var formData=new FormData();
		formData.set('insertID',$("#insertID").val());
		app.request(base_url + "getIntraInfoById",formData).then(res=>{
		$.LoadingOverlay("hide");
			if(res.status == 200) {
				var userdata=res.data;
				$("#year").val(userdata.year);
				$("#month").val(userdata.quarter);
				$("#year_name").html(res.year);
				$("#month_name").html(res.month);
			}
		}).catch(e => {
		$.LoadingOverlay("hide");
			console.log(e);
		});
	}
	function  getBranchList() {
		app.request(base_url + "getCompanyBranchList",null).then(res=>{
			if(res.status == 200) {
				$('#from_branch_id').append(res.data);
				$('#to_branch_id').append(res.data);
			}
		}).catch(e => {
			console.log(e);
		});
	}
	function getBranchGlAccount(branch)
	{
		return new Promise(function (resolve,reject){
		$.ajax({
			url: base_url + "getBranchGlAccount",
			type: "POST",
			dataType: "json",
			data:{branch:branch},
			success: function (result) {
				resolve(result.data);
			},
			error: function (error) {
				console.log(error);
				// $.LoadingOverlay("hide");
			}
		});
		});
	}

	$("#exportexcelsheet").validate({
		rules: {
			from_branch_id: {
				required: true
			},
			from_gl_account: {
				required: true
			},
			to_branch_id: {
				required: true
			},
			to_gl_account: {
				required: true
			},
			amount: {
				required: true
			}
		},
		messages: {
			from_branch_id: {
				required: "Please select branch",
			},
			from_gl_account: {
				required: "Please select gl_account",
			},
			to_branch_id: {
				required: "Please select branch",
			},
			amount: {
				required: "Enter Amount",
			}
		},
		errorElement: 'span',
		submitHandler: function (form) {
		$.LoadingOverlay("show");
			// $.LoadingOverlay("show");
			
			var formData=new FormData(form);
			// formData.set('insertID',$("#insertID").val());
			$.ajax({
				url: base_url+"uploadIntraTransaction",
				type: "POST",
				dataType: "json",
				data: formData,
				processData: false,
        		contentType: false,
				success: function (result) {
		$.LoadingOverlay("hide");
					if (result.status === 200) {
		               toastr.success(result.body);
						$("#amount").val("");
		               intraTransactionTable()
		            }
		            else
		            {
		            	toastr.error(result.body);
		            	// alert(result.body);
		            }
				}, error: function (error) {
		$.LoadingOverlay("hide");
					// $.LoadingOverlay("hide");
					toastr.error("Something went wrong please try again");
				}

			});
		}
	});
	let debitCheck=0;
	let creditCheck=0;
	// let columnRows=[];
	// let columnsHeader=[];
	function loadEditableTable()
	{
		$.LoadingOverlay("show");
		var formData=new FormData();
		formData.set('id',$("#insertID").val());
		$.ajax({
	        url: base_url + "getIntraTableData",
	        type: "POST",
	        dataType: "json",
	        data: formData,
	        processData: false,
        	contentType: false,
	        success: function (result) {
		$.LoadingOverlay("hide");
	        	var branchName=result.branchName;
	        	var rows = [
						['','', '', '', '', '','','',''],
					];
					if($("#user_type").val()!=2)
					{
						rows = [
							[branchName,'', '', '', '', '','','',''],
						];
					}
	            if (result.status === 200) {
	                // var columns=result.columns;
	                 rows=result.rows;
	                //  var types=result.types;
	                 // columnRows=rows;
	                 // columnsHeader=columns;
	            	// createHandonTable(columns,rows,types,'newDiv');
	            	// if(result.rows.length>0)
	             //    {
	             //    	 rows=result.rows;
	             //    }
	            }
	            
	            var dataschema=result.dataSchema;
	            // console.log(dataschema);
	           var types=result.type;
	           var columns=result.columns;
	            createHandonTable(columns,rows,types,'newDiv',dataschema);
	        },
	        error: function (error) {
		$.LoadingOverlay("hide");
	            console.log(error);
	           // $.LoadingOverlay("hide");
	        }
	    });
		
	}
	 function firstRowRenderer1(instance, td) {
	    td.style.background = '#deffde';
	    td.style.color = 'black';
	  }
	 function firstRowRenderer2(instance, td) {
	    td.style.background = '#ffe1e5';
	  }
	 function firstRowRenderer3(instance, td) {
	    td.style.background = '#fee0d7';
	  }

let hotDiv;
function createHandonTable(columnsHeader,columnRows,columnTypes,divId,dataschema)
{

 var element=document.getElementById(divId);
  hotDiv !=null ? hotDiv.destroy():'';
 hotDiv= new Handsontable(element, {
				  data:columnRows,
				  colHeaders: columnsHeader,
				  manualColumnResize: true,
				  manualRowResize :true,
				  // columns: [
				  //  { type: 'text' },
				  //   { type: 'dropdown',source:[1,2,3,4] ,validator: function(value, callback) {
				  //     	if (/^(\d|\-)*$/.test(value)) { 
				  //       	callback(true);
				  //     	} else {
				  //       	callback(false);
				  //     	}
				  //   	}
				  // 	},
				  //   { type: 'text' },
				  //   { type: 'text' },
				  //   { type: 'text' },
				  //   { type: 'date', dateFormat: 'M/D/YYYY' }
				  // ],
				  // cells: function (row,col) {
				  //     var cp = {};
				      
				  //     if (col === 0 || col === 1 || col === 2 || col === 3|| col === 4 || col === 5 || col === 6) {
				  //       cp.renderer = firstRowRenderer1; 
				  //     }
				  //     if (col === 7 || col === 8 || col === 9 || col === 10 || col === 11 || col === 12  || col === 13) {
				  //       cp.renderer = firstRowRenderer2; 
				  //     }
				  //     if (col === 14) {
				  //       cp.renderer = firstRowRenderer3; 
				  //     }
				      
				  //     return cp
				  //   },
				  columns:columnTypes,
				  dataSchema:dataschema,
				   

				 // cells: function(row, col){
				 //    	var cp = {}
				      

				 //      let com=this.instance.getData();
				 //      console.log(com.length);
				 //      for(var i;i<com.length;i++)
				 //      {
				 //      	console.log(i);
				 //      }
				 //      	cp.className='even';
				      
				 //      return cp
				 //    },
				 // cell : function(row, col, prop) {
					// var cellProperties: any = {};
					// cellProperties.className = 'even';
					// }
				// cell: [
				//     {
				//       row: 0,
				//       col: 0,
				//       className: 'custom-cell',
				//     },
				//   ],
				afterCreateRow: function(row, amount, src) {
			      for (var i = 0; i < this.countCols(); i++) {
			        this.setCellMeta(row, i, 'className', 'MyRow')
			      }
			    },
				 beforeCut: function(data, coords) {
				 	console.log(1);
				 },
				  beforeChange: function (changes, source) {

				  	// hotDiv.render();
				  	  var row = changes[0][0];

				        var prop = changes[0][1];

				        var value = changes[0][3];
				   //      if(prop==0)
				   //      {
				   //      	getBranchGlAccount(value).then(e=>{
				   //      		hotDiv.setcellmeta(0,1,'typw', 'dropdown');
				   //      		hotDiv.setcellmeta(0,1,'source', e);
							// 	// this.setDataAtRowProp(row,1,e);
							// });
				   //       // this.setDataAtRowProp(row,2,"supriya");
				   //      }
				        // console.log(changes,row,prop,value);
				      
				  },
				  afterChange: function(changes, src){
				  	// console.log(changes);
				    if(changes){
				    	
				    	var row = changes[0][0];
				    	var value = changes[0][3];
				    	 var prop = changes[0][1];
				    	 if(prop==0)
				    	 {
				    	 	this.setCellMeta(row,1, 'type', 'dropdown');
				      		var data=getBranchGlAccount(value).then(e=>{;
				      			this.setCellMeta(row,1, 'source', e);
				      		});
				        	
				      
				        	this.render();
				    	 }
				    	 if(prop==7)
				    	 {
				    	 	this.setCellMeta(row,8, 'type', 'dropdown');
				      		var data=getBranchGlAccount(value).then(e=>{;
				      			this.setCellMeta(row,8, 'source', e);
				      		});
				        	this.render();
				    	 }
				    	 if(prop==1)
				    	 {
				    	 	this.setCellMeta(row,2, 'type', 'text');
				      		var data=getBranchGlAccountSeperate(value).then(e=>{;
				      			// console.log(glc_value);
				      			// this.setCellMeta(row,2, 'source', e);
				      			this.setDataAtRowProp(row,2,e);
				      		});
				        	
				        	this.render();
				    	 }
				    	 if(prop==8)
				    	 {
				    	 	this.setCellMeta(row,9, 'type', 'text');
				      		var data=getBranchGlAccountSeperate(value).then(e=>{;
				      			// this.setCellMeta(row,5, 'source', e);
				      			this.setDataAtRowProp(row,9,e);
				      		});
				        	
				        	this.render();
				    	 }
				      		
				      }
				    },
				   stretchH: 'all',
				   colWidths: '100%',
				    width: '100%',
				    height: 320,
				    rowHeights: 23,
				    rowHeaders: true,
				    filters: true,
				    contextMenu: true,
				    hiddenColumns: {
					    // specify columns hidden by default
					    columns: [2,9]
					  },
					  
				    dropdownMenu: ['filter_by_condition', 'filter_action_bar'],
				    licenseKey: 'non-commercial-and-evaluation'
				});
 	hotDiv.validateCells();
 // 	hotDiv.addHook('afterCreateRow', (row, amount) => {
	//   console.log(`1`);
	// })
 	hotDiv.updateSettings({
 		cells(row, col) {
		   if (col === 0 || col === 1 || col === 2 || col === 3|| col === 4 || col === 5 || col === 6) {
		      return {
		        className:'backgrn1',
		      };
		    }
		    if (col === 7 || col === 8 || col === 9 || col === 10 || col === 11 || col === 12  || col === 13) {
		      return {
		        className:'backgrn2',
		      };
		    }
		    if (col === 14) {
		      return {
		        className:'backgrn3',
		      };
		    }
		  }
	});
}
function saveCopyIntraData()
{
		$.LoadingOverlay("show");
	var data = hotDiv.getData();
	let formData = new FormData();
	formData.set('arrData', JSON.stringify(data));
	formData.set('insertID', $("#insertID").val());
	app.request(base_url + "saveCopyIntraData",formData).then(res=>{
		$.LoadingOverlay("hide");
		// data=res.data2;
		// console.log(res);
		if(res.status==200)
		{
			toastr.success(res.body);
			loadEditableTable();
			// $("#insertID").val('');
		    // $("#branchID").val('');
		    // document.getElementById("exportexcelsheet").reset();
		    // $("#newDiv").html('');
		    // document.getElementById('newDiv').style.height=null;
		    // $("#finacialBtn").hide();
		}
		else
		{
			toastr.error(res.body);
		}
		
	});
}

function getBranchGlAccountSeperate(glc_value)
{
	
	return new Promise(function (resolve,reject){
	let glcNumber='';
	if(glc_value!="")
	{
		let glcData=glc_value.split('-');
		if(glcData.length>1)
		{
			glcNumber=glcData[0];

		}

	}

		resolve(glcNumber);
	});
}

</script>


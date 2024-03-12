<?php 
/**
 *	File        : masterdata/myprodt-invent-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Sept 17, 2017
 * 	last update : Sept 17, 2017
 * 	description : Product Type Inventory Records
 */
 
$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->mymdacct = model('App\Models\MyDRRCVModel');
$cuser   = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();
$cusergrp = $this->myusermod->mysys_usergrp();
$cuserrema = $this->myusermod->mysys_userrema();
$txtsearchedrec = $request->getVar('txtsearchedrec');

$fld_d2dtfrm    = $request->getVar('fld_d2dtfrm'); 
$fld_d2dtto     = $request->getVar('fld_d2dtto'); 
$fld_d2brnch    = $request->getVar('fld_d2brnch');
$fld_brancharea = $request->getVar('fld_brancharea');
$data = array();
$memodule = 'mydashb_dr_rcvng_recs';

$str_style='';
$reporttype = $request->getVar('report');


$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

?>

<style>
	table.memetable, th.memetable, td.memetable {
		border: 1px solid #F6F5F4;
		border-collapse: collapse;
	}
	thead.memetable, th.memetable, td.memetable {
		padding: 6px;
	}
</style>


<?=form_open('mydashb-dr-rcvng-vw','class="needs-validation-search" id="myfrmsearchrec" ');?>

    <div class="col-md-6 mb-1">
        <div class="input-group input-group-sm">
            <label class="input-group-text fw-bold" for="search">Search:</label>
            <input type="text" id="txtsearchedrec" class="form-control form-control-sm" name="txtsearchedrec" placeholder="Search" value="<?=$txtsearchedrec;?>"/>
            <button type="submit" class="btn btn-dgreen btn-sm" style="background-color:#167F92; color:#fff;"><i class="bi bi-search"></i></button>
			<?=anchor('mydashb-dr', 'Reset',' class="btn btn-primary" ');?>
        </div>
    </div>
<?=form_close();?> <!-- end of ./form -->
<div class="col-md-8">
    <?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
</div>

<div class="d-flex justify-content-end">
	<input type="hidden" name="txt-report" id="txt-report" value="<?=$reporttype;?>">
	<input type="hidden" name="fld_d2brnch" id="fld_d2brnch" class="fld_d2brnch" value="<?=$fld_d2brnch;?>">
	<input type="hidden" name="fld_d2dtfrm" id="fld_d2dtfrm" class="fld_d2dtfrm" value="<?=$fld_d2dtfrm;?>">
	<input type="hidden" name="fld_d2dtto" id="fld_d2dtto" class="fld_d2dtto" value="<?=$fld_d2dtto;?>">
	<input type="hidden" name="fld_brancharea" id="fld_brancharea" class="fld_brancharea" value="<?=$fld_brancharea;?>">
<?php if(!empty($fld_d2dtto) && !empty($fld_d2dtfrm)):?>	
<button class="btn btn-success btn-sm btn_claims_dll"  id="btn_claims_dll" type="button">Download</button>
<?php endif;?>
</div>
<div class="table-responsive mt-2">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<table class="table table-striped table-bordered table-sm table-condensed">
			<thead>
				<tr>
					<?php if($reporttype == '3' || $reporttype == '4' || $reporttype == '6' || $reporttype == '10' || $reporttype == '7' || $reporttype == '8' || $reporttype == '11' || $reporttype == '9'): ?>
					<th> <i class="fa fa-cog"></i></th>
					<th>View</th>
					<?php endif; ?>
					<?php if($reporttype == '5'): ?>
					<th colspan="2"> <i class="fa fa-cog"></i></th>
					<th>View</th>
					<?php endif; ?>
					<th>Transaction No</th>
					<th>Company</th>
					<th>Area Code</th>
					<th>Supplier</th>
					<th>DR Qty</th>
					<th <?=$str_style;?>>Total DR Cost</th>
					<th>Total DR SRP</th>
					<th>DR No</th>
					<th>DR Date</th>
					<th>Received Date</th>
					<th>Date In</th>
					<th>Claim Date</th>
					<?php if($reporttype == '3' || $reporttype == '4' || $reporttype == '6' || $reporttype == '8' || $reporttype == '9' || $reporttype == '7' || $reporttype == '10' || $reporttype == '11'): ?>
					<th>Validated Date</th>
					<th>Validated By</th>
					<th>Final Date</th>
					<?php endif; ?>
					<th>User</th>
					<th>S/M Flag</th>	
					<th>D/F Tag</th>
					<th>Y/N Posted</th>
					<th>Remarks</th>
					<th>Encoded Date</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if($rlist !== ''):
					$nn = 1;
					foreach($rlist as $row): 

						$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
						$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
						$mtkn_trxno = hash('sha384', $row['recid'] . $mpw_tkn);
						$dis = ($row['post_tag'] == 'Y' || $row['df_tag'] == 'D' ? "disabled" : '');
						$mtknImg = './uploads/rcv_claims/'.$row['claim_rcpt'];
						$mtknImg2 = '/uploads/rcv_claims/'.$row['claim_rcpt'];
						$crpl = null;

						if ($this->mymdacct !== null) {
							$crpl = $this->mymdacct->get_crplData($row['drno']);
						}
						
						$crpl = ($crpl === null) ? 'N' : $crpl;
						
						$trxno = $row['trx_no'];
						$mprevent = '';
				?>
						<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
							<?php 
							if($reporttype == '3' || $reporttype == '4' || $reporttype == '6' || $reporttype == '10' || $reporttype == '7' || $reporttype == '8' || $reporttype == '11' || $reporttype == '9'): ?>
							<td>
							<?php 
								if($cuserrema != 'B'): 
									if($row['claimdateCount'] > 13):
										echo anchor('dr-trx?file=claims&mtkn_trxno=' . $mtkn_trxno, '<i class="bi bi-send"></i> ',' Title="View Claims" class="btn btn-info btn-xs " ');
									elseif($row['claimdateCount'] <=13 && $row['claim_tag'] == 'Y' && $crpl == 'N' AND ($row[`supplier_id`] = '3' OR $row[`supplier_id`] = '1425' OR $row[`supplier_id`] = '4773' OR $row[`supplier_id`] = '7805')): 
										echo anchor('dr-trx?file=claims&mtkn_trxno=' . $mtkn_trxno, '<i class="bi bi-send"></i> ',' Title="File for Claims" class="btn btn-info btn-xs " ');
									else: ?>
									<button class=" btn btn-sm btn-secondary disabled"> <i class="bi bi-send"></i> </button>
								<?php endif; 
								  endif; ?>
							</td>
						

							<td><a  style="font-size: 10px;"  onclick ="window.open('<?=site_url().$mtknImg;?>')" class="btn btn-info btn-sm text-white" title = "View"><i class="bi bi-file-earmark"></i> View </a></td>
							<?php endif; ?>

							<?php 
							if($reporttype == '5' ): ?>
							<td>
								<button class="btn btn-info btn-sm btn_claims_val " onclick="btn_claims_val('<?=$mtkn_trxno?>','<?=$row['trx_no'];?>')" Title="Claims Validate" value="<?=$mtkn_trxno?>"> <i class="bi bi-send"></i></button>
								

							</td>
							<td>
							<button class="btn btn-success btn-sm btn_claims_val_dl " id="btn_claims_val_dl<?=$nn?>" onclick="btn_claims_val_dl('<?=$mtkn_trxno?>','<?=$row['trx_no'];?>','btn_claims_val_dl<?=$nn?>')" Title="Download" value="<?=$mtkn_trxno?>"> <i class="bi bi-box-arrow-down"></i></button>	
							</td>
							<td><button class="btn btn-info btn-sm" onclick="window.open('<?=site_url().$mtknImg;?>')"> <i class="bi bi-eye"></i></button></td>
							
							<?php endif; ?>
							<?php if($reporttype =='12'):?>
							<!-- <td><button id="btn_forcountered" class="btn btn-info btn-sm btn_forcountered" data-trxno ="<?=$row['trx_no'];?>"><i class="bi bi-check-circle"></i></button></td> -->
							<td nowrap="nowrap"><button class="btn btn-sm btn-success  btn_forcountered" type="button" data-trxno="<?=$row['trx_no'];?>" name="btn_forcountered<?=$memodule . $mprevent;?>[]"><i class="bi bi-check-circle"></i></button></td>
							<?php endif;?>
							<td nowrap="nowrap"><?=$row['trx_no'];?></td>
							<td nowrap="nowrap"><?=$row['COMP_NAME'];?></td>
							<td nowrap="nowrap"><?=$row['BRNCH_NAME'];?></td>
							<td nowrap="nowrap"><?=$row['VEND_NAME'];?></td>
							<?php if($reporttype == '12'):?>
								<td nowrap="nowrap"><?=number_format($row['QUANTITY'],2,'.',',');?></td>
								<td <?=$str_style;?> nowrap="nowrap"><?=number_format($row['COST'],2,'.',',');?></td>
								<td nowrap="nowrap"><?=number_format($row['AMOUNT'],2,'.',',');?></td>
							<?php else:?>
								<td nowrap="nowrap"><?=number_format($row['hd_subtqty'],2,'.',',');?></td>
								<td <?=$str_style;?> nowrap="nowrap"><?=number_format($row['hd_subtcost'],2,'.',',');?></td>
								<td nowrap="nowrap"><?=number_format($row['hd_subtamt'],2,'.',',');?></td>
							<?php endif;?>
							<td nowrap="nowrap"><?=$row['drno'];?></td>
							<td nowrap="nowrap"><?=$row['dr_date'];?></td>
							<td nowrap="nowrap"><?=$row['rcv_date'];?></td>
							<td nowrap="nowrap"><?=$row['date_in'];?></td>
							<td nowrap="nowrap"><?=$row['claim_date'];?></td>
							<?php if($reporttype == '3' || $reporttype == '4' || $reporttype == '6' || $reporttype == '8' || $reporttype == '9' || $reporttype == '7' || $reporttype == '10' || $reporttype == '11') : ?>
							<td nowrap="nowrap"><?=$row['validated_date'];?></td>
							<td nowrap="nowrap"><?=$row['validated_by'];?></td>
							<td nowrap="nowrap"><?=$row['final_date'];?></td>
							<?php endif; ?>
							<td nowrap="nowrap"><?=$row['muser'];?></td>
							<td nowrap="nowrap"><?=$row['hd_sm_tags'];?></td>
							<td nowrap="nowrap"><?=$row['df_tag'];?></td>
							<td nowrap="nowrap"><?=$row['post_tag'];?></td>
							<td nowrap="nowrap"><?=$row['hd_remarks'];?></td>
							<td nowrap="nowrap"><?=$row['encd_date']?></td>
						</tr>
						<?php 
						$nn++;
					endforeach;
				else:
					?>
					<tr>
						<td colspan="18">No data was found.</td>
					</tr>
					<?php 
				endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div id="dl_rpt" class="container-fluid">
	
		</div>
	</div> <!-- end col-md-12 -->
</div>
<?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
	echo $mylibzsys->memsgbox_yesno1('meyn3_' . $memodule,'','');
?>  

<script type="text/javascript"> 

	 __mysys_apps.mepreloader('mepreloaderme',false);

	 function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#txtsearchedrec').val();
			var mtkn_wshe_page = jQuery('#txt-wshe').val();
			var txt_warehouse = jQuery('#txt-warehouse').attr("data-id");
			var fld_d2brnch = jQuery('.fld_d2brnch').val();
			var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
			var fld_d2dtto = jQuery('.fld_d2dtto').val();
			var fld_brancharea = jQuery('.fld_brancharea').val();
			var report = jQuery('#txt-report').val();
		
            //mytrx_sc/mndt_sc2_recs
            var mparam = { 
            	txtsearchedrec: txtsearchedrec,
				mtkn_wshe_page:mtkn_wshe_page,
				txt_warehouse:txt_warehouse,
				report:report,
				fld_d2brnch:fld_d2brnch,
				fld_d2dtfrm:fld_d2dtfrm,
				fld_d2dtto:fld_d2dtto,
				fld_brancharea:fld_brancharea,
            	mpages: mobj 
            };	
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>mydashb-dr-rcvng-vw',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					$('#mydshbrdrecs').html(data);
					
					return false;
				},
				error: function() { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					
					return false;
				}	
			});			
			
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;

		}  //end try
	}	
	
	jQuery('#txtsearchedrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				__mysys_apps.mepreloader('mepreloaderme',true);
				var txtsearchedrec = jQuery('#txtsearchedrec').val();
				var mtkn_wshe_page = jQuery('#txt-wshe').val();
				var txt_warehouse = jQuery('#txt-warehouse').attr("data-id");
				var report = jQuery('#txt-report').val();
				var fld_d2brnch = jQuery('.fld_d2brnch').val();
				var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
				var fld_d2dtto = jQuery('.fld_d2dtto').val();
				var fld_brancharea = jQuery('.fld_brancharea').val();
				var mparam = {
					txtsearchedrec: txtsearchedrec,
					mtkn_wshe_page:mtkn_wshe_page,
					txt_warehouse:txt_warehouse,
					report:report,
					fld_d2brnch:fld_d2brnch,
					fld_d2dtfrm:fld_d2dtfrm,
					fld_d2dtto:fld_d2dtto,
					fld_brancharea:fld_brancharea,
					mpages: 1 
				};	

				jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mydashb-dr-rcvng-vw',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data)  { //display html using divID
						jQuery('#mydshbrdrecs').html(data);
						__mysys_apps.mepreloader('mepreloaderme',false);
						return false;
					},
					error: function() { // display global error on the menu function
						__mysys_apps.mepreloader('mepreloaderme',false);
						alert('error loading page...');
						return false;
					}	
				});	
			} catch(err) { 
				var mtxt = 'There was an error on this page.\n';
				mtxt += 'Error description: ' + err.message;
				mtxt += '\nClick OK to continue.';
				__mysys_apps.mepreloader('mepreloaderme',false);
				alert(mtxt);
				return false;
			}  //end try	
			
		}
	});	
	

	(function () {
		'use strict'

		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.needs-validation-search')
		// Loop over them and prevent submission
		Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}
				form.classList.add('was-validated') 

				try {
					event.preventDefault();
					event.stopPropagation();


					//start here
					try { 
						__mysys_apps.mepreloader('mepreloaderme',true);
						var txtsearchedrec = jQuery('#txtsearchedrec').val();
						var mtkn_wshe_page = jQuery('#txt-wshe').val();
						var txt_warehouse = jQuery('#txt-warehouse').attr("data-id");
						var report = jQuery('#txt-report').val();
						var fld_d2brnch = jQuery('.fld_d2brnch').val();
						var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
						var fld_d2dtto = jQuery('.fld_d2dtto').val();
						var fld_brancharea = jQuery('.fld_brancharea').val();
						
						var mparam = {
							txtsearchedrec: txtsearchedrec,
							mtkn_wshe_page:mtkn_wshe_page,
							txt_warehouse:txt_warehouse,
							report:report,
							fld_d2brnch:fld_d2brnch,
							fld_d2dtfrm:fld_d2dtfrm,
							fld_d2dtto:fld_d2dtto,
							fld_brancharea:fld_brancharea,
							mpages: 1 
						};	
						
						jQuery.ajax({ // default declaration of ajax parameters
							type: "POST",
							url: '<?=site_url();?>mydashb-dr-rcvng-vw',
							context: document.body,
							data: eval(mparam),
							global: false,
							cache: false,
							success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#mydshbrdrecs').html(data);
								
							},
							error: function() { // display global error on the menu function
								__mysys_apps.mepreloader('mepreloaderme',false);
								alert('error loading page...');
								
							}	
						});			
						
					} catch(err) { 
						__mysys_apps.mepreloader('mepreloaderme',false);
						var mtxt = 'There was an error on this page.\n';
						mtxt += 'Error description: ' + err.message;
						mtxt += '\nClick OK to continue.';
						alert(mtxt);
					}  //end try

					//end here



				} catch(err) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					var mtxt = 'There was an error on this page.\n';
					mtxt += 'Error description: ' + err.message;
					mtxt += '\nClick OK to continue.';
					alert(mtxt);
					return false;
				}  //end try					
			}, false)
		})
	})();	


	$('.btn_claims_dll').on('click',function(){
	try { 
	  //$('html,body').scrollTop(0);

		var fld_d2dtfrm = jQuery('#date_from').val();
		var fld_brancharea = jQuery('#branch_area').val();
		var fld_d2dtto  = jQuery('#date_to').val();
		var fld_d2brnch = '<?=$fld_d2brnch;?>';
		var rtp_type    = jQuery('#rtp_type').val();

		__mysys_apps.mepreloader('mepreloaderme',true);

		var mparam = {
			fld_d2dtfrm : fld_d2dtfrm,
			fld_brancharea:fld_brancharea,
			fld_d2dtto : fld_d2dtto,
			fld_d2brnch:fld_d2brnch,
			report:rtp_type
		}; 
	
	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mydashb-dr-rcvng-dl',
	  context: document.body,
	  data: eval(mparam),
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
			__mysys_apps.mepreloader('mepreloaderme',false);
			jQuery('#myModSysMsgBod').css({
				display: ''
			});
	        $('#dl_rpt').html(data)
	        // $('#dl_rpt').html(data);
	        
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  
	  return false;

	}  //end try
});

function btn_claims_val(fld_mktn,trxno){
	try { 
	  //$('html,body').scrollTop(0);

	  __mysys_apps.mepreloader('mepreloaderme',true);
	  var mparam = {
	  fld_mktn : fld_mktn,
	  trxno:trxno

	  }; 

	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mydashb-dr-validate',
	  context: document.body,
	  data: eval(mparam),
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
	        __mysys_apps.mepreloader('mepreloaderme',false);
	        $('#dl_rpt').html(data);
	        
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  
	  return false;

	}  //end try
}

function btn_claims_val_dl(fld_mktn,trxno,btn_id){
	try { 
	  //$('html,body').scrollTop(0);
	  __mysys_apps.mepreloader('mepreloaderme',true);
	  var mparam = {
	  fld_mktn : fld_mktn,
	  trxno:trxno,
	  btn_id:btn_id

	  }; 

	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mydashb-dr-validate-dl',
	  context: document.body,
	  data: eval(mparam),
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
	        __mysys_apps.mepreloader('mepreloaderme',false);
			jQuery('#myModSysMsgBod').css({
				display: ''
			});
	        $('#dl_rpt').html(data);
			
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  
	  return false;

	}  //end try
}

// $('.btn_forcountered').on('click',function(){
// 	try { 
// 	  //$('html,body').scrollTop(0);
// 	  __mysys_apps.mepreloader('mepreloaderme',true);
// 		var trxno = jQuery(this).attr('data-trxno'); 

// 		var mparam = {
// 			trxno:trxno
// 	  	}; 

// 	  $.ajax({ // default declaration of ajax parameters
// 	  type: "POST",
// 	  url: '<?=site_url();?>mydashb-dr-forcountered',
// 	  context: document.body,
// 	  data: eval(mparam),
// 	  global: false,
// 	  cache: false,
// 	    success: function(data)  { //display html using divID
// 	        __mysys_apps.mepreloader('mepreloaderme',false);
// 			jQuery('#memsgtestent_bod').html(data);
// 			jQuery('#memsgtestent').modal('show');
// 			$('#btn_forcountered').prop('disabled', true);
// 	        return false;
// 	    },
// 	    error: function() { // display global error on the menu function
// 	      alert('error loading page...');
	      
// 	      return false;
// 	    } 
// 	  });     
	            
// 	} catch(err) {
// 	  var mtxt = 'There was an error on this page.\n';
// 	  mtxt += 'Error description: ' + err.message;
// 	  mtxt += '\nClick OK to continue.';
// 	  alert(mtxt);
	  
// 	  return false;

// 	}  //end try
// });


jQuery('[name="btn_forcountered<?=$memodule;?>[]"]').click(function() { 
		try { 

			var trxno = jQuery(this).attr('data-trxno'); 
			
			var memsg = "<input type=\"hidden\" id=\"merecdatame\" data-trxno=\"" + trxno + "\" data-trxno=\"" + trxno + "\"  />";
			memsg += "<div id=\"memsgrecme\" style=\"display:none;\"></div>";

			jQuery('#meyn3_<?=$memodule;?>_bod').html('<span class=\"fw-bold\">Selected record [' + trxno + '] will be marked as countered.<br\>Proceed anyway?</span>' + memsg);
			jQuery('#staticBackdropmeyn3_<?=$memodule;?>').html('<span class=\"fw-bold\">DR Counter</span>');
			jQuery('#meyn3_<?=$memodule;?>_yes').prop('disabled',false);
			jQuery('#meyn3_<?=$memodule;?>_no').html('No');
			jQuery('#memsgrecme').hide();
			jQuery('#meyn3_<?=$memodule;?>').modal('show');
			
			
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  

		
		
	}); //end button for posting 
	
	jQuery('#meyn3_<?=$memodule;?>_yes').click(function() { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			jQuery('#meyn3_<?=$memodule;?>').modal('hide');
			var trxno = jQuery('#merecdatame').attr('data-trxno');
			var my_data = new FormData();
			my_data.append('trxno',trxno);
			
			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url()?>mydashb-dr-forcountered',
				context: document.body,
				data: my_data,
				contentType: false,
				global: false,
				cache: false,
				processData: false,
				success: function(data) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#memsgtestent_bod').html(data);
					jQuery('#memsgtestent').modal('show');
					
					$('#btn_forcountered').prop('disabled', true);
					return false;
				},
				error: function() { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				} 
			}); 			
			
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;
		}  
		
	});  // posting yes button 

</script>

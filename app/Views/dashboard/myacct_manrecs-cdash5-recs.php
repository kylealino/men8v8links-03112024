<?php 
/**
 *	File        : masterdata/myprodt-invent-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Sept 17, 2017
 * 	last update : Sept 17, 2017
 * 	description : Product Type Inventory Records
 */
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$cuser = $this->mylibz->mysys_user();
$mpw_tkn = $this->mylibz->mpw_tkn();
$cusergrp = $this->mylibz->mysys_usergrp();
$astore_dlmem = $this->mydataz->lk_Active_Store_or_Mem($this->db_erp);


$txtstore_dlmemhd= '';
//para sa isang user na gusto magprint sa ibang user na taga branch lang halimbawa si AD-SHANE gusto niya iprint lahat ng entry ni AD-MON
$buacct = $this->mydataz->lk_branch_users($this->db_erp);
$txtbuacct = '';

$str_style_p=" style=\"display:none;\"";
$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='28'","myua_acct");
if($result == 1){
	$str_style_p= '';
}//endif
//end
$data = array();
$mpages = (empty($this->mylibz->oa_nospchar($this->input->post('mpages'))) ? 0 : $this->mylibz->oa_nospchar($this->input->post('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}
$str_style='';
$reporttype = $this->input->get_post('report');
$cuserrema=$this->mylibz->mysys_userrema();
if($cuserrema ==='B'){
    //$this->load->view('template/novo/header_br');
    $str_style=" style=\"display:none;\"";
}
else{
    //$this->load->view('template/novo/header');
    $str_style='';
}
?>
<div class="d-flex justify-content-end">
	<button class="btn btn-success" id="btn_claims_dll"> <i class="fa fa-download"></i> Download</button>
</div>
<div class="table-responsive mt-2">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<table class="table table-striped table-bordered table-sm table-condensed">
			<thead>
				<tr>
					<?php if($reporttype == '3' || $reporttype == '4' || $reporttype == '6' ): ?>
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
					<?php if($reporttype == '3' || $reporttype == '4' || $reporttype == '6'): ?>
					<th>Validated Date</th>
					<th>Verified Date</th>
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
						$newBaseurl = $this->mymelibz->get_new_file_path($mtknImg2);
						$crpl = $this->mymdacct->get_crplData($row['drno']);
						?>
						<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
							<?php 
							if($reporttype == '3' || $reporttype == '4' || $reporttype == '6' ): ?>
							<td>
							<?php 
								if($cuserrema != 'B'): 
									if($row['claimdateCount'] > 13):
										echo anchor('mytrx_acct/acct_man_recs_vw/?file=claims&mtkn_trxno=' . $mtkn_trxno, '<i class="fa fa-share-square-o"></i> ',' Title="View Claims" class="btn btn-info btn-xs " ');
									elseif($row['claimdateCount'] <=13 && $row['claim_tag'] == 'Y' && $crpl == 'N' AND ($row[`supplier_id`] = '3' OR $row[`supplier_id`] = '1425' OR $row[`supplier_id`] = '4773' )): 
										echo anchor('mytrx_acct/acct_man_recs_vw/?file=claims&mtkn_trxno=' . $mtkn_trxno, '<i class="fa fa-share-square-o"></i> ',' Title="File for Claims" class="btn btn-info btn-xs " ');
									else: ?>
									<button class=" btn btn-sm btn-secondary disabled"> <i class="fa fa-share-square-o"></i> </button>
								<?php endif; 
								  endif; ?>
							</td>
						

							<td><a  style="font-size: 10px;"  onclick ="window.open('<?=$newBaseurl.$mtknImg;?>')" class="btn btn-info btn-sm text-white" title = "View"><i class="fa fa-file"></i> View </a></td>
							<?php endif; ?>

							<?php 
							if($reporttype == '5' ): ?>
							<td>
								<button class="btn btn-info btn-sm btn_claims_val " onclick="btn_claims_val('<?=$mtkn_trxno?>','<?=$row['trx_no'];?>')" Title="Claims Validate" value="<?=$mtkn_trxno?>"> <i class=" fa fa-send"></i></button>
								

							</td>
							<td>
							<button class="btn btn-success btn-sm btn_claims_val_dl " id="btn_claims_val_dl<?=$nn?>" onclick="btn_claims_val_dl('<?=$mtkn_trxno?>','<?=$row['trx_no'];?>','btn_claims_val_dl<?=$nn?>')" Title="Download" value="<?=$mtkn_trxno?>"> <i class=" fa fa-download"></i></button>	
							</td>
							<td><a  style="font-size: 10px;"  onclick ="window.open('<?=$newBaseurl.$mtknImg;?>')" class="btn btn-info btn-sm text-white" title = "View"><i class="fa fa-file"></i> View </a></td>
							<?php endif; ?>
							<td nowrap="nowrap"><?=$row['trx_no'];?></td>
							<td nowrap="nowrap"><?=$row['COMP_NAME'];?></td>
							<td nowrap="nowrap"><?=$row['BRNCH_NAME'];?></td>
							<td nowrap="nowrap"><?=$row['VEND_NAME'];?></td>
							<td nowrap="nowrap"><?=number_format($row['hd_subtqty'],2,'.',',');?></td>
							<td <?=$str_style;?> nowrap="nowrap"><?=number_format($row['hd_subtcost'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=number_format($row['hd_subtamt'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=$row['drno'];?></td>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['dr_date']);?></td>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['rcv_date']);?></td>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['date_in']);?></td>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['claim_date']);?></td>
							<?php if($reporttype == '3' || $reporttype == '4' || $reporttype == '6') : ?>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['validated_date']);?></td>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['verified_date']);?></td>
							<td nowrap="nowrap"><?=$this->mylibz->mydate_mmddyyyy($row['final_date']);?></td>
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
<div class="mt-2 d-flex justify-content-center">
	<?=$this->mylibz->mypagination($npage_curr,$npage_count,'__myredirected_rsearchc5','');?>
</div>
<div id="dl_rpt"> </div>	

<script type="text/javascript"> 

	
	
	 function __mndt_invent_crecs(mtkn_itm) { 

                try { 
                    $('html,body').scrollTop(0);
                    var cusergrp ='<?=$cusergrp;?>';
                   if (cusergrp !='SA'){
                    	var mtxt = 'You dont have authorized to delete this data.\n';
		                alert(mtxt);
		                return false;
                    }
                    $.showLoading({name: 'line-pulse', allowHide: false });

                    var mparam = {
                       mtkn_itm: mtkn_itm

                    }; 

                $.ajax({ // default declaration of ajax parameters
                    type: "POST",
                    url: '<?=site_url();?>mytrx_acct/msg_rcv_crecs',
                    context: document.body,
                    data: eval(mparam),
                    global: false,
                    cache: false,

                    success: function(data)  { //display html using divID
                        $.hideLoading();
                        jQuery('#myMod_crecs_Bod').html(data);
                    	jQuery('#myMod_crecs').modal('show');


                        return false;
                    },
                    error: function() { // display global error on the menu function
                        alert('error loading page...');
                        $.hideLoading();
                        return false;
                    }   
                }); 
            } catch(err) {
                var mtxt = 'There was an error on this page.\n';
                mtxt += 'Error description: ' + err.message;
                mtxt += '\nClick OK to continue.';
                alert(mtxt);
                $.hideLoading();
                return false;
            }  //end try            
        }
	
	
	$('#mytxtsearchrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				$('html,body').scrollTop(0);
				$.showLoading({name: 'line-pulse', allowHide: false });
				var txtsearchedrec = $('#mytxtsearchrec').val();
				var fld_vw_dteto = $('#fld_vw_dteto').val();
				var fld_vw_dtefrm = $('#fld_vw_dtefrm').val();
				if(fld_vw_dteto > fld_vw_dtefrm){
					alert('Check your date!');
					return false;
				}
				var mparam = {
					txtsearchedrec: txtsearchedrec,
					fld_vw_dteto: fld_vw_dteto,
					fld_vw_dtefrm: fld_vw_dtefrm,
					mpages: 1 
				};	
				$.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>mytrx_acct/mndt_invent_recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							$.hideLoading();
							$('#mymodoutrecs').html(data);
							
							return false;
					},
					error: function() { // display global error on the menu function
						alert('error loading page...');
						$.hideLoading();
						return false;
					}	
				});	
			} catch(err) {
				var mtxt = 'There was an error on this page.\n';
				mtxt += 'Error description: ' + err.message;
				mtxt += '\nClick OK to continue.';
				alert(mtxt);
				$.hideLoading();
				return false;
			}  //end try	
			
		}
	});
	
	$('#myfrmsearchrec').validate({
		submitHandler: function() { 
			try { 
				$('html,body').scrollTop(0);
				$.showLoading({name: 'line-pulse', allowHide: false });
				var txtsearchedrec = $('#mytxtsearchrec').val();
				var fld_vw_dteto = $('#fld_vw_dteto').val();
				var fld_vw_dtefrm = $('#fld_vw_dtefrm').val();
				if(fld_vw_dteto > fld_vw_dtefrm){
					alert('Check your date!');
					return false;
				}
				var mparam = {
					txtsearchedrec: txtsearchedrec,
					fld_vw_dteto: fld_vw_dteto,
					fld_vw_dtefrm: fld_vw_dtefrm,
					mpages: 1 
				};	
				$.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>mytrx_acct/mndt_invent_recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
					success: function(data)  { //display html using divID
							$.hideLoading();
							$('#mymodoutrecs').html(data);
							
							return false;
					},
					error: function() { // display global error on the menu function
						alert('error loading page...');
						$.hideLoading();
						return false;
					}	
				});			
							
			} catch(err) {
				var mtxt = 'There was an error on this page.\n';
				mtxt += 'Error description: ' + err.message;
				mtxt += '\nClick OK to continue.';
				alert(mtxt);
			}  //end try
			return false; 
		}
	});	
	jQuery('#fld_dlbranch')
            // don't navigate away from the field on tab when selecting an item
            .bind( 'keydown', function( event ) {
            	if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            		jQuery( this ).data( 'autocomplete' ).menu.active ) {
            		event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
            	event.preventDefault();
            }
        })
            .autocomplete({
            	minLength: 0,
            	source: '<?= site_url(); ?>mysearchdata/companybranch_v/',
            	focus: function() {
                        // prevent value inserted on focus
                        return false;
                    },
                    search: function(oEvent, oUi) {
                    	var sValue = jQuery(oEvent.target).val();
                        //var comp = jQuery('#fld_Company').val();
                        //var comp = jQuery('#fld_Company').attr("data-id");
                        jQuery(this).autocomplete('option', 'source', '<?=site_url();?>mysearchdata/companybranch_v'); 
                        //jQuery(oEvent.target).val('&mcocd=1' + sValue);

                    },
                    select: function( event, ui ) {
                    	var terms = ui.item.value;
                    	var mtkn_comp = ui.item.mtkn_comp;
                    	var mtknr_rid = ui.item.mtknr_rid;
                    	jQuery('#fld_dlbranch').val(terms);
                    	jQuery('#fld_dlbranch_id').val(mtknr_rid);
                    	jQuery(this).autocomplete('search', jQuery.trim(terms));
                    	return false;
                    }
                })
            .click(function() {
                    /*var comp = jQuery('#fld_Company').val();
                    var comp2 = this.value +'XOX'+comp;
                    var terms = comp2.split('XOX');//dto naq 4/25
                    */
                    var terms = this.value;
                    jQuery(this).autocomplete('search', jQuery.trim(terms));

                });
	


$('#btn_claims_dll').on('click',function(){
	try { 
	  //$('html,body').scrollTop(0);
	  $.showLoading({name: 'line-pulse', allowHide: false });
	  var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
	  var fld_brancharea = jQuery('#fld_itmgrparea_s').val();
	  var fld_d2dtto  = jQuery('#fld_d2dtto').val();
	  var fld_d2brnch = jQuery('#fld_d2brnch').val();
	  var rtp_type    = jQuery('#rtp_type').val();

	  var mparam = {
	  fld_d2dtfrm : fld_d2dtfrm,
	  fld_brancharea:fld_brancharea,
	  fld_d2dtto : fld_d2dtto,
	  fld_d2brnch:fld_d2brnch,
	  report:rtp_type,
	  }; 
	
	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mytrx_acct/myrcv_claims_dl',
	  context: document.body,
	  data: eval(mparam),
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
	        $.hideLoading();
	        $('#dl_rpt').html(data);
	        
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      $.hideLoading();
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  $.hideLoading();
	  return false;

	}  //end try
});



// $('.btn_claims_val').on('click',function(){
// 	try { 
// 	  //$('html,body').scrollTop(0);
// 	  $.showLoading({name: 'line-pulse', allowHide: false });
// 	  var fld_mktn = this.value;

// 	  var mparam = {
// 	  fld_mktn : fld_mktn,

// 	  }; 
// 	alert(fld_mktn);
// 	return false;
// 	  $.ajax({ // default declaration of ajax parameters
// 	  type: "POST",
// 	  url: '<?=site_url();?>mytrx_acct/myrcv_claims_validate',
// 	  context: document.body,
// 	  data: eval(mparam),
// 	  global: false,
// 	  cache: false,
// 	    success: function(data)  { //display html using divID
// 	        $.hideLoading();
// 	        $('#dl_rpt').html(data);
	        
// 	        return false;
// 	    },
// 	    error: function() { // display global error on the menu function
// 	      alert('error loading page...');
// 	      $.hideLoading();
// 	      return false;
// 	    } 
// 	  });     
	            
// 	} catch(err) {
// 	  var mtxt = 'There was an error on this page.\n';
// 	  mtxt += 'Error description: ' + err.message;
// 	  mtxt += '\nClick OK to continue.';
// 	  alert(mtxt);
// 	  $.hideLoading();
// 	  return false;

// 	}  //end try
// });

function btn_claims_val(fld_mktn,trxno){
	try { 
	  //$('html,body').scrollTop(0);
	  $.showLoading({name: 'line-pulse', allowHide: false });

	  var mparam = {
	  fld_mktn : fld_mktn,
	  trxno:trxno

	  }; 

	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mytrx_acct/myrcv_claims_validate',
	  context: document.body,
	  data: eval(mparam),
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
	        $.hideLoading();
	        $('#dl_rpt').html(data);
	        
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      $.hideLoading();
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  $.hideLoading();
	  return false;

	}  //end try
}

function btn_claims_val_dl(fld_mktn,trxno,btn_id){
	try { 
	  //$('html,body').scrollTop(0);
	  $.showLoading({name: 'line-pulse', allowHide: false });

	  var mparam = {
	  fld_mktn : fld_mktn,
	  trxno:trxno,
	  btn_id:btn_id

	  }; 

	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mytrx_acct/myrcv_claims_validate_dl',
	  context: document.body,
	  data: eval(mparam),
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
	        $.hideLoading();
	        $('#dl_rpt').html(data);
			
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      $.hideLoading();
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  $.hideLoading();
	  return false;

	}  //end try
}

</script>

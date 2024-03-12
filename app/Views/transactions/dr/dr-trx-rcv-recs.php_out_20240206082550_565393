<?php 
/**
 *	File        : transactions/dr/dr-trx-rcv-recs.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Dec 20, 2022
 * 	last update : Dec 20, 2022
 * 	description : Migrate into php8 compatability 
 *                DR Receiving encoded records listing
 */

$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod =  model('App\Models\MyUserModel');
$mydrtrx =  model('App\Models\MyDRTrxModel');
$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$cusergrp = $myusermod->mysys_usergrp();
$astore_dlmem = $mydatum->lk_Active_Store_or_Mem($mydbname->medb(0));
$txtstore_dlmemhd= '';
//para sa isang user na gusto magprint sa ibang user na taga branch lang halimbawa si AD-SHANE gusto niya iprint lahat ng entry ni AD-MON
$buacct = $mydatum->lk_branch_users($mydbname->medb(0));
$txtbuacct = '';
$str_style_p=" style=\"display:none;\"";
$result = $myusermod->get_Active_menus($mydbname->medb(0),$cuser,"myuaacct_id='28'","myua_acct");
if($result == 1){
	$str_style_p= '';
} // end if


$data = array();
$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}
$str_style='';
$cuserrema=$myusermod->mysys_userrema();
if($cuserrema ==='B'){
    //$this->load->view('template/novo/header_br');
    $str_style=" style=\"display:none;\"";
}
else{
    //$this->load->view('template/novo/header');
    $str_style='';
}
?>
<div class="row m-0 p-1">
	<div class="col-sm-12">
	<?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
	</div>
</div>
<div class="row m-0 p-1">
	<div class="col-sm-12">
		<div class="table-responsive">
			<table class="metblentry-font table-striped table-hover table-bordered table-sm">
				<thead>
					<tr>
						<th colspan="2" class="text-center">
							<?=anchor('mytrx_acct/acct_man_recs_vw', '<i class="bi bi-plus-lg"></i>',' class="btn btn-success btn-sm" ');?>
						</th>
						<th><i class="fa fa-cog"></i></th>
						<th>Transaction No</th>
						<th>Company</th>
						<th>Area Code</th>
						<th>Supplier</th>
						<th>Total Actual Qty</th>
						<th <?=$str_style;?>>Total Actual Cost</th>
						<th>Total Actual SRP</th>
						<th>DR No</th>
						<th>DR Date</th>
						<th>Received Date</th>
						<th>Date In</th>
						<th>User</th>
						<th>S/M Flag</th>
						<th>D/F Tag</th>
						<th>Y/N Posted</th>
						<th>Claim Tag</th>
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
							$crpl = $mydrtrx->get_crplData($row['drno']);
							
						//seven days validations
						if($row['claim_tag'] == 'Y'):
						if($row['claimdateCount'] > 13 && ($row['post_tag'] != 'Y' || $row['df_tag'] != 'F') AND ($row['supplier_id'] == '3' OR $row['supplier_id'] == '1425' OR $row['supplier_id'] == '4773' ) && $row['encd_date'] >= '2021-11-02' ):
							$mydrtrx->UpdateStatusAboveseven($row['recid'],$row['trx_no'],'13D');
						endif;
						else:
							if($row['issevenDays'] > 7 && ($row['post_tag'] != 'Y' || $row['df_tag'] != 'F') AND ($row['supplier_id'] == '3' OR $row['supplier_id'] == '1425' OR $row['supplier_id'] == '4773' ) && $row['encd_date'] >= '2021-11-02' ):
							$mydrtrx->UpdateStatusAboveseven($row['recid'],$row['trx_no'],'7D');
							endif;
						endif;
						//put comment on deletion tagged onclick="javascript:__mndt_invent_crecs('$mtkn_trxno;')
						?>
						<tr bgcolor="<?=$bgcolor;?>" <?=$on_mouse;?>>
							

							<td class="text-center" nowrap>
								<?=anchor('dr-trx/?mtkn_trxno=' . $mtkn_trxno, '<i class="bi bi-pencil-fill"></i>',' class="btn text-warning btn-sm" ');?>
							</td>
							<td class="text-center" nowrap>
								<button class="btn text-danger btn-sm" type="button" ><i class="bi bi-x-circle"></i></button>
							</td>
							<td class="text-center" nowrap>

								<?php if($cuserrema == 'B'): if($row['issevenDays'] <= 7 && $crpl == 'N' && ($row['supplier_id'] == '3' || $row['supplier_id'] == '1425' || $row['supplier_id'] == '4773' ) ): ?>
								<?=anchor('dr-trx/?file=claims&mtkn_trxno=' . $mtkn_trxno, '<i class="bi bi-box-arrow-in-up-right"></i>',' Title="File for Claims" class="btn text-primary btn-sm" ');?>
								<?php else: ?>
									<button class=" btn btn-sm text-secondary disabled"> <i class="bi bi-box-arrow-in-up-right"></i> </button>
								<?php endif; endif; ?>

								<?php if($cuserrema != 'B'): if($row['claimdateCount'] <= 13 && $row['claim_tag'] == 'Y' && $crpl == 'N' && ($row['supplier_id'] == '3' || $row['supplier_id'] == '1425' || $row['supplier_id'] == '4773' ) ): ?>
								<?=anchor('dr-trx/?file=claims&mtkn_trxno=' . $mtkn_trxno, '<i class="bi bi-box-arrow-in-up-right"></i>',' Title="File for Claims HO" class="btn text-primary btn-sm" ');?>
								<?php else: ?>
									<button class=" btn btn-sm text-secondary disabled"> <i class="bi bi-folder-symlink-fill"></i> </button>
								<?php endif; endif; ?>
								
							</td>

							<td nowrap="nowrap"><?=$row['trx_no'];?></td>
							<td nowrap="nowrap"><?=$row['COMP_NAME'];?></td>
							<td nowrap="nowrap"><?=$row['BRNCH_NAME'];?></td>
							<td nowrap="nowrap"><?=$row['VEND_NAME'];?></td>
							<td nowrap="nowrap"><?=number_format($row['hd_subtqty'],2,'.',',');?></td>
							<td <?=$str_style;?> nowrap="nowrap"><?=number_format($row['hd_subtcost'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=number_format($row['hd_subtamt'],2,'.',',');?></td>
							<td nowrap="nowrap"><?=$row['drno'];?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['dr_date']);?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['rcv_date']);?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['date_in']);?></td>
							<td nowrap="nowrap"><?=$row['muser'];?></td>
							<td nowrap="nowrap"><?=$row['hd_sm_tags'];?></td>
							<td nowrap="nowrap"><?=$row['df_tag'];?></td>
							<td nowrap="nowrap"><?=$row['post_tag'];?></td>
							<td nowrap="nowrap"><?=$row['claim_tag'];?></td>
							<td nowrap="nowrap"><?=$row['hd_remarks'];?></td>
							<td nowrap="nowrap"><?=$mylibzsys->mydate_mmddyyyy($row['encd_date']);?></td>
						</tr>
						<?php 
						$nn++;
						endforeach;
					else:
						?>
						<tr>
							<td colspan="14">No data was found.</td>
						</tr>
					<?php 
					endif; ?>
				</tbody>
			</table>
		</div> <!-- end table-responsive -->
	</div> <!-- end  div col-sm-12 -->
</div>
<script type="text/javascript"> 
	
	
	
	function __myredirected_rsearch(mobj) { 
		try { 
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var fld_vw_dteto = jQuery('#fld_vw_dteto').val();
			var fld_vw_dtefrm = jQuery('#fld_vw_dtefrm').val();
			if(fld_vw_dteto > fld_vw_dtefrm){
					alert('Check your date!');
					return false;
			}
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mparam = {
				txtsearchedrec: txtsearchedrec,
				fld_vw_dteto: fld_vw_dteto,
				fld_vw_dtefrm: fld_vw_dtefrm,
				mpages: mobj 
			};	
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>dr-trx-rcv-recs',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mymodoutrecs').html(data);
						
						return false;
				},
				error: function() { // display global error on the menu function
					alert('error loading page...');
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				}	
			});			
								
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			__mysys_apps.mepreloader('mepreloaderme',false);
			return false;

		}  //end try
	} //end __myredirected_rsearch
	
	
</script>

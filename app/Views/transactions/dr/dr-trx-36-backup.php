<?php
/* =================================================
 * Author      : Oliver Sta Maria
 * Date Created: December 07, 2022
 * Module Desc : Branch Receiving DR 
 * File Name   : transactions/dr/dr-trx.php
 * Revision    : Migration to Php8 Compatability 
*/
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod = model('App\Models\MyUserModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();

$file = $request->getVar('file');
$mtkn_trxno = $request->getVar('mtkn_trxno');
//PO BR
$trns_id = $request->getVar('trns_id');


$adftag = '';
$txtdf_tag= '';
$mtrx_no = '';
$txtcomp = '';
$txtarea_code = '';
$txtsupplier = '';
$txtpono = '';
$txtgldate = '';
$txtrems = '';
$mmnhd_rid ='';
$nmnrecs = 0;
$txtsubtdeb='';
$txtsubtcre='';
$rr_file_upld = '';
$COMP_NAME = '';
$BRNCH_NAME = '';
$VEND_NAME = '';
$CUST_NAME = '';
$entTyp = '';
$entTyprid = '';

$txtpotobrnc='';
$txtimsno ='';

$fld_jodrdate = '';
$medatetrx = $mylibzdb->getdate();
$adftag = $mydatum->lk_Active_DF($mydbname->medb(0));

$txttrx_no = '';
$txtcomp = '';
$txtarea_code = '';
$txtarea_id = '';
$txtsupplier = '';
$txtsupplier_id = '';
$txtsupplier_code = '';
$txtsupplier_tag = '';
$txtstore_memhd = '';
$astore_mem = array();
$astore_mem = $mydatum->lk_Active_Store_or_Mem($mydbname->medb(0));
$txtrcvfrmbrnc = '';
$txtdf_tag = '';
$fld_drno = '';
$txtdrno = '';
$txtdrdate = '';
$txtrcvdate = '';
//$txtdatein = $mylibzsys->mydate_mmddyyyy($medatetrx);
$txtdatein = substr($medatetrx,0,10);
$txtsubtqty = '';

$txtsubtcost = '';
$txtsubtamt = '';
$dis3 = '';
$mtkn_supp = '';
$mtknImg   = '';
$claim_tag ='';
$mtkn_vend = '';
if(!empty($mtkn_trxno)) { 
    $str = "SELECT
        aa.`trx_no`,
        aa.`supplier_id`,
        sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) mtkn_supp,
        aa.`drno`,
        aa.`dr_date`,
        aa.`rcv_date`,
        aa.`date_in`,
        aa.`hd_remarks`,
        aa.`hd_sm_tags`,
        aa.`df_tag`,
        aa.`hd_subtqty`,
        aa.`hd_subtcost`,
        aa.`hd_subtamt`,
        aa.`post_tag`,
        aa.`branch_id`,
        aa.`claim_tag`,
        aa.`claim_rcpt`,
        aa.`branch_id`,
        bb.`COMP_NAME`,
        cc.`BRNCH_NAME`,
        ee.`BRNCH_NAME` BRNCH_NAME2,
        dd.`VEND_NAME`,
        dd.`VEND_ENABLED`,
        dd.`VEND_CODE`,
        sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_trxtr 
        FROM {$mydbname->medb(0)}.`trx_manrecs_hd` aa
        JOIN {$mydbname->medb(0)}.`mst_company` bb
        ON (aa.`comp_id` = bb.`recid`)
        JOIN {$mydbname->medb(0)}.`mst_companyBranch` cc
        ON (aa.`branch_id` = cc.`recid`)
        LEFT JOIN {$mydbname->medb(0)}.`mst_companyBranch` ee
        ON (aa.`hd_rfrom_id` = ee.`recid`)
        JOIN {$mydbname->medb(0)}.`mst_vendor` dd
        ON (aa.`supplier_id` = dd.`recid`)
        WHERE sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$mtkn_trxno' ";
    $qq = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    $rw = $qq->getRowArray();
    $mmnhd_rid      = $rw['mtkn_trxtr'];
    $txttrx_no      = $rw['trx_no'];
    $txtcomp        = $rw['COMP_NAME'];
    $txtarea_code   = $rw['BRNCH_NAME'];
    $txtsupplier    = $rw['VEND_NAME'];
    $txtsupplier_code = $rw['VEND_CODE'];
    $txtsupplier_tag  = $rw['VEND_ENABLED'];
    $txtsupplier_id = $rw['supplier_id'];
    $mtkn_supp = $rw['mtkn_supp'];
    $txtdrno        = $rw['drno'];
    $txtdrdate  = substr($rw['dr_date'],0,10);
    $txtrcvdate = substr($rw['rcv_date'],0,10);
    $txtdatein  = substr($rw['date_in'],0,10);
    $txtrems        = $rw['hd_remarks'];
    $txtstore_memhd = $rw['hd_sm_tags'];
    $txtdf_tag      = $rw['df_tag'];
    $txtsubtqty     = number_format($rw['hd_subtqty'],2,'.',',');
    $txtsubtcost    = number_format($rw['hd_subtcost'],2,'.',',');
    $txtsubtamt     = number_format($rw['hd_subtamt'],2,'.',',');
    $txtrcvfrmbrnc = $rw['BRNCH_NAME2'];
    $dis3       = (($rw['post_tag'] == 'Y') ? "disabled" : '');
    $txtarea_id = $rw['branch_id'];
    $claim_tag  = $rw['claim_tag'];
    $mtknImg    = './uploads/rcv_claims/'.$rw['claim_rcpt'];
    $qq->freeResult();
}
elseif(!empty($trns_id)) { 
    $str = "SELECT 
            aa.`sysctrl_seqn`,
            aa.`branchID`,
            aa.`vendID`,
            sha2(concat(aa.`vendID`,'{$mpw_tkn}'),384) mtkn_vend,
            aa.`expectedDateDel` dr_date,
            bb.`COMP_NAME`,
            cc.`BRNCH_NAME`,
            dd.`VEND_NAME`,
            dd.`VEND_ENABLED`,
            dd.`VEND_CODE`
            FROM {$mydbname->medb(0)}.`trx_pobr_hd` aa 
            JOIN {$mydbname->medb(0)}.`mst_company` bb 
            ON bb.`recid` = aa.`compID` 
            JOIN {$mydbname->medb(0)}.`mst_companyBranch` cc 
            ON cc.`recid` = aa.`branchID`
            JOIN {$mydbname->medb(0)}.`mst_vendor` dd 
            ON dd.`recid` = aa.`vendID`
            where (sha2(concat(aa.`recid`,'{$mpw_tkn}'),384)) = '{$trns_id}'";
    $qq = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	$rw = $qq->getRowArray();
	$mmnhd_rid      = '';
	$txttrx_no      = '';
	$txtcomp        = $rw['COMP_NAME'];
	$txtarea_code   = $rw['BRNCH_NAME'];
	$txtsupplier    = $rw['VEND_NAME'];
	$txtsupplier_code = $rw['VEND_CODE'];
	$txtsupplier_tag  = $rw['VEND_ENABLED'];
	$txtsupplier_id = $rw['vendID'];
	$mtkn_supp = $rw['mtkn_vend'];
	$txtdrno        = $rw['sysctrl_seqn'];
	$txtdrdate      = substr($rw['dr_date'],0,10);
	$txtrcvdate     = '';
	$txtdatein      = $txtdatein;
	$txtrems        = '';
	$txtstore_memhd = '';
	$txtdf_tag      = '';
	$txtsubtqty     = '';
	$txtsubtcost    = '';
	$txtsubtamt     = '';
	$txtrcvfrmbrnc  = '';
	$dis3 = '';
	$txtarea_id = $rw['branchID'];
	$qq->freeResult();
}

$str_style='';
$cuserrema=$myusermod->mysys_userrema();
if($cuserrema ==='B'){
    $str_style=" style=\"display:none;\"";
}
else{
   
    $str_style=''; 
}


?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Delivery Receipt In</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
				<li class="breadcrumb-item">Transaction</li>
				<li class="breadcrumb-item active">Delivery Receipt In</li>
			</ol>
		</nav>
	</div> <!-- End Page Title -->
	<section class="section">
		<div class="row metblentry-font">
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Transaction No.:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" id="txttrx_no" name="txttrx_no" class="form-control form-control-sm" value="<?=$txttrx_no;?>" readonly />
						<input type="hidden" class="form-control form-control-sm" name="__hmtkn_trxnoid" id="__hmtkn_trxnoid" value="<?= $mmnhd_rid;?>" readonly />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Company</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" data-id="" id="fld_Company" name="fld_Company" value="<?=$txtcomp;?>" required />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Area Code:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" data-id="" id="fld_area_code" name="fld_area_code" value="<?=$txtarea_code;?>" required />
						<input type="hidden" id="fld_area_id" name="fld_area_id" value="<?=$txtarea_id;?>"/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">Supplier:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm fld_supplier" data-idsupptkn="<?=$mtkn_supp;?>" id="fld_supplier" name="fld_supplier" value="<?=$txtsupplier;?>" required />
						<input type="hidden" id="fld_supplier_id" name="fld_supplier_id" value="<?=$txtsupplier_id;?>" />
						<input type="hidden" id="fld_supplier_code" name="fld_supplier_code" value="<?=$txtsupplier_code;?>"/>
						<input type="hidden" id="fld_supplier_tag" name="fld_supplier_tag" value="<?=$txtsupplier_tag;?>"/>
						<input type="hidden" id="supp_id_n" name="supp_id_n" value=""/>
						
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span class="fw-bold">S/M:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($astore_mem,$txtstore_memhd,'fld_somhd','class="form-control form-control-sm" ','','');?>
					</div>
				</div>
				<div id="RFB" class="row mb-3" style ="display:none;">
					<div class="col-sm-3">
						<span>Rcv. From Branch:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm" id="fld_rcvfrmbrnc" name="fld_rcvfrmbrnc" value="<?=$txtrcvfrmbrnc;?>"/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>MO/DR #:</span>
					</div>
					<div class="col-sm-9">
						<div class="input-group mb-3">
						  <button class="btn btn-success btn-sm select_mo" type="button" id="btn_pickfrm">PICK FROM</button>
						  <input type="text" class="form-control form-control-sm" placeholder="" id="fld_mo" name="fld_mo" value=""  aria-label="Paste your DR Number" aria-describedby="btn_pickfrm-addon1">
						</div>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Total DR Qty:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm text-end" name="fld_subtqty_dr" id="fld_subtqty_dr" value="" readonly/>
					</div>
				</div>
				<div class="row mb-3" <?=$str_style;?> >
					<div class="col-sm-3">
						<span>Total DR Cost:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm text-end" name="fld_subtcost_dr" id="fld_subtcost_dr" value="" readonly/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Total DR SRP:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm text-end" name="fld_subtamt_dr" id="fld_subtamt_dr" value="" readonly/>
					</div>
				</div>
				<?php if(!empty($file )):?>
				<div class="row mb-3">
					<?php if( $claim_tag == "Y"):?>
					<div class="text-center col-lg-6 offset-lg-3 offset-md-0 p-2 rounded" style="background-color:#ababab78;"> 
						<h6>Attached File</h6>
						<div class="text-center">
							<span class="bi bi-file-earmark-medical" style="font-size: 100px;"></span>
						</div>
						<div class="text-center">
							<label style ="font-size:15px;"id="__lbl01"></label>
						</div>
						<div class="d-flex justify-content-center">
							<a onclick ="window.open('<?=site_url().$mtknImg;?>')" class="btn text-success btn-sm" title = "View"><i class="bi bi-cloud-upload text-danger"></i> View </a>
						</div>
					</div>					
					<?php else: ?>
					<div class="col-sm-3">
						<span>Upload Attachment</span>
					</div>
					<div class="col-sm-9">
						<label class="btn btn-sm text-success" >
							<span class="bi bi-cloud-upload"></span> Browse<input data-id="__lbl01" accept="image/gif,image/jpeg,image/png,application/pdf" class="__pld_file_img01" size="5" id="__pld_file_img01" type="file" multiple name="images[]">
						</label>
					</div>
					<?php endif;?>
				</div>
				<?php endif;?>
			</div> <!-- end col-6 -->
			<div class="col-md-6">
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Tagging D/F:</span>
					</div>
					<div class="col-sm-9">
						<?=$mylibzsys->mypopulist_2($adftag,$txtdf_tag,'fld_dftag','class="form-control form-control-sm" ','','');?>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>DR #:</span>
					</div>
					<div class="col-sm-9">
						<div class="input-group mb-3">
						  <button class="btn btn-success btn-sm check_dr" title="Check if DR No is already exist" type="button" id="btn_pickfrm"><i class="bi bi-card-checklist"></i></button>
						  <input type="text" class="form-control form-control-sm" placeholder="" id="fld_drno" name="fld_drno" value="<?=$txtdrno;?>" required aria-describedby="btn_pickfrm-addon1">
						</div>						
					</div>
				</div>				
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>DR Date:</span>
					</div>
					<div class="col-sm-9">
						<input type="date" placeholder="mm/dd/yyyy" class="form-control form-control-sm text-start" data-id="" id="fld_drdate" name="fld_drdate" value="<?=$txtdrdate;?>" required />
					</div>
				</div>				
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Received Date:</span>
					</div>
					<div class="col-sm-9">
						<input min="2015-01-01" type="date" placeholder="mm/dd/yyyy" type="text" class="form-control form-control-sm text-start" name="fld_rcvdate" id="fld_rcvdate" value="<?=$txtrcvdate;?>" required />
					</div>
				</div>				
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Date In:</span>
					</div>
					<div class="col-sm-9">
						<input type="date" class="form-control form-control-sm" placeholder="mm/dd/yyyy" id="fld_datein" name="fld_datein" value="<?=$txtdatein;?>" readonly disabled />
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Remarks:</span>
					</div>
					<div class="col-sm-9">
						<textarea type="text" class="form-control form-control-sm" name="fld_rems" id="fld_rems"><?=$txtrems;?></textarea>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Total Actual Qty:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm text-end" name="fld_subtqty" id="fld_subtqty" value="<?=$txtsubtqty;?>" readonly/>
					</div>
				</div>
				<div class="row mb-3" <?=$str_style;?>>
					<div class="col-sm-3">
						<span>Total Cost:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm text-end" name="fld_subtcost" id="fld_subtcost" value="<?=$txtsubtcost;?>" readonly/>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-3">
						<span>Total SRP:</span>
					</div>
					<div class="col-sm-9">
						<input type="text" class="form-control form-control-sm text-end" name="fld_subtamt" id="fld_subtamt" value="<?=$txtsubtamt;?>" readonly/>
					</div>
				</div>
			</div> <!-- end col-6 2nd screen --> 
		</div> <!-- end row -->
		<!-- table data entry -->
		<div class="row mb-3">
			<div class="col-sm-12" id="tbl_items_ent">
				<input type="hidden" id="metagged_itmremove" value=""/>
				  <?php 
				  $data['rlist']='';
				  $data['__nores']='';
				  $data['mmnhd_rid']=$mmnhd_rid;
				  $data['dis3']= $dis3;
				  $data['txtdrno']= $txtdrno;
				  echo view('transactions/dr/dr-trx-recs',$data); ?>
			</div>
		</div> <!-- end div row -->
		<div class="row mb-3">
			<div class="col-sm-12">
				<?php if(!empty($file)): ?> 
				<button class="btn btn-primary btn-sm" id="mbtn_mn_Claim" <?php echo $dis3; ?> type="submit">Save Claims</button>
				<?php else: ?>
				<button type="button" class="btn btn-success btn-sm" id="mbtn_drin_save">
					Save
				</button>
				<?php endif; ?>
				<button type="button" class="btn btn-danger btn-sm" id="mbtn_drin_cancel">
					Cancel
				</button>
				<button type="button" class="btn btn-warning btn-sm" id="mbtn_drin_new">
					New Trx
				</button>
			</div>
		</div>
		<!-- end table data entry -->
	</section>
	<!-- tabular module -->
	<div class="row mb-4">
		<div class="col-lg-12 col-md-12 mb-md-0 mb-4">
			<div class="card">
				<ul class="nav nav-tabs nav-tabs-bordered" id="myTabArticle" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="drreclisting-tab" data-bs-toggle="tab" data-bs-target="#drreclisting" type="button" role="tab" aria-controls="drreclisting" aria-selected="true">Record Listing...</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="droltdashboard-tab" data-bs-toggle="tab" data-bs-target="#droltdashboard" type="button" role="tab" aria-controls="droltdashboard" aria-selected="false">OLT Dashboard</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="branchclimsdashboard-tab" data-bs-toggle="tab" data-bs-target="#branchclimsdashboard" type="button" role="tab" aria-controls="branchclimsdashboard" aria-selected="false">Branch Claims Dashboard</button>
					</li>
				</ul>
				<div class="tab-content" id="medrrecs">
					<div class="tab-pane fade show active" id="drreclisting" role="tabpanel" aria-labelledby="drreclisting-tab">
						<div class="row p-2">
							<div class="col-sm-12">
							   <div class="input-group input-group-sm">
								  <span class="input-group-text" id="basic-addon1">Search</span>
								  <input type="text" class="form-control" id="mytxtsearchrec" placeholder="Search Transaction/Company/Area/DR/Supplier" aria-label="mytxtsearchrec" aria-describedby="basic-addon1" required>
								  <button type="submit" class="btn btn-success btn-sm" id="mebtn_searchdr"><i class="bi bi-search"></i></button>
								  <?=anchor('dr-trx', 'Reset',' class="btn btn-success btn-sm" ');?>  
								</div>
							</div>
						</div>
						<div class="row" id="mymodoutrecs">
						</div>
					</div>
					<div class="tab-pane fade" id="droltdashboard" role="tabpanel" aria-labelledby="droltdashboard-tab">222</div>
					<div class="tab-pane fade" id="branchclimsdashboard" role="tabpanel" aria-labelledby="branchclimsdashboard-tab">333</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end tabular module -->
	<?php
	echo $mylibzsys->memypreloader01('mepreloaderme');
	echo $mylibzsys->memsgbox1('memsgme','System Alert','...');
	echo $mylibzsys->memsgbox2('memsgme2','System Msg','...');
	echo $mylibzsys->memsgbox_yesno1('metrxdrincancmsg','Closed and Cancel DR IN Transaction Entry','Cancel changes made?');
	echo $mylibzsys->memsgbox_yesno1('metrxdrinnewcmsg','Nel DR IN Transaction Entry','Are you sure you want to new transaction?');
	echo $mylibzsys->memsgbox_yesno1('metrxdrinitmdel','DR IN Transaction Entry - Delete Item','Delete this Item?');
	echo $mylibzsys->memsgbox_yesno1('metrxdrisave','DR IN Transaction Entry - Saving Confirmation','Save DR Transaction Entry?');
	?>
		  	
</main>  <!-- end main -->
<script type="text/javascript"> 
	
	jQuery('#mbtn_drin_save').click(function() { 
		try { 
			jQuery('#metrxdrisave').modal('show');
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
			
	});
	
	jQuery('#metrxdrisave_yes').click(function() { 
		try { 
			var mesrmesg = jQuery('#metrxdrisave_bod').html();
			//jQuery('#metrxdrisave_bod').html(mesrmesg + '<br/><span class="text-success text-bolder">Line Item deleted...</span>');
			
			var fld_txttrx_no = jQuery('#txttrx_no').val();
			var fld_Company = jQuery('#fld_Company').val();
			var fld_area_code = jQuery('#fld_area_code').val();
			var fld_supplier = jQuery('#fld_supplier').val();
			
			var fld_dftag = jQuery('#fld_dftag').val();

			var fld_drno = jQuery('#fld_drno').val();
			var txt_mo_d = fld_drno.substr(0, 3);

			var fld_drdate = jQuery('#fld_drdate').val();
			var fld_rcvdate = jQuery('#fld_rcvdate').val();
			var fld_datein = jQuery('#fld_datein').val();
			var fld_somhd = jQuery('#fld_somhd').val();
			var fld_rems = jQuery('#fld_rems').val();
			var trxno_id = jQuery('#__hmtkn_trxnoid').val();

			var fld_subtqty = jQuery('#fld_subtqty').val();
			var fld_subtcost = jQuery('#fld_subtcost').val();
			var fld_subtamt = jQuery('#fld_subtamt').val();
			
			//var tbl_PayData = jQuery('#tbl_PayData');
			var rowCount1 = jQuery('#tbl_PayData tr').length - 1;
			var adata1 = [];
			var adata2 = [];
			var mdata = '';
			var mdat ='';
			
			var rdate = new Date(jQuery('#fld_rcvdate').val());
			var drdate = new Date(jQuery('#fld_drdate').val());
			var datein = new Date(jQuery('#fld_datein').val());
			//FROM
			var __rfrom =jQuery('#fld_rcvfrmbrnc').val();
			
			if(rdate < drdate){ 
					jQuery('#metrxdrisave').modal('hide');
					alert('Received Date must be greater than or equal to DR Date');
					return false;
			}
			
			if(jQuery('#fld_datein').val().length != 0) {
				if(datein < rdate){ 
					jQuery('#metrxdrisave').modal('hide');
					alert('Date In must be greater than or equal to Received Date');
					return false;
				}
			}
			if(fld_dftag == 'F'){
				if(fld_datein=='' || fld_datein == 0 || fld_datein=='00/00/0000' || fld_datein=='mm/dd/yyyy'){ 
					jQuery('#metrxdrisave').modal('hide');
					alert('Date In is required!');
					return false;
				}
				if(datein < rdate){
					jQuery('#metrxdrisave').modal('hide');
					alert('Date In must be greater than or equal to Received Date');
					return false;
				} 
			}
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			
			
			//var rowCount = jQuery('#tbl_PayData tr').length;
			//var meid = (rowCount + 1);
			//console.log(fld_rcvdate);
			if (fld_txttrx_no != ""){
				for(aa = rowCount1; aa > 0; aa--) { 
				var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone(); 
				var __meuid = jQuery(clonedRow).find('input[type=hidden]').eq(2).val();
				var fld_mitemcode = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
				var fld_mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); //desc
				var fld_mitempkg = jQuery(clonedRow).find('input[type=text]').eq(2).val(); //pkg
				var fld_ucost = jQuery(clonedRow).find('input[type=text]').eq(3).val(); //ucost
				var fld_mitemtcost = jQuery(clonedRow).find('input[type=text]').eq(4).val(); //ucost
				var fld_srp = jQuery(clonedRow).find('input[type=text]').eq(5).val(); //srp
				var fld_mitemtamt = jQuery(clonedRow).find('input[type=text]').eq(6).val(); //tamt
				var fld_mitemqty = jQuery(clonedRow).find('input[type=text]').eq(7).val(); //qty r
				var fld_mitemqtyc = jQuery(clonedRow).find('input[type=text]').eq(8).val(); //qty c
				var fld_remks = "";//jQuery(clonedRow).find('input[type=text]').eq(9).val(); //rems
				var fld_olt = jQuery(clonedRow).find('input[type=text]').eq(9).val(); //olt
				var fld_som = "";//$('#fld_mitemstore_mem_' + __meuid ).val();//S/M.children("option:selected").val();
				if(txt_mo_d == 'GRO'){
					var fld_expdate = jQuery(clonedRow).find('input[type=text]').eq(10).val(); //olt
				}
				else{
					var fld_expdate = '';
				}
				
				var fld_mndt_rid = jQuery(clonedRow).find('input[type=hidden]').eq(1).val(); //mndt id
				var fld_mndt_tag = jQuery(clonedRow).find('input[type=hidden]').eq(3).val(); //mndt tag
					if(fld_mndt_tag == 'Y'){
						mdata = fld_mitemcode + 'x|x' + fld_mitemdesc + 'x|x' + fld_mitempkg + 'x|x' + fld_ucost + 'x|x' + fld_mitemtcost + 'x|x' +  fld_srp + 'x|x' + fld_mitemtamt + 'x|x' + fld_mitemqty + 'x|x' + fld_mitemqtyc + 'x|x' +fld_remks +  'x|x' + fld_olt +  'x|x' + fld_som + 'x|x' + fld_mndt_rid + 'x|x' + fld_expdate;
						adata1.push(mdata);
						mdat = jQuery(clonedRow).find('input[type=hidden]').eq(0).val(); //icode
						adata2.push(mdat);
					}

				}  //end for
			}
			else{
				//INSERT
				for(aa = rowCount1; aa > 0; aa--) { 
					var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone(); 
					var __meuid = jQuery(clonedRow).find('input[type=hidden]').eq(2).val();
					var fld_mitemcode = jQuery(clonedRow).find('input[type=text]').eq(0).val(); //icode
					var fld_mitemdesc = jQuery(clonedRow).find('input[type=text]').eq(1).val(); //desc
					var fld_mitempkg = jQuery(clonedRow).find('input[type=text]').eq(2).val(); //pkg
					var fld_ucost = jQuery(clonedRow).find('input[type=text]').eq(3).val(); //ucost
					var fld_mitemtcost = jQuery(clonedRow).find('input[type=text]').eq(4).val(); //ucost
					var fld_srp = jQuery(clonedRow).find('input[type=text]').eq(5).val(); //srp
					var fld_mitemtamt = jQuery(clonedRow).find('input[type=text]').eq(6).val(); //tamt
					var fld_mitemqty = jQuery(clonedRow).find('input[type=text]').eq(7).val(); //qty r
					var fld_mitemqtyc = jQuery(clonedRow).find('input[type=text]').eq(8).val(); //qty c
					var fld_remks = "";//jQuery(clonedRow).find('input[type=text]').eq(9).val(); //rems
					var fld_olt = jQuery(clonedRow).find('input[type=text]').eq(9).val(); //olt
					var fld_som = "";//$('#fld_mitemstore_mem_' + __meuid ).val();//S/M.children("option:selected").val();
					 if(txt_mo_d == 'GRO'){
						var fld_expdate = jQuery(clonedRow).find('input[type=text]').eq(10).val(); //olt
					}
					else{
						var fld_expdate = '';
					}
					
					//console.log(fld_som);
					var fld_mndt_rid = jQuery(clonedRow).find('input[type=hidden]').eq(1).val(); //mndt id
					var fld_mndt_tag = jQuery(clonedRow).find('input[type=hidden]').eq(3).val(); //mndt tag
					if(fld_mndt_tag == 'Y'){
					
						mdata = fld_mitemcode + 'x|x' + fld_mitemdesc + 'x|x' + fld_mitempkg + 'x|x' + fld_ucost + 'x|x' + fld_mitemtcost + 'x|x' +  fld_srp + 'x|x' + fld_mitemtamt + 'x|x' + fld_mitemqty + 'x|x' + fld_mitemqtyc + 'x|x' +fld_remks +  'x|x' + fld_olt +  'x|x' + fld_som + 'x|x' + fld_mndt_rid + 'x|x' + fld_expdate;
						adata1.push(mdata);
						mdat = jQuery(clonedRow).find('input[type=hidden]').eq(0).val(); //icode
						adata2.push(mdat);
					}

				}  //end for
			}
			var smparam = { 
				trxno_id: trxno_id,
				fld_txttrx_no: fld_txttrx_no,
				fld_Company: fld_Company,
				fld_area_code: fld_area_code,
				fld_supplier: fld_supplier,
				fld_dftag: fld_dftag,
				fld_drno: fld_drno,
				fld_drdate: fld_drdate,
				fld_rcvdate: fld_rcvdate,
				fld_datein: fld_datein,
				fld_somhd:fld_somhd,
				fld_rems: fld_rems,
				fld_subtqty: fld_subtqty,
				fld_subtcost: fld_subtcost,
				fld_subtamt: fld_subtamt,
				__rfrom:__rfrom,
				adata1: adata1,
				adata2: adata2
			}
			
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?= site_url() ?>dr-trx-save',
				context: document.body,
				data: eval(smparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#metrxdrisave').modal('hide');
					jQuery('#memsgme2_bod').html(data);
					jQuery('#memsgme2').modal('show');
				},
				error: function(data) { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}
			});
			//jQuery(this).prop('disabled', true);
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});		
	
	
	jQuery('.medatepicker').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true		
		});
		
    __mysys_apps.mepreloader('mepreloaderme',false);
    
    function meSetCellPadding () {
        var metable = document.getElementById ("tbl_JODRInData");
        metable.cellPadding = 3;
        metable.style.border = "1px solid #F6F5F4";
        var tabletd = metable.getElementsByTagName("td");
        //for(var i=0; i<tabletd.length; i++) {
        //    var td = tabletd[i];
        //    td.style.borderColor ="#F6F5F4";
        //}

    }
    
	jQuery('#fld_mo').keyup(function() {
		jQuery(this).val(jQuery(this).val().toUpperCase());
	});
	jQuery('#fld_drno').keyup(function() {
		jQuery(this).val(jQuery(this).val().toUpperCase());
	});
	vw_edt_frm();
	function vw_edt_frm(){
		var rfrom = '<?=$txtrcvfrmbrnc;?>';
		if(rfrom != ''){
		   jQuery('#RFB').css('display','flex');
		}
	}
	
	function vw_smc_disabled_additm(supp_id){ 
		if(supp_id == "3") { 
			jQuery('#btn_add').prop('disabled',true);
			//alert('Please use the pickfrom!');
			//jQuery('#btn_pickfrm').on('click');
			jQuery('#fld_mo').prop('disabled',false);
			jQuery('#fld_supplier').prop('disabled',true);
		}
		else if(supp_id == "4773") {
			jQuery('#btn_add').prop('disabled',true);
			jQuery('#fld_mo').prop('disabled',false);
			 jQuery('#fld_supplier').prop('disabled',true);
		   //alert('Please use the pickfrom!');
			//jQuery('#btn_pickfrm').on('click');
		}
		else if(supp_id == "1425") { //gwemc
			jQuery('#btn_add').prop('disabled',true);
			jQuery('#fld_mo').prop('disabled',false);
			jQuery('#fld_supplier').prop('disabled',true);
			//alert('Please use the pickfrom!');
		   //jQuery('#btn_pickfrm').on('click');
		}
		else if(supp_id == "5537") { //receive in pullout
			jQuery('#btn_add').prop('disabled',true);
			jQuery('#fld_mo').prop('disabled',false);
			jQuery('#fld_supplier').prop('disabled',true);
			jQuery('#fld_somhd').prop('disabled',true);
			jQuery('#fld_rcvfrmbrnc').prop('disabled',true);
			//alert('Please use the pickfrom!');
		   //jQuery('#btn_pickfrm').on('click');
		}
		else if(supp_id == "7325") { //wanbee
			jQuery('#btn_add').prop('disabled',true);
			jQuery('#fld_mo').prop('disabled',false);
			jQuery('#fld_supplier').prop('disabled',true);
			//alert('Please use the pickfrom!');
		   //jQuery('#btn_pickfrm').on('click');
		}
		else {
		  //jQuery('#btn_pickfrm').off('click');
		  jQuery('#fld_mo').val('');
		  jQuery('#fld_mo').prop('disabled',true);
		   jQuery('#fld_supplier').prop('disabled',true);
		  
		}
		
	}  //end vw_smc_disabled_additm
        	
	jQuery('#fld_somhd').on('change',function() {
	   vw_from();
	});  //end fld_somhd
	
	function vw_from(){
		var fld_somhd = jQuery('#fld_somhd').val();
		
		if (fld_somhd == 'R') {
			jQuery('#RFB').css('display', (fld_somhd == 'R') ? 'flex' : 'none');
		}
		if (fld_somhd != 'R') {
			jQuery('#RFB').css('display', (fld_somhd != 'R') ? 'none' : 'flex');
		}
	}  //end vw_from
	
	function __meNumbersOnly(e) { 
		var code = (e.which) ? e.which : e.keyCode;
		//if (code > 31 && (code < 47 || code > 57)) {
		if(!((code > 47 && code < 58) || code == 46)) { 
			e.preventDefault();
		}
	} //end __meNumbersOnly
	
    jQuery('.select_mo').on('click',function() {
        var fld_area_id = jQuery('#fld_area_id').val();
        var select_mo = jQuery('#fld_mo').val();
        var check_dr = jQuery('#fld_mo').val();
        var __hmtkn_trxnoid = jQuery('#__hmtkn_trxnoid').val();
        var fld_supplier_id = jQuery('#fld_supplier_id').val();
        var supp_id_n = jQuery('#supp_id_n').val();
        __checking_mo(select_mo,select_mo,__hmtkn_trxnoid,fld_supplier_id,fld_area_id,supp_id_n);
    });  //end select_mo
    
	function __checking_mo(txt_mo,dr_no,trxno,supp,branch_id,supp_id_n) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			jQuery('#fld_drno').val(txt_mo);
			var mparam = {
				txt_mo:txt_mo,
				dr_no:dr_no,
				trxno: trxno,
				branch_id:branch_id,
				supp_id_n: supp_id_n,
				supp: supp

			};
		
		//old from mydblinks/select_mo_items
		jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>search-pick-from-trx',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,

			success: function(data)  { //display html using divID
				jQuery('#tbl_items_ent').html(data);
				//my_add_line_item();
				//__my_item_lookup();
				jQuery('#btn_add').prop('disabled',true);
				jQuery('.btn_remove').prop('disabled',true);
			   
				jQuery('#fld_drdate').prop('disabled',true);
				jQuery('.mitemcode').prop('disabled',true);
				__mysys_apps.mepreloader('mepreloaderme',false);
				//nullvalue(0);
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
	}  //end __checking_mo    	
	
	jQuery('.check_dr').on('click',function() {
		var check_dr = jQuery('#fld_drno').val();
		var __hmtkn_trxnoid = jQuery('#__hmtkn_trxnoid').val();
		var fld_supplier_id = jQuery('#fld_supplier_id').val();
		var fld_supplier_tkn = jQuery('#fld_supplier').attr('data-idsupptkn');
		__checking_dr(check_dr,__hmtkn_trxnoid,fld_supplier_tkn);
	});	
	
	function __checking_dr(dr_no,trxno,supp) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mparam = {
				dr_no:dr_no,
				trxno: trxno,
				supp: supp

			};
		jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>search-check-dr',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
			success: function(data)  { //display html using divID
				jQuery('#memsgme_bod').html(data);
				jQuery('#memsgme').modal('show');
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
	}	//end __checking_dr
	
	jQuery('#mbtn_mn_NTRX').click(function() { 
		var userselection = confirm("Are you sure you want to new transaction?");
		if (userselection == true){
			window.location = '<?=site_url();?>/mytrx_acct/acct_man_recs_vw';
		}
		else{
			$.hideLoading();
			return false;
		} 
	});	
	
	jQuery('#fld_Company')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
				event.preventDefault();
			}
			if( event.keyCode === jQuery.ui.keyCode.TAB ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>search-company/',
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();
				//jQuery(oEvent.target).val('&mcocd=1' + sValue);
				//alert(sValue);
			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				var apv_id = ui.item._compcode;
				this.value = ui.item.value;
				var comp_id = ui.item.mtkn_recid;
				//console.log(comp_id);
				//jQuery('#apv_id').val('APV-'+ui.item._compcode+'-'+ui.item.cseqn);
				//jQuery('#comp_code_').val(ui.item._compcode);
				jQuery('#fld_Company').val(terms);
				jQuery('#fld_Company').attr("data-id",comp_id);


				return false;
			}
		})
	.click(function() {
		//jQuery(this).keydown();
		var terms = this.value;

		// var terms=('')+'xox'+$('#fld_Company').val()
		//jQuery(this).autocomplete('search', '');
		jQuery(this).autocomplete('search', jQuery.trim(terms));


	});  //end fld_Company
        	
	jQuery('#fld_area_code')
	// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
				event.preventDefault();
		}
		if( event.keyCode === jQuery.ui.keyCode.TAB ) {
			event.preventDefault();
		}
	})
	.autocomplete({
		minLength: 0,
		source: '<?= site_url(); ?>search-area-company/',
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		search: function(oEvent, oUi) {
			var sValue = jQuery(oEvent.target).val();
			//var comp = jQuery('#fld_Company').val();
			var comp = jQuery('#fld_Company').attr("data-id");
			jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-area-company/?mtkn_compid=' + comp); 
			//jQuery(oEvent.target).val('&mcocd=1' + sValue);
		   
		},
		select: function( event, ui ) {
			var terms = ui.item.value;
			var mtkn_comp = ui.item.mtkn_comp;
			 var mtkn_brnch = ui.item.mtkn_brnch;
			jQuery('#fld_area_code').val(terms);
			jQuery('#fld_area_id').val(mtkn_brnch);
			jQuery('#fld_area_code').attr("data-id",mtkn_brnch);
			jQuery('#fld_Company').val(mtkn_comp);
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
	  
	}); //fld_area_code
	
	jQuery('.fld_supplier' ) 
	// don't navigate away from the field on tab when selecting an item
	.bind( 'keydown', function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
			event.preventDefault();
	}
	if( event.keyCode === jQuery.ui.keyCode.TAB ) {
		event.preventDefault();
	}
	})
	.autocomplete({
		minLength: 0,
		source: '<?=site_url();?>search-vendor/',
		focus: function() {
	// prevent value inserted on focus
	return false;
	},
	search: function(oEvent, oUi) { 
		var sValue = jQuery(oEvent.target).val();
	//jQuery(oEvent.target).val('&mcocd=1' + sValue);
	//alert(sValue);
	},
	select: function( event, ui ) {

		var terms = ui.item.value;
		jQuery('#' + this.id).attr('alt', jQuery.trim(terms));
		jQuery('#' + this.id).attr('title', jQuery.trim(terms));
		jQuery(this).attr('data-id', jQuery.trim(ui.item.mtkn_rid));
		jQuery(this).attr('data-idsupptkn', jQuery.trim(ui.item.mtkn_rid));
		jQuery('#fld_supplier_id').val(ui.item.mtkn_rid);
		jQuery('#fld_supplier_code').val(ui.item.mtkn_vcode);
		jQuery('#fld_supplier_tag').val(ui.item.mtkn_vtag);
		jQuery('#supp_id_n').val(ui.item._rid);
		vw_smc_disabled_additm(ui.item._rid);
		this.value = ui.item.value; 
		return false;
	}
	})
	.click(function() { 
		//jQuery(this).keydown(); 
		var terms = this.value.split('|');
		//jQuery(this).autocomplete('search', '');
		jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
	});
	// end fld_supplier	
	
	jQuery('#fld_rcvfrmbrnc')
	// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
				event.preventDefault();
		}
		if( event.keyCode === jQuery.ui.keyCode.TAB ) {
			event.preventDefault();
		}
	})
	.autocomplete({
		minLength: 0,
		source: '<?= site_url(); ?>search-rcv-frm-brnch-pullout/',
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		search: function(oEvent, oUi) {
			var sValue = jQuery(oEvent.target).val();
			//var comp = jQuery('#fld_Company').val();
			//var comp = jQuery('#fld_Company').attr("data-id");
			jQuery(this).autocomplete('option', 'source', '<?=site_url();?>search-rcv-frm-brnch-pullout/'); 
			//jQuery(oEvent.target).val('&mcocd=1' + sValue);
		   
		},
		select: function( event, ui ) {
			var terms = ui.item.value;
			var mtkn_comp = ui.item.mtkn_comp;
			jQuery('#fld_rcvfrmbrnc').val(terms);
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
	  
	}); //end fld_rcvfrmbrnc	
	
	
	jQuery('#mbtn_drin_cancel').click(function() { 
		try { 
			jQuery('#metrxdrincancmsg').modal('show');
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
		
	});
	
	jQuery('#metrxdrincancmsg_yes').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);	
			window.location.href = '<?=site_url();?>dr-trx';
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});			
	
	jQuery('#mbtn_drin_new').click(function() { 
		try { 
			jQuery('#metrxdrinnewcmsg').modal('show');
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
		
	});
	
	jQuery('#metrxdrinnewcmsg_yes').click(function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			window.location.href = '<?=site_url();?>dr-trx';
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});		
	
	jQuery('#metrxdrinitmdel_yes').click(function() { 
		try { 
			var meidx = jQuery('#metagged_itmremove').val();
			jQuery( '#tbl_PayData tr').eq(meidx).remove();
			var mesrmesg = jQuery('#metrxdrinitmdel_bod').html();
			jQuery('#metrxdrinitmdel_bod').html(mesrmesg + '<br/><span class="text-success text-bolder">Line Item deleted...</span>');
			jQuery(this).prop('disabled', true);
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});		
	
	__mysys_apps.mepreloader('mepreloaderme',false);
	
	jQuery('#drreclisting-tab').on('click',function() { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var mtkn_arttr = '<?=$mtkn_trxno;?>';
			var mparam = { 
				mtkn_arttr: mtkn_arttr
			}
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?= site_url() ?>dr-trx-rcv-recs',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID 
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery('#mymodoutrecs').html(data);
					return false;
				},
				error: function(data) { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}
			});		
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});	
	
	jQuery('#mebtn_searchdr').click(function() { 
		try {
			var txtsearchedrec = jQuery('#mytxtsearchrec').val();
			var fld_vw_dteto = jQuery('#fld_vw_dteto').val();
			var fld_vw_dtefrm = jQuery('#fld_vw_dtefrm').val();
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
			__mysys_apps.mepreloader('mepreloaderme',true);
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
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try					
	});  //end mebtn_searchdr
	
	/* --- receiving functions ---- */
	function __do_makeid()
	{
		var text = '';
		var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		for( var i=0; i < 7; i++ )
			text += possible.charAt(Math.floor(Math.random() * possible.length));

		return text;
	}

	  function my_add_line_item($expdate_tag = '') { 
	   try {
		var rowCount = jQuery('#tbl_PayData tr').length;
		var meid = (rowCount + 1);
		var mid = __do_makeid() + meid;
		var clonedRow = jQuery('#tbl_PayData tr:eq(' + (rowCount - 1) + ')').clone(); 
		jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id','mitemrid_' + mid);
		jQuery(clonedRow).find('input[type=hidden]').eq(1).attr('id','mid_' + mid);
		jQuery(clonedRow).find('input[type=hidden]').eq(2).attr('value',mid);
		jQuery(clonedRow).find('input[type=hidden]').eq(3).attr('id','__me_tag' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(0).attr('id','fld_mitemcode' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(1).attr('id','fld_mitemdesc' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(2).attr('id','fld_mitempkg' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(3).attr('id','fld_ucost' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(4).attr('id','fld_mitemtcost' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(5).attr('id','fld_srp' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(6).attr('id','fld_mitemtamt' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(7).attr('id','fld_mitemqty' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(8).attr('id','fld_mitemqtycorr' + mid);
		/*jQuery(clonedRow).find('input[type=text]').eq(9).attr('id','fld_remks' + mid);*/
		jQuery(clonedRow).find('input[type=text]').eq(9).attr('id','fld_mitemolt' + mid);
		jQuery(clonedRow).find('input[type=text]').eq(11).attr('id','fld_claimqty' + mid);
		if($expdate_tag == 'GRO'){
			jQuery(clonedRow).find('input[type=text]').eq(10).attr('id','fld_mitemexpdte' + mid).addClass('form_datetime exp_date');

		}
		/* jQuery(clonedRow).find('select[id=fld_mitemstore_mem_]').eq(0).attr('id','fld_mitemstore_mem_' + mid);*/


		jQuery('#tbl_PayData tr').eq(1).before(clonedRow);
		jQuery(clonedRow).css({'display':''});
		//AccName();

		var xobjArtItem= jQuery(clonedRow).find('input[type=text]').eq(0).attr('id');
		jQuery('#' + xobjArtItem).focus();

		jQuery( '#tbl_PayData tr').each(function(i) { 
				jQuery(this).find('td').eq(0).html(i);
		});

		__my_item_lookup();
		__mysys_apps.dr_show_totals();;
		} catch(err) { 
			var mtxt = 'There was an error on this page.\\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\\nClick OK to continue.';
			alert(mtxt);
			return false;
			}  //end try 
	}   
	
	function __my_item_lookup() {  
			jQuery('.mitemcode' ) 
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) {
				event.preventDefault();
		}
		if( event.keyCode === jQuery.ui.keyCode.TAB ) {
			event.preventDefault();
		}
		})
		.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>search-mat-article-vend/',
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) { 
				var sValue = jQuery(oEvent.target).val();
				//jQuery(oEvent.target).val('&mcocd=1' + sValue);
				//alert(sValue);
				
			   var fld_supplier_code = jQuery('#fld_supplier_code').val();
			   var fld_supplier_tag = jQuery('#fld_supplier_tag').val();
			   if(fld_supplier_code == ''){
					alert('Please input Supplier first!!!');
					return false;
				}
			   var fld_area_id = jQuery('#fld_area_id').val();
					if(fld_area_id == ''){
						alert('Please input Area Code/Branch first!!!');
						return false;
					}
				$(this).autocomplete('option', 'source', '<?=site_url();?>search-mat-article-vend/?pbranchid=' + fld_area_id + '&mtknvcode=' + fld_supplier_code + '&mtknvtag=' + fld_supplier_tag);
			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				
				jQuery(this).attr('alt', jQuery.trim(ui.item.ART_CODE));
				jQuery(this).attr('title', jQuery.trim(ui.item.ART_CODE));

			   this.value = ui.item.ART_CODE;

				var clonedRow = jQuery(this).parent().parent().clone();
				var indexRow = jQuery(this).parent().parent().index();
				var xobjArtMDescId = jQuery(clonedRow).find('input[type=text]').eq(1).attr('id');
				var xobjArtMUOM = jQuery(clonedRow).find('input[type=text]').eq(2).attr('id');
				var xobjArtMUcost= jQuery(clonedRow).find('input[type=text]').eq(3).attr('id');
				var xobjArtMSRP = jQuery(clonedRow).find('input[type=text]').eq(5).attr('id');
				var xobjArtMQty = jQuery(clonedRow).find('input[type=text]').eq(7).attr('id');
				var xobjArtMrid = jQuery(clonedRow).find('input[type=hidden]').eq(0).attr('id');
				jQuery('#' + xobjArtMDescId).val(ui.item.ART_DESC);
				jQuery('#' + xobjArtMUOM).val(ui.item.ART_SKU);
				jQuery('#' + xobjArtMUcost).val(ui.item.ART_UCOST);
				jQuery('#' + xobjArtMSRP).val(ui.item.ART_UPRICE);
				jQuery('#' + xobjArtMrid).val(ui.item.mtkn_rid);

				jQuery('#' + xobjArtMQty).focus();
		  return false;
			  }
		  })
			.click(function() { 
				//jQuery(this).keydown(); 
				var terms = this.value.split('=>');
				//jQuery(this).autocomplete('search', '');
				jQuery(this).autocomplete('search', jQuery.trim(terms[0]));
			});        
		}  //end __my_item_lookup
		
	jQuery('#tbl_PayData').on('keydown', "input", function(e) { 
			switch(e.which) {
				case 37: // left 
				break;
				case 38: // up
					var nidx_rw = jQuery(this).parent().parent().index();
					var nidx_td = jQuery(this).parent().index();
					if(nidx_td == 2) { 
					} else { 
						var clonedRow = jQuery('#tbl_PayData tr:eq(' + (nidx_rw) + ')').clone(); 
						var el_id = jQuery(clonedRow).find('td').eq(nidx_td).find('input[type=text]').eq(0).attr('id');
						jQuery('#' + el_id).focus();
					}
					
					break;
				case 39: // right
					break;
				case 40: // down
					var nidx_rw = jQuery(this).parent().parent().index();
					var nidx_td = jQuery(this).parent().index();
					if(nidx_td == 2) { 
					} else { 
						var clonedRow = jQuery('#tbl_PayData tr:eq(' + (nidx_rw + 2) + ')').clone(); 
						var el_id = jQuery(clonedRow).find('td').eq(nidx_td).find('input[type=text]').eq(0).attr('id');
						//alert(nidx_rw + ':' + nidx_td + ':' + el_id);
						jQuery('#' + el_id).focus();
					}
					break;
				default: return; // exit this handler for other keys
			}
			//e.preventDefault(); // prevent the default action (scroll / move caret)
	 });
	 
   function meconfirmdel(smuid){ 
	var userselection = confirm("Are you sure you want to remove this item permanently?");
		if (userselection == true){
			alert("Item deleted!");
			nullvalue(smuid);
		  }
		else{
			alert("Item is not deleted!");
		}    
		
	}  //end meconfirmdel
	
	function nullvalue(muid) {
		
		jQuery(muid).parent().parent().remove();
		jQuery( '#tbl_PayData tr').each(function(i) { 
				$(this).find('td').eq(0).html(i);
		});
		__mysys_apps.dr_show_totals();;
	} //end nullvalue
	
	function meitm_remove(meobj) { 
		try { 
			var itemtag = jQuery(meobj).attr('data-melinetag');
			var meindx = jQuery(meobj).parent().parent().index() + 1;
			var meindxobj = jQuery(meobj).parent().parent();
		
			var clonedRow = jQuery(meobj).parent().parent().clone();
			var indexRow = jQuery(meobj).parent().parent().index();
			var itemrowlabel = jQuery(clonedRow).find('td').eq(0).html();
                    			
			jQuery('#metagged_itmremove').val(meindx);
			jQuery('#metrxdrinitmdel_yes').prop('disabled', false);
			jQuery('#metrxdrinitmdel_bod').html('<span>Delete Line Item ' + itemrowlabel + '?</span>');
			jQuery('#metrxdrinitmdel').modal('show');
			return false;
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 
	} //end meitm_remove
        
	function __my_item_onchange(mtkn_tag) {  
		 var clonedRow = jQuery(mtkn_tag).parent().parent().clone();
		 var xobjArtMrid = jQuery(clonedRow).find('input[type=hidden]').eq(3).attr('id');
		 jQuery('#' + xobjArtMrid).val('Y');
	}
	
	function __my_item_onchange(mtkn_tag) {  
		 var clonedRow = jQuery(mtkn_tag).parent().parent().clone();
		 var xobjArtMrid = jQuery(clonedRow).find('input[type=hidden]').eq(3).attr('id');
		 jQuery('#' + xobjArtMrid).val('Y');
	} //end __my_item_onchange
	
	/* --- end receiving functions ---- */	
	
	/* -- saving claims --*/
	jQuery('#mbtn_mn_Claim').on('click',function(){
	   try {
		   
		   my_data = new FormData();
		   var fld_txttrx_no = jQuery('#txttrx_no').val();
		   var trxno_id      = jQuery('#__hmtkn_trxnoid').val();
		   var userrema       = '<?=$cuserrema?>';
			var claim_tag = '<?=$claim_tag;?>';
		   var filerfp       = '__pld_file_img01'; //Approved RFP
		   my_data.append('fld_txttrx_no',fld_txttrx_no);
		   my_data.append('_hdrid_mtkn',trxno_id);
		   var filerfps    = jQuery('.'+filerfp);
		   var filesCount   = 0;

		   var invalid = 0;
		   var mearray = [];
		   var adata1 = [];
		   var mdata  = [];
		   var sep = '';
		   if(trxno_id != '') {
			   if(userrema =='B') { 
				   if(claim_tag == 'Y') { 
						jQuery('#memsgme2_bod').html('Claim modification update cannot be done!!!<br/>Please coordinate to Operation/Inventory Department...');
						jQuery('#memsgme2').modal('show');
					   return false;
				   }
				   
				   jQuery.each(filerfps, function(i,filerfp) {
					   if(filerfp.files.length > 0 ){
						   jQuery.each(filerfp.files, function(k,file){
							   my_data.append('images[]', file);
							   filesCount++;
						   });
					   }
				   });
					if (filesCount == 0 ){
						   alert('Please select file to upload.');
						   return false;
					}
					if(filesCount >1 ){
						alert('Kindly compile the image into pdf format');
						return false;
					}

			   }
		   }
			__mysys_apps.mepreloader('mepreloaderme',true);
		   var rowCount1 = jQuery('#tbl_PayData tr').length-1

		   if (fld_txttrx_no != ""){
			   for(aa = rowCount1; aa > 0; aa--) { 
				   var clonedRow = jQuery('#tbl_PayData tr:eq(' + aa + ')').clone(); 
				   var __meuid = jQuery(clonedRow).find('input[type=hidden]').eq(2).val();
				   var fld_mitemqtyc = jQuery(clonedRow).find('input[type=text]').eq(8).val(); //qty c
				   var fld_claimsqty = jQuery(clonedRow).find('input[type=text]').eq(11).val(); //claims
				   var fld_mndt_rid = jQuery(clonedRow).find('input[type=hidden]').eq(1).val(); //mndt id
				   var fld_edit_tag = jQuery(clonedRow).find('input[type=hidden]').eq(3).val(); //mndt tag
				   var fld_olt_tag = jQuery(clonedRow).find('input[type=text]').eq(9).val(); //olt
				   if(fld_edit_tag == 'Y') {
					   if(adata1 != '')  {
							sep = 'x|';
						} else {
							sep = '';
						}
					   mdata = sep + __meuid + 'x|x' + fld_claimsqty + 'x|x' + fld_mndt_rid+ 'x|x' + fld_mitemqtyc+ 'x|x' + fld_olt_tag;
					   adata1.push(mdata);
					}
			   }  //end for
		   }
		   
		   if(trxno_id != ''  ){    
			   my_data.append('adata1',adata1);
			   jQuery.ajax({ 
				   type: "POST",
				   url: '<?=site_url()?>dr-trx-rcv-claims-save',
				   context: document.body,
				   data: my_data,
				   contentType: false,
				   global: false,
				   cache: false,
				   processData:false,
				   success: function(data) { 
					   __mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#memsgme2_bod').html(data);
						jQuery('#memsgme2').modal('show');
					   return false;
				   },
				   error: function() { 
					   __mysys_apps.mepreloader('mepreloaderme',false);
					   alert('error loading page...');
					   return false;
				   } 
			   }); 
		   }
		   else{
			   __mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#memsgme2_bod').html('<div class="alert alert-danger">Transaction not found.</div>');
				jQuery('#memsgme2').modal('show');					   
			   return false;
		   }
	   }
	   catch (err) { 
		   __mysys_apps.mepreloader('mepreloaderme',false);
		   var mtxt = 'There was an error on this page.\n';
		   mtxt += 'Error description: ' + err.message;
		   mtxt += '\nClick OK to continue.';
		   alert(mtxt);
	   } 
	   return false;
	});	
	
	jQuery(document).on('change', ':file', function() {
		var input     = jQuery(this),
		numFiles      = input.get(0).files ? input.get(0).files.length : 1,
		label         = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		var label     = jQuery(this).parent();
		var lblountID = jQuery(this).data('id');
		jQuery('#'+lblountID).css("margin","0rem");
		if(numFiles > 1 ){
			jQuery('#'+lblountID).text (numFiles + ' files selected');
		}
		else{
			jQuery('#'+lblountID).text (numFiles + ' file selected');
		}
	});	
	/* -- end saving claims --*/
</script>

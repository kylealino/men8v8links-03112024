<?php 
/**
 *	File        : model/Mymd_prodt_invent_model.php
 *  Auhtor      : Oliver V. Sta Maria
 *  Date Created: Sept 17, 2017
 * 	last update : Sept 17, 2017
 * 	description : Data Model handling for Product Type Iventory Master Data
 */
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mymd_acct_manrecs_model extends CI_Model { 
	public function __construct()
	{
		parent::__construct();
		$this->cusergrp = $this->mylibz->mysys_usergrp();
		
	}
	
	public function view_recs($npages = 1,$npagelimit = 30,$msearchrec='',$fld_vw_dteto='',$fld_vw_dtefrm='') { 
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		//PARA SA MGA ADMINSITRATOR LANG
		
		
		$__flag="C";
		$str_optn = "";
		$str_date="";
		if((!empty($fld_dl_dteto) && !empty($fld_dl_dtefrom)) && (($fld_dl_dteto != '--') && ($fld_dl_dtefrom != '--'))){
			$str_date="AND (aa.`rcv_date` >= '{$fld_vw_dtefrm}' AND  aa.`rcv_date` <= '{$fld_vw_dteto}')";
		}
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		//BRANCH VIEW
		$result_brnch = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='23'","myua_acct");
       	if($result_brnch == 1 && ($this->cusergrp  != 'SA')){
			$aua_branch = $this->mydatazua->ua_brnch($this->db_erp,$cuser);
			$str_branch = "aa.`branch_id` = '__MEBRNCH__' ";

			if(count($aua_branch) > 0) { 
				$str_branch = "";
				for($xx = 0; $xx < count($aua_branch); $xx++) { 
					$mbranch = $aua_branch[$xx];
					$str_branch .= "aa.`branch_id` = '$mbranch' or ";
	            } //end for 
	            $str_branch = "(" . substr($str_branch,0,strlen($str_branch) - 3) . "or aa.`muser` = '$cuser')";
	        }
			$str_vwrecs = "and {$str_branch} AND aa.df_tag ='D'";
		}
		//ADMINISTRATOR VIEW SA
		elseif($this->cusergrp == 'SA'){
			$str_vwrecs = "";
		}
		
		//IF SEARCH IS NOT EMPTY
		if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = " where (aa.`trx_no` like '%$msearchrec%' or aa.`drno` like '%$msearchrec%' or bb.`COMP_NAME` like '%$msearchrec%' or cc.`BRNCH_NAME` like '%$msearchrec%' or dd.`VEND_NAME` like '%$msearchrec%') AND aa.flag != '$__flag' {$str_date} {$str_vwrecs}";
		}
		if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_date} {$str_vwrecs}";
		}  
		$strqry = "
		select aa.*,
		bb.COMP_NAME,
		cc.BRNCH_NAME,
		dd.VEND_NAME,
		DATEDIFF(CURDATE(),aa.`encd_date`) issevenDays,
		DATEDIFF(CURDATE(),aa.`claim_date`) claimdateCount,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`trx_manrecs_hd` aa
		LEFT JOIN {$this->db_erp}.`mst_company` bb
		ON (aa.`comp_id` = bb.`recid`)
		LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc
		ON (aa.`branch_id` = cc.`recid`)
		LEFT JOIN {$this->db_erp}.`mst_vendor` dd
		ON (aa.`supplier_id` = dd.`recid`)
		{$str_optn}
		";
		
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->row_array();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa order by recid desc limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->num_rows() > 0) { 
			$data['rlist'] = $qry->result_array();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
	} 
	
	public function save() { 
		$cuser = $this->mylibz->mysys_user();
		$cuserlvl = $this->mylibz->mysys_userlvl();
		
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cuserrema=$this->mylibz->mysys_userrema();
		
		$trxno = $this->input->get_post('trxno_id');
		//$this->dbx->escape_str($this->input->get_post('fld_txttrx_no'));//systemgen
		$tfld_Company =  $this->dbx->escape_str($this->input->get_post('fld_Company'));//GET id
		$tfld_area_code = $this->dbx->escape_str($this->input->get_post('fld_area_code'));//GET id
		$tfld_supplier = $this->dbx->escape_str($this->input->get_post('fld_supplier'));//GET id
		
		//this is for branch tag,para sa walang tag default ay final
		$fld_dftag_temp  = $this->dbx->escape_str($this->input->get_post('fld_dftag'));
		$fld_dftag_r = (empty($fld_dftag_temp) ? 'F' : $fld_dftag_temp);
		$fld_dftag =(($cuserrema ==='B') ? 'D': $fld_dftag_r);
		
		$fld_drno  = $this->dbx->escape_str($this->input->get_post('fld_drno'));
		$fld_drdate = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_drdate')); 
		$fld_rcvdate = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_rcvdate'));
		$fld_datein = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_datein'));
		
		$fld_somhd_temp = $this->input->get_post('fld_somhd');
		$fld_somhd = (empty($fld_somhd_temp) ? 'D' : $fld_somhd_temp);
		
		$fld_rems = $this->input->get_post('fld_rems');
		$__rfrom = $this->input->get_post('__rfrom');

		$txt_mo_d = substr($fld_drno, 0,3);
		/*$fld_subtqty = $this->dbx->escape_str(str_replace(',','',$this->input->get_post('fld_subtqty')));
		$fld_subtcost = $this->dbx->escape_str(str_replace(',','',$this->input->get_post('fld_subtcost')));
		$fld_subtamt = $this->dbx->escape_str(str_replace(',','',$this->input->get_post('fld_subtamt')));*/
		
		if(empty($trxno)) {
			//DEADLINE
			$str = "SELECT DATE(NOW()) >=  DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))-1 DAY)  
					AND DATE(NOW()) <  DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',`CUTOFF_DATE`)) 
					AND DATE_SUB(LAST_DAY(NOW()  - INTERVAL 1 MONTH ),INTERVAL DAY(LAST_DAY(NOW()  - INTERVAL 1 MONTH ))-1 DAY)  <= DATE('$fld_rcvdate')
					OR( MONTH('$fld_rcvdate') >= MONTH(NOW()))
					AND( YEAR('$fld_rcvdate') >= YEAR(NOW()))
					AS DATE_DEADLINE FROM {$this->db_erp}.`mst_cutoff_date`";
			//var_dump($str);
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibz->user_logs_activity_module($this->db_erp,'DEADLINE','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->row_array();
			$DATE_DEADLINE = $rw['DATE_DEADLINE'];
			
			if($DATE_DEADLINE == 0) { 
				echo "<div class=\"alert alert-warning\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Unable to save DR Transaction [$fld_drno], you've reached the cut off date in encoding this transaction.</div>";
				die();
			}
			$q->free_result();
		}

		$adata1 = $this->input->get_post('adata1');
		$adata2 = $this->input->get_post('adata2');
		if(count($adata1) > 0) { 
			$mdata = "";
			$ame = array();
			$adatar1 = array();
			$adatar2 = array();
			$ntqty = 0;
			$ntamt = 0;
			$nNetamt = 0;
			$nTDisc = 0;
	
			for($aa = 0; $aa < count($adata1); $aa++) { 
				$mdata .= $adata1[$aa] . '\n';

			}
			$this->mylibz->user_logs_activity_module($this->db_erp,'ACCT_MANR_DATA1','',$cuser,$mdata,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		}
		$mmn_rid = '';
		$fld_txttrx_no = '';
		$fld_Company =  '';
		$fld_area_code = '';
		$fld_supplier = '';
		//Date In must be greater than or equal to Received Date
		if($fld_datein < $fld_rcvdate){
            echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid Entries</strong> Date In must be greater than or equal to Received Date!!!.</div>";
			die();
        }
        //RCV DATE VALIDATION
        if($fld_rcvdate=='' || $fld_rcvdate=='0000-00-00' || $fld_rcvdate=='0' || $fld_rcvdate=='--'){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Received Date is required!</div>";
			die();
		}
		
		//COMPANY
		$str = "select recid,COMP_NAME 
		 from {$this->db_erp}.`mst_company` aa where aa.`COMP_NAME` = '$tfld_Company'";
		
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->num_rows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
			die();
		}

		$rw = $q->row_array();
		$fld_Company = $rw['recid'];
		$q->free_result();
		//END COMPANY

		//BRANCH
		$str = "select recid,BRNCH_NAME 
		 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$tfld_area_code'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->num_rows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
			die();
		}

		$rw = $q->row_array();
		$fld_area_code = $rw['recid'];
		$q->free_result();
		//END BRANCH
		
		//BRANCH FROM
		if(!empty($__rfrom)){

			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$__rfrom'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$__rfrom = $rw['recid'];
			$q->free_result();
			//END BRANCH
		}
		
		//VENDOR
		$str = "select recid,VEND_NAME 
		 from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$tfld_supplier'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->user_logs_activity_module($this->db_erp,'VENDOR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->num_rows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Supplier Data!!!.</div>";
			die();
		}

		$rw = $q->row_array();
		$fld_supplier = $rw['recid'];
		$q->free_result();
		//END VENDOR
		
		//CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		/*if(!empty($trxno)) { 
			if($this->cusergrp != 'SA') { 
				$str = "select aa.muser,aa.trx_no from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.muser ='$cuser' and aa.df_tag ='D'";
				$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				if($q->num_rows() == 0){
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
					die();
				}
			}
		}*/ //END CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		
		//CHECK IF VALID TRX
		if(!empty($trxno)) { 
			$str = "select aa.recid,
			aa.trx_no,
			aa.comp_id,
			aa.branch_id,
			aa.df_tag,
			aa.drno,
			aa.dr_date,
			aa.supplier_id,
			aa.rcv_date,
			aa.date_in,
			aa.hd_sm_tags,
			aa.hd_remarks,
			aa.hd_subtqty,
			aa.hd_subtcost,
			aa.hd_subtamt,
			aa.hd_rfrom_id
			 from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' ";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction DATA!!!.</div>";
				die();
			}
			$rw = $q->row_array();
			$mmn_rid  = $rw['recid'];
			$fld_txttrx_no = $rw['trx_no'];
			$comp_id  =$rw['comp_id'];
			$branch_id  =$rw['branch_id'];
			$df_tag  =$rw['df_tag'];
			$drno  =$rw['drno'];
			$dr_date  =$rw['dr_date'];
			$supplier_id  =$rw['supplier_id'];
			$rcv_date  =$rw['rcv_date'];
			$date_in  =$rw['date_in'];
			$hd_sm_tags  =$rw['hd_sm_tags'];
			$hd_remarks  =$rw['hd_remarks'];
			$hd_subtqty  =$rw['hd_subtqty'];
			$hd_subtcost  =$rw['hd_subtcost'];
			$hd_subtamt  =$rw['hd_subtamt'];
			$hd_rfrom_id  =$rw['hd_rfrom_id'];
			$q->free_result();
		} //END CHECK IF VALID PO

		//GENERATE NEW PO CTRL NO
		else { 
			$fld_txttrx_no =  $this->mydataz->get_ctr_new($fld_Company.$fld_area_code,$fld_supplier.$fld_drno,$this->db_erp,'CTRL_NO03');//TRANSACTION NO
		} //end mtkn_potr
		//ITEM
		if(empty($adata1)) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
			die();
		}
		if(count($adata1) > 0) { 
			$ame = array();
			$adatar1 = array();
			$adatar2 = array();
			$ntqty = 0;
			$ntamt = 0;
			$ntcost = 0;
	
			for($aa = 0; $aa < count($adata1); $aa++) { 
				$medata = explode("x|x",$adata1[$aa]);
				$mat_mtkn = $adata2[$aa];
				$fld_mitemcode = $this->dbx->escape_str(trim($medata[0]));
				$fld_mitemdesc = $this->dbx->escape_str(trim($medata[1]));
				$fld_mitempkg = $this->dbx->escape_str(trim($medata[2]));
				$fld_ucost =(empty(str_replace(',','',$medata[3])) ? 0 : (str_replace(',','',$medata[3]) + 0));
				$fld_mitemtcost = (empty(str_replace(',','',$medata[4])) ? 0 : (str_replace(',','',$medata[4]) + 0));
				$fld_srp =  (empty(str_replace(',','',$medata[5])) ? 0 : (str_replace(',','',$medata[5]) + 0));
				$fld_mitemtamt =(empty(str_replace(',','',$medata[6])) ? 0 : (str_replace(',','',$medata[6]) + 0));
				$fld_mitemqty = (empty(str_replace(',','',$medata[7])) ? 0 : (str_replace(',','',$medata[7]) + 0));
				$fld_mitemqtyc = (empty(str_replace(',','',$medata[8])) ? 0 : (str_replace(',','',$medata[8]) + 0));
				$fld_remks = $this->dbx->escape_str(trim($medata[9]));
				$fld_olt = $this->dbx->escape_str(trim($medata[10]));
				$fld_som = $this->dbx->escape_str(trim($medata[11]));
				$fld_expdate = '';
				if(!empty($medata[13])){
					$fld_expdate = $this->mylibz->mydate_yyyymmdd($medata[13]);
				}
				//COMPUTATION ON SAVING
				$fld_mitemtcost = ($fld_mitemqtyc * $fld_ucost);//ACTUAL QTY * fld_ucost
				$fld_mitemtamt =($fld_mitemqtyc * $fld_srp); //ACTUAL QTY * fld_srp/price
				
				$ntqty = $ntqty + $fld_mitemqtyc;//actual hd_subtqty
				$ntcost = $ntcost + $fld_mitemtcost;//actual hd_subtcost
				$ntamt = $ntamt + $fld_mitemtamt;//actual hd_subtamt
				
				//GETTING THE GRAND TOTAL HD BASED ON ACTUAL
				$fld_subtqty = $this->dbx->escape_str(str_replace(',','',$ntqty));
				$fld_subtcost = $this->dbx->escape_str(str_replace(',','',$ntcost));
				$fld_subtamt = $this->dbx->escape_str(str_replace(',','',$ntamt));
				//$total_pcs = $nconvf*$nqty;
				//$cmat_code = $this->dbx->escape_str(trim($medata[0])) . $mktn_plnt_id . $mtkn_wshe_id;
				
				$amatnr = array();
				if(!empty($fld_mitemcode)) { 
					$fld_mitemcode = urldecode($fld_mitemcode);
					$str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn' and aa.`ART_CODE` = '$fld_mitemcode' ";
					$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->num_rows() == 0) { 
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$fld_mitemcode]</div>";
						die();
					} else { 
						//VALIDATION OF ITEMS,QTY,PRICE
						//if(in_array($cmat_code,$ame)) { 
						if(in_array($fld_mitemcode,$ame)) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Material Data already exists [$fld_mitemcode]</div>";
							die();
						} else { 
							//if($fld_dftag== 'F' AND ($fld_mitemqty == 0 || $fld_mitemtcost == 0 || $fld_mitemtamt == 0)) { 
							if($cuserlvl != 'S' && $fld_dftag == 'F' && !($fld_supplier == '3') && (($fld_ucost == 0 || $fld_srp == 0) || ($fld_datein == '' || $fld_datein == 0 || $fld_datein=='0000-00-00' || $fld_datein=='yyyy-0m-dd'))) { 
								echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>[Info]{$fld_ucost}-{$fld_srp}<br/></strong><strong>Error</strong> Invalid QTY or Price entries!!! </br>Note: Final Tags required to fill in all fields.</div>";
								die();
							}
							
							if($cuserlvl != 'S' && $fld_somhd == 'D' && !($fld_supplier == '3' || $fld_supplier == '4105') && ($fld_srp <= $fld_ucost)){ // || $fld_supplier != '4105'
                               echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Unit Price must be greater than to Unit Cost [$fld_mitemcode]</div>";
							   die();
                        	}
                        	if($txt_mo_d == "GRO"){
			                	if(empty($fld_expdate)){
			                		echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Expiration Date is required [$fld_mitemcode]</div>";
							   		die();
			                	}
			                }
			                if(!empty($fld_expdate)){
			                	$str = "SELECT DATE('$fld_expdate') <= DATE(NOW())  __IFEXPIRED ";
								$qs = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								$rws = $qs->row_array();
								$__IFEXPIRED  = $rws['__IFEXPIRED'];
								if($__IFEXPIRED == 1){
				                	echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Expiration Date was expired [$fld_mitemcode]</div>";
							   		die();
			                	}
			                }
                        	
							
						}
						
						$rw = $q->row_array();
						$mmat_rid = $rw['recid'];
						//array_push($ame,$cmat_code); 
						array_push($ame,$fld_mitemcode); 
						array_push($adatar1,$medata);
						array_push($adatar2,$mmat_rid);
						/*$ntqty = ($ntqty + $nqty);*/
						//$ntamt = ($ntamt + ($nprice * $nconvf * $nqty));
						//$ntamt = ($ntamt + ($tamt));
					}

					$q->free_result();
				}

			}  //end for 
		

			//if(count($adatar1) > 0) {
			if(((count($adatar1) == 0) && (!empty($trxno))) || ((count($adatar1) > 0) && ((empty($trxno)) || (!empty($trxno)))  )) {  
				if(!empty($trxno)) { 
					if($txt_mo_d != "GRO"){
						//DR bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
						$str = "select aa.`drno` from {$this->db_erp}.`trx_manrecs_hd` aa where aa.`drno` = '$fld_drno' AND aa.`supplier_id` = '$fld_supplier' AND !(aa.`flag`='C') AND !(sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$trxno')";
						$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->num_rows() > 0) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> DR No already exists.!!!.[".$fld_drno."]</div>";
							die();
						}
					}
					
					$str = "
					update {$this->db_erp}.`trx_manrecs_hd`
					SET `comp_id` = '$fld_Company',
					  	`branch_id` = '$fld_area_code',
					  	`df_tag`='$fld_dftag',
					  	`drno` = trim('$fld_drno'),
					  	`dr_date` ='$fld_drdate',
					  	`supplier_id` = '$fld_supplier',
					  	`rcv_date` = '$fld_rcvdate',
					  	`date_in` ='$fld_datein',
					  	`hd_sm_tags` ='$fld_somhd',
						`hd_remarks` ='$fld_rems',
						`hd_subtqty`='$fld_subtqty',
						`hd_subtcost`='$fld_subtcost',
						`hd_subtamt`='$fld_subtamt',
						`hd_rfrom_id`='$__rfrom'
					WHERE `recid` = '$mmn_rid';
					";
					$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibz->user_logs_activity_module($this->db_erp,'MN_UREC'.$cuser,'',$fld_txttrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
					$arrfield = '';
					if($comp_id != $fld_Company){
						$arrfield .= "COMPANY" . "->" . $tfld_Company . "\n";
					}
					if($branch_id != $fld_area_code){
						$arrfield .= "BRANCH" . "->" . $tfld_area_code . "\n";
					}
					if($df_tag != $fld_dftag){
						$arrfield .= "D/F TAG" . "->" . $fld_dftag . "\n";
					}
					if($drno != $fld_drno){
						$arrfield .= "DR NUMBER" . "->" . $fld_drno . "\n";
					}
					if($dr_date != $fld_drdate){
						$arrfield .= "DR DATE" . "->" . $fld_drdate . "\n";
					}
					if($supplier_id != $fld_supplier){
						$arrfield .= "SUPPLIER" . "->" . $tfld_supplier . "\n";
					}
					if($rcv_date != $fld_rcvdate){
						$arrfield .= "RECEIVED DATE" . "->" . $fld_rcvdate . "\n";
					}
					if($date_in != $fld_datein){
						$arrfield .= "DATE IN" . "->" . $fld_datein . "\n";
					}
					if($hd_sm_tags != $fld_somhd){
						$arrfield .= "S/M TAG" . "->" . $fld_somhd . "\n";
					}
					if($hd_remarks != $fld_rems){
						$arrfield .= "REMARKS" . "->" . $fld_rems . "\n";
					}
					if($hd_subtqty != $fld_subtqty && $fld_subtqty != 0){
						$arrfield .= "TOTAL QTY" . "->" . $fld_subtqty . "\n";
					}
					if($hd_subtcost != $fld_subtcost && $fld_subtcost != 0){
						$arrfield .= "TOTAL COST" . "->" . $fld_subtcost . "\n";
					}
					if($hd_subtamt != $fld_subtamt && $fld_subtamt != 0){
						$arrfield .= "TOTAL AMOUNT" . "->" . $fld_subtamt . "\n";
					}
					if($hd_rfrom_id != $__rfrom){
						$arrfield .= "RECEIVED FRM BRANCH" . "->" . $__rfrom . "\n";
					}
					
					if(!empty($arrfield)){
						$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_HEADER','R');	
					}
					

				} else { 
					if($txt_mo_d != "GRO"){
						//DR bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
						$str = "select aa.`drno` from {$this->db_erp}.`trx_manrecs_hd` aa where aa.`drno` = '$fld_drno' AND aa.`supplier_id` = '$fld_supplier' AND !(aa.`flag`='C')";
						$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->num_rows() > 0) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> DR No already exists.!!!.[".$fld_drno."]</div>";
							die();
						}
					}

					//END DR
					
					$str = "insert into {$this->db_erp}.`trx_manrecs_hd`
					(`trx_no`,
					`comp_id`,
					`branch_id`,
					`drno`,
					`dr_date`,
					`supplier_id`,
					`rcv_date`,
					`date_in`,
					`hd_sm_tags`, 
					`hd_remarks`,
					`hd_subtqty`,
					`hd_subtcost`,
					`hd_subtamt`,
					`hd_rfrom_id`,
					`muser`,
					`df_tag`)
					VALUES ('$fld_txttrx_no',
					'$fld_Company',
					'$fld_area_code',
					trim('$fld_drno'),
					'$fld_drdate',
					'$fld_supplier',
					'$fld_rcvdate',
					'$fld_datein',
					'$fld_somhd',
					'$fld_rems',
					'$fld_subtqty',
					'$fld_subtcost',
					'$fld_subtamt',
					'$__rfrom',
					'$cuser',
					'$fld_dftag')";
					$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibz->user_logs_activity_module($this->db_erp,'MN_AREC','',$fld_txttrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_mntr from {$this->db_erp}.`trx_manrecs_hd` aa where `trx_no` = '$fld_txttrx_no' ";
					$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$rw = $q->row_array();
					$mmn_rid = $rw['recid'];
					//var_dump($mmn_rid);
					$__hmtkn_mntr = $rw['mtkn_mntr'];
					$q->free_result();
					
					$arrfield = '';
					$arrfield .= "COMPANY" . "->" . $tfld_Company . "\n";
					$arrfield .= "BRANCH" . "->" . $tfld_area_code . "\n";
					$arrfield .= "D/F TAG" . "->" . $fld_dftag . "\n";
					$arrfield .= "DR NUMBER" . "->" . $fld_drno . "\n";
					$arrfield .= "DR DATE" . "->" . $fld_drdate . "\n";
					$arrfield .= "SUPPLIER" . "->" . $tfld_supplier . "\n";
					$arrfield .= "RECEIVED DATE" . "->" . $fld_rcvdate . "\n";
					$arrfield .= "DATE IN" . "->" . $fld_datein . "\n";
					$arrfield .= "S/M TAG" . "->" . $fld_somhd . "\n";
					$arrfield .= "REMARKS" . "->" . $fld_subtqty . "\n";
					$arrfield .= "TOTAL QTY" . "->" . $fld_rems . "\n";
					$arrfield .= "TOTAL COST" . "->" . $fld_subtcost . "\n";
					$arrfield .= "TOTAL AMOUNT" . "->" . $fld_subtamt . "\n";
					$arrfield .= "RECEIVED FRM BRANCH" . "->" . $__rfrom . "\n";
					
					$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'A','ADD_RECEIVING_HEADER','R');


				}

				//GET PLNT, WSHE, SBIN


				for($xx = 0; $xx < count($adatar1); $xx++) {  //MAY MALI DITO
		
					$xdata = $adatar1[$xx];
					$mat_rid = $adatar2[$xx];
					
					//$fld_mitemrid = $this->dbx->escape_str(trim($xdata[0]));
					$fld_mitemcode = $xdata[0];
					$fld_mitemdesc = $this->dbx->escape_str(trim($xdata[1]));
					$fld_mitempkg = $this->dbx->escape_str(trim($xdata[2]));
					$fld_ucost = (empty(str_replace(',','',$xdata[3])) ? 0 : (str_replace(',','',$xdata[3]) + 0));
					$fld_mitemtcost = (empty(str_replace(',','',$xdata[4])) ? 0 : (str_replace(',','',$xdata[4]) + 0));
					$fld_srp =  (empty(str_replace(',','',$xdata[5])) ? 0 : (str_replace(',','',$xdata[5]) + 0));
					$fld_mitemtamt =(empty(str_replace(',','',$xdata[6])) ? 0 : (str_replace(',','',$xdata[6]) + 0));
					$fld_mitemqty = (empty(str_replace(',','',$xdata[7])) ? 0 : (str_replace(',','',$xdata[7]) + 0));
					$fld_mitemqtyc = (empty(str_replace(',','',$xdata[8])) ? 0 : (str_replace(',','',$xdata[8]) + 0));
					$fld_remks = $this->dbx->escape_str(trim($xdata[9]));
					$fld_olt = $this->dbx->escape_str(trim($xdata[10]));
					$fld_som = $this->dbx->escape_str(trim($xdata[11]));
					$mndt_rid = $this->dbx->escape_str(trim($xdata[12]));//dt mn id
					$fld_expdate = '';
					if(!empty($xdata[13])){
						$fld_expdate = $this->mylibz->mydate_yyyymmdd($xdata[13]);
					}
					//COMPUTATION ON SAVING
					$fld_mitemtcost = ($fld_mitemqtyc * $fld_ucost);//ACTUAL QTY * fld_ucost
					$fld_mitemtamt =($fld_mitemqtyc * $fld_srp); //ACTUAL QTY * fld_srp/price
				
				//	$tamt = $xdata[7];

					
					
					if(empty($trxno)) {  
						
						$str = "select recid from {$this->db_erp}.`trx_manrecs_dt` where `trx_no` = '$fld_txttrx_no' and `mat_rid` = '$mat_rid'";
						$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->num_rows() > 0 ) { 
							$rw = $q->row_array();
							$mndt_rid = $rw['recid'];

							$str = "update {$this->db_erp}.`trx_manrecs_dt`
							SET `mat_rid` = '$mat_rid',
							  `mat_code` = '$fld_mitemcode',
							  `qty` = '$fld_mitemqty',
							  `ucost` = '$fld_ucost',
							  `tcost` = '$fld_mitemtcost',
							  `uprice` = '$fld_srp',
							  `tamt` = '$fld_mitemtamt',
							  `qty_corrected` = '$fld_mitemqtyc',
							  `OLT_Tag` = '$fld_olt',
							  `SM_Tag`='$fld_som',
							  `exp_date`='$fld_expdate',
							  `nremarks` = '$fld_remks',
							  `muser` = '$cuser'
							WHERE `recid` = '$mndt_rid'
							";
							
							$arrfield_dtl = '';
							$arrfield_dtl .= "ITEMCODE" . "->" . $fld_mitemcode . "\n";
							$arrfield_dtl .= "DR QTY" . "->" . $fld_mitemqty . "\n";
							$arrfield_dtl .= "ACTUAL QTY" . "->" . $fld_mitemqtyc . "\n";
							$arrfield_dtl .= "COST" . "->" . $fld_ucost . "\n";
							$arrfield_dtl .= "TOTAL COST" . "->" . $fld_mitemtcost . "\n";
							$arrfield_dtl .= "UNIT PRICE" . "->" . $fld_srp . "\n";
							$arrfield_dtl .= "TOTAL AMOUNT" . "->" . $fld_mitemtamt . "\n";
							$arrfield_dtl .= "OLT TAG" . "->" . $fld_olt . "\n";
							$arrfield_dtl .= "S/M TAG" . "->" . $fld_som . "\n";
							$arrfield_dtl .= "EXP DATE" . "->" . $fld_expdate . "\n";
							$arrfield_dtl .= "REMARKS" . "->" . $fld_remks . "\n";
							
							$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_DTLS','R');
						} else { 
							$str = "insert into {$this->db_erp}.`trx_manrecs_dt`
							(`mrhd_rid`,
							`trx_no`,
							`mat_rid`,
							`mat_code`,
							`ucost`,
							`tcost`,
							`uprice`,
							`tamt`,
							`qty`,
							`qty_corrected`,
							`OLT_Tag`,
							`SM_Tag`,
							`exp_date`,
							`nremarks`,
							`muser`)
							VALUES ('$mmn_rid',
							'$fld_txttrx_no',
							'$mat_rid',
							'$fld_mitemcode',
							'$fld_ucost',
							'$fld_mitemtcost',
							'$fld_srp',
							'$fld_mitemtamt',
							'$fld_mitemqty',
							'$fld_mitemqtyc',
							'$fld_olt',
							'$fld_som',
							'$fld_expdate',
							'$fld_remks',
							'$cuser')
							";
							
							$arrfield_dtl = '';
							$arrfield_dtl .= "ITEMCODE" . "->" . $fld_mitemcode . "\n";
							$arrfield_dtl .= "DR QTY" . "->" . $fld_mitemqty . "\n";
							$arrfield_dtl .= "ACTUAL QTY" . "->" . $fld_mitemqtyc . "\n";
							$arrfield_dtl .= "COST" . "->" . $fld_ucost . "\n";
							$arrfield_dtl .= "TOTAL COST" . "->" . $fld_mitemtcost . "\n";
							$arrfield_dtl .= "UNIT PRICE" . "->" . $fld_srp . "\n";
							$arrfield_dtl .= "TOTAL AMOUNT" . "->" . $fld_mitemtamt . "\n";
							$arrfield_dtl .= "OLT TAG" . "->" . $fld_olt . "\n";
							$arrfield_dtl .= "S/M TAG" . "->" . $fld_som . "\n";
							$arrfield_dtl .= "EXP DATE" . "->" . $fld_expdate . "\n";
							$arrfield_dtl .= "REMARKS" . "->" . $fld_remks . "\n";
							
							//$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'A','ADD_RECEIVING_DTLS','R');
						}
						$q->free_result();
						$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
						$this->mylibz->user_logs_activity_module($this->db_erp,'TRX_mn_DT','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($cuserlvl == 'S' && (($fld_srp <= $fld_ucost) || ($fld_ucost == 0) || ($fld_srp == 0))){
							$this->mylibz->user_logs_override_itemcode($this->db_erp,'ITEM_OVERRIDE','',$cuser,$fld_txttrx_no.' ITEMCODE:'.$fld_mitemcode.'= SRP: '.$fld_srp.'<= COST:'.$fld_ucost,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						}
						
						
						
						
						
					} else { 
						if(empty($mndt_rid)) { 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_dt` where `trx_no` = '$fld_txttrx_no' and `mat_rid` = '$mat_rid'";
							
							$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($q->num_rows() > 0 ) { 
								$rw = $q->row_array();
								$mn_rid = $rw['recid'];
								$str = "update {$this->db_erp}.`trx_manrecs_dt`
										SET `mat_rid` = '$mat_rid',
										  `mat_code` = '$fld_mitemcode',
										  `qty` = '$fld_mitemqty',
										  `qty_corrected` = '$fld_mitemqtyc',
							  			  `OLT_Tag` = '$fld_olt',
							  			  `SM_Tag`='$fld_som',
							  			  `exp_date`='$fld_expdate',
							  			  `ucost` = '$fld_ucost',
										  `tcost` = '$fld_mitemtcost',
										  `uprice` = '$fld_srp',
										  `tamt` = '$fld_mitemtamt',
										  `nremarks` = '$fld_remks',
							  			  `muser` = '$cuser'
										WHERE `recid` = '$mn_rid'
										";
										
										$arrfield_dtl = '';
										$arrfield_dtl .= "ITEMCODE" . "->" . $fld_mitemcode . "\n";
										$arrfield_dtl .= "DR QTY" . "->" . $fld_mitemqty . "\n";
										$arrfield_dtl .= "ACTUAL QTY" . "->" . $fld_mitemqtyc . "\n";
										$arrfield_dtl .= "COST" . "->" . $fld_ucost . "\n";
										$arrfield_dtl .= "TOTAL COST" . "->" . $fld_mitemtcost . "\n";
										$arrfield_dtl .= "UNIT PRICE" . "->" . $fld_srp . "\n";
										$arrfield_dtl .= "TOTAL AMOUNT" . "->" . $fld_mitemtamt . "\n";
										$arrfield_dtl .= "OLT TAG" . "->" . $fld_olt . "\n";
										$arrfield_dtl .= "S/M TAG" . "->" . $fld_som . "\n";
										$arrfield_dtl .= "EXP DATE" . "->" . $fld_expdate . "\n";
										$arrfield_dtl .= "REMARKS" . "->" . $fld_remks . "\n";
										
										$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_DTLS','R');
						} else { 
							$str = "insert into {$this->db_erp}.`trx_manrecs_dt`
							(`mrhd_rid`,
							`trx_no`,
							`mat_rid`,
							`mat_code`,
							`ucost`,
							`tcost`,
							`uprice`,
							`tamt`,
							`qty`,
							`qty_corrected`,
							`OLT_Tag`,
							`SM_Tag`,
							`exp_date`,
							`nremarks`,
							`muser`)
							VALUES ('$mmn_rid',
							'$fld_txttrx_no',
							'$mat_rid',
							'$fld_mitemcode',
							'$fld_ucost',
							'$fld_mitemtcost',
							'$fld_srp',
							'$fld_mitemtamt',
							'$fld_mitemqty',
							'$fld_mitemqtyc',
							'$fld_olt',
							'$fld_som',
							'$fld_expdate',
							'$fld_remks',
							'$cuser')
							";
							
							$arrfield_dtl = '';
							$arrfield_dtl .= "ITEMCODE" . "->" . $fld_mitemcode . "\n";
							$arrfield_dtl .= "DR QTY" . "->" . $fld_mitemqty . "\n";
							$arrfield_dtl .= "ACTUAL QTY" . "->" . $fld_mitemqtyc . "\n";
							$arrfield_dtl .= "COST" . "->" . $fld_ucost . "\n";
							$arrfield_dtl .= "TOTAL COST" . "->" . $fld_mitemtcost . "\n";
							$arrfield_dtl .= "UNIT PRICE" . "->" . $fld_srp . "\n";
							$arrfield_dtl .= "TOTAL AMOUNT" . "->" . $fld_mitemtamt . "\n";
							$arrfield_dtl .= "OLT TAG" . "->" . $fld_olt . "\n";
							$arrfield_dtl .= "S/M TAG" . "->" . $fld_som . "\n";
							$arrfield_dtl .= "EXP DATE" . "->" . $fld_expdate . "\n";
							$arrfield_dtl .= "REMARKS" . "->" . $fld_remks . "\n";
							
							$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'A','ADD_RECEIVING_DTLS','R');
							}
							$q->free_result();
							$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
							$this->mylibz->user_logs_activity_module($this->db_erp,'trx_po_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($cuserlvl == 'S' && (($fld_srp <= $fld_ucost) || ($fld_ucost == 0) || ($fld_srp == 0))){
								$this->mylibz->user_logs_override_itemcode($this->db_erp,'ITEM_OVERRIDE','',$cuser,$fld_txttrx_no.' ITEMCODE:'.$fld_mitemcode.'= SRP: '.$fld_srp.'<= COST:'.$fld_ucost,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							}
							
						} else { // end empty podt_rid 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_dt` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mndt_rid'";
							$qq = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($qq->num_rows() > 0) { 
								$rrw = $qq->row_array();
								$mn_dtrid = $rrw['recid'];
								$str = "
								update {$this->db_erp}.`trx_manrecs_dt`
										SET `mat_rid` = '$mat_rid',
										  `mat_code` = '$fld_mitemcode',
										  `qty` = '$fld_mitemqty',
										  `qty_corrected` = '$fld_mitemqtyc',
							  			  `OLT_Tag` = '$fld_olt',
							  			  `SM_Tag`='$fld_som',
							  			  `exp_date`='$fld_expdate',
							  			  `ucost` = '$fld_ucost',
										  `tcost` = '$fld_mitemtcost',
										  `uprice` = '$fld_srp',
										  `tamt` = '$fld_mitemtamt',
										  `nremarks` = '$fld_remks',
										  `muser` = '$cuser'
										WHERE `recid` = '$mn_dtrid'";
								$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
								$this->mylibz->user_logs_activity_module($this->db_erp,'trx_mn_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								if($cuserlvl == 'S' && (($fld_srp <= $fld_ucost) || ($fld_ucost == 0) || ($fld_srp == 0))){
									$this->mylibz->user_logs_override_itemcode($this->db_erp,'ITEM_OVERRIDE','',$cuser,$fld_txttrx_no.' ITEMCODE:'.$fld_mitemcode.'= SRP: '.$fld_srp.'<= COST:'.$fld_ucost,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								}
								
								$arrfield_dtl = '';
								$arrfield_dtl .= "ITEMCODE" . "->" . $fld_mitemcode . "\n";
								$arrfield_dtl .= "DR QTY" . "->" . $fld_mitemqty . "\n";
								$arrfield_dtl .= "ACTUAL QTY" . "->" . $fld_mitemqtyc . "\n";
								$arrfield_dtl .= "COST" . "->" . $fld_ucost . "\n";
								$arrfield_dtl .= "TOTAL COST" . "->" . $fld_mitemtcost . "\n";
								$arrfield_dtl .= "UNIT PRICE" . "->" . $fld_srp . "\n";
								$arrfield_dtl .= "TOTAL AMOUNT" . "->" . $fld_mitemtamt . "\n";
								$arrfield_dtl .= "OLT TAG" . "->" . $fld_olt . "\n";
								$arrfield_dtl .= "S/M TAG" . "->" . $fld_som . "\n";
								$arrfield_dtl .= "EXP DATE" . "->" . $fld_expdate . "\n";
								$arrfield_dtl .= "REMARKS" . "->" . $fld_remks . "\n";
								
								$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_DTLS','R');
							}
							$qq->free_result();

							

							
						}  //end 
						
					}
					
					
				}  //end for 
				//PARA SA PILI LANG O WALA INUPDATE SA DETAILS
				if(((count($adatar1) == 0) && (!empty($trxno))) || ((count($adatar1) > 0) && (!empty($trxno)))){
					$str = " SELECT mrhd_rid,
						SUM(`qty_corrected`) qty,
						SUM(`tcost`) tcost,
						SUM(`tamt`) tamt 
						FROM {$this->db_erp}.`trx_manrecs_dt` 
						WHERE `mrhd_rid` = '$mmn_rid' 
						GROUP BY `mrhd_rid` ";

					$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

					$rw = $q->row_array();
					$fld_subtqty = $rw['qty'];
					$fld_subtcost = $rw['tcost'];
					$fld_subtamt = $rw['tamt'];

					$str = "
					update {$this->db_erp}.`trx_manrecs_hd`
					SET `hd_subtqty`='$fld_subtqty',
						`hd_subtcost`='$fld_subtcost',
						`hd_subtamt`='$fld_subtamt'
					WHERE `recid` = '$mmn_rid'
					";
					$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibz->user_logs_activity_module($this->db_erp,'MN_UREC_RECON_HD_QTY','',$fld_txttrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				}//endif

				
				//record on AV Work Flow
//$qry->free_result();	
				if(empty($trxno)) { 
					echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Recorded Successfully!!!</div>
					<script type=\"text/javascript\"> 
						function __po_refresh_data() { 
							try { 
								$('#__hmtkn_trxnoid').val('{$__hmtkn_mntr}');
								$('#txttrx_no').val('{$fld_txttrx_no}');
								$('#mbtn_mn_Save').prop('disabled',true);
							} catch(err) { 
								var mtxt = 'There was an error on this page.\\n';
								mtxt += 'Error description: ' + err.message;
								mtxt += '\\nClick OK to continue.';
								alert(mtxt);
								return false;
							}  //end try 
						} 
						
						__po_refresh_data();
					</script>
					";
					die();
				} else { 
					echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
					";
					die();
				}
			} else { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Item Data!!!.</div>";
				die();
			} //end if 
		} else { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
			die();
		}
		
		
	}
	
	public function delrecs() { 
			$cuser 			= $this->mylibz->mysys_user();
			$mpw_tkn 		= $this->mylibz->mpw_tkn();
			//$mtkn_mndt_rid: = $this->input->get_post('mtkn_podt_rid');
			$mtkn_mmn_rid  = $this->input->get_post('mtkn_mmn_rid');
			$mtkn_mndt_rid  = $this->input->get_post('mtkn_mndt_rid');
		

			$str = "select * from {$this->db_erp}.`trx_manrecs_dt` where sha2(concat(recid,'{$mpw_tkn}'),384) = '$mtkn_mndt_rid'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);



			if($q->num_rows() > 0) { 
				$rw 		= $q->row_array();
				$txtartid 	= $rw['mat_rid'];
				$mat_code 	= $rw['mat_code'];
				$trxno 	    = $rw['trx_no'];
	

				$str = "delete from {$this->db_erp}.`trx_manrecs_dt` where sha2(concat(recid,'{$mpw_tkn}'),384) = '$mtkn_mndt_rid'";
				$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
				$this->mylibz->user_logs_activity_module($this->db_erp,'SOITEM_DREC','',$txtartid,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$arrfield = '';
				$arrfield .= "ITEMCODE" . "->" . $mat_code . "\n";
				
				$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$trxno,'D','DELETE_RECEIVING_DTL','R');
				echo "
				<div class=\" alert alert-success\">
				<strong>Success</strong> 
				<p>Records successfully deleted!!!</p>
				</div>
				
				";
			} 
			else 
			{
				echo "
				<div class=\" alert alert-danger\">
				<strong>Error</strong> 
				<p>Records already deleted!!!</p>
				</div>
				";
			}
	}  //end delrecs
	public function canrecs(){
			$cuser 			= $this->mylibz->mysys_user();
			$mpw_tkn 		= $this->mylibz->mpw_tkn();
			//$mtkn_mndt_rid: = $this->input->get_post('mtkn_podt_rid');
			$mtkn_rid  = $this->input->get_post('mtkn_itm');
			
		

			$str = "select * from {$this->db_erp}.`trx_manrecs_hd` where sha2(concat(recid,'{$mpw_tkn}'),384) = '$mtkn_rid'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);



			if($q->num_rows() > 0) { 
				$rw 		= $q->row_array();
				$txtrecid 	= $rw['recid'];
				$trxno 	    = $rw['trx_no'];
				$__flag="C";

				$str = "update {$this->db_erp}.`trx_manrecs_hd` set `flag` = '$__flag' where sha2(concat(recid,'{$mpw_tkn}'),384) = '$mtkn_rid'";
				$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
				$this->mylibz->user_logs_activity_module($this->db_erp,'RCV_CREC','',$txtrecid,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$arrfield = '';
				$arrfield .= "TRANSACTION" . "->" . $trxno . "\n";
				
				$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$trxno,'D','DELETE_RECEIVING_HEADER','R');
				
				echo "
				<div class=\" alert alert-success\">
				<strong>Success</strong> 
				<p>Records successfully deleted!!!</p>
				</div>
				
				";
			} 
			else 
			{
				echo "
				<div class=\" alert alert-danger\">
				<strong>Error</strong> 
				<p>Records already deleted!!!</p>
				</div>
				";
			}
	}
	public function inv_rpt_download_proc(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();

		$fld_dlsupp =$this->input->get_post('fld_dlsupp');
		$fld_dlsupp_id =$this->input->get_post('fld_dlsupp_id');
		$fld_months =$this->input->get_post('fld_months');
		$fld_years =$this->input->get_post('fld_years');
		$str_supp_aa='';
		$str_supp='';
		$grp_supp='';
		$fld_ADDR1 ='';
		$fld_ADDR2 ='';
		$fld_TELNO ='';
		$chtmlhd="";
		$chtmljs ="";
		$chtml = "";
		$cmsexp =  "";
		$cmsgt =  "";
		$chtml2 = "";
		$chtml3 = "";
		$chtml4 = "";
		$cmsft =  "";
		$date = date("F j, Y, g:i A");
		//CONVERTING MONTH to name
		$dateObj   = DateTime::createFromFormat('!m', $fld_months);
		$monthName = $dateObj->format('F');
		//VALIDATING DATA SUPPLIER
		if(!empty($fld_dlsupp)) {
		$str = "select recid,
			VEND_NAME,
			VEND_ADDR1 ADDR1,
			VEND_ADDR2 ADDR2,
			VEND_TELNO TELNO from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$fld_dlsupp' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				Invalid Supplier Data	
				";
				die();
			}//end if
			$rw = $q->row_array();
			$fld_ADDR1 = $rw['ADDR1'];
			$fld_ADDR2 = $rw['ADDR2'];
			$fld_TELNO = $rw['TELNO'];
			$q->free_result();
			$str_supp_aa= "AND sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
			$str_supp= "AND sha2(concat(`supplier_id`,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
			$grp_supp=",`supplier_id`";
		}//end if
		
		
		$chtmljs .= "
		<div class=\"col-md-6\" id=\"__mtoexport\">
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success fa fa-download\"> INVENTORY SUMMARY /BRANCH</i></a></span>
			</div>
			</br>
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_sb\"><i class=\"btn btn-success fa fa-download\"> INVENTORY SUMMARY /SKU /BRANCH</i></a></span>
			</div>
			</br>
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_c\"><i class=\"btn btn-success fa fa-download\"> INVENTORY SUMMARY /COMPANY</i></a></span>
			</div>
			</br>
			
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_s\"><i class=\"btn btn-success fa fa-download\"> INVENTORY STATEMENT</i></a></span>
			</div>
        </div>	
		";
	////////////////////////////////////////////////////////////////////////SUMMARY BRANCH/////////////////////////////////////////////////////////////////////////////////
        $chtml = "
					<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
						<head>
						<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
						</head>
						<body>
                	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable\">
					   
					     <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">Inventory counter of the month per branch</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">".$monthName." ".$fld_years."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">SUPPLIER: ".$fld_dlsupp."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">&nbsp;</th>
					      </tr>
					      <tr class =\"header-theme-purple text-white\">
					        <th class=\"noborder\">No</th>
					        <th class=\"noborder\">BRANCHES</th>
					        <th class=\"noborder\">UNIQUE IDENTIER</th>
					        <th class=\"noborder\">QUANTITY</th>
					        <th class=\"noborder\">SRP</th>
					        <th class=\"noborder\">MARK UP</th>
					        <th class=\"noborder\">COST</th>
					        <th class=\"noborder\">PURCHASE RETURN COST</th>
					        <th class=\"noborder\">PAYABLES</th>
					        </tr>
					    ";
		
		//QUERY

		$str="
		SELECT 
		xx.`BRANCHES`,
		xx.`BRNCH_OCODE3`,
		xx.`QUANTITY`,
		xx.`SRP`,
		xx.`COST`,
		xx.`MARK_UP`,
		IFNULL(yy.`__hd_subtcost`,0) PRC,
		(xx.`COST` - IFNULL(yy.`__hd_subtcost`,0)) PAYABLES  
		FROM 
			((SELECT cc.`BRNCH_NAME` BRANCHES,
			cc.`BRNCH_OCODE3`,
			aa.`branch_id`,
			aa.`supplier_id`, 
			SUM(aa.`hd_subtqty`) QUANTITY, 
			SUM(aa.`hd_subtamt`) SRP, 
			SUM(aa.`hd_subtcost`) COST, 
			SUM(aa.`hd_subtamt` - aa.`hd_subtcost`) MARK_UP
			FROM ({$this->db_erp}.`trx_manrecs_hd` aa 
			LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc 
			ON (cc.`recid` = aa.`branch_id`)) 
			WHERE YEAR(aa.`rcv_date`) = '$fld_years' AND  MONTH(aa.`rcv_date`) = '$fld_months' {$str_supp_aa} AND !(aa.`flag`='C') AND (aa.`hd_sm_tags`='D') AND !(aa.`df_tag`='D') AND !(aa.`post_tag`='N') 
			GROUP BY aa.`branch_id`) xx 
		LEFT JOIN (
		SELECT 
		`branch_id`,
		`supplier_id`,
		SUM(`hd_subtcost`) __hd_subtcost 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` 
		WHERE YEAR(`po_date`) = '$fld_years' AND  MONTH(`po_date`) = '$fld_months' AND !(`flag`='C') {$str_supp} AND !(`df_tag`='D') AND !(`post_tag`='N') AND (`po_rsons_id`='5')
		GROUP BY `branch_id` {$grp_supp}
		) yy ON ( xx.`branch_id` = yy.`branch_id` AND xx.`supplier_id` = yy.`supplier_id`)
		)";
		
		/*$str = "SELECT 
		bb.`BRNCH_NAME` BRANCHES,
		aa.`branch_id`,
		SUM(aa.`hd_subtqty`) QUANTITY,
		SUM(aa.`hd_subtamt`) SRP,
		SUM(aa.`hd_subtcost`) COST,
		(SUM(aa.`hd_subtamt`) - SUM(aa.`hd_subtcost`)) MARK_UP,
		IFNULL(SUM(cc.`hd_subtcost`),0) PRC,
		IFNULL(SUM(aa.`hd_subtcost`)  - SUM(cc.`hd_subtcost`) ,0) PAYABLES 
		FROM {$this->db_erp}.`trx_manrecs_hd` aa 
		LEFT JOIN {$this->db_erp}.`mst_companyBranch` bb
		ON (bb.`recid` = aa.`branch_id`)
		LEFT JOIN {$this->db_erp}.`trx_manrecs_po_hd` cc
		ON (cc.`branch_id` = aa.`branch_id`)
		WHERE sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '$fld_dlsupp_id' 
		AND !(aa.`flag`='C')
		GROUP BY aa.`branch_id`
		";*/
		//var_dump($str);
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->num_rows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn = _XMYAPP_PATH_; 
			$mpathdest = $mpathdn . '/downloads'; 
			$cdate = date('Ymd');
			$cfiletmp = 'rcvng_inv_rpt' . '_' . $this->mylibz->random_string(9) . '.xls' ;
			$cfiledest = $mpathdest . '/' . $cfiletmp;
			$cfilelnk = site_url() . '/downloads/' . $cfiletmp;
			//SEND TO UALAM
			$this->mylibz->user_logs_activity_module($this->db_erp,'RCVINVRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			//SECUREW FILES
			if(file_exists($cfiledest)) {
			unlink($cfiledest);
			}
			$fh = fopen($cfiledest, 'w');
			fwrite($fh, $chtml);
			fclose($fh); 
			chmod($cfiledest, 0755);
			$ntqty=0;
			$ntsrp=0;
			$ntmu=0;
			$ntcost=0;
			$ntprc=0;
			$ntpay=0;
			$qrw = $q->result_array();
				foreach($qrw as $rw):
					$chtml = "	<tr class=\"data-nm\">
						       	<td>".$res."</td>
						       	<td>".$rw['BRANCHES']."</td>
						       	<td>".$rw['BRNCH_OCODE3']."</td>
						        <td>".number_format($rw['QUANTITY'],2,'.',',')."</td>
								<td>".number_format($rw['SRP'],2,'.',',') ."</td>
								<td>".number_format($rw['MARK_UP'],2,'.',',')."</td>
								<td>".number_format($rw['COST'],2,'.',',')."</td>
						       	<td>". number_format($rw['PRC'],2,'.',',')."</td>
						       	<td>". number_format($rw['PAYABLES'],2,'.',',')."</td>
						       	</tr>
						   ";
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
				$ntqty=$ntqty + $rw['QUANTITY'];
				$ntsrp=$ntsrp + $rw['SRP'];
				$ntmu= $ntmu + $rw['MARK_UP'];
				$ntcost= $ntcost + $rw['COST'];
				$ntprc=$ntprc + $rw['PRC'];
				$ntpay=$ntpay + $rw['PAYABLES'];
				$res++;
				endforeach;

				
			
		}//end if
		else{
			echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				No Data Found!!!
				</div>				
			";
			die();
		}
		
		$q->free_result();
		$cmsgt = "	<tr class=\"noborder\" style=\"font-weight:bold;\">
						       	<th colspan=\"3\">Grand Total</th>
						        <th>".number_format($ntqty,2,'.',',')."</th>
								<th>".number_format($ntsrp,2,'.',',') ."</th>
								<th>".number_format($ntmu,2,'.',',') ."</th>
								<th>".number_format($ntcost,2,'.',',')  ."</th>
						       	<th>". number_format($ntprc,2,'.',',') ."</th>
						       	<th>". number_format($ntpay,2,'.',',') ."</th>
						       	</tr>
						   
						   ";
		file_put_contents( $cfiledest , $cmsgt , FILE_APPEND | LOCK_EX );
	////////////////////////////////////////////////////////////////////////SUMMARY SKU BRANCH/////////////////////////////////////////////////////////////////////////////////
        $chtml4 = "
					<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
						<head>
						<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
						</head>
						<body>
                	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable4\">
					   
					     <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"10\">Inventory counter of the month per sku per branch</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"10\">".$monthName." ".$fld_years."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"10\">SUPPLIER: ".$fld_dlsupp."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"10\">&nbsp;</th>
					      </tr>
					      <tr class =\"header-theme-purple text-white\">
					        <th class=\"noborder\">No</th>
					        <th class=\"noborder\">BRANCHES</th>
					        <th class=\"noborder\">UNIQUE IDENTIER</th>
					        <th class=\"noborder\">ITEMCODE</th>
					        <th class=\"noborder\">QUANTITY</th>
					        <th class=\"noborder\">SRP</th>
					        <th class=\"noborder\">MARK UP</th>
					        <th class=\"noborder\">COST</th>
					        <th class=\"noborder\">PURCHASE RETURN COST</th>
					        <th class=\"noborder\">PAYABLES</th>
					        </tr>
					    ";
		
		//QUERY

		$str="
		SELECT 
		xx.`BRANCHES`,
		xx.`BRNCH_OCODE3`,
		xx.`QUANTITY`,
		zz.`ART_CODE`,
		xx.`SRP`,
		xx.`COST`,
		xx.`MARK_UP`,
		IFNULL(yy.`__hd_subtcost`,0) PRC,
		(xx.`COST` - IFNULL(yy.`__hd_subtcost`,0)) PAYABLES  
		FROM 
			((SELECT cc.`BRNCH_NAME` BRANCHES,
			cc.`BRNCH_OCODE3`,
			aa.`branch_id`,
			aa.`supplier_id`, 
			 dd.`mat_rid`,
			      SUM(dd.`qty_corrected`) QUANTITY,
			      SUM(dd.`uprice` * dd.`qty_corrected`) SRP,
			      SUM(dd.`ucost` * dd.`qty_corrected`) COST,
			      SUM((dd.`uprice` * dd.`qty_corrected`) - (dd.`ucost` * dd.`qty_corrected`)) MARK_UP
			FROM ({$this->db_erp}.`trx_manrecs_hd` aa 
			JOIN {$this->db_erp}.`trx_manrecs_dt` dd 
			ON (aa.`recid` = dd.`mrhd_rid`)
			JOIN {$this->db_erp}.`mst_companyBranch` cc 
			ON (cc.`recid` = aa.`branch_id`)) 
			WHERE YEAR(aa.`rcv_date`) = '$fld_years' AND  MONTH(aa.`rcv_date`) = '$fld_months' {$str_supp_aa} AND !(aa.`flag`='C') AND (aa.`hd_sm_tags`='D') AND !(aa.`df_tag`='D') AND !(aa.`post_tag`='N') 
			GROUP BY aa.`branch_id`,dd.`mat_rid`) xx 
		LEFT JOIN (
		SELECT 
		ff.`branch_id`,
		ff.`supplier_id`,
		ee.`mat_rid`,
		 SUM(ee.`ucost` * ee.`qty_corrected`) __hd_subtcost 
		FROM {$this->db_erp}.`trx_manrecs_po_hd`  ff
		JOIN {$this->db_erp}.`trx_manrecs_po_dt` ee 
		ON (ff.`recid` = ee.`mrhd_rid`)
		WHERE YEAR(ff.`po_date`) = '$fld_years' AND  MONTH(ff.`po_date`) = '$fld_months' AND !(ff.`flag`='C') {$str_supp} AND !(ff.`df_tag`='D') AND !(ff.`post_tag`='N') AND (ff.`po_rsons_id`='5')
		GROUP BY  ff.`branch_id`,ee.`mat_rid` {$grp_supp}
		) yy ON ( xx.`branch_id` = yy.`branch_id` AND xx.`supplier_id` = yy.`supplier_id` AND xx.`mat_rid` = yy.`mat_rid`)
		JOIN  {$this->db_erp}.`mst_article` zz
		ON (zz.`recid` = xx.`mat_rid`)
		)";
		
		//var_dump($str);
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->num_rows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn4 = _XMYAPP_PATH_; 
			$mpathdest4 = $mpathdn4 . '/downloads'; 
			$cdate4 = date('Ymd');
			$cfiletmp4 = 'rcvng_inv_rpt' . '_' . $this->mylibz->random_string(9) . '.xls' ;
			$cfiledest4 = $mpathdest4 . '/' . $cfiletmp4;
			$cfilelnk4 = site_url() . '/downloads/' . $cfiletmp4;
			//SEND TO UALAM
			$this->mylibz->user_logs_activity_module($this->db_erp,'RCVINVRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp4,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			//SECUREW FILES
			if(file_exists($cfiledest4)) {
			unlink($cfiledest4);
			}
			$fh4 = fopen($cfiledest4, 'w');
			fwrite($fh4, $chtml4);
			fclose($fh4); 
			chmod($cfiledest4, 0755);
			$ntqty=0;
			$ntsrp=0;
			$ntmu=0;
			$ntcost=0;
			$ntprc=0;
			$ntpay=0;
			$qrw = $q->result_array();
				foreach($qrw as $rw):
					$chtml4 = "	<tr class=\"data-nm\">
						       	<td>".$res."</td>
						       	<td>".$rw['BRANCHES']."</td>
						       	<td>".$rw['BRNCH_OCODE3']."</td>
						       	<td>".$rw['ART_CODE']."</td>
						        <td>".number_format($rw['QUANTITY'],2,'.',',')."</td>
								<td>".number_format($rw['SRP'],2,'.',',') ."</td>
								<td>".number_format($rw['MARK_UP'],2,'.',',')."</td>
								<td>".number_format($rw['COST'],2,'.',',')."</td>
						       	<td>". number_format($rw['PRC'],2,'.',',')."</td>
						       	<td>". number_format($rw['PAYABLES'],2,'.',',')."</td>
						       	</tr>
						   ";
				file_put_contents ( $cfiledest4 , $chtml4 , FILE_APPEND | LOCK_EX ); 
				$ntqty=$ntqty + $rw['QUANTITY'];
				$ntsrp=$ntsrp + $rw['SRP'];
				$ntmu= $ntmu + $rw['MARK_UP'];
				$ntcost= $ntcost + $rw['COST'];
				$ntprc=$ntprc + $rw['PRC'];
				$ntpay=$ntpay + $rw['PAYABLES'];
				$res++;
				endforeach;

				
			
		}//end if
		else{
			echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				No Data Found!!!
				</div>				
			";
			die();
		}
		
		$q->free_result();
		$cmsgt4 = "	<tr class=\"noborder\" style=\"font-weight:bold;\">
						       	<th colspan=\"4\">Grand Total</th>
						        <th>".number_format($ntqty,2,'.',',')."</th>
								<th>".number_format($ntsrp,2,'.',',') ."</th>
								<th>".number_format($ntmu,2,'.',',') ."</th>
								<th>".number_format($ntcost,2,'.',',')  ."</th>
						       	<th>". number_format($ntprc,2,'.',',') ."</th>
						       	<th>". number_format($ntpay,2,'.',',') ."</th>
						       	</tr>
						   
						   ";
		file_put_contents( $cfiledest4 , $cmsgt4 , FILE_APPEND | LOCK_EX );
	////////////////////////////////////////////////////////////////////////SUMMARY PER COMPANY/////////////////////////////////////////////////////////////////////////////////
	        $chtml3 = "
						<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
							<head>
							<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
							</head>
							<body>
	                	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable3\">
						   
						      <tr class=\"header-tr-addr\">
						        <th class=\"noborder\" colspan=\"8\">Inventory counter of the month per company</th>
						      </tr>
						      <tr class=\"header-tr-addr\">
						        <th class=\"noborder\" colspan=\"8\">".$monthName." ".$fld_years."</th>
						      </tr>
						      <tr class=\"header-tr-addr\">
						        <th class=\"noborder\" colspan=\"8\">SUPPLIER: ".$fld_dlsupp."</th>
						      </tr>
						      <tr class=\"header-tr-addr\">
						        <th class=\"noborder\" colspan=\"8\">&nbsp;</th>
						      </tr>
						      <tr class =\"header-theme-purple text-white\">
						        <th class=\"noborder\">No</th>
						        <th class=\"noborder\">COMPANY</th>
						        <th class=\"noborder\">QUANTITY</th>
						        <th class=\"noborder\">SRP</th>
						        <th class=\"noborder\">MARK UP</th>
						        <th class=\"noborder\">COST</th>
						        <th class=\"noborder\">PURCHASE RETURN COST</th>
						        <th class=\"noborder\">PAYABLES</th>
						        </tr>
						    ";
			
			//QUERY

			$str="
			SELECT 
			xx.`COMP_NAME`,
			xx.`QUANTITY`,
			xx.`SRP`,
			xx.`COST`,
			xx.`MARK_UP`,
			IFNULL(yy.`__hd_subtcost`,0) PRC,
			(xx.`COST` - IFNULL(yy.`__hd_subtcost`,0)) PAYABLES  
			FROM 
				((SELECT cc.`COMP_NAME` COMP_NAME,
				aa.`branch_id`,
				aa.`comp_id`,
				aa.`supplier_id`, 
				SUM(aa.`hd_subtqty`) QUANTITY, 
				SUM(aa.`hd_subtamt`) SRP, 
				SUM(aa.`hd_subtcost`) COST, 
				SUM(aa.`hd_subtamt` - aa.`hd_subtcost`) MARK_UP
				FROM ({$this->db_erp}.`trx_manrecs_hd` aa 
				LEFT JOIN {$this->db_erp}.`mst_company` cc 
				ON (cc.`recid` = aa.`comp_id`)) 
				WHERE YEAR(aa.`rcv_date`) = '$fld_years' AND  MONTH(aa.`rcv_date`) = '$fld_months' {$str_supp_aa} AND !(aa.`flag`='C') AND (aa.`hd_sm_tags`='D') AND !(aa.`df_tag`='D') AND !(aa.`post_tag`='N') 
				GROUP BY aa.`comp_id`) xx 
			LEFT JOIN (
			SELECT 
			`comp_id`,
			`supplier_id`,
			SUM(`hd_subtcost`) __hd_subtcost 
			FROM {$this->db_erp}.`trx_manrecs_po_hd` 
			WHERE YEAR(`po_date`) = '$fld_years' AND  MONTH(`po_date`) = '$fld_months' AND !(`flag`='C') {$str_supp} AND !(`df_tag`='D') AND !(`post_tag`='N') AND (`po_rsons_id`='5')
			GROUP BY `comp_id` {$grp_supp}
			) yy ON ( xx.`comp_id` = yy.`comp_id` AND xx.`supplier_id` = yy.`supplier_id`))";
			
			
			//var_dump($str);
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($str);
			$res_c=1;
			if($q->num_rows() > 0) { 
				//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
				$mpathdn3 = _XMYAPP_PATH_; 
				$mpathdest3 = $mpathdn3 . '/downloads'; 
				$cdate3 = date('Ymd');
				$cfiletmp3 = 'rcvng_inv_rpt' . '_' . $this->mylibz->random_string(9) . '.xls' ;
				$cfiledest3 = $mpathdest3 . '/' . $cfiletmp3;
				$cfilelnk3 = site_url() . '/downloads/' . $cfiletmp3;
				//SEND TO UALAM
				$this->mylibz->user_logs_activity_module($this->db_erp,'RCVINVRPTCOMP_DOWNLOAD','',$cuser."_FN_".$cfiletmp3,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				//SECUREW FILES
				if(file_exists($cfiledest3)) {
				unlink($cfiledest3);
				}
				$fh3 = fopen($cfiledest3, 'w');
				fwrite($fh3, $chtml3);
				fclose($fh3); 
				chmod($cfiledest3, 0755);
				$ntqty3=0;
				$ntsrp3=0;
				$ntmu3=0;
				$ntcost3=0;
				$ntprc3=0;
				$ntpay3=0;
				$qrw = $q->result_array();
					foreach($qrw as $rw):
						$chtml3 = "	<tr class=\"data-nm\">
							       	<td>".$res_c."</td>
							       	<td>".$rw['COMP_NAME']."</td>
							        <td>".number_format($rw['QUANTITY'],2,'.',',')."</td>
									<td>".number_format($rw['SRP'],2,'.',',') ."</td>
									<td>".number_format($rw['MARK_UP'],2,'.',',')."</td>
									<td>".number_format($rw['COST'],2,'.',',')."</td>
							       	<td>". number_format($rw['PRC'],2,'.',',')."</td>
							       	<td>". number_format($rw['PAYABLES'],2,'.',',')."</td>
							       	</tr>
							   ";
					file_put_contents ( $cfiledest3 , $chtml3 , FILE_APPEND | LOCK_EX ); 
					$ntqty3=$ntqty3 + $rw['QUANTITY'];
					$ntsrp3=$ntsrp3 + $rw['SRP'];
					$ntmu3= $ntmu3 + $rw['MARK_UP'];
					$ntcost3= $ntcost3 + $rw['COST'];
					$ntprc3=$ntprc3 + $rw['PRC'];
					$ntpay3=$ntpay3 + $rw['PAYABLES'];
					$res_c++;
					endforeach;

					
				
			}//end if
			else{
				echo "
					<div class=\"alert alert-danger\" role=\"alert\">
					No Data Found!!!
					</div>				
				";
				die();
			}
			
			$q->free_result();
			$cmsgt3 = "	<tr class=\"noborder\" style=\"font-weight:bold;\">
							       	<th colspan=\"2\">Grand Total</th>
							        <th>".number_format($ntqty3,2,'.',',')."</th>
									<th>".number_format($ntsrp3,2,'.',',') ."</th>
									<th>".number_format($ntmu3,2,'.',',') ."</th>
									<th>".number_format($ntcost3,2,'.',',')  ."</th>
							       	<th>". number_format($ntprc3,2,'.',',') ."</th>
							       	<th>". number_format($ntpay3,2,'.',',') ."</th>
							       	</tr>
							   
							   ";
			file_put_contents( $cfiledest3 , $cmsgt3 , FILE_APPEND | LOCK_EX );

/////////////////////////////////////////////////////////////////////////STATEMENT///////////////////////////////////////////////////////////////////
		
		$chtml2 = "
					<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
						<head>
						<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
						</head>
						<body>
                	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable2\">
					   
					      <tr class=\"header-tr\">
					        <th class=\"noborder\" colspan=\"4\">NOVOHOLDINGS INCORPORATED</th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Date:</td>
					        <td align=\"left\" class=\"noborder\" colspan=\"1\">".$date."</td>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"4\">Inventory counter of the month</th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Statement #</td>
					        <td align=\"left\" class=\"noborder\" colspan=\"1\"></td>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"4\">".$monthName." ".$fld_years."</th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Supplier ID: </td>
					        <td align=\"left\" class=\"noborder\" colspan=\"1\"></td>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"4\"></th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Page 1 of 1	</td>
					        <th align=\"left\" class=\"noborder\" colspan=\"1\"></th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"7\">&nbsp;</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th align=\"left\" class=\"noborder\" colspan=\"3\">Supplier</th>
					        <th class=\"noborder\" colspan=\"1\"></th>
					        <th align=\"left\" class=\"noborder\" colspan=\"3\">Account Summary</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <td align=\"left\" class=\"noborder\" colspan=\"3\">".$fld_dlsupp."</td>
					        <th class=\"noborder\" colspan=\"1\"></th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Previous Balance</td>
					        <th align=\"left\" class=\"noborder\" colspan=\"1\"></th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <td align=\"left\" class=\"noborder\" colspan=\"3\">".$fld_ADDR1."</td>
					        <th class=\"noborder\" colspan=\"1\"></th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Credits</td>
					        <th align=\"left\" class=\"noborder\" colspan=\"1\"></th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <td align=\"left\" class=\"noborder\" colspan=\"3\">".$fld_ADDR2."</td>
					        <th class=\"noborder\" colspan=\"1\"></th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">New Charges </td>
					        <th align=\"left\" class=\"noborder\" colspan=\"1\"></th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <td align=\"left\" class=\"noborder\" colspan=\"3\">".$fld_TELNO."</td>
					        <th class=\"noborder\" colspan=\"1\"></th>
					        <th align=\"left\" class=\"noborder\" colspan=\"2\">Total Balance Due</th>
					        <th align=\"left\" class=\"noborder\" colspan=\"1\"></th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <td align=\"left\" class=\"noborder\" colspan=\"3\"></td>
					        <th class=\"noborder\" colspan=\"1\"></th>
					        <td align=\"left\" class=\"noborder\" colspan=\"2\">Payment Due Date</td>
					        <th align=\"left\" class=\"noborder\" colspan=\"1\"></th>
					      </tr>


					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"7\">&nbsp;</th>
					      </tr>
					      <tr class =\"header-theme-purple text-white\">
					        <th class=\"noborder\">No</th>
					        <th class=\"noborder\">Date</th>
					        <th class=\"noborder\">DR #</th>
					        <th class=\"noborder\">Description</th>
					        <th class=\"noborder\">Cost</th>
					        <th class=\"noborder\">Purchase Return Cost</th>
					        <th class=\"noborder\">Net Payables</th>
					        </tr>
					    ";
		
		//QUERY

		$str="
		SELECT 
		  xxx.`_t1`,
  		  xxx.`_t2`,
		  xxx.`SUPPLIER`,
		  xxx.`BRANCHES`,
		  xxx.`QUANTITY`,
		  xxx.`SRP`,
		  xxx.`COST`,
		  xxx.`MARK_UP`,
		  xxx.`PRC`,
		  xxx.`PAYABLES`
		FROM(
			SELECT
			CONCAT('Purchase for the month of ','$monthName ','$fld_years') _t1,
	  		CONCAT('Purchase Return for the month of ','$monthName ','$fld_years') _t2,
			xx.`SUPPLIER`,
			xx.`BRANCHES`,
			xx.`QUANTITY`,
			xx.`SRP`,
			xx.`COST`,
			xx.`MARK_UP`,
			IFNULL(yy.`__hd_subtcost`,0) PRC,
			(xx.`COST` - IFNULL(yy.`__hd_subtcost`,0)) PAYABLES  
			FROM 
				((SELECT cc.`BRNCH_NAME` BRANCHES,
				dd.`VEND_NAME` SUPPLIER,
				aa.`branch_id`,
				aa.`supplier_id`, 
				SUM(aa.`hd_subtqty`) QUANTITY, 
				SUM(aa.`hd_subtamt`) SRP, 
				SUM(aa.`hd_subtcost`) COST, 
				SUM(aa.`hd_subtamt` - aa.`hd_subtcost`) MARK_UP
				FROM ({$this->db_erp}.`trx_manrecs_hd` aa
				LEFT JOIN {$this->db_erp}.`mst_vendor` dd ON (dd.`recid` = aa.`supplier_id`)
				LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc 
				ON (cc.`recid` = aa.`branch_id`)) 
				WHERE YEAR(aa.`rcv_date`) = '$fld_years' AND  MONTH(aa.`rcv_date`) = '$fld_months' {$str_supp_aa} AND !(aa.`flag`='C') AND (aa.`hd_sm_tags`='D') AND !(aa.`df_tag`='D') AND !(aa.`post_tag`='N') 
				GROUP BY aa.`supplier_id`) xx 
			LEFT JOIN (
				SELECT 
				`branch_id`,
				`supplier_id`,
				SUM(`hd_subtcost`) __hd_subtcost 
				FROM {$this->db_erp}.`trx_manrecs_po_hd` 
				WHERE YEAR(`encd_date`) = '$fld_years' AND  MONTH(`encd_date`) = '$fld_months' AND !(`flag`='C') {$str_supp} AND !(`df_tag`='D') AND !(`post_tag`='N') AND (`po_rsons_id`='5') 
				GROUP BY `branch_id` {$grp_supp}
				) yy ON ( xx.`branch_id` = yy.`branch_id` AND xx.`supplier_id` = yy.`supplier_id`)
			)

			UNION ALL

			SELECT
			'Prior Period Purchases not yet countered' _t1,
	  		'Prior Period Purchase Returns not yet countered' _t2,
			xx.`SUPPLIER`,
			xx.`BRANCHES`,
			xx.`QUANTITY`,
			xx.`SRP`,
			xx.`COST`,
			xx.`MARK_UP`,
			IFNULL(yy.`__hd_subtcost`,0) PRC,
			(xx.`COST` - IFNULL(yy.`__hd_subtcost`,0)) PAYABLES  
			FROM 
				((SELECT cc.`BRNCH_NAME` BRANCHES,
				dd.`VEND_NAME` SUPPLIER,
				aa.`branch_id`,
				aa.`supplier_id`, 
				SUM(aa.`hd_subtqty`) QUANTITY, 
				SUM(aa.`hd_subtamt`) SRP, 
				SUM(aa.`hd_subtcost`) COST, 
				SUM(aa.`hd_subtamt` - aa.`hd_subtcost`) MARK_UP
				FROM ({$this->db_erp}.`trx_manrecs_hd` aa
				LEFT JOIN {$this->db_erp}.`mst_vendor` dd ON (dd.`recid` = aa.`supplier_id`)
				LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc 
				ON (cc.`recid` = aa.`branch_id`)) 
				WHERE YEAR(aa.`rcv_date`) = '$fld_years' AND  MONTH(aa.`rcv_date`) = '$fld_months' {$str_supp_aa} AND !(aa.`flag`='C') AND (aa.`hd_sm_tags`='D') AND !(aa.`df_tag`='D') AND !(aa.`post_tag`='N') AND  (aa.`counter_tag` = 'N') 
				GROUP BY aa.`supplier_id`) xx 
			LEFT JOIN (
				SELECT 
				`branch_id`,
				`supplier_id`,
				SUM(`hd_subtcost`) __hd_subtcost 
				FROM {$this->db_erp}.`trx_manrecs_po_hd` 
				WHERE YEAR(`encd_date`) = '$fld_years' AND  MONTH(`encd_date`) = '$fld_months' AND !(`flag`='C') {$str_supp} AND !(`df_tag`='D') AND !(`post_tag`='N') AND (`po_rsons_id`='5')
				GROUP BY `branch_id` {$grp_supp}
				) yy ON ( xx.`branch_id` = yy.`branch_id` AND xx.`supplier_id` = yy.`supplier_id`)
			)
		)xxx
		GROUP BY
		xxx.`_t1` DESC
		";
		
		
		//var_dump($str);
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res_s=1;
		$inv_camt=0;
		$inv_prc =0;
		if($q->num_rows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn2 = _XMYAPP_PATH_; 
			$mpathdest2 = $mpathdn2 . '/downloads'; 
			$cdate2 = date('Ymd');
			$cfiletmp2 = 'rcvng_inv_rpt_st' . '_' . $this->mylibz->random_string(9) . '.xls' ;
			$cfiledest2 = $mpathdest2 . '/' . $cfiletmp2;
			$cfilelnk2 = site_url() . '/downloads/' . $cfiletmp2;
			//SEND TO UALAM
			$this->mylibz->user_logs_activity_module($this->db_erp,'RCVINVRPTSTATE_DOWNLOAD','',$cuser."_FN_".$cfiletmp2,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			//SECUREW FILES
			if(file_exists($cfiledest2)) {
			unlink($cfiledest2);
			}
			$fh2 = fopen($cfiledest2, 'w');
			fwrite($fh2, $chtml2);
			fclose($fh2); 
			chmod($cfiledest2, 0755);
			$qrw = $q->result_array();
				foreach($qrw as $rw):
					$chtml2 = "	<tr class=\"data-nm\">
						       	<td>".$res_s."</td>
						       	<td></td>
						        <td></td>
								<td>".$rw['_t1']."</td>
								<td>".number_format($rw['COST'],2,'.',',')."</td>
								<td></td>
						       	<td>". number_format($rw['COST'],2,'.',',')."</td>
						       	</tr>
						       	<tr class=\"data-nm\">
						       	<td></td>
						       	<td></td>
						        <td></td>
								<td>".$rw['_t2']."</td>
								<td></td>
								<td>".number_format($rw['PRC'],2,'.',',')."</td>
						       	<td>". number_format($rw['PRC'],2,'.',',')."</td>
						       	</tr>
						       	<tr></tr>
						       	</tr>
						       	
						   ";
				file_put_contents ( $cfiledest2 , $chtml2 , FILE_APPEND | LOCK_EX ); 
				$inv_camt = $inv_camt + ($rw['COST'] - $rw['PRC']);
				
				$inv_prc = $inv_prc + $rw['PRC'];
				$res_s++;
				endforeach;

				$chtml2 = "
					<tr class=\"data-nm\">
			       	<td></td>
			       	<td></td>
			        <td></td>
					<td>Please see attachement</td>
					<td></td>
					<td>".number_format($inv_prc, 2, '.', ',')."</td>
			       	<td>".number_format($inv_prc, 2, '.', ',')."</td>
			       	</tr>

					<tr></tr>	
					<tr class=\"data-nm\">
					<td align=\"right\" colspan=\"6\" ><strong>Inventory Counter Amount: </strong></td>
					<td><strong>". number_format($inv_camt,2,'.',',')."</strong></td>
					</tr>
					";
				file_put_contents ( $cfiledest2 , $chtml2 , FILE_APPEND | LOCK_EX ); 

				$chtml2 = "	
					<tr></tr>
					<tr class=\"header-tr\" align=\"center\">
					    <td class=\"noborder\" colspan=\"7\">Please provide signature over printed name on the space provided as acknowledgment of the amount of the Inventory countered</td>
					</tr>
					<tr></tr>
					<tr class=\"header-tr\" align=\"center\">
					    <td height=\"25\ class=\"noborder\" colspan=\"7\"><strong>Thank you for your business!</strong></td>
					</tr>
					<tr></tr>
					<tr class=\"header-tr\" align=\"center\">
					    <td class=\"noborder\" colspan=\"7\">Should you have any inquiries concerning this statement, please contact <strong> Margie V.Oliver on (02) 352-2387 loc.136</strong></td>
					</tr> 
					<tr></tr>
					<tr class=\"header-tr\" align=\"center\">
					    <td class=\"noborder\" colspan=\"7\">Tel: (02) 352-2387 local136  E-mail: moliver@novoholdings.com.ph</td>
					</tr>
					";
				file_put_contents ( $cfiledest2 , $chtml2 , FILE_APPEND | LOCK_EX ); 
				
			
		}//end if
		else{
			echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				No Data Found!!!
				</div>				
			";
			die();
		}
		$chtmljs .= "
				<script type=\"text/javascript\">
					//window.parent.document.getElementById('myscrloading').innerHTML = '';
					jQuery('#lnkexportmsexcel').click(function() { 
						//jQuery('#messproc').css({display:''});
						window.location = '{$cfilelnk}';
						jQuery('#fld_dlsupp').val('');
						jQuery('#fld_dlsupp_id').val('');
						$('#lnkexportmsexcel').css({display:'none'});
					});
					jQuery('#lnkexportmsexcel_c').click(function() { 
						//jQuery('#messproc').css({display:''});
						window.location = '{$cfilelnk3}';
						jQuery('#fld_dlsupp').val('');
						jQuery('#fld_dlsupp_id').val('');
						$('#lnkexportmsexcel_c').css({display:'none'});
					});
					jQuery('#lnkexportmsexcel_s').click(function() { 
						//jQuery('#messproc').css({display:''});
						window.location = '{$cfilelnk2}';
						jQuery('#fld_dlsupp').val('');
						jQuery('#fld_dlsupp_id').val('');
						$('#lnkexportmsexcel_s').css({display:'none'});
					});
					jQuery('#lnkexportmsexcel_sb').click(function() { 
						//jQuery('#messproc').css({display:''});
						window.location = '{$cfilelnk4}';
						jQuery('#fld_dlsupp').val('');
						jQuery('#fld_dlsupp_id').val('');
						$('#lnkexportmsexcel_sb').css({display:'none'});
					});
					jQuery('#lnktoprint').click(function() { 
						jQuery('#__mtoexport').css({display:'none'});
						//jQuery('#__mtoprint').css({display:'none'});
						window.print();			
					});
				</script>
				
				";
		echo $chtmljs;

	}//end func
	public function rcv_posting(){
		$mtkn_trxno = $this->input->get_post('mtkn_trxno');
		$id_post = $this->input->get_post('id_post');
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$trx_no = '';
		//CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		if(!empty($mtkn_trxno)) { 
			if($this->cusergrp != 'SA') { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Note</strong> You don't authorized to edit this data!!!</div>";
				die();
			}
			//SELECT IF ALREADY POSTED
			$str = "select post_tag,trx_no from {$this->db_erp}.`trx_manrecs_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_trxno' AND `post_tag` = 'Y'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() > 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already posted!!!.</div>";
				die();
			}
			else{
				$rw = $q->row_array();
				$trx_no = $rw['trx_no'];
			}
			
		} //END CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		if(!empty($mtkn_trxno)) { 
					$str = "
					update {$this->db_erp}.`trx_manrecs_hd`
					SET `post_tag` = 'Y'
					WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_trxno';
					";

					$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibz->user_logs_activity_module($this->db_erp,'UPD_POSTING','',$mtkn_trxno,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
					$arrfield = '';
					$arrfield .= "POSTING" . "->" . 'Y' . "\n";
					
					$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$trx_no,'U','POSTING_RECEIVING','R');
					echo  "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Recorded Successfully!!!</div>
					
					";
					

				}
			//redirect('mytrx_acct/acct_man_recs_vw');
	}//end func
	public function view_invlogfile_recs($npages = 1,$npagelimit = 30,$msearchrec='',$fld_dlsupp='',$fld_dlsupp_id='',$fld_dlbranch='',$fld_dl_dteto='',$fld_dl_dtefrom=''){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		/*var_dump($fld_dlsupp);
		die();*/

		$__flag="C";
		$str_optn = "";
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		if($this->cusergrp == 'SA'){
			$str_vwrecs = "";
		}
		//IF SEARCH IS NOT EMPTY
		if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = " where (aa.`trx_no` like '%$msearchrec%' or aa.`drno` like '%$msearchrec%' or bb.`COMP_NAME` like '%$msearchrec%' or cc.`BRNCH_NAME` like '%$msearchrec%' or dd.`VEND_NAME` like '%$msearchrec%') AND aa.flag != '$__flag' {$str_vwrecs}";
		}
		if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_vwrecs}";
		} 
		/*
		ee.*,
		ff.ART_CODE,
		ff.ART_DESC,
		aa.`recid` __arid,
		bb.`recid` __brid,
		cc.`recid` __crid,
		dd.`recid` __drid,
		ff.`recid` __frid,
		*/
		$strqry = "
		select
		aa.`recid` __arid,
		aa.`trx_no`,
	  	aa.`comp_id`,
	  	aa.`branch_id`,
	  	aa.`drno`,
	  	aa.`dr_date`,
	  	aa.`supplier_id`,
	  	aa.`rcv_date`,
	  	aa.`date_in`,
	  	aa.`hd_remarks`,
	  	aa.`hd_sm_tags`,
	  	aa.`hd_subtqty`,
	  	aa.`hd_subtcost`,
	  	aa.`hd_subtamt`,
	  	aa.`muser`,
	  	aa.`encd_date`,
	  	aa.`flag`,
	  	aa.`p_flag`,
	  	aa.`df_tag`,
	  	aa.`post_tag`,
        aa.`claim_tag`,
		ee.`recid` __brid,
	  	ee.`mrhd_rid`,
	  	ee.`mat_rid`,
	  	ee.`mat_code`,
	  	ee.`ucost`,
	  	ee.`tcost`,
	  	ee.`uprice`,
	  	ee.`tamt`,
	  	ee.`qty`,
	  	ee.`qty_corrected`,
	  	ee.`OLT_tag`,
	  	ee.`SM_Tag`,
	  	ee.`nremarks`,
	  	bb.COMP_NAME,
		cc.BRNCH_NAME,
		dd.VEND_NAME,
		ff.ART_CODE,
		ff.ART_DESC,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`trx_manrecs_hd` aa
		join {$this->db_erp}.`mst_company` bb
		on (aa.`comp_id` = bb.`recid`)
		join {$this->db_erp}.`mst_companyBranch` cc
		on (aa.`branch_id` = cc.`recid`)
		join {$this->db_erp}.`mst_vendor` dd
		on (aa.`supplier_id` = dd.`recid`)
		join {$this->db_erp}.`trx_manrecs_dt` ee
		on (aa.`recid` = ee.`mrhd_rid`)
		join {$this->db_erp}.`mst_article` ff
		on (ff.`recid`= ee.`mat_rid`)
		{$str_optn} 
		";
		
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->row_array();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->num_rows() > 0) { 
			$data['rlist'] = $qry->result_array();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
	}//end func
	
	//VIEW LOGFILE FILTER
	public function view_invlogfile_recs_fltr($npages = 1,$npagelimit = 30,$fld_dlsupp='',$fld_dlsupp_id='',$fld_dlbranch='',$fld_dl_dteto='',$fld_dl_dtefrom=''){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$str_supp="";
		$str_brnch="";
		$str_optn = "";
		$fld_dlsupp_q = "";
		$fld_dlbranch_q = "";
		$chtmlhd="";
		$chtmljs ="";
		$chtml = "";
		$cmsexp =  "";
		$cmsgt =  "";
		$chtml2 = "";
		$cmsft =  "";
		$date = date("F j, Y, g:i A");
		/*var_dump($fld_dlsupp);
		die();*/
		
		$fld_logitemcode_s = $this->input->get_post('fld_logitemcode_s');
		$str_itemc_b ='';
		if(!empty($fld_logitemcode_s)) { 
			$str = "select `ART_CODE` 
			from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$fld_logitemcode_s'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid itemcode!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$fld_logitemcode_s = $rw['ART_CODE'];
			$q->free_result();
			//$str_itemc = "AND (xx.`ART_CODE` = '$fld_stinqitemcode_s')";
			$str_itemc_b = " AND (ff.`ART_CODE` = '$fld_logitemcode_s')";
			
			
		}
		//VENDOR
		if(!empty($fld_dlsupp_id)){
			$str = "select recid,VEND_NAME 
			 from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$fld_dlsupp'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibz->user_logs_activity_module($this->db_erp,'VENDOR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Supplier Data!!!.</div>";
				die();
			}
			$str_supp="AND sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
		}//END VENDOR

		//BRANCH
		if(!empty($fld_dlbranch)){
			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_dlbranch'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$fld_dlbranch_id = $rw['recid'];
			$q->free_result();
			$str_brnch="AND aa.`branch_id` = '$fld_dlbranch_id'";
			//END BRANCH
		}//endif

		$__flag="C";
		$str_optn = "";
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		/*$str_vwrecs = "AND aa.`muser` = '$cuser'";
		if($this->cusergrp == 'SA'){
			$str_vwrecs = "";
		}*/
		//IF SEARCH IS NOT EMPTY
		if((!empty($fld_dl_dteto) && !empty($fld_dl_dtefrom)) && (($fld_dl_dteto != '--') && ($fld_dl_dtefrom != '--'))){
			//$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = "AND (aa.`rcv_date` >= '{$fld_dl_dtefrom}' AND  aa.`rcv_date` <= '{$fld_dl_dteto}')";
		}
		/*if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_vwrecs}";
		}*/ 
		/*
		ee.*,
		ff.ART_CODE,
		ff.ART_DESC,
		aa.`recid` __arid,
		bb.`recid` __brid,
		cc.`recid` __crid,
		dd.`recid` __drid,
		ff.`recid` __frid,
		*/
		$chtmljs .= "
		<div class=\"col-md-3\" id=\"__mtoexport_rcv\">
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_rcv\"><i class=\"btn btn-success fa fa-download\"> DOWNLOAD</i></a></span>
			</div>
			</br>
        </div>
		";
		/*<th class=\"noborder\">Total Actual Qty</th>
							<th class=\"noborder\">Total Actual Cost</th>
							<th class=\"noborder\">Total Actual SRP</th>
							*/
		/*<td>".number_format($row['hd_subtqty'],2,'.',',')."</td>
											<td>".number_format($row['hd_subtcost'],2,'.',',')."</td>
											<td>".number_format($row['hd_subtamt'],2,'.',',')."</td>
											*/
		////////////////////////////////////////////////////////////////////////PULLOUT LOGFILE REPORTS/////////////////////////////////////////////////////////////////////////////////
	    $chtml = "
					<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
						<head>
						<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
						</head>
						<body>
	            	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_dr\">
					   
					      <tr class=\"header-tr\">
					        <th class=\"noborder\" colspan=\"21\">NOVOHOLDINGS INCORPORATED</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"21\">Receiving Logfile</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"21\">".$fld_dl_dtefrom."- ".$fld_dl_dteto."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"21\">SUPPLIER: ".$fld_dlsupp."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"21\">BRANCH: ".$fld_dlbranch."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"21\">&nbsp;</th>
					      </tr>
					      <tr class =\"header-theme-purple text-white\">
					        <th class=\"noborder\">No</th>
					        <th class=\"noborder\">Transaction No</th>
							<th class=\"noborder\">Company</th>
							<th class=\"noborder\">Area Code</th>
							<th class=\"noborder\">Unique Identifier</th>
							<th class=\"noborder\">Supplier</th>
							<th class=\"noborder\">Item Code</th>
							<th class=\"noborder\">Item Description</th>
							<th class=\"noborder\">Unit Cost</th>
							<th class=\"noborder\">Total Unit Cost</th>
							<th class=\"noborder\">Unit Price</th>
							<th class=\"noborder\">Total Unit Price</th>
							<th class=\"noborder\">Actual Qty</th>
							<th class=\"noborder\">Corrected Qty</th>
							<th class=\"noborder\">DR No</th>
							<th class=\"noborder\">DR Date</th>
							<th class=\"noborder\">RCV Date</th>
							<th class=\"noborder\">Date In</th>
							<th class=\"noborder\">User</th>
							<th class=\"noborder\">S/M/D Tag</th>
                            <th class=\"noborder\">Claim Tag</th>
							<th class=\"noborder\">D/F Tag</th>
							<th class=\"noborder\">Remarks</th>
							</tr>
					    ";
		$strqry = "
		select
		aa.`recid` __arid,
		aa.`trx_no`,
	  	aa.`comp_id`,
	  	aa.`branch_id`,
	  	aa.`drno`,
	  	aa.`dr_date`,
	  	aa.`supplier_id`,
	  	aa.`rcv_date`,
	  	aa.`date_in`,
	  	aa.`hd_remarks`,
	  	aa.`hd_sm_tags`,
	  	aa.`hd_subtqty`,
	  	aa.`hd_subtcost`,
	  	aa.`hd_subtamt`,
	  	aa.`muser`,
	  	aa.`encd_date`,
	  	aa.`flag`,
	  	aa.`p_flag`,
	  	aa.`df_tag`,
	  	aa.`post_tag`,
        aa.`claim_tag`,
		ee.`recid` __brid,
	  	ee.`mrhd_rid`,
	  	ee.`mat_rid`,
	  	ee.`mat_code`,
	  	ee.`ucost`,
	  	ee.`tcost`,
	  	ee.`uprice`,
	  	ee.`tamt`,
	  	ee.`qty`,
	  	ee.`qty_corrected`,
	  	ee.`OLT_tag`,
	  	ee.`SM_Tag`,
	  	ee.`nremarks`,
	  	bb.COMP_NAME,
		cc.BRNCH_NAME,
		cc.BRNCH_OCODE3,
		dd.VEND_NAME,
		ff.ART_CODE,
		ff.ART_DESC,
		sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`trx_manrecs_hd` aa
		join {$this->db_erp}.`mst_company` bb
		on (aa.`comp_id` = bb.`recid`)
		join {$this->db_erp}.`mst_companyBranch` cc
		on (aa.`branch_id` = cc.`recid`)
		join {$this->db_erp}.`mst_vendor` dd
		on (aa.`supplier_id` = dd.`recid`)
		join {$this->db_erp}.`trx_manrecs_dt` ee
		on (aa.`recid` = ee.`mrhd_rid`)
		join {$this->db_erp}.`mst_article` ff
		on (ff.`recid`= ee.`mat_rid`)
		where aa.flag != '$__flag'
		{$str_optn} {$str_supp} {$str_brnch} {$str_itemc_b}
		";
		$q = $this->mylibz->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$res=1;
					if($q->num_rows() > 0) { 
						//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
						$mpathdn = _XMYAPP_PATH_; 
						$mpathdest = $mpathdn . '/downloads'; 
						$cdate = date('Ymd');
						$cfiletmp = 'rcvlogfile_rpt' . '_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
						$cfiledest = $mpathdest . '/' . $cfiletmp;
						$cfilelnk = site_url() . '/downloads/' . $cfiletmp;
						//SEND TO UALAM
						$this->mylibz->user_logs_activity_module($this->db_erp,'RCVLOGFILERPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						
						//SECUREW FILES
						if(file_exists($cfiledest)) {
						unlink($cfiledest);
						}
						$fh = fopen($cfiledest, 'w');
						fwrite($fh, $chtml);
						fclose($fh); 
						chmod($cfiledest, 0755);
						$ntqty=0;
						$ntsrp=0;
						$ntmu=0;
						$ntcost=0;
						$ntprc=0;
						$ntpay=0;
						$qrw = $q->result_array();
							foreach($qrw as $row):
								$chtml = "	<tr class=\"data-nm\">
									       	<td>".$res."</td>
									       	<td>'".$row['trx_no']."</td>
											<td>".$row['COMP_NAME']."</td>
											<td>".$row['BRNCH_NAME']."</td>
											<td>".$row['BRNCH_OCODE3']."</td>
											<td>".$row['VEND_NAME']."</td>
											<td>".$row['ART_CODE']."</td>
											<td>".$row['ART_DESC']."</td>
											<td>".number_format($row['ucost'],2,'.',',')."</td>
											<td>".number_format($row['tcost'],2,'.',',')."</td>
											<td>".number_format($row['uprice'],2,'.',',')."</td>
											<td>".number_format($row['tamt'],2,'.',',')."</td>
											<td>".number_format($row['qty'],2,'.',',')."</td>
											<td>".number_format($row['qty_corrected'],2,'.',',')."</td>
											<td>'".$row['drno']."</td>
											<td>".$this->mylibz->mydate_mmddyyyy($row['dr_date'])."</td>
											<td>".$this->mylibz->mydate_mmddyyyy($row['rcv_date'])."</td>
											<td>".$this->mylibz->mydate_mmddyyyy($row['date_in'])."</td>
											<td>".$row['muser']."</td>
											<td>".$row['hd_sm_tags']."</td>
											<td>".$row['df_tag']."</td>
                                            <td>".$row['claim_tag']."</td>
											<td>".$row['hd_remarks']."</td>
											</tr>
									   ";
							file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
							$res++;
							endforeach;

							
						
					}//end if
					else{
						echo "
							<div class=\"alert alert-danger\" role=\"alert\">
							No Data Found!!!
							</div>				
						";
						die();
					}
			$chtmljs .= "
							<script type=\"text/javascript\">
								//window.parent.document.getElementById('myscrloading').innerHTML = '';
								jQuery('#lnkexportmsexcel_rcv').click(function() { 
									//jQuery('#messproc').css({display:''});
									window.location = '{$cfilelnk}';
									jQuery('#fld_logsupp').val('');
									jQuery('#fld_logsupp_id').val('');
									jQuery('#fld_logbrnch').val('');
									jQuery('#fld_logdftag').val('');
									jQuery('#fld_logrson').val('');
									$('#lnkexportmsexcel_rcv').css({display:'none'});
								});
								
								jQuery('#lnktoprint').click(function() { 
									jQuery('#__mtoexport_rcv').css({display:'none'});
									//jQuery('#__mtoprint').css({display:'none'});
									window.print();			
								});
							</script>
							
							";
		echo $chtmljs;
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->row_array();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa GROUP BY __brid limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->num_rows() > 0) { 
			$data['rlist'] = $qry->result_array();
			$data['fld_dl_dteto'] = $fld_dl_dteto;
			$data['fld_dl_dtefrom'] = $fld_dl_dtefrom;
			$data['fld_dlsupp'] = $fld_dlsupp;
			$data['fld_dlsupp_id'] = $fld_dlsupp_id;
			$data['fld_dlbranch'] = $fld_dlbranch;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_dl_dteto'] = $fld_dl_dteto;
			$data['fld_dl_dtefrom'] = $fld_dl_dtefrom;
			$data['fld_dlsupp'] = $fld_dlsupp;
			$data['fld_dlsupp_id'] = $fld_dlsupp_id;
			$data['fld_dlbranch'] = $fld_dlbranch;
		}
		return $data;
	}//end func
	//DR MONTHLY REPORTS
	public function dr_rpt_download_proc(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cuser_fullname = $this->mylibz->mysys_user_fullname();
		$fld_drsomhd =$this->input->get_post('fld_drsomhd');
		$fld_drsupp =$this->input->get_post('fld_drsupp');
		$fld_drsupp_id =$this->input->get_post('fld_drsupp_id');
		$fld_drbrnch =$this->input->get_post('fld_drbrnch');
		$fld_drbrnch_id =$this->input->get_post('fld_drbrnch_id');
		$fld_months =$this->input->get_post('fld_months');
		$fld_years =$this->input->get_post('fld_years');
		$str_supp='';
		$str_brnch='';
		$str_optn ='';
		$str_optn_po ='';
		$monthName ='';
		$fld_area_code ='';
		$chtmlhd="";
		$chtmljs ="";
		$chtml = "";
		$cmsexp =  "";
		$cmsgt =  "";
		$chtml2 = "";
		$cmsft =  "";
		$date = date("F j, Y, g:i A");
		//TAGS
		$str_dlsomhd ='';
		if(!empty($fld_drsomhd)) {
		 	$str_dlsomhd ="AND (aa.`hd_sm_tags` = '$fld_drsomhd')";
		 	//var_dump($fld_drsomhd);
		}
		//VALIDATING DATA SUPPLIER
		if(!empty($fld_drsupp)) {
		$str = "select recid,
			VEND_NAME,
			VEND_ADDR1 ADDR1,
			VEND_ADDR2 ADDR2,
			VEND_TELNO TELNO from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$fld_drsupp' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_drsupp_id'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				Invalid Supplier Data	
				";
				die();
			}//end if
			$rw = $q->row_array();
			$fld_ADDR1 = $rw['ADDR1'];
			$fld_ADDR2 = $rw['ADDR2'];
			$fld_TELNO = $rw['TELNO'];
			$q->free_result();
			$str_supp= "AND sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '{$fld_drsupp_id}'";
		}//end if
		//BRANCH
		if(!empty($fld_drbrnch)) {
			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_drbrnch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_drbrnch_id'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid branch Data!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$fld_area_code = $rw['BRNCH_NAME'];
			$q->free_result();
			$str_brnch="AND sha2(concat(aa.`branch_id`,'{$mpw_tkn}'),384)= '{$fld_drbrnch_id}'";
			//END BRANCH
		}//end if
		//IF SEARCH IS NOT EMPTY
		if(!empty($fld_years) && !empty($fld_months)){
			//CONVERTING MONTH to name
			$dateObj   = DateTime::createFromFormat('!m', $fld_months);
			$monthName = $dateObj->format('F');
			//$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = "AND YEAR(aa.`rcv_date`) = '{$fld_years}' AND MONTH(aa.`rcv_date`) = '{$fld_months}'";
			$str_optn_po = "AND YEAR(aa.`po_date`) = '{$fld_years}' AND MONTH(aa.`po_date`) = '{$fld_months}'";
		}
		$chtmljs .= "
		<div class=\"col-md-6\" id=\"__mtoexport_drtd\">
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_dr\"><i class=\"btn btn-success fa fa-download\"> DR</i></a></span>
			</div>
			</br>
        </div>	
		";
	////////////////////////////////////////////////////////////////////////DR REPORTS/////////////////////////////////////////////////////////////////////////////////
        $chtml = "
					<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
						<head>
						<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
						</head>
						<body>
                	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_dr\">
					   
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">DR Breakdown of the month</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">".$monthName." ".$fld_years."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">SUPPLIER: ".$fld_drsupp."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">BRANCH: ".$fld_area_code."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"9\">&nbsp;</th>
					      </tr>
					      <tr class =\"header-theme-purple text-white\">
					        <th class=\"noborder\">No</th>
					        <th class=\"noborder\">DR NO</th>
					        <th class=\"noborder\">BRANCH</th>
					        <th class=\"noborder\">UNIQUE IDENTIER</th>
					        <th class=\"noborder\">DR DATE</th>
					        <th class=\"noborder\">RCV DATE</th>
					        <th class=\"noborder\">QUANTITY</th>
					        <th class=\"noborder\">AMOUNT</th>
					        <th class=\"noborder\">COST</th>
					        </tr>
					    ";
		
		//QUERY
		// $str="
		// SELECT 
		//   aa.`recid`,
		//   aa.`trx_no`,
		//   aa.`comp_id`,
		//   aa.`branch_id`,
		//   aa.`drno`,
		//   aa.`dr_date`,
		//   aa.`supplier_id`,
		//   aa.`rcv_date`,
		//   aa.`date_in`,
		//   aa.`hd_remarks`,
		//   aa.`hd_sm_tags`,
		//   SUM(xx.`qty_corrected`) QUANTITY,
		//   SUM(xx.`tcost`) COST,
		//   SUM(xx.`tamt`) AMOUNT,
		//   aa.`muser`,
		//   aa.`encd_date`,
		//   aa.`flag`,
		//   aa.`p_flag`,
		//   aa.`df_tag`,
		//   aa.`post_tag`,
		// bb.`VEND_NAME` SUPPLIER,
		// cc.`BRNCH_NAME` BRANCHES,
		// cc.`BRNCH_OCODE3`
		// FROM ((({$this->db_erp}.`trx_manrecs_hd` aa 
		// JOIN {$this->db_erp}.`trx_manrecs_dt` xx
		// ON (aa.`recid` = xx.`mrhd_rid`))
		// LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc ON (cc.`recid` = aa.`branch_id`))
		// LEFT JOIN {$this->db_erp}.`mst_vendor` bb ON (bb.`recid` = aa.`supplier_id`)) 
		// WHERE !(aa.`flag`='C') AND !(aa.`df_tag`='D') AND (aa.`post_tag`='Y') {$str_supp} {$str_brnch} {$str_optn} {$str_dlsomhd}
		// GROUP BY aa.`drno`";
		
		$str="
		SELECT 
		  aa.`recid`,
		  aa.`trx_no`,
		  aa.`comp_id`,
		  aa.`branch_id`,
		  aa.`drno`,
		  aa.`dr_date`,
		  aa.`supplier_id`,
		  aa.`rcv_date`,
		  aa.`date_in`,
		  aa.`hd_remarks`,
		  aa.`hd_sm_tags`,
		  SUM(xx.`qty_corrected`) QUANTITY,
		  SUM(xx.`qty_corrected` * xx.`ucost`) COST,
		  SUM(xx.`qty_corrected` * xx.`uprice`) AMOUNT,
		  aa.`muser`,
		  aa.`encd_date`,
		  aa.`flag`,
		  aa.`p_flag`,
		  aa.`df_tag`,
		  aa.`post_tag`,
		bb.`VEND_NAME` SUPPLIER,
		cc.`BRNCH_NAME` BRANCHES,
		cc.`BRNCH_OCODE3`
		FROM (({$this->db_erp}.`trx_manrecs_hd` aa 
		JOIN {$this->db_erp}.`trx_manrecs_dt` xx
		ON (aa.`recid` = xx.`mrhd_rid`)
		LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc ON (cc.`recid` = aa.`branch_id`))
		LEFT JOIN {$this->db_erp}.`mst_vendor` bb ON (bb.`recid` = aa.`supplier_id`)) 
		WHERE !(aa.`flag`='C') AND !(aa.`df_tag`='D') AND (aa.`post_tag`='Y') {$str_supp} {$str_brnch} {$str_optn} {$str_dlsomhd}
		GROUP BY aa.`drno`
		"; //  AND !(aa.`post_tag`='N')  Pinatanggal noong septyembere 17,2019 ni Sir Claudio
		//reviseed oct 26 2022 add sum and join in dt
		//var_dump($str);
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->num_rows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn = _XMYAPP_PATH_; 
			$mpathdest = $mpathdn . '/downloads'; 
			$cdate = date('Ymd');
			$cfiletmp = 'rcvng_dr_rpt' . '_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
			$cfiledest = $mpathdest . '/' . $cfiletmp;
			$cfilelnk = site_url() . '/downloads/' . $cfiletmp;
			//SEND TO UALAM
			$this->mylibz->user_logs_activity_module($this->db_erp,'RCVDRRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			//SECUREW FILES
			if(file_exists($cfiledest)) {
			unlink($cfiledest);
			}
			$fh = fopen($cfiledest, 'w');
			fwrite($fh, $chtml);
			fclose($fh); 
			chmod($cfiledest, 0755);
			$ntqty=0;
			$ntsrp=0;
			$ntmu=0;
			$ntcost=0;
			$ntprc=0;
			$ntpay=0;
			$gt_ntqty=0;
			$gt_ntcost=0;
			$gt_ntsrp=0;
			$qrw = $q->result_array();
				foreach($qrw as $rw):
					$chtml = "	<tr class=\"data-nm\">
						       	<td>".$res."</td>
						       	<td>'".$rw['drno']."</td>
						       	<td>".$rw['BRANCHES']."</td>
						       	<td>".$rw['BRNCH_OCODE3']."</td>
						       	<td>".$rw['dr_date']."</td>
						       	<td>".$rw['rcv_date']."</td>
						        <td>".$rw['QUANTITY']."</td>
								<td>".number_format($rw['AMOUNT'],2,'.','') ."</td>
								<td>".number_format($rw['COST'],2,'.','')."</td>
						       </tr>
						   ";
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
				$ntqty=$ntqty + $rw['QUANTITY'];
				$ntsrp=$ntsrp + $rw['AMOUNT'];
				$ntcost= $ntcost + $rw['COST'];
				$res++;
				endforeach;
				
				//QUERY
				$chtml = "<tr></tr>
						  <tr class=\"header-tr\">
					        <th align=\"right\" class=\"noborder\" colspan=\"6\">PURCHASES</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".$ntqty."</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".number_format($ntsrp,2,'.','')."</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".number_format($ntcost,2,'.','')."</th>
					      </tr>
					      <tr></tr>
						   ";
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX );	

				
			
		}//end if
		else{
			echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				No Data Found!!!
				</div>				
			";
			die();
		}
		//QUERY

		$res2=1;
		$po_ntqty=0;
		$po_ntsrp=0;
		$po_ntcost=0;
		$str="
		SELECT 
		  aa.`recid`,
		  aa.`potrx_no`,
		  aa.`po_no`,
		  aa.`po_date`,
		  cc.`BRNCH_NAME` BRANCHES,
		  cc.`BRNCH_OCODE3`,
		IFNULL(aa.`hd_subtamt`,0) PTSRP,
		IFNULL(aa.`hd_subtqty`,0) PTQTY,
		IFNULL(aa.`hd_subtcost`,0) PTCOST
		FROM ({$this->db_erp}.`trx_manrecs_po_hd` aa 
		LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc ON (cc.`recid` = aa.`branch_id`))
		WHERE !(aa.`flag`='C') AND !(aa.`df_tag`='D') AND (aa.`po_rsons_id`='5') {$str_supp} {$str_brnch} {$str_optn_po}";//  AND !(aa.`post_tag`='N') Pinatanggal noong septyembere 17,2019 ni Sir Claudio
		//var_dump($str);
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$qrw2 = $q->result_array();
		foreach($qrw2 as $rw2):
					$chtml = "	<tr class=\"data-nm\">
						       	<td>".$res2."</td>
						       	<td>'".$rw2['po_no']."</td>
						       	<td>".$rw2['BRANCHES']."</td>
						       	<td>".$rw2['BRNCH_OCODE3']."</td>
						       	<td>".$rw2['po_date']."</td>
						       	<td></td>
						        <td>".$rw2['PTQTY']."</td>
								<td>".number_format($rw2['PTSRP'],2,'.','') ."</td>
								<td>".number_format($rw2['PTCOST'],2,'.','')."</td>
						       </tr>
						   ";
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
				$po_ntqty=$po_ntqty + $rw2['PTQTY'];
				$po_ntsrp=$po_ntsrp + $rw2['PTSRP'];
				$po_ntcost= $po_ntcost + $rw2['PTCOST'];
				$res2++;
				endforeach;
				$gt_ntqty= $ntqty-$po_ntqty;
				$gt_ntcost= $ntcost-$po_ntcost;
				$gt_ntsrp= $ntsrp-$po_ntsrp;
		$chtml = "<tr></tr>
						 
					      <tr class=\"header-tr\">
					        <th align=\"right\" class=\"noborder\" colspan=\"6\">PURCHASE RETURN</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".$po_ntqty."</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".number_format($po_ntsrp,2,'.','')."</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".number_format($po_ntcost,2,'.','')."</th>
					      </tr>
					      <tr class=\"header-tr\">
					        <th align=\"right\" class=\"noborder\" colspan=\"6\">NET PURCHASES(PAYABLES)</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".$gt_ntqty."</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".number_format($gt_ntsrp,2,'.','')."</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"1\">".number_format($gt_ntcost,2,'.','')."</th>
					      </tr>
					      <tr></tr>
					      <tr></tr>
					      <tr></tr>
					      <tr class=\"header-tr\">
					        <th align=\"left\" class=\"noborder\" colspan=\"2\">PREPARED BY:</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"2\">".$cuser_fullname."</th>
					        <th align=\"left\" class=\"noborder\" colspan=\"2\">CHECKED BY:</th>
					        <th align=\"center\" class=\"noborder\" colspan=\"2\">MARGIE OLIVER</th>
					      </tr>
						   ";
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
		$chtmljs .= "
				<script type=\"text/javascript\">
					//window.parent.document.getElementById('myscrloading').innerHTML = '';
					jQuery('#lnkexportmsexcel_dr').click(function() { 
						//jQuery('#messproc').css({display:''});
						window.location = '{$cfilelnk}';
						jQuery('#fld_drsupp').val('');
						jQuery('#fld_drsupp_id').val('');
						jQuery('#fld_drbrnch').val('');
						jQuery('#fld_drbrnch_id').val('');
						$('#lnkexportmsexcel_dr').css({display:'none'});
					});
					
					jQuery('#lnktoprint').click(function() { 
						jQuery('#__mtoexport_drtd').css({display:'none'});
						//jQuery('#__mtoprint').css({display:'none'});
						window.print();			
					});
				</script>
				
				";
		echo $chtmljs;

	}//end func
	public function td_rpt_download_proc(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cuser_fullname = $this->mylibz->mysys_user_fullname();
		$fld_tdsupp =$this->input->get_post('fld_tdsupp');
		$fld_tdsupp_id =$this->input->get_post('fld_tdsupp_id');
		$fld_months =$this->input->get_post('fld_months');
		$fld_years =$this->input->get_post('fld_years');
		$str_supp_aa='';
		$str_supp='';
		$grp_supp='';
		$chtmlhd="";
		$chtmljs ="";
		$chtml = "";
		$cmsexp =  "";
		$cmsgt =  "";
		$chtml2 = "";
		$cmsft =  "";
		$date = date("F j, Y, g:i A");
		//CONVERTING MONTH to name
		$dateObj   = DateTime::createFromFormat('!m', $fld_months);
		$monthName = $dateObj->format('F');
		//VALIDATING DATA SUPPLIER
		if(!empty($fld_tdsupp)) {
		$str = "select recid,
			VEND_NAME,
			VEND_ADDR1 ADDR1,
			VEND_ADDR2 ADDR2,
			VEND_TELNO TELNO from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$fld_tdsupp' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_tdsupp_id'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				Invalid Supplier Data	
				";
				die();
			}//end if
			$rw = $q->row_array();
			$fld_ADDR1 = $rw['ADDR1'];
			$fld_ADDR2 = $rw['ADDR2'];
			$fld_TELNO = $rw['TELNO'];
			$q->free_result();
			$str_supp_aa= "AND sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '$fld_tdsupp_id'";
			$str_supp= "AND sha2(concat(`supplier_id`,'{$mpw_tkn}'),384) = '$fld_tdsupp_id'";
			$grp_supp=",`supplier_id`";
		}//end if
		
		
		$chtmljs .= "
		<div class=\"col-md-6\" id=\"__mtoexport\">
			<div class=\"col-md-3\">
				<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_td\"><i class=\"btn btn-success fa fa-download\"> TOTAL DELIVERY</i></a></span>
			</div>
			</br>
		</div>	
		";
	
        $chtml = "
					<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
						<head>
						<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
						</head>
						<body>
                	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable\">
					   
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"8\">Total Delivery of the month</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"8\">".$monthName." ".$fld_years."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"8\">SUPPLIER: ".$fld_tdsupp."</th>
					      </tr>
					      <tr class=\"header-tr-addr\">
					        <th class=\"noborder\" colspan=\"8\">&nbsp;</th>
					      </tr>
					      <tr class =\"header-theme-purple text-white\">
					        <th class=\"noborder\">No</th>
					        <th class=\"noborder\">BRANCHES</th>
					        <th class=\"noborder\">UNIQUE IDENTIER</th>
					        <th class=\"noborder\">PUCHASE QUANTITY</th>
					       	<th class=\"noborder\">PURCHASE COST</th>
					       	<th class=\"noborder\">PURCHASE RETURN QUANTITY</th>
					        <th class=\"noborder\">PURCHASE RETURN COST</th>
					        <th class=\"noborder\">PAYABLES</th>
					        </tr>
					    ";
		
		//QUERY

		$str="
		SELECT 
		xx.`BRANCHES`,
		xx.`BRNCH_OCODE3`,
		xx.`QUANTITY`,
		xx.`SRP`,
		xx.`COST`,
		xx.`MARK_UP`,
		IFNULL(yy.`__hd_subttqty`,0) PRQUANTITY,
		IFNULL(yy.`__hd_subtcost`,0) PRC,
		(xx.`COST` - IFNULL(yy.`__hd_subtcost`,0)) PAYABLES  
		FROM 
			((SELECT cc.`BRNCH_NAME` BRANCHES,
			cc.`BRNCH_OCODE3`,
			aa.`branch_id`,
			aa.`supplier_id`, 
			SUM(aa.`hd_subtqty`) QUANTITY, 
			SUM(aa.`hd_subtamt`) SRP, 
			SUM(aa.`hd_subtcost`) COST, 
			SUM(aa.`hd_subtamt` - aa.`hd_subtcost`) MARK_UP
			FROM ({$this->db_erp}.`trx_manrecs_hd` aa 
			LEFT JOIN {$this->db_erp}.`mst_companyBranch` cc 
			ON (cc.`recid` = aa.`branch_id`)) 
			WHERE YEAR(aa.`rcv_date`) = '$fld_years' AND  MONTH(aa.`rcv_date`) = '$fld_months' {$str_supp_aa} AND !(aa.`flag`='C') AND !(aa.`df_tag`='D') AND !(aa.`post_tag`='N') 
			GROUP BY aa.`branch_id`) xx 
		LEFT JOIN (
		SELECT 
		`branch_id`,
		`supplier_id`,
		SUM(`hd_subtqty`) __hd_subttqty,
		SUM(`hd_subtcost`) __hd_subtcost 
		FROM {$this->db_erp}.`trx_manrecs_po_hd` 
		WHERE YEAR(`po_date`) = '$fld_years' AND  MONTH(`po_date`) = '$fld_months' {$str_supp} AND !(`flag`='C') AND !(`df_tag`='D') AND !(`post_tag`='N') AND (`po_rsons_id`='5') 
		GROUP BY `branch_id` {$grp_supp} 
		) yy ON ( xx.`branch_id` = yy.`branch_id` AND xx.`supplier_id` = yy.`supplier_id`))";
		
		//var_dump($str);
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->num_rows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn = _XMYAPP_PATH_; 
			$mpathdest = $mpathdn . '/downloads'; 
			$cdate = date('Ymd');
			$cfiletmp = 'rcvng_inv_rpt' . '_' . $this->mylibz->random_string(9) . '.xls' ;
			$cfiledest = $mpathdest . '/' . $cfiletmp;
			$cfilelnk = site_url() . '/downloads/' . $cfiletmp;
			//SEND TO UALAM
			$this->mylibz->user_logs_activity_module($this->db_erp,'RCVTDRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			//SECUREW FILES
			if(file_exists($cfiledest)) {
			unlink($cfiledest);
			}
			$fh = fopen($cfiledest, 'w');
			fwrite($fh, $chtml);
			fclose($fh); 
			chmod($cfiledest, 0755);
			$ntqty=0;
			$ntprtqty=0;
			$ntmu=0;
			$ntcost=0;
			$ntprc=0;
			$ntpay=0;
			$qrw = $q->result_array();
				foreach($qrw as $rw):
					$chtml = "	<tr class=\"data-nm\">
						       	<td>".$res."</td>
						       	<td>".$rw['BRANCHES']."</td>
						       	<td>".$rw['BRNCH_OCODE3']."</td>
						        <td>".$rw['QUANTITY']."</td>
								<td>".number_format($rw['COST'],2,'.','')."</td>
								<td>".$rw['PRQUANTITY']."</td>
						       	<td>". number_format($rw['PRC'],2,'.','')."</td>
						       	<td>". number_format($rw['PAYABLES'],2,'.','')."</td>
						       	</tr>
						   ";
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
				$ntqty=$ntqty + $rw['QUANTITY'];
				$ntcost= $ntcost + $rw['COST'];
				$ntprtqty=$ntprtqty + $rw['PRQUANTITY'];
				$ntprc=$ntprc + $rw['PRC'];
				$ntpay=$ntpay + $rw['PAYABLES'];
				$res++;
				endforeach;

				
			
		}//end if
		else{
			echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				No Data Found!!!
				</div>				
			";
			die();
		}
		
		$q->free_result();
		$cmsgt = "	<tr class=\"noborder\" style=\"font-weight:bold;\">
						       	<th colspan=\"3\">Grand Total</th>
						        <th>".$ntqty."</th>
								<th>".number_format($ntcost,2,'.','')  ."</th>
								<th>".$ntprtqty."</th>
						       	<th>". number_format($ntprc,2,'.','') ."</th>
						       	<th>". number_format($ntpay,2,'.','') ."</th>
					</tr>
					<tr></tr>
					<tr></tr>
					<tr></tr>
					<tr class=\"header-tr\">
					    <th align=\"left\" class=\"noborder\" colspan=\"1\">PREPARED BY:</th>
					    <th align=\"center\" class=\"noborder\" colspan=\"3\">".$cuser_fullname."</th>
					    <th align=\"left\" class=\"noborder\" colspan=\"1\">CHECKED BY:</th>
					    <th align=\"center\" class=\"noborder\" colspan=\"2\">MARGIE OLIVER</th>
					</tr>
						   
						   ";
		file_put_contents( $cfiledest , $cmsgt , FILE_APPEND | LOCK_EX );


		$chtmljs .= "
				<script type=\"text/javascript\">
					//window.parent.document.getElementById('myscrloading').innerHTML = '';
					jQuery('#lnkexportmsexcel_td').click(function() { 
						//jQuery('#messproc').css({display:''});
						window.location = '{$cfilelnk}';
						jQuery('#fld_tdsupp').val('');
						jQuery('#fld_tdsupp_id').val('');
						$('#lnkexportmsexcel_td').css({display:'none'});
					});
					
					jQuery('#lnktoprint').click(function() { 
						jQuery('#__mtoexport').css({display:'none'});
						//jQuery('#__mtoprint').css({display:'none'});
						window.print();			
					});
				</script>
				
				";
		echo $chtmljs;

	}//end func
	//VIEWING LOGFILE
	public function view_post_recs($npages = 1,$npagelimit = 30,$fld_pbranch='',$fld_pbranch_id='',$fld_pdtfrm='',$fld_pdtto=''){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		/*var_dump($fld_pbranch);
		die();*/

		$__flag="C";
		$str_brnch = "";
		$str_date = "";
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		if($this->cusergrp == 'SA'){
			$str_vwrecs = "";
		}
		if(!empty($fld_pbranch) && !empty($fld_pbranch_id)) {
			$str_brnch = " AND sha2(concat(aa.`branch_id`,'{$mpw_tkn}'),384) = '$fld_pbranch_id'";
		}
		if((!empty($fld_pdtfrm) && !empty($fld_pdtto)) && (($fld_pdtfrm != '--') && ($fld_pdtto != '--'))) {
			$str_date = " AND (aa.`rcv_date` >= '{$fld_pdtfrm}' AND  aa.`rcv_date` <= '{$fld_pdtto}')";
			
		}
		if(((!empty($fld_pdtfrm) && !empty($fld_pdtto)) && (($fld_pdtfrm != '--') && ($fld_pdtto != '--'))) || (!empty($fld_pbranch) && !empty($fld_pbranch_id))){
			$strqry = "
			select
			aa.`recid` __arid,
			aa.`trx_no`,
		  	aa.`comp_id`,
		  	aa.`branch_id`,
		  	aa.`drno`,
		  	aa.`dr_date`,
		  	aa.`supplier_id`,
		  	aa.`rcv_date`,
		  	aa.`date_in`,
		  	aa.`hd_remarks`,
		  	aa.`hd_sm_tags`,
		  	aa.`hd_subtqty`,
		  	aa.`hd_subtcost`,
		  	aa.`hd_subtamt`,
		  	aa.`muser`,
		  	aa.`encd_date`,
		  	aa.`flag`,
		  	aa.`p_flag`,
		  	aa.`df_tag`,
		  	aa.`post_tag`,
			bb.COMP_NAME,
			cc.BRNCH_NAME,
			dd.VEND_NAME,
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
			 from {$this->db_erp}.`trx_manrecs_hd` aa
			JOIN {$this->db_erp}.`mst_company` bb
			ON (aa.`comp_id` = bb.`recid`)
			JOIN {$this->db_erp}.`mst_companyBranch` cc
			ON (aa.`branch_id` = cc.`recid`)
			JOIN {$this->db_erp}.`mst_vendor` dd
			ON (aa.`supplier_id` = dd.`recid`)
			where aa.flag != '$__flag' AND aa.`post_tag` = 'N' AND aa.`df_tag`='F'
			{$str_brnch} {$str_date}
			";
			
			//var_dump($strqry);
			$str = "
			select count(*) __nrecs from ({$strqry}) oa
			";
			$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $qry->row_array();
			$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
			$nstart = ($npagelimit * ($npages - 1));
			
			
			$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
			$data['npage_count'] = $npage_count;
			$data['npage_curr'] = $npages;
			$str = "
			SELECT * from ({$strqry}) oa order by __arid limit {$nstart},{$npagelimit} ";
			$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($qry->num_rows() > 0) { 
				$data['rlist'] = $qry->result_array();
				$data['fld_pbranch'] = $fld_pbranch;
				$data['fld_pbranch_id'] = $fld_pbranch_id;
				$data['fld_pdtfrm'] = $fld_pdtfrm;
				$data['fld_pdtto'] = $fld_pdtto;
			} else { 
				$data = array();
				$data['npage_count'] = 1;
				$data['npage_curr'] = 1;
				$data['rlist'] = '';
				$data['fld_pbranch'] = $fld_pbranch;
				$data['fld_pbranch_id'] = $fld_pbranch_id;
				$data['fld_pdtfrm'] = $fld_pdtfrm;
				$data['fld_pdtto'] = $fld_pdtto;
			}
			return $data;
		}
		else{
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_pbranch'] = $fld_pbranch;
			$data['fld_pbranch_id'] = $fld_pbranch_id;
			$data['fld_pdtfrm'] = $fld_pdtfrm;
			$data['fld_pdtto'] = $fld_pdtto;

			return $data;
		}
		//IF SEARCH IS NOT EMPTY
		/*if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = " where (aa.`trx_no` like '%$msearchrec%' or aa.`drno` like '%$msearchrec%' or bb.`COMP_NAME` like '%$msearchrec%' or cc.`BRNCH_NAME` like '%$msearchrec%' or dd.`VEND_NAME` like '%$msearchrec%') AND aa.flag != '$__flag' {$str_vwrecs}";
		}
		if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_vwrecs}";
		}*/ 
		/*
		ee.*,
		ff.ART_CODE,
		ff.ART_DESC,
		aa.`recid` __arid,
		bb.`recid` __brid,
		cc.`recid` __crid,
		dd.`recid` __drid,
		ff.`recid` __frid,
		*/
		
	}//endfunc
	//VIEWING cpost recs
	public function view_cpost_recs($npages = 1,$npagelimit = 30,$fld_pbranch='',$fld_pbranch_id='',$fld_pdtfrm='',$fld_pdtto=''){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		/*var_dump($fld_pbranch);
		die();*/
		//var_dump($npages);
		$__flag="C";
		$str_brnch = "";
		$str_date = "";
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		if($this->cusergrp == 'SA'){
			$str_vwrecs = "";
		}
		if(!empty($fld_pbranch) && !empty($fld_pbranch_id)) {
			$str_brnch = " AND sha2(concat(aa.`branch_id`,'{$mpw_tkn}'),384) = '$fld_pbranch_id'";
		}
		if((!empty($fld_pdtfrm) && !empty($fld_pdtto)) && (($fld_pdtfrm != '--') && ($fld_pdtto != '--'))) {
			$str_date = " AND (aa.`rcv_date` >= '{$fld_pdtfrm}' AND  aa.`rcv_date` <= '{$fld_pdtto}')";
			
		}
		if(((!empty($fld_pdtfrm) && !empty($fld_pdtto)) && (($fld_pdtfrm != '--') && ($fld_pdtto != '--'))) || (!empty($fld_pbranch) && !empty($fld_pbranch_id))){
			$strqry = "
			select
			aa.`recid` __arid,
			aa.`trx_no`,
		  	aa.`comp_id`,
		  	aa.`branch_id`,
		  	aa.`drno`,
		  	aa.`dr_date`,
		  	aa.`supplier_id`,
		  	aa.`rcv_date`,
		  	aa.`date_in`,
		  	aa.`hd_remarks`,
		  	aa.`hd_sm_tags`,
		  	aa.`hd_subtqty`,
		  	aa.`hd_subtcost`,
		  	aa.`hd_subtamt`,
		  	aa.`muser`,
		  	aa.`encd_date`,
		  	aa.`flag`,
		  	aa.`p_flag`,
		  	aa.`df_tag`,
		  	aa.`post_tag`,
		  	aa.`counter_tag`,
			bb.COMP_NAME,
			cc.BRNCH_NAME,
			dd.VEND_NAME,
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
			 from {$this->db_erp}.`trx_manrecs_hd` aa
			JOIN {$this->db_erp}.`mst_company` bb
			ON (aa.`comp_id` = bb.`recid`)
			JOIN {$this->db_erp}.`mst_companyBranch` cc
			ON (aa.`branch_id` = cc.`recid`)
			JOIN {$this->db_erp}.`mst_vendor` dd
			ON (aa.`supplier_id` = dd.`recid`)
			where aa.flag != '$__flag' AND aa.`post_tag` = 'Y' AND aa.`df_tag`='F' AND aa.`counter_tag` ='N'
			{$str_brnch} {$str_date}
			";
			
			//var_dump($strqry);
			$str = "
			select count(*) __nrecs from ({$strqry}) oa
			";
			$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $qry->row_array();
			$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
			$nstart = ($npagelimit * ($npages - 1));
			
			
			$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
			$data['npage_count'] = $npage_count;
			$data['npage_curr'] = $npages;
			$str = "
			SELECT * from ({$strqry}) oa order by __arid limit {$nstart},{$npagelimit} ";
			$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($qry->num_rows() > 0) { 
				$data['rlist'] = $qry->result_array();
				$data['fld_pbranch'] = $fld_pbranch;
				$data['fld_pbranch_id'] = $fld_pbranch_id;
				$data['fld_pdtfrm'] = $fld_pdtfrm;
				$data['fld_pdtto'] = $fld_pdtto;

			} else { 
				$data = array();
				$data['npage_count'] = 1;
				$data['npage_curr'] = 1;
				$data['rlist'] = '';
				$data['fld_pbranch'] = $fld_pbranch;
				$data['fld_pbranch_id'] = $fld_pbranch_id;
				$data['fld_pdtfrm'] = $fld_pdtfrm;
				$data['fld_pdtto'] = $fld_pdtto;
			}
			return $data;
		}
		else{
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_pbranch'] = $fld_pbranch;
			$data['fld_pbranch_id'] = $fld_pbranch_id;
			$data['fld_pdtfrm'] = $fld_pdtfrm;
			$data['fld_pdtto'] = $fld_pdtto;

			return $data;
		}
		//IF SEARCH IS NOT EMPTY
		/*if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = " where (aa.`trx_no` like '%$msearchrec%' or aa.`drno` like '%$msearchrec%' or bb.`COMP_NAME` like '%$msearchrec%' or cc.`BRNCH_NAME` like '%$msearchrec%' or dd.`VEND_NAME` like '%$msearchrec%') AND aa.flag != '$__flag' {$str_vwrecs}";
		}
		if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_vwrecs}";
		}*/ 
		/*
		ee.*,
		ff.ART_CODE,
		ff.ART_DESC,
		aa.`recid` __arid,
		bb.`recid` __brid,
		cc.`recid` __crid,
		dd.`recid` __drid,
		ff.`recid` __frid,
		*/
		
	}//endfunc
	//ENCODING POSTING
	public function rcv_cposting(){
		$mtkn_trxno = $this->input->get_post('mtkn_trxno');
		$id_post = $this->input->get_post('id_post');
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		
		//CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		if(!empty($mtkn_trxno)) { 
			if($this->cusergrp != 'SA') { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Note</strong> You don't authorized to edit this data!!!</div>";
				die();
			}
			//SELECT IF ALREADY POSTED
			$str = "select counter_tag from {$this->db_erp}.`trx_manrecs_hd` aa WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_trxno' AND `post_tag` = 'Y' AND counter_tag ='Y'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() > 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Already countered!!!.</div>";
				die();
			}
		} //END CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		if(!empty($mtkn_trxno)) { 
					$str = "
					update {$this->db_erp}.`trx_manrecs_hd`
					SET `counter_tag` = 'Y',
					`counter_date` = now()
					WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_trxno';
					";

					$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibz->user_logs_activity_module($this->db_erp,'UPD_CPOSTING','',$mtkn_trxno,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
					echo  "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Counter Posting Recorded Successfully!!!</div>
					
					";
					/*<script type=\"text/javascript\"> 
						function __post_refresh_data() { 
							try { 
								$('#post_".$id_post."').prop('disabled',true);
							} catch(err) { 
								var mtxt = 'There was an error on this page.\\n';
								mtxt += 'Error description: ' + err.message;
								mtxt += '\\nClick OK to continue.';
								alert(mtxt);
								return false;
							}  //end try 
						} 
						
						__post_refresh_data();
					</script>*/

				}
			//redirect('mytrx_acct/acct_man_recs_vw');
	}
	public function drrcv_rpt_download_proc(){
	$cuserlvl=$this->mylibz->mysys_userlvl();
	$cuser = $this->mylibz->mysys_user();
	$cuser_fullname = $this->mylibz->mysys_user_fullname();
	$mpw_tkn = $this->mylibz->mpw_tkn();

	$fld_drcvbranch = $this->input->get_post('fld_drcvbranch');
	$fld_drcvbranch_id = $this->input->get_post('fld_drcvbranch_id');
	
	$fld_drcv_month = $this->input->get_post('fld_drcv_month');
	$fld_drcv_year = $this->input->get_post('fld_drcv_year');
	

	$str_brnch='';
	$str_optn_r ='';
	$str_optn_p ='';
	$monthName ='';
	$fld_area_code ='';
	$chtmlhd="";
	$chtmljs ="";
	$chtml = "";
	$cmsexp =  "";
	$cmsgt =  "";
	$chtml2 = "";
	$cmsft =  "";
	$date = date("F j, Y, g:i A");
	//CONVERTING MONTH to name
	$dateObj   = DateTime::createFromFormat('!m', $fld_drcv_month);
	$monthName = $dateObj->format('F');
	if((!empty($fld_drcv_month)) && (!empty($fld_drcv_year))) {
		$str_optn_r = "AND (YEAR(b.`dr_date`) ='$fld_drcv_year' AND MONTH(b.`dr_date`) ='$fld_drcv_month')";
		$str_optn_p = "AND (YEAR(b.`dr_date`) ='$fld_drcv_year' AND MONTH(b.`dr_date`) ='$fld_drcv_month')";
	}
	//BRANCH
	if(!empty($fld_drcvbranch)) {
		$str = "select recid,BRNCH_NAME 
		 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_drcvbranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_drcvbranch_id'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->num_rows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid branch Data!!!.</div>";
			die();
		}

		$rw = $q->row_array();
		$fld_area_code = $rw['BRNCH_NAME'];
		$q->free_result();
		$str_brnch="AND sha2(concat(b.`branch_id`,'{$mpw_tkn}'),384)= '{$fld_drcvbranch_id}'";
		//END BRANCH
	}//end if
	
	// $chtmljs .= "
	// <div class=\"col-md-6\" id=\"__mtoexport_drtd\">
	// 	<div class=\"col-md-3\">
	// 		<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_drcvb\"><i class=\"btn btn-success fa fa-download\"> DR</i></a></span>
	// 	</div>
	// 	</br>
		//       </div>	
	// ";
	////////////////////////////////////////////////////////////////////////DR REPORTS/////////////////////////////////////////////////////////////////////////////////
    $chtml = "
				<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
					<head>
					<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
					</head>
					<body>
            	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_drrcv\">
				   
				     <tr class=\"header-tr-addr\">
				        <th class=\"noborder\" colspan=\"12\">GOLDEN WIN EMPIRE MARKETING CORPORATION</th>
				      </tr>
				      <tr class=\"header-tr-addr\">
				          <th class=\"noborder\" colspan=\"12\">".$monthName." ".$fld_drcv_year."</th>
				      </tr>
				      <tr class=\"header-tr-addr\">
				        <th class=\"noborder\" colspan=\"12\">BRANCH: ".$fld_area_code."</th>
				      </tr>
				      <tr class=\"header-tr-addr\">
				        <th class=\"noborder\" colspan=\"12\">&nbsp;</th>
				      </tr>
				      <tr class =\"header-theme-purple text-white\">
				        <th class=\"noborder\"></th>
				        <th class=\"noborder\">TRANSACTION NO</th>
				        <th class=\"noborder\">DR NUMBER</th>
				        <th class=\"noborder\">SUPPLIER</th>
				        <th class=\"noborder\">COMPANY</th>
				        <th class=\"noborder\">AREA CODE</th>
				        <th class=\"noborder\">UNIQUE IDENTIER</th>
				        <th class=\"noborder\">DR DATE</th>
				        <th class=\"noborder\">DATE RECEIVED</th>
				        <th class=\"noborder\">DATE IN</th>
				        <th class=\"noborder\">QUANTIITY</th>
				        <th class=\"noborder\">TOTAL COST</th>
				        <th class=\"noborder\">TOTAL SRP</th>
				        <th class=\"noborder\">ENCODED USER</th>
				        <th class=\"noborder\">ENCODED DATE</th>
				      </tr>
				    ";
	
	//QUERY

	$str="
	SELECT
	xxx.`__r`,
	xxx.`__trxno`,
	xxx.`__drno`,
	bbb.`VEND_NAME`,
	ccc.`COMP_NAME`,
	aaa.`BRNCH_NAME`,
	aaa.`BRNCH_OCODE3`,
	xxx.`__drdate`,
	xxx.`__datercv`,
	xxx.`__datein`,
	xxx.`tqty` tqty,
	xxx.`tcost` tcost,
	xxx.`tamt` tamt,
	xxx.`muser`,
	xxx.`encd_date`
	FROM
	(	
		SELECT 
		'Delivery Receipt' __r,
		b.`drtrx_no` __trxno,
		b.`drtrx_no` __drno,
		b.`supplier_id`,
		b.`comp_id`,
		b.`branch_id` __branch,
		b.`dr_date` __drdate,
		'' __datercv,
		'' __datein,
		SUM(a.`qty`) tqty,
		SUM(a.`tcost`) tcost,
		SUM(a.`tamt`) tamt,
		b.`encd_date`,
		b.`muser`
		FROM
		{$this->db_erp}.`trx_manrecs_dr_dt` a
		JOIN {$this->db_erp}.`trx_manrecs_dr_hd` b
		ON (a.`mrhd_rid` = b.`recid`)
		WHERE b.`flag`= 'R'
		{$str_optn_p}
		{$str_brnch}
		GROUP BY  b.`drtrx_no`

		UNION ALL	

		SELECT 
		'Received In' __r,
		b.`trx_no` __trxno,
		b.`drno` __drno,
		b.`supplier_id`,
		b.`comp_id`,
		b.`branch_id` __branch,
		b.`dr_date` __drdate,
		b.`rcv_date` __datercv,
		b.`date_in` __datein,
		SUM(a.`qty_corrected`) tqty,
		SUM(a.`tcost`) tcost,
		SUM(a.`tamt`) tamt,
		b.`encd_date`,
		b.`muser`
		FROM
		{$this->db_erp}.`trx_manrecs_dt` a
		JOIN {$this->db_erp}.`trx_manrecs_hd` b
		ON (a.`mrhd_rid` = b.`recid`)
		WHERE (b.`supplier_id` = '1425' OR b.`supplier_id` = '4773') AND b.`flag`= 'R' AND b.`drno` LIKE '%DR%'
		{$str_optn_r}
		{$str_brnch}
		GROUP BY  b.`drno` 

		UNION ALL	

		SELECT 
		'Variance' __r,
		aa.`__trxno`,
		aa.`__drno`,
		aa.`supplier_id`,
		aa.`comp_id`,
		aa.`__branch` __branch,
		aa.`dr_date` __drdate,
		'' __datercv,
		'' __datein,
		SUM(aa.`tqty`) tqty,
		SUM(aa.`tcost`) tcost,
		SUM(aa.`tamt`) tamt,
		aa.`encd_date`,
		aa.`muser`
		FROM
		(
			SELECT 
			b.`drtrx_no` __trxno,
			b.`drtrx_no` __drno,
			b.`supplier_id`,
			b.`comp_id`,
			b.`branch_id` __branch,
			b.`dr_date`,
			SUM(a.`qty`) tqty,
			SUM(a.`tcost`) tcost,
			SUM(a.`tamt`) tamt,
			b.`encd_date`,
			b.`muser`
			FROM
			{$this->db_erp}.`trx_manrecs_dr_dt` a
			JOIN {$this->db_erp}.`trx_manrecs_dr_hd` b
			ON (a.`mrhd_rid` = b.`recid`)
			WHERE b.`flag`= 'R'
			{$str_optn_p}
			{$str_brnch}
			GROUP BY  b.`drtrx_no`

			UNION ALL	

			SELECT 
			b.`trx_no` __trxno,
			b.`drno` __drno,
			b.`supplier_id`,
			b.`comp_id`,
			b.`branch_id` __branch,
			b.`dr_date`,
			SUM(a.`qty_corrected` * -1) tqty,
			SUM(a.`tcost`  * -1) tcost,
			SUM(a.`tamt`  * -1) tamt,
			b.`encd_date`,
			b.`muser`
			FROM
			`trx_manrecs_dt` a
			JOIN {$this->db_erp}.`trx_manrecs_hd` b
			ON (a.`mrhd_rid` = b.`recid`)
			WHERE (b.`supplier_id` = '1425' OR b.`supplier_id` = '4773') AND b.`flag`= 'R' AND b.`drno` LIKE '%DR%'
			{$str_optn_r}
			{$str_brnch}
			GROUP BY  b.`drno` 
		)aa
		GROUP BY aa.`__drno`,aa.`__branch`
		
	) xxx
	JOIN {$this->db_erp}.`mst_companyBranch` aaa
	ON (xxx.`__branch` = aaa.`recid`)
	JOIN {$this->db_erp}.`mst_vendor` bbb
	ON (xxx.`supplier_id` = bbb.`recid`)
	JOIN {$this->db_erp}.`mst_company` ccc
	ON (xxx.`comp_id` = ccc.`recid`)
	GROUP BY  xxx.`__drno`,xxx.`__branch`,xxx.`__r`

	";  //  AND !(aa.`post_tag`='N')  Pinatanggal noong septyembere 17,2019 ni Sir Claudio
	
	//var_dump($str);
	$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	//var_dump($str);
	$res=1;
	if($q->num_rows() > 0) { 
		//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
		$mpathdn = _XMYAPP_PATH_; 
		$mpathdest = $mpathdn . '/downloads'; 
		$cdate = date('Ymd');
		$cfiletmp = 'dr_rcv_rpt' . '_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
		$cfiledest = $mpathdest . '/' . $cfiletmp;
		$cfilelnk = site_url() . '/downloads/' . $cfiletmp;
		//SEND TO UALAM
		$this->mylibz->user_logs_activity_module($this->db_erp,'POUTRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		//SECUREW FILES
		if(file_exists($cfiledest)) {
		unlink($cfiledest);
		}
		$fh = fopen($cfiledest, 'w');
		fwrite($fh, $chtml);
		fclose($fh); 
		chmod($cfiledest, 0755);
		$ntqty=0;
		$ntsrp=0;
		$ntcost=0;
		
		








		//<th class=\"noborder\">SUPPLIER</th> <td>".$rw['SUPPLIER']."</td>
		$qrw = $q->result_array();
			foreach($qrw as $rw):
				$bold = ($rw['__r'] == 'Variance' ? "<strong style=\"color: #0000ff; !important\">" : ''); 
				$bold1 = ($rw['__r'] == 'Variance' ? "</strong>" : ''); 
				$bold2 = ($rw['__r'] == 'Variance' ? "<tr></tr>" : '');
				
				$__trxno = "'".$rw['__trxno'];
				$__drno = "'".$rw['__drno'];
				$__VEND_NAME =$rw['VEND_NAME'];
				$__COMP_NAME = $rw['COMP_NAME'];
				$__BRNCH_NAME = $rw['BRNCH_NAME'];
				$__BRNCH_OCODE3 = $rw['BRNCH_OCODE3'];
				$__drdate = $this->mylibz->mydate_mmddyyyy($rw['__drdate']);
				$__datercv = $this->mylibz->mydate_mmddyyyy($rw['__datercv']);
				$__datein = $this->mylibz->mydate_mmddyyyy($rw['__datein']);
				$__muser = $rw['muser'];
				$__encd_date = $this->mylibz->mydate_mmddyyyy($rw['encd_date']);
				if($rw['__r'] == 'Variance'){
					$__trxno = '';
					$__drno = '';
					$__VEND_NAME ='';
					$__COMP_NAME = '';
					$__BRNCH_NAME = '';
					$__BRNCH_OCODE3 = '';
					$__drdate = '';
					$__datercv = '';
					$__datein = '';
					$__muser = '';
					$__encd_date = '';
				}
				
				$chtml = "	<tr class=\"data-nm\">
					       	<td>".$bold.$rw['__r'].$bold1."</td>
					       	<td>".$bold.$__trxno.$bold1."</td>
					       	<td>".$bold.$__drno.$bold1."</td>
					       	<td>".$bold.$__VEND_NAME.$bold1."</td>
					       	<td>".$bold.$__COMP_NAME.$bold1."</td>
				       		<td><strong>".$bold.$__BRNCH_NAME.$bold1."</strong></td>
				       		<td><strong>".$bold.$__BRNCH_OCODE3.$bold1."</strong></td>
				       		<td><strong>".$bold.$__drdate.$bold1."</strong></td>
				       		<td><strong>".$bold.$__datercv.$bold1."</strong></td>
				       		<td><strong>".$bold.$__datein.$bold1."</strong></td>
					       	<td>".$bold.number_format($rw['tqty'],2,'.','').$bold1."</td>
					       	<td>".$bold.number_format($rw['tcost'],2,'.','').$bold1."</td>
					       	<td>".$bold.number_format($rw['tamt'],2,'.','').$bold1."</td>
					       	<td><strong>".$bold.$__muser.$bold1."</strong></td>
				       		<td><strong>".$bold.$__encd_date.$bold1."</strong></td>
					       	</tr>

					   ".
					   $bold2;
			file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
			$res++;
			endforeach;

			
		
	}//end if
	else{
		echo "
			<div class=\"alert alert-danger\" role=\"alert\">
			No Data Found!!!
			</div>				
		";
		die();
	}
			
	$chtmljs .= "
			<script type=\"text/javascript\">
				//window.parent.document.getElementById('myscrloading').innerHTML = '';
				drrcv_dl();
				function drrcv_dl() { 
					//jQuery('#lnkexportmsexcel_lbbd_sku').click(function() { 
					//jQuery('#messproc').css({display:''});
					window.location = '{$cfilelnk}';
					//});
				}
				// jQuery('#lnkexportmsexcel_drcvb').click(function() { 
				// 	//jQuery('#messproc').css({display:''});
				// 	window.location = '{$cfilelnk}';
				// 	jQuery('#fld_drcvbranch').val('');
				// 	jQuery('#fld_drcvbranch_id').val('');
				// 	$('#lnkexportmsexcel_drcvb').css({display:'none'});
				// });
				
				jQuery('#lnktoprint').click(function() { 
					jQuery('#__mtoexport_drtd').css({display:'none'});
					//jQuery('#__mtoprint').css({display:'none'});
					window.print();			
				});
			</script>
			
			";
	echo $chtmljs;

	}//end func
	public function var_rpt_download_proc(){
	$cuserlvl=$this->mylibz->mysys_userlvl();
	$cuser = $this->mylibz->mysys_user();
	$cuser_fullname = $this->mylibz->mysys_user_fullname();
	$mpw_tkn = $this->mylibz->mpw_tkn();

	$fld_varbranch = $this->input->get_post('fld_varbranch');
	$fld_varbranch_id = $this->input->get_post('fld_varbranch_id');
	
	$fld_var_month = $this->input->get_post('fld_var_month');
	$fld_var_year = $this->input->get_post('fld_var_year');
	

	$str_brnch='';
	$str_optn_r ='';
	$str_optn_p ='';
	$monthName ='';
	$fld_area_code ='';
	$chtmlhd="";
	$chtmljs ="";
	$chtml = "";
	$cmsexp =  "";
	$cmsgt =  "";
	$chtml2 = "";
	$cmsft =  "";
	$date = date("F j, Y, g:i A");
	$str_itemc_b = '';
	if(!empty($fld_varitemcode_s)) { 
			$str = "select `ART_CODE` 
			from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$fld_varitemcode_s'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> invalid itemcode!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$fld_varitemcode_s = $rw['ART_CODE'];
			$q->free_result();
			//$str_itemc = "AND (xx.`ART_CODE` = '$fld_stinqitemcode_s')";
			$str_itemc_b = " AND (bb.`mat_code` = '$fld_varitemcode_s')";
			
			
		}
	//CONVERTING MONTH to name
	$dateObj   = DateTime::createFromFormat('!m', $fld_var_month);
	$monthName = $dateObj->format('F');
	if((!empty($fld_var_month)) && (!empty($fld_var_year))) {
		$str_optn_r = "AND (YEAR(aa.`rcv_date`) ='$fld_var_year' AND MONTH(aa.`rcv_date`) ='$fld_var_month')";
		
	}
	//BRANCH
	if(!empty($fld_varbranch)) {
		$str = "select recid,BRNCH_NAME 
		 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_varbranch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_varbranch_id'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->num_rows() == 0) { 
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid branch Data!!!.</div>";
			die();
		}

		$rw = $q->row_array();
		$fld_area_code = $rw['BRNCH_NAME'];
		$q->free_result();
		$str_brnch="AND sha2(concat(aa.`branch_id`,'{$mpw_tkn}'),384)= '{$fld_varbranch_id}'";
		//END BRANCH
	}//end if
	
	// $chtmljs .= "
	// <div class=\"col-md-6\" id=\"__mtoexport_drtd\">
	// 	<div class=\"col-md-3\">
	// 		<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_varb\"><i class=\"btn btn-success fa fa-download\"> DR</i></a></span>
	// 	</div>
	// 	</br>
		//       </div>	
	// ";
	////////////////////////////////////////////////////////////////////////DR REPORTS/////////////////////////////////////////////////////////////////////////////////
    $chtml = "
				<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
					<head>
					<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
					</head>
					<body>
            	<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_drrcv\">
				   
				     <tr class=\"header-tr-addr\">
				        <th class=\"noborder\" colspan=\"10\">VARIANCE REPORT</th>
				      </tr>
				      <tr class=\"header-tr-addr\">
				          <th class=\"noborder\" colspan=\"10\">".$monthName." ".$fld_var_year."</th>
				      </tr>
				      <tr class=\"header-tr-addr\">
				        <th class=\"noborder\" colspan=\"10\">BRANCH: ".$fld_area_code."</th>
				      </tr>
				      <tr class=\"header-tr-addr\">
				        <th class=\"noborder\" colspan=\"10\">&nbsp;</th>
				      </tr>
				       <tr class =\"header-theme-purple text-white\">
				        <th class=\"noborder\"></th>
				         <th class=\"noborder\">DR Number</th>
				        <th class=\"noborder\">Item Code</th>
				        <th class=\"noborder\">DR Quantity</th>
				        <th class=\"noborder\">Actual Quantity</th>
				        <th class=\"noborder\">Unit Cost</th>
				        <th class=\"noborder\">SRP</th>
				        <th class=\"noborder\">Variance (Qty)</th>
				        <th class=\"noborder\">Variance (Cost)</th>
				        <th class=\"noborder\">Variance (SRP)</th>
				      </tr>
				    ";
	
	//QUERY

	$str="
	SELECT 
		aa.`drno`,
	    bb.`mat_code` ART_CODE,
	    SUM(bb.`qty_corrected`) ACT_QTY,
	    SUM(bb.`qty`) DR_QTY,
	    bb.`ucost`,
	    bb.`uprice`,
	    SUM(bb.`qty_corrected` - bb.`qty`) VAR_QTY,
	    SUM(bb.`ucost` * (bb.`qty_corrected` - bb.`qty`)) VTCOST,
	    SUM(bb.`uprice` * (bb.`qty_corrected` - bb.`qty`)) VTAMT,
	    aa.`branch_id` BRNCH
	  FROM
	    {$this->db_erp}.`trx_manrecs_hd` aa 
	    JOIN ap2.`trx_manrecs_dt` bb 
	      ON (aa.`recid` = bb.`mrhd_rid`) 
	  WHERE aa.`flag` = 'R' 
	  	{$str_itemc_b}
		{$str_optn_r}
		{$str_brnch}
		GROUP BY aa.`drno`,bb.`mat_code`
	";  //  AND !(aa.`post_tag`='N')  Pinatanggal noong septyembere 17,2019 ni Sir Claudio
	
	//var_dump($str);
	$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	//var_dump($str);
	$res=1;
	if($q->num_rows() > 0) { 
		//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
		$mpathdn = _XMYAPP_PATH_; 
		$mpathdest = $mpathdn . '/downloads'; 
		$cdate = date('Ymd');
		$cfiletmp = 'dr_rcv_rpt' . '_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
		$cfiledest = $mpathdest . '/' . $cfiletmp;
		$cfilelnk = site_url() . '/downloads/' . $cfiletmp;
		//SEND TO UALAM
		$this->mylibz->user_logs_activity_module($this->db_erp,'POUTRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		//SECUREW FILES
		if(file_exists($cfiledest)) {
		unlink($cfiledest);
		}
		$fh = fopen($cfiledest, 'w');
		fwrite($fh, $chtml);
		fclose($fh); 
		chmod($cfiledest, 0755);
		$ntdrqty=0;
		$ntacqty=0;
		$ntqty=0;
		$ntsrp=0;
		$ntcost=0;
		
		$qrw = $q->result_array();
			foreach($qrw as $rw):
				
				
				$chtml = "	<tr class=\"data-nm\">
							<td>".$res."</td>
							<td>'".$rw['drno']."</td>
					       	<td>".$rw['ART_CODE']."</td>
					       	<td>".number_format($rw['DR_QTY'],2,'.','')."</td>
					       	<td>".number_format($rw['ACT_QTY'],2,'.','')."</td>
					       	<td>".number_format($rw['ucost'],2,'.','')."</td>
					       	<td>".number_format($rw['uprice'],2,'.','')."</td>
					       	<td>".number_format($rw['VAR_QTY'],2,'.','')."</td>
					       	<td>".number_format($rw['VTCOST'],2,'.','')."</td>
					        <td>".number_format($rw['VTAMT'],2,'.','')."</td>
				       		</tr>

					   ";
					   
				$ntdrqty=$ntdrqty + $rw['DR_QTY'];
				$ntacqty=$ntacqty + $rw['ACT_QTY'];
				$ntqty=$ntqty + $rw['VAR_QTY'];
				$ntcost=$ntcost + $rw['VTCOST'];
				$ntsrp=$ntsrp + $rw['VTAMT'];
				file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
				$res++;
				
			endforeach;

			$chtml = "	<tr class=\"data-nm\">
					       	<td colspan= '2'><strong>GRAND TOTAL :</strong></td>
					       	<td>".number_format($ntdrqty,2,'.','')."</td>
					       	<td>".number_format($ntacqty,2,'.','')."</td>
					       	<td></td>
					       	<td></td>
					       	<td>".number_format($ntqty,2,'.','')."</td>
					       	<td>".number_format($ntcost,2,'.','')."</td>
					       	<td>".number_format($ntsrp,2,'.','')."</td>
					        </tr>

					   ";
					   
		file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
			
		
	}//end if
	else{
		echo "
			<div class=\"alert alert-danger\" role=\"alert\">
			No Data Found!!!
			</div>				
		";
		die();
	}
			
	$chtmljs .= "
			<script type=\"text/javascript\">
				var_dl();
				function var_dl() { 
					window.location = '{$cfilelnk}';
					
				}
				jQuery('#lnktoprint').click(function() { 
					jQuery('#__mtoexport_drtd').css({display:'none'});
					//jQuery('#__mtoprint').css({display:'none'});
					window.print();			
				});
			</script>
			
			";
	echo $chtmljs;

}//end func
	public function view_rcvulogs_recs($npages = 1,$npagelimit = 30,$msearchrec='',$fld_ulogstrxno='',$fld_ulogs_dtefrom='',$fld_ulogs_dteto='',$fld_utrx= ''){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		
		$str_optn = "";
		$str_trx_no ="";
		
		$tbls = '`trx_manrecs_hd`';
		$flds = '`trx_no`';
		
		if(!empty($fld_utrx)) {
			$tbls = $fld_utrx == 'R' ? '`trx_manrecs_hd`' : '`trx_manrecs_po_hd`';
			$flds = $fld_utrx == 'R' ? '`trx_no`' : '`potrx_no`';
			
		}	
		
		if(!empty($fld_ulogstrxno)) { 
			$str = "select `recid`,{$flds} trx_no
			from {$this->db_erp}.{$tbls} aa where {$flds} = '$fld_ulogstrxno'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction No!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$fld_ulogstrxno = $rw['trx_no'];
			$trx_id = $rw['recid'];
			$q->free_result();
			//$str_itemc = "AND (xx.`ART_CODE` = '$fld_stinqitemcode_s')";
			$str_trx_no = " AND (aa.`trx_no` = '$fld_ulogstrxno')";
			
			
		}
		if((!empty($fld_ulogs_dtefrom) && !empty($fld_ulogs_dteto)) && (($fld_ulogs_dtefrom != '--') && ($fld_ulogs_dteto != '--'))){
			//$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = "AND (aa.`encd_date` >= '{$fld_ulogs_dtefrom} 00:00:00' AND  aa.`encd_date` <= '{$fld_ulogs_dteto} 23:59:59')";
		}
		/*
		ee.*,
		ff.ART_CODE,
		ff.ART_DESC,
		aa.`recid` __arid,
		bb.`recid` __brid,
		cc.`recid` __crid,
		dd.`recid` __drid,
		ff.`recid` __frid,
		*/
		$strqry = "
		SELECT
		  aa.`recid`,
		  aa.`trx_no`,
		  aa.`data_updated`,
		  aa.`u_tag`,
		  aa.`u_module`,
		  aa.`u_desc`,
		  aa.`muser`,
		  aa.`encd_date`
		FROM {$this->db_erp}.`trx_manrecs_rcv_ulogs` aa
		WHERE !(aa.`u_tag` = '')
		{$str_optn} 
		{$str_trx_no}
		";
		//var_dump($strqry);
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->row_array();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->num_rows() > 0) { 
			$data['rlist'] = $qry->result_array();
			$data['fld_ulogstrxno'] = $fld_ulogstrxno;
			$data['fld_ulogs_dtefrom'] = $fld_ulogs_dtefrom;
			$data['fld_ulogs_dteto'] = $fld_ulogs_dteto;
			$data['fld_utrx'] = $fld_utrx;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_ulogstrxno'] = $fld_ulogstrxno;
			$data['fld_ulogs_dtefrom'] = $fld_ulogs_dtefrom;
			$data['fld_ulogs_dteto'] = $fld_ulogs_dteto;
			$data['fld_utrx'] = $fld_utrx;
		}
		return $data;
	}
	//VIEWING post recs
	public function view_grpo_recs($npages = 1,$npagelimit = 30,$fld_grpobranch='',$fld_grpobranch_id='',$fld_grpodtfrm='',$fld_grpodtto=''){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		/*var_dump($fld_grpobranch);
		die();*/

		$__flag="C";
		$str_brnch = "";
		$str_date = "";
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		if($this->cusergrp == 'SA'){
			$str_vwrecs = "";
		}
		if(!empty($fld_grpobranch) && !empty($fld_grpobranch_id)) {
			$str_brnch = " AND sha2(concat(aa.`branch_id`,'{$mpw_tkn}'),384) = '$fld_grpobranch_id'";
		}
		if((!empty($fld_grpodtfrm) && !empty($fld_grpodtto)) && (($fld_grpodtfrm != '--') && ($fld_grpodtto != '--'))) {
			$str_date = " AND (aa.`rcv_date` >= '{$fld_grpodtfrm}' AND  aa.`rcv_date` <= '{$fld_grpodtto}')";
			
		}
		// if(((!empty($fld_grpodtfrm) && !empty($fld_grpodtto)) && (($fld_grpodtfrm != '--') && ($fld_grpodtto != '--'))) || (!empty($fld_grpobranch) && !empty($fld_grpobranch_id))){
		if(   (!empty($fld_grpobranch) && !empty($fld_grpobranch_id))   ||  ((!empty($fld_grpodtfrm) && !empty($fld_grpodtto)) && (($fld_grpodtfrm != '--') && ($fld_grpodtto != '--')))) {
			$strqry = "
			select
			aa.`recid` __arid,
			aa.`trx_no`,
		  	aa.`comp_id`,
		  	aa.`branch_id`,
		  	aa.`drno`,
		  	aa.`dr_date`,
		  	aa.`supplier_id`,
		  	aa.`rcv_date`,
		  	aa.`date_in`,
		  	aa.`hd_remarks`,
		  	aa.`hd_sm_tags`,
		  	aa.`hd_subtqty`,
		  	aa.`hd_subtcost`,
		  	aa.`hd_subtamt`,
		  	aa.`muser`,
		  	aa.`encd_date`,
		  	aa.`flag`,
		  	aa.`p_flag`,
		  	aa.`df_tag`,
		  	aa.`post_tag`,
			bb.COMP_NAME,
			cc.BRNCH_NAME,
			dd.VEND_NAME,
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_arttr 
			 from {$this->db_erp}.`trx_manrecs_hd` aa
			JOIN {$this->db_erp}.`mst_company` bb
			ON (aa.`comp_id` = bb.`recid`)
			JOIN {$this->db_erp}.`mst_companyBranch` cc
			ON (aa.`branch_id` = cc.`recid`)
			JOIN {$this->db_erp}.`mst_vendor` dd
			ON (aa.`supplier_id` = dd.`recid`)
			where aa.flag != '$__flag' AND aa.`df_tag`='D' AND aa.`drno` LIKE 'GRO%'
			{$str_brnch} {$str_date}
			";
			
			//var_dump($strqry);
			$str = "
			select count(*) __nrecs from ({$strqry}) oa
			";
			$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $qry->row_array();
			$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
			$nstart = ($npagelimit * ($npages - 1));
			
			
			$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
			$data['npage_count'] = $npage_count;
			$data['npage_curr'] = $npages;
			$str = "
			SELECT * from ({$strqry}) oa order by __arid limit {$nstart},{$npagelimit} ";
			$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($qry->num_rows() > 0) { 
				$data['rlist'] = $qry->result_array();
				$data['fld_grpobranch'] = $fld_grpobranch;
				$data['fld_grpobranch_id'] = $fld_grpobranch_id;
				$data['fld_grpodtfrm'] = $fld_grpodtfrm;
				$data['fld_grpodtto'] = $fld_grpodtto;
			} else { 
				$data = array();
				$data['npage_count'] = 1;
				$data['npage_curr'] = 1;
				$data['rlist'] = '';
				$data['fld_grpobranch'] = $fld_grpobranch;
				$data['fld_grpobranch_id'] = $fld_grpobranch_id;
				$data['fld_grpodtfrm'] = $fld_grpodtfrm;
				$data['fld_grpodtto'] = $fld_grpodtto;
			}
			return $data;
		}
		else{
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_grpobranch'] = $fld_grpobranch;
			$data['fld_grpobranch_id'] = $fld_grpobranch_id;
			$data['fld_grpodtfrm'] = $fld_grpodtfrm;
			$data['fld_grpodtto'] = $fld_grpodtto;

			return $data;
		}
		//IF SEARCH IS NOT EMPTY
		/*if(!empty($msearchrec)) { 
			$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = " where (aa.`trx_no` like '%$msearchrec%' or aa.`drno` like '%$msearchrec%' or bb.`COMP_NAME` like '%$msearchrec%' or cc.`BRNCH_NAME` like '%$msearchrec%' or dd.`VEND_NAME` like '%$msearchrec%') AND aa.flag != '$__flag' {$str_vwrecs}";
		}
		if(empty($msearchrec)) {
			$str_optn = " where aa.flag != '$__flag' {$str_vwrecs}";
		}*/ 
		/*
		ee.*,
		ff.ART_CODE,
		ff.ART_DESC,
		aa.`recid` __arid,
		bb.`recid` __brid,
		cc.`recid` __crid,
		dd.`recid` __drid,
		ff.`recid` __frid,
		*/
		
	}//endfunc
	public function _rcv_file_claims(){
		$cuser   = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cuserrema = $this->mylibz->mysys_userrema();
		$trns_rflag		= $this->dbx->escape_str(trim($this->input->get_post('trns_rflag')));
		$fld_txttrx_no	= $this->dbx->escape_str(trim($this->input->get_post('fld_txttrx_no')));
		$trxno_id	= $this->dbx->escape_str(trim($this->input->get_post('_hdrid_mtkn')));
		$madata	= $this->input->get_post('adata1');
		$madata = explode(',x|', $madata);


		//for branch only
		if($cuserrema == 'B'):
		$_trns_reupload = true;
		$count_uploaded_files   = 0;
		if($fld_txttrx_no != ''  || $_trns_reupload === 'true'){
			$count_uploaded_files   = count( $_FILES['images']['name'] );
		}
		$files = $_FILES;
		$image_ofile = "";
		$emp_img_path = './uploads/rcv_claims/' ;
		$emp_img_upath = './uploads/rcv_claims/';
		$config['upload_path'] = $emp_img_upath;
		$config['allowed_types'] = 'jpg|jpeg|png|gif|application/pdf|pdf';
		$config['overwrite'] = TRUE;
		$config['encrypt_name'] = TRUE; // to give unique filename
		$this->load->library('upload', $config);
		if($fld_txttrx_no != '' || $_trns_reupload === 'true' ){
			if($count_uploaded_files == 0 ){
				echo "Please select file to upload.";
				die();
			}
			else{
				$this->mymelibz->file_type_check($_FILES['images']['type'],$count_uploaded_files);
				
			}
		}
		endif;
	

		//get hd
		$str = "
		SELECT 
		recid,
		is_verified,
		encd_date	
		FROM
		{$this->db_erp}.`trx_manrecs_hd` a
		WHERE
		sha2(concat(a.`recid`,'{$mpw_tkn}'),384) = '{$trxno_id}'
		limit 1
		";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$valid_id = '';
		if($q->num_rows() > 0){
			$r = $q->row_array();
			$trx_recid  = $r['recid'];
			$is_verified  = $r['is_verified'];
			$encd_date  = $r['encd_date'];
			
			//if($_trns_reupload === 'true'):
			// 	$str_del_files = "DELETE FROM {$this->db_erp}.`trx_ap_trns_hd_files` WHERE `ctrlno_hd`='$trx_no' AND trx ='{$trans_type}';";
			// 	$this->mylibz->myoa_sql_exec($str_del_files,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			// //endif;
			if($cuserrema == 'B'):
			if($fld_txttrx_no != '' || $_trns_reupload === 'true' ){
				//saving files when edit
				if($count_uploaded_files>0 ){
					for( $i = 0; $i < $count_uploaded_files; $i++ )
					{
						$_FILES['images']['name']     = $files['images']['name'][$i];
						$_FILES['images']['type']     = $files['images']['type'][$i];
						$_FILES['images']['tmp_name'] = $files['images']['tmp_name'][$i];
						$_FILES['images']['error']    = $files['images']['error'][$i];
						$_FILES['images']['size']     = $files['images']['size'][$i];
						if(!is_dir($emp_img_path)) mkdir($emp_img_path, '0755', true);
						$this->load->library('upload', $config);
						if($this->upload->do_upload('images'))
						{
							$data = $this->upload->data();
							$__rfp_filename = $data['file_name'];
							if($this->mymelibz->file_type_image_only($data['file_type']))
							{
								$this->mymelibz->resizeImage($__rfp_filename,$data['full_path']);
							}
						}
					}
				}
			}//savong files end
		endif;


			//update claim dt
			if(count($madata) > 0){ 
				for($bb = 0; $bb < count($madata); $bb++){
				$adata         = explode("x|x",$madata[$bb]);
				$fld_claimsqty = $this->dbx->escape_str($adata[1]);
				$fld_mndt_rid  = $this->dbx->escape_str($adata[2]);
				$fld_actual_qty = $this->dbx->escape_str($adata[3]);
				$fld_olt_tag    = $this->dbx->escape_str($adata[4]);
				
				$str = "SELECT recid FROM {$this->db_erp}.trx_manrecs_dt WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$fld_mndt_rid'";
				$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				if($q->num_rows() > 0) {
					$row = $q->row_array();
					$mdt_recid = $row['recid'];
					$str ="UPDATE {$this->db_erp}.`trx_manrecs_dt` aa
					SET aa.`qty_claim`='{$fld_claimsqty}',
						aa.`qty_corrected`='{$fld_actual_qty}',
						aa.`OLT_tag` = '$fld_olt_tag'
					WHERE aa.`recid` = '$mdt_recid' "; 
					$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				} 
				}
			}

			if($cuserrema == 'B'):
				$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
				SET aa.`claim_tag`='Y',
				aa.`claim_date`= now(),
				aa.`claim_rcpt` = '{$__rfp_filename}'
				WHERE aa.`recid` = '$trx_recid' "; 

				$arrfield = '';
				$arrfield .= "claim_tag" . "->" . "Y" . "\n";
				$arrfield .= "claim_date" . "->" .$this->mylibz->mydatetimedb(). "\n";
				$arrfield .= "claim_rcpt" . "->" . $__rfp_filename . "\n";
				$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FILE_CLAIM_RECEIVING','R');
			else:
				if($encd_date >= '2022-10-29'){
					if($is_verified == 'Y'):
						$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
						SET
						aa.`post_tag` = 'Y',
						aa.`df_tag` ='F',
						aa.`final_date` = now()  
						WHERE aa.`recid` = '$trx_recid' AND aa.`claim_tag`='Y' AND aa.`is_verified` = 'Y' "; 

						$arrfield = '';
						$arrfield .= "post_tag" . "->" . "Y" . "\n";
						$arrfield .= "df_tag" . "->" . "F" . "\n";
						$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FINAL_RECEIVING_IMD','R');
					else:
						echo "<div class=\"alert alert-danger\">Transaction not yet verfied.</div>";
						die();
					endif;
				}
				else{
					$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
					SET
					aa.`post_tag` = 'Y',
					aa.`df_tag` ='F',
					aa.`final_date` = now()  
					WHERE aa.`recid` = '$trx_recid' AND aa.`claim_tag`='Y'"; 

					$arrfield = '';
					$arrfield .= "post_tag" . "->" . "Y" . "\n";
					$arrfield .= "df_tag" . "->" . "F" . "\n";
					$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FINAL_RECEIVING','R');
				}
				

			endif;

			$q3 = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			$this->mylibz->user_logs_activity_module($this->db_erp,'FILE_CLAIM',$fld_txttrx_no,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success\"><strong>SAVE</strong><br> Claims successfully filed!</div>
			<script type=\"text/javascript\"> 
			function __po_refresh_data() { 
				try { 

					$('#mbtn_mn_Claim').prop('disabled',true);
					
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
					__po_refresh_data();
					</script>

					";

		}//select branch end
		else{
			echo "<div class=\"alert alert-success\">Transaction not found.</div>";
		}		

	}
	public function _rcv_file_verify(){
		$cuser   = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cuserrema = $this->mylibz->mysys_userrema();
		$trns_rflag		= $this->dbx->escape_str(trim($this->input->get_post('trns_rflag')));
		$fld_txttrx_no	= $this->dbx->escape_str(trim($this->input->get_post('fld_txttrx_no')));
		$trxno_id	= $this->dbx->escape_str(trim($this->input->get_post('_hdrid_mtkn')));
		$madata	= $this->input->get_post('adata1');
		$madata = explode(',x|', $madata);


		
		//for cad only
		if($cuserrema == 'H'):
		$_trns_reupload_isver = true;
		$count_uploaded_files_isver   = 0;
		if($fld_txttrx_no != ''  || $_trns_reupload_isver === 'true'){
			$count_uploaded_files_isver   = count( $_FILES['images_isver']['name'] );
		}
		$files_isver = $_FILES;
		$image_ofile_isver = "";
		$emp_img_path_isver = './uploads/rcv_claims/' ;
		$emp_img_upath_isver = './uploads/rcv_claims/';
		$config_isver['upload_path'] = $emp_img_upath_isver;
		$config_isver['allowed_types'] = 'jpg|jpeg|png|gif|application/pdf|pdf';
		$config_isver['overwrite'] = TRUE;
		$config_isver['encrypt_name'] = TRUE; // to give unique filename
		$this->load->library('upload', $config_isver);
		if($fld_txttrx_no != '' || $_trns_reupload_isver === 'true' ){
			if($count_uploaded_files_isver == 0 ){
				echo "For CAD: Please select file to upload.";
				die();
			}
			else{
				$this->mymelibz->file_type_check($_FILES['images_isver']['type'],$count_uploaded_files_isver);
				
			}
		}
		endif;


		//get hd
		$str = "
		SELECT 
		recid
		FROM
		{$this->db_erp}.`trx_manrecs_hd` a
		WHERE
		sha2(concat(a.`recid`,'{$mpw_tkn}'),384) = '{$trxno_id}'
		limit 1
		";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$valid_id = '';
		if($q->num_rows() > 0){
			$r = $q->row_array();
			$trx_recid  = $r['recid'];
			//if($_trns_reupload === 'true'):
			// 	$str_del_files = "DELETE FROM {$this->db_erp}.`trx_ap_trns_hd_files` WHERE `ctrlno_hd`='$trx_no' AND trx ='{$trans_type}';";
			// 	$this->mylibz->myoa_sql_exec($str_del_files,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			// //endif;
			
			if($cuserrema == 'H'):
				if($fld_txttrx_no != '' || $_trns_reupload_isver === 'true' ){
					//saving files when edit
					if($count_uploaded_files_isver > 0 ){
						for( $i = 0; $i < $count_uploaded_files_isver; $i++ )
						{
							$_FILES['images_isver']['name']     = $files_isver['images_isver']['name'][$i];
							$_FILES['images_isver']['type']     = $files_isver['images_isver']['type'][$i];
							$_FILES['images_isver']['tmp_name'] = $files_isver['images_isver']['tmp_name'][$i];
							$_FILES['images_isver']['error']    = $files_isver['images_isver']['error'][$i];
							$_FILES['images_isver']['size']     = $files_isver['images_isver']['size'][$i];
							if(!is_dir($emp_img_path_isver)) mkdir($emp_img_path_isver, '0755', true);
							$this->load->library('upload', $config_isver);
							if($this->upload->do_upload('images_isver'))
							{
								$data_isver = $this->upload->data();
								$__rfp_filename_isver = $data_isver['file_name'];
								if($this->mymelibz->file_type_image_only($data_isver['file_type']))
								{
									$this->mymelibz->resizeImage($__rfp_filename_isver,$data_isver['full_path']);
								}
							}
						}
					}
				}//savong files end
			endif;


			if($cuserrema == 'H'):
				$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
				SET aa.`is_verified`='Y',
				aa.`verified_date`= now(),
				aa.`verified_rcpt` = '{$__rfp_filename_isver}'
				WHERE aa.`recid` = '$trx_recid' 
				AND aa.`is_verified`='N'"; 

				$arrfield = '';
				$arrfield .= "is_verified" . "->" . "Y" . "\n";
				$arrfield .= "verified_date" . "->" .$this->mylibz->mydatetimedb(). "\n";
				$arrfield .= "verified_rcpt" . "->" . $__rfp_filename_isver . "\n";
				$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FILE_VERIFY_RECEIVING','R');
			endif;

			$q3 = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			$this->mylibz->user_logs_activity_module($this->db_erp,'FILE_VERIFY',$fld_txttrx_no,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success\"><strong>SAVE</strong><br> Verify successfully!</div>
			<script type=\"text/javascript\"> 
			function __isver_refresh_data() { 
				try { 

					$('#mbtn_mn_Verify').prop('disabled',true);
					
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
					__isver_refresh_data();
					</script>

					";

		}//select branch end
		else{
			echo "<div class=\"alert alert-success\">Transaction not found.</div>";
		}		

	}

public function _rcv_claims_dash(){
	$cuser          = $this->mylibz->mysys_user();
	$mpw_tkn        = $this->mylibz->mpw_tkn();
	$fld_d2dtfrm    = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_d2dtfrm')); 
	$fld_d2dtto     = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_d2dtto')); 
	$fld_d2brnch    = $this->input->get_post('fld_d2brnch');
	$fld_itmgrparea_s = $this->input->get_post('fld_brancharea');
	
	$rcvng            = '';
	$noclaims         = '';
	$claimsvalidation = '';
	$finalclaims      = '';
	$validatedclaims  = '';
	$forverify  	  = '';
	$optn_branch      = "";
	$str_branchgrparea = '';

	$defDate = $this->getfistAndLastday();
	$firstdate = $defDate['firstday'];
	$str_date = "AND date(aa.`encd_date`) >='{$firstdate}' AND date(aa.`encd_date`) <= NOW()";

	//get_branch
	if(!empty($fld_d2brnch)):
	$branhData = $this->mymelibz->getCompanyBranch_data_byname($fld_d2brnch);
	$optn_branch = "AND (aa.`branch_id`) = ". $branhData['recid'];
	endif;
	


	//AREA AND GROUP
	if(!empty($fld_itmgrparea_s)){
		$str_branchgrparea = " AND (cc.`BRNCH_GROUP` ='$fld_itmgrparea_s')";
		//END BRANCH
	}

	//DASH 3 OPTIONS DATE RANGE
	if((!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) && (($fld_d2dtfrm != '--') && ($fld_d2dtto != '--'))){
		//$msearchrec = $this->dbx->escape_str($msearchrec);
		$str_date = "AND (date(aa.`encd_date`) >= '{$fld_d2dtfrm}' AND  date(aa.`encd_date`) <= '{$fld_d2dtto}')";
	}
//AND (DATEDIFF(CURDATE(),aa.`encd_date`) <=7) remove oct272022 new claims revised
	//START DATE NG CAD VERIFICATION
	$claims_revised_date = '2022-10-29';
	$str ="SELECT
        xx.`rcvng`,
        yy.`noclaims`,
        bb.`claimsvalidation`,
        zz.`finalclaims`,
    	aa.`validatedclaims`,
	    cc.`forverify`
        FROM
        ((SELECT COUNT(aa.`recid`) rcvng FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') {$optn_branch} {$str_date} {$str_branchgrparea} ) xx,

         (SELECT COUNT(aa.`recid`) noclaims,DATEDIFF(CURDATE(),aa.`encd_date`) lessSeven  FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') AND (aa.`claim_tag` = 'N') {$optn_branch}  {$str_date} {$str_branchgrparea} ) yy,

         (SELECT COUNT(aa.`recid`) claimsvalidation FROM {$this->db_erp}.`trx_manrecs_hd`aa  JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)  WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) bb,

         (SELECT COUNT(aa.`recid`) finalclaims FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= '$claims_revised_date',aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) zz,

 		 (SELECT COUNT(aa.`recid`) validatedclaims FROM {$this->db_erp}.`trx_manrecs_hd` aa  JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)  WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') AND (aa.`claim_tag` = 'Y')  AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') {$optn_branch} {$str_date} {$str_branchgrparea}) aa,

 		  (SELECT COUNT(aa.`recid`) forverify FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) cc
        )";
       
        $str_q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
        if($str_q->num_rows() > 0 ):
        	$row = $str_q->row_array();
			$rcvng            = $row['rcvng'];
			$noclaims         = $row['noclaims'];
			$claimsvalidation = $row['claimsvalidation'];
			$finalclaims      = $row['finalclaims'];
			$validatedclaims  = $row['validatedclaims'];
			$forverify  	  = $row['forverify'];
        endif;



	echo "<script type=\"text/javascript\"> 
		function __me_refresh_data() { 
			try { 

				$('#rcvng').html('{$rcvng}');
				$('#noclaims').html('{$noclaims}');
					$('#forvalclaims').html('{$claimsvalidation}');
				$('#finalclaims').html('{$finalclaims}');
				$('#validatedclaims').html('{$validatedclaims}');
				$('#forverify').html('{$forverify}');
				
			} 
			catch(err){ 
				var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
				__me_refresh_data();
				</script>";


}

public function _rcv_claims_dash_recs($npages = 1,$npagelimit = 20){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$fld_d2dtfrm    = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_d2dtfrm')); 
		$fld_d2dtto     = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_d2dtto')); 
		$fld_d2brnch    = $this->input->get_post('fld_d2brnch');
		$fld_itmgrparea_s = $this->input->get_post('fld_brancharea');
		
		$report    = $this->input->get_post('report');

		$optn_rpt          = '';
		$optn_branch       = "";
		$optn_order        = "	ORDER BY encd_date ASC";
		$str_branchgrparea = "";
		$defDate   = $this->getfistAndLastday();
		$firstdate = $defDate['firstday'];
		$str_date  = "AND date(aa.`encd_date`) >='{$firstdate}' AND date(aa.`encd_date`) <= NOW()";

		//get_branch
		if(!empty($fld_d2brnch)):
		$branhData = $this->mymelibz->getCompanyBranch_data_byname($fld_d2brnch);
		$optn_branch = "AND (aa.`branch_id`) = ". $branhData['recid'];
		endif;



		//AREA AND GROUP
		if(!empty($fld_itmgrparea_s)){
			$str_branchgrparea = " AND (cc.`BRNCH_GROUP` ='$fld_itmgrparea_s')";
			//END BRANCH
		}

		//START DATE NG CAD VERIFICATION
		$claims_revised_date = '2022-10-29';
		//DASH 3 OPTIONS DATE RANGE
		if((!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) && (($fld_d2dtfrm != '--') && ($fld_d2dtto != '--'))){
			//$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_date = "AND (date(aa.`encd_date`) >= '{$fld_d2dtfrm}' AND  date(aa.`encd_date`) <= '{$fld_d2dtto}')";
		}
		if(!empty($report)):
			switch ($report) {
				case '2':
				$optn_rpt = "AND (aa.`claim_tag` = 'N')"; //AND (DATEDIFF(CURDATE(),aa.`encd_date`) <=7) 
				break;
				case '3':
				$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= '$claims_revised_date',aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR CORRECTED oR FOR FINAL RCV
				break;
				case '4':
				$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')";//CORRECTED
				break;
				case '6':
					$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND  (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')"; // FOR VERIFY CAD// FOR VERIFY CAD
					break;
				case '5':
				$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR VALIDATED
				$optn_order = "	ORDER BY claim_date ASC";	
				break;
				default:
					$optn_rpt = '';
				break;
			}
		endif;

		$strqry = "
		SELECT aa.*,
		bb.`COMP_NAME`,
		cc.`BRNCH_NAME`,
		dd.`VEND_NAME`,
		DATEDIFF(CURDATE(),aa.`claim_date`) claimdateCount,
		sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_arttr 
		FROM {$this->db_erp}.`trx_manrecs_hd` aa
		JOIN {$this->db_erp}.`mst_company` bb
		ON (aa.`comp_id` = bb.`recid`)
		JOIN {$this->db_erp}.`mst_companyBranch` cc
		ON (aa.`branch_id` = cc.`recid`)
		JOIN {$this->db_erp}.`mst_vendor` dd
		ON (aa.`supplier_id` = dd.`recid`)
		WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')
		{$optn_branch} {$str_date} {$optn_rpt} {$str_branchgrparea}
		";
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->row_array();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa {$optn_order} limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->num_rows() > 0) { 
			$data['rlist'] = $qry->result_array();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
}
public function _rcv_claims_dash_rpt(){
		$cuser     = $this->mylibz->mysys_user();
		$mpw_tkn   = $this->mylibz->mpw_tkn();
		$cuserrema = $this->mylibz->mysys_userrema();
		$fld_d2dtfrm    = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_d2dtfrm')); 
		$fld_d2dtto     = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_d2dtto')); 
		$fld_d2brnch    = $this->input->get_post('fld_d2brnch');
		$report    = $this->input->get_post('report');
		$fld_itmgrparea_s = $this->input->get_post('fld_brancharea');

		$chtmlhd   = "";
		$chtmljs   = "";
		$chtml     = "";
		$cmsexp    = "";
		$cmsgt     = "";
		$chtml2    = "";
		$cmsft     = "";
		$monthName = "";
		$str_ctrlno    ='';
		$str_branch = '';
		$str_branch_rcvr = '';
		$trxtype = '_logs';
		$str_branchgrparea = '';
		
		$optn_rpt = '';
		$optn_branch = "";
		$optn_order = "	ORDER BY encd_date ASC";

		$defDate = $this->getfistAndLastday();
		$firstdate = $defDate['firstday'];
		$str_date = "AND date(aa.`encd_date`) >='{$firstdate}' AND date(aa.`encd_date`) <= NOW()";
		//get_branch
		if(!empty($fld_d2brnch)):
		$branhData = $this->mymelibz->getCompanyBranch_data_byname($fld_d2brnch);
		$optn_branch = "AND (aa.`branch_id`) = ". $branhData['recid'];
		endif;
		
		//AREA AND GROUP
		if(!empty($fld_itmgrparea_s)){
			$str_branchgrparea = " AND (cc.`BRNCH_GROUP` ='$fld_itmgrparea_s')";
			//END BRANCH
		}

		//START DATE NG CAD VERIFICATION
		$claims_revised_date = '2022-10-29';
		//DASH 3 OPTIONS DATE RANGE
		if((!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) && (($fld_d2dtfrm != '--') && ($fld_d2dtto != '--'))){
			//$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_date = "AND (date(aa.`encd_date`) >= '{$fld_d2dtfrm}' AND  date(aa.`encd_date`) <= '{$fld_d2dtto}')";
		}
		if(!empty($report)):
			switch ($report) {
				case '2':
				$optn_rpt = "AND (aa.`claim_tag` = 'N')"; //AND (DATEDIFF(CURDATE(),aa.`encd_date`) <=7) 
				break;
				case '3':
				$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= '$claims_revised_date',aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR CORRECTED oR FOR FINAL RCV
				break;
				case '4':
				$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')";//CORRECTED
				break;
				case '6':
					$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND  (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')"; // FOR VERIFY CAD// FOR VERIFY CAD
					break;
				case '5':
				$optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR VALIDATED
				$optn_order = "	ORDER BY claim_date ASC";	
				break;
				default:
					$optn_rpt = '';
				break;
			}
		endif;

	    $chtml = "
			<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
			<head>
			<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
			</head>
			<body>
			<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_ssd\">

			<tr class=\"header-tr\">
			<th class=\"noborder\" colspan=\"22\">CLAIMS REPORT</th>
			</tr>
			<tr class=\"header-tr\">
			<th class=\"noborder\" colspan=\"22\"></th>
			</tr>
			<tr class=\"header-tr-addr\">
			<th class=\"noborder\" colspan=\"22\">&nbsp;</th>
			</tr>
			<tr class =\"header-theme-purple text-white\">
			<th class=\"noborder\">NO</th>
			<th class=\"noborder\">Transaction No</th>
			<th class=\"noborder\">Company</th>
			<th class=\"noborder\">Area Code</th>
			<th class=\"noborder\">Supplier</th>
			<th class=\"noborder\">DR Qty</th>
			<th class=\"noborder\">Total DR Cost</th>
			<th class=\"noborder\">Total DR SRP</th>
			<th class=\"noborder\">DR No</th>
			<th class=\"noborder\">DR Date</th>
			<th class=\"noborder\">Received Date</th>
			<th class=\"noborder\">Date In</th>
			<th class=\"noborder\">Claim Date</th>
			<th class=\"noborder\">Validated  Date</th>
			<th class=\"noborder\">Verified  Date</th>
			<th class=\"noborder\">Final Date</th>
			<th class=\"noborder\">User</th>
			<th class=\"noborder\">S/M Flag</th>
			<th class=\"noborder\">D/F Tag</th>
			<th class=\"noborder\">Y/N Posted</th>
			<th class=\"noborder\">Claim Tag</th>
			<th class=\"noborder\">Remarks</th>
			<th class=\"noborder\">Encoded Date</th>	

			</tr>";


				$strqry = "
				SELECT aa.*,
				bb.`COMP_NAME`,
				cc.`BRNCH_NAME`,
				dd.`VEND_NAME`,
				sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_arttr 
				FROM {$this->db_erp}.`trx_manrecs_hd` aa
				JOIN {$this->db_erp}.`mst_company` bb
				ON (aa.`comp_id` = bb.`recid`)
				JOIN {$this->db_erp}.`mst_companyBranch` cc
				ON (aa.`branch_id` = cc.`recid`)
				JOIN {$this->db_erp}.`mst_vendor` dd
				ON (aa.`supplier_id` = dd.`recid`)
				WHERE aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')
				{$optn_branch} {$str_date} {$optn_rpt} {$str_branchgrparea}
				{$optn_order}
				";
				$q = $this->mylibz->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$res=1;
				if($q->num_rows() > 0) { 
					//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
					$mpathdn   = _XMYAPP_PATH_; 
					$mpathdest = $mpathdn . '/downloads'; 
					$cdate     = date('Ymd');
					$cfiletmp  = 'rcvclaims_report_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
					$cfiledest = $mpathdest . '/' . $cfiletmp;
					$cfilelnk  = site_url() . '/downloads/' . $cfiletmp;
					//SEND TO UALAM
					$this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
					//SECUREW FILES
					if(file_exists($cfiledest)) {
					unlink($cfiledest);
					}
					$fh = fopen($cfiledest, 'w');
					fwrite($fh, $chtml);
					fclose($fh); 
					chmod($cfiledest, 0755);
					$qrw = $q->result_array();
					foreach($qrw as $row):
						$chtml = "<tr class=\"data-nm\">
								       	<td>".$res."</td>
								       	<td>'".$row['trx_no']."</td>
										<td>".$row['COMP_NAME']."</td>
										<td>".$row['BRNCH_NAME']."</td>
										<td>".$row['VEND_NAME']."</td>
										<td>".$row['hd_subtqty']."</td>
										<td>".$row['hd_subtcost']."</td>
										<td>".$row['hd_subtamt']."</td>
										<td>'".$row['drno']."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['dr_date'])."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['rcv_date'])."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['date_in'])."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['claim_date'])."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['validated_date'])."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['verified_date'])."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['final_date'])."</td>
										<td>".$row['muser']."</td>
										<td>".$row['hd_sm_tags']."</td>
										<td>".$row['df_tag']."</td>
										<td>".$row['post_tag']."</td>
										<td>".$row['claim_tag']."</td>
										<td>".$row['hd_remarks']."</td>
										<td>".$this->mylibz->mydate_mmddyyyy($row['encd_date'])."</td>

									</tr>
								   ";
						file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
						$res++;
						endforeach;
				}//end if
				else{
					echo "
						<div class=\"alert alert-danger\" role=\"alert\">
						No Data Found!!!
						</div>				
					";
					die();
				}
				$chtmljs .= "
				<script type=\"text/javascript\">
				function dl_logs_rpt_auto(){
				window.location = '{$cfilelnk}';
				$('#btn_rpt_logs').prop('disabled',true);
				}
				dl_logs_rpt_auto();
				";
				echo $chtmljs;
}
	public function get_crplData($drno){
		$tag = 'N';
		$str = "SELECT
		  `reftag`
		  from trx_crpl
		  WHERE  `crpl_code` = '$drno'";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qry->num_rows() > 0 ):
		 $row = $qry->row_array();
		 $tag = $row['reftag'];
		endif;

		return $tag;
	}

	public function getfistAndLastday(){
		
		$str = "SELECT DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) firstday,LAST_DAY(CURDATE()) lastday";
		$qry = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		return $qry->row_array();
		$qry->free_result();


	}


	public function UpdateStatusAboveseven($recid,$fld_txttrx_no,$type){


		$arrfield = '';
		$arrfield .= "post_tag" . "->" . "Y" . "\n";
		$arrfield .= "df_tag" . "->" . "F" . "\n";
		
		$str = "UPDATE
	  	{$this->db_erp}.`trx_manrecs_hd`
		SET
		`post_tag` = 'Y',
		`df_tag` ='F',
		`final_date` = now()  
		WHERE `recid` = '{$recid}' AND date(`encd_date`) >= '2021-11-02' ";

		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,'AUTO_SYS',$fld_txttrx_no,'U','UPD_RECEIVING_AUTOPOST_'.$type,'R');


	}

	public function auto_update_sys_dash(){
		$arrfield = '';
		$arrfield .= "post_tag" . "->" . "Y" . "\n";
		$arrfield .= "df_tag" . "->" . "F" . "\n";
		$db = $this->db_erp;

	//seven
	$val = "SELECT `trx_no`
	     	FROM $this->db_erp.`trx_manrecs_hd` aa
	     	WHERE DATE(`encd_date`) >= '2021-11-02' AND claim_tag = 'N'  AND (df_tag != 'F' OR post_tag != 'Y') 
	    	AND aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')    
	    	AND DATEDIFF(CURDATE(),aa.`encd_date`)  > 7";
	$q_v = $this->mylibz->myoa_sql_exec($val,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

	if($q_v->num_rows() > 0):
		$str = "INSERT INTO $this->db_erp.`trx_manrecs_rcv_ulogs` (trx_no,data_updated,u_tag,u_module,u_desc,muser,encd_date)
		    	SELECT `trx_no`,'{$arrfield}','U','R','UPD_RECEIVING_AUTOPOST_7D','AUTO_SYS',NOW()
		     	FROM $this->db_erp.`trx_manrecs_hd` aa
		     	WHERE DATE(`encd_date`) >= '2021-11-02' AND claim_tag = 'N'  AND (df_tag != 'F' OR post_tag != 'Y') 
		    	AND aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')    
		    	AND DATEDIFF(CURDATE(),aa.`encd_date`)  > 7 ";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$str_u ="UPDATE $this->db_erp.`trx_manrecs_hd` aa 
		   		SET
		  		aa.`post_tag` = 'Y', 
		   		aa.`df_tag` ='F',
		   		aa.`final_date` = now()      
		    	WHERE  DATE(`encd_date`) >= '2021-11-02' AND claim_tag = 'N'  AND (df_tag != 'F' OR post_tag != 'Y') 
		    	AND aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')    
		    	AND DATEDIFF(CURDATE(),aa.`encd_date`)  > 7";
		$this->mylibz->myoa_sql_exec($str_u,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	endif;
	$q_v->free_result();

	//thirteen
	$val = "SELECT `trx_no`
	     	FROM $this->db_erp.`trx_manrecs_hd` aa
	     	WHERE DATE(`encd_date`) >= '2021-11-02' AND claim_tag = 'Y'  AND (df_tag != 'F' OR post_tag != 'Y') 
	    	AND aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')    
	    	AND DATEDIFF(CURDATE(),aa.`claim_date`)  > 13 ";
	$q_v = $this->mylibz->myoa_sql_exec($val,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

	if($q_v->num_rows() > 0):	
		$str = "INSERT INTO $this->db_erp.`trx_manrecs_rcv_ulogs` (trx_no,data_updated,u_tag,u_module,u_desc,muser,encd_date)
		    	SELECT `trx_no`,'{$arrfield}','U','R','UPD_RECEIVING_AUTOPOST_13D','AUTO_SYS',NOW()
		     	FROM $this->db_erp.`trx_manrecs_hd` aa
		     	WHERE DATE(`encd_date`) >= '2021-11-02' AND claim_tag = 'Y'  AND (df_tag != 'F' OR post_tag != 'Y') 
		    	AND aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')    
		    	AND DATEDIFF(CURDATE(),aa.`claim_date`)  > 13 ";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$str_u ="UPDATE $this->db_erp.`trx_manrecs_hd` aa 
		   		SET
		  		aa.`post_tag` = 'Y', 
		   		aa.`df_tag` ='F',
		   		aa.`final_date` = now()   
		    	WHERE  DATE(`encd_date`) >= '2021-11-02' AND claim_tag = 'Y'  AND (df_tag != 'F' OR post_tag != 'Y') 
		    	AND aa.`flag` = 'R' AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773')    
		    	AND DATEDIFF(CURDATE(),aa.`claim_date`)  > 13";
		$this->mylibz->myoa_sql_exec($str_u,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	endif;
	$q_v->free_result();
	}

	public function _rcv_claims_validate(){
		$cuser     = $this->mylibz->mysys_user();
		$mpw_tkn   = $this->mylibz->mpw_tkn();
		$mtkn_rid    = $this->input->get_post('fld_mktn');
		$trxno = $this->input->get_post('trxno');

		$arrfield = '';
		$arrfield .= "is_validated" . "->" . "Y" . "\n";

		$str = "UPDATE
	  	{$this->db_erp}.`trx_manrecs_hd`
		SET
		`is_validated` = 'Y',
		`validated_date` = now()
		WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_rid' ";

		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$trxno,'U','UPD_RECEIVING_VALIDATE','R');
		echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong>Successfully validated the transaction</div>";


	}
	//CAD Oct 27,2022
	public function _rcv_claims_forverify(){
		$cuser     = $this->mylibz->mysys_user();
		$mpw_tkn   = $this->mylibz->mpw_tkn();
		$mtkn_rid    = $this->input->get_post('fld_mktn');
		$trxno = $this->input->get_post('trxno');

		$arrfield = '';
		$arrfield .= "is_verified" . "->" . "Y" . "\n";

		$str = "UPDATE
	  	{$this->db_erp}.`trx_manrecs_hd`
		SET
		`is_verified` = 'Y',
		`verified_date` = now()
		WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_rid' ";

		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$trxno,'U','UPD_RECEIVING_CLAIMS_VERIFY','R');
		echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong>Successfully verified the transaction</div>";


	}

	public function _rcv_claims_validate_dl(){
		$cuser     = $this->mylibz->mysys_user();
		$mpw_tkn   = $this->mylibz->mpw_tkn();
		$mtkn_rid    = $this->input->get_post('fld_mktn');
		$trxno = $this->input->get_post('trxno');
		$btn_id = $this->input->get_post('btn_id');

			$chtmlhd   = "";
			$chtmljs   = "";
			$chtml     = "";
			$cmsexp    = "";
			$cmsgt     = "";
			$chtml2    = "";
			$cmsft     = "";
			$monthName = "";
			$str_ctrlno        ='';
			$str_branch        = '';
			$str_branch_rcvr   = '';
			$trxtype           = '_logs';
			$str_branchgrparea = '';
			
			$optn_rpt    = '';
			$optn_branch = "";
		
		    $chtml = "
				<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
				<head>
				<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
				</head>
				<body>
				<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_ssd\">

				<tr class=\"header-tr\">
				<th class=\"noborder\" colspan=\"14\">CLAIMS VALIDATE REPORT</th>
				</tr>
				<tr class=\"header-tr\">
				<th class=\"noborder\" colspan=\"14\"></th>
				</tr>
				<tr class=\"header-tr-addr\">
				<th class=\"noborder\" colspan=\"14\">&nbsp;</th>
				</tr>
				<tr class =\"header-theme-purple text-white\">
				<th class=\"noborder\">NO</th>
				<th class=\"noborder\">Transaction No</th>
				<th class=\"noborder\">Itemcode</th>
				<th class=\"noborder\">Description</th>
				<th class=\"noborder\">PKG</th>
				<th class=\"noborder\">Unit Cost</th>
				<th class=\"noborder\">Total Cost</th>
				<th class=\"noborder\">SRP</th>
				<th class=\"noborder\">Total SRP </th>
				<th class=\"noborder\">DR Qty</th>
				<th class=\"noborder\">Actual Qty</th>
				<th class=\"noborder\">O/L/T</th>
				<th class=\"noborder\">CLaims</th>
				<th class=\"noborder\">Encoded Date</th>	
				</tr>";

					$strqry = "
					SELECT
		   			a.*,
		            b.`ART_CODE`,
		            b.`ART_DESC`,
		            b.`ART_SKU`,
		            b.`ART_UCOST`,
		            b.`ART_UPRICE`
		            FROM
		            {$this->db_erp}.`trx_manrecs_dt` a
		            LEFT JOIN
		            {$this->db_erp}.`mst_article` b
		            ON
		            a.`mat_rid` = b.`recid`
		            WHERE
		            sha2(concat(a.`mrhd_rid`,'{$mpw_tkn}'),384) = '{$mtkn_rid}'
		            ORDER BY 
		            a.`recid`";

					$q = $this->mylibz->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

					$res=1;
					if($q->num_rows() > 0) { 
						//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
						$mpathdn   = _XMYAPP_PATH_; 
						$mpathdest = $mpathdn . '/downloads'; 
						$cdate     = date('Ymd');
						$cfiletmp  = 'rcvclaimsval_report_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
						$cfiledest = $mpathdest . '/' . $cfiletmp;
						$cfilelnk  = site_url() . '/downloads/' . $cfiletmp;
						//SEND TO UALAM
						$this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_VAL_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						
						//SECUREW FILES
						if(file_exists($cfiledest)) {
						unlink($cfiledest);
						}
						$fh = fopen($cfiledest, 'w');
						fwrite($fh, $chtml);
						fclose($fh); 
						chmod($cfiledest, 0755);
						$qrw = $q->result_array();
						foreach($qrw as $row):
							$chtml = "<tr class=\"data-nm\">
									       	<td>".$res."</td>
									       	<td>'".$row['trx_no']."</td>
											<td>".$row['ART_CODE']."</td>
											<td>".$row['ART_DESC']."</td>
											<td>".$row['ART_SKU']."</td>
											<td>".$row['ucost']."</td> 
											<td>".$row['tcost']."</td> 
											<td>".$row['uprice']."</td>
											<td>".$row['tamt']."</td>
											<td>".$row['qty']."</td>
											<td>".$row['qty_corrected']."</td>
											<td>".$row['OLT_tag']."</td>
											<td>".$row['qty_claim']."</td>
											<td>".$this->mylibz->mydate_mmddyyyy($row['encd'])."</td>

										</tr>
									   ";
							file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
							$res++;
							endforeach;
					}//end if
					else{
						echo "
							<div class=\"alert alert-danger\" role=\"alert\">
							No Data Found!!!
							</div>				
						";
						die();
					}
					$chtmljs .= "
					<script type=\"text/javascript\">
					function dl_logs_rpt_auto(){
					window.location = '{$cfilelnk}';
					$('#{$btn_id}').prop('disabled',true);
					}
					dl_logs_rpt_auto();
					";
					echo $chtmljs;

	}
	public function _rcv_claims_verified_dl(){
		$cuser     = $this->mylibz->mysys_user();
		$mpw_tkn   = $this->mylibz->mpw_tkn();
		$mtkn_rid    = $this->input->get_post('fld_mktn');
		$trxno = $this->input->get_post('trxno');
		$btn_id = $this->input->get_post('btn_id');

			$chtmlhd   = "";
			$chtmljs   = "";
			$chtml     = "";
			$cmsexp    = "";
			$cmsgt     = "";
			$chtml2    = "";
			$cmsft     = "";
			$monthName = "";
			$str_ctrlno    ='';
			$str_branch = '';
			$str_branch_rcvr = '';
			$trxtype = '_logs';
			$str_branchgrparea = '';
			
			$optn_rpt = '';
			$optn_branch = "";
		
		    $chtml = "
				<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
				<head>
				<meta http-equiv=Content-Type content=\"text/csv; charset=utf-8\">
				</head>
				<body>
				<table class=\"table table-sm table-bordered table-hover\" id=\"testTable_ssd\">

				<tr class=\"header-tr\">
				<th class=\"noborder\" colspan=\"14\">CLAIMS VERIFIED REPORT</th>
				</tr>
				<tr class=\"header-tr\">
				<th class=\"noborder\" colspan=\"14\"></th>
				</tr>
				<tr class=\"header-tr-addr\">
				<th class=\"noborder\" colspan=\"14\">&nbsp;</th>
				</tr>
				<tr class =\"header-theme-purple text-white\">
				<th class=\"noborder\">NO</th>
				<th class=\"noborder\">Transaction No</th> 
				<th class=\"noborder\">Itemcode</th>
				<th class=\"noborder\">Description</th>
				<th class=\"noborder\">PKG</th>
				<th class=\"noborder\">Unit Cost</th>
				<th class=\"noborder\">Total Cost</th>
				<th class=\"noborder\">SRP</th>
				<th class=\"noborder\">Total SRP </th>
				<th class=\"noborder\">DR Qty</th>
				<th class=\"noborder\">Actual Qty</th>
				<th class=\"noborder\">O/L/T</th>
				<th class=\"noborder\">CLaims</th>
				<th class=\"noborder\">Encoded Date</th>
				</tr>";

					$strqry = "
					SELECT
		   			a.*,
		            b.`ART_CODE`,
		            b.`ART_DESC`,
		            b.`ART_SKU`,
		            b.`ART_UCOST`,
		            b.`ART_UPRICE`
		            FROM
		            {$this->db_erp}.`trx_manrecs_dt` a
		            LEFT JOIN
		            {$this->db_erp}.`mst_article` b
		            ON
		            a.`mat_rid` = b.`recid`
		            WHERE
		            sha2(concat(a.`mrhd_rid`,'{$mpw_tkn}'),384) = '{$mtkn_rid}'
		            ORDER BY 
		            a.`recid`";

					$q = $this->mylibz->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

					$res=1;
					if($q->num_rows() > 0) { 
						//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
						$mpathdn   = _XMYAPP_PATH_; 
						$mpathdest = $mpathdn . '/downloads'; 
						$cdate     = date('Ymd');
						$cfiletmp  = 'rcvclaimsverify_report_' . $cdate .$this->mylibz->random_string(9) . '.xls' ;
						$cfiledest = $mpathdest . '/' . $cfiletmp;
						$cfilelnk  = site_url() . '/downloads/' . $cfiletmp;
						//SEND TO UALAM
						$this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_VERIFIED_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						
						//SECUREW FILES
						if(file_exists($cfiledest)) {
						unlink($cfiledest);
						}
						$fh = fopen($cfiledest, 'w');
						fwrite($fh, $chtml);
						fclose($fh); 
						chmod($cfiledest, 0755);
						$qrw = $q->result_array();
						foreach($qrw as $row):
							$chtml = "<tr class=\"data-nm\">
									       	<td>".$res."</td>
									       	<td>'".$row['trx_no']."</td>
											<td>".$row['ART_CODE']."</td>
											<td>".$row['ART_DESC']."</td>
											<td>".$row['ART_SKU']."</td>
											<td>".$row['ucost']."</td> 
											<td>".$row['tcost']."</td> 
											<td>".$row['uprice']."</td>
											<td>".$row['tamt']."</td>
											<td>".$row['qty']."</td>
											<td>".$row['qty_corrected']."</td>
											<td>".$row['OLT_tag']."</td>
											<td>".$row['qty_claim']."</td>
											<td>".$this->mylibz->mydate_mmddyyyy($row['encd'])."</td>

										</tr>
									   ";
							file_put_contents ( $cfiledest , $chtml , FILE_APPEND | LOCK_EX ); 
							$res++;
							endforeach;
					}//end if
					else{
						echo "
							<div class=\"alert alert-danger\" role=\"alert\">
							No Data Found!!!
							</div>				
						";
						die();
					}
					$chtmljs .= "
					<script type=\"text/javascript\">
					function dl_logs_rpt_auto(){
					window.location = '{$cfilelnk}';
					$('#{$btn_id}').prop('disabled',true);
					}
					dl_logs_rpt_auto();
					";
					echo $chtmljs;

	}

	
}  //end main class

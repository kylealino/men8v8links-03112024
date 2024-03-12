<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyDatumModel;
use App\Models\MyUserModel;
class MyDRTrxModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->mydatum =  new MyDatumModel();
		$this->myusermod =  new MyUserModel();
		$this->cusergrp = $this->myusermod->mysys_usergrp();
	}	
	
	public function rcv_view_recs($npages = 1,$npagelimit = 30,$msearchrec='',$fld_vw_dteto='',$fld_vw_dtefrm='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		//PARA SA MGA ADMINSITRATOR LANG 
		$__flag="C";
		$str_optn = "";
		$str_date="";
		if((!empty($fld_dl_dteto) && !empty($fld_dl_dtefrom)) && (($fld_dl_dteto != '--') && ($fld_dl_dtefrom != '--'))){
			$str_date="AND (aa.`rcv_date` >= '{$fld_vw_dtefrm}' AND  aa.`rcv_date` <= '{$fld_vw_dteto}')";
		}
		//IF USERGROUP IS EQUAL SA THEN ALL DATA WILL VIEW ELSE PER USER
		$str_vwrecs = "AND aa.`muser` = '$cuser'";
		//ADD THIS FOR BRANCH VIEW ACCESS TRX SAME BRANCH CREATED
		$cuserrema=$this->myusermod->mysys_userrema();
		if($cuserrema ==='B'){
			$aua_branch = $this->myusermod->ua_brnch($this->db_erp,$cuser);
			$str_branch = "aa.`branch_id` = '__MEBRNCH__' ";

			if(count($aua_branch) > 0) { 
				$str_branch = "";
				for($xx = 0; $xx < count($aua_branch); $xx++) { 
					$mbranch = $aua_branch[$xx];
					$str_branch .= "aa.`branch_id` = '$mbranch' or ";
	            } //end for 
	            $str_branch = "(" . substr($str_branch,0,strlen($str_branch) - 3) . ")";
	        }
			$str_vwrecs = "and {$str_branch}";
		}	
		
		//BRANCH VIEW		
		$result_brnch = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='23'","myua_acct");
       	if($result_brnch == 1 && ($this->cusergrp  != 'SA')){
			$aua_branch = $this->myusermod->ua_brnch($this->db_erp,$cuser);
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
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
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
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa order by recid desc limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		$qry->freeResult();
		return $data;
	} 	//end rcv_view_recs
	
	
	public function dr_save() { 
		
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$cuserrema=$this->myusermod->mysys_userrema();
		
		$trxno = $this->request->getVar('trxno_id');
		//$this->mylibzdb->me_escapeString($this->request->getVar('fld_txttrx_no'));//systemgen
		$tfld_Company =  $this->mylibzdb->me_escapeString($this->request->getVar('fld_Company'));//GET id
		$tfld_area_code = $this->mylibzdb->me_escapeString($this->request->getVar('fld_area_code'));//GET id
		$tfld_supplier = $this->mylibzdb->me_escapeString($this->request->getVar('fld_supplier'));//GET id
		
		//this is for branch tag,para sa walang tag default ay final
		$fld_dftag_temp  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_dftag'));
		$fld_dftag_r = (empty($fld_dftag_temp) ? 'F' : $fld_dftag_temp);
		$fld_dftag =(($cuserrema ==='B') ? 'D': $fld_dftag_r);
		
		$fld_drno  = $this->mylibzdb->me_escapeString($this->request->getVar('fld_drno'));
		$fld_drdate = $this->request->getVar('fld_drdate'); 
		$fld_rcvdate = $this->request->getVar('fld_rcvdate');
		$fld_datein = $this->request->getVar('fld_datein');
		
		$fld_somhd_temp = $this->request->getVar('fld_somhd');
		$fld_somhd = (empty($fld_somhd_temp) ? 'D' : $fld_somhd_temp);
		
		$fld_rems = $this->request->getVar('fld_rems');
		$__rfrom = $this->request->getVar('__rfrom');

		$txt_mo_d = substr($fld_drno, 0,3);
		/*$fld_subtqty = $this->mylibzdb->me_escapeString(str_replace(',','',$this->request->getVar('fld_subtqty')));
		$fld_subtcost = $this->mylibzdb->me_escapeString(str_replace(',','',$this->request->getVar('fld_subtcost')));
		$fld_subtamt = $this->mylibzdb->me_escapeString(str_replace(',','',$this->request->getVar('fld_subtamt')));*/
		
		if(empty($trxno)) {
			//DEADLINE
			$str = "SELECT DATE(NOW()) >=  DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))-1 DAY)  
					AND DATE(NOW()) <  DATE(CONCAT(YEAR(NOW()),'-',MONTH(NOW()),'-',`CUTOFF_DATE`)) 
					AND DATE_SUB(LAST_DAY(NOW()  - INTERVAL 1 MONTH ),INTERVAL DAY(LAST_DAY(NOW()  - INTERVAL 1 MONTH ))-1 DAY)  <= DATE('$fld_rcvdate')
					OR( MONTH('$fld_rcvdate') >= MONTH(NOW()))
					AND( YEAR('$fld_rcvdate') >= YEAR(NOW()))
					AS DATE_DEADLINE FROM {$this->db_erp}.`mst_cutoff_date`";
			//var_dump($str);
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'DEADLINE','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->getRowArray();
			$DATE_DEADLINE = $rw['DATE_DEADLINE'];
			
			if($DATE_DEADLINE == 0) { 
				echo "<div class=\"alert alert-warning mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> Unable to save DR Transaction [$fld_drno], you've reached the cut off date in encoding this transaction.</div>";
				die();
			}
			$q->freeResult();
		}

		$adata1 = $this->request->getVar('adata1');
		$adata2 = $this->request->getVar('adata2');
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
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'ACCT_MANR_DATA1','',$cuser,$mdata,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		}
		$mmn_rid = '';
		$fld_txttrx_no = '';
		$fld_Company =  '';
		$fld_area_code = '';
		$fld_supplier = '';
		//Date In must be greater than or equal to Received Date
		if($fld_datein < $fld_rcvdate){
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid Entries</strong> Date In must be greater than or equal to Received Date!!!.</div>";
			die();
        }
        //RCV DATE VALIDATION
        if($fld_rcvdate=='' || $fld_rcvdate=='0000-00-00' || $fld_rcvdate=='0' || $fld_rcvdate=='--'){
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Received Date is required!</div>";
			die();
		}

		 //RCV DATE VALIDATION
        if($fld_drdate=='' || $fld_drdate=='0000-00-00' || $fld_drdate=='0' || $fld_drdate=='--'){
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> DELETE_RECEIVING_DTL Date is required!</div>";
			die();
		}
		
		//COMPANY
		$str = "select recid,COMP_NAME 
		 from {$this->db_erp}.`mst_company` aa where aa.`COMP_NAME` = '$tfld_Company'";
		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_Company = $rw['recid'];
		$q->freeResult();
		//END COMPANY

		//BRANCH
		$str = "select recid,BRNCH_NAME 
		 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$tfld_area_code'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_area_code = $rw['recid'];
		$q->freeResult();
		//END BRANCH
		
		//BRANCH FROM
		if(!empty($__rfrom)){

			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$__rfrom'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Company Data!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$__rfrom = $rw['recid'];
			$q->freeResult();
			//END BRANCH
		}
		
		//VENDOR
		$str = "select recid,VEND_NAME 
		 from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$tfld_supplier'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'VENDOR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($q->getNumRows() == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Supplier Data!!!.</div>";
			die();
		}

		$rw = $q->getRowArray();
		$fld_supplier = $rw['recid'];
		$q->freeResult();
		//END VENDOR
		
		//CHECK IF USER IS ADMINISTARTOR-> ONLY THE ADMINISTRATOR CAN EDIT
		/*if(!empty($trxno)) { 
			if($this->cusergrp != 'SA') { 
				$str = "select aa.muser,aa.trx_no from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.muser ='$cuser' and aa.df_tag ='D'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				if($q->getNumRows() == 0){
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
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Transaction DATA!!!.</div>";
				die();
			}
			$rw = $q->getRowArray();
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
			$q->freeResult();
		} //END CHECK IF VALID PO

		//GENERATE NEW PO CTRL NO
		else { 
			$fld_txttrx_no =  $this->mydatum->get_ctr_new($fld_Company.$fld_area_code,$fld_supplier.$fld_drno,$this->db_erp,'CTRL_NO03');//TRANSACTION NO
		} //end mtkn_potr
		//ITEM
		if(empty($adata1)) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No Item Data!!!.</div>";
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
				//$mat_mtkn = $adata2[$aa];
				$fld_mitemcode = $this->mylibzdb->me_escapeString(trim($medata[0]));
				$fld_mitemdesc = $this->mylibzdb->me_escapeString(trim($medata[1]));
				$fld_mitempkg = $this->mylibzdb->me_escapeString(trim($medata[2]));
				$fld_ucost =(empty(str_replace(',','',$medata[3])) ? 0 : (str_replace(',','',$medata[3]) + 0));
				$fld_mitemtcost = (empty(str_replace(',','',$medata[4])) ? 0 : (str_replace(',','',$medata[4]) + 0));
				$fld_srp =  (empty(str_replace(',','',$medata[5])) ? 0 : (str_replace(',','',$medata[5]) + 0));
				$fld_mitemtamt =(empty(str_replace(',','',$medata[6])) ? 0 : (str_replace(',','',$medata[6]) + 0));
				$fld_mitemqty = (empty(str_replace(',','',$medata[7])) ? 0 : (str_replace(',','',$medata[7]) + 0));
				$fld_mitemqtyc = (empty(str_replace(',','',$medata[8])) ? 0 : (str_replace(',','',$medata[8]) + 0));
				$fld_remks = $this->mylibzdb->me_escapeString(trim($medata[9]));
				$fld_olt = $this->mylibzdb->me_escapeString(trim($medata[10]));
				$fld_som = $this->mylibzdb->me_escapeString(trim($medata[11]));
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
				$fld_subtqty = $this->mylibzdb->me_escapeString(str_replace(',','',$ntqty));
				$fld_subtcost = $this->mylibzdb->me_escapeString(str_replace(',','',$ntcost));
				$fld_subtamt = $this->mylibzdb->me_escapeString(str_replace(',','',$ntamt));
				//$total_pcs = $nconvf*$nqty;
				//$cmat_code = $this->mylibzdb->me_escapeString(trim($medata[0])) . $mktn_plnt_id . $mtkn_wshe_id;
				
				$amatnr = array();
				if(!empty($fld_mitemcode)) { 
					$fld_mitemcode = urldecode($fld_mitemcode);
					$str = "select aa.recid,aa.ART_CODE from {$this->db_erp}.`mst_article` aa where aa.`ART_CODE` = '$fld_mitemcode' ";//sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mat_mtkn' and
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() == 0) { 
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Material Data!!!<br/>[$fld_mitemcode]</div>";
						die();
					} else { 
						//VALIDATION OF ITEMS,QTY,PRICE
						//if(in_array($cmat_code,$ame)) { 
						if(in_array($fld_mitemcode,$ame)) { 
							echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Material Data already exists [$fld_mitemcode]</div>";
							die();
						} else { 
							//if($fld_dftag== 'F' AND ($fld_mitemqty == 0 || $fld_mitemtcost == 0 || $fld_mitemtamt == 0)) { 
							if($cuserlvl != 'S' && $fld_dftag == 'F' && !($fld_supplier == '3') && (($fld_ucost == 0 || $fld_srp == 0) || ($fld_datein == '' || $fld_datein == 0 || $fld_datein=='0000-00-00' || $fld_datein=='yyyy-0m-dd'))) { 
								echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>[Info]{$fld_ucost}-{$fld_srp}<br/></strong><strong>Error</strong> Invalid QTY or Price entries!!! </br>Note: Final Tags required to fill in all fields.</div>";
								die();
							}
							
							if($cuserlvl != 'S' && $fld_somhd == 'D' && !($fld_supplier == '3' || $fld_supplier == '4105') && ($fld_srp <= $fld_ucost)){ // || $fld_supplier != '4105'
                               echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Unit Price must be greater than to Unit Cost [$fld_mitemcode]</div>";
							   die();
                        	}
                        	if($txt_mo_d == "GRO"){
			                	if(empty($fld_expdate)){
			                		echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Expiration Date is required [$fld_mitemcode]</div>";
							   		die();
			                	}
			                }
			                if(!empty($fld_expdate)){
			                	$str = "SELECT DATE('$fld_expdate') <= DATE(NOW())  __IFEXPIRED ";
								$qs = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								$rws = $qs->getRowArray();
								$__IFEXPIRED  = $rws['__IFEXPIRED'];
								if($__IFEXPIRED == 1){
				                	echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Invalid</strong> Expiration Date was expired [$fld_mitemcode]</div>";
							   		die();
			                	}
			                }
                        	
							
						}
						
						$rw = $q->getRowArray();
						$mmat_rid = $rw['recid'];
						//array_push($ame,$cmat_code); 
						array_push($ame,$fld_mitemcode); 
						array_push($adatar1,$medata);
						array_push($adatar2,$mmat_rid);
						/*$ntqty = ($ntqty + $nqty);*/
						//$ntamt = ($ntamt + ($nprice * $nconvf * $nqty));
						//$ntamt = ($ntamt + ($tamt));
					}

					$q->freeResult();
				}

			}  //end for 
		

			//if(count($adatar1) > 0) {
			if(((count($adatar1) == 0) && (!empty($trxno))) || ((count($adatar1) > 0) && ((empty($trxno)) || (!empty($trxno)))  )) {  
				if(!empty($trxno)) { 
					if($txt_mo_d != "GRO"){
						//DR bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
						$str = "select aa.`drno` from {$this->db_erp}.`trx_manrecs_hd` aa where aa.`drno` = '$fld_drno' AND aa.`supplier_id` = '$fld_supplier' AND !(aa.`flag`='C') AND !(sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) = '$trxno')";
						$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0) { 
							echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> DR No already exists.!!!.[".$fld_drno."]</div>";
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
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_UREC'.$cuser,'',$fld_txttrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
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
						$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_HEADER','R');	
					}
					

				} else { 
					if($txt_mo_d != "GRO"){
						//DR bAKA MAGAKATAON NA MAY MAGAKAIBANG SUP NA PAREHAS ANG DR
						$str = "select aa.`drno` from {$this->db_erp}.`trx_manrecs_hd` aa where aa.`drno` = '$fld_drno' AND aa.`supplier_id` = '$fld_supplier' AND !(aa.`flag`='C')";
						$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0) { 
							echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> DR No already exists.!!!.[".$fld_drno."]</div>";
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
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_AREC','',$fld_txttrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$str = "select recid,sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_mntr from {$this->db_erp}.`trx_manrecs_hd` aa where `trx_no` = '$fld_txttrx_no' ";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					$rw = $q->getRowArray();
					$mmn_rid = $rw['recid'];
					//var_dump($mmn_rid);
					$__hmtkn_mntr = $rw['mtkn_mntr'];
					$q->freeResult();
					
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
					
					$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'A','ADD_RECEIVING_HEADER','R');
				}
				
				//GET PLNT, WSHE, SBIN
				for($xx = 0; $xx < count($adatar1); $xx++) {  //MAY MALI DITO
		
					$xdata = $adatar1[$xx];
					$mat_rid = $adatar2[$xx];
					
					//$fld_mitemrid = $this->mylibzdb->me_escapeString(trim($xdata[0]));
					$fld_mitemcode = $xdata[0];
					$fld_mitemdesc = $this->mylibzdb->me_escapeString(trim($xdata[1]));
					$fld_mitempkg = $this->mylibzdb->me_escapeString(trim($xdata[2]));
					$fld_ucost = (empty(str_replace(',','',$xdata[3])) ? 0 : (str_replace(',','',$xdata[3]) + 0));
					$fld_mitemtcost = (empty(str_replace(',','',$xdata[4])) ? 0 : (str_replace(',','',$xdata[4]) + 0));
					$fld_srp =  (empty(str_replace(',','',$xdata[5])) ? 0 : (str_replace(',','',$xdata[5]) + 0));
					$fld_mitemtamt =(empty(str_replace(',','',$xdata[6])) ? 0 : (str_replace(',','',$xdata[6]) + 0));
					$fld_mitemqty = (empty(str_replace(',','',$xdata[7])) ? 0 : (str_replace(',','',$xdata[7]) + 0));
					$fld_mitemqtyc = (empty(str_replace(',','',$xdata[8])) ? 0 : (str_replace(',','',$xdata[8]) + 0));
					$fld_remks = $this->mylibzdb->me_escapeString(trim($xdata[9]));
					$fld_olt = $this->mylibzdb->me_escapeString(trim($xdata[10]));
					$fld_som = $this->mylibzdb->me_escapeString(trim($xdata[11]));
					$mndt_rid = $this->mylibzdb->me_escapeString(trim($xdata[12]));//dt mn id
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
						$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0 ) { 
							$rw = $q->getRowArray();
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
							
							$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_DTLS','R');
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
						$q->freeResult();
						$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
						$this->mylibzdb->user_logs_activity_module($this->db_erp,'TRX_mn_DT','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($cuserlvl == 'S' && (($fld_srp <= $fld_ucost) || ($fld_ucost == 0) || ($fld_srp == 0))){
							$this->mylibzdb->user_logs_override_itemcode($this->db_erp,'ITEM_OVERRIDE','',$cuser,$fld_txttrx_no.' ITEMCODE:'.$fld_mitemcode.'= SRP: '.$fld_srp.'<= COST:'.$fld_ucost,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						}
						
						
						
						
						
					} else { 
						if(empty($mndt_rid)) { 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_dt` where `trx_no` = '$fld_txttrx_no' and `mat_rid` = '$mat_rid'";
							
							$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($q->getNumRows() > 0 ) { 
								$rw = $q->getRowArray();
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
										
										$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_DTLS','R');
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
							$q->freeResult();
							$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
							$this->mylibzdb->user_logs_activity_module($this->db_erp,'trx_po_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($cuserlvl == 'S' && (($fld_srp <= $fld_ucost) || ($fld_ucost == 0) || ($fld_srp == 0))){
								$this->mylibzdb->user_logs_override_itemcode($this->db_erp,'ITEM_OVERRIDE','',$cuser,$fld_txttrx_no.' ITEMCODE:'.$fld_mitemcode.'= SRP: '.$fld_srp.'<= COST:'.$fld_ucost,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							}
							
						} else { // end empty podt_rid 
							$str = "select recid from {$this->db_erp}.`trx_manrecs_dt` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mndt_rid'";
							$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							if($qq->getNumRows() > 0) { 
								$rrw = $qq->getRowArray();
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
								$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
								$this->mylibzdb->user_logs_activity_module($this->db_erp,'trx_mn_dt','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
								if($cuserlvl == 'S' && (($fld_srp <= $fld_ucost) || ($fld_ucost == 0) || ($fld_srp == 0))){
									$this->mylibzdb->user_logs_override_itemcode($this->db_erp,'ITEM_OVERRIDE','',$cuser,$fld_txttrx_no.' ITEMCODE:'.$fld_mitemcode.'= SRP: '.$fld_srp.'<= COST:'.$fld_ucost,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
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
								
								$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield_dtl,$cuser,$fld_txttrx_no,'U','UPDATE_RECEIVING_DTLS','R');
							}
							$qq->freeResult();

							

							
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

					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

					$rw = $q->getRowArray();
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
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__ . chr(13) . chr(10) . 'USER: ' . $cuser);
					$this->mylibzdb->user_logs_activity_module($this->db_erp,'MN_UREC_RECON_HD_QTY','',$fld_txttrx_no,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				}//endif

				
				//record on AV Work Flow
				//$qry->freeResult();
				
				if(empty($trxno)) { 
					echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Recorded Successfully!!!</div>
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
					echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Data Changes Successfully RECORDED!!!</div>
					";
					die();
				}
			} else { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> No VALID Item Data!!!.</div>";
				die();
			} //end if 
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Item Data!!!.</div>";
			die();
		}	
	} //end dr_save
	
	public function get_crplData($drno){
		$tag = 'N';
		$str = "SELECT
		  `reftag`
		  from trx_crpl
		  WHERE  `crpl_code` = '$drno'";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qry->getNumRows() > 0 ):
		 $row = $qry->getRowArray();
		 $tag = $row['reftag'];
		endif;
		$qry->freeResult();
		return $tag;
	} //end get_crplData
	
	public function UpdateStatusAboveseven($recid,$fld_txttrx_no,$type) {
		$arrfield = '';
		$arrfield .= "post_tag" . "->" . "Y" . "\n";
		$arrfield .= "df_tag" . "->" . "F" . "\n";
		
		$str = "UPDATE
	  	{$this->db_erp}.`trx_manrecs_hd`
		SET
		`post_tag` = 'Y',
		`df_tag` ='F',
		`final_date` = now()  
		WHERE `recid` = '{$recid}' AND date(`encd_date`) >= date('2021-11-02') "; 
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,'AUTO_SYS',$fld_txttrx_no,'U','UPD_RECEIVING_AUTOPOST_'.$type,'R');
	}  //end UpdateStatusAboveseven
	
	public function _rcv_file_claims() {
		$cuserrema = $this->myusermod->mysys_userrema();
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$trns_rflag		= $this->mylibzdb->me_escapeString($this->request->getVar('trns_rflag'));
		$fld_txttrx_no	= $this->mylibzdb->me_escapeString($this->request->getVar('fld_txttrx_no'));
		$trxno_id	= $this->mylibzdb->me_escapeString($this->request->getVar('_hdrid_mtkn'));
		$madata	= $this->request->getVar('adata1');
		$madata = explode(',x|', $madata);
		
		$__rfp_filename = '';
		$files = $_FILES;
		$image_ofile = "";
		$emp_img_path = ROOTPATH . 'public/uploads/rcv_claims/';
		$emp_img_upath = 'uploads/rcv_claims/';
		//for branch only
		if($cuserrema == 'B'):
			$_trns_reupload = true;
			$count_uploaded_files   = 0;
			
			if ($imagefile = $this->request->getFiles()) {
				foreach ($imagefile['images'] as $img) {
					if ($img->isValid() && ! $img->hasMoved()) { 
						$newName = $img->getRandomName();
						$__rfp_filename = '';
						if($img->getMimeType() == 'application/pdf') { 
							//echo 'yes-pdf ' . ($img->getSize() / 1024) . '<br/>';
							$__rfp_filename = $img->getName();
						} else { 
							if(!$this->mylibz->valid_file_type_image($img->getMimeType())) { 
								echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please select only <strong>gif/jpg/png </strong> file.</div>";
								die();
							}
							$__rfp_filename = $img->getName();
						}
						if(!empty($__rfp_filename)) {
							//$img->move(WRITEPATH . 'uploads', $newName);
							$img->move($emp_img_path, $__rfp_filename);
						}
					}
				}
			}
			
			if(empty($__rfp_filename)) {
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Document File attachment INVALID...</div>";
				die();
			}
			
			if($fld_txttrx_no != ''  || $_trns_reupload === 'true'){
				$count_uploaded_files   = count( $_FILES['images']['name'] );
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
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$valid_id = '';
		if($q->getNumRows() > 0) { 
			$r = $q->getRowArray();
			$trx_recid  = $r['recid'];
			//update claim dt
			if(count($madata) > 0) { 
				for($bb = 0; $bb < count($madata); $bb++) { 
				$adata         = explode("x|x",$madata[$bb]);
				$fld_claimsqty = $this->mylibzdb->me_escapeString($adata[1]);
				$fld_mndt_rid  = $this->mylibzdb->me_escapeString($adata[2]);
				$fld_actual_qty = $this->mylibzdb->me_escapeString($adata[3]);
				$fld_olt_tag    = $this->mylibzdb->me_escapeString($adata[4]);
				
				$str = "SELECT recid FROM {$this->db_erp}.trx_manrecs_dt WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$fld_mndt_rid'";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
				if($q->getNumRows() > 0) {
					$row = $q->getRowArray();
					$mdt_recid = $row['recid'];
					$str ="UPDATE {$this->db_erp}.`trx_manrecs_dt` aa
					SET aa.`qty_claim`='{$fld_claimsqty}',
						aa.`qty_corrected`='{$fld_actual_qty}',
						aa.`OLT_tag` = '$fld_olt_tag'
					WHERE aa.`recid` = '$mdt_recid' "; 
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
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
				$arrfield .= "claim_date" . "->" .$this->mylibzdb->getdate(). "\n";
				$arrfield .= "claim_rcpt" . "->" . $__rfp_filename . "\n";
				$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FILE_CLAIM_RECEIVING','R');
			else:
				$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
				SET
				aa.`post_tag` = 'Y',
				aa.`df_tag` ='F',
				aa.`final_date` = now()  
				WHERE aa.`recid` = '$trx_recid' AND aa.`claim_tag`='Y' "; 
				$arrfield = '';
				$arrfield .= "post_tag" . "->" . "Y" . "\n";
				$arrfield .= "df_tag" . "->" . "F" . "\n";
				$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FINAL_RECEIVING_CAD','R'); 
			endif;
			$q3 = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'FILE_CLAIM',$fld_txttrx_no,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br> Claims successfully filed!</div>
			<script type=\"text/javascript\"> 
			function __po_refresh_data() { 
				try { 
					jQuery('#mbtn_mn_Claim').prop('disabled',true);
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; 
				} 
			} 
					__po_refresh_data();
			</script>";
		} //select branch end
		else {
			echo "<div class=\"alert alert-success\">Transaction not found.</div>";
		} 
	} //end _rcv_file_claims

	public function dr_claims_verify(){
		$cuserrema = $this->myusermod->mysys_userrema();
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();

		$trns_rflag		= $this->mylibzdb->me_escapeString($this->request->getVar('trns_rflag'));
		$fld_txttrx_no	= $this->mylibzdb->me_escapeString($this->request->getVar('fld_txttrx_no'));
		$trxno_id	= $this->mylibzdb->me_escapeString($this->request->getVar('_hdrid_mtkn'));
		$madata	= $this->request->getVar('adata1');
		$madata = explode(',x|', $madata);

		$count_uploaded_files_isver   = 0;

		if($fld_txttrx_no != ''  || $_trns_reupload === 'true'){
			$count_uploaded_files_isver   = count( $_FILES['images_isver']['name'] );
		}

		$files = $_FILES;
		$image_ofile = "";

		$emp_img_path_isver = './uploads/rcv_claims/' ;
		$emp_img_upath_isver = './uploads/rcv_claims/';
		if($fld_txttrx_no != '' || $_trns_reupload_isver === 'true'){
			if($count_uploaded_files_isver == 0){
				echo "For CAD: Please select file to upload.";
			die();
			}
			else{
				$this->file_type_check_PDF_only($_FILES['images_isver']['type'],$count_uploaded_files_isver);

			}
		}


		//get hd
		$str = "
		SELECT 
		recid
		FROM
		{$this->db_erp}.`trx_manrecs_hd` a
		WHERE
		sha2(concat(a.`recid`,'{$mpw_tkn}'),384) = '{$trxno_id}'
		AND a.`df_tag` = 'D' 
		AND a.`post_tag` = 'N'
		limit 1
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$valid_id = '';
		if($q->getNumRows() > 0){
			$r = $q->getRowArray();
			$trx_recid  = $r['recid'];
			//if($_trns_reupload === 'true'):
			// 	$str_del_files = "DELETE FROM {$this->db_erp}.`trx_ap_trns_hd_files` WHERE `ctrlno_hd`='$trx_no' AND trx ='{$trans_type}';";
			// 	$this->mylibz->myoa_sql_exec($str_del_files,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			// //endif;
			
			if($cuserrema == 'H'):
				if($fld_txttrx_no != '' || $_trns_reupload_isver === 'true' ){

					if($count_uploaded_files_isver>0 ){
				
						if ($mefiles = $this->request->getFiles()) { 
							// approve
							foreach ($mefiles['images_isver'] as $mfile) {
								
								if ($mfile->isValid() && ! $mfile->hasMoved()) { 
									
									$newName = $mfile->getRandomName();
									$__upld_filename = '';
									$itisfilename = $mfile->getName();
									$itisfilename = $this->myusermod->mylibzdb->me_escapeString(str_replace(' ','_',$itisfilename));
				
									$__upld_filename = $cuser . '_' . $itisfilename;
		
									if(!empty($__upld_filename)) { 
										if (file_exists($emp_img_path_isver . $__upld_filename)) { 
											unlink($emp_img_path_isver . $__upld_filename);
										}
										$mfile->move($emp_img_path_isver, $__upld_filename);
										
										$arrfield = array();
										$arrfield[] = "ira_filename" . "xOx'" . $__upld_filename . "'";
		
									}
								}
							} //end foreach 
							
						} //end if  
					}
				}//savong files end
			endif;

			if($cuserrema == 'H'):
				$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
				SET aa.`is_verified`='Y',
				aa.`verified_date`= now(),
				aa.`verified_by`= '$cuser',
				aa.`verified_rcpt` = '{$__upld_filename}'
				WHERE aa.`recid` = '$trx_recid' 
				AND aa.`is_verified`='N'"; 

				$arrfield = '';
				$arrfield .= "is_verified" . "->" . "Y" . "\n";
				$arrfield .= "verified_date" . "->" .$this->mydatetimedb(). "\n";
				$arrfield .= "verified_by" . "->" . $cuser . "\n";
				$arrfield .= "verified_rcpt" . "->" . $__upld_filename . "\n";
				$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FILE_VERIFY_RECEIVING','R');
			endif;

			$q3 = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			$this->mylibzdb->user_logs_activity_module($this->db_erp,'FILE_VERIFY',$fld_txttrx_no,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br> Verify successfully!</div>
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
			echo "<div class=\"alert alert-success\">Transaction not found or already posted.</div>";
		}		

	}

	public function dr_claims_review(){
		$cuserrema = $this->myusermod->mysys_userrema();
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();

		$trns_rflag		= $this->mylibzdb->me_escapeString($this->request->getVar('trns_rflag'));
		$fld_txttrx_no	= $this->mylibzdb->me_escapeString($this->request->getVar('fld_txttrx_no'));
		$trxno_id	= $this->mylibzdb->me_escapeString($this->request->getVar('_hdrid_mtkn'));
		$madata	= $this->request->getVar('adata1');
		$madata = explode(',x|', $madata);

		$count_uploaded_files_isreview   = 0;

		if($fld_txttrx_no != ''  || $_trns_reupload === 'true'){
			$count_uploaded_files_isreview   = count( $_FILES['images_isreview']['name'] );
		}

		$files = $_FILES;
		$image_ofile = "";

		$emp_img_path_isreview = './uploads/rcv_claims/' ;
		if($fld_txttrx_no != '' || $_trns_reupload_isver === 'true'){
			if($count_uploaded_files_isreview == 0){
				echo "For SOD: Please select file to upload.";
			die();
			}
			else{
				$this->file_type_check_PDF_only($_FILES['images_isreview']['type'],$count_uploaded_files_isreview);

			}
		}
		

		//get hd
		$str = "
		SELECT 
		recid
		FROM
		{$this->db_erp}.`trx_manrecs_hd` a
		WHERE
		sha2(concat(a.`recid`,'{$mpw_tkn}'),384) = '{$trxno_id}'
		AND a.`df_tag` = 'D' 
		AND a.`post_tag` = 'N'
		limit 1
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$valid_id = '';
		if($q->getNumRows() > 0){
			$r = $q->getRowArray();
			$trx_recid  = $r['recid'];
			//if($_trns_reupload === 'true'):
			// 	$str_del_files = "DELETE FROM {$this->db_erp}.`trx_ap_trns_hd_files` WHERE `ctrlno_hd`='$trx_no' AND trx ='{$trans_type}';";
			// 	$this->mylibz->myoa_sql_exec($str_del_files,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $cuser);
			// //endif;
			
			if($cuserrema == 'H'):
				if($fld_txttrx_no != '' || $_trns_reupload_isreview === 'true' ){

					if($count_uploaded_files_isreview>0 ){
				
						if ($mefiles = $this->request->getFiles()) { 
							// approve
							foreach ($mefiles['images_isreview'] as $mfile) {
								
								if ($mfile->isValid() && ! $mfile->hasMoved()) { 
									
									$newName = $mfile->getRandomName();
									$__upld_filename = '';
									$itisfilename = $mfile->getName();
									$itisfilename = $this->myusermod->mylibzdb->me_escapeString(str_replace(' ','_',$itisfilename));
				
									$__upld_filename = $cuser . '_' . $itisfilename;
		
									if(!empty($__upld_filename)) { 
										if (file_exists($emp_img_path_isreview . $__upld_filename)) { 
											unlink($emp_img_path_isreview . $__upld_filename);
										}
										$mfile->move($emp_img_path_isreview, $__upld_filename);
										
										$arrfield = array();
										$arrfield[] = "ira_filename" . "xOx'" . $__upld_filename . "'";
		
									}
								}
							} //end foreach 
							
						} //end if  
					}
				}//savong files end
			endif;

			if($cuserrema == 'H'):
				$str ="UPDATE {$this->db_erp}.`trx_manrecs_hd` aa
				SET aa.`is_reviewed`='Y',
				aa.`reviewed_date`= now(),
				aa.`reviewed_by`= '$cuser',
				aa.`reviewed_rcpt` = '{$__upld_filename}'
				WHERE aa.`recid` = '$trx_recid' 
				AND aa.`is_reviewed`='N'"; 

				$arrfield = '';
				$arrfield .= "is_reviewed" . "->" . "Y" . "\n";
				$arrfield .= "reviewed_date" . "->" .$this->mydatetimedb(). "\n";
				$arrfield .= "reviewed_by" . "->" . $cuser . "\n";
				$arrfield .= "reviewed_rcpt" . "->" . $__upld_filename . "\n";
				$this->mylibzdb->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$fld_txttrx_no,'U','FILE_REVIEW_RECEIVING','R');
			endif;

			$q3 = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			$this->mylibzdb->user_logs_activity_module($this->db_erp,'FILE_REVIEW',$fld_txttrx_no,$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br> Review successfully!</div>
			<script type=\"text/javascript\"> 
			function __isreview_refresh_data() { 
				try { 

					$('#mbtn_mn_Review').prop('disabled',true);
					
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
					__isreview_refresh_data();
					</script>

					";

		}//select branch end
		else{
			echo "<div class=\"alert alert-success\">Transaction not found or already posted.</div>";
		}		

	}

	public function file_type_check($file_types,$file_count){
		$allowed_mime_type_arr = array('application/pdf','image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
		for( $j = 0; $j < $file_count; $j++ )
			{ 
				$file_type = $file_types[$j];
				if(!in_array($file_type, $allowed_mime_type_arr)){
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please select only <strong>pdf/gif/jpg/png </strong> file.</div>";
				
					die();
				}
		
		}
	}

	public function file_type_check_PDF_only($file_types,$file_count){
		$allowed_mime_type_arr = array('application/pdf','image/png');
		for( $j = 0; $j < $file_count; $j++ )
		{ 
			$file_type = $file_types[$j];
			if(!in_array($file_type, $allowed_mime_type_arr)){
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please select only <strong>PDF </strong> file.</div>";
				die();
			}

		}
	}
	
	public function mydatetimedb() { 
		$str = "select date_format(now(),'%Y-%m-%d %H:%i:%s') __mdatetime";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRowArray();
		$q->freeResult();
		return $rw['__mdatetime'];
	}
	
} //end main class

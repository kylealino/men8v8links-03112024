<?php
namespace App\Controllers;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;
use App\Models\MyDRTrxModel;

class MyDRTrx extends BaseController
{
	public function __construct()
	{
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->myusermod =  new MyUserModel();
		$this->mydrtrx =  new MyDRTrxModel();
	}
	
	public function index() { 
		
	} //end index
	
	public function rcvrec_vw() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$fld_vw_dteto = (!empty($this->request->getVar('fld_vw_dteto')) ? $this->mylibz->mydate_yyyymmdd($this->request->getVar('fld_vw_dteto')) : '');
		$fld_vw_dtefrm = (!empty($this->request->getVar('fld_vw_dtefrm')) ? $this->mylibz->mydate_yyyymmdd($this->request->getVar('fld_vw_dtefrm')) : '');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 1: ($mpages + 0));
		$data = $this->mydrtrx->rcv_view_recs($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
        echo view('transactions/dr/dr-trx-rcv-recs',$data);        
	}  //end rcvrec_vw
	
	public function dr_trx()
	{
		echo view('templates/meheader01');
		echo view('transactions/dr/dr-trx');
		echo view('templates/mefooter01');
	} //end dr_trx
	
	public function dr_trx_save() { 
		$trxno = $this->request->getVar('trxno_id');
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$str_tag ='';
		
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

		if(!empty($trxno)) { 
			//EDIT ACCESS --RECEIVING EDIT
			$result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='7'","myua_acct");
			if($result == 1){
				//IF USER IS NOT A SUPERADMIN WILL FALL THIS VALIDATION
				if($this->myusermod->mysys_userlvl() != 'S') {
					
					//USER ONLY CAN EDIT THEIR ENTRY WHEN TAG IS DRAFT ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_drft = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='21'","myua_acct");
					
					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_fnal = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='22'","myua_acct");

					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDIT BRNCH 
					$result_brnch = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='23'","myua_acct");
					
					if(!($result_fnal == 1) && ($result_drft == 1) && !($result_brnch == 1)){ //DRAFT
						$str_tag ="and aa.muser ='$cuser' and aa.df_tag ='D'";
					}//endif
					elseif(!($result_drft == 1) && ($result_fnal == 1) && !($result_brnch == 1)){ //FINAL
						$str_tag ="and aa.df_tag ='F'";
					}//endif
					elseif(($result_drft == 1) && ($result_brnch == 1) && !($result_fnal == 1)){  //DRAFT WITH BRANCH DAPAT NAKAON ANG DRAFT at BRCNH
						$str_tag ="and {$str_branch} and aa.df_tag ='D'";
					}//endif
					elseif(($result_fnal == 1) && ($result_drft == 1) && !($result_brnch == 1)){ //DRAFT and FINAL DAPAT NAKAON ANG DRAFT at FINAL
						$str_tag ="";
					}//endif
					else{
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
						die();
					}
					
					$str = "select aa.muser,aa.trx_no from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' {$str_tag}";
					$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->getNumRows() == 0){
						echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
						die();
					}//endif
				} //endif
				//ELSE SUPERADMIN CAN EDIT  FINAL AND DRAFT TAG
				//WHEN TRANSACTIONS IS POSTED IT IS UNEDITABLE
				if($this->myusermod->mysys_userlvl() != 'S') { 
					$str = "select aa.post_tag from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.post_tag ='Y'";
						$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->getNumRows() > 0){
							echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Transactions already posted!!!</br>Note:Posted Transactions is uneditable.</div>";
						die();
					}
				}//endif
				$this->mydrtrx->dr_save();
			}
			else{
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
		}else{
			//ADD SAVE ACCESS
			$result = $this->myusermod->get_Active_menus($this->db_erp,$cuser,"myuaacct_id='6'","myua_acct");
			if($result != 1){
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
			$this->mydrtrx->dr_save();
		}		
	} //end dr_trx_save
	
	public function dr_claims_save() { 
		$this->mydrtrx->_rcv_file_claims();
	} //end dr_claims_save


}  //end main class MyDRTrx

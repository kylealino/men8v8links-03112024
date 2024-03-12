<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mytrx_acct extends CI_Controller {
	
	public function __construct()
	{ 
		parent::__construct();
		
		$this->dbx = $this->load->database('default',TRUE);
		$this->db_erp = $this->config->item('__mydberp');
		$this->db_temp = $this->config->item('__mdbtemp');
		$this->db_lnks = $this->config->item('__mydblnks');
		$this->load->model('mylibsys_model','mylibz');
		if ($this->mylibz->msys_is_logged() == FALSE):
			redirect('mylogin');
		endif;
		$this->load->model('mymd_article_model','mymdartm');
		$this->load->model('mymd_prodline_model','mymdprdl');
		$this->load->model('mymd_acct_manrecs_model','mymdacct');//RCVNG
		$this->load->model('mymd_acct_manrecs_gro_model','mymdacctgro');//GROCERY
		$this->load->model('mymd_acct_manrecs_d1_model','mymdacct_dash1');//RCVNG
		$this->load->model('mymd_acct_manrecs_po_model','mymdacctpo');//PO
		$this->load->model('mymd_acct_manrecs_po_gro_model','mymdacctpogro');//PO GROCERY
		$this->load->model('mymd_acct_manrecs_po_d1_model','mymdacctpo_dash1');//PO
		$this->load->model('mymd_acct_manrecs_trans_model','mymdaccttrans');//TRANS
		$this->load->model('mymd_acct_manrecs_inv_model','mymdacctinv');//INV
		$this->load->model('mymd_acct_manrecs_lb_model','mymdacctlb');//LB
		$this->load->model('mymd_acct_manrecs_drupld_model','mymdacctdru');//DRUPLD
		$this->load->model('mymd_acct_manrecs_cyc_model','mymdacctcyc');//INV
		$this->load->model('mydatum_model','mydataz');
		$this->load->model('mymd_vend_model','mymdvend');
		$this->load->model('mymd_comp_model','mymdcomp');
		$this->load->model('mymd_userm_model','mymduserm');
		$this->load->model('mytrx_warehouse_model','warehouse');
		$this->load->model('mytrx_rcvng_model','myrcvng');
  		$this->load->model('MyAP_model','myap');  		
  		$this->load->model('MyRFP_model','myrfp');
  		$this->load->model('mydata_ua_model','mydatazua');
  		$this->load->model('warehouse_model','wshe');
  		$this->load->model('mymelibsys_model','mymelibz');
		$this->sysuaid = $this->mylibz->mysys_user();
		$this->cusergrp = $this->mylibz->mysys_usergrp();
		$this->cuserlvl = $this->mylibz->mysys_userlvl();
		
	}
	//UNAUTHORIZED
	public function unathorized_vw() { 
		$this->load->view('unauthorized');
	}
	//FOR RECEIVING
	public function acct_man_recs_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY
	public function acct_man_recs_gro_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_gro_manrecs');
		}
		else{
			$this->load->view('unauthorized');
		}
	}
	//posting
	public function acct_man_recs_posting() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='8'","myua_acct");
		if($result != 1){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
			die();
		}
		$this->mymdacct->rcv_posting();
	}
	//saving new data
	public function man_recs_sv() { 
		$trxno = $this->input->get_post('trxno_id');
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$str_tag ='';
		
		$aua_branch = $this->mydatazua->ua_brnch($this->db_erp,$cuser);
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
			$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='7'","myua_acct");
			if($result == 1){
				//IF USER IS NOT A SUPERADMIN WILL FALL THIS VALIDATION
				if($this->cuserlvl != 'S') {
					
					//USER ONLY CAN EDIT THEIR ENTRY WHEN TAG IS DRAFT ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_drft = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='21'","myua_acct");
					
					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_fnal = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='22'","myua_acct");

					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDIT BRNCH 
					$result_brnch = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='23'","myua_acct");
					
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
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
						die();
					}
					
					$str = "select aa.muser,aa.trx_no from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' {$str_tag}";
					$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->num_rows() == 0){
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
						die();
					}//endif
				} //endif
				//ELSE SUPERADMIN CAN EDIT  FINAL AND DRAFT TAG
				//WHEN TRANSACTIONS IS POSTED IT IS UNEDITABLE
				if($this->cuserlvl != 'S') {
					$str = "select aa.post_tag from {$this->db_erp}.`trx_manrecs_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.post_tag ='Y'";
						$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						if($q->num_rows() > 0){
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Transactions already posted!!!</br>Note:Posted Transactions is uneditable.</div>";
						die();
					}
				}//endif
				$this->mymdacct->save();
			}
			else{
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
		}else{
			//ADD SAVE ACCESS
			$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='6'","myua_acct");
			if($result != 1){
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
			$this->mymdacct->save();
		}
		
	}
	//search 
	public function mndt_invent_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_vw_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dteto'));
		$fld_vw_dtefrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dtefrm'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_recs($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-recs',$data);
	}
    
	public function mndt_invent_gro_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_vw_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dteto'));
		$fld_vw_dtefrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dtefrm'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_recs($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs_gro-recs',$data);
	}

	//search 
	public function mndt_invent_recs_goods() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_vw_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dteto'));
		$fld_vw_dtefrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dtefrm'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_recs_goods($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-goods-recs',$data);
	}

	//search 
	public function mndt_invent_recs_dlvry() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_vw_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dteto'));
		$fld_vw_dtefrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dtefrm'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_recs_ddlvry($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dlvry-recs',$data);
	}

	public function mndt_invent_recs_transfer() { 

		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_vw_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dteto'));
		$fld_vw_dtefrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dtefrm'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_recs($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-transfer-recs',$data);

	}

	public function mndt_invent_recs_transfer_rpt() { 

		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_vw_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dteto'));
		$fld_vw_dtefrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_vw_dtefrm'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_recs($mpages,20,$txtsearchedrec,$fld_vw_dteto,$fld_vw_dtefrm);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-transfer-rpt-recs',$data);

	}
	

	//search 
	public function mndt_invent_recs_premium() { 

		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-premium-recs',$data);
	}	

	//search 
	public function mndt_invent_recs_regpremium() { 

		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-regpremium-recs',$data);
	}

	//search 
	public function mndt_invent_po_recs_sts() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs_sts($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-sts-recs_po',$data);
	}

	//search 
	public function mndt_invent_po_recs_dmglss() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs_dmglss($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-dmglss-recs_po',$data);
	}
	//search 
	// public function mndt_invent_po_recs_transfer() { 
	// 	$txtsearchedrec = $this->input->get_post('txtsearchedrec');
	// 	$mpages = $this->input->get_post('mpages');
	// 	$mpages = (empty($mpages) ? 0: $mpages);
	// 	$data = $this->mymdacctpogro->view_recs($mpages,20,$txtsearchedrec);
	// 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-transfer-recs_po',$data);
	// }

	//search 
	public function mndt_invent_po_recs_poadjstmnt() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-poadjstmnt-recs_po',$data);
	}

		//search 
	public function mndt_invent_recs_rtv() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs_rtv($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-rtv-recs',$data);
	}

	public function myacct_manrecs_confdrecs() { 
		$data = array();
		$mtkn_mndt_rid = $this->input->get_post('mtkn_mndt_rid');
		$mtkn_mmn_rid = $this->input->get_post('mmn_rid');
		$data['mtkn_mndt_rid'] = $mtkn_mndt_rid;
		$data['mtkn_mmn_rid'] = $mtkn_mmn_rid;
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-drecs',$data);
	}

	public function msg_rcv_crecs() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='9'","myua_acct");
		if($result != 1){
			echo "<div class=\"color-line\"></div>
					<div class=\"modal-header text-center\">
						<h4 class=\"modal-title\">Record Deletion</h4>
						<!--<small class=\"font-bold\">...</small>-->
					</div>
					<div class=\"modal-body\">
						It appears that you don't have permission to access this page.
					</div>
					<div class=\"modal-footer\">
						<button type=\"button\" class=\"btn btn-danger btn-sm\" data-dismiss=\"modal\">Close</button>
					</div>";
			die();
		}
		$data = array();
		$mtkn_itm= $this->input->get_post('mtkn_itm');
		$data['mtkn_itm'] = $mtkn_itm;
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs_dt-drecs',$data);
	}
	
	public function myacct_manrecs_drecs() { //DT DEL RECS
		$this->mymdacct->delrecs();
	}
	public function rcv_crecs() { //HD CANCEL RECS
		
		$this->mymdacct->canrecs();
	}

	//FOR PULLOUT
	public function acct_man_recs_po_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='3'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY
	public function acct_man_recs_gro_po_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='3'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro_po');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

		//FOR GROCERY STS LOG
	public function acct_man_recs_gro_po_vw_stslog() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-stslog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY PENALTY LOG
	public function acct_man_recs_gro_penalty_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-penalty');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY HVI LOG
	public function acct_man_recs_gro_po_vw_hvilog() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-hvilog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	public function acct_man_recs_rej_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rej');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	public function acct_man_recs_bndlform_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-bndlform');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	public function acct_man_recs_adjstform_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-adjstform');
		}
		else{
			$this->load->view('unauthorized');
		}
	}
	public function acct_man_recs_rtvform_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rtvform');
		}
		else{
			$this->load->view('unauthorized');
		}
	}


	//posting
	public function acct_man_recs_po_posting() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='17'","myua_acct");
		if($result != 1){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
			die();
		}
		$this->mymdacctpo->rcv_po_posting();
	}

		//saving new data STS Log
    public function stslog_save() 
	
	{ 
		$this->mymdacctpogro->stslog_save();
	}

			//saving new data TDR Log
    public function tdrlog_save() 
	
	{ 
		$this->mymdacctpogro->tdrlog_save();
	}

	//saving new data DLR Log
    public function dlrlog_save() 
	
	{ 
		$this->mymdacctpogro->dlrlog_save();
	}

	//saving new data REJ Log
    public function rejlog_save() 
	
	{ 
		$this->mymdacctpogro->rejlog_save();
	}


	//saving new data PO Log
    public function polog_save() 
	
	{ 
		$this->mymdacctpogro->polog_save();
	}

	//saving new data GP Log
    public function gplog_save() 
	
	{ 
		$this->mymdacctpogro->gplog_save();
	}

	public function rpts_rtv_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rpts-rtv');
	 }
 	public function rpts_sts_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rpts-sts');
	 }
  	public function rpts_dmglss_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rpts-dmglss');
	 }

   	public function rpts_poadjst_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rpts-adjst');
	 }

  	public function gro_report(){

	$this->mymdacctpogro->myacct_gro_report();

	}
  	public function gro_report_rtv(){

	$this->mymdacctpogro->myacct_gro_report_rtv();

	}
  	public function gro_report_sts(){

	$this->mymdacctpogro->myacct_gro_report_sts();

	}
  	public function gro_report_dmglss(){

	$this->mymdacctpogro->myacct_gro_report_dmglss();

	}	

  	public function gro_report_poadjst(){

	$this->mymdacctpogro->myacct_gro_report_poadjst();

	}
  	public function gro_report_ddr(){

	$this->mymdacctgro->myacct_gro_report_ddr();

	}
  	public function gro_report_tdr(){

	$this->mymdacctgro->myacct_gro_report_tdr();

	}
    public function hvilog_save() 
	
	{ 
		$this->mymdacctpogro->hvilog_save();
	}
	    public function rej_save() 
	
	{ 
		$this->mymdacctpogro->rej_save();
	}

	public function rpts_rej_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rej-rpts');
	 }

  	public function gro_rej_report(){

	$this->mymdacctpogro->myacct_gro_rej_report();

	}



	//FOR GROCERY TDR LOG
	public function acct_man_recs_gro_po_vw_tdrlog() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-tdrlog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY DNL LOG
	public function acct_man_recs_gro_po_vw_dnllog() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-dnllog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY REJ LOG
	public function acct_man_recs_gro_po_vw_rejlog() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rejlog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

	//FOR GROCERY PO LOG
	public function acct_man_recs_gro_po_vw_log() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-polog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}

		//FOR GROCERY GP LOG
	public function acct_man_recs_gro_po_vw_gplog() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='2'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-gplog');
		}
		else{
			$this->load->view('unauthorized');
		}
	}


		//posting
	public function acct_man_recs_po_posting_gro() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='17'","myua_acct");
		if($result != 1){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
			die();
		}
		$this->mymdacctpogro->rcv_po_posting();
	}
	//saving new data
	public function man_recs_po_sv() { 
		$trxno = $this->input->get_post('trxno_id');
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$fld_ptyp = $this->input->get_post('fld_ptyp');
		$str_tag ='';
		
		$aua_branch = $this->mydatazua->ua_brnch($this->db_erp,$cuser);
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
			//EDIT ACCESS
			$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='16'","myua_acct");
			if($result == 1){
				//IF USER IS NOT A SUPERADMIN WILL FALL THIS VALIDATION
				if($this->cuserlvl != 'S') {
					
					//USER ONLY CAN EDIT THEIR ENTRY WHEN TAG IS DRAFT ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_drft = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='32'","myua_acct");
					
					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDITDRAFT
					$result_fnal = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='33'","myua_acct");

					//USER CAN EDIT ENTRY WHEN TAG IS FINAL ELSE NO ACCESS --RCVNG EDIT BRNCH 
					$result_brnch = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='34'","myua_acct");
					
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
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</div>";
						die();
					}
					
					$str = "select aa.muser,aa.potrx_no from {$this->db_erp}.`trx_manrecs_po_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.flag = 'R' {$str_tag}";
					//var_dump($str);
					$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->num_rows() == 0){
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> You don't authorized to edit this data!!!</br>Note:Only the Administrative User can edit the Final Tagging.</div>";
						die();
					}//endif
				} //endif
				//WHEN TRANSACTIONS IS POSTED IT IS UNEDITABLE
				$str = "select aa.post_tag from {$this->db_erp}.`trx_manrecs_po_hd` aa where sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$trxno' and aa.post_tag ='Y'";
					$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					if($q->num_rows() > 0){
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Transactions already posted!!!</br>Note:Posted Transactions is uneditable.</div>";
					die();
				}
				if($fld_ptyp == 'N'){
					$this->mymdacctpo->save_nontrade();
				}
				else{
					$this->mymdacctpo->save();
				}
			}
			else{ //IF EDIT DATA AND NO PERMISSION
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
		}else{
			//ADD SAVE ACCESS
			$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='15'","myua_acct");
			if($result != 1){
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
				die();
			}
			if($fld_ptyp == 'N'){
				$this->mymdacctpo->save_nontrade();
			}
			else{
				$this->mymdacctpo->save();
			}
		}

	}
	//search 
	public function mndt_invent_po_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpo->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-recs_po',$data);
	}

	//search for grocery sts log
	public function mndt_invent_po_stslog_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-stslog-recs',$data);
	}

	//search 
	public function mndt_invent_gro_po_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-recs_po',$data);
	}
	//search for grocery hvi log
	public function mndt_invent_po_hvilog_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs_hvilog($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-hvilog-recs',$data);
	}

	public function mndt_invent_rej_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_recs_rej($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_gro-rej-recs',$data);
	}

	public function myacct_manrecs_po_confdrecs() { 
		$data = array();
		$mtkn_mndt_rid = $this->input->get_post('mtkn_mndt_rid');
		$mtkn_mmn_rid = $this->input->get_post('mmn_rid');
		$data['mtkn_mndt_rid'] = $mtkn_mndt_rid;
		$data['mtkn_mmn_rid'] = $mtkn_mmn_rid;
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-drecs_po',$data);
	}
	public function msg_po_crecs() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='18'","myua_acct");
		if($result != 1){
			echo "<div class=\"color-line\"></div>
					<div class=\"modal-header text-center\">
						<h4 class=\"modal-title\">Record Deletion</h4>
						<!--<small class=\"font-bold\">...</small>-->
					</div>
					<div class=\"modal-body\">
						It appears that you don't have permission to access this page.
					</div>
					<div class=\"modal-footer\">
						<button type=\"button\" class=\"btn btn-danger btn-sm\" data-dismiss=\"modal\">Close</button>
					</div>";
			die();
		}
		$data = array();
		$mtkn_itm= $this->input->get_post('mtkn_itm');
		$data['mtkn_itm'] = $mtkn_itm;
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_dt-drecs_po',$data);
	}
	
	public function myacct_manrecs_po_drecs() { //DT DEL RECS
		$this->mymdacctpo->delrecs();
	}
	public function po_crecs() { //HD CANCEL RECS
		$this->mymdacctpo->canrecs();
	}
	public function pout_summ_wv() { //HD CANCEL RECS
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='35'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-summ');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	public function pout_summ_pdf() { //PO BREAKDOWN
		$this->load->library('fpdf/mypdf');
		$this->load->view('masterdata/acct_mod/man_recs_po/posumm_print_pdf');
		
	}//end func
	public function pout_summ() { //PO BREAKDOWN
		$this->mymdacctpo->po_rpt_download_proc();
	}//end func
	
	//FOR TRANSFER
	public function acct_man_recs_trans_vw() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='4'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_trans/myacct_manrecs_trans');
		}
		else{
			$this->load->view('unauthorized');
		}
	}
	
	//saving new data
	public function man_recs_trans_sv() { 
		$this->mymdaccttrans->save();
	}
	//search 
	public function mndt_invent_trans_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdaccttrans->view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_trans/myacct_manrecs-recs_trans',$data);
	}

	public function myacct_manrecs_trans_confdrecs() { 
		$data = array();
		$mtkn_mndt_rid = $this->input->get_post('mtkn_mndt_rid');
		$mtkn_mmn_rid = $this->input->get_post('mmn_rid');
		$data['mtkn_mndt_rid'] = $mtkn_mndt_rid;
		$data['mtkn_mmn_rid'] = $mtkn_mmn_rid;
		$this->load->view('masterdata/acct_mod/man_recs_trans/myacct_manrecs-drecs_trans',$data);
	}
	public function msg_trans_crecs() { 
		$data = array();
		$mtkn_itm= $this->input->get_post('mtkn_itm');
		$data['mtkn_itm'] = $mtkn_itm;
		$this->load->view('masterdata/acct_mod/man_recs_trans/myacct_manrecs_dt-drecs_trans',$data);
	}
	
	public function myacct_manrecs_trans_drecs() { //DT DEL RECS
		$this->mymdaccttrans->delrecs();
	}
	public function trans_crecs() { //HD CANCEL RECS
		$this->mymdaccttrans->canrecs();
	}
	//FOR INVENTORY
	public function acct_man_recs_inv_vw() { 
		
		$str = "INSERT IGNORE INTO {$this->db_erp}.`menu_acct`(`acct_name`) VALUES ('Ending Inventory')";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='5'","myua_acct");
		if($result == 1){
			
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs_inv');
		}
		else{
			$this->load->view('unauthorized');
		}
	}
	public function acct_man_recs_inv_vw_2() {
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('fld_years');
		$mmonths = $this->input->get_post('fld_months');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctinv->view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-recs_inv',$data);
	}
	public function inv_simpleupld_proc_vw(){
		$this->mymdacctinv->inv_simpleupld_proc();
	}
	//search 
	public function mndt_inv_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('myear');
		$mmonths = $this->input->get_post('mmonths');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctinv->view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-recs_inv',$data);
	}
	public function acct_man_recs_inv_unexist(){
		$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-ue_inv');
	}
	////////////////////////////////////////////////RECEIVING///////////////////////////
	//DOWNLOAD FOR RECEIVING
	public function rcvdl_acct(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='14'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
			$this->load->view('masterdata/acct_mod/man_recs/rcv_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	}//end func
	public function rcvdl_acct1(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
			$this->load->view('masterdata/acct_mod/man_recs/rcv_goods_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	}//end func
	public function invrpt_download_proc() { 
		$this->mymdacct->inv_rpt_download_proc();
		
	}//end func
	public function inv_rpts_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='10'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-rpts');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	
	//INV LOG FILE
	public function invlogfile_vw() {
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='11'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-logfile');
		}else{
			//$data = $this->mymdacct->view_invlogfile_recs();
			$this->load->view('unauthorized_sm');
		}
	}//end func
	public function invlogfile_recs() { 
		$txtsearchedrec2 = $this->input->get_post('txtsearchedrec2');
		$fld_dlsupp = $this->input->get_post('fld_dlsupp');
		$fld_dlsupp_id = $this->input->get_post('fld_dlsupp_id');
		$fld_dlbranch = $this->input->get_post('fld_dlbranch');
		$fld_dl_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_dl_dteto'));
		$fld_dl_dtefrom = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_dl_dtefrom'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_invlogfile_recs_fltr($mpages,20,$fld_dlsupp,$fld_dlsupp_id,$fld_dlbranch,$fld_dl_dteto,$fld_dl_dtefrom);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-logfile-recs',$data);
	}
	public function invlogfile_recs_fltr() { 
		$fld_dlsupp = $this->input->get_post('fld_dlsupp');
		$fld_dlsupp_id = $this->input->get_post('fld_dlsupp_id');
		$fld_dlbranch = $this->input->get_post('fld_dlbranch');
		$fld_dl_dteto = $this->input->get_post('fld_dl_dteto');
		$fld_dl_dtefrom = $this->input->get_post('fld_dl_dtefrom');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_invlogfile_recs_fltr($mpages,20,$fld_dlsupp,$fld_dlsupp_id,$fld_dlbranch,$fld_dl_dteto,$fld_dl_dtefrom);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-logfile-recs',$data);
	}
	public function auto_add_lines() { 
		$nval = $this->input->get_post('menumerate');
		for($aa = 0; $aa <= $nval; $aa++) { 
			$chtml = "
			<script>
				my_add_line_item();
			</script>
			";
			echo $chtml;
		} //end for
		
	}  //end auto_add_lines
	//DR BREAKDOWN MONTHLY REPORT
	public function dr_rpts_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='12'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dr_rpts');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function drrpt_download_proc() { 
		/*$cusergrp = $this->mylibz->mysys_usergrp();
		if($cusergrp=='SA'){*/
			$this->mymdacct->dr_rpt_download_proc();
		/*}
		else{
			echo "You don't have permission to access this Module.";
			return;
		}*/
	}//end func

	//TOTAL DELIVER MONTHLY REPORT
	public function td_rpts_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='13'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-td_rpts');
		}else{
			$this->load->view('unauthorized_sm');	
		}

        
	}
	public function tdrpt_download_proc() { 
		$this->mymdacct->td_rpt_download_proc();
	}//end func
	
////////////////////////////////////////////////PULL OUT///////////////////////////
	public function rpts_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-rpts_po');
	 }
  
	public function rpts_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/po_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc

//PULL OUT LOG FILE
	public function poutlogfile_vw() {
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='19'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-logfile');
		}else{
			//$data = $this->mymdacct->view_invlogfile_recs();
			$this->load->view('unauthorized_sm');
		}
	}//end func
	public function poutlogfile_recs() { 
		$txtsearchedrec2 = $this->input->get_post('txtsearchedrec2');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpo->view_poutlogfile_recs($mpages,20,$txtsearchedrec2);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-logfile-recs',$data);
	}
	public function poutlogfile_recs_fltr() { 
		$fld_dlsupp = $this->input->get_post('fld_dlsupp');
		$fld_dlsupp_id = $this->input->get_post('fld_dlsupp_id');
		$fld_dlbranch = $this->input->get_post('fld_dlbranch');
		$fld_dldftag = $this->input->get_post('fld_logdftag');
		$fld_dlrson = $this->input->get_post('fld_logrson');

		$fld_dl_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_dl_dteto'));
		$fld_dl_dtefrom = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_dl_dtefrom'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpo->view_poutlogfile_recs_fltr($mpages,20,$fld_dlsupp,$fld_dlsupp_id,$fld_dlbranch,$fld_dl_dteto,$fld_dl_dtefrom,$fld_dldftag,$fld_dlrson);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-logfile-recs',$data);
	}
	//DR CHECKING
	public function myacct_dr_checking(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$check_dr = $this->input->get_post('dr_no');
		$trxno = $this->input->get_post('trxno');
		$supp = $this->input->get_post('supp');
		if(empty($supp)){
			echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Please select supplier.!!!.</div>";
			die();
		}
		$strno ='';
		if(!empty($trxno)){
			$strno = "AND !(sha2(concat(recid,'{$mpw_tkn}'),384) = '$trxno')";
		}
		$str = "
			select trx_no,drno __mdata from {$this->db_erp}.trx_manrecs_hd where drno = '$check_dr' AND sha2(concat(supplier_id,'{$mpw_tkn}'),384) ='$supp' AND flag= 'R' {$strno}";
			$q =  $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($str);
			if($q->num_rows() > 0) { 
				$rrec = $q->row_array();
				$trxno = $rrec['trx_no'];
				$drno = $rrec['__mdata'];
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> DR No ".$drno." Already exist in transaction number ".$trxno." !!!.</div>";
				die();
			}
			else{
				echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> DR No is available.!!!.</div>";
			}
		$q->free_result();
	}//endfunc
	//POSTING
	public function post_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='24'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-post');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}//endfunc

	public function post_vw_gro(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='24'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-post_gro');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}//endfunc
	public function posting_proc(){
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_pdtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtfrm'));
		$fld_pdtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-post-recs',$data);
	}//endfunc

	public function posting_proc_gro(){
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_pdtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtfrm'));
		$fld_pdtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-post-recs_gro',$data);
	}//endfunc
	public function posting_recs() { 
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_pdtfrm = $this->input->get_post('fld_pdtfrm');
		$fld_pdtto = $this->input->get_post('fld_pdtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-post-recs',$data);
	}//endfunc

	public function posting_recs_gro() { 
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_pdtfrm = $this->input->get_post('fld_pdtfrm');
		$fld_pdtto = $this->input->get_post('fld_pdtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-post-recs_gro',$data);
	}//endfunc
	//DASH 1
	public function dash1_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='25'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash1');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}//endfunc
	public function dash1_recs(){
		$this->mymdacct_dash1->view_dash1_recs();
	}//endfunc
	public function myacct_vw_drcvng(){
		$mtkn = $this->input->get_post('mtkn');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct_dash1->view_drcvng_recs($mpages,20,$mtkn);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash1-recs',$data);
	}//endfunc
	//DASH 2
	public function dash2_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='26'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash2');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function dash2_recs(){
		$this->mymdacct_dash1->view_d2rcvng_recs();	
	}
	//DR UPLOADING
	public function acct_man_recs_dru_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='27'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_drupld/myacct_manrecs_dru');
		}else{
			$this->load->view('unauthorized');	
		}
	}
	public function acct_man_recs_dru_vw_2() {
		/*$data['myear'] = $this->input->get_post('fld_years_s');
		$data['mmonths'] = $this->input->get_post('fld_months_s');
		$this->load->view('masterdata/acct_mod/man_recs_drupld/myacct_manrecs_dru-',$data);*/
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('fld_years');
		$mmonths = $this->input->get_post('fld_months');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($mmonths);
		$data = $this->mymdacctdru->view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_drupld/myacct_manrecs-recs_dru',$data);
	}
	public function dru_simpleupld_proc_vw(){
		$this->mymdacctdru->process_dr_upld();
	}
	public function mndt_dru_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('myear');
		$mmonths = $this->input->get_post('mmonths');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($mpages);
		$data = $this->mymdacctdru->view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_drupld/myacct_manrecs-recs_dru',$data);
	}//endfunc
	///RECON TAB
	public function recon_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='29'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-recon_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function recon_process_vw(){
		$fld_rrcvmonths  = $this->dbx->escape_str($this->input->get_post('fld_rrcvmonths'));
		$fld_rrcvyear  = $this->dbx->escape_str($this->input->get_post('fld_rrcvyear'));
		$fld_rinmonths  = $this->dbx->escape_str($this->input->get_post('fld_rinmonths'));
		$fld_rinyear  = $this->dbx->escape_str($this->input->get_post('fld_rinyear'));
		$fld_rconmonths  = $this->dbx->escape_str($this->input->get_post('fld_rconmonths'));
		$fld_rconyear  = $this->dbx->escape_str($this->input->get_post('fld_rconyear'));
		$data= $this->mymdacctinv->recon_recs(1,20,'',$fld_rrcvmonths,$fld_rrcvyear,$fld_rinmonths,$fld_rinyear,$fld_rconmonths,$fld_rconyear);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-recon-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function mndt_recon_recs() { 
		$txtsearchedrec2 = $this->input->get_post('txtsearchedrec2');
		$fld_rrcvmonths = $this->input->get_post('fld_rrcvmonths');
		$fld_rrcvyear = $this->input->get_post('fld_rrcvyear');
		$fld_rinmonths = $this->input->get_post('fld_rinmonths');
		$fld_rinyear = $this->input->get_post('fld_rinyear');
		$fld_rconmonths  = $this->dbx->escape_str($this->input->get_post('fld_rconmonths'));
		$fld_rconyear  = $this->dbx->escape_str($this->input->get_post('fld_rconyear'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($mpages);
		$data= $this->mymdacctinv->recon_recs($mpages,20,$txtsearchedrec2,$fld_rrcvmonths,$fld_rrcvyear,$fld_rinmonths,$fld_rinyear,$fld_rconmonths,$fld_rconyear);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-recon-recs_inv',$data);
	}
	public function recon_process_sv(){
		$this->mymdacctinv->recon_sv_proc();
		
	}
	//SUMM
	public function summ_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='30'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-summ_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function summ_process_vw(){
		$fld_summmonths  = $this->dbx->escape_str($this->input->get_post('fld_summmonths'));
		$fld_summyear  = $this->dbx->escape_str($this->input->get_post('fld_summyear'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data= $this->mymdacctinv->summ_recs($mpages,20,'',$fld_summyear,$fld_summmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-summ-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function mndt_summ_recs() { 
		$txtsearchedrec3 = $this->input->get_post('txtsearchedrec3');
		$fld_summmonths  = $this->dbx->escape_str($this->input->get_post('fld_summmonths'));
		$fld_summyear  = $this->dbx->escape_str($this->input->get_post('fld_summyear'));
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($mpages);
		$data= $this->mymdacctinv->summ_recs($mpages,20,$txtsearchedrec3,$fld_summyear,$fld_summmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-summ-recs_inv',$data);
	}
	//COST OF SALES
	public function cos_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='31'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-cos_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function cos_process_vw(){

		$fld_cosmonths  = $this->dbx->escape_str($this->input->get_post('fld_cosmonths'));
		$fld_cosyear  = $this->dbx->escape_str($this->input->get_post('fld_cosyear'));

		$mpages = $this->input->get_post('mpages');
		//var_dump($mpages);
		$mpages = (empty($mpages) ? 0: $mpages);

		$data= $this->mymdacctinv->cos_recs($mpages,20,'',$fld_cosyear,$fld_cosmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-cos-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function mndt_cos_recs() { 
		$txtsearchedrec4 = $this->input->get_post('txtsearchedrec4');
		$fld_cosmonths  = $this->dbx->escape_str($this->input->get_post('mmonths'));
		$fld_cosyear  = $this->dbx->escape_str($this->input->get_post('myear'));
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_cosyear);
		$data= $this->mymdacctinv->cos_recs($mpages,20,$txtsearchedrec4,$fld_cosyear,$fld_cosmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-cos-recs_inv',$data);
	}
	public function cos_recs_dl_vw() { 
		$fld_cosmonths  = $this->dbx->escape_str($this->input->get_post('fld_cosmonths'));
		$fld_cosyear  = $this->dbx->escape_str($this->input->get_post('fld_cosyear'));
		$fld_ccos  = $this->dbx->escape_str($this->input->get_post('fld_ccos'));
		$fld_ccos_id  = $this->dbx->escape_str($this->input->get_post('fld_ccos_id'));
		$fld_bcos  = $this->dbx->escape_str($this->input->get_post('fld_bcos'));
		$fld_bcos_id  = $this->dbx->escape_str($this->input->get_post('fld_bcos_id'));
		$this->mymdacctinv->cos_recs_dl($fld_cosyear,$fld_cosmonths,$fld_bcos,$fld_bcos_id,$fld_ccos,$fld_ccos_id);
	}
	//POSTING PROCESS POUT 11-13-19
	public function pout_post_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='17'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}

	public function pout_post_vw_gro(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='17'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post_gro');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}

	public function pout_posting_proc(){
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_pdtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtfrm'));
		$fld_pdtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpo->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post-recs',$data);
	}

	public function pout_posting_proc_gro(){
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_preason = $this->input->get_post('fld_preason');
		$fld_preason_id = $this->input->get_post('fld_preason_id');
		$fld_pdtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtfrm'));
		$fld_pdtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_pdtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_preason,$fld_preason_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post-recs_gro',$data);
	}
	public function pout_posting_recs() { 
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_pdtfrm = $this->input->get_post('fld_pdtfrm');
		$fld_pdtto = $this->input->get_post('fld_pdtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpo->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post-recs',$data);
	}

	public function pout_posting_recs_gro() { 
		$fld_pbranch = $this->input->get_post('fld_pbranch');
		$fld_pbranch_id = $this->input->get_post('fld_pbranch_id');
		$fld_preason = $this->input->get_post('fld_preason');
		$fld_preason_id = $this->input->get_post('fld_preason_id');
		$fld_pdtfrm = $this->input->get_post('fld_pdtfrm');
		$fld_pdtto = $this->input->get_post('fld_pdtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpogro->view_post_recs($mpages,20,$fld_pbranch,$fld_pbranch_id,$fld_preason,$fld_preason_id,$fld_pdtfrm,$fld_pdtto);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-pout-post-recs_gro',$data);
	}
	//DASH 3
	public function dash3_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='37'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash3');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function dash3_recs(){
		$this->mymdacct_dash1->view_dash3_recs();
	}
	public function dash3_dt_vw(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$mbranch_id ='';
		$mbranch ='';
		$fld_years =$this->input->get_post('fld_d3years');
		$fld_months =$this->input->get_post('fld_d3months');
		$fld_d3brnch =$this->input->get_post('fld_d3brnch');
        $fld_d3brnch_id=$this->input->get_post('fld_d3brnch_id');
		if(!empty($fld_d3brnch) && !empty($fld_d3brnch_id)){
			//BRANCH
			$str = "select recid,BRNCH_NAME,BRNCH_OCODE1 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_d3brnch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_d3brnch_id'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($fld_d3brnch_id);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$mbranch_id = $rw['recid'];
			$mbranch = $rw['BRNCH_OCODE1'];
			$q->free_result();
		}
		//var_dump($fld_years);
		$data = $this->mymdacct_dash1->view_dash3_recs($fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash3-recs',$data);
	}
	public function dash3_dt_vw_brnch(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$fld_years =$this->input->get_post('fld_d3years');
		$fld_months =$this->input->get_post('fld_d3months');
		$fld_d3brnch =$this->input->get_post('fld_d3brnch');
        $fld_d3brnch_id=$this->input->get_post('fld_d3brnch_id');
		$str_branch ='';
		if(!empty($fld_d3brnch) && !empty($fld_d3brnch_id)){
			$str_branch ="WHERE sha2(concat(a.recid,'{$mpw_tkn}'),384) = '$fld_d3brnch_id'";
		}
		$str = "
            SELECT 
            a.`recid`,
            a.`BRNCH_OCODE1`,
            a.`BRNCH_NAME`
            FROM  {$this->db_erp}.`mst_companyBranch` a
            {$str_branch}
            ORDER BY a.`BRNCH_NAME`
            ";
            $q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($q->num_rows() > 0) { 
              $str_branch = "";
              foreach($q->result_array() as $res){
                $mbranch =  $res['BRNCH_OCODE1'];
                $mbranch_id =  $res['recid'];
                
                $data_r = $this->mymdacct_dash1->view_dash3_recs_brnch($fld_years,$fld_months,$mbranch_id,$mbranch);
                $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-bdash3-recs',$data_r);
              }
            }
	}//endif
	public function myacct_vw_d3a_drcvng(){
		$mtkn = $this->input->get_post('mtkn');
		$fld_years = $this->input->get_post('fld_years');
		$fld_months = $this->input->get_post('fld_months');
		$mbranch_id = $this->input->get_post('mbranch_id');
		$mbranch = $this->input->get_post('mbranch');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_years);
		$data = $this->mymdacct_dash1->view_d3a_drcvng_recs($mpages,20,$mtkn,$fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash3-srecs',$data);
	}
	public function myacct_vw_d3a_drcvng_brnch(){
		$mtkn = $this->input->get_post('mtkn');
		$fld_years = $this->input->get_post('fld_years');
		$fld_months = $this->input->get_post('fld_months');
		$mbranch_id = $this->input->get_post('mbranch_id');
		$mbranch = $this->input->get_post('mbranch');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_years);
		$data = $this->mymdacct_dash1->view_d3a_drcvng_recs($mpages,20,$mtkn,$fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-bdash3-srecs',$data);
	}
	public function myacct_manrecs_bd1_remk(){
		$this->mymdacct_dash1->ins_remk_sv();
	}
	//LIVE BALANCE
	public function lb_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='39'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lb_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function lb_process_vw(){

		$fld_lbmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbmonths'));
		$fld_lbyear  = $this->dbx->escape_str($this->input->get_post('fld_lbyear'));

		$mpages = $this->input->get_post('mpages');
		//var_dump($mpages);
		$mpages = (empty($mpages) ? 0: $mpages);

		$data= $this->mymdacctinv->lb_recs($mpages,20,'',$fld_lbyear,$fld_lbmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lb-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function lb_recs_dl_vw_ins() { 
		$fld_lbmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbmonths'));
		$fld_lbyear  = $this->dbx->escape_str($this->input->get_post('fld_lbyear'));
		$fld_clb  = $this->dbx->escape_str($this->input->get_post('fld_clb'));
		$fld_clb_id  = $this->dbx->escape_str($this->input->get_post('fld_clb_id'));
		$fld_blb  = $this->dbx->escape_str($this->input->get_post('fld_blb'));
		$fld_blb_id  = $this->dbx->escape_str($this->input->get_post('fld_blb_id'));
		$this->mymdacctinv->lb_recs_insert($fld_lbyear,$fld_lbmonths,$fld_blb,$fld_blb_id,$fld_clb,$fld_clb_id);
	}
	public function mndt_lb_recs() { 
		$txtsearchedrec4 = $this->input->get_post('txtsearchedrec4');
		$fld_lbmonths  = $this->dbx->escape_str($this->input->get_post('mmonths'));
		$fld_lbyear  = $this->dbx->escape_str($this->input->get_post('myear'));
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_lbyear);
		$data= $this->mymdacctinv->lb_recs($mpages,20,$txtsearchedrec4,$fld_lbyear,$fld_lbmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lb-recs_inv',$data);
	}
	public function lb_recs_dl_vw() { 
		$fld_lbmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbmonths'));
		$fld_lbyear  = $this->dbx->escape_str($this->input->get_post('fld_lbyear'));
		$fld_clb  = $this->dbx->escape_str($this->input->get_post('fld_clb'));
		$fld_clb_id  = $this->dbx->escape_str($this->input->get_post('fld_clb_id'));
		$fld_blb  = $this->dbx->escape_str($this->input->get_post('fld_blb'));
		$fld_blb_id  = $this->dbx->escape_str($this->input->get_post('fld_blb_id'));
		$fld_lbdate = trim($this->input->get_post('fld_lbdate'));
		if(!empty($fld_lbdate)) {
			$fld_lbdate  = $this->dbx->escape_str($this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_lbdate')));
		} 
		$myear_d ='';
		$mmonths_d ='';
		if(!empty($fld_lbdate)){
			$myear_d   = DateTime::createFromFormat('!Y', $fld_lbdate);
			$mmonths_d   = DateTime::createFromFormat('!m', $fld_lbdate);
		}
		if(($fld_lbyear=='2020' && $fld_lbmonths=='2') || ($myear_d == '2020' && $mmonths_d == '2')){
			$this->mymdacctinv->lb_recs_dl($fld_lbyear,$fld_lbmonths,$fld_blb,$fld_blb_id,$fld_clb,$fld_clb_id);
		}
		else{
			$this->mymdacctinv->lb_recs_dl_n($fld_lbyear,$fld_lbmonths,$fld_blb,$fld_blb_id,$fld_clb,$fld_clb_id,$fld_lbdate);
		}

	}
	//V2 VIEW AND PROCESS PANG NOV
	public function lb_recs_dl_vw_2() { 
		$fld_lbmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbmonths'));
		$fld_lbyear  = $this->dbx->escape_str($this->input->get_post('fld_lbyear'));
		$fld_clb  = $this->dbx->escape_str($this->input->get_post('fld_clb'));
		$fld_clb_id  = $this->dbx->escape_str($this->input->get_post('fld_clb_id'));
		$fld_blb  = $this->dbx->escape_str($this->input->get_post('fld_blb'));
		$fld_blb_id  = $this->dbx->escape_str($this->input->get_post('fld_blb_id'));
		$this->mymdacctinv->lb_recs_dl_n($fld_lbyear,$fld_lbmonths,$fld_blb,$fld_blb_id,$fld_clb,$fld_clb_id);
	}
	public function lb_recs_dl_vw_ins_2() { 
		$fld_lbmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbmonths'));
		$fld_lbyear  = $this->dbx->escape_str($this->input->get_post('fld_lbyear'));
		$fld_clb  = $this->dbx->escape_str($this->input->get_post('fld_clb'));
		$fld_clb_id  = $this->dbx->escape_str($this->input->get_post('fld_clb_id'));
		$fld_blb  = $this->dbx->escape_str($this->input->get_post('fld_blb'));
		$fld_blb_id  = $this->dbx->escape_str($this->input->get_post('fld_blb_id'));
		$fld_lbdate  = $this->dbx->escape_str($this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_lbdate')));
		$this->mymdacctinv->lb_recs_insert_2($fld_lbyear,$fld_lbmonths,$fld_blb,$fld_blb_id,$fld_clb,$fld_clb_id,$fld_lbdate);
	}
	//FOR CYCLE COUNT
	public function acct_man_recs_cyc_vw() { 
		
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='40'","myua_acct");
		if($result == 1){
			
			$this->load->view('masterdata/acct_mod/man_recs_cyc/myacct_manrecs_cyc');
		}
		else{
			$this->load->view('unauthorized');
		}
	}
	public function acct_man_recs_cyc_vw_2() {
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('fld_years');
		$mmonths = $this->input->get_post('fld_months');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/myacct_manrecs-recs_cyc',$data);
	}
	public function cyc_simpleupld_proc_vw(){
		$this->mymdacctcyc->cyc_simpleupld_proc();
	}
	//search 
	public function mndt_cyc_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('myear');
		$mmonths = $this->input->get_post('mmonths');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/myacct_manrecs-recs_cyc',$data);
	}
	//delete
	public function cyc_remove_upld_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='62'","myua_acct");
		if($result == 1){
			$this->mymdacctcyc->cyc_remove_upld();
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Delete Failed</strong> It appears that you don't have permission to access this page!!!.</div>";
			die();
		}
	}
	//LIVE BALANCE BREAKDOWN
	public function lbbd_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='41'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lbbd_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function lbbd_process_vw(){

		$fld_lbbdmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbbdmonths'));
		$fld_lbbdyear  = $this->dbx->escape_str($this->input->get_post('fld_lbbdyear'));

		$mpages = $this->input->get_post('mpages');
		//var_dump($mpages);
		$mpages = (empty($mpages) ? 0: $mpages);

		$data= $this->mymdacctinv->lbbd_recs($mpages,20,'',$fld_lbbdyear,$fld_lbbdmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lbbd-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function lbbd_recs_dl_vw_ins() { 
		$fld_lbbdmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbbdmonths'));
		$fld_lbbdyear  = $this->dbx->escape_str($this->input->get_post('fld_lbbdyear'));
		$fld_clbbd  = $this->dbx->escape_str($this->input->get_post('fld_clbbd'));
		$fld_clbbd_id  = $this->dbx->escape_str($this->input->get_post('fld_clbbd_id'));
		$fld_blbbd  = $this->dbx->escape_str($this->input->get_post('fld_blbbd'));
		$fld_blbbd_id  = $this->dbx->escape_str($this->input->get_post('fld_blbbd_id'));
		$this->mymdacctinv->lbbd_recs_insert($fld_lbbdyear,$fld_lbbdmonths,$fld_blbbd,$fld_blbbd_id,$fld_clbbd,$fld_clbbd_id);
	}
	public function mndt_lbbd_recs() { 
		$txtsearchedrec4 = $this->input->get_post('txtsearchedrec4');
		$fld_lbbdmonths  = $this->dbx->escape_str($this->input->get_post('mmonths'));
		$fld_lbbdyear  = $this->dbx->escape_str($this->input->get_post('myear'));
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_lbbdyear);
		$data= $this->mymdacctinv->lbbd_recs($mpages,20,$txtsearchedrec4,$fld_lbbdyear,$fld_lbbdmonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lbbd-recs_inv',$data);
	}
	public function lbbd_recs_dl_vw() { 
		$fld_lbbdmonths  = $this->dbx->escape_str($this->input->get_post('fld_lbbdmonths'));
		$fld_lbbdyear  = $this->dbx->escape_str($this->input->get_post('fld_lbbdyear'));
		$fld_clbbd  = $this->dbx->escape_str($this->input->get_post('fld_clbbd'));
		$fld_clbbd_id  = $this->dbx->escape_str($this->input->get_post('fld_clbbd_id'));
		$fld_blbbd  = $this->dbx->escape_str($this->input->get_post('fld_blbbd'));
		$fld_blbbd_id  = $this->dbx->escape_str($this->input->get_post('fld_blbbd_id'));
		if($fld_lbbdyear=='2020' && $fld_lbbdmonths=='2'){
			$this->mymdacctinv->lbbd_recs_dl($fld_lbbdyear,$fld_lbbdmonths,$fld_blbbd,$fld_blbbd_id,$fld_clbbd,$fld_clbbd_id);
		}
		else{
			$this->mymdacctinv->lbbd_recs_dl_n($fld_lbbdyear,$fld_lbbdmonths,$fld_blbbd,$fld_blbbd_id,$fld_clbbd,$fld_clbbd_id);
		}
		
	}
	//LIVE BALANCE BREAKDOWN SKU
	public function lbbd_sku_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='42'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lbbd_sku_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function lbbd_sku_process_vw(){

		$fld_lbbd_skumonths  = $this->dbx->escape_str($this->input->get_post('fld_lbbd_skumonths'));
		$fld_lbbd_skuyear  = $this->dbx->escape_str($this->input->get_post('fld_lbbd_skuyear'));

		$mpages = $this->input->get_post('mpages');
		//var_dump($mpages);
		$mpages = (empty($mpages) ? 0: $mpages);

		$data= $this->mymdacctlb->lbbd_sku_recs($mpages,20,'',$fld_lbbd_skuyear,$fld_lbbd_skumonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lbbd-sku-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function mndt_lbbd_sku_recs() { 
		$txtsearchedrec4 = $this->input->get_post('txtsearchedrec4');
		$fld_lbbd_skumonths  = $this->dbx->escape_str($this->input->get_post('mmonths'));
		$fld_lbbd_skuyear  = $this->dbx->escape_str($this->input->get_post('myear'));
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_lbbd_skuyear);
		$data= $this->mymdacctlb->lbbd_sku_recs($mpages,20,$txtsearchedrec4,$fld_lbbd_skuyear,$fld_lbbd_skumonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-lbbd-sku-recs_inv',$data);
	}
	public function lbbd_sku_recs_dl_vw() { 
		$fld_lbbd_skumonths  = $this->dbx->escape_str($this->input->get_post('fld_lbbd_skumonths'));
		$fld_lbbd_skuyear  = $this->dbx->escape_str($this->input->get_post('fld_lbbd_skuyear'));
		$fld_clbbd_sku  = $this->dbx->escape_str($this->input->get_post('fld_clbbd_sku'));
		$fld_clbbd_sku_id  = $this->dbx->escape_str($this->input->get_post('fld_clbbd_sku_id'));
		$fld_blbbd_sku  = $this->dbx->escape_str($this->input->get_post('fld_blbbd_sku'));
		$fld_blbbd_sku_id  = $this->dbx->escape_str($this->input->get_post('fld_blbbd_sku_id'));
		$this->mymdacctlb->lbbd_sku_recs_dl($fld_lbbd_skuyear,$fld_lbbd_skumonths,$fld_blbbd_sku,$fld_blbbd_sku_id,$fld_clbbd_sku,$fld_clbbd_sku_id);
		
		
	}
	//COST OF SALES SKU
	public function cos_sku_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='44'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-cossku_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function cos_sku_process_vw(){

		$fld_cskumonths  = $this->dbx->escape_str($this->input->get_post('fld_cskumonths'));
		$fld_cskuyear  = $this->dbx->escape_str($this->input->get_post('fld_cskuyear'));

		$mpages = $this->input->get_post('mpages');
		//var_dump($mpages);
		$mpages = (empty($mpages) ? 0: $mpages);

		$data= $this->mymdacctinv->cos_sku_recs($mpages,20,'',$fld_cskuyear,$fld_cskumonths);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-cossku-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function mndt_cos_sku_recs() { 
			$txtsearchedrec4 = $this->input->get_post('txtsearchedrec4');
			$fld_cskumonths  = $this->dbx->escape_str($this->input->get_post('mmonths'));
			$fld_cskuyear  = $this->dbx->escape_str($this->input->get_post('myear'));
			
			$mpages = $this->input->get_post('mpages');
			$mpages = (empty($mpages) ? 0: $mpages);
			//var_dump($fld_cskuyear);
			$data= $this->mymdacctinv->cos_sku_recs($mpages,20,$txtsearchedrec4,$fld_cskuyear,$fld_cskumonths);
		    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs-cossku-recs_inv',$data);
		}
	public function cos_sku_recs_dl_vw() { 
			$fld_cskumonths  = $this->dbx->escape_str($this->input->get_post('fld_cskumonths'));
			$fld_cskuyear  = $this->dbx->escape_str($this->input->get_post('fld_cskuyear'));
			$fld_ccsku  = $this->dbx->escape_str($this->input->get_post('fld_ccsku'));
			$fld_ccsku_id  = $this->dbx->escape_str($this->input->get_post('fld_ccsku_id'));
			$fld_bcsku  = $this->dbx->escape_str($this->input->get_post('fld_bcsku'));
			$fld_bcsku_id  = $this->dbx->escape_str($this->input->get_post('fld_bcsku_id'));

			$this->mymdacctinv->cos_sku_recs_dl($fld_cskuyear,$fld_cskumonths,$fld_bcsku,$fld_bcsku_id,$fld_ccsku,$fld_ccsku_id);
			

	}
	//POSTING PROCESS RCVNG
	//posting
	public function acct_man_recs_cposting() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='45'","myua_acct");
		if($result != 1){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
			die();
		}
		$this->mymdacct->rcv_cposting();
	}
	public function cpost_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='45'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-cpost');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function cposting_proc(){
		$fld_cpbranch = $this->input->get_post('fld_cpbranch');
		$fld_cpbranch_id = $this->input->get_post('fld_cpbranch_id');
		$fld_cpdtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_cpdtfrm'));
		$fld_cpdtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_cpdtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_cpost_recs($mpages,20,$fld_cpbranch,$fld_cpbranch_id,$fld_cpdtfrm,$fld_cpdtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-cpost-recs',$data);
	}
	public function cposting_recs() { 
		$fld_cpbranch = $this->input->get_post('fld_cpbranch');
		$fld_cpbranch_id = $this->input->get_post('fld_cpbranch_id');
		$fld_cpdtfrm = $this->input->get_post('fld_cpdtfrm');
		$fld_cpdtto = $this->input->get_post('fld_cpdtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		
		$data = $this->mymdacct->view_cpost_recs($mpages,20,$fld_cpbranch,$fld_cpbranch_id,$fld_cpdtfrm,$fld_cpdtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-cpost-recs',$data);
	}
	//DASH 3 PO
	public function dash3po_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='48'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-dash3');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function dash3po_recs(){
		$this->mymdacctpo_dash1->view_dash3po_recs();
	}
	public function dash3po_dt_vw(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$mbranch_id ='';
		$mbranch ='';
		$fld_years =$this->input->get_post('fld_d3years');
		$fld_months =$this->input->get_post('fld_d3months');
		$fld_d3brnch =$this->input->get_post('fld_d3brnch');
        $fld_d3brnch_id=$this->input->get_post('fld_d3brnch_id');
		if(!empty($fld_d3brnch) && !empty($fld_d3brnch_id)){
			//BRANCH
			$str = "select recid,BRNCH_NAME,BRNCH_OCODE1 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_d3brnch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_d3brnch_id'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($fld_d3brnch_id);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$mbranch_id = $rw['recid'];
			$mbranch = $rw['BRNCH_OCODE1'];
			$q->free_result();
		}
		//var_dump($fld_years);
		$data = $this->mymdacctpo_dash1->view_dash3po_recs($fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-dash3-recs',$data);
	}
	public function dash3po_dt_vw_brnch(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$fld_years =$this->input->get_post('fld_d3years');
		$fld_months =$this->input->get_post('fld_d3months');
		$fld_d3brnch =$this->input->get_post('fld_d3brnch');
        $fld_d3brnch_id=$this->input->get_post('fld_d3brnch_id');
		$str_branch ='';
		if(!empty($fld_d3brnch) && !empty($fld_d3brnch_id)){
			$str_branch ="WHERE sha2(concat(a.recid,'{$mpw_tkn}'),384) = '$fld_d3brnch_id'";
		}
		$str = "
            SELECT 
            a.`recid`,
            a.`BRNCH_OCODE1`,
            a.`BRNCH_NAME`
            FROM  {$this->db_erp}.`mst_companyBranch` a
            {$str_branch}
            ORDER BY a.`BRNCH_NAME`
            ";
            $q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($q->num_rows() > 0) { 
              $str_branch = "";
              foreach($q->result_array() as $res){
                $mbranch =  $res['BRNCH_OCODE1'];
                $mbranch_id =  $res['recid'];
                
                $data_r = $this->mymdacctpo_dash1->view_dash3po_recs_brnch($fld_years,$fld_months,$mbranch_id,$mbranch);
                $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-bdash3-recs',$data_r);
              }
            }
	}//endif
	public function myacct_vw_d3a_dpo(){
		$mtkn = $this->input->get_post('mtkn');
		$fld_years = $this->input->get_post('fld_years');
		$fld_months = $this->input->get_post('fld_months');
		$mbranch_id = $this->input->get_post('mbranch_id');
		$mbranch = $this->input->get_post('mbranch');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_years);
		$data = $this->mymdacctpo_dash1->view_d3a_dpo_recs($mpages,20,$mtkn,$fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-dash3-srecs',$data);
	}
	public function myacct_vw_d3a_dpo_brnch(){
		$mtkn = $this->input->get_post('mtkn');
		$fld_years = $this->input->get_post('fld_years');
		$fld_months = $this->input->get_post('fld_months');
		$mbranch_id = $this->input->get_post('mbranch_id');
		$mbranch = $this->input->get_post('mbranch');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_years);
		$data = $this->mymdacctpo_dash1->view_d3a_dpo_recs($mpages,20,$mtkn,$fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-bdash3-srecs',$data);
	}
	public function myacct_manrecs_pobd3_remk(){
		$this->mymdacctpo_dash1->ins_remk_sv();
	}
	///RECON TAB PULLOUT
	public function po_recon_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='61'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs_po-recon_inv');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function po_recon_process_vw(){
		$fld_rpomonths  = $this->dbx->escape_str($this->input->get_post('fld_rpomonths'));
		$fld_rpoyear  = $this->dbx->escape_str($this->input->get_post('fld_rpoyear'));
		$fld_rpoinmonths  = $this->dbx->escape_str($this->input->get_post('fld_rpoinmonths'));
		$fld_rpoinyear  = $this->dbx->escape_str($this->input->get_post('fld_rpoinyear'));
		$fld_rpoconmonths  = $this->dbx->escape_str($this->input->get_post('fld_rpoconmonths'));
		$fld_rpoconyear  = $this->dbx->escape_str($this->input->get_post('fld_rpoconyear'));
		$data= $this->mymdacctinv->pout_recon_recs(1,20,'',$fld_rpomonths,$fld_rpoyear,$fld_rpoinmonths,$fld_rpoinyear,$fld_rpoconmonths,$fld_rpoconyear);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs_po-recon-recs_inv',$data);
		//$this->mymdacctinv->recon_sv_proc();
	}
	public function po_mndt_recon_recs() { 
		$txtsearchedrec2 = $this->input->get_post('txtsearchedrec2');
		$fld_rpomonths = $this->input->get_post('fld_rpomonths');
		$fld_rpoyear = $this->input->get_post('fld_rpoyear');
		$fld_rpoinmonths = $this->input->get_post('fld_rpoinmonths');
		$fld_rpoinyear = $this->input->get_post('fld_rpoinyear');
		$fld_rpoconmonths  = $this->dbx->escape_str($this->input->get_post('fld_rpoconmonths'));
		$fld_rpoconyear  = $this->dbx->escape_str($this->input->get_post('fld_rpoconyear'));

		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($mpages);
		//die();
		$data= $this->mymdacctinv->pout_recon_recs($mpages,20,$txtsearchedrec2,$fld_rpomonths,$fld_rpoyear,$fld_rpoinmonths,$fld_rpoinyear,$fld_rpoconmonths,$fld_rpoconyear);
	    $this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs_po-recon-recs_inv',$data);
	}
	public function po_recon_process_sv(){
		$this->mymdacctinv->pout_recon_sv_proc();
		
	}
	////DASH 4
	public function dash4_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='63'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash4');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function dash4_recs(){
		$this->mymdacct_dash1->view_dash4_recs();
	}
	public function dash4_dt_vw(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$mbranch_id ='';
		$mbranch ='';
		$fld_years =$this->input->get_post('fld_d4years');
		$fld_months =$this->input->get_post('fld_d4months');
		$fld_d4brnch =$this->input->get_post('fld_d4brnch');
        $fld_d4brnch_id=$this->input->get_post('fld_d4brnch_id');
		if(!empty($fld_d4brnch) && !empty($fld_d4brnch_id)){
			//BRANCH
			$str = "select recid,BRNCH_NAME,BRNCH_OCODE1 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_d4brnch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_d4brnch_id'";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($fld_d4brnch_id);
			if($q->num_rows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}

			$rw = $q->row_array();
			$mbranch_id = $rw['recid'];
			$mbranch = $rw['BRNCH_OCODE1'];
			$q->free_result();
		}
		//var_dump($fld_years);
		$data = $this->mymdacct_dash1->view_dash4_recs($fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash4-recs',$data);
	}
	public function dash4_dt_vw_brnch(){
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$fld_years =$this->input->get_post('fld_d4years');
		$fld_months =$this->input->get_post('fld_d4months');
		$fld_d4brnch =$this->input->get_post('fld_d4brnch');
        $fld_d4brnch_id=$this->input->get_post('fld_d4brnch_id');
		$str_branch ='';
		if(!empty($fld_d4brnch) && !empty($fld_d4brnch_id)){
			$str_branch ="WHERE sha2(concat(a.recid,'{$mpw_tkn}'),384) = '$fld_d4brnch_id'";
		}
		$str = "
            SELECT 
            a.`recid`,
            a.`BRNCH_OCODE1`,
            a.`BRNCH_NAME`
            FROM  {$this->db_erp}.`mst_companyBranch` a
            {$str_branch}
            ORDER BY a.`BRNCH_NAME`
            ";
            $q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            if($q->num_rows() > 0) { 
              $str_branch = "";
              foreach($q->result_array() as $res){
                $mbranch =  $res['BRNCH_OCODE1'];
                $mbranch_id =  $res['recid'];
                
                $data_r = $this->mymdacct_dash1->view_dash4_recs_brnch($fld_years,$fld_months,$mbranch_id,$mbranch);
                $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-bdash4-recs',$data_r);
              }
            }
	}//endif
	public function myacct_vw_d4a_drcvng(){
		$mtkn = $this->input->get_post('mtkn');
		$fld_years = $this->input->get_post('fld_years');
		$fld_months = $this->input->get_post('fld_months');
		$mbranch_id = $this->input->get_post('mbranch_id');
		$mbranch = $this->input->get_post('mbranch');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_years);
		$data = $this->mymdacct_dash1->view_d4a_drcvng_recs($mpages,20,$mtkn,$fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash4-srecs',$data);
	}
	public function myacct_vw_d4a_drcvng_brnch(){
		$mtkn = $this->input->get_post('mtkn');
		$fld_years = $this->input->get_post('fld_years');
		$fld_months = $this->input->get_post('fld_months');
		$mbranch_id = $this->input->get_post('mbranch_id');
		$mbranch = $this->input->get_post('mbranch');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		//var_dump($fld_years);
		$data = $this->mymdacct_dash1->view_d4a_drcvng_recs($mpages,20,$mtkn,$fld_years,$fld_months,$mbranch_id,$mbranch);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-bdash4-srecs',$data);
	}
	public function myacct_manrecs_bd4_remk(){
		$this->mymdacct_dash1->ins_remk_sv_sd();
	}
	//POSTED BALANCE BRANCH
	public function posbra_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='89'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_inv/myacct_manrecs_posbra');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function posbra_ins(){
		$fld_months = $this->input->get_post('fld_posbramonths');
		$fld_years = $this->input->get_post('fld_posbrayear');
		$this->mymdacctlb->posbra_process_ins($fld_years,$fld_months);
	}
	public function pout_pab() { //PO BREAKDOWN
		$this->mymdacctpo->pa_brnch_rpt_download_proc();
	}//end func
	public function pout_pab_wv() { //PRICE ADJUSTMENT PER BRANCH
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='99'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-pab');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	public function pout_pap_wv() { //PRICE ADJUSTMENT PER POA
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='100'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-pap');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	public function pout_pap() { //PO BREAKDOWN
		$this->mymdacctpo->pa_poa_rpt_download_proc();
	}//end func
	//PULLOUT RPT
	public function pout_pf_wv() { //PRICE ADJUSTMENT PER POA
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='103'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs_po-pf');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	public function pout_pf() { //PO BREAKDOWN
		$this->mymdacctpo->po_frm_rpt_download_proc();
	}//end func
	//DR RCV RPT
	public function rcv_drcv_vw() { //PRICE ADJUSTMENT PER POA
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='108'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-drrcvrpts');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	public function rcv_drcv() { //PO BREAKDOWN
		$this->mymdacct->drrcv_rpt_download_proc();
	}//end func
	//SIMPLE CYCLE COUNT ///////////////////////////////////////////////////////////////////////////////////////////////
	public function cyc_upldposting() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='116'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_cyc/mycyc_upld_post');
		}
		else{
			$this->load->view('unauthorized_sm');
		}

	}
	public function upldpost_vw_2() {
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->cyc_upldpost_view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/mycyc_upld-recs_post',$data);
	}
	


	//search 
	public function mndt_upldpost_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->cyc_upldpost_view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/mycyc_upld-recs_post',$data);
	}
	//upldpost DL
	public function upldpost_download_proc() {
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cseqn = $this->input->get_post('cseqn');
		$tbltemp = $this->input->get_post('tbltemp');
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='117'","myua_acct");
		if($result == 1){
			$this->mymdacctcyc->cyc_upldpost_dl($cseqn,$tbltemp);
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong>You don't have permission to access this Module.!!!.</div>";
			return;
		}
	}
	//upldpost DL
	public function upldpost_posting_proc() {
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$mtkn_ctrlno = $this->input->get_post('mtkn_ctrlno');
		$mtkn_temp = $this->input->get_post('mtkn_temp');
		$fld_cycbranch_id = $this->input->get_post('fld_cycbranch_id');
		$fld_cycsource = '';//$this->input->get_post('fld_cycsource');
		$fld_cycdate = $this->input->get_post('fld_cycdate');
		$fld_upld_cyctag = $this->input->get_post('fld_upld_cyctag');
		$fld_cyctag = $this->input->get_post('fld_cyctag');
		$fld_months = $this->input->get_post('fld_months');
		$fld_years = $this->input->get_post('fld_years');
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='118'","myua_acct");
		if($result == 1){
			$this->mymdacctcyc->cyc_simpleupld_post($mtkn_temp,$mtkn_ctrlno,$fld_cycbranch_id,$fld_cycsource,$fld_cycdate,$fld_cyctag,$fld_years,$fld_months,$fld_upld_cyctag);
		
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong>You don't have permission to access this Module.!!!.</div>";
			return;
		}
	}
	//search HEAD to
	public function mndt_upldpost_hd_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->cyc_upldpost_hd_view_recs($mpages,20,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/mycyc_upld-hrecs_post',$data);
	}
	
	public function mndt_upldpost_temp_recs() {
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$cseqn=$this->input->get_post('cseqn');
		$tbltemp=$this->input->get_post('tbltemp');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->cyc_upldpost_proceed_view_recs($mpages,20,$txtsearchedrec,$cseqn,$tbltemp);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/mycyc_upld-trecs_post',$data);
	}
	//upldpost DL
	public function upldproc_download_proc() {
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$cseqn=$this->input->get_post('cseqn');
		$tbltemp=$this->input->get_post('tbltemp');
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='119'","myua_acct");
		if($result == 1){
			$this->mymdacctcyc->cyc_upldproc_dl($cseqn,$tbltemp);
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong>You don't have permission to access this Module.!!!.</div>";
			return;
		}
	}
	//TEMP VIEW
	//MONTHLY SUMM
	public function cyc_temp_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='120'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_cyc/myacct_manrecs_temp_cyc');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function cyc_temp_w_2() {
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$fld_temp_years = $this->input->get_post('fld_temp_years');
		$fld_temp_months = $this->input->get_post('fld_temp_months');
		$mpages = $this->input->get_post('mpages');

		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->temp_view_recs($mpages,20,$txtsearchedrec,$fld_temp_years,$fld_temp_months);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/myacct_manrecs_temp-recs_cyc',$data);
	}
	//search 
	public function mndt_cyc_temp_recs() { 
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$myear = $this->input->get_post('myear');
		$mmonths = $this->input->get_post('mmonths');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctcyc->temp_view_recs($mpages,20,$txtsearchedrec,$myear,$mmonths);
		$this->load->view('masterdata/acct_mod/man_recs_cyc/myacct_manrecs_temp-recs_cyc',$data);
	}
	//upldpost DL
	public function cyc_changeqty_sv() {
		$cuser = $this->mylibz->mysys_user();
		$mpw_tkn = $this->mylibz->mpw_tkn();
		$mtkn_trxno=$this->input->get_post('mtkn_trxno');
		$mtkn_itemc=$this->input->get_post('mtkn_itemc');
		$qty=$this->input->get_post('qty');
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='121'","myua_acct");
		if($result == 1){
			$this->mymdacctcyc->cyc_changeqty_sv_proc($mtkn_trxno,$qty,$mtkn_itemc);
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong>You don't have permission to access this Module.!!!.</div>";
			return;
		}
	}
	//DR RCV RPT
	public function rcv_var_vw() { //PRICE ADJUSTMENT PER POA
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='133'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-varrpts');
		}else{
			$this->load->view('unauthorized_sm');
		}
	}
	public function rcv_var() { //VARIANCE REPORT
		$this->mymdacct->var_rpt_download_proc();
	}//end func
	//ULOGS
	public function rcv_ulogs(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='162'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-ulogs');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function rcv_ulogs_recs(){
		$fld_utrx = $this->input->get_post('fld_utrx');
		$fld_ulogstrxno = $this->input->get_post('fld_ulogstrxno');
		$fld_ulogs_dtefrom = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_ulogs_dtefrom'));
		$fld_ulogs_dteto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_ulogs_dteto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_rcvulogs_recs($mpages,20,'',$fld_ulogstrxno,$fld_ulogs_dtefrom,$fld_ulogs_dteto,$fld_utrx);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-ulogs-recs',$data);
	}
	public function rcv_ulogs_recs2() { 
		$fld_utrx = $this->input->get_post('fld_utrx');
		$fld_ulogstrxno = $this->input->get_post('fld_ulogstrxno');
		$fld_ulogs_dtefrom = $this->input->get_post('fld_ulogs_dtefrom');
		$fld_ulogs_dteto = $this->input->get_post('fld_ulogs_dteto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_rcvulogs_recs($mpages,20,'',$fld_ulogstrxno,$fld_ulogs_dtefrom,$fld_ulogs_dteto,$fld_utrx);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-ulogs-recs',$data);
	}
	//POSTING PROCESS RCVNG
	public function grpo_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='170'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-grpo');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}

	public function grpo_vw_gro(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='170'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-grpo-gro');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function grpo_proc(){
		$fld_grpobranch = $this->input->get_post('fld_grpobranch');
		$fld_grpobranch_id = $this->input->get_post('fld_grpobranch_id');
		$fld_grpodtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_grpodtfrm'));
		$fld_grpodtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_grpodtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_grpo_recs($mpages,20,$fld_grpobranch,$fld_grpobranch_id,$fld_grpodtfrm,$fld_grpodtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-grpo-recs',$data);
	}

	public function grpo_proc_gro(){
		$fld_grpobranch = $this->input->get_post('fld_grpobranch');
		$fld_grpobranch_id = $this->input->get_post('fld_grpobranch_id');
		$fld_grpodtfrm = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_grpodtfrm'));
		$fld_grpodtto = $this->mylibz->mydate_yyyymmdd($this->input->get_post('fld_grpodtto'));
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_grpo_recs($mpages,20,$fld_grpobranch,$fld_grpobranch_id,$fld_grpodtfrm,$fld_grpodtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-grpo-recs-gro',$data);
	}
	public function grpo_recs() { 
		$fld_grpobranch = $this->input->get_post('fld_grpobranch');
		$fld_grpobranch_id = $this->input->get_post('fld_grpobranch_id');
		$fld_grpodtfrm = $this->input->get_post('fld_grpodtfrm');
		$fld_grpodtto = $this->input->get_post('fld_grpodtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct->view_grpo_recs($mpages,20,$fld_grpobranch,$fld_grpobranch_id,$fld_grpodtfrm,$fld_grpodtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-grpo-recs',$data);
	}

	public function grpo_recs_gro() { 
		$fld_grpobranch = $this->input->get_post('fld_grpobranch');
		$fld_grpobranch_id = $this->input->get_post('fld_grpobranch_id');
		$fld_grpodtfrm = $this->input->get_post('fld_grpodtfrm');
		$fld_grpodtto = $this->input->get_post('fld_grpodtto');
		
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctgro->view_grpo_recs($mpages,20,$fld_grpobranch,$fld_grpobranch_id,$fld_grpodtfrm,$fld_grpodtto);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-grpo-recs-gro',$data);
	}
	public function grpo_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='170'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs/rcv_goods_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
 	
 	}
 	//DASH 5
	public function dash5_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='171'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash5');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}

	public function dash5_vw_gro(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='171'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash5-gro');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}

	public function dash5_recs(){
		$this->mymdacct_dash1->view_dash5_recs();
	}

	public function dash5_recs_gro(){
		$this->mymdacct_dash1->view_dash5_recs();
	}

	public function myacct_vw_drcvng5(){
		$mtkn = $this->input->get_post('mtkn');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct_dash1->view_drcvng5_recs($mpages,20,$mtkn);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash5-recs',$data);
	}
  

	public function myacct_vw_drcvng5_gro(){
		$mtkn = $this->input->get_post('mtkn');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct_dash1->view_drcvng5_recs($mpages,20,$mtkn);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dash5-recs-gro',$data);
	}
	//DASH OLT
	public function dasholt_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='214'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-oltdash');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function dasholt_recs(){
		$this->mymdacct_dash1->view_dasholt_recs();
	}
	public function myacct_vw_dasholt(){
		$mtkn = $this->input->get_post('mtkn');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct_dash1->view_ddasholt_recs($mpages,20,$mtkn);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-oltdash-recs',$data);
	}
	//DASH BR
	public function dashbr_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='215'","myua_acct");
		if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dashbr');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}
	public function dashbr_recs(){
		$this->mymdacct_dash1->view_dashbr_recs();
	}
	public function myacct_vw_dashbr(){
		$mtkn = $this->input->get_post('mtkn');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacct_dash1->view_ddashbr_recs($mpages,20,$mtkn);
		$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dashbr-recs',$data);
	}

	public function rcv_file_claims(){
		$this->mymdacct->_rcv_file_claims();
	}

	////DASH 5 claims
	public function medash5_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='63'","myua_acct");
		$result = 1;
		if($result == 1){
		
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-cdash5-vw');
		}else{
			$this->load->view('unauthorized_sm');	
		}
	}

	public function myrcv_claims_dash(){
		//var_dump('hello mada facker');
		$this->mymdacct->_rcv_claims_dash();
	}
	public function myrcv_claims_recs(){

	$mpages = $this->input->get_post('mpages');
	$mpages = (empty($mpages) ? 0: $mpages);
	$data = $this->mymdacct->_rcv_claims_dash_recs($mpages,20);
	$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-cdash5-recs',$data);

	}
	public function myrcv_claims_dl(){
		$this->mymdacct->_rcv_claims_dash_rpt();

	}
	//DASH POUT TPD
	public function dashtpd_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
		//if($result == 1){
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-tpddash');
	// 	}else{
	// 		$this->load->view('unauthorized_sm');	
	// 	}
	// }
	}
	
	public function dashtpd_recs(){
		$this->mymdacctpo_dash1->view_dashtpd_recs();
	}
	public function myacct_vw_dashtpd(){
		$mtkn = $this->input->get_post('mtkn');
		/*$fnal = $this->input->get_post('fnal');
		$posted = $this->input->get_post('posted');*/
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdacctpo_dash1->view_dtpd_recs($mpages,20,$mtkn);
		$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-tpddash-recs',$data);
	}
	//check done
	public function acct_man_recs_po_isdone() { 
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='213'","myua_acct");
		if($result != 1){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
			die();
		}
		$this->mymdacctpo_dash1->tpd_donepullout();
	}

	public function myrcv_claims_validate(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='220'","myua_acct");
		if($result == 1){
		$this->mymdacct->_rcv_claims_validate();
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong>You don't have permission to access this Module.!!!.</div>";
			return;
		}

	}

	public function myrcv_claims_validate_dl(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='221'","myua_acct");
		if($result == 1){
		$this->mymdacct->_rcv_claims_validate_dl();
		}
		else{
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong>You don't have permission to access this Module.!!!.</div>";
			return;
		}

	}
	//Receiving - RFP Dashboard
	public function RcvRFPdashboardFigureSet(){
		$_comp = $this->input->get_post('_comp');
		$_brnch = $this->input->get_post('_brnch');
		$_enttyp = $this->input->get_post('_enttyp');
		$_dtefrm = $this->input->get_post('_dtefrm');
		$_dteto = $this->input->get_post('_dteto');
		$data = $this->myrcvng->vw_rcv_dshbrd($_comp,$_brnch,$_enttyp,$_dtefrm,$_dteto);
		$this->load->view('masterdata/acct_mod/man_recs/rfp_inv_dshbrd_fgrs',$data);
	}

	//Receiving - RFP Dashboard
	public function RcvRFPdashboardFigure(){
		$txtsearchedrec = $this->input->get_post('txtsearchedrec');
		$mpages = $this->input->get_post('mpages');
		$mpages = (empty($mpages) ? 1: $mpages);
		$_comp = $this->input->get_post('_comp');
		$_brnch = $this->input->get_post('_brnch');
		$_enttyp = $this->input->get_post('_enttyp');
		$_dtefrm = $this->input->get_post('_dtefrm');
		$_dteto = $this->input->get_post('_dteto');	
		$_fgrtyp = $this->input->get_post('_fgrtyp');
		$data = $this->myrcvng->RCVRFPDshbrdview($_comp,$_brnch,$_enttyp,$_dtefrm,$_dteto,$_fgrtyp,$mpages,10,$txtsearchedrec);
		$this->load->view('masterdata/acct_mod/man_recs/rcv_rfp_dshbrd_fgrs',$data);
	}

	public function RCVRFPDshbrd_dl() { 
		$this->myrcvng->RCVRFPDshbrdvw_dl();
	}//end func
	
	public function rqstPRT() { 
		$httpsSet = $this->dbx->escape_str(trim($this->input->get_post('httpsSet')));
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaap_id='22'","myua_ap");
		if($result != 1){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>It appears that you don't have permission to access this page.</br><strong>Note:</strong>If you think you should be able to view this page, please contact your administrator.</div>";
			die();
		}
		echo "<div class=\"alert alert-info\" role=\"alert\"><strong>Info.<br/></strong><strong>Redirecting...</strong></div>
		<script language='javascript'>
		var httpsSet = \"$httpsSet\";
		window.location.href=httpsSet;
		</script>";
	}

	public function rfp_dshbrd_vw(){
		 $this->load->view('masterdata/acct_mod/man_recs/myacct_rcv_rfp_dshbrd');
	}
////////////////////////////////////////FOR RECEIVING///////////////////////////////////
	//GOODS RECEIVED ADJUSTMENT
	public function goods_dshbrd_vw(){
		
		$data = $this->mymdacctgro->view_recs_goods(1,20);
        $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-goods-recs',$data);
	}
	//BUNDLING PERMIT
	public function bpermit_dshbrd_vw(){
		
		$data = $this->mymdacctgro->view_recs(1,20);
        $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-bpermit',$data);
	}
	//ADJUSTMENT FORM
	public function adjustment_dshbrd_vw(){
		
		$data = $this->mymdacctgro->view_recs(1,20);
        $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-adjustment',$data);
	}
	// DELIVERY DISCREPANCY REPORT
	public function dlvry_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
	    $data = $this->mymdacctgro->view_recs_ddlvry(1,20);
	    
			// $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dlvry');
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-dlvry-recs',$data);
	}

	public function rpts_ddr_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs_gro-rpts-ddr');
	 }

 	public function rpts_tdr_gro_vw(){
	 	$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs_gro-rpts-tdr');
	 }
	// RTV
	public function rtv_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
		 $data = $this->mymdacctpogro->view_recs_rtv(1,20);	

			// $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-rtv');
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-rtv-recs',$data);
			
	}
	//IN STORE BUNDLING-PREMIUM ADJUSTMENT
	public function premium_vw(){
		
		$data = $this->mymdacctpogro->view_recs(1,20);
        $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-premium-recs',$data);
	}

	//IN STORE BUNDLING-REGULAR TO PREMIUM ADJUSTMENT
	public function reg_to_premium_vw(){
		
		$data = $this->mymdacctpogro->view_recs(1,20);
        $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-regpremium-recs',$data);
	}
////////////////////////////////////////////FOR PULL OUT//////////////////////////////////////////	
	// STORE TO STORE TRANSFER
	public function store_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
		$data = $this->mymdacctpogro->view_recs_sts(1,20);
		
			// $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-sts');
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-sts-recs_po',$data);
	}
	// TRANSFER DISCREPANCY ADJUSTMENT
	public function transfer_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
		$data = $this->mymdacctgro->view_recs_dtransfer(1,20);

			// $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-transfer');
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-transfer-recs',$data);
	}

	// TRANSFER DISCREPANCY REPORT
	public function transfer_rpt_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
		$data = $this->mymdacctgro->view_recs_dtransfer(1,20);

			// $this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-transfer');
			$this->load->view('masterdata/acct_mod/man_recs/myacct_manrecs-transfer-rpt-recs',$data);
	}
	// DAMAGE AND LOSS REPORT
	public function dmglss_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
		$data = $this->mymdacctpogro->view_recs_dmglss(1,20);
			
			// $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-dmglss');
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-dmglss-recs_po',$data);
	}
	// REJECTION LOGBOOK
	public function rjction_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
			
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-rjction');
	}
	// PULL OUT ADJUSTMENT
	public function adjst_vw(){
		// $result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");

			$data = $this->mymdacctpogro->view_recs(1,20);
			// $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-poadjstmnt');
            $this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-poadjstmnt-recs_po',$data);
	}
	// GATE PASS
	public function gpass_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
			
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-gpass');
	}
	// HIGH VALUE ITEMS
	public function hghval_vw(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='212'","myua_acct");
			
			$this->load->view('masterdata/acct_mod/man_recs_po/myacct_manrecs-hghval');
	}

	// FOR GOODS RECEIVED ADJUSTMENT
  	public function rpts_print_goods(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
	if($result == 1){
		$this->load->library('fpdf/mypdf');
 		$this->load->view('masterdata/acct_mod/man_recs/rcv_goods_print_pdf');
	}else{
		redirect('mytrx_acct/unathorized_vw');
	}
 	
 	}//endfunc
 	// FOR DELIVERY DISCREPANCY REPORT
	 public function rpts_print_delivery(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs/dlvry_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc
	 // FOR STORE TO STORE TRANSFER
   	public function rpts_print_sts(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
	if($result == 1){
		$this->load->library('fpdf/mypdf');
 		$this->load->view('masterdata/acct_mod/man_recs_po/sts_print_pdf');
	}else{
		redirect('mytrx_acct/unathorized_vw');
	}
 	
 	}//endfunc
 	// FOR PULL OUT ADJUSTMENT
	 public function rpts_print_poadjstmnt(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/poadjstmnt_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc
	 // FOR TRANSFER DISCREPANCY ADJUSTMENT
	//  public function rpts_print_transfer(){
	//  	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
	// if($result == 1){
	// 	$this->load->library('fpdf/mypdf');
 // 		$this->load->view('masterdata/acct_mod/man_recs/transferd_print_pdf');
	// }else{
	// 	redirect('mytrx_acct/unathorized_vw');
	// }
 	
 // 	}//endfunc

 	// FOR TRANSFER DISCREPANCY REPORT
    public function rpts_print_transfer(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
	if($result == 1){
		$this->load->library('fpdf/mypdf');
 		$this->load->view('masterdata/acct_mod/man_recs/transfer_print_pdf');
	}else{
		redirect('mytrx_acct/unathorized_vw');
	}
 	
 	}//endfunc
	// FOR TRANSFER DISCREPANCY REPORT
    public function rpts_print_transfer_rpt(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
	if($result == 1){
		$this->load->library('fpdf/mypdf');
 		$this->load->view('masterdata/acct_mod/man_recs/transfer_rpt_print_pdf');
	}else{
		redirect('mytrx_acct/unathorized_vw');
	}
 	
 	}//endfunc
 	// FOR IN STORE PREMIUM
 	public function rpts_print_premium(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
	if($result == 1){
		$this->load->library('fpdf/mypdf');
 		$this->load->view('masterdata/acct_mod/man_recs_po/premium_print_pdf');
	}else{
		redirect('mytrx_acct/unathorized_vw');
	}
 	
 	}//end func
	// FOR IN STORE REGULAR PREMIUM
	public function rpts_print_regpremium(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
			$this->load->view('masterdata/acct_mod/man_recs_po/reg-to-premium_print_pdf');
		}else{
			redirect('mytrx_acct/unauthorized_vw');
		}

		}//end func
	// FOR RETURN TO VENDOR
	public function rpts_print_rtv(){
		$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
			$this->load->view('masterdata/acct_mod/man_recs_po/rtv_print_pdf');
		}else{
			redirect('mytrx_acct/unauthorized_vw');
		}

		}//end func	

	public function stslog_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/stslog_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc	

  	public function dnllog_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/dnllog_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc

   	public function rejlog_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/rejlog_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc

	public function polog_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/polog_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc

 	public function gplog_print(){
	 	$result = $this->mydatazua->get_Active_menus($this->db_erp,$this->sysuaid,"myuaacct_id='20'","myua_acct");
		if($result == 1){
			$this->load->library('fpdf/mypdf');
	 		$this->load->view('masterdata/acct_mod/man_recs_po/gplog_print_pdf');
		}else{
			redirect('mytrx_acct/unathorized_vw');
		}
	 	
	 }//endfunc
		
}//end mod
?>



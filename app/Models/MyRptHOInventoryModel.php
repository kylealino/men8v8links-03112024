<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyDatumModel;
use App\Models\MyUserModel;
class MyRptHOInventoryModel extends Model
{
	public function __construct()
	{ 
		parent::__construct();
		$this->request = \Config\Services::request();
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->db_erp_br = $this->mydbname->medb(1);
		$this->db_temp = $this->mydbname->medb(2);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->mydatum =  new MyDatumModel();
		$this->myusermod =  new MyUserModel();
		$this->cusergrp = $this->myusermod->mysys_usergrp();
		$this->cuser = $this->myusermod->mysys_user();
		$this->mpw_tkn = $this->myusermod->mpw_tkn();
		$this->mylibzsys = model('App\Models\MyLibzSysModel');
	}	
	
	public function detailed_gen($npages = 1,$npagelimit = 30,$msearchrec='',$lArtmU=0,$metkntmp='') { 
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$bid_mtknattr = $this->request->getVar('bid_mtknattr');
		$fld_branch = $this->mylibzdb->me_escapeString($this->request->getVar('fld_branch'));
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$cuser,'02','0004','00070701')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}  //end if
		
		$data = array();
		$str_optn = "";
		if (!empty($msearchrec)): 
			$msearchrec = $this->mylibzdb->me_escapeString($msearchrec);
			$str_optn = " where (`ITEMC` = '$msearchrec'  or `ITEM_BARCODE` = '$msearchrec' or  `ITEM_DESC` like '%{$msearchrec}%') ";
		endif;
		
		if(!empty($fld_branch) && !empty($bid_mtknattr)) { 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_branch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$bid_mtknattr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.trx_E{$br_ocode2}_myivty_lb_dtl";
			if(!empty($metkntmp)):
				$tblivty = "{$this->db_temp}.meivtytmp_{$metkntmp}";
			endif;
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
			//END BRANCH
		} else { 
			echo "Branch is INVALID!!!";
			die();
		} // end if
		
		//this should be process only once by trigerring generate or process button the form entry
		if($lArtmU) { 
			if($lperbr) { 

				$tbltemp = $this->db_temp . ".`artm_gro_" . $this->mylibz->random_string(15) . "`";
				$str = "drop table if exists {$tbltemp}";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$str = "CREATE TABLE IF NOT EXISTS {$tbltemp} ( 
				  `recid` int(25) NOT NULL AUTO_INCREMENT,
				  `ITEMC` varchar(35) NOT NULL,
				  `ITEM_BARCODE` varchar(18) DEFAULT '',
				  `ITEM_DESC` varchar(150) DEFAULT '',
				  `ITEMC_DESCC` varchar(35) DEFAULT '',
				  `ITEM_COST` double(15,4) DEFAULT 0.0000,
				  `ITEM_PRICE` double(15,4) DEFAULT 0.0000,
				  PRIMARY KEY (`recid`),
				  KEY `idx01` (`ITEMC`) 
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
				
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$str = "insert into {$tbltemp} (
				`ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,`ITEMC_DESCC`,`ITEM_COST`,`ITEM_PRICE`
				) 
				select itm.ART_CODE,itm.ART_BARCODE1,itm.ART_DESC,itm.ART_DESC_CODE,kk.art_cost,kk.art_uprice 
				from {$this->db_erp}.mst_article itm join {$this->db_erp}.`mst_article_per_branch` kk ON (itm.`recid` = kk.`artID`) 
				where itm.`ART_HIERC1` = '0600' and kk.`brnchID` = {$br_id}  
				";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$str = "update {$tblivty} aa join {$tbltemp} bb on(aa.`ITEMC` = bb.`ITEMC`) 
				SET aa.ITEM_DESC = bb.ITEM_DESC,
				aa.ITEM_BARCODE = bb.ITEM_BARCODE,
				aa.MARTM_COST = bb.ITEM_COST,
				aa.MARTM_PRICE = bb.ITEM_PRICE,
				aa.`ITEMC_DESCC` = bb.ITEMC_DESCC";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg4_hd_new cc on(cc.`MAT_CATG1_CODE` = itm.ART_HIERC1 and cc.`MAT_CATG2_CODE` = itm.ART_HIERC2 and cc.`MAT_CATG3_CODE` = itm.ART_HIERC3 and cc.`MAT_CATG4_CODE` = itm.ART_HIERC4) 
				SET aa.ITEMC_HIER1 = cc.`MAT_CATG1_DESC`,
				aa.ITEMC_HIER2 = cc.`MAT_CATG2_DESC`,
				aa.ITEMC_HIER3 = cc.`MAT_CATG3_DESC`,
				aa.ITEMC_HIER4 = cc.`MAT_CATG4_DESC`";

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg1_hd cc on(cc.`MAT_CATG1_CODE` = itm.ART_HIERC1) 
				SET aa.ITEMC_HIER1 = cc.`MAT_CATG1_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg2_hd cc on(cc.`MAT_CATG2_CODE` = itm.ART_HIERC2) 
				SET aa.ITEMC_HIER2 = cc.`MAT_CATG2_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg3_hd cc on(cc.`MAT_CATG3_CODE` = itm.ART_HIERC3) 
				SET aa.ITEMC_HIER3 = cc.`MAT_CATG3_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg4_hd cc on(cc.`MAT_CATG4_CODE` = itm.ART_HIERC4) 
				SET aa.ITEMC_HIER4 = cc.`MAT_CATG4_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				if ($cuser == '181-1'):  //for debugging purposes 
					//echo "{$tbltemp}";
					$str = "drop table if exists {$tbltemp}";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				else: 
					$str = "drop table if exists {$tbltemp}";
					$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				endif;
				
			} else { 
				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				SET aa.ITEM_DESC = itm.ART_DESC,
				aa.ITEM_BARCODE = itm.ART_BARCODE1,
				aa.MARTM_COST = itm.ART_UCOST,
				aa.MARTM_PRICE = itm.ART_UPRICE,
				aa.`ITEMC_DESCC` = itm.ART_DESC_CODE";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg4_hd_new cc on(cc.`MAT_CATG1_CODE` = itm.ART_HIERC1 and cc.`MAT_CATG2_CODE` = itm.ART_HIERC2 and cc.`MAT_CATG3_CODE` = itm.ART_HIERC3 and cc.`MAT_CATG4_CODE` = itm.ART_HIERC4) 
				SET aa.ITEMC_HIER1 = cc.`MAT_CATG1_DESC`,
				aa.ITEMC_HIER2 = cc.`MAT_CATG2_DESC`,
				aa.ITEMC_HIER3 = cc.`MAT_CATG3_DESC`,
				aa.ITEMC_HIER4 = cc.`MAT_CATG4_DESC`";
				//$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg1_hd cc on(cc.`MAT_CATG1_CODE` = itm.ART_HIERC1) 
				SET aa.ITEMC_HIER1 = cc.`MAT_CATG1_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg2_hd cc on(cc.`MAT_CATG2_CODE` = itm.ART_HIERC2) 
				SET aa.ITEMC_HIER2 = cc.`MAT_CATG2_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg3_hd cc on(cc.`MAT_CATG3_CODE` = itm.ART_HIERC3) 
				SET aa.ITEMC_HIER3 = cc.`MAT_CATG3_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

				$str = "update {$tblivty} aa join {$this->db_erp}.mst_article itm on(aa.`ITEMC` = itm.`ART_CODE`) 
				join {$this->db_erp}.mst_mat_catg4_hd cc on(cc.`MAT_CATG4_CODE` = itm.ART_HIERC4) 
				SET aa.ITEMC_HIER4 = cc.`MAT_CATG4_DESC` ";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				
			}
		} //end if update the latest pricing and costing

		$strqryxx = "
		select `ITEMC`,`ITEM_DESC`,`MARTM_COST`,`ITEM_BARCODE`,`MARTM_PRICE`,
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'GEN-IVTYC',1,0)), -- items have general inventory count 
		(sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from " . $tblivty . " {$str_optn} group by `ITEMC` 
		";

	

		$strqry = "
		select sha2(concat(`recid`,'{$mpw_tkn}'),384) mtkn_recid,`ITEMC`,`ITEM_DESC`,`MARTM_COST`,ifnull(`ITEM_BARCODE`,'') ITEM_BARCODE,`MARTM_PRICE`,
		`ITEMC_HIER1`,`ITEMC_HIER2`,`ITEMC_HIER3`,`ITEMC_HIER4`,`ITEMC_DESCC`,
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		else 0 end ) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'GEN-IVTYC',1,0)), -- items have general inventory count 
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from " . $tblivty . " {$str_optn} group by `ITEMC` 
		";
		
		
		$str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = (($npagelimit * ($npages - 1)) > 0 ? ($npagelimit * ($npages - 1)) : 0);
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		
		$str = "
		SELECT oa.*,`ITEM_DESC` `ART_DESC`,MARTM_COST `ITEM_COST`,MARTM_PRICE `ITEM_PRICE`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * `END_BAL_QTY`) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * oa. `END_BAL_QTY`) end ) ITEM_AMT_PRICE 
		from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
		
		$strg = "
		SELECT oa.*,`ITEM_DESC` `ART_DESC`,MARTM_COST `ITEM_COST`,MARTM_PRICE `ITEM_PRICE`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * `END_BAL_QTY`) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * oa. `END_BAL_QTY`) end ) ITEM_AMT_PRICE 
		from ({$strqry}) oa 	
		";
		
		$strnobal = "
		SELECT count(*) nrecs 
		from ({$strqry}) oa where `END_BAL_QTY` < 0";
		$qnb = $this->mylibzdb->myoa_sql_exec($strnobal,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rwnb = $qnb->getRowArray();
		$G_NOBAL_ITEMS = (empty($rwnb['nrecs']) ? 0 : $rwnb['nrecs']);
		$qnb->freeResult();
		
		
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->getNumRows() > 0) { 
			$data['rlist'] = $qry->getResultArray();
			$data['fld_branch'] = $fld_branch;
			$data['mtknbrid'] = $bid_mtknattr;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
			$data['fld_branch'] = $fld_branch;
			$data['mtknbrid'] = $bid_mtknattr;
		}
		
		$qry->freeResult();	
		$str = "select sum(`BEG_QTY`) `G_BEG_QTY`,
		sum(`GEN_IVTYC`) `G_GEN_IVTYC`,
		sum(`GEN_IVTYC_DIFF`) `G_GEN_IVTYC_DIFF`,
		sum(`CYC-ADJ_QTY`) `G_CYC_ADJ_QTY`,
		sum(`RCV_QTY`) `G_RCV_QTY`,
		sum(`CLM_QTY`) `G_CLM_QTY`,
		sum(`RCV-S_QTY`) `G_RCV_S_QTY`,
		sum(`RCV-M_QTY`) `G_RCV_M_QTY`,
		sum(`RCV-C_QTY`) `G_RCV_C_QTY`,
		sum(`RCV-R_QTY`) `G_RCV_R_QTY`,
		sum(`SALES_QTY`) `G_SALES_QTY`,
		sum(`B1T1_QTY`) `G_B1T1_QTY`,
		sum(`DSP_QTY`) `G_DSP_QTY`,
		sum(`BRG_QTY`) `G_BRG_QTY`,
		sum(`GVA_QTY`) `G_GVA_QTY`,
		sum(`TO_QTY`) `G_TO_QTY`,
		sum(`TOB_QTY`) `G_TOB_QTY`,
		sum(`RTML_QTY`) `G_RTML_QTY`,
		sum(`POSU_QTY`) `G_POSU_QTY`,
		sum(`POOTH_QTY`) `G_POOTH_QTY`,
		sum(`END_BAL_QTY`) `G_END_BAL_QTY`,
		sum(`ITEM_AMT_COST`) `G_ITEM_AMT_COST`,
		sum(`ITEM_AMT_PRICE`) `G_ITEM_AMT_PRICE` 
		 from 
		 ({$strg}) me";
		$qq = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qq->getRowArray();
		$data['G_BEG_QTY'] = $rw['G_BEG_QTY']; 
		$data['G_GEN_IVTYC'] = $rw['G_GEN_IVTYC']; 
		$data['G_GEN_IVTYC_DIFF'] = $rw['G_GEN_IVTYC_DIFF']; 
		$data['G_CYC_ADJ_QTY'] = $rw['G_CYC_ADJ_QTY']; 
		$data['G_RCV_QTY'] = $rw['G_RCV_QTY']; 
		$data['G_CLM_QTY'] = $rw['G_CLM_QTY']; 
		$data['G_RCV_S_QTY'] = $rw['G_RCV_S_QTY']; 
		$data['G_RCV_M_QTY'] = $rw['G_RCV_M_QTY']; 
		$data['G_RCV_C_QTY'] = $rw['G_RCV_C_QTY']; 
		$data['G_RCV_R_QTY'] = $rw['G_RCV_R_QTY']; 
		$data['G_SALES_QTY'] = $rw['G_SALES_QTY']; 
		$data['G_B1T1_QTY'] = $rw['G_B1T1_QTY']; 
		$data['G_DSP_QTY'] = $rw['G_DSP_QTY']; 
		$data['G_BRG_QTY'] = $rw['G_BRG_QTY']; 
		$data['G_GVA_QTY'] = $rw['G_GVA_QTY']; 
		$data['G_TO_QTY'] = $rw['G_TO_QTY']; 
		$data['G_TOB_QTY'] = $rw['G_TOB_QTY']; 
		$data['G_RTML_QTY'] = $rw['G_RTML_QTY']; 
		$data['G_POSU_QTY'] = $rw['G_POSU_QTY']; 
		$data['G_POOTH_QTY'] = $rw['G_POOTH_QTY']; 
		$data['G_END_BAL_QTY'] = $rw['G_END_BAL_QTY']; 
		$data['G_ITEM_AMT_COST'] = $rw['G_ITEM_AMT_COST']; 
		$data['G_ITEM_AMT_PRICE'] = $rw['G_ITEM_AMT_PRICE']; 
		$data['G_NOBAL_ITEMS'] = $G_NOBAL_ITEMS; 
		$data['metkntmp'] = $metkntmp;
		$qq->freeResult();
		return $data;
	} //end detailed_gen


	
	public function ivty_item_detl_delete() { 
		$mtkn_recid = $this->request->getVar('mtkn_recid');
		$bid_mtknattr = $this->request->getVar('bid_mtknattr'); 
		$fld_branch = $this->mylibzdb->me_escapeString($this->request->getVar('fld_branch'));

		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00040210')) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>";
			die();
		}  //end if
		
		if(!empty($fld_branch) && !empty($bid_mtknattr)) { 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_branch' AND sha2(concat(aa.recid,'{$this->mpw_tkn}'),384) = '$bid_mtknattr'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_GEN','',$this->cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$tblivty_dtl_del_logs = "{$this->db_erp_br}.trx_E{$br_ocode2}_myivty_lb_dtl_del_logs";
			$tblivty = "{$this->db_erp_br}.trx_E{$br_ocode2}_myivty_lb_dtl";
			$tblivty_pmo = "{$this->db_erp_br}.trx_E{$br_ocode2}_myivty_lb_dtl_pmo";
			$q->freeResult();
			//END BRANCH
		} else { 
			echo "Branch is INVALID!!!";
			die();
		} // end if


		$str = "CREATE TABLE IF NOT EXISTS {$tblivty_dtl_del_logs} ( 
			`recid` int(9) NOT NULL AUTO_INCREMENT,
			`id` int(9) NOT NULL,
			`MBRANCH_ID` int(9) NOT NULL,
			`ITEMC` varchar(35) DEFAULT '',
			`ITEM_BARCODE` varchar(18) DEFAULT '',
			`ITEM_DESC` varchar(150) DEFAULT '',
			`MQTY` double(15,4) DEFAULT 0.0000,
			`MQTY_CORRECTED` double(15,4) DEFAULT 0.0000,
			`MCOST` double(15,4) DEFAULT 0.0000,
			`MSRP` double(15,4) DEFAULT 0.0000,
			`SO_GROSS` double(15,4) DEFAULT 0.0000,
			`SO_NET` double(15,4) DEFAULT 0.0000,
			`MARTM_COST` double(15,4) DEFAULT 0.0000,
			`MARTM_PRICE` double(15,4) DEFAULT 0.0000,
			`MTYPE` varchar(10) DEFAULT '',
			`MFORM_SIGN` varchar(2) DEFAULT '',
			`ITEMC_HIER1` varchar(150) DEFAULT '',
			`ITEMC_HIER2` varchar(150) DEFAULT '',
			`ITEMC_HIER3` varchar(150) DEFAULT '',
			`ITEMC_HIER4` varchar(150) DEFAULT '',
			`ITEMC_DESCC` varchar(150) DEFAULT '',
			`MUSER` varchar(30) DEFAULT '',
			`MLASTDELVD` datetime,
			`MPROCDATE` timestamp,
			`mencd_dlt` timestamp DEFAULT current_timestamp(),
			`muser_dlt` varchar(25) DEFAULT '',
			 PRIMARY KEY (`recid`),
			 KEY `idx01` (`ITEMC`) 
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
		  
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	
		$str = "select `recid`, `ITEMC`  FROM {$tblivty} where sha2(concat(`recid`,'{$this->mpw_tkn}'),384) = '{$mtkn_recid}'";
        $qry = $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
		$me_ITEMC = '';
		if($qry->resultID->num_rows == 0) { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Failed</strong> This item is already deleted!!!.</div>";
			die;
		}else{
			$rw = $qry->getRowArray(); 
			$me_ITEMC = $rw['ITEMC'];
		}
		$qry->freeResult();


		$str = "insert into {$tblivty_dtl_del_logs} (
		`id`,`MBRANCH_ID`,`ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,`MQTY`,`MQTY_CORRECTED`, `MCOST`, `MSRP`,`SO_GROSS`,`SO_NET`,
		`MARTM_COST`,`MARTM_PRICE`,`MTYPE`,`MFORM_SIGN`,`ITEMC_HIER1`,`ITEMC_HIER2`,`ITEMC_HIER3`,`ITEMC_HIER4`,`ITEMC_DESCC`,
		`MUSER`,`MLASTDELVD`,`MPROCDATE`,`muser_dlt`) 
		select `recid`,`MBRANCH_ID`,`ITEMC`,`ITEM_BARCODE`,`ITEM_DESC`,`MQTY`,`MQTY_CORRECTED`, `MCOST`, `MSRP`,`SO_GROSS`,`SO_NET`,
		`MARTM_COST`,`MARTM_PRICE`,`MTYPE`,`MFORM_SIGN`,`ITEMC_HIER1`,`ITEMC_HIER2`,`ITEMC_HIER3`,`ITEMC_HIER4`,`ITEMC_DESCC`,
		`MUSER`,`MLASTDELVD`,`MPROCDATE`,'{$this->cuser}'
		from {$tblivty} 
		where sha2(concat(`recid`,'{$this->mpw_tkn}'),384) = '{$mtkn_recid}'";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		$str = "DELETE FROM {$tblivty} where sha2(concat(`recid`,'{$this->mpw_tkn}'),384) = '{$mtkn_recid}'";
        $this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_ITM_DTL_DEL',$me_ITEMC,$this->cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		echo "<div class=\"alert alert-success mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Success</strong> Item has been successfully deleted!!! </div>";
		
	} // end ivty_item_detl_delete
	
	public function detailed_download() {
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		
		$mdl_me_branch = $this->request->getVar('mdl_me_branch');
		$mdl_me_branch_mtkn = $this->request->getVar('mdl_me_branch_mtkn');
		$mdl_metkntmp = $this->request->getVar('mdl_metkntmp');
		if(!empty($mdl_me_branch) && !empty($mdl_me_branch_mtkn)) {
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$mdl_me_branch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$mdl_me_branch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_DTL_DWNLD','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_E{$br_ocode2}_myivty_lb_dtl`";
			if(!empty($mdl_metkntmp)):
				$tblivty = "{$this->db_temp}.`meivtytmp_{$mdl_metkntmp}`";
			endif;			
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
		} //end if
		
		$dloadpath = ROOTPATH . 'public/downloads/me/';
		$mfile = $dloadpath . 'ivty_dtl_dload_' . $this->mylibz->random_string(15) . '.txt';
		if (file_exists($mfile)) { 
			unlink($mfile);
		}
		
		$str_END_BAL_QTY = "
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		)
		";
		
		$str = "
		SELECT * INTO OUTFILE '{$mfile}'
		  FIELDS TERMINATED BY '\t' 
		  LINES TERMINATED BY '\n'
		FROM (
		select 'Item Code','Item Barcode','Item Description','Cost', 'Srp','Beginning Balance','General Inventory (Physical Count)','General Inventory Discrepancy QTY','Adjusted Cycle Counting','Receiving (Deliveries)',
		'Claims','Receiving (Store Use)','Receiving (Membership)','Receiving (Change Price)','Receiving (Rcv in frm PO)','Sales Out',
		'Pull Out (Buy1Take1)','Pull Out (Dispose)','Pull Out (For Bargain)','Pull Out (Giveaways)','Pull Out (Inventory Transfer Out)',
		'Pull Out (Pull Out to Other Branch)','Pull Out (Return to Mapulang Lupa)','Pull Out (Store-Use)','Pull Out (Others)','Ending Balance',
		'Cost Amount','SRP Amount' 
		union all 
		select `ITEMC`,ifnull(`ITEM_BARCODE`,''),`ITEM_DESC`, ifnull(`MARTM_COST`,''),ifnull(`MARTM_PRICE`,''),
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		{$str_END_BAL_QTY} `END_BAL_QTY`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * {$str_END_BAL_QTY}) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * {$str_END_BAL_QTY}) end ) ITEM_AMT_PRICE 
		 from " . $tblivty . " group by `ITEMC` 
		) oa 
		";
	
		$str = "
		SELECT * INTO OUTFILE '{$mfile}'
		  FIELDS TERMINATED BY '\t' 
		  LINES TERMINATED BY '\n'
		FROM (
		select 'Item Code','Item Barcode','Item Description', 'Cost', 'Srp','Product Section','Desc Code','Beginning Balance','General Inventory (Physical Count)','General Inventory Discrepancy QTY','Adjusted Cycle Counting','Receiving (Deliveries)',
		'Claims','Receiving (Store Use)','Receiving (Membership)','Receiving (Change Price)','Receiving (Rcv in frm PO)','Sales Out',
		'Pull Out (Buy1Take1)','Pull Out (Dispose)','Pull Out (For Bargain)','Pull Out (Giveaways)','Pull Out (Inventory Transfer Out)',
		'Pull Out (Pull Out to Other Branch)','Pull Out (Return to Mapulang Lupa)','Pull Out (Store-Use)','Pull Out (Others)','Ending Balance',
		'Cost Amount','SRP Amount' 
		union all 
		select `ITEMC`,ifnull(`ITEM_BARCODE`,''),ifnull(`ITEM_DESC`,''),ifnull(`MARTM_COST`,''),ifnull(`MARTM_PRICE`,''),ifnull(`ITEMC_HIER2`,''),ifnull(`ITEMC_DESCC`,''),
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) `BEG_QTY`, -- Beginning QTY 
		sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `GEN_IVTYC`, -- General Inventory thru Physical Count QTY 
		(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		else 0 end ) `GEN_IVTYC_DIFF`, -- General Inventory Discrepancy QTY 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `CYC-ADJ_QTY`, -- Adjusted Cycle Counting 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `RCV_QTY`, -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'RCV',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `CLM_QTY`,  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `RCV-S_QTY`, -- received from Store Use 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `RCV-M_QTY`, -- received from Membership 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `RCV-C_QTY`,  -- received from Change Price 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `RCV-R_QTY`, -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `SALES_QTY`, -- Sales  
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `B1T1_QTY`, -- PO Buy 1 Take 1  
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `DSP_QTY`, -- PO Dispose 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `BRG_QTY`, -- PO Bargain  
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `GVA_QTY`, -- PO Give Aways  
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `TO_QTY`, -- PO Transfer Out 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `TOB_QTY`, -- PO Transfer Out to Other Branch 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `RTML_QTY`, -- PO Return to Mapulang Lupa WSHE 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `POSU_QTY`, -- PO Store Use 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `POOTH_QTY`, -- PO Store Use 
		{$str_END_BAL_QTY} `END_BAL_QTY`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * {$str_END_BAL_QTY}) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * {$str_END_BAL_QTY}) end ) ITEM_AMT_PRICE 
		 from " . $tblivty . " group by `ITEMC` 
		) oa 
		";
		
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		//Clear system output buffer
		//flush();
		
		//Define header information
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		//header("Cache-Control: no-cache, must-revalidate");
		header("Expires: 0");
		header('Content-disposition: attachment; filename="ivty_dtl_dload.csv"');
		header('Content-Length: ' . filesize($mfile));
		header("Pragma: no-cache"); 
		//header('Pragma: public');

		//Clear system output buffer
		flush();
		
		//Read the size of the file
		readfile($mfile);
		
	} //end detailed_download
	
	public function ivtysummary() { 
		//$this->cuser;
		//$this->mpw_tkn;
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070703')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}  //end if
		
		$strqry = "
		select `ITEMC`,`ITEM_DESC`,max(`MARTM_COST`) MARTM_COST,`ITEM_BARCODE`,max(`MARTM_PRICE`) MARTM_PRICE,
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl` group by `ITEMC` 
		";
		$strg = "
		SELECT oa.*,`ITEM_DESC` `ART_DESC`,MARTM_COST `ITEM_COST`,MARTM_PRICE `ITEM_PRICE`,
		(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * `END_BAL_QTY`) end ) ITEM_AMT_COST,
		(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * `END_BAL_QTY`) end ) ITEM_AMT_PRICE 
		from ({$strqry}) oa 	
		";		
		$str_meivty = "select 
		concat(ifnull(sum(`END_BAL_QTY`),0),'|',
		ifnull(sum(`ITEM_AMT_COST`),0),'|',
		ifnull(sum(`ITEM_AMT_PRICE`),0)) 
		 from 
		 ({$strg}) me";
		 		
		// $strx = "select concat(\"select '\",Branch_code,\"' ME_BRANCH,\",
		// \"(SELECT {$str_END_BAL} from {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl`) END_BAL_QTY union all \") meqry FROM {$this->db_erp}.mst_branch_ivty_tag ORDER BY Branch_code";
		
		// $strx = "select concat(\"select '\",Branch_code,\"' ME_BRANCH,
		// '\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		// \"(SELECT {$str_ivty} from {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl`) END_BAL_QTY union all \") meqry FROM {$this->db_erp}.mst_branch_ivty_tag aa 
		// join {$this->db_erp}.mst_companyBranch bb on(aa.Branch_code = concat('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";

		$str = "select concat(\"select '\",Branch_code,\"' ME_BRANCH,
		'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"({$str_meivty}) END_BAL_DATA union all \") meqry FROM {$this->db_erp}.mst_branch_ivty_tag aa 
		join {$this->db_erp}.mst_companyBranch bb on(aa.Branch_code = concat('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";

		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		foreach($q->getResultArray() as $rw): 
			$str .= $rw['meqry'];
		endforeach;
		$q->freeResult();
		//die();
		//$rw = $q->getRowArray();
		$memodule = "__merptivtyrecssumma__";
		$str = substr($str,0,strlen($str) - 10);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		 $chtml = "
		<div class=\"row m-0 p-1 mt-2 mb-2\">
			<div class=\"col-md-12 col-md-12 col-md-12\">
				<div class=\"table-responsive\">
					<table class=\"metblentry-font table-bordered\" id=\"__tbl_{$memodule}\">
						<thead>
							<tr>
								<th></th>
								<th>Branch Code</th>
								<th>Branch Name</th>
								<th>Beg Bal QTY</th>
								<th>Cost Amount</th>
								<th>SRP Amount</th>
							</tr>
		 ";
		 $nn = 1;
		foreach($q->getResultArray() as $rw): 
			$xdata = explode("|",$rw['END_BAL_DATA']);
			list($nBalQty, $nBalCost, $nBalPrice) = $xdata;
			$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
			$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
			
			$chtml .= "
			<tr style=\"background-color: {$bgcolor} !important;\" {$on_mouse}>
				<td>{$nn}</td>
				<td>{$rw['ME_BRANCH']}</td>
				<td>{$rw['ME_BRANCH_NAME']}</td>
				<td class=\"text-end\">" . number_format($nBalQty,4,'.',',') . "</td>
				<td class=\"text-end\">" . number_format($nBalCost,4,'.',',') . "</td>
				<td class=\"text-end\">" . number_format($nBalPrice,4,'.',',') . "</td>
			</tr>
			";
			$nn++;
			$str = "update {$this->db_erp_br}.`trx_branch_bal_summary` set 
			`BAL_QTY` = {$nBalQty},
			`BAL_COST` = {$nBalCost},
			`BAL_SRP_AMT` = {$nBalPrice} 	
			where B_CODE = '{$rw['ME_BRANCH']}'";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		endforeach;
		$q->freeResult();
		$chtml .= "
		</table>
		</div>
		</div>
		</div>
		<script>
		__mysys_apps.meTableSetCellPadding('__tbl_{$memodule}',3,'1px solid #7F7F7F');
		</script>
		";
		echo $chtml;
		
	}  //end ivtysummary
	
	public function live_inventory_balance() { 
		$memodmtkn = 'baa138d483ce20366f3c1270a6abf9f86d58619a8665dd0fcd5b2145feb7c25846056ed4540238d0e60c6fa99a15b80f8e7712af0c846420b59657cceaeca9dd';
		$memtkn = $this->request->getVar('memtkn');
		if ($memtkn == $memodmtkn) { 
			$memodule = "__merptivtybalonline__";
			$str = "select    `B_CODE`,
			bb.BRNCH_NAME,
			ifnull(`BAL_QTY`,0) BAL_QTY,
			ifnull(`BAL_COST`,0) BAL_COST,
			ifnull(`BAL_SRP_AMT`,0) BAL_SRP_AMT,
			`MPROCDATE` from {$this->db_erp_br}.`trx_branch_bal_summary` aa join 
			{$this->db_erp}.mst_companyBranch bb on(aa.B_CODE = concat('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$chtml = "
			<div class=\"row m-0 p-1 mt-2 mb-2\">
				<div class=\"col-md-12 col-md-12 col-md-12\">
					<div class=\"table-responsive\">
						<table class=\"metblentry-font table-bordered\" id=\"__tbl_{$memodule}\">
							<thead>
								<tr>
									<th></th>
									<th>Branch Code</th>
									<th>Branch Name</th>
									<th nowrap>Balance QTY</th>
									<th>Cost Amount</th>
									<th>SRP Amount</th>
									<th>as of Dated</th>
								</tr>
			 ";
			 $nn = 1;
			 $ntotQty = 0; $ntotCost = 0; $ntotSrp = 0;
			foreach($q->getResultArray() as $rw): 
				$nBalQty = $rw['BAL_QTY']; $nBalCost = $rw['BAL_COST']; $nBalPrice = $rw['BAL_SRP_AMT'];
				$bgcolor = ($nn % 2) ? "#EAEAEA" : "#F2FEFF";
				$on_mouse = " onmouseover=\"this.style.backgroundColor='#97CBFF';\" onmouseout=\"this.style.backgroundColor='" . $bgcolor  . "';\"";	
				
				$chtml .= "
				<tr style=\"background-color: {$bgcolor} !important;\" {$on_mouse}>
					<td>{$nn}</td>
					<td nowrap>{$rw['B_CODE']}</td>
					<td nowrap>{$rw['BRNCH_NAME']}</td>
					<td class=\"fw-bolder text-danger text-end\" nowrap>" . number_format($nBalQty,4,'.',',') . "</td>
					<td class=\"fw-bolder text-danger text-end\" nowrap>" . number_format($nBalCost,4,'.',',') . "</td>
					<td class=\"fw-bolder text-danger text-end\" nowrap>" . number_format($nBalPrice,4,'.',',') . "</td>
					<td nowrap>{$rw['MPROCDATE']}</td>
				</tr>
				";
				$nn++;
				$ntotQty += $nBalQty; 
				$ntotCost += $nBalCost;
				$ntotSrp += $nBalPrice;
				
			endforeach;
			$q->freeResult();
			$chtml .= "
				<tr>
					<td colspan=\"3\"></td>
					<td class=\"fw-bolder text-end\">" . number_format($ntotQty,4,'.',',') . "</td>
					<td class=\"fw-bolder text-end\">" . number_format($ntotCost,4,'.',',') . "</td>
					<td class=\"fw-bolder text-end\">" . number_format($ntotSrp,4,'.',',') . "</td>
					<td></td>
				</tr>
			</table>
			</div>
			</div>
			</div>
			<script>
			__mysys_apps.meTableSetCellPadding('__tbl_{$memodule}',3,'1px solid #7F7F7F');
			</script>
			";
			echo $chtml;
		} else { 
			echo "...INVALID_TOKEN...";
		}  //end if
		
	} //end live_inventory_balance
	
	public function itemized_ivty_abrach() { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();
		$adata = array(); 		
		$meitem = $this->request->getVar('meitem');
		$meitemtkn = $this->request->getVar('meitemtkn');
		$str_optn = "";
		if(!empty($meitem) && !empty($meitem)) { 
			$str = "select aa.recid,ART_CODE,ART_DESC,ART_BARCODE1 
			from {$this->db_erp}.`mst_article` aa where `ART_CODE` = '$meitem' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$meitemtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_POS_TALLY_TAXR','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Product Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$ART_CODE = $rw['ART_CODE'];
			$ART_DESC = $rw['ART_DESC'];
			$ART_BCODE = $rw['ART_BARCODE1'];
			$str_optn = " `ITEMC` = '$ART_CODE' ";
		} else { 
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong>Product Item is REQUIRED!!!</div>";
			die();
		} //end if
		
		$strqry_fields = "
		concat((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
		sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end),'x|x', -- Beginning Balance 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)),'x|x', -- from Receiving Deliveries 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)),'x|x',  -- claims adjustment 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)),'x|x', -- received from Pull Outs 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)),'x|x', -- Sales  
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		)) -- ending balance  
		";
				
		$str = "
		SELECT
		CONCAT(\"select '\",Branch_code,\"' ME_BRANCH,\",
		\"'\",bb.BRNCH_NAME,\"' ME_BRANCH_NAME,\",
		\"'{$ART_CODE}' ART_CODE,\",
		\"'{$ART_DESC}' ART_DESC,\",
		\"'{$ART_BCODE}' ART_BCODE,\",
		\"(SELECT {$strqry_fields} FROM {$this->db_erp_br}.`trx_\",TRIM(Branch_code),\"_myivty_lb_dtl` where {$str_optn} ) ME_DATA union all \" 
		 ) meqry 
		 FROM {$this->db_erp}.mst_branch_ivty_tag aa JOIN {$this->db_erp}.mst_companyBranch bb ON(aa.Branch_code = CONCAT('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME
		";

		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		foreach($q->getResultArray() as $rw): 
			$str .= $rw['meqry'];
		endforeach;
		$q->freeResult();
		//die();
		//$rw = $q->getRowArray();
		$str = substr($str,0,strlen($str) - 10);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rlist'] = $q->getResultArray();
			$adata['rfieldnames'] = $q->getFieldNames();
		} else { 
			$adata['rlist'] = '';
			$adata['rfieldnames'] = '';
		} 
		$q->freeResult();		
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_BRITEMIZED_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);				
		return $adata;
	 } //itemized_ivty_abrach
	 
	 public function live_balance_branches_conso() { 
		$cuser            = $this->myusermod->mysys_user();
		$mpw_tkn          = $this->myusermod->mpw_tkn();
		
		if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070707')) { 
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Restricted.<br/></strong><strong>Access DENIED!!!</strong></div>
			";
			die();
		}  //end if
		
		$meyr = $this->request->getVar('mesumm_year');
		$memo = $this->request->getVar('mesumm_month');
		
		if(empty($meyr) && !empty($memo)):
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Alert<br/></strong><strong>Invalid Year and Month!!!</strong></div>
			";
			die();
		endif;

		if(!empty($meyr) && empty($memo)):
			echo "
			<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Alert<br/></strong><strong>Invalid Year and Month!!!</strong></div>
			";
			die();
		endif;
		
		
		
		$adata = array(); 		
		$str = "select aa.Branch_code,bb.BRNCH_NAME FROM {$this->db_erp}.mst_branch_ivty_tag aa JOIN {$this->db_erp}.mst_companyBranch bb ON(aa.Branch_code = CONCAT('E',bb.BRNCH_OCODE2)) ORDER BY bb.BRNCH_NAME";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "";
		$str_END_BAL_QTY = "
			((case when if(`MTYPE` = 'GEN-IVTYC',1,0) > 0 then 
				if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))  
			else if(`MTYPE` = 'BEG-BAL',`MQTY`,0) 
			end) + 
			if(`MTYPE` = 'RCV',`MQTY`,0) + 
			if(`MTYPE` = 'CYC-ADJ',`MQTY`,0) + 
			if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0) + 
			if(`MTYPE` = 'RCV-S',`MQTY`,0) + 
			if(`MTYPE` = 'RCV-M',`MQTY`,0) + 
			if(`MTYPE` = 'RCV-C',`MQTY`,0) + 
			if(`MTYPE` = 'RCV-R',`MQTY`,0) + 
			if(`MTYPE` = 'SALES',`MQTY`,0) + 
			if(`MTYPE` = 'PO-B1T1',`MQTY`,0) + 
			if(`MTYPE` = 'PO-DSP',`MQTY`,0) + 
			if(`MTYPE` = 'PO-BRG',`MQTY`,0) + 
			if(`MTYPE` = 'PO-GVA',`MQTY`,0) + 
			if(`MTYPE` = 'PO-TO',`MQTY`,0) + 
			if(`MTYPE` = 'PO-TOB',`MQTY`,0) + 
			if(`MTYPE` = 'PO-RTML',`MQTY`,0) + 
			if(`MTYPE` = 'PO-SU',`MQTY`,0) + 
			if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)
			) ";
			
		foreach($q->getResultArray() as $rw): 		
			$B_CODE = $rw['Branch_code'];
			$ivtytbl = "{$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl`";
			$str_optn = '';
			if(!empty($meyr) && !empty($memo)):
				$ivtytbl = "{$this->db_erp_br}.`trx_{$B_CODE}_myivty_lb_dtl_mo`";
				$str_optn = " where `MYEAR` = {$meyr} AND `MMONTH` = {$memo}";
				$strq = "create table if not exists {$ivtytbl} like {$this->db_erp_br}.`trx_myivty_lb_dtl_pmo`";
			else: 
				$strq = "create table if not exists {$ivtytbl} like {$this->db_erp_br}.`trx_myivty_lb_dtl`";
			endif;
			$this->mylibzdb->myoa_sql_exec($strq,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			$str .= "
			select '{$rw['BRNCH_NAME']}' `Branch`,
			(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
				sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
			else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
			end) `Beginning Balance`,
			sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0)) `General Inventory (Physical Count)`, -- General Inventory thru Physical Count QTY 
			(case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
				sum(if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0) - if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
			else 0 end ) `General Inventory Discrepancy QTY`, -- General Inventory Discrepancy QTY 
			sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) `Adjusted Cycle Counting`, -- Adjusted Cycle Counting 
			sum(if(`MTYPE` = 'RCV',`MQTY`,0)) `Receiving (Deliveries)`, -- from Receiving Deliveries 
			sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) `Claims`,  -- claims adjustment 
			sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) `Receiving (Store Use)`, -- received from Store Use 
			sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) `Receiving (Membership)`, -- received from Membership 
			sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) `Receiving (Change Price)`,  -- received from Change Price 
			sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) `Receiving (Rcv in frm Pull Out)`, -- received from Pull Outs 
			sum(if(`MTYPE` = 'SALES',`MQTY`,0)) `Sales Out`, -- Sales  
			sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) `Pull Out (Buy1Take1)`, -- PO Buy 1 Take 1  
			sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) `Pull Out (Dispose)`, -- PO Dispose 
			sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) `Pull Out (For Bargain)`, -- PO Bargain  
			sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) `Pull Out (Giveaways)`, -- PO Give Aways  
			sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) `Pull Out (Inventory Transfer Out)`, -- PO Transfer Out 
			sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) `Pull Out (Pull Out to Other Branch)`, -- PO Transfer Out to Other Branch 
			sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) `Pull Out (Return to Mapulang Lupa)`, -- PO Return to Mapulang Lupa WSHE 
			sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) `Pull Out (Store-Use)`, -- PO Store Use 
			sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0)) `Pull Out (Others)`, -- PO Store Use 
			((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
				sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
			else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
			end) + 
			sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
			sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
			sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
			) `Ending Balance`,
			sum(case when (MARTM_COST is null or MARTM_COST = 0) then 0 else (MARTM_COST * $str_END_BAL_QTY) end ) `Cost Amount`,
			sum(case when (MARTM_PRICE is null or MARTM_PRICE = 0) then 0 else (MARTM_PRICE * $str_END_BAL_QTY) end ) `SRP Amount` 
			 from {$ivtytbl} {$str_optn} union all ";
		endforeach;
		$q->freeResult();
		$str = substr($str,0,strlen($str) - 10);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$adata['rlist'] = $q->getResultArray();
			$adata['rfieldnames'] = $q->getFieldNames();
			$adata['mesumm_year'] = $meyr;
			$adata['mesumm_month'] = $memo;
		} else { 
			$adata['rlist'] = '';
			$adata['rfieldnames'] = '';
			$adata['mesumm_year'] = $meyr;
			$adata['mesumm_month'] = $memo;
		} 
		$q->freeResult();
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'HO_IVTY_BRCONSO_GEN','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);				
		return $adata;
	 } //end live_balance_arbaches_conso
	
	public function getDumpedLB($mdl_me_branch_mtkn='') { 
		$mstring_tbl = $this->mylibz->random_string(15) ;
		$tbltemp = $this->db_temp . ".`tbldumpedlb_{$mstring_tbl}`";
		if(!empty($mdl_me_branch_mtkn)) { 
			$str = "select recid,BRNCH_NAME,trim(BRNCH_OCODE2) B_OCODE2,BRNCH_MAT_FLAG 
			from {$this->db_erp}.`mst_companyBranch` aa where sha2(concat(aa.recid,'{$this->mpw_tkn}'),384) = '$mdl_me_branch_mtkn'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid Branch Data!!!.</div>";
				die();
			}
			
			$rw = $q->getRowArray();
			$br_id = $rw['recid'];
			$br_ocode2 = $rw['B_OCODE2'];
			$tblivty = "{$this->db_erp_br}.`trx_E{$br_ocode2}_myivty_lb_dtl`";
			$tblartm = "{$this->db_erp}.`mst_article`";
			$lperbr = 0;
			if($rw['BRNCH_MAT_FLAG'] == 'G') { 
				$lperbr = 1;
			}
			$q->freeResult();
		} else {
			echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong>Branch Data TOKEN is REQUIRED!!!.</div>";
			die();
		} //end if
		$str = "drop table if exists {$tbltemp}";
		$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$str = "create table if not exists {$tbltemp} (
			`recid` int(10) NOT NULL AUTO_INCREMENT,
			`ITEMC` varchar(35) NOT NULL,
			`ITEM_DESC` varchar(150) NOT NULL,
			`ITEM_BARCODE` varchar(35) NOT NULL,
			`MARTM_COST` decimal(15,4) NOT NULL,
			`MARTM_PRICE` decimal(15,4) NOT NULL,
			`END_BAL_QTY` decimal(15,4) NOT NULL,
			UNIQUE KEY `idx01` (`ITEMC`),
			KEY `recid` (`recid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
		";
		$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$str = "
		insert into {$tbltemp} (`ITEMC`,`ITEM_DESC`,`ITEM_BARCODE`,`MARTM_COST`,`MARTM_PRICE`,`END_BAL_QTY`) 
		select `ITEMC`,`ITEM_DESC`,`ITEM_BARCODE`,max(`MARTM_COST`) MARTM_COST,max(`MARTM_PRICE`) MARTM_PRICE,
		((case when sum(if(`MTYPE` = 'GEN-IVTYC',1,0)) > 0 then 
			sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - (if(`MTYPE` = 'BEG-BAL',`MQTY`,0) - if(`MTYPE` = 'GEN-IVTYC',`MQTY`,0))) 
		else sum(if(`MTYPE` = 'BEG-BAL',`MQTY`,0)) 
		end) + 
		sum(if(`MTYPE` = 'RCV',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CYC-ADJ',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'CLAIMS',(0 - (`MQTY` - `MQTY_CORRECTED`)) ,0)) + 
		sum(if(`MTYPE` = 'RCV-S',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-M',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-C',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'RCV-R',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'SALES',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-B1T1',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-DSP',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-BRG',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-GVA',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TO',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-TOB',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-RTML',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-SU',`MQTY`,0)) + 
		sum(if(`MTYPE` = 'PO-OTHERS',`MQTY`,0))
		) `END_BAL_QTY` 
		 from {$tblivty} group by `ITEMC`";	
		$this->myusermod->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		return $mstring_tbl;
	} //end getDumpedLB
	
	public function ho_inv_report_dl(){
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$fld_dlsupp = $this->request->getVar('fld_dlsupp');
		$fld_dlsupp_id = $this->request->getVar('fld_dlsupp_id');
		$fld_months = $this->request->getVar('fld_months');
		$fld_years = $this->request->getVar('fld_years');

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

		$monthName = date('F', mktime(0, 0, 0, $fld_months, 1));

		//VALIDATING DATA SUPPLIER
		if(!empty($fld_dlsupp)) {
		$str = "select recid,
			VEND_NAME,
			VEND_ADDR1 ADDR1,
			VEND_ADDR2 ADDR2,
			VEND_TELNO TELNO from {$this->db_erp}.mst_vendor aa where `VEND_NAME` = '$fld_dlsupp' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				Invalid Supplier Data	
				";
				die();
			}//end if
			$rw = $q->getRowArray();
			$fld_ADDR1 = $rw['ADDR1'];
			$fld_ADDR2 = $rw['ADDR2'];
			$fld_TELNO = $rw['TELNO'];
			$q->freeResult();
			$str_supp_aa= "AND sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
			$str_supp= "AND sha2(concat(`supplier_id`,'{$mpw_tkn}'),384) = '$fld_dlsupp_id'";
			$grp_supp=",`supplier_id`";
		}//end if
		
		if($this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070802') && !$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070803') && !$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070804') && !$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070805')) { 
			$chtmljs .= "
			<div class=\"col-sm-12 mx-0 px-0\" id=\"__mtoexport\" role=\"alert\">
	
				<div class=\"row alert alert-warning\" role=\"alert\">
					<div class=\"col-sm-12 mb-3\">
						Click to download inventory summaries below:
					</div>
					<hr>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER BRANCH</i></a></span>
					</div>
				</div>
			</div>
			";
		}elseif($this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070802') && $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070803') && !$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070804') && !$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070805')){
			$chtmljs .= "
			<div class=\"col-sm-12 mx-0 px-0\" id=\"__mtoexport\" role=\"alert\">
	
				<div class=\"row alert alert-warning\" role=\"alert\">
					<div class=\"col-sm-12 mb-3\">
						Click to download inventory summaries below:
					</div>
					<hr>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER BRANCH</i></a></span>
					</div>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_sb\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SKU/BRANCH</i></a></span>
					</div>
				</div>
			</div>
			";
		}elseif($this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070802') && $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070803') && $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070804') && !$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070805')){
			$chtmljs .= "
			<div class=\"col-sm-12 mx-0 px-0\" id=\"__mtoexport\" role=\"alert\">
	
				<div class=\"row alert alert-warning\" role=\"alert\">
					<div class=\"col-sm-12 mb-3\">
						Click to download inventory summaries below:
					</div>
					<hr>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER BRANCH</i></a></span>
					</div>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_sb\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SKU/BRANCH</i></a></span>
					</div>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_c\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SUPPLIER</i></a></span>
					</div>
				</div>
			</div>
			";
		}elseif($this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070802') && $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070803') && $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070804') && $this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','00070805')){
			$chtmljs .= "
			<div class=\"col-sm-12 mx-0 px-0\" id=\"__mtoexport\" role=\"alert\">
	
				<div class=\"row alert alert-warning\" role=\"alert\">
					<div class=\"col-sm-12 mb-3\">
						Click to download inventory summaries below:
					</div>
					<hr>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER BRANCH</i></a></span>
					</div>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_sb\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SKU/BRANCH</i></a></span>
					</div>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_c\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SUPPLIER</i></a></span>
					</div>
					<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
						<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_s\"><i class=\"btn btn-success btn-sm bi bi-download\"> STATEMENT</i></a></span>
					</div>
				</div>
			</div>
			";
		}else{
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> NO ACCESS FOR ALL EXTRACTION!!!</div>";
			die();
		}

		// $chtmljs .= "
		// <div class=\"col-sm-12 mx-0 px-0\" id=\"__mtoexport\" role=\"alert\">

		// 	<div class=\"row alert alert-warning\" role=\"alert\">
		// 		<div class=\"col-sm-12 mb-3\">
		// 			Click to download inventory summaries below:
		// 		</div>
		// 		<hr>
		// 		<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
		// 			<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER BRANCH</i></a></span>
		// 		</div>
		// 		<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
		// 			<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_sb\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SKU/BRANCH</i></a></span>
		// 		</div>
		// 		<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
		// 			<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_c\"><i class=\"btn btn-success btn-sm bi bi-download\"> PER SUPPLIER</i></a></span>
		// 		</div>
		// 		<div class=\"col-lg-3 col-md-6 col-sm-12 mb-3\">
		// 			<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_s\"><i class=\"btn btn-success btn-sm bi bi-download\"> STATEMENT</i></a></span>
		// 		</div>
		// 	</div>
        // </div>
		// ";

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
		
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->getNumRows() > 0) { 
			$mpathdn   = ROOTPATH; 
            $mpathdest = $mpathdn . 'public/downloads/me'; 
            $cdate     = date('Ymd');
            $cfiletmp  = 'rcvng_inv_rpt' . $cdate . $this->mylibzsys->random_string(9) . '.xls' ;
            $cfiledest = $mpathdest . '/' . $cfiletmp;
            $cfilelnk  = site_url() . 'downloads/me/' . $cfiletmp;
			//SEND TO UALAM
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'RCVINVRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
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
			$qrw = $q->getResultArray();
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
		
		$q->freeResult();
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

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->getNumRows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn   = ROOTPATH; 
            $mpathdest = $mpathdn . 'public/downloads/me'; 
            $cdate     = date('Ymd');
            $cfiletmp4  = 'rcvng_inv_rpt' . $cdate . $this->mylibzsys->random_string(9) . '.xls' ;
            $cfiledest4 = $mpathdest . '/' . $cfiletmp4;
            $cfilelnk4  = site_url() . 'downloads/me/' . $cfiletmp4;
			//SEND TO UALAM
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'RCVINVRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp4,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
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
			$qrw = $q->getResultArray();
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
		
		$q->freeResult();
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

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
			$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			//var_dump($str);
			$res_c=1;
			if($q->getNumRows() > 0) { 
				//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
				$mpathdn   = ROOTPATH; 
				$mpathdest = $mpathdn . 'public/downloads/me'; 
				$cdate     = date('Ymd');
				$cfiletmp3  = 'rcvng_inv_rpt' . $cdate . $this->mylibzsys->random_string(9) . '.xls' ;
				$cfiledest3 = $mpathdest . '/' . $cfiletmp3;
				$cfilelnk3  = site_url() . 'downloads/me/' . $cfiletmp3;
				//SEND TO UALAM
				$this->mylibzdb->user_logs_activity_module($this->db_erp,'RCVINVRPTCOMP_DOWNLOAD','',$cuser."_FN_".$cfiletmp3,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
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
				$qrw = $q->getResultArray();
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
			
			$q->freeResult();
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

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res_s=1;
		$inv_camt=0;
		$inv_prc =0;
		if($q->getNumRows() > 0) { 
		//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
		$mpathdn   = ROOTPATH; 
		$mpathdest = $mpathdn . 'public/downloads/me'; 
		$cdate     = date('Ymd');
		$cfiletmp2  = 'rcvng_inv_rpt_st' . $cdate . $this->mylibzsys->random_string(9) . '.xls' ;
		$cfiledest2 = $mpathdest . '/' . $cfiletmp2;
		$cfilelnk2  = site_url() . 'downloads/me/' . $cfiletmp2;

		//SEND TO UALAM
		$this->mylibzdb->user_logs_activity_module($this->db_erp,'RCVINVRPTSTATE_DOWNLOAD','',$cuser."_FN_".$cfiletmp2,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		//SECUREW FILES
		if(file_exists($cfiledest2)) {
		unlink($cfiledest2);
		}
		$fh2 = fopen($cfiledest2, 'w');
		fwrite($fh2, $chtml2);
		fclose($fh2); 
		chmod($cfiledest2, 0755);
		$qrw = $q->getResultArray();
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

	}

	public function ho_inv_report_br_dl(){
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$fld_drsomhd =$this->request->getVar('fld_drsomhd');
		$fld_drsupp =$this->request->getVar('fld_drsupp');
		$fld_drsupp_id =$this->request->getVar('fld_drsupp_id');
		$fld_drbrnch =$this->request->getVar('fld_drbrnch');
		$fld_drbrnch_id =$this->request->getVar('fld_drbrnch_id');
		$fld_months =$this->request->getVar('fld_months');
		$fld_years =$this->request->getVar('fld_years');
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

		$str="
			SELECT myuserfulln FROM myusers WHERE myusername = '$cuser'
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->getRowArray();
		$cuser_fullname = $rw['myuserfulln'];

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
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows() == 0) { 
				echo "
				<div class=\"alert alert-danger\" role=\"alert\">
				Invalid Supplier Data	
				";
				die();
			}//end if
			$rw = $q->getRowArray();
			$fld_ADDR1 = $rw['ADDR1'];
			$fld_ADDR2 = $rw['ADDR2'];
			$fld_TELNO = $rw['TELNO'];
			$q->freeResult();
			$str_supp= "AND sha2(concat(aa.`supplier_id`,'{$mpw_tkn}'),384) = '{$fld_drsupp_id}'";
		}//end if
		//BRANCH
		if(!empty($fld_drbrnch)) {
			$str = "select recid,BRNCH_NAME 
			 from {$this->db_erp}.`mst_companyBranch` aa where `BRNCH_NAME` = '$fld_drbrnch' AND sha2(concat(aa.recid,'{$mpw_tkn}'),384) = '$fld_drbrnch_id'";
			 $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'COMPANY','',$cuser,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			if($q->getNumRows() == 0) { 
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Invalid branch Data!!!.</div>";
				die();
			}

			$rw = $q->getRowArray();
			$fld_area_code = $rw['BRNCH_NAME'];
			$q->freeResult();
			$str_brnch="AND sha2(concat(aa.`branch_id`,'{$mpw_tkn}'),384)= '{$fld_drbrnch_id}'";
			//END BRANCH
		}//end if
		//IF SEARCH IS NOT EMPTY
		if(!empty($fld_years) && !empty($fld_months)){
			//CONVERTING MONTH to name
			$dateObj = \DateTime::createFromFormat('!m', $fld_months);
			$monthName = $dateObj->format('F');
			//$msearchrec = $this->dbx->escape_str($msearchrec);
			$str_optn = "AND YEAR(aa.`rcv_date`) = '{$fld_years}' AND MONTH(aa.`rcv_date`) = '{$fld_months}'";
			$str_optn_po = "AND YEAR(aa.`po_date`) = '{$fld_years}' AND MONTH(aa.`po_date`) = '{$fld_months}'";
		}
		$chtmljs .= "
		<div class=\"col-sm-12 mx-0 px-0\" id=\"__mtoexport_drtd\" role=\"alert\">
	
			<div class=\"row alert alert-warning\" role=\"alert\">
				<div class=\"col-sm-12 mb-3\">
					Click to download inventory breakdown below:
				</div>
				<hr>
				<div class=\"col-md-3\">
					<span class=\"\"><a href=\"JavaScript:void(0);\" id=\"lnkexportmsexcel_dr\"><i class=\"btn btn-success bi bi-download\"> DR Breakdown</i></a></span>
				</div>
			</div>
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
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		//var_dump($str);
		$res=1;
		if($q->getNumRows() > 0) { 
			//IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
			$mpathdn   = ROOTPATH; 
            $mpathdest = $mpathdn . 'public/downloads/me'; 
            $cdate     = date('Ymd');
            $cfiletmp  = 'rcvng_dr_rpt' . $cdate . $this->mylibzsys->random_string(9) . '.xls' ;
            $cfiledest = $mpathdest . '/' . $cfiletmp;
            $cfilelnk  = site_url() . 'downloads/me/' . $cfiletmp;
			//SEND TO UALAM
			$this->mylibzdb->user_logs_activity_module($this->db_erp,'RCVDRRPT_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
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
			$qrw = $q->getResultArray();
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
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$qrw2 = $q->getResultArray();
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
} //end main 

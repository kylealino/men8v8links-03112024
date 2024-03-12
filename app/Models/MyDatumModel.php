<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\MyDBNamesModel;
use App\Models\MyLibzDBModel;
use App\Models\MyLibzSysModel;
use App\Models\MyUserModel;

class MyDatumModel extends Model
{
	public function __construct()
	{
		$this->mydbname = new MyDBNamesModel();
		$this->db_erp = $this->mydbname->medb(0);
		$this->mylibz =  new MyLibzSysModel();
		$this->mylibzdb =  new MyLibzDBModel();
		$this->myusermod =  new MyUserModel();
		$this->session = session();
		$this->request = \Config\Services::request();
	}
	
	
	public function lk_Active_Store_or_Mem($dbname) { 
		$adata = array();
		$str = "select concat(rcv_code,'xOx',trim(rcv_desc)) __mdata from {$dbname}.mst_manrecs_rcvng_tag order by recid";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end lk_Active_Store_or_Mem
	
	public function lk_branch_users($dbname) { 
		$adata = array();
		$str = "select concat(myusername,'xOx',trim(myusername)) __mdata from {$dbname}.myusers 
		where myuserrema = 'B' order by recid";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;		
	}  //end lk_branch_users
		
	public function lk_Active_DF($dbname) { 
		$cuserrema = $this->myusermod->mysys_userrema();
		$adata=array();
		if($cuserrema ==='B'){
			$adata[]="D" . "xOx" . "Draft";
		}
		else{ 
			$adata[]="D" . "xOx" . "Draft";
			$adata[]="F" . "xOx" . "Final";	
		}
		return $adata;		
	}	//lk_Active_DF
	
	public function lk_manrecs_pout_rson($dbname) { 
		$adata = array();
		$str = "select concat(recid,'xOx',trim(`pout_rsondesc`)) __mdata from {$dbname}.mst_manrecs_pout_reas order by pout_rsondesc";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end lk_manrecs_pout_rson
	
	public function lk_manrecs_pout_typ($dbname) { 
		$adata=array();
		$adata[]="T" . "xOx" . "Trade";
		$adata[]="N" . "xOx" . "Non Trade";
		return $adata;	
	}  //end lk_manrecs_pout_typ

	public function get_ctr($dbname,$mfld='') { 
		$str = "
		CREATE TABLE if not exists {$dbname}.`myctr` (
		  `CTR_YEAR` varchar(4) DEFAULT '0000',
		  `CTRL_NO01` varchar(15) DEFAULT '00000000',
		  `CTRL_NO02` varchar(15) DEFAULT '00000000',
		  `CTRL_NO03` varchar(15) DEFAULT '00000000',
		  `CTRL_NO04` varchar(15) DEFAULT '00000000',
		  `CTRL_NO05` varchar(15) DEFAULT '00000000',
		  `CTRL_NO06` varchar(15) DEFAULT '00000000',
		  `CTRL_NO07` varchar(15) DEFAULT '00000000',
		  `CTRL_NO08` varchar(15) DEFAULT '00000000',
		  `CTRL_NO09` varchar(15) DEFAULT '00000000',
		  `CTRL_NO10` varchar(15) DEFAULT '00000000',
		  `CTRL_NO11` varchar(15) DEFAULT '00000000',
		  UNIQUE KEY `ctr01` (`CTR_YEAR`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$xfield = (empty($mfld) ? 'CTRL_NO01' : $mfld);
		
		$str = "select year(now()) XSYSYEAR";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$ryear = $q->getRowArray();
		$xsysyear = $ryear['XSYSYEAR'];
		
		$str = "select {$xfield} from {$dbname}.myctr WHERE CTR_YEAR = '$xsysyear' limit 1";
		$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qctr->getNumRows() == 0) {
			$xnumb = '0000000001';
			$str = "insert into {$dbname}.myctr (CTR_YEAR,{$xfield}) values('$xsysyear','$xnumb')";
			$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$qctr->freeResult();
		} else {
			$qctr->freeResult();
			$str = "select {$xfield} MYFIELD from {$dbname}.myctr WHERE CTR_YEAR = '$xsysyear' limit 1";
			$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rctr = $qctr->getRowArray();
			if(trim($rctr['MYFIELD'],' ') == '') { 
				$xnumb = '0000000001';
			} else {
				$xnumb = $rctr['MYFIELD'];
				$str = "select ('{$xnumb}' + 1) XNUMB";
				$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rctr = $qctr->getRowArray();
				$xnumb = trim($rctr['XNUMB'],' ');
				$xnumb = str_pad($xnumb + 0,10,"0",STR_PAD_LEFT);
				$str = "update {$dbname}.myctr set {$xfield} = '{$xnumb}' where CTR_YEAR = '$xsysyear'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		}
		return $xsysyear . $xnumb;
	} //end getctr	
	
	public function get_ctr_new($class,$supp,$dbname,$mfld='') { 
		$str = "
		CREATE TABLE if not exists {$dbname}.`myctr_stkcode` (
		  `CTR_YEAR` varchar(4) DEFAULT '0000',
		  `CTR_MONTH` varchar(2) DEFAULT '00',
		  `CTR_DAY` varchar(2) DEFAULT '00',
		  `CTRL_NO01` varchar(15) DEFAULT '00000000',
		  `CTRL_NO02` varchar(15) DEFAULT '00000000',
		  `CTRL_NO03` varchar(15) DEFAULT '00000000',
		  `CTRL_NO04` varchar(15) DEFAULT '00000000',
		  `CTRL_NO05` varchar(15) DEFAULT '00000000',
		  `CTRL_NO06` varchar(15) DEFAULT '00000000',
		  `CTRL_NO07` varchar(15) DEFAULT '00000000',
		  `CTRL_NO08` varchar(15) DEFAULT '00000000',
		  `CTRL_NO09` varchar(15) DEFAULT '00000000',
		  `CTRL_NO10` varchar(15) DEFAULT '00000000',
		  `CTRL_NO11` varchar(15) DEFAULT '00000000',
		  UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$xfield = (empty($mfld) ? 'CTRL_NO01' : $mfld);
		
		$str = "select date(now()) XSYSDATE";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rdate = $q->getRowArray();
		$xsysdate = $rdate['XSYSDATE'];
		$xsysdate_exp = explode('-', $xsysdate);
		$xsysyear =  $xsysdate_exp[0];
		$xsysmonth = $xsysdate_exp[1];
		$xsysday = $xsysdate_exp[2];
		
		$str = "select {$xfield} from {$dbname}.myctr_stkcode WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday'  limit 1";
		$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qctr->getNumRows() == 0) {
			$xnumb = '0000000001';
			$str = "insert into {$dbname}.myctr_stkcode (CTR_YEAR,CTR_MONTH,CTR_DAY,{$xfield}) values('$xsysyear','$xsysmonth','$xsysday','$xnumb')";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$qctr->freeResult();
		} else {
			$str = "select {$xfield} MYFIELD from {$dbname}.myctr_stkcode WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday' limit 1";
			$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rctr = $qctr->getRowArray();
			if(trim($rctr['MYFIELD'],' ') == '') { 
				$xnumb = '0000000001';
			} else {
				$xnumb = $rctr['MYFIELD'];
				$str = "select ('{$xnumb}' + 1) XNUMB";
				$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rctr = $qctr->getRowArray();
				$xnumb = trim($rctr['XNUMB'],' ');
				$xnumb = str_pad($xnumb + 0,10,"0",STR_PAD_LEFT);
				$str = "update {$dbname}.myctr_stkcode set {$xfield} = '{$xnumb}'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$qctr->freeResult();
		}
		return  substr($xsysyear, -2, 2) . $xsysmonth . $xsysday .$class. $xnumb; //.$supp
	} //end get_ctr_new	
	
	
	public function mo_select_items_rcv($trx_no= '') {
		$cuser = $this->myusermod->mysys_user();
		$mpw_tkn = $this->myusermod->mpw_tkn();
		$txt_mo = $this->request->getVar('txt_mo');
		$txt_mo_d = substr($txt_mo, 0,3);
		$branch_id = $this->request->getVar('branch_id');
		$supp_id_n = $this->request->getVar('supp_id_n');

		$data['rlist']='';
		$data['__nores']='';
	
			$str_qry = "SELECT aaa.`recid`,
				ddd.`ART_CODE` ART_CODE,
				ddd.`ART_DESC` ART_DESC,
				bbb.`dr_date`,
				ddd.`ART_SKU` ART_SKU,
				aaa.`ucost` ucost,
				aaa.`uprice` uprice,
				aaa.`qty` qty_corrected,
				NULL mtkn_mndttr,
				SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
				aaa.`qty` qty,
				NULL qty_claim,
				NULL exp_date,
				NULL SM_Tag

				FROM {$this->db_erp}.`trx_manrecs_dr_dt` aaa 
				JOIN {$this->db_erp}.`trx_manrecs_dr_hd` bbb 
				ON (aaa.`drtrx_no` = bbb.`drtrx_no`) 
				LEFT JOIN {$this->db_erp}.`mst_article` ddd 
				ON (aaa.`mat_code` = ddd.`ART_CODE`)
				WHERE bbb.`flag` = 'R' AND bbb.`drtrx_no`= '{$txt_mo}'";


			$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows()>0){
				if(($supp_id_n == '3') || ($supp_id_n == '5537') || ($supp_id_n == '7325')){
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
					";
					die();
				}
				//VALIDATION OF BRANCH CHECKING PER DR
				$str_q = "
				select drtrx_no __mdata from {$this->db_erp}.trx_manrecs_dr_hd where drtrx_no = '$txt_mo' AND branch_id ='$branch_id'";
				$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				
				if($qq->getNumRows() == 0) { 
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
					";
					die();
				}
				$data['rlist'] = $q->getResultArray();
				$rdr = $q->getRowArray();
				//$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
				$drdate = $rdr['dr_date'];
				$fld_somhd = $rdr['SM_Tag'];
				echo "<script type=\"text/javascript\"> 
						jQuery('#fld_drdate').val('{$drdate}');
						jQuery('#fld_somhd').val('{$fld_somhd}');
									</script>";
				
			}else{
				$str = "
					DELETE FROM tap_shipdoc_item WHERE header = '{$txt_mo}'
				";
				$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
				$str="
				INSERT INTO `d_ap2`.`tap_shipdoc_item` (
					`dt_id`,
					`ART_CODE`,
					`ART_DESC`,
					`dr_date`,
					`ART_SKU`,
					`ucost`,
					`uprice`,
					`qty_corrected`,
					`mtkn_mndttr`,
					`qty`,
					`qty_claim`,
					`exp_date`,
					`SM_Tag`,
					`branch_name`,
					`tap_branch_id`,
					`header`
				)
				SELECT
					d.`recid`,
					c.`ART_CODE` ART_CODE,
					c.`ART_DESC` ART_DESC,
					(SELECT DATE(`date_encd`) FROM warehouse_shipdoc_hd WHERE crpl_code = d.`header`) dr_date,
					c.`ART_SKU` ART_SKU,
					c.`ART_UCOST` ucost,
					c.`ART_UPRICE` uprice,
					(b.`qty_perpack` * b.`req_pack`) qty_corrected ,
					NULL mtkn_mndttr,
					(b.`qty_perpack` * b.`req_pack`) qty,
					NULL qty_claim,
					NULL exp_date,
					NULL SM_Tag,
					f.`branch_name`,
					g.`recid` tap_branch_id,
					d.`header`
				FROM
					fgp_inv_rcv a
				JOIN
					trx_fgpack_req_dt b
				ON
					a.`fgreq_trxno` = b.`fgreq_trxno`
				JOIN
					mst_article c
				ON
					b.`mat_code` = c.`ART_CODE`
				JOIN
					warehouse_shipdoc_dt d
				ON
					a.`wob_barcde` = d.`wob_barcde`
				JOIN
					trx_tpa_dt e
				ON
					b.`tpa_trxno` = e.`tpa_trxno`
				JOIN
					trx_tpa_hd f
				ON
					e.`tpa_trxno` = f.`tpa_trxno`
				JOIN
					mst_companyBranch g
				ON
					f.`branch_name` = g.`BRNCH_NAME`
				WHERE
					d.`header` = '{$txt_mo}' AND a.`is_out` = '1'
				GROUP BY 
					b.`mat_code`, b.`fgreq_trxno`
				ORDER BY 
					b.`fgreq_trxno`
			";

        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				// $str_qry = "
				// 	SELECT
				// 		d.`recid`,
				// 		c.`ART_CODE` ART_CODE,
				// 		c.`ART_DESC` ART_DESC,
				// 		d.`dr_date` dr_date,
				// 		c.`ART_SKU` ART_SKU,
				// 		c.`ART_UCOST` ucost,
				// 		c.`ART_UPRICE` uprice,
				// 		(b.`qty_perpack` * b.`req_pack`) qty_corrected ,
				// 		NULL mtkn_mndttr,
				// 		SHA2(CONCAT(d.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
				// 		(b.`qty_perpack` * b.`req_pack`) qty,
				// 		NULL qty_claim,
				// 		NULL exp_date,
				// 		NULL SM_Tag,
				// 		f.`branch_name`,
				// 		g.`recid` tap_branch_id
				// 	FROM
				// 		fgp_inv_rcv a
				// 	JOIN
				// 		trx_fgpack_req_dt b
				// 	ON
				// 		a.`fgreq_trxno` = b.`fgreq_trxno`
				// 	JOIN
				// 		mst_article c
				// 	ON
				// 		b.`mat_code` = c.`ART_CODE`
				// 	JOIN
				// 		warehouse_shipdoc_dt d
				// 	ON
				// 		a.`wob_barcde` = d.`wob_barcde`
				// 	JOIN
				// 		trx_tpa_dt e
				// 	ON
				// 		b.`tpa_trxno` = e.`tpa_trxno`
				// 	JOIN
				// 		trx_tpa_hd f
				// 	ON
				// 		e.`tpa_trxno` = f.`tpa_trxno`
				// 	JOIN
				// 		mst_companyBranch g
				// 	ON
				// 		f.`branch_name` = g.`BRNCH_NAME`
				// 	WHERE
				// 			d.`header` = '{$txt_mo}'
				// 	GROUP BY 
				// 		b.`mat_code`, b.`fgreq_trxno`
				// 	ORDER BY 
				// 		b.`fgreq_trxno`
				// ";

				$str_qry = "
				SELECT 
					`dt_id`,
					`ART_CODE`,
					`ART_DESC`,
					`dr_date`,
					`ART_SKU`,
					`ucost`,
					`uprice`,
					sum(`qty_corrected`) qty_corrected,
					NULL mtkn_mndttr,
					SHA2(CONCAT(dt_id,'$mpw_tkn'),384) mtkn_artmtr,
					sum(`qty`) qty,
					`qty_claim`,
					`exp_date`,
					`SM_Tag`,
					`branch_name`,
					`tap_branch_id`,
					`header`
				FROM
					`tap_shipdoc_item`
				WHERE 
					header = '{$txt_mo}'
				GROUP BY
					ART_CODE
				";


			$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($q->getNumRows()>0){
				if(($supp_id_n == '3') || ($supp_id_n == '5537') || ($supp_id_n == '7325')){
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
					";
					die();
				}
				$rdr = $q->getRowArray();
				$tap_branch_id = $rdr['tap_branch_id'];
				if ($tap_branch_id != $branch_id) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
					";
					die();
				}
				$data['rlist'] = $q->getResultArray();
				$rdr = $q->getRowArray();
				//$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
				$drdate = $rdr['dr_date'];
				$fld_somhd = $rdr['SM_Tag'];
				echo "<script type=\"text/javascript\"> 
						jQuery('#fld_drdate').val('{$drdate}');
						jQuery('#fld_somhd').val('{$fld_somhd}');
									</script>";
			}else{ //manual mkg andsku
				$str_qry = "SELECT aaa.`recid`,
				ddd.`ART_CODE` ART_CODE,
				ddd.`ART_DESC` ART_DESC,
				bbb.`mo_date` dr_date,
				ddd.`ART_SKU` ART_SKU,
				aaa.`ucost` ucost,
				aaa.`uprice` uprice,
				SUM(aaa.`qty` * aaa.`convf`) qty_corrected,
				NULL mtkn_mndttr,
				SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
				SUM(aaa.`qty` * aaa.`convf`) qty,
				NULL qty_claim,
				NULL exp_date,
				NULL SM_Tag

				FROM {$this->db_erp}.`trx_manrecs_mo_dt` aaa 
				JOIN {$this->db_erp}.`trx_manrecs_mo_hd` bbb 
				ON (aaa.`motrx_no` = bbb.`motrx_no`) 
				LEFT JOIN {$this->db_erp}.`mst_article` ddd 
				ON (aaa.`mat_code` = ddd.`ART_CODE`)
				WHERE bbb.`flag` = 'R' AND bbb.`motrx_no`= '{$txt_mo}'
				GROUP BY aaa.`mat_code` ";

				//var_dump($str_qry);
				$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				//$q = $this->dblinks->query($str_qry);
				if($q->getNumRows()>0){
					if(!($supp_id_n == '3')){
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
						";
						die();
					}
					//VALIDATION OF BRANCH CHECKING PER DR
					$str_q = "
					select motrx_no __mdata from {$this->db_erp}.trx_manrecs_mo_hd where motrx_no = '$txt_mo' AND branch_id ='$branch_id'";
					$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
					if($qq->getNumRows() == 0) { 
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
						";
						die();
					}
					$data['rlist'] = $q->getResultArray();
					$rdr = $q->getRowArray();
					$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
					$fld_somhd = $rdr['SM_Tag'];
					echo "<script type=\"text/javascript\"> 
							jQuery('#fld_drdate').val('{$drdate}');
							jQuery('#fld_somhd').val('{$fld_somhd}');
										</script>";
					
					
				}
				else{
				 //PULLOUT
				$str_qry = "
				SELECT aaa.`recid`,
				ddd.`ART_CODE` ART_CODE,
				ddd.`ART_DESC` ART_DESC,
				bbb.`po_date` dr_date,
				ddd.`ART_SKU` ART_SKU,
				aaa.`ucost` ucost,
				aaa.`uprice` uprice,
				SUM(aaa.`qty`) qty_corrected,
				NULL mtkn_mndttr,
				SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
				SUM(aaa.`qty`) qty,
				NULL qty_claim,
				NULL exp_date,
				'R' SM_Tag

				FROM {$this->db_erp}.`trx_manrecs_po_dt` aaa 
				JOIN {$this->db_erp}.`trx_manrecs_po_hd` bbb 
				ON (aaa.`potrx_no` = bbb.`potrx_no`) 
				LEFT JOIN {$this->db_erp}.`mst_article` ddd 
				ON (aaa.`mat_code` = ddd.`ART_CODE`)
				WHERE bbb.`flag` = 'R' AND bbb.`potrx_no`= '{$txt_mo}' AND bbb.`post_tag` = 'Y'
				GROUP BY aaa.`mat_code` ";
				//var_dump($str_qry);
				$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				//$q = $this->dblinks->query($str_qry);
				if($q->getNumRows()>0){
					//SUPPLIER VALIDATION
					if(!($supp_id_n == '5537')){
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
						";
						die();
					}
					//VALIDATION OF BRANCH CHECKING PER DR

					$str_q = "
					select potrx_no __mdata,cb.`BRNCH_NAME` from {$this->db_erp}.trx_manrecs_po_hd hd
					JOIN {$this->db_erp}.mst_companyBranch cb ON hd.branch_id = cb.recid
					where `potrx_no` = '$txt_mo' AND hd.`hd_pfrom_id` ='$branch_id' AND `po_rsons_id` = '4'";
					$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					
					if($qq->getNumRows() == 0) { 
						echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
						";
						die();
					}
							
					$data['rlist'] = $q->getResultArray();
					$rdr = $q->getRowArray();
					//$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
					$drdate = substr($rdr['dr_date'],0,10);
					$fld_somhd = $rdr['SM_Tag'];
					$rdr2 = $qq->getRowArray();
					$brancNme = $rdr2['BRNCH_NAME'];
					echo "<script type=\"text/javascript\"> 
							jQuery('#fld_drdate').val('{$drdate}');
							jQuery('#fld_somhd').val('{$fld_somhd}');
							jQuery('#fld_rcvfrmbrnc').val('{$brancNme}');
							vw_from();
						</script>";
					
					
				}//pull out end

				else{ //shipdoc ni smc price is a cost uprice is srp
					$str_qry = "SELECT bbb.`recid`,
					ddd.`ART_CODE` ART_CODE,
					ddd.`ART_DESC` ART_DESC,
					aaa.`encd` dr_date,
					ddd.`ART_SKU` ART_SKU,
					bbb.`price` ucost,
					IFNULL(bbb.`uprice`,ddd.`ART_UPRICE`) uprice,
					SUM(bbb.`qty`) qty_corrected,
					NULL mtkn_mndttr,
					SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
					SUM(bbb.`qty`) qty,
					NULL qty_claim,
					NULL SM_Tag

					FROM {$this->db_erp}.`wshe_crpl_dt` aaa 
					JOIN {$this->db_erp}.`wshe_crpl_item` bbb 
					ON (aaa.`recid` = bbb.`crpl_id`) 
					LEFT JOIN {$this->db_erp}.`mst_article` ddd 
					ON (bbb.`mat_rid` = ddd.`recid`)
					WHERE aaa.`header`= '{$txt_mo}' AND !(aaa.`frm_plnt_id` ='5')
					GROUP BY ddd.`ART_CODE`
					ORDER BY ddd.`ART_CODE`";


					$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					//$q = $this->dblinks->query($str_qry);
					if($q->getNumRows()>0){
						if(!($supp_id_n == '3')){
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
							";
							die();
						}
						$fld_somhd = '';
						//VALIDATION OF BRANCH CHECKING PER DR//jQuery('#fld_somhd').attr('disabled', true);
						$str_q = "
						select date_encd dr_date,sm_tag SM_Tag,crpl_code __mdata from {$this->db_erp}.trx_crpl where crpl_code = '$txt_mo' AND brnch_rid ='$branch_id'";
						$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						
						if($qq->getNumRows() == 0) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
							";
							die();
						}
						else{
							$rdr_q = $qq->getRowArray();
							$fld_somhd = $rdr_q['SM_Tag'];
							$drdate = $this->mylibz->mydate_mmddyyyy($rdr_q['dr_date']);
						}
						$data['rlist'] = $q->getResultArray();
						$rdr = $q->getRowArray();
						//$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
						echo "<script type=\"text/javascript\"> 
								jQuery('#fld_drdate').val('{$drdate}');
								jQuery('#fld_somhd').val('{$fld_somhd}');
								
											</script>";
						
					}
					//CROSS DOCKING SD ON (SUBSTR(aaa.`wob_barcde`,5,18) = SUBSTR(bbb.`witb_barcde`,5,18))  off nd pwede kasi nauul;it ang barcode sa magakaibang shipdoc
					else{ //shipdoc ni smc price is a cost uprice is srp
					$str_qry = "SELECT bbb.`recid`,
					ddd.`ART_CODE` ART_CODE,
					ddd.`ART_DESC` ART_DESC,
					aaa.`encd` dr_date,
					ddd.`ART_SKU` ART_SKU,
					bbb.`price` ucost,
					IFNULL(bbb.`uprice`,ddd.`ART_UPRICE`) uprice,
					SUM(bbb.`qty`) qty_corrected,
					NULL mtkn_mndttr,
					SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
					SUM(bbb.`qty`) qty,
					NULL qty_claim,
					NULL SM_Tag

					FROM {$this->db_erp}.`warehouse_shipdoc_dt` aaa 
					JOIN {$this->db_erp}.`warehouse_shipdoc_item` bbb 
					ON (aaa.`recid` = bbb.`wshe_out_id`)
					LEFT JOIN {$this->db_erp}.`mst_article` ddd 
					ON (bbb.`mat_rid` = ddd.`recid`)
					WHERE aaa.`header`= '{$txt_mo}' AND (aaa.`is_out` ='1')
					GROUP BY ddd.`ART_CODE`
					ORDER BY ddd.`ART_CODE`";


					$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					//$q = $this->dblinks->query($str_qry);
					if($q->getNumRows()>0){
						if(!($supp_id_n == '3')){
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
							";
							die();
						}
						$fld_somhd = '';
						//VALIDATION OF BRANCH CHECKING PER DR//jQuery('#fld_somhd').attr('disabled', true);
						$str_q = "
						select sm_tag SM_Tag,crpl_code __mdata from {$this->db_erp}.warehouse_shipdoc_hd where crpl_code = '$txt_mo' AND brnch_rid ='$branch_id' ";
						$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						
						if($qq->getNumRows() == 0) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
							";
							die();
						}
						else{
							$str_q_s = "
							select sm_tag SM_Tag,crpl_code __mdata from {$this->db_erp}.warehouse_shipdoc_hd where crpl_code = '$txt_mo' AND `done` = '0'";
							$qq_s =  $this->mylibzdb->myoa_sql_exec($str_q_s,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							
							if($qq_s->getNumRows() > 0) { 
								echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> ShipDoc selected is invalid !!!.</div>
								";
								die();
							}
							else{
								$rdr_q = $qq->getRowArray();
								$fld_somhd = $rdr_q['SM_Tag'];
							}
						}
						$data['rlist'] = $q->getResultArray();
						$rdr = $q->getRowArray();
						//$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
						$drdate = substr($rdr['dr_date'],0,10);
						echo "<script type=\"text/javascript\"> 
								jQuery('#fld_drdate').val('{$drdate}');
								jQuery('#fld_somhd').val('{$fld_somhd}');
								</script>";
						
					}//END CROSS DOCKING
					else{ //shipdoc ni wanbee aanj  price is a cost uprice is srp WANBEEEEEEEEEEEEEEEEEEEEEEEEEE
					$str_qry = "SELECT bbb.`recid`,
					ddd.`ART_CODE` ART_CODE,
					ddd.`ART_DESC` ART_DESC,
					aaa.`encd` dr_date,
					ddd.`ART_SKU` ART_SKU,
					bbb.`price` ucost,
					IFNULL(bbb.`uprice`,ddd.`ART_UPRICE`) uprice,
					SUM(bbb.`qty`) qty_corrected,
					NULL mtkn_mndttr,
					SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
					SUM(bbb.`qty`) qty,
					NULL qty_claim,
					NULL SM_Tag

					FROM {$this->db_erp}.`aa_wshe_crpl_dt` aaa 
					JOIN {$this->db_erp}.`aa_wshe_crpl_item` bbb 
					ON (aaa.`recid` = bbb.`crpl_id`) 
					LEFT JOIN {$this->db_erp}.`mst_article` ddd 
					ON (bbb.`mat_code` = ddd.`ART_CODE`)
					WHERE aaa.`header`= '{$txt_mo}' AND (aaa.`frm_plnt_id` ='5')
					GROUP BY ddd.`ART_CODE`
					ORDER BY ddd.`ART_CODE`";


					$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
					//$q = $this->dblinks->query($str_qry);
					if($q->getNumRows()>0){
						if(!($supp_id_n == '7325')){ //AANJ suplier
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Supplier selected is invalid !!!.</div>
							";
							die();
						}
						$fld_somhd = '';
						//VALIDATION OF BRANCH CHECKING PER DR//jQuery('#fld_somhd').attr('disabled', true);
						$str_q = "
						select sm_tag SM_Tag,crpl_code __mdata from {$this->db_erp}.aa_trx_crpl where crpl_code = '$txt_mo' AND brnch_rid ='$branch_id'";
						$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						
						if($qq->getNumRows() == 0) { 
							echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
							";
							die();
						}
						else{
							$rdr_q = $qq->getRowArray();
							$fld_somhd = $rdr_q['SM_Tag'];
						}
						$data['rlist'] = $q->getResultArray();
						$rdr = $q->getRowArray();
						$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
						echo "<script type=\"text/javascript\"> 
								jQuery('#fld_drdate').val('{$drdate}');
								jQuery('#fld_somhd').val('{$fld_somhd}');
								
											</script>";
						
					}
					else{//GROCERY
						if($txt_mo_d == "GRO"){
							
							$str_qrys = "
							SELECT  `drno`,GROUP_CONCAT(`trx_no` ORDER BY `trx_no` ASC SEPARATOR ', ') `trx_no` FROM {$this->db_erp}.`trx_manrecs_hd` WHERE `drno` = '$txt_mo' GROUP BY `drno`
							";
							$qs = $this->mylibzdb->myoa_sql_exec($str_qrys,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

							if($qs->getNumRows()>0){
								$rr = $qs->getRowArray();
								$trx_no = $rr['trx_no'];
								
								$str_qry = "
								 SELECT aaa.`recid`,
									ddd.`ART_CODE` ART_CODE,
									ddd.`ART_DESC` ART_DESC,
									bbb.`expectedDateDel` dr_date,
									ddd.`ART_SKU` ART_SKU,
									aaa.`unitCost` ucost,
									ddd.`ART_UPRICE` uprice,
									NULL qty_corrected,
									NULL mtkn_mndttr,
									SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
									aaa.`purchaseQty` qty,
									NULL qty_claim,
									NULL exp_date,
									NULL SM_Tag

									FROM {$this->db_erp}.`trx_pobr_dt` aaa 
									JOIN {$this->db_erp}.`trx_pobr_hd` bbb 
									ON (aaa.`hdID` = bbb.`recid`) 
									LEFT JOIN {$this->db_erp}.`mst_article` ddd 
									ON (aaa.`ItemCode` = ddd.`ART_CODE`)
									WHERE bbb.`sysctrl_seqn`= '{$txt_mo}'
									AND bbb.`poStatus`= 'A'
									AND ddd.`ART_CODE` NOT IN (SELECT mat_code FROM {$this->db_erp}.`trx_manrecs_dt` WHERE `trx_no` IN ($trx_no))
								";	
								// var_dump($str_qry);
								// die();
							}//end if
							else{
								$str_qry = "
								SELECT aaa.`recid`,
									ddd.`ART_CODE` ART_CODE,
									ddd.`ART_DESC` ART_DESC,
									bbb.`expectedDateDel` dr_date,
									ddd.`ART_SKU` ART_SKU,
									aaa.`unitCost` ucost,
									ddd.`ART_UPRICE` uprice,
									NULL qty_corrected,
									NULL mtkn_mndttr,
									SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
									aaa.`purchaseQty` qty,
									NULL qty_claim,
									NULL exp_date,
									NULL SM_Tag

									FROM {$this->db_erp}.`trx_pobr_dt` aaa 
									JOIN {$this->db_erp}.`trx_pobr_hd` bbb 
									ON (aaa.`hdID` = bbb.`recid`) 
									LEFT JOIN {$this->db_erp}.`mst_article` ddd 
									ON (aaa.`ItemCode` = ddd.`ART_CODE`)
									WHERE bbb.`sysctrl_seqn`= '{$txt_mo}'
									AND bbb.`poStatus`= 'A' ";
							}//end else
						}//endif

						// //IF PARTIAL lang nireceived
						// if(!empty($trx_no)){
						// 	$str_qry = "
						// 	SELECT aaa.`recid`,
						// 		ddd.`ART_CODE` ART_CODE,
						// 		ddd.`ART_DESC` ART_DESC,
						// 		bbb.`expectedDateDel` dr_date,
						// 		ddd.`ART_SKU` ART_SKU,
						// 		aaa.`unitCost` ucost,
						// 		aaa.`grossCost` uprice,
						// 		NULL qty_corrected,
						// 		NULL mtkn_mndttr,
						// 		SHA2(CONCAT(ddd.`recid`,'$mpw_tkn'),384) mtkn_artmtr,
						// 		aaa.`purchaseQty` qty,
						// 		NULL exp_date,
						// 		NULL SM_Tag

						// 		FROM {$this->db_erp}.`trx_pobr_dt` aaa 
						// 		JOIN {$this->db_erp}.`trx_pobr_hd` bbb 
						// 		ON (aaa.`hdID` = bbb.`recid`) 
						// 		LEFT JOIN {$this->db_erp}.`mst_article` ddd 
						// 		ON (aaa.`ItemCode` = ddd.`ART_CODE`)
						// 		WHERE bbb.`sysctrl_seqn`= '{$txt_mo}'
						// 		AND ddd.`ART_CODE` NOT IN (SELECT mat_code FROM {$this->db_erp}.`trx_manrecs_dt` WHERE `trx_no` = '$trx_no')
							   
						// 	UNION ALL

						// 	SELECT
						// 	    a.`recid`,
						// 	    b.`ART_CODE`,
						// 	    b.`ART_DESC`,
						// 	    NULL dr_date,
						// 	    b.`ART_SKU`,
						// 	    a.`ucost`,
						// 	    a.`uprice`,
						// 	    a.`qty_corrected`,
						// 	    SHA2(CONCAT(a.`recid`,'{$mpw_tkn}'),384) mtkn_mndttr,
						// 	    SHA2(CONCAT(b.`recid`,'{$mpw_tkn}'),384) mtkn_artmtr,
						//         a.`qty`,
						//         a.`exp_date`,
						// 	    NULL SM_Tag
						// 	    FROM
						// 	    {$this->db_erp}.`trx_manrecs_dt` a
						// 	    LEFT JOIN
						// 	    {$this->db_erp}.`mst_article` b
						// 	    ON
						// 	    a.`mat_rid` = b.`recid`
						// 	    WHERE
						// 	    `trx_no` = '$trx_no'
							
						// 	";
							//var_dump($str_qry);
							
						
						
						$q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
						//$q = $this->dblinks->query($str_qry);
						if($q->getNumRows()>0){
							//VALIDATION OF BRANCH CHECKING PER DR
							$str_q = "
							select sysctrl_seqn __mdata from {$this->db_erp}.trx_pobr_hd where sysctrl_seqn = '$txt_mo' AND branchID ='$branch_id'";
							$qq =  $this->mylibzdb->myoa_sql_exec($str_q,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
							
							if($qq->getNumRows() == 0) { 
								echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong><strong>Status</strong> Branch selected is invalid !!!.</div>
								";
								die();
							}
							$data['rlist'] = $q->getResultArray();
							$rdr = $q->getRowArray();
							$drdate = $this->mylibz->mydate_mmddyyyy($rdr['dr_date']);
							$fld_somhd = $rdr['SM_Tag'];
							echo "<script type=\"text/javascript\"> 
									jQuery('#fld_drdate').val('{$drdate}');
									jQuery('#fld_somhd').val('{$fld_somhd}');
								</script>";
							
						}
						else{
							echo "<script type=\"text/javascript\"> 
									function __nores_data() { 
										try { 
											jQuery('#myModSysNoRes').modal('show');
										} catch(err) { 
											var mtxt = 'There was an error on this page.\\n';
											mtxt += 'Error description: ' + err.message;
											mtxt += '\\nClick OK to continue.';
											alert(mtxt);
											return false;
										}  //end try 
									} 
									__nores_data();
						</script>";
						}//endelse 4 sixun
					}//endesle3 smc
				}//else 2 gwemc
			} //else 1 gro
		} //else 5 pullout
	} //else 6 wanbee
	} //else 7 crossdocking
		}//Tshirt and pants

		return $data;
	}  //end mo_select_items_rcv
	
	
	
	public function get_prod_line() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$adata = array();
		$str = "
		SELECT
			aa.	recid,concat(trim(aa.`PRODL_CODE`),'xOx',trim(aa.`PRODL_DESC`)) __mdata,  
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_product_line` aa
		WHERE `PRODL_RFLAG` = 'Y' ORDER BY aa.`PRODL_DESC`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$adata[] = $row['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end get_prod_line
	
	public function get_mst_gender() { 
		$cuser              = $this->mylibzdb->mysys_user();
		$mpw_tkn            = $this->mylibzdb->mpw_tkn();
		$adata = array();
		$str = "
		SELECT
			aa.	recid,concat(trim(aa.`GNDR_CODE`),'xOx',trim(aa.`GNDR_NAME`)) __mdata,  
			sha2(concat(aa.recid,'{$mpw_tkn}'),384) mtkn_rid 
		FROM {$this->db_erp}.`mst_gender` aa
		WHERE `MREC_FLAG` = 'A' ORDER BY aa.`GNDR_NAME`
		";
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				$adata[] = $row['__mdata'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	} //end get_prod_line
	
	public function get_ctr_new_dr($class,$supp,$dbname,$mfld='') { 
		$str = "
		CREATE TABLE if not exists {$dbname}.`myctr_stkcode` (
		  `CTR_YEAR` varchar(4) DEFAULT '0000',
		  `CTR_MONTH` varchar(2) DEFAULT '00',
		  `CTR_DAY` varchar(2) DEFAULT '00',
		  `CTRL_NO01` varchar(15) DEFAULT '00000000',
		  `CTRL_NO02` varchar(15) DEFAULT '00000000',
		  `CTRL_NO03` varchar(15) DEFAULT '00000000',
		  `CTRL_NO04` varchar(15) DEFAULT '00000000',
		  `CTRL_NO05` varchar(15) DEFAULT '00000000',
		  `CTRL_NO06` varchar(15) DEFAULT '00000000',
		  `CTRL_NO07` varchar(15) DEFAULT '00000000',
		  `CTRL_NO08` varchar(15) DEFAULT '00000000',
		  `CTRL_NO09` varchar(15) DEFAULT '00000000',
		  `CTRL_NO10` varchar(15) DEFAULT '00000000',
		  `CTRL_NO11` varchar(15) DEFAULT '00000000',
		  `SS_CTR` varchar(15) DEFAULT '000000',
		  UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$xfield = (empty($mfld) ? 'CTRL_NO01' : $mfld);
		
		$str = "select date(now()) XSYSDATE";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rdate = $q->getRowArray();
		$xsysdate = $rdate['XSYSDATE'];
		$xsysdate_exp = explode('-', $xsysdate);
		$xsysyear =  $xsysdate_exp[0];
		$xsysmonth = $xsysdate_exp[1];
		$xsysday = $xsysdate_exp[2];
		
		$str = "select {$xfield} from {$dbname}.myctr_stkcode WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday'  limit 1";
		$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qctr->getNumRows() == 0) {
			$xnumb = '0000000001';
			$str = "insert into {$dbname}.myctr_stkcode (CTR_YEAR,CTR_MONTH,CTR_DAY,{$xfield}) values('$xsysyear','$xsysmonth','$xsysday','$xnumb')";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$qctr->freeResult();
		} else {
			$qctr->freeResult();
			$str = "select {$xfield} MYFIELD from {$dbname}.myctr_stkcode WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday' limit 1";
			$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rctr = $qctr->getRowArray();
			if(trim($rctr['MYFIELD'],' ') == '') { 
				$xnumb = '0000000001';
			} else {
				$xnumb = $rctr['MYFIELD'];
				$str = "select ('{$xnumb}' + 1) XNUMB";
				$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rctr = $qctr->getRowArray();
				$xnumb = trim($rctr['XNUMB'],' ');
				$xnumb = str_pad($xnumb + 0,10,"0",STR_PAD_LEFT);
				$str = "update {$dbname}.myctr_stkcode set {$xfield} = '{$xnumb}'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		}
		return  $class. substr($xsysyear, -2, 2) . $xsysmonth . $xsysday . $xnumb;//.$supp
	} //end get_ctr_new_dr

	public function get_ctr_promotions($class,$supp,$dbname,$mfld='') { 
		$str = "
		CREATE TABLE if not exists {$dbname}.`myctr_promotions` (
		  `CTR_YEAR` varchar(4) DEFAULT '0000',
		  `CTR_MONTH` varchar(2) DEFAULT '00',
		  `CTR_DAY` varchar(2) DEFAULT '00',
		  `CTRL_NO01` varchar(15) DEFAULT '00000000',
		  `CTRL_NO02` varchar(15) DEFAULT '00000000',
		  `CTRL_NO03` varchar(15) DEFAULT '00000000',
		  `CTRL_NO04` varchar(15) DEFAULT '00000000',
		  `CTRL_NO05` varchar(15) DEFAULT '00000000',
		  `CTRL_NO06` varchar(15) DEFAULT '00000000',
		  `CTRL_NO07` varchar(15) DEFAULT '00000000',
		  `CTRL_NO08` varchar(15) DEFAULT '00000000',
		  `CTRL_NO09` varchar(15) DEFAULT '00000000',
		  `CTRL_NO10` varchar(15) DEFAULT '00000000',
		  `CTRL_NO11` varchar(15) DEFAULT '00000000',
		  `SS_CTR` varchar(15) DEFAULT '000000',
		  UNIQUE KEY `ctr01` (`CTR_YEAR`,`CTR_MONTH`,`CTR_DAY`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
		$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		$xfield = (empty($mfld) ? 'CTRL_NO01' : $mfld);
		
		$str = "select date(now()) XSYSDATE";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rdate = $q->getRowArray();
		$xsysdate = $rdate['XSYSDATE'];
		$xsysdate_exp = explode('-', $xsysdate);
		$xsysyear =  $xsysdate_exp[0];
		$xsysmonth = $xsysdate_exp[1];
		$xsysday = $xsysdate_exp[2];
		
		$str = "select {$xfield} from {$dbname}.myctr_promotions WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday'  limit 1";
		$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qctr->getNumRows() == 0) {
			$xnumb = '000001';
			$str = "insert into {$dbname}.myctr_promotions (CTR_YEAR,CTR_MONTH,CTR_DAY,{$xfield}) values('$xsysyear','$xsysmonth','$xsysday','$xnumb')";
			$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$qctr->freeResult();
		} else {
			$qctr->freeResult();
			$str = "select {$xfield} MYFIELD from {$dbname}.myctr_promotions WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday' limit 1";
			$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rctr = $qctr->getRowArray();
			if(trim($rctr['MYFIELD'],' ') == '') { 
				$xnumb = '000001';
			} else {
				$xnumb = $rctr['MYFIELD'];
				$str = "select ('{$xnumb}' + 1) XNUMB";
				$qctr = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rctr = $qctr->getRowArray();
				$xnumb = trim($rctr['XNUMB'],' ');
				$xnumb = str_pad($xnumb + 0,6,"0",STR_PAD_LEFT);
				$str = "update {$dbname}.myctr_promotions set {$xfield} = '{$xnumb}' WHERE CTR_YEAR = '$xsysyear' AND CTR_MONTH = '$xsysmonth' AND CTR_DAY = '$xsysday'";
				$this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		}
		return  $class. substr($xsysyear, -2, 2) . $xsysmonth . $xsysday . $xnumb;//.$supp
	} //end get_ctr_promotions

	public function get_promo_percent_cost_cap($dbname='',$promotype='') { 
		$promocappercent = 0;
		$str = "select PROMO_PERCENT_COST_CAP from {$dbname}.mst_pos_promotion_capping where `PROMO_TYPE` = '$promotype'";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0): 
			$rw = $q->getRowArray();
			$promocappercent = $rw['PROMO_PERCENT_COST_CAP'];
		endif;
		$q->freeResult();
		return $promocappercent;
	}  //end get_promo_percent_cost_cap
	
} //end main class
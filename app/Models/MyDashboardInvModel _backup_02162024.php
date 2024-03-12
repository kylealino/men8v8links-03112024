<?php
namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Files\File;
use CodeIgniter\Files\Exceptions\FileException;

class MyDashboardInvModel extends Model
{
    
    public function __construct() { 
        parent::__construct();
		$this->myusermod = model('App\Models\MyUserModel');
		$this->mylibzdb = model('App\Models\MyLibzDBModel');
        $this->mylibzsys = model('App\Models\MyLibzSysModel');
		$this->mydataz = model('App\Models\MyDatumModel');
        $this->db_erp = $this->myusermod->mydbname->medb(0);
        $this->db_temp = $this->myusermod->mydbname->medb(2);
		$this->request = \Config\Services::request();     
		$this->cuser = $this->myusermod->mysys_user();
		$this->mpw_tkn = $this->myusermod->mpw_tkn();
        $this->mydatum =  new MyDatumModel();
        $this->dbx = $this->mylibzdb->dbx;   
    }

    public function dashb_inv_recs($npages = 1,$npagelimit = 10,$msearchrec=''){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
        $fld_d2brnch    = $this->request->getVar('fld_d2brnch');
        $fld_itmgrparea_s = $this->request->getVar('fld_brancharea');
        $report    = $this->request->getVar('report');

        $optn_rpt          = '';
        $optn_branch       = "";
        $optn_order        = "	ORDER BY encd_date ASC";
        $str_branchgrparea = "";

        $str_date = "";

        $defDate = $this->getfistAndLastday();
        $firstdate = $defDate['firstday'];
        $str_date = "AND date(aa.`encd_date`) >='2024-01-01' AND date(aa.`encd_date`) <= NOW()";

        if((!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) ){
            //$msearchrec = $this->dbx->escape_str($msearchrec);
            $str_date = " AND (date(aa.`encd_date`) >= date('{$fld_d2dtfrm}') AND  date(aa.`encd_date`) <= date('{$fld_d2dtto}'))";
        }

        $str_optn = "";
        if(!empty($msearchrec)) { 
            $msearchrec = $this->dbx->escapeString($msearchrec);
            $str_optn = " AND
            (aa.`trx_no` LIKE '%{$msearchrec}%' OR aa.`drno` LIKE '%{$msearchrec}%'  )";
        }
        
        //get_branch
        if(!empty($fld_d2brnch)):
            $str="SELECT recid FROM mst_companyBranch WHERE `BRNCH_NAME` = '{$fld_d2brnch}'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $qry->getRowArray();
            $branch_id = $rw['recid'];
            $optn_branch = "AND (aa.`branch_id`) = '$branch_id' ";
        endif;

        //AREA AND GROUP
        if(!empty($fld_itmgrparea_s)){
            $str_branchgrparea = " AND (cc.`BRNCH_GROUP` ='$fld_itmgrparea_s')";
            //END BRANCH
        }
        
        //START DATE NG CAD VERIFICATION
        $claims_revised_date = '2023-11-01';
        $claims_revised_date2 = '2023-12-21';
        $optn_rpt_join ='';

        if(!empty($report)):
            switch ($report) {
                case '2':
                    $optn_rpt = "AND (aa.`claim_tag` = 'N')"; //AND (DATEDIFF(CURDATE(),aa.`encd_date`) <=7) 
                break;

                case '3':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR CORRECTED oR FOR FINAL RCV
                break;

                case '4':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')";//CORRECTED
                break;

                case '6':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND  (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')"; // FOR VERIFY CAD// FOR VERIFY CAD
                    break;
                case '5':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR VALIDATED
                    $optn_order = "	ORDER BY claim_date ASC";	
                break;

                case '7':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')";//nFOR VALIDATED
                    $optn_order = "	ORDER BY claim_date ASC";	
                break;

                case '8':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND  (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')"; //nFOR VERIFY CAD// FOR VERIFY CAD
                break;

                case '9':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') AND (ee.`u_module` = 'UPD_RECEIVING_AUTOPOST_13D')";//NOT ORRECTED
                    $optn_rpt_join = "JOIN {$this->db_erp}.`trx_manrecs_rcv_ulogs` ee ON (aa.`trx_no` = ee.`trx_no`)";
                break;

                case '10':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`is_reviewed` = 'N') AND  (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')"; // FOR SOD
                break;
                case '11':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`is_reviewed` = 'N') AND  (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')"; //FOR SOD REVIEW
                break;
                case '12':
                    $optn_rpt = "AND ((aa.`counter_tag` = 'N' AND aa.`claim_tag` = 'N')  OR ((aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N'))))"; //FOR COUNTERED
                break;
                case '13':
                    $optn_rpt = "AND aa.`counter_tag` = 'Y'"; //COUNTERED
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
        sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_arttr,
        (SELECT SUM(xx.`qty_corrected`) FROM trx_manrecs_dt xx WHERE aa.`recid` = xx.`mrhd_rid`) QUANTITY,
        (SELECT SUM(xx.`qty_corrected` * xx.`ucost`) FROM trx_manrecs_dt xx WHERE aa.`recid` = xx.`mrhd_rid`)COST,
        (SELECT SUM(xx.`qty_corrected` * xx.`uprice`) FROM trx_manrecs_dt xx WHERE aa.`recid` = xx.`mrhd_rid`) AMOUNT
        FROM {$this->db_erp}.`trx_manrecs_hd` aa    
        JOIN {$this->db_erp}.`mst_company` bb
        ON (aa.`comp_id` = bb.`recid`)
        JOIN {$this->db_erp}.`mst_companyBranch` cc
        ON (aa.`branch_id` = cc.`recid`)
        JOIN {$this->db_erp}.`mst_vendor` dd
        ON (aa.`supplier_id` = dd.`recid`)

        {$optn_rpt_join}
        WHERE aa.`flag` = 'R' 
        {$optn_branch} {$optn_rpt} {$str_branchgrparea} {$str_optn} {$str_date}
        ";

        //tinangal yung condition para maging para sa lahat na
        //AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773' OR aa.`supplier_id` = '7805')
        $str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
        $nstart = $nstart < 0 ? 0 : $nstart; 
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;

        if ($report == '5') {
            $str = "
            SELECT * from ({$strqry}) oa ORDER BY `claim_date` ASC limit {$nstart},{$npagelimit} ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
        }else{
            $str = "
            SELECT * from ({$strqry}) oa limit {$nstart},{$npagelimit} ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    
        }

		if($qry->resultID->num_rows > 0) { 
			$data['rlist'] = $qry->getResultArray();
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
            $data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
        
		return $data;
    }

    public function get_crplData($drno){
		$tag = 'N';
		$str = "SELECT
		  `reftag`
		  from trx_crpl
		  WHERE  `crpl_code` = '$drno'";
		$qry = $this->myusermod->mylibzdb->myoa_sql_exec2($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($qry->getNumRows() > 0 ):
		 $row = $qry->getRowArray();
		 $tag = $row['reftag'];
		endif;

		return $tag;
	}

    public function dashb_inv_recs_dl(){
        $cuser          = $this->myusermod->mysys_user();
        $mpw_tkn        = $this->myusermod->mpw_tkn();
        $cuserrema      = $this->myusermod->mysys_userrema();
        $fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
        $fld_d2brnch    = $this->request->getVar('fld_d2brnch');
        $fld_itmgrparea_s = $this->request->getVar('fld_brancharea');
        $report    = $this->request->getVar('report');

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000502')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		}             

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000503')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000505')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000507')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000508')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000509')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 


        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000510')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000511')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000513')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 


        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000514')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000515')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000517')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000518')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000519')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		} 

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

        $str_date = "";
    
        $defDate = $this->getfistAndLastday();
        $firstdate = $defDate['firstday'];
        $str_date = "AND date(aa.`encd_date`) >='{$firstdate}' AND date(aa.`encd_date`) <= NOW()";

        if((!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) ){
            //$msearchrec = $this->dbx->escape_str($msearchrec);
            $str_date .= " AND (date(aa.`encd_date`) >= date('{$fld_d2dtfrm}') AND  date(aa.`encd_date`) <= date('{$fld_d2dtto}'))";
        }
        
        //get_branch
        if(!empty($fld_d2brnch)):
            $str="SELECT recid FROM mst_companyBranch WHERE `BRNCH_NAME` = '{$fld_d2brnch}'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $qry->getRowArray();
            $branch_id = $rw['recid'];
            $optn_branch = "AND (aa.`branch_id`) = '$branch_id' ";
        endif;


        //AREA AND GROUP
        if(!empty($fld_itmgrparea_s)){
            $str_branchgrparea = " AND (cc.`BRNCH_GROUP` ='$fld_itmgrparea_s')";
            //END BRANCH
        }
        
        //START DATE NG CAD VERIFICATION
        $claims_revised_date = '2023-11-01';
        $claims_revised_date2 = '2023-12-21';

        if(!empty($report)):
            switch ($report) {
				case '2':
                    $optn_rpt = "AND (aa.`claim_tag` = 'N')"; //AND (DATEDIFF(CURDATE(),aa.`encd_date`) <=7) 
                    break;
                    case '3':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR CORRECTED oR FOR FINAL RCV
                    break;
                    case '4':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')";//CORRECTED
                    break;
                    case '6':
                        $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND  (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')"; // FOR VERIFY CAD// FOR VERIFY CAD
                        break;
                    case '5':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')";//FOR VALIDATED
                    $optn_order = "	ORDER BY claim_date ASC";	
                    break;
                    case '7':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')";//nFOR VALIDATED
                    $optn_order = "	ORDER BY claim_date ASC";	
                    break;
    
                    case '8':
                        $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND  (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')"; //nFOR VERIFY CAD// FOR VERIFY CAD
                    break;
    
                    case '9':
                    $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(DATE(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') AND (ee.`u_module` = 'UPD_RECEIVING_AUTOPOST_13D')";//CORRECTED
                    $optn_rpt_join = "JOIN {$this->db_erp}.`trx_manrecs_rcv_ulogs` ee ON (aa.`trx_no` = ee.`trx_no`)";
                    break;
    
                    case '10':
                        $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`is_reviewed` = 'N') AND  (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N')"; // FOR SOD
                        break;
                    case '11':
                        $optn_rpt = "AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`is_reviewed` = 'N') AND  (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y')"; //FOR SOD REVIEW
                    break;
                    case '12':
                        $optn_rpt = "AND ((aa.`counter_tag` = 'N' AND aa.`claim_tag` = 'N')  OR ((aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N'))))"; //FOR COUNTERED
                    break;
                    case '13':
                        $optn_rpt = "AND aa.`counter_tag` = 'Y'"; //COUNTERED
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
            WHERE aa.`flag` = 'R' 
            {$optn_branch} {$optn_rpt} {$str_branchgrparea} {$str_date}
            {$optn_order}
            ";
            $q = $this->myusermod->mylibzdb->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            //AND (aa.`supplier_id` = '3' OR aa.`supplier_id` = '1425' OR aa.`supplier_id` = '4773') pinatangal
            $res=1;
            if($q->getNumRows() > 0) { 
                //IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
                $mpathdn   = ROOTPATH; 
                $mpathdest = $mpathdn . 'public/downloads/me'; 
                $cdate     = date('Ymd');
                $cfiletmp  = 'rcvclaims_report_' . $cdate . $this->mylibzsys->random_string(15) .  '.xls' ;
                $cfiledest = $mpathdest . '/' . $cfiletmp;
                $cfilelnk  = site_url() . 'downloads/me/' . $cfiletmp;

                if(file_exists($cfiledest)) {
                unlink($cfiledest);
                }
                $fh = fopen($cfiledest, 'w');
                fwrite($fh, $chtml);
                fclose($fh); 
                chmod($cfiledest, 0755);

                // $file_name = 'rcvclaims_report_'.'_'.date('Ymd'). '.xls';
                // $mpathdn   = ROOTPATH;
                // $_csv_path = '/public/downloads/';
                // $filepath = $mpathdn.$_csv_path.$file_name;
                // $cfilelnk = site_url() . 'public/downloads/' . $file_name; 

                //SEND TO UALAM
                // $this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                
                //SECUREW FILES
                // if(file_exists($cfiledest)) {
                // unlink($cfiledest);
                // }
                // $fh = fopen($cfiledest, 'w');
                // fwrite($fh, $chtml);
                // fclose($fh); 
                // chmod($cfiledest, 0755);
                $qrw = $q->getResultArray();
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
                                    <td>".$row['dr_date']."</td>
                                    <td>".$row['rcv_date']."</td>
                                    <td>".$row['date_in']."</td>
                                    <td>".$row['claim_date']."</td>
                                    <td>".$row['validated_date']."</td>
                                    <td>".$row['final_date']."</td>
                                    <td>".$row['muser']."</td>
                                    <td>".$row['hd_sm_tags']."</td>
                                    <td>".$row['df_tag']."</td>
                                    <td>".$row['post_tag']."</td>
                                    <td>".$row['claim_tag']."</td>
                                    <td>".$row['hd_remarks']."</td>
                                    <td>".$row['encd_date']."</td>

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
                wshe_report_dl();
                function wshe_report_dl() {
                    window.location.href = '{$cfilelnk}';
                }
            </script>
            
            ";
        echo $chtmljs;
	}

    public function dashb_inv_process(){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
        $fld_d2brnch    = $this->request->getVar('fld_d2brnch');
        $fld_itmgrparea_s = $this->request->getVar('fld_brancharea');
        
        $optn_branch      = "";
        $str_branchgrparea = "";
        $str_date = "";
        $optn_branch_mo = "";
        $optn_branch_cwo = "";
        $optn_branch_crpl = "";
        $optn_branch_tap = "";
    
        $defDate = $this->getfistAndLastday();
        $firstdate = $defDate['firstday'];
        $str_date = "AND date(aa.`encd_date`) >='2024-01-01' AND date(aa.`encd_date`) <= NOW()";
        $str_date_mo = " AND date(`mo_date`) >='2024-01-01' AND date(`mo_date`) <= NOW()";
        $str_date_cwo = "AND date(`done_date`) >='2024-01-01' AND date(`done_date`) <= NOW()";
        $str_date_crpl = "AND date(`date_encd`) >='2024-01-01' AND date(`date_encd`) <= NOW()";
        $str_date_tap = "AND date(`done_date`) >='2024-01-01' AND date(`done_date`) <= NOW()";

        //get_branch
        if(!empty($fld_d2brnch)):
            $str="SELECT recid FROM mst_companyBranch WHERE `BRNCH_NAME` = '{$fld_d2brnch}'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $qry->getRowArray();
            $branch_id = $rw['recid'];
            $optn_branch = "AND (aa.`branch_id`) = '$branch_id' ";
            $optn_branch_mo = "AND (`branch_id`) = '$branch_id' ";
            $optn_branch_cwo = "AND (`brnch_rid`) = '$branch_id' ";
            $optn_branch_crpl = "AND (`brnch_rid`) = '$branch_id' ";
            $optn_branch_tap = "AND (`brnch_rid`) = '$branch_id' ";
        endif;

        //AREA AND GROUP
        if(!empty($fld_itmgrparea_s)){
            $str_branchgrparea = " AND (cc.`BRNCH_GROUP` ='$fld_itmgrparea_s')";
            //END BRANCH
        }
    
        if((!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) ){
            //$msearchrec = $this->dbx->escape_str($msearchrec);
            $str_date = " AND (date(aa.`encd_date`) >= date('{$fld_d2dtfrm}') AND  date(aa.`encd_date`) <= date('{$fld_d2dtto}'))";
            $str_date_mo = " AND (date(`mo_date`) >= date('{$fld_d2dtfrm}') AND  date(`mo_date`) <= date('{$fld_d2dtto}'))";
            $str_date_cwo = " AND (date(`done_date`) >= date('{$fld_d2dtfrm}') AND  date(`done_date`) <= date('{$fld_d2dtto}'))";
            $str_date_crpl = " WHERE (date(`date_encd`) >= date('{$fld_d2dtfrm}') AND  date(`date_encd`) <= date('{$fld_d2dtto}'))";
            $str_date_tap = " AND (date(`done_date`) >= date('{$fld_d2dtfrm}') AND  date(`done_date`) <= date('{$fld_d2dtto}'))";
        }
        //AND (DATEDIFF(CURDATE(),aa.`encd_date`) <=7) remove oct272022 new claims revised

        //START DATE NG CAD VERIFICATION
        $claims_revised_date = '2023-11-01';
        $claims_revised_date2 = '2023-12-21';
        $str ="
            SELECT
                xx.`rcvng`,
                fct.`forcountered`,
                ct.`countered`,
                yy.`noclaims`,
                bb.`claimsvalidation`,
                zz.`finalclaims`,
                aa.`validatedclaims`,
                cc.`forverify`,
                dd.`nvalidatedclaims`,
                ee.`notverify`,
                ff.`ncorrectedclaims`,
                gg.`forreview`,
                hh.`notreview`
            FROM
            (
                (SELECT COUNT(aa.`recid`) rcvng FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) WHERE aa.`flag` = 'R' {$optn_branch} {$str_date} {$str_branchgrparea} ) xx,

                (SELECT COUNT(aa.`recid`) forcountered FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) WHERE aa.`flag` = 'R' AND ((aa.`counter_tag` = 'N' AND aa.`claim_tag` = 'N') OR ((aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N'))) {$str_date} {$optn_branch} {$str_branchgrparea} ) fct,

                (SELECT COUNT(aa.`recid`) countered FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) WHERE aa.`flag` = 'R' AND aa.`counter_tag` = 'Y' {$optn_branch} {$str_date} {$str_branchgrparea} ) ct,

                (SELECT COUNT(aa.`recid`) noclaims,DATEDIFF(CURDATE(),aa.`encd_date`) lessSeven  FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'N') {$optn_branch}  {$str_date} {$str_branchgrparea} ) yy,

                (SELECT COUNT(aa.`recid`) claimsvalidation FROM {$this->db_erp}.`trx_manrecs_hd`aa  JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)  WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) bb,

                (SELECT COUNT(aa.`recid`) finalclaims FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) zz,

                (SELECT COUNT(aa.`recid`) validatedclaims FROM {$this->db_erp}.`trx_manrecs_hd` aa  JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)  WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y')  AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') {$optn_branch} {$str_date} {$str_branchgrparea}) aa,

                (SELECT COUNT(aa.`recid`) forverify FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) cc,

                (SELECT COUNT(aa.`recid`) nvalidatedclaims FROM {$this->db_erp}.`trx_manrecs_hd`aa  JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)  WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'N') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') {$optn_branch} {$str_date} {$str_branchgrparea} ) dd,

                (SELECT COUNT(aa.`recid`) notverify FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'N') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') {$optn_branch} {$str_date} {$str_branchgrparea} ) ee,

                (SELECT COUNT(aa.`recid`) ncorrectedclaims FROM {$this->db_erp}.`trx_manrecs_hd` aa  JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) JOIN {$this->db_erp}.`trx_manrecs_rcv_ulogs` dd ON (aa.`trx_no` = dd.`trx_no`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y')  AND (aa.`is_validated` = 'Y') AND (IF(date(aa.`encd_date`) >= DATE('$claims_revised_date'),aa.`is_verified` = 'Y',aa.`is_verified` = 'N')) AND (IF(date(aa.`verified_date`) >= DATE('$claims_revised_date2'),aa.`is_reviewed` = 'Y',aa.`is_reviewed` = 'N')) AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') AND (dd.`u_module` = 'UPD_RECEIVING_AUTOPOST_13D') {$optn_branch} {$str_date} {$str_branchgrparea}) ff,

                (SELECT COUNT(aa.`recid`) forreview FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y') AND (aa.`is_reviewed` = 'N')  AND (aa.`df_tag` = 'D' AND aa.`post_tag` = 'N') {$optn_branch} {$str_date} {$str_branchgrparea} ) gg,

                (SELECT COUNT(aa.`recid`) notreview FROM {$this->db_erp}.`trx_manrecs_hd`aa JOIN {$this->db_erp}.`mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`)   WHERE aa.`flag` = 'R' AND (aa.`claim_tag` = 'Y') AND (aa.`is_validated` = 'Y') AND (aa.`is_verified` = 'Y')  AND (aa.`is_reviewed` = 'N') AND (aa.`df_tag` = 'F' AND aa.`post_tag` = 'Y') {$optn_branch} {$str_date} {$str_branchgrparea} ) hh

            )";

    
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $row = $qry->getRowArray();
            $rcvng            = $row['rcvng'];
            $forcountered     = $row['forcountered'];
            $countered        = $row['countered'];
            $noclaims         = $row['noclaims'];
            $claimsvalidation = $row['claimsvalidation'];
            $finalclaims      = $row['finalclaims'];
            $validatedclaims  = $row['validatedclaims'];
            $forverify  	  = $row['forverify'];
            $nvalidatedclaims  	  = $row['nvalidatedclaims'];
            $notverify  	  = $row['notverify'];
            $ncorrectedclaims  	  = $row['ncorrectedclaims'];
            $forreview  	  = $row['forreview'];
            $notreview  	  = $row['notreview'];

            $str="
                SELECT
                    ii.`created_mn`,
                    jj.`created_cwo`,
                    kk.`created_crpl`,

                    ll.`intransit_mn`,
                    mm.`intransit_cwo`,
                    nn.`intransit_crpl`,
                    oo.`cancelled_cwo`,
                    ss.`cancelled_mn`
                FROM
                (

                    (SELECT COUNT(aa.`recid`) created_mn FROM `trx_manrecs_mo_hd` aa JOIN `mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) {$str_date_mo} {$optn_branch_mo} {$str_branchgrparea}) ii,
                    (SELECT COUNT(aa.`recid`) created_cwo FROM `warehouse_shipdoc_hd` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) WHERE done = '1' AND aa.`is_receive` = 'N' {$str_date_cwo} {$optn_branch_cwo} {$str_branchgrparea}) jj,
                    (SELECT COUNT(aa.`recid`) created_crpl FROM `trx_crpl` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) {$str_date_crpl} {$optn_branch_crpl} {$str_branchgrparea}) kk,

                    (SELECT COUNT(aa.`recid`) intransit_mn FROM `trx_manrecs_mo_hd` aa JOIN `mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) WHERE aa.`is_receive` = 'N' {$str_date_mo} {$optn_branch_mo} {$str_branchgrparea}) ll,
                    (SELECT COUNT(aa.`recid`) intransit_cwo FROM `warehouse_shipdoc_hd` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) WHERE aa.`is_intransit` = 'Y' AND aa.`is_receive` = 'N' and aa.`done` = '1' {$str_date_cwo} {$optn_branch_cwo} {$str_branchgrparea}) mm,
                    (SELECT COUNT(aa.`recid`) intransit_crpl FROM `trx_crpl` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) {$str_date_crpl} {$optn_branch_crpl} {$str_branchgrparea}) nn,
                    
                    (SELECT COUNT(aa.`is_cancelled`) cancelled_cwo FROM `warehouse_shipdoc_hd` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) WHERE aa.`is_cancelled` = 'Y' {$str_date_cwo} {$optn_branch_cwo} {$str_branchgrparea}) oo,
                    (SELECT COUNT(aa.`is_cancelled`) cancelled_mn FROM `trx_manrecs_mo_hd` aa JOIN `mst_companyBranch` cc ON (aa.`branch_id` = cc.`recid`) WHERE aa.`is_cancelled` = 'Y' {$str_date_mo} {$optn_branch_mo} {$str_branchgrparea}) ss
                )
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $row = $qry->getRowArray();
            $created_mn  	  = $row['created_mn'];
            $created_cwo  	  = $row['created_cwo'];
            $created_crpl  	  = $row['created_crpl'];
            $intransit_mn  	  = $row['intransit_mn'];
            $intransit_cwo  	  = $row['intransit_cwo'];
            $intransit_crpl  	  = $row['intransit_crpl'];
            $cancelled_cwo  	  = $row['cancelled_cwo'];
            $cancelled_mn  	  = $row['cancelled_mn'];

            $str="
                SELECT
                pp.`created_tap`,
                qq.`intransit_tap`,
                rr.`cancelled_tap`

                FROM
                (

                    (SELECT COUNT(aa.`recid`) created_tap FROM `tap_shipdoc_hd` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) WHERE done = '1' {$str_date_tap} {$optn_branch_tap} {$str_branchgrparea}) pp,
                    (SELECT COUNT(aa.`recid`) intransit_tap FROM `tap_shipdoc_hd` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) WHERE aa.`is_intransit` = 'Y' AND aa.`is_receive` = 'N' AND aa.`done` = '1' {$str_date_tap} {$optn_branch_tap} {$str_branchgrparea}) qq,
                    (SELECT COUNT(aa.`is_cancelled`) cancelled_tap FROM `tap_shipdoc_hd` aa JOIN `mst_companyBranch` cc ON (aa.`brnch_rid` = cc.`recid`) WHERE aa.`is_cancelled` = 'Y' {$str_date_tap} {$optn_branch_tap} {$str_branchgrparea}) rr
                )
                
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $row = $qry->getRowArray();
            $created_tap  	  = $row['created_tap'];
            $intransit_tap  	  = $row['intransit_tap'];
            $cancelled_tap  	  = $row['cancelled_tap'];
            $total_cancelled = ($cancelled_cwo + $cancelled_mn + $cancelled_tap);

            $total_created = ($created_mn + $created_cwo + $created_crpl + $created_tap);
            $total_intransit = ($intransit_mn + $intransit_cwo + $intransit_crpl + $intransit_tap );



    
        echo "<script type=\"text/javascript\"> 
            function __me_refresh_data() { 
                try { 
    
                    $('#txt-receive').html('{$rcvng}');
                    $('#txt-forcountered').html('{$forcountered}');
                    $('#txt-countered').html('{$countered}');
                    $('#txt-receivedwoclaims').html('{$noclaims}'); 
                    $('#txt-claimsforval').html('{$claimsvalidation}');
                    $('#txt-claimsforcorrect').html('{$finalclaims}');
                    $('#txt-correctedclaims').html('{$validatedclaims}');
                    $('#txt-claimsforveri').html('{$forverify}');
                    $('#txt-notvalclaims').html('{$nvalidatedclaims}');
                    $('#txt-notverclaims').html('{$notverify}');
                    $('#txt-notcorclaims').html('{$ncorrectedclaims}');
                    $('#txt-claimsforrev').html('{$forreview}'); 
                    $('#txt-notrevclaims').html('{$notreview}');
                    $('#txt-created').html('{$total_created}');
                    $('#txt-intransit').html('{$total_intransit}');
                    $('#txt-cancelled').html('{$total_cancelled}');
                    
                } 
                catch(err){ 
                    var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
                    __me_refresh_data();
                    </script>";

        
    }

    public function getfistAndLastday(){
		
		$str = "SELECT DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) firstday,LAST_DAY(CURDATE()) lastday";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		return $qry->getRowArray();

		$qry->freeResult();

	}

    public function dashb_inv_validate(){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
		$mtkn_rid    = $this->request->getVar('fld_mktn');
		$trxno = $this->request->getVar('trxno');

		$str = "
		SELECT 
		recid
		FROM
		{$this->db_erp}.`trx_manrecs_hd` a
		WHERE
		sha2(concat(a.`recid`,'{$mpw_tkn}'),384) = '{$mtkn_rid}'
		AND a.`df_tag` = 'F' 
		AND a.`post_tag` = 'Y'
		limit 1
		";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

		if($q->getNumRows() > 0){
			echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Info.<br/></strong>Transaction already Posted</div>";
			die();
		}

		$arrfield = '';
		$arrfield .= "is_validated" . "->" . "Y" . "\n";
		$arrfield .= "validated_by" . "->" . $cuser . "\n";

		$str = "UPDATE
	  	{$this->db_erp}.`trx_manrecs_hd`
		SET
		`is_validated` = 'Y',
		`validated_by`= '$cuser',
		`validated_date` = now()
		WHERE sha2(concat(`recid`,'{$mpw_tkn}'),384) = '$mtkn_rid' ";

        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		// $this->mylibz->logs_trx_rcv_audit($this->db_erp,$arrfield,$cuser,$trxno,'U','UPD_RECEIVING_VALIDATE','R');
		echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Info.<br/></strong>Successfully validated the transaction</div>";


	}

    public function dashb_inv_validate_dl(){
        $cuser          = $this->myusermod->mysys_user();
        $mpw_tkn        = $this->myusermod->mpw_tkn();
		$mtkn_rid    = $this->request->getVar('fld_mktn');
		$trxno = $this->request->getVar('trxno');
		$btn_id = $this->request->getVar('btn_id');


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
            </tr>
        ";

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
        JOIN
        {$this->db_erp}.`mst_article` b
        ON
        a.`mat_rid` = b.`recid`
        WHERE
        sha2(concat(a.`mrhd_rid`,'{$mpw_tkn}'),384) = '{$mtkn_rid}'
        ORDER BY 
        a.`recid`";


        $q = $this->myusermod->mylibzdb->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        $res=1;
        if($q->getNumRows() > 0) { 
            //IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
            $mpathdn   = ROOTPATH; 
            $mpathdest = $mpathdn . 'public/downloads/me'; 
            $cdate     = date('Ymd');
            $cfiletmp  = 'rcvclaimsval_report_' . $cdate .$this->mylibzsys->random_string(9) . '.xls' ;
            $cfiledest = $mpathdest . '/' . $cfiletmp;
            $cfilelnk  = site_url() . 'downloads/me/' . $cfiletmp;
            //SEND TO UALAM
            // $this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_VAL_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            if(file_exists($cfiledest)) {
            unlink($cfiledest);
            }
            $fh = fopen($cfiledest, 'w');
            fwrite($fh, $chtml);
            fclose($fh); 
            chmod($cfiledest, 0755);

            

            $qrw = $q->getResultArray();
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
                                <td>".$row['encd']."</td>

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
        wshe_report_dl();
        function wshe_report_dl() {
            window.location.href = '{$cfilelnk}';
            $('#{$btn_id}').prop('disabled',true);
        }
        </script>
        ";
        echo $chtmljs;

	}

    public function dashb_inv_forcountered(){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $trxno = $this->request->getVar('trxno');
        
        if(!$this->myusermod->ua_mod_access_verify($this->db_erp,$this->cuser,'02','0004','000516')) { 
			echo "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> Access RESTRICTED!!!</div>";
			die();
		}         

        $str="
            SELECT counter_tag FROM trx_manrecs_hd WHERE trx_no = '$trxno' AND counter_tag = 'N'
        ";
        $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if ($qry->getNumRows() > 0) {
            $str="
                UPDATE trx_manrecs_hd SET counter_tag = 'Y', counter_date ='NOW()', countered_by = '$cuser' WHERE trx_no = '$trxno'
            ";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            echo "
                <div class=\"alert alert-success mb-0\" role=\"alert\">Data Countered Successfully!!! Series No:{$trxno} </div>
            ";
        }else{
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"> Series No:{$trxno} is already countered!!! </div>";
        }


        
    }

    public function dashb_inv_created_recs($npages = 1,$npagelimit = 10,$msearchrec=''){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
        $fld_d2brnch    = $this->request->getVar('fld_d2brnch');
        $fld_itmgrparea_s = $this->request->getVar('fld_brancharea');
        $sd_type = $this->request->getVar('sd_type');

        $optn_branch      = "";
        $str_branchgrparea = "";
        $str_date = "";
        $optn_branch_mo = "";
        $optn_branch_cwo = "";
        $optn_branch_tap = "";
        //get_branch
        if(!empty($fld_d2brnch)):
            $str="SELECT recid FROM mst_companyBranch WHERE `BRNCH_NAME` = '{$fld_d2brnch}'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $qry->getRowArray();
            $branch_id = $rw['recid'];
            $optn_branch = "AND (aa.`branch_id`) = '$branch_id' ";
            $optn_branch_mo = "AND (`branch_id`) = '$branch_id' ";
            $optn_branch_cwo = "AND (`brnch_rid`) = '$branch_id' ";
            $optn_branch_tap = "AND (`brnch_rid`) = '$branch_id' ";
        endif;

        if ($sd_type == 'CWO') {
            
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`crpl_code` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT
                `recid`,
                `crpl_code`,
                `plate_no`,
                `brnch`,
                `user`,
                `date_encd`,
                `is_cancelled`
            FROM 
                warehouse_shipdoc_hd
            WHERE 
                DATE(`done_date`) >= '2024-01-01'
                {$str_optn} {$optn_branch_cwo}
            ";
        }elseif ($sd_type == 'MN') {

            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`motrx_no` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT 
                `recid`,
                `motrx_no`,
                `mo_plateno`,
                `branch_id`,
                `muser`,
                `encd_date`,
                `is_cancelled`
            FROM 
                trx_manrecs_mo_hd
            WHERE
                DATE(`mo_date`) >= '2024-01-01'
                {$str_optn} {$optn_branch_mo}
            ";
        }elseif ($sd_type == 'TAP') {

            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`crpl_code` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT
                `recid`,
                `crpl_code`,
                `plate_no`,
                `brnch`,
                `user`,
                `date_encd`,
                `is_cancelled`
            FROM 
                tap_shipdoc_hd
            WHERE
                DATE(`done_date`) >= '2024-01-01'
                {$str_optn} {$optn_branch_tap}
            ";
        }

        $str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
        $nstart = $nstart < 0 ? 0 : $nstart; 
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
        SELECT * from ({$strqry}) oa ORDER BY oa.`recid` DESC limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->resultID->num_rows > 0) { 
			$data['rlist'] = $qry->getResultArray();
            $data['fld_d2dtfrm'] = $fld_d2dtfrm;
            $data['fld_d2dtto'] = $fld_d2dtto;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
    }

    public function dashb_inv_created_dl(){
        $cuser          = $this->myusermod->mysys_user();
        $mpw_tkn        = $this->myusermod->mpw_tkn();
		$mtkn_rid    = $this->request->getVar('fld_mktn');
        $sd_type = $this->request->getVar('sd_type');

        
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
            <th class=\"noborder\" colspan=\"7\">CREATED DR {$sd_type} REPORT</th>
            </tr>
            <tr class=\"header-tr\">
            <th class=\"noborder\" colspan=\"7\"></th>
            </tr>
            <tr class=\"header-tr-addr\">
            <th class=\"noborder\" colspan=\"7\">&nbsp;</th>
            </tr>
            <tr class =\"header-theme-purple text-white\">
            <th class=\"noborder\">NO</th>
            <th class=\"noborder\">Transaction No</th>
            <th class=\"noborder\">Plate No</th>
            <th class=\"noborder\">Branch</th>
            <th class=\"noborder\">User</th>
            <th class=\"noborder\">Date</th>
            </tr>
        ";
        if ($sd_type == 'CWO') {
            $strqry = "
            SELECT
                `crpl_code` transaction_no,
                `plate_no` plate_no,
                `brnch` branch,
                `user` user,
                `date_encd` encd_date
            FROM 
                warehouse_shipdoc_hd
            AND
                DATE(`done_date`) >= '2024-01-01'
            ";
        }elseif ($sd_type == 'MN') {
            $strqry = "
            SELECT 
                `motrx_no` transaction_no,
                `mo_plateno` plate_no,
                `branch_id` branch,
                `muser` user,
                `encd_date` encd_date
            FROM 
                trx_manrecs_mo_hd
            WHERE
                DATE(`mo_date`) >= '2024-01-01'
            ";
        }elseif ($sd_type == 'TAP') {
            $strqry = "
            SELECT
                `crpl_code` transaction_no,
                `plate_no` plate_no,
                `brnch` branch,
                `user` user,
                `date_encd` encd_date
            FROM 
                tap_shipdoc_hd
            WHERE
                DATE(`done_date`) >= '2024-01-01'
            ";
        }

        $q = $this->myusermod->mylibzdb->myoa_sql_exec($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        $res=1;
        if($q->getNumRows() > 0) { 
            //IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
            $mpathdn   = ROOTPATH; 
            $mpathdest = $mpathdn . 'public/downloads/me'; 
            $cdate     = date('Ymd');
            $cfiletmp  = 'createddr_report_' . $cdate .$this->mylibzsys->random_string(9) . '.xls' ;
            $cfiledest = $mpathdest . '/' . $cfiletmp;
            $cfilelnk  = site_url() . 'downloads/me/' . $cfiletmp;
            //SEND TO UALAM
            // $this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_VAL_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            if(file_exists($cfiledest)) {
            unlink($cfiledest);
            }
            $fh = fopen($cfiledest, 'w');
            fwrite($fh, $chtml);
            fclose($fh); 
            chmod($cfiledest, 0755);

            $qrw = $q->getResultArray();
            foreach($qrw as $row):
                $chtml = "<tr class=\"data-nm\">
                                <td>".$res."</td>
                                <td>'".$row['transaction_no']."</td>
                                <td>".$row['plate_no']."</td>
                                <td>".$row['branch']."</td>
                                <td>".$row['user']."</td>
                                <td>".$row['encd_date']."</td> 
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
        wshe_report_dl();
        function wshe_report_dl() {
            window.location.href = '{$cfilelnk}';
        }
        </script>
        ";
        echo $chtmljs;
    }

    public function dashb_inv_intransit_recs($npages = 1,$npagelimit = 10,$msearchrec=''){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
        $fld_d2brnch    = $this->request->getVar('fld_d2brnch');
        $fld_itmgrparea_s = $this->request->getVar('fld_brancharea');
        $sd_type = $this->request->getVar('sd_type');

        $optn_branch      = "";
        $str_branchgrparea = "";
        $str_date = "";
        $optn_branch_mo = "";
        $optn_branch_cwo = "";
        $optn_branch_tap = "";

        //get_branch
        if(!empty($fld_d2brnch)):
            $str="SELECT recid FROM mst_companyBranch WHERE `BRNCH_NAME` = '{$fld_d2brnch}'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $qry->getRowArray();
            $branch_id = $rw['recid'];
            $optn_branch = "AND (aa.`branch_id`) = '$branch_id' ";
            $optn_branch_mo = "AND (`branch_id`) = '$branch_id' ";
            $optn_branch_cwo = "AND (`brnch_rid`) = '$branch_id' ";
            $optn_branch_tap = "AND (`brnch_rid`) = '$branch_id' ";
        endif;

        if ($sd_type == 'CWO') {
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`crpl_code` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT
                `recid`,
                `crpl_code`,
                `plate_no`,
                `brnch`,
                `user`,
                `date_encd`,
                `cwo_filename`
            FROM 
                warehouse_shipdoc_hd
            WHERE 
                `is_intransit` = 'Y' AND `is_receive` = 'N'
            AND
                DATE(`done_date`) >= '2024-01-01'

            {$str_optn} {$optn_branch_cwo}
            ";
        }elseif ($sd_type == 'MN') {
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`motrx_no` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT 
                `recid`,
                `motrx_no`,
                `mo_plateno`,
                `branch_id`,
                `muser`,
                `encd_date`,
                `mn_filename`
            FROM 
                trx_manrecs_mo_hd
            WHERE
                DATE(`mo_date`) >= '2024-01-01'
            {$str_optn} {$optn_branch_mo}
            ";
        }elseif ($sd_type == 'TAP') {
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`crpl_code` LIKE '%{$msearchrec}%') ";
            }
            $strqry = "
            SELECT
                `recid`,
                `crpl_code`,
                `plate_no`,
                `brnch`,
                `user`,
                `date_encd`,
                `tap_filename`
            FROM 
                tap_shipdoc_hd
            WHERE
                `is_intransit` = 'Y' AND `is_receive` = 'N' AND
                DATE(`done_date`) >= '2024-01-01'
            {$str_optn} {$optn_branch_tap}
            ";
        }

        $str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
        $nstart = $nstart < 0 ? 0 : $nstart; 
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa ORDER BY oa.`recid` DESC limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->resultID->num_rows > 0) { 
			$data['rlist'] = $qry->getResultArray();
            $data['fld_d2dtfrm'] = $fld_d2dtfrm;
            $data['fld_d2dtto'] = $fld_d2dtto;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
    }

    public function dashb_inv_intransit_dl(){
        $cuser          = $this->myusermod->mysys_user();
        $mpw_tkn        = $this->myusermod->mpw_tkn();
		$mtkn_rid    = $this->request->getVar('fld_mktn');
        $sd_type = $this->request->getVar('sd_type');

        
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
            <th class=\"noborder\" colspan=\"7\">INTRANSIT DR {$sd_type} REPORT</th>
            </tr>
            <tr class=\"header-tr\">
            <th class=\"noborder\" colspan=\"7\"></th>
            </tr>
            <tr class=\"header-tr-addr\">
            <th class=\"noborder\" colspan=\"7\">&nbsp;</th>
            </tr>
            <tr class =\"header-theme-purple text-white\">
            <th class=\"noborder\">NO</th>
            <th class=\"noborder\">Transaction No</th>
            <th class=\"noborder\">Plate No</th>
            <th class=\"noborder\">Branch</th>
            <th class=\"noborder\">User</th>
            <th class=\"noborder\">Date</th>
            </tr>
        ";
        if ($sd_type == 'CWO') {
            $strqry = "
            SELECT
                `crpl_code` transaction_no,
                `plate_no` plate_no,
                `brnch` branch,
                `user` user,
                `done_date` encd_date
            FROM 
                warehouse_shipdoc_hd
            WHERE 
                `is_intransit` = 'Y'
            AND
                DATE(`done_date`) >= '2024-01-01'
            ";
        }elseif ($sd_type == 'MN') {
            $strqry = "
            SELECT 
                `motrx_no` transaction_no,
                `mo_plateno` plate_no,
                `branch_id` branch,
                `muser` user,
                `encd_date` encd_date
            FROM 
                trx_manrecs_mo_hd
            WHERE
                DATE(`mo_date`) >= '2024-01-01'
            ";
        }elseif ($sd_type == 'TAP') {
            $strqry = "
            SELECT
                `crpl_code` transaction_no,
                `plate_no` plate_no,
                `brnch` branch,
                `user` user,
                `done_date` encd_date
            FROM 
                tap_shipdoc_hd
            WHERE
                DATE(`done_date`) >= '2024-01-01'
            ";
        }

        $q = $this->myusermod->mylibzdb->myoa_sql_exec2($strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        $res=1;
        if($q->getNumRows() > 0) { 
            //IF QUERY HAS ALTEAST ONE RESULT CREATE PATH and FILE
            $mpathdn   = ROOTPATH; 
            $mpathdest = $mpathdn . 'public/downloads/me'; 
            $cdate     = date('Ymd');
            $cfiletmp  = 'intransitdr_report_' . $cdate .$this->mylibzsys->random_string(9) . '.xls' ;
            $cfiledest = $mpathdest . '/' . $cfiletmp;
            $cfilelnk  = site_url() . 'downloads/me/' . $cfiletmp;
            //SEND TO UALAM
            // $this->mylibz->user_logs_activity_module($this->db_erp,'RCVCLAIMS_VAL_DOWNLOAD','',$cuser."_FN_".$cfiletmp,$strqry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            if(file_exists($cfiledest)) {
            unlink($cfiledest);
            }
            $fh = fopen($cfiledest, 'w');
            fwrite($fh, $chtml);
            fclose($fh); 
            chmod($cfiledest, 0755);

            $qrw = $q->getResultArray();
            foreach($qrw as $row):
                $chtml = "<tr class=\"data-nm\">
                                <td>".$res."</td>
                                <td>'".$row['transaction_no']."</td>
                                <td>".$row['plate_no']."</td>
                                <td>".$row['branch']."</td>
                                <td>".$row['user']."</td>
                                <td>".$row['encd_date']."</td> 
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
        wshe_report_dl();
        function wshe_report_dl() {
            window.location.href = '{$cfilelnk}';
        }
        </script>
        ";
        echo $chtmljs;
    }

    public function dashb_inv_intransit_rcv(){
        $cuser          = $this->myusermod->mysys_user();
        $mpw_tkn        = $this->myusermod->mpw_tkn();
        $trxno = $this->request->getVar('trxno');
        $sd_type = $this->request->getVar('sd_type');


        $str="
            SELECT recid FROM trx_manrecs_hd WHERE drno = '$trxno';
        ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if ($q->resultID->num_rows> 0 ) {
            echo "<div class=\"alert alert-danger mb-0\" role=\"alert\">This transaction is already received... </div>	";
            die();
        }

        //GLOBAL DEFAULT VARIABLES
        //fld_dftag
        $fld_dftag ='D';
        //fld_rems
        $fld_rems = '';
        //fld_drno
        $fld_somhd = 'D';
        //fld_drno
        $fld_drno = $trxno;
        //fld_supplier
        $str="
        SELECT `recid` FROM `mst_vendor` WHERE `recid` = '7805'
        ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $q->getRowArray();
        $fld_supplier = $rw['recid'];
        //mmn_rid
        $mmn_rid = '';

        $sd_type = substr($trxno, 0, 2);

        if ($sd_type == 'CW') {
            $str="
                SELECT cwo_filename FROM warehouse_shipdoc_hd WHERE crpl_code = '$trxno'
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $cwo_filename = $rw['cwo_filename'];
            if (empty($cwo_filename)) {
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\">[CWO] Attachment of receipt not found!!!</div>	";
                die();
            }
        }elseif ($sd_type == 'MN') {
            
            $str="
                SELECT mn_filename FROM trx_manrecs_mo_hd WHERE motrx_no = '$trxno'
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $mn_filename = $rw['mn_filename'];
            if (empty($mn_filename)) {
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\">[MN] Attachment of receipt not found!!!</div>	";
                die();
            }
        }elseif ($sd_type == 'TA') {
            $str="
                SELECT tap_filename FROM tap_shipdoc_hd WHERE crpl_code = '$trxno'
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $tap_filename = $rw['tap_filename'];
            if (empty($tap_filename)) {
                echo "<div class=\"alert alert-danger mb-0\" role=\"alert\">[TAP] Attachment of receipt not found!!!</div>	";
                die();
            }
        }


        //CWO TEMPORARY SAVING TO PANSAMANTALA .38
        if ($sd_type == 'CW') {

            //fld_subtqty && fld_subtcost && fld_subtamt
            $str="
            SELECT 
                (bbb.`price` * SUM(bbb.`qty`)) tcost,
                (bbb.`uprice` * SUM(bbb.`qty`)) tprice,
                SUM(bbb.`qty`) qty,
                aaa.`encd`
            FROM 
                `warehouse_shipdoc_dt` aaa 
            JOIN 
                `warehouse_shipdoc_item` bbb 
            ON 
                (aaa.`recid` = bbb.`wshe_out_id`)
            LEFT JOIN 
                `mst_article` ddd 
            ON 
                (bbb.`mat_rid` = ddd.`recid`)
            WHERE 
                aaa.`header`= '{$trxno}' AND (aaa.`is_out` ='1')
            GROUP BY 
                ddd.`ART_CODE`

            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $fld_subtqty = $rw['qty'];
            $fld_subtcost = $rw['tcost'];
            $fld_subtamt = $rw['tprice'];
            $fld_drdate = $rw['encd'];
            
            //fld_Company && fld_area_code &&  __rfrom
            $str = "
            SELECT
                a.`recid`,
                c.`brnch_rid`
            FROM
                mst_company a
            JOIN
                mst_companyBranch b
            ON
                a.`recid` = b.`COMP_ID`
            JOIN
                warehouse_shipdoc_hd c
            ON
                b.`recid` = c.`brnch_rid`
            WHERE
                c.`crpl_code` = '$trxno'";
        
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $fld_Company = $rw['recid'];
            $fld_area_code = $rw['brnch_rid'];
            $__rfrom = $rw['brnch_rid'];

            $str_dt_temp = "
                SELECT 
                '$mmn_rid',
                '$trxno',
                ddd.`recid`,
                ddd.`ART_CODE` ART_CODE,
                bbb.`price` ucost,
                (bbb.`price` * SUM(bbb.`qty`)) tcost,
                IFNULL(bbb.`uprice`,ddd.`ART_UPRICE`) uprice,
                (bbb.`uprice` * SUM(bbb.`qty`)) tprice,
                SUM(bbb.`qty`) qty_corrected,
                SUM(bbb.`qty`) qty,
                'TALLY',
                NOW(),
                '$fld_rems',
                '$cuser',
                NOW()
        
                FROM `warehouse_shipdoc_dt` aaa 
                JOIN `warehouse_shipdoc_item` bbb 
                ON (aaa.`recid` = bbb.`wshe_out_id`)
                LEFT JOIN `mst_article` ddd 
                ON (bbb.`mat_rid` = ddd.`recid`)
                WHERE aaa.`header`= '{$trxno}' AND (aaa.`is_out` ='1')
                GROUP BY ddd.`ART_CODE`
                ORDER BY ddd.`ART_CODE`
            ";

        }elseif($sd_type == 'MN'){

                //fld_subtqty && fld_subtcost && fld_subtamt
                $str="

                SELECT 
                    (aaa.`ucost` *  SUM(aaa.`qty` * aaa.`convf`)) tcost,
                    (aaa.`uprice` *  SUM(aaa.`qty` * aaa.`convf`)) tprice,
                    SUM(aaa.`qty` * aaa.`convf`) qty,
                    aaa.`encd`
                FROM 
                    `trx_manrecs_mo_dt` aaa 
                JOIN 
                    `trx_manrecs_mo_hd` bbb 
                ON 
                    aaa.`motrx_no` = bbb.`motrx_no`
                WHERE 
                    bbb.`flag` = 'R' AND bbb.`motrx_no`= '{$trxno}'

                ";
                $q = $this->mylibzdb->myoa_sql_exec2($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                $rw = $q->getRowArray();
                $fld_subtqty = $rw['qty'];
                $fld_subtcost = $rw['tcost'];
                $fld_subtamt = $rw['tprice'];
                $fld_drdate = $rw['encd'];

                //fld_Company && fld_area_code &&  __rfrom
                $str = "
                SELECT
                    a.`recid`,
                    c.`branch_id`
                FROM
                    mst_company a
                JOIN
                    mst_companyBranch b
                ON
                    a.`recid` = b.`COMP_ID`
                JOIN
                    trx_manrecs_mo_hd c
                ON
                    b.`recid` = c.`branch_id`
                WHERE
                    c.`motrx_no` = '$trxno'";
            
                $q = $this->mylibzdb->myoa_sql_exec2($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
                $rw = $q->getRowArray();
                $fld_Company = $rw['recid'];
                $fld_area_code = $rw['branch_id'];
                $__rfrom = $rw['branch_id'];
                
                $str_dt_temp = "
                    SELECT 
                        '$mmn_rid',
                        '$trxno',
                        ddd.`recid`,
                        ddd.`ART_CODE` ART_CODE,
                        aaa.`ucost` ucost,
                        (aaa.`ucost` *  SUM(aaa.`qty` * aaa.`convf`)) tcost,
                        aaa.`uprice` uprice,
                        (aaa.`uprice` *  SUM(aaa.`qty` * aaa.`convf`)) tprice,
                        SUM(aaa.`qty` * aaa.`convf`) qty_corrected,
                        SUM(aaa.`qty` * aaa.`convf`) qty,
                        'TALLY',
                        NOW(),
                        '$fld_rems',
                        '$cuser',
                        NOW()
        
                    FROM 
                        `trx_manrecs_mo_dt` aaa 
                    JOIN 
                        `trx_manrecs_mo_hd` bbb 
                    ON 
                        aaa.`motrx_no` = bbb.`motrx_no`
                    LEFT JOIN 
                        `mst_article` ddd 
                    ON 
                        aaa.`mat_code` = ddd.`ART_CODE`
                    WHERE 
                        bbb.`flag` = 'R' AND bbb.`motrx_no`= '{$trxno}'
                    GROUP BY 
                        aaa.`mat_code`    
                ";

        }elseif($sd_type == 'TA'){

            //fld_Company && fld_area_code &&  __rfrom
            $str = "
            SELECT
                a.`recid`,
                c.`brnch_rid`
            FROM
                mst_company a
            JOIN
                mst_companyBranch b
            ON
                a.`recid` = b.`COMP_ID`
            JOIN
                tap_shipdoc_hd c
            ON
                b.`recid` = c.`brnch_rid`
            WHERE
                c.`crpl_code` = '$trxno'";
        
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $fld_Company = $rw['recid'];
            $fld_area_code = $rw['brnch_rid'];
            $__rfrom = $rw['brnch_rid'];

            //SETUP FOR TEMPORARY DT SAVING

            $str = "
                DELETE FROM tap_shipdoc_item WHERE header = '{$trxno}'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        
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
                (SELECT DATE(`date_encd`) FROM tap_shipdoc_hd WHERE crpl_code = d.`header`) dr_date,
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
                tap_shipdoc_dt d
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
                d.`header` = '{$trxno}' AND a.`is_out` = '1'
            GROUP BY 
                b.`mat_code`, b.`fgreq_trxno`
            ORDER BY 
                b.`fgreq_trxno`
			";

            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            //TEMPORARY HD TOTAL SUM 
            $str = "
            SELECT 
                (a.`ucost` * SUM(a.`qty`)) tcost,
                (a.`uprice` * SUM(a.`qty`)) tprice,
                SUM(a.`qty`) qty,
                a.`dr_date`
            FROM
                `tap_shipdoc_item` a
            JOIN
                `mst_article` b
            ON
                a.`ART_CODE` = b.`ART_CODE`
            WHERE 
                header = '{$trxno}'
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $q->getRowArray();
            $fld_subtqty = $rw['qty'];
            $fld_subtcost = $rw['tcost'];
            $fld_subtamt = $rw['tprice'];
            $fld_drdate = $rw['dr_date'];

            //TEMPORARY DT SAVING
            $str_dt_temp = "
            SELECT 
                '$mmn_rid',
                '$trxno',
                b.`recid`,
                a.`ART_CODE`,
                a.`ucost`,
                (a.`ucost` * SUM(a.`qty`)) tcost,
                IFNULL(a.`uprice`,b.`ART_UPRICE`) uprice,
                (a.`uprice` * SUM(a.`qty`)) tprice,
                SUM(a.`qty_corrected`) qty_corrected,
                SUM(a.`qty`) qty,
                'TALLY',
                NOW(),
                '$fld_rems',
                '$cuser',
                NOW()
            FROM
                `tap_shipdoc_item` a
            JOIN
                `mst_article` b
            ON
                a.`ART_CODE` = b.`ART_CODE`
            WHERE 
                header = '{$trxno}'
            GROUP BY
                ART_CODE
            ";
        }

        //generate transaction no.
        $fld_txttrx_no =  $this->mydatum->get_ctr_new($fld_Company.$fld_area_code,$fld_supplier.$fld_drno,$this->db_erp,'CTRL_NO03');//TRANSACTION NO
        
        $metoken = $this->mylibzsys->random_string(9);
        $trx_manrecs_hd_tmp = "trx_manrecs_hd_tmp_" . $metoken;
        $trx_manrecs_dt_tmp = "trx_manrecs_dt_tmp_" . $metoken;

        if($sd_type == 'TA'){

            ///////////////////////////////////////////////TERMPORARY HD SAVING////////////////////////////////////////

            $str="
                CREATE TABLE IF NOT EXISTS {$this->db_temp}.{$trx_manrecs_hd_tmp} LIKE {$this->db_erp}.`trx_manrecs_hd`;
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            $str = "
            insert into {$this->db_temp}.{$trx_manrecs_hd_tmp}
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
                VALUES (
                '$fld_txttrx_no',
                '$fld_Company',
                '$fld_area_code',
                trim('$fld_drno'),
                '$fld_drdate',
                '$fld_supplier',
                NOW(),
                NOW(),
                '$fld_somhd',
                '$fld_rems',
                '$fld_subtqty',
                '$fld_subtcost',
                '$fld_subtamt',
                '$__rfrom',
                '$cuser',
                '$fld_dftag')";
                
                
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            ///////////////////////////////////////////////TERMPORARY DT SAVING////////////////////////////////////////

            $str="
            CREATE TABLE IF NOT EXISTS {$this->db_temp}.{$trx_manrecs_dt_tmp} LIKE {$this->db_erp}.`trx_manrecs_dt`;
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            

            //insert details
            $str_qry = "
            INSERT INTO {$this->db_temp}.{$trx_manrecs_dt_tmp} (
                `mrhd_rid`,
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
                `exp_date`,
                `nremarks`,
                `muser`,
                `encd`
            )
            {$str_dt_temp}  
            ";

            $q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        }else{

            ///////////////////////////////////////////////TERMPORARY HD SAVING////////////////////////////////////////
            //set to ap2
            $str="
                CREATE TABLE IF NOT EXISTS {$this->db_temp}.{$trx_manrecs_hd_tmp} LIKE `trx_manrecs_hd`;
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            $str = "
            insert into {$this->db_temp}.{$trx_manrecs_hd_tmp}
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
                NOW(),
                NOW(),
                '$fld_somhd',
                '$fld_rems',
                '$fld_subtqty',
                '$fld_subtcost',
                '$fld_subtamt',
                '$__rfrom',
                '$cuser',
                '$fld_dftag')";
                
                
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

            ///////////////////////////////////////////////TERMPORARY DT SAVING////////////////////////////////////////
            //set to ap2
            $str="
            CREATE TABLE IF NOT EXISTS {$this->db_temp}.{$trx_manrecs_dt_tmp} LIKE `trx_manrecs_dt`;
            ";
            $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            

            //insert details
            $str_qry = "
            INSERT INTO {$this->db_temp}.{$trx_manrecs_dt_tmp} (
                `mrhd_rid`,
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
                `exp_date`,
                `nremarks`,
                `muser`,
                `encd`
            )
            {$str_dt_temp}  
            ";

            $q = $this->mylibzdb->myoa_sql_exec($str_qry,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

        
            //execute third party apps for pull push data functionality from other server to local server
            $mescript = ROOTPATH . 'app/ThirdParty/me-python/dr-data-pull-push.py';
            exec("/usr/bin/python3 $mescript {$metoken}",$output);
            //execute third party apps for pull push data functionality from other server to local server

        }

        ///////////////////////////////////////////////HD SAVING////////////////////////////////////////
        $str = "
        insert into {$this->db_erp}.`trx_manrecs_hd`
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
            SELECT
            `trx_no`,
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
            `df_tag`
            FROM
            {$this->db_temp}.{$trx_manrecs_hd_tmp}

            ";

        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);


        //GET MANRECS HD RECID

        $str="
            SELECT recid FROM trx_manrecs_hd WHERE `drno` = '$trxno'
        ";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $rw = $q->getRowArray();
        $mmn_rid = $rw['recid'];


        ///////////////////////////////////////////////DT SAVING////////////////////////////////////////

        $str = 
        "
        INSERT INTO `trx_manrecs_dt`
        (
        `mrhd_rid`,
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
        `exp_date`,
        `nremarks`,
        `muser`,
        `encd`
        )
        SELECT
        '$mmn_rid',
        `trx_no`,
        `mat_rid`,
        `mat_code`,
        `ucost`,
        `tcost`,
        `uprice`,
        `tamt`,
        `qty`,
        `qty_corrected`,
        `OLT_tag`,
        `exp_date`,
        `nremarks`,
        `muser`,
        `encd`
        FROM
        {$this->db_temp}.{$trx_manrecs_dt_tmp}
        ";

        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);



        if ($sd_type == 'CW') {
            $str="
                UPDATE warehouse_shipdoc_hd SET `is_receive` = 'Y',`received_by` = '$cuser',`received_date` = NOW() WHERE `crpl_code` = '$trxno'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
        }elseif ($sd_type == 'MN') {
            $str="
                UPDATE trx_manrecs_mo_hd SET `is_receive` = 'Y',`received_by` = '$cuser',`received_date` = NOW() WHERE `motrx_no` = '$trxno'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
        }elseif ($sd_type == 'TA') {
            $str="
                UPDATE tap_shipdoc_hd SET `is_receive` = 'Y',`received_by` = '$cuser',`received_date` = NOW() WHERE `crpl_code` = '$trxno'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
        }


        $mtkn_trxno = hash('sha384', $mmn_rid . $mpw_tkn);
        $cfilelnk  = site_url() . 'dr-trx?mtkn_trxno=' . $mtkn_trxno;
        $chtmljs = "
        <div class=\"alert alert-success mb-0\" role=\"alert\">
            RECEIVED SUCCESSFULLY! Please wait while redirecting...
        </div>	
        <script type=\"text/javascript\">
            dr_rcv_link();
            function dr_rcv_link() {
                window.location.href = '{$cfilelnk}';
            }
        </script>
        
        ";

        echo $chtmljs;

            
    }

    public function getNewFilePath($filepath){
        $mpathdn = ROOTPATH;
        $filePath = $mpathdn . $filepath;
        $finalPath = base_url(); // Assuming you want to use base_url() in CI4

        if (strpos(current_url(), 'mynlinks') !== false) {
            // Check if the file exists
            if (!file_exists($filePath)) {
                $finalPath = str_replace('mynlinks', 'myn8v8links', base_url());
            }
        } else {
            if (!file_exists($filePath)) {
                $finalPath = str_replace('myn8v8links', 'mynlinks', base_url());
            }
        }

        return $finalPath;
    }

    public function dashb_inv_intransit_cwo_upld(){
		$cuserrema = $this->myusermod->mysys_userrema();
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();

		$trxno_id	= $this->mylibzdb->me_escapeString($this->request->getVar('trxno'));
		$count_uploaded_files_isver   = 0;

		if($trxno_id != ''){
			$count_uploaded_files_isver   = count( $_FILES['images_isver']['name'] );
		}

        if($cuserrema == 'H'):
            echo "<div class=\"alert alert-success mb-0\">Only branch can upload receipt attachment.</div>";
            die();
        endif;

		$files = $_FILES;
		$image_ofile = "";

		$emp_img_path_isver = './uploads/rcv_claims/' ;
		$emp_img_upath_isver = './uploads/rcv_claims/';
		if($trxno_id != ''){
			if($count_uploaded_files_isver == 0){
				echo "For Receiving: Please select file to upload.";
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
		{$this->db_erp}.`warehouse_shipdoc_hd` a
		WHERE
		a.`crpl_code` = '{$trxno_id}'
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
			
			if($cuserrema == 'B'):
				if($trxno_id != '' ){

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

			if($cuserrema == 'B'):
				$str ="UPDATE {$this->db_erp}.`warehouse_shipdoc_hd` aa
				SET aa.`cwo_filename` = '{$__upld_filename}'

				WHERE aa.`recid` = '$trx_recid' 
                "; 

			endif;

			$q3 = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br> Uploaded successfully!</div>
			<script type=\"text/javascript\"> 
			function __cwo_refresh_data() { 
				try { 

					$('#mbtn_cwo_upld').prop('disabled',true);
					
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
					__cwo_refresh_data();
					</script>

					";

		}//select branch end
		else{
			echo "<div class=\"alert alert-success mb-0\">Transaction not found or already posted.</div>";
		}		

	}

    public function dashb_inv_intransit_mn_upld(){
		$cuserrema = $this->myusermod->mysys_userrema();
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();

		$trxno_id	= $this->mylibzdb->me_escapeString($this->request->getVar('trxno'));
		$count_uploaded_files_isver   = 0;

		if($trxno_id != ''){
			$count_uploaded_files_isver   = count( $_FILES['images_isver']['name'] );
		}

        if($cuserrema == 'H'):
            echo "<div class=\"alert alert-success mb-0\">Only branch can upload receipt attachment.</div>";
            die();
        endif;

		$files = $_FILES;
		$image_ofile = "";

		$emp_img_path_isver = './uploads/rcv_claims/' ;
		$emp_img_upath_isver = './uploads/rcv_claims/';
		if($trxno_id != ''){
			if($count_uploaded_files_isver == 0){
				echo "For Receiving: Please select file to upload.";
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
		{$this->db_erp}.`trx_manrecs_mo_hd` a
		WHERE
		a.`motrx_no` = '{$trxno_id}'
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
			
			if($cuserrema == 'B'):
				if($trxno_id != '' ){

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

			if($cuserrema == 'B'):
				$str ="UPDATE {$this->db_erp}.`trx_manrecs_mo_hd` aa
				SET aa.`mn_filename` = '{$__upld_filename}'

				WHERE aa.`recid` = '$trx_recid' 
                "; 

			endif;

			$q3 = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br> Uploaded successfully!</div>
			<script type=\"text/javascript\"> 
			function __mn_refresh_data() { 
				try { 

					$('#mbtn_mn_upld').prop('disabled',true);
					
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
					__mn_refresh_data();
					</script>

					";

		}//select branch end
		else{
			echo "<div class=\"alert alert-success mb-0\">Transaction not found or already posted.</div>";
		}		

	}

    public function dashb_inv_intransit_tap_upld(){
		$cuserrema = $this->myusermod->mysys_userrema();
		$cuser = $this->myusermod->mysys_user();
		$cuserlvl = $this->myusermod->mysys_userlvl();
		$mpw_tkn = $this->myusermod->mpw_tkn();

		$trxno_id	= $this->mylibzdb->me_escapeString($this->request->getVar('trxno'));
		$count_uploaded_files_isver   = 0;

		if($trxno_id != ''){
			$count_uploaded_files_isver   = count( $_FILES['images_isver']['name'] );
		}

        if($cuserrema == 'H'):
            echo "<div class=\"alert alert-success mb-0\">Only branch can upload receipt attachment.</div>";
            die();
        endif;

		$files = $_FILES;
		$image_ofile = "";

		$emp_img_path_isver = './uploads/rcv_claims/' ;
		$emp_img_upath_isver = './uploads/rcv_claims/';
		if($trxno_id != ''){
			if($count_uploaded_files_isver == 0){
				echo "For Receiving: Please select file to upload.";
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
		{$this->db_erp}.`tap_shipdoc_hd` a
		WHERE
		a.`crpl_code` = '{$trxno_id}'
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
			
			if($cuserrema == 'B'):
				if($trxno_id != '' ){

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

			if($cuserrema == 'B'):
				$str ="UPDATE {$this->db_erp}.`tap_shipdoc_hd` aa
				SET aa.`tap_filename` = '{$__upld_filename}'

				WHERE aa.`recid` = '$trx_recid' 
                "; 

			endif;

			$q3 = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

			echo "<div class=\"alert alert-success mb-0\"><strong>SAVE</strong><br> Uploaded successfully!</div>
			<script type=\"text/javascript\"> 
			function __tap_refresh_data() { 
				try { 

					$('#mbtn_tap_upld').prop('disabled',true);
					
				} 
				catch(err){ 
					var mtxt = 'There was an error on this page.\\n'; mtxt += 'Error description: ' + err.message; mtxt += '\\nClick OK to continue.'; alert(mtxt); return false; } } 
					__tap_refresh_data();
					</script>

					";

		}//select branch end
		else{
			echo "<div class=\"alert alert-success mb-0\">Transaction not found or already posted.</div>";
		}		

	}

	public function file_type_check_PDF_only($file_types,$file_count){
		$allowed_mime_type_arr = array('application/pdf');
		for( $j = 0; $j < $file_count; $j++ )
		{ 
			$file_type = $file_types[$j];
			if(!in_array($file_type, $allowed_mime_type_arr)){
				echo "<div class=\"alert alert-danger mb-0\" role=\"alert\"><strong>Info.<br/></strong><strong>Error</strong> Please select only <strong>PDF </strong> file.</div>";
				die();
			}

		}
	}

    public function dashb_inv_created_cancel(){
        $cuser = $this->myusermod->mysys_user();
        $trxno = $this->request->getVar('trxno');

        $sd_type = substr($trxno, 0, 2);

        if ($sd_type == 'CW') {

            $str="
                UPDATE warehouse_shipdoc_hd SET `is_cancelled` = 'Y',`cancelled_by` = '$cuser',`cancelled_date` = NOW() WHERE `crpl_code` = '$trxno'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo "<div class=\"alert alert-success mb-0\" role=\"alert\">[$trxno] Cancelled successfully... </div>	";

        }elseif ($sd_type == 'MN') {
            
            $str="
                UPDATE trx_manrecs_mo_hd SET `is_cancelled` = 'Y',`cancelled_by` = '$cuser',`cancelled_date` = NOW() WHERE `motrx_no` = '$trxno'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo "<div class=\"alert alert-success mb-0\" role=\"alert\">[$trxno] Cancelled successfully... </div>	";

        }elseif ($sd_type == 'TA') {
            $str="
                UPDATE tap_shipdoc_hd SET `is_cancelled` = 'Y',`cancelled_by` = '$cuser',`cancelled_date` = NOW() WHERE `crpl_code` = '$trxno'
            ";
            $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            
            echo "<div class=\"alert alert-success mb-0\" role=\"alert\">[$trxno] Cancelled successfully... </div>	";

        }


    }

    public function dashb_inv_cancelled_recs($npages = 1,$npagelimit = 10,$msearchrec=''){
        $cuser            = $this->myusermod->mysys_user();
        $mpw_tkn          = $this->myusermod->mpw_tkn();
        $fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
        $fld_d2brnch    = $this->request->getVar('fld_d2brnch');
        $fld_itmgrparea_s = $this->request->getVar('fld_brancharea');
        $sd_type = $this->request->getVar('sd_type');

        $optn_branch      = "";
        $str_branchgrparea = "";
        $str_date = "";
        $optn_branch_mo = "";
        $optn_branch_cwo = "";
        $optn_branch_tap = "";

        //get_branch
        if(!empty($fld_d2brnch)):
            $str="SELECT recid FROM mst_companyBranch WHERE `BRNCH_NAME` = '{$fld_d2brnch}'";
            $qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
            $rw = $qry->getRowArray();
            $branch_id = $rw['recid'];
            $optn_branch = "AND (aa.`branch_id`) = '$branch_id' ";
            $optn_branch_mo = "AND (`branch_id`) = '$branch_id' ";
            $optn_branch_cwo = "AND (`brnch_rid`) = '$branch_id' ";
            $optn_branch_tap = "AND (`brnch_rid`) = '$branch_id' ";
        endif;

        if ($sd_type == 'CWO') {
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`crpl_code` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT
                `recid`,
                `crpl_code`,
                `plate_no`,
                `brnch`,
                `cancelled_by`,
                `is_cancelled`,
                DATE(cancelled_date)cancelled_date
            FROM 
                warehouse_shipdoc_hd
            WHERE 
                `is_cancelled` = 'Y'
            AND
                DATE(`done_date`) >= '2024-01-01'

            {$str_optn} {$optn_branch_cwo}
            ";
        }elseif ($sd_type == 'MN') {
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`motrx_no` LIKE '%{$msearchrec}%') ";
            }

            $strqry = "
            SELECT 
                `recid`,
                `motrx_no`,
                `mo_plateno`,
                `branch_id`,
                `cancelled_by`,
                `is_cancelled`,
                DATE(cancelled_date)cancelled_date
            FROM 
                trx_manrecs_mo_hd
            WHERE 
                `is_cancelled` = 'Y'
            AND
                DATE(`mo_date`) >= '2024-01-01'
            {$str_optn} {$optn_branch_mo}
            ";
        }elseif ($sd_type == 'TAP') {
            $str_optn = "";
            if(!empty($msearchrec)) { 
                $msearchrec = $this->dbx->escapeString($msearchrec);
                $str_optn = " AND
                (`crpl_code` LIKE '%{$msearchrec}%') ";
            }
            $strqry = "
            SELECT
                `recid`,
                `crpl_code`,
                `plate_no`,
                `brnch`,
                `cancelled_by`,
                `is_cancelled`,
                DATE(cancelled_date)cancelled_date

            FROM
                tap_shipdoc_hd
            WHERE
                `is_cancelled` = 'Y' AND
                DATE(`done_date`) >= '2024-01-01'
            {$str_optn} {$optn_branch_tap}
            ";
        }

        $str = "
		select count(*) __nrecs from ({$strqry}) oa
		";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $qry->getRowArray();
		$npagelimit = ($npagelimit > 0 ? $npagelimit : 30);
		$nstart = ($npagelimit * ($npages - 1));
        $nstart = $nstart < 0 ? 0 : $nstart; 
		
		
		$npage_count = ceil(($rw['__nrecs'] + 0) / $npagelimit);
		$data['npage_count'] = $npage_count;
		$data['npage_curr'] = $npages;
		$str = "
		SELECT * from ({$strqry}) oa ORDER BY oa.`recid` DESC limit {$nstart},{$npagelimit} ";
		$qry = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		if($qry->resultID->num_rows > 0) { 
			$data['rlist'] = $qry->getResultArray();
            $data['fld_d2dtfrm'] = $fld_d2dtfrm;
            $data['fld_d2dtto'] = $fld_d2dtto;
		} else { 
			$data = array();
			$data['npage_count'] = 1;
			$data['npage_curr'] = 1;
			$data['rlist'] = '';
		}
		return $data;
    }
}
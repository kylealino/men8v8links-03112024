<?php 
include APPPATH .'/ThirdParty/fpdf186/fpdf.php';

$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->mymdacct = model('App\Models\MyDRRCVModel');
$cuser   = $this->myusermod->mysys_user();
$cuserlvl= "S";
$mpw_tkn = $this->myusermod->mpw_tkn();
$cusergrp = $this->myusermod->mysys_usergrp();
$cuserrema = $this->myusermod->mysys_userrema();
$this->db_erp = $mydbname->medb(0);

$rcpt_checker = "AHMED BENJAMES SUAREZ";

$fld_d2dtfrm = $request->getVar('fld_d2dtfrm');
$fld_d2dtto  = $request->getVar('fld_d2dtto');
$fld_brancharea = $request->getVar('fld_brancharea');
$fld_d2brnch  = $request->getVar('fld_d2brnch');
$fld_dlsomhd =  $request->getVar('fld_dlsomhd');
$fld_dlbuacct = $request->getVar('fld_dlbuacct');

$str="
SELECT myuserfulln FROM myusers WHERE myusername = '$cuser'
";
$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
$rw = $q->getRowArray();
$cuser_fullname = $rw['myuserfulln'];

$str="
	SELECT recid FROM `mst_companyBranch` WHERE `BRNCH_NAME` = '$fld_d2brnch'
";
$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
$rw = $q->getRowArray();
$fld_dlbranch_id = $rw['recid'];

//Dto mo ilagay yung access.
$str = "
	SELECT 
		a.`COMP_CODE`
	FROM
	{$this->db_erp}.`mst_companyBranch` a
	WHERE
	a.`recid`= '{$fld_dlbranch_id}'
";

$q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);

$valid_id = '';
if($q->getNumRows() > 0){
	$r = $q->getRowArray();
	$comp_name = $r['COMP_CODE'];
	
}
else{
	return false;
}
$str_posted ="AND ((aa.`post_tag`='N') OR (aa.`post_tag`='Y'))";//"AND (aa.`post_tag`='N')";change 111821
if($cuserlvl == 'S'){
	$str_posted ="AND ((aa.`post_tag`='N') OR (aa.`post_tag`='Y'))";
}
//TAGS
$str_dlsomhd ='';
if(!empty($fld_dlsomhd)) {
 	$str_dlsomhd ="AND (aa.`hd_sm_tags` = '$fld_dlsomhd')";
}
//WHEN USER IS USER THEN DOWNLOAD ONLY THEY ENCODE ELSE SA WILL ALL DOWNLOAD THE DATA.
if($cuserlvl=="S"){
	$str_encduser="";
}
//para sa isang user na gusto magprint sa ibang user na taga branch lang halimbawa si AD-SHANE gusto niya iprint lahat ng entry ni AD-MON
elseif($fld_dlbuacct != '') {
	$str_encduser ="AND (aa.`muser` = '$fld_dlbuacct')";
}
else{
	$str_encduser="AND (aa.`muser` = '$cuser')";

}
//  AND !(aa.`p_flag` = 'Y' ) pinatanggal noong 9/20/19
//WHEN USER IS USER SELECT A RCV DATE FROM AND TO.
$str_date="";
if((!empty($fld_d2dtto) && !empty($fld_d2dtfrm)) && (($fld_d2dtto != '--') && ($fld_d2dtfrm != '--'))){
	$str_date="AND (aa.`rcv_date` >= '{$fld_d2dtfrm}' AND  aa.`rcv_date` <= '{$fld_d2dtto}')";
}

//NLINKS: RCVNG:Remove the restriction w/ regards to multiple printing of Delivery Receipts.Sir Claudio 9/20/19 -AND !(aa.`post_tag`='N') AND !(aa.`p_flag` = 'Y' ) pinatanggal noong 9/20/19
//NLINKS: RCVNG: Can we make it na kapag posted na sya, di na sya kasama sa for printing. Lahat lang ng naka tagged as "Final" yung masasama sa printing.Delivery Receipts.-Sir Carlo 9/25/19 AND (aa.`df_tag`='F') AND (aa.`post_tag`='N')
$str = "select aa.*,
		bb.`COMP_NAME`,
		bb.`COMP_CODE`,
		cc.`BRNCH_NAME`,
		dd.`VEND_NAME`,
		SUM(ee.`qty_corrected`) ihd_subtqty,
		SUM(ee.`ucost` * ee.`qty_corrected`) ihd_subtcost,
		SUM(ee.`uprice` * ee.`qty_corrected`) ihd_subtamt,
		sha2(concat(aa.`recid`,'{$mpw_tkn}'),384) mtkn_arttr 
		 from {$this->db_erp}.`trx_manrecs_hd` aa
		  JOIN {$this->db_erp}.`trx_manrecs_dt` ee
		 ON (aa.`recid` =ee.`mrhd_rid`)
		JOIN {$this->db_erp}.`mst_company` bb
		ON (aa.`comp_id` = bb.`recid`)
		JOIN {$this->db_erp}.`mst_companyBranch` cc
		ON (aa.`branch_id` = cc.`recid`)
		JOIN {$this->db_erp}.`mst_vendor` dd
		ON (aa.`supplier_id` = dd.`recid`)
		WHERE (aa.`branch_id` = '$fld_dlbranch_id') {$str_dlsomhd} {$str_date} {$str_encduser} AND !(aa.`flag` = 'C' ) AND (aa.`df_tag`='F') {$str_posted}
		GROUP BY aa.`recid`"; //AND !(aa.`post_tag`='N') pinatanggal noong 92019 
$q3 = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
if($q3->getNumRows() == 0) { 
	return false;
}
//echo $str;
// }
$date = date("F j, Y, g:i A");
$r = $q3->getRowArray();


$pdf = new fpdf();
$pdf->AliasNbPages();
$pdf->SetTitle('BRANCH -'. $fld_d2brnch);
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);


$pdf->SetFont('Arial','',8);

// header page

$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);


// header page

$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);

/*$pdf->Image(site_url().'public/assets/images/SMC-LOGO.png',5,1,40,0,'png');
$pdf->SetXY(47,5);  
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,5,'1002-B Apolonia St. Mapulang Lupa, Valenzuela City',0,0,'L'); 

$pdf->SetXY(47,9);  
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,5,'Tel. Nos.: (02) 961-8641 / 961-8526',0,0,'L'); 
*/
$pdf->SetXY(5,18);  
$pdf->SetFont('Arial','',8);
$pdf->Cell(206,5,'BRANCH: '.$fld_d2brnch,0,0,'C'); 


$pdf->SetXY(5,27);  
$pdf->SetFont('Arial','',8);
$pdf->Cell(16.5,5,'COMPANY:',0,0,'L'); 
$pdf->SetFont('Arial','',8);
$pdf->Cell(50.5,5,$comp_name,'B',0,'L');  
$pdf->SetFont('Arial','',8);

$pdf->SetXY(150,27);  
$pdf->Cell(10.5,5,'DATE:',0,0,'L'); 
$pdf->SetFont('Arial','',8);
$pdf->Cell(45,5,$date,'B',0,'L'); 


$pdf->SetXY(5,32);  
$pdf->SetFont('Arial','',8);
$pdf->Cell(16.5,5,'FROM:',0,0,'L'); 
$pdf->SetFont('Arial','',8);
$pdf->Cell(50.5,5,$fld_d2brnch .'-'. $fld_d2dtfrm,'B',0,'L');  
$pdf->SetFont('Arial','',8);

/*$pdf->SetXY(150,32);  
$pdf->Cell(10.5,5,'USER:',0,0,'L'); 
$pdf->SetFont('Arial','',6);
$pdf->Cell(50.5,5,$cuser ,'B',0,'L');*/

//ITEMS TH
$pdf->SetFillColor(239,225,131,1);
$pdf->SetFont('Arial','B',7);
$pdf->SetXY(5,42); 
$pdf->Cell(5,4,'#',1,0,'C'); 
$pdf->Cell(25,4,'DR NO',1,0,'C'); 
$pdf->Cell(25,4,'BRANCH CODE',1,0,'C'); 
$pdf->Cell(53,4,'SUPPLIER',1,0,'C'); 
$pdf->Cell(14,4,'DR DATE',1,0,'C'); 
$pdf->Cell(14,4,'RCV DATE',1,0,'C'); 
$pdf->Cell(14,4,'DATE IN',1,0,'C');
$pdf->Cell(17,4,'QTY',1,0,'C'); 
$pdf->Cell(17,4,'COST AMT',1,0,'C'); 
$pdf->Cell(17,4,'SRP AMT',1,0,'C'); 


//footer page number
$pdf->SetY(-12);
$pdf->SetFont('Arial','',6);
$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of RECEIVED FORM: '.$fld_d2brnch,0,0,'C');

//header page number
$pdf->SetY(0);
$pdf->SetX(156);
$pdf->SetFont('Arial','',6);
$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of RECEIVED FORM: '.$fld_d2brnch,0,0,'C');

$Y = 46;
$total_qty = 0;
$box_no = 1;
$ntqty = 0;
$ntamt = 0;
$ntcost = 0;


foreach($q3->getResult() as $row){

		
	$drno = $row->drno;
	$comp = $row->COMP_CODE;
	$branch = $row->BRNCH_NAME;
	$supp = $row->VEND_NAME;
	$ndrdte = new DateTime($row->dr_date);
    $drdte = $ndrdte->format('m/d/y');

    $nrcvdte = new DateTime($row->rcv_date);
    $rcvdte = $nrcvdte->format('m/d/y');

    $ndtein = new DateTime($row->date_in);
    $dtein = $ndtein->format('m/d/y');
	$rqty = $row->ihd_subtqty;
	$ramt = $row->ihd_subtamt;
	$rcost = $row->ihd_subtcost;
	
	
		if($Y < 226){
			$border = '1';
			
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY(5,$Y); 
			/*if($_recid != $xrecid){*/

				$pdf->Cell(5,5,$box_no,$border,0,'C'); 
				$pdf->Cell(25,5,$drno,$border,0,'L'); 
				$pdf->Cell(25,5,$branch,1,0,'C'); 
				$pdf->Cell(53,5,$supp,1,0,'C'); 
				$pdf->Cell(14,5,$drdte,$border,0,'C'); 
				$pdf->Cell(14,5,$rcvdte,$border,0,'C'); 
				$pdf->Cell(14,5,$dtein,1,0,'C'); 
				$pdf->Cell(17,5,number_format($rqty,2),1,0,'C');
				$pdf->Cell(17,5,number_format($rcost,2),$border,0,'C');  
				$pdf->Cell(17,5,number_format($ramt,2),$border,0,'C'); 
			/*}
			else{
				$pdf->Cell(10,5,'',$border,0,'C'); 
				$pdf->Cell(40,5,'',$border,0,'C'); 
				$pdf->Cell(30,5,$_ART_CODE,1,0,'C'); 
				$pdf->Cell(30,5,$_ART_BARCODE1,1,0,'C'); 
				$pdf->Cell(18,5,$ART_SKU,1,0,'C'); 
				$pdf->Cell(17,5,'',$border,0,'C'); 
				$pdf->Cell(17,5,'',$border,0,'C'); 
				$pdf->Cell(20,5,$_total_pcs,1,0,'C');
				$pdf->Cell(20,5,$_cost,1,0,'C'); 
				$pdf->Cell(30,5,$_total_amt,1,0,'C');  
				$pdf->Cell(35,5,'',$border,0,'C'); 
			}
		*/

			
			
		}

		else{

			$pdf->AddPage();
			$pdf->SetAutoPageBreak(false);

			$Y = 11;

			//ITEMS TH
			$pdf->SetFillColor(239,225,131,1);
			$pdf->SetFont('Arial','B',7);
			$pdf->SetXY(5,$Y); 
			$pdf->Cell(5,4,'#',1,0,'C'); 
			$pdf->Cell(25,4,'DR NO',1,0,'C'); 
			$pdf->Cell(25,4,'BRANCH CODE',1,0,'C'); 
			$pdf->Cell(53,4,'SUPPLIER',1,0,'C'); 
			$pdf->Cell(14,4,'DR DATE',1,0,'C'); 
			$pdf->Cell(14,4,'RCV DATE',1,0,'C'); 
			$pdf->Cell(14,4,'DATE IN',1,0,'C');
			$pdf->Cell(17,4,'QTY',1,0,'C'); 
			$pdf->Cell(17,4,'COST AMT',1,0,'C'); 
			$pdf->Cell(17,4,'SRP AMT',1,0,'C');

			//footer page number
			$pdf->SetY(-12);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of gi FORM: '.$fld_d2brnch,0,0,'C');

			//header page number
			$pdf->SetY(0);
			$pdf->SetX(145);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}'.' of gi FORM: '.$fld_d2brnch,0,0,'C');



			$Y = $Y + 4;

			$pdf->SetFont('Arial','',6);
			$pdf->SetXY(5,$Y); 
			$border = '1';
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY(5,$Y);
			$pdf->Cell(5,5,$box_no,$border,0,'C'); 
			$pdf->Cell(25,5,$drno,$border,0,'L'); 
			$pdf->Cell(25,5,$branch,1,0,'C'); 
			$pdf->Cell(53,5,$supp,1,0,'C'); 
			$pdf->Cell(14,5,$drdte,$border,0,'C'); 
			$pdf->Cell(14,5,$rcvdte,$border,0,'C'); 
			$pdf->Cell(14,5,$dtein,1,0,'C'); 
			$pdf->Cell(17,5,number_format($rqty,2),1,0,'C');
			$pdf->Cell(17,5,number_format($rcost,2),$border,0,'C');  
			$pdf->Cell(17,5,number_format($ramt,2),$border,0,'C'); 
		
			
		//$item_no++;
	}//endfor
	$Y = $Y + 5;
	$box_no++;
	$ntqty = $ntqty + $rqty;
	$ntamt = $ntamt + $ramt;
	$ntcost  = $ntcost + $rcost;
	
	
}//endforeach

if($Y <= 216){
	$pdf->SetFont('Arial','',6);

	$pdf->SetXY(145,$Y);  
	$pdf->Cell(10,5,'TOTAL: ',0,0,'L'); 
	$pdf->SetXY(155,$Y);  
	$pdf->Cell(17,5,number_format($ntqty,2),1,0,'C');
	$pdf->SetXY(172,$Y);  
	$pdf->Cell(17,5,number_format($ntcost,2),1,0,'C'); 
	$pdf->SetXY(189,$Y);  
	$pdf->Cell(17,5,number_format($ntamt,2),1,0,'C');  

	/*$Y = $Y + 5;

	$pdf->SetXY(5,$Y);  
	$pdf->Cell(16,5,'REMARKS: ',0,0,'L'); 
	$pdf->Cell(250,4,'','B',0,'L'); 
*/
	$Y = $Y + 5;
	$pdf->SetXY(5,$Y);  
	$pdf->Cell(203,4,'','',0,'L'); 


	$pdf->SetFont('Arial','',6);

	$Y = $Y + 10;
	$pdf->SetXY(5,$Y);  
	$pdf->Cell(60,4,$cuser_fullname,'',0,'C'); 
	$pdf->SetXY(5,$Y+6);  
	$pdf->Cell(60,5,'PREPARED BY: ','T',0,'C'); 
	
	$pdf->SetXY(76,$Y);  
	$pdf->Cell(60,4,'MARGIE OLIVER','',0,'C'); 
	$pdf->SetXY(76,$Y+6);  
	$pdf->Cell(60,5,'CHECKED BY: ','T',0,'C'); 
	// $pdf->SetX(135); 
	// $pdf->Cell(60,4,'','',0,'L'); 

	$pdf->SetXY(150,$Y);  
	$pdf->Cell(60,4,$rcpt_checker,'',0,'C'); 
	$pdf->SetXY(150,$Y+6);   
	$pdf->Cell(60,5,'NOTED BY: ','T',0,'C'); 

	/*$Y = $Y + 8;
	$pdf->SetXY(5,$Y);  
	$pdf->Cell(16,5,'RECEIVED BY: ',0,0,'L'); 
	$pdf->SetXY(30,$Y);  
	$pdf->Cell(60,4,'','B',0,'L'); 
	$Y = $Y + 4;
	$pdf->SetXY(30,$Y);  
	$pdf->Cell(60,4,'NAME/SIGNATURE/DATE',0,0,'C');*/
	

}
else{

	$pdf->SetFont('Arial','',6);

	$pdf->SetXY(146,$Y);  
	$pdf->Cell(10,5,'TOTAL: ',0,0,'L'); 
	$pdf->SetXY(155,$Y);  
	$pdf->Cell(17,5,number_format($ntqty,2),1,0,'C');
	$pdf->SetXY(172,$Y);  
	$pdf->Cell(17,5,number_format($ntcost,2),1,0,'C'); 
	$pdf->SetXY(189,$Y);  
	$pdf->Cell(17,5,number_format($ntamt,2),1,0,'C');   

	$pdf->AddPage();
	$pdf->SetAutoPageBreak(false);
	$Y = 11;

	/*$Y = $Y + 5;

	$pdf->SetXY(5,$Y);  
	$pdf->Cell(16,155,'REMARKS: ',0,0,'L'); 
	$pdf->Cell(187,4,'','B',0,'L'); 
*/
	$Y = $Y + 5;
	$pdf->SetXY(5,$Y);  
	$pdf->Cell(232,4,'','',0,'L'); 


	$pdf->SetFont('Arial','',6);

	$Y = $Y + 10;
	$pdf->SetXY(5,$Y);  
	$pdf->Cell(60,4,$cuser_fullname,'',0,'C'); 
	$pdf->SetXY(5,$Y+6);  
	$pdf->Cell(60,5,'PREPARED BY: ','T',0,'C'); 
	
	$pdf->SetXY(76,$Y);  
	$pdf->Cell(60,4,'MARGIE OLIVER','',0,'C'); 
	$pdf->SetXY(76,$Y+6);  
	$pdf->Cell(60,5,'CHECKED BY: ','T',0,'C'); 
	// $pdf->SetX(135); 
	// $pdf->Cell(60,4,'','',0,'L'); 

	$pdf->SetXY(150,$Y);  
	$pdf->Cell(60,4,$rcpt_checker,'',0,'C');  
	$pdf->SetXY(150,$Y+6);   
	$pdf->Cell(60,5,'NOTED BY: ','T',0,'C'); 

	/*$Y = $Y + 8;
	$pdf->SetXY(5,$Y);  
	$pdf->Cell(16,5,'NOTED BY: ',0,0,'L'); 
	$pdf->SetXY(30,$Y);  
	$pdf->Cell(60,4,'','B',0,'L'); 
	$Y = $Y + 4;
	$pdf->SetXY(30,$Y);  
	$pdf->Cell(60,4,'NAME/SIGNATURE/DATE',0,0,'C');*/

}


if($cuserlvl!="S"){
$str = "update {$this->db_erp}.`trx_manrecs_hd` aa
		set aa.`p_flag`='Y'
		WHERE (`branch_id` = '$fld_dlbranch_id') {$str_date} AND (aa.`muser` = '$cuser') AND !(aa.`flag` = 'C') AND (aa.`df_tag`='F') "; //AND (aa.`post_tag`='N') AND !(aa.`post_tag`='N') pinatanggal noong 92019
$q3 = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
// $this->mylibz->user_logs_activity_module($this->db_erp,'PRINT_MANRECS_RCV',$fld_d2brnch.'-'.$fld_d2dtfrm,$cuser.'- BRANCH: '.$fld_dlbranch,$str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
}
$pdf->output();

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


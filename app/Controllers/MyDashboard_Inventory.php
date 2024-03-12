<?php
namespace App\Controllers;

class MyDashboard_Inventory extends BaseController
{
	public function __construct()
	{
		$this->request = \Config\Services::request();
		$this->mymdacct = model('App\Models\MyDashboardInvModel');
	}
	
	public function index() {
		$cuser = $this->myusermod->mysys_user();      
		echo view('templates/meheader02');
		echo view('dashboard/inventory/dashb_inv_main');
		echo view('templates/mefooter01');
	} 
	
	public function dashb_inv_recs(){
		$report = $this->request->getVar('report');
		$fld_d2dtfrm    = $this->request->getVar('fld_d2dtfrm'); 
        $fld_d2dtto     = $this->request->getVar('fld_d2dtto'); 
		$date_data = array();
		$date_data['fld_d2dtfrm'] = $fld_d2dtfrm;
		$date_data['fld_d2dtto'] = $fld_d2dtto;

		if ($report == '14') {
			return view('dashboard/inventory/dashb_inv_createdr_main',$date_data);
		}elseif ($report == '15') {
			return view('dashboard/inventory/dashb_inv_intransitdr_main',$date_data);
		}elseif ($report == '16') {
			return view('dashboard/inventory/dashb_inv_cancelleddr_main',$date_data);
		}else{
			$data = $this->mymdacct->dashb_inv_recs(1, 10);
			return view('dashboard/inventory/dashb_inv_recs',$data);
		}

	}

	public function  dashb_inv_recs_vw(){

		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdacct->dashb_inv_recs($mpages, 10, $txtsearchedrec);
		return view('dashboard/inventory/dashb_inv_recs', $data);
	} 

	public function dashb_inv_recs_dl(){

		$this->mymdacct->dashb_inv_recs_dl();
		
	}

	public function dashb_inv_recs_overall_dl(){

		$this->mymdacct->dashb_inv_recs_overall_dl();
		
	}

	public function dashb_inv_getbranch(){ 

        $term = $this->request->getVar('term');

        $autoCompleteResult = array();

		$str = "
		SELECT 
		a.`BRNCH_NAME` __mdata
		FROM 
		mst_companyBranch a
		WHERE  
		a.`BRNCH_NAME` like '%{$term}%'
		ORDER
		by BRNCH_NAME limit 15 
		";

        
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['__mdata']
				));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	}

	public function dashb_inv_getbrancharea(){ 

        $term = $this->request->getVar('term');

        $autoCompleteResult = array();

		$str = "
		SELECT 
		a.`BRNCH_GROUP` __mdata
		FROM 
		mst_companyBranch a
		WHERE
		!(a.`BRNCH_GROUP` = '') AND a.`BRNCH_GROUP` like '%{$term}%'
		GROUP BY a.`BRNCH_GROUP`
		ORDER
		by BRNCH_GROUP ASC
		";

        
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['__mdata']
				));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	} 

	public function dashb_inv_process(){ 
		$this->mymdacct->dashb_inv_process();	
	} 

	public function dashb_inv_validate(){

		$this->mymdacct->dashb_inv_validate();

	}

	public function dashb_inv_validate_dl(){

		$this->mymdacct->dashb_inv_validate_dl();

	}

	public function dashb_inv_forcountered(){

		$this->mymdacct->dashb_inv_forcountered();

	}

	public function dashb_inv_getbranchuser(){ 

        $term = $this->request->getVar('term');

        $autoCompleteResult = array();

		$str = "
		SELECT 
			`myusername` __mdata
		FROM 
			myusers 
		WHERE 
			`myuserrema` = 'B'
		AND
			`myuserrema` like '%{$term}%'
		ORDER
		by 
			`myuserrema` limit 15 
		";

        
		$q =  $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);// AND {$str_comp} 
		if($q->getNumRows() > 0) { 
			$rrec = $q->getResultArray();
			foreach($rrec as $row):
				array_push($autoCompleteResult,array("value" => $row['__mdata']
				));
				
			endforeach;
		}
		$q->freeResult();
		echo json_encode($autoCompleteResult);
		
	}

	public function dashb_inv_created_recs(){
		$data = $this->mymdacct->dashb_inv_created_recs(1, 10);
		return view('dashboard/inventory/dashb_inv_createdr_recs',$data);
	}

	public function dashb_inv_created_recs_vw(){

		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdacct->dashb_inv_created_recs($mpages, 10, $txtsearchedrec);
		return view('dashboard/inventory/dashb_inv_createdr_recs',$data);
	} 

	public function dashb_inv_intransit_recs(){
		$data = $this->mymdacct->dashb_inv_intransit_recs(1, 10);
		return view('dashboard/inventory/dashb_inv_intransitdr_recs',$data);
	}

	public function dashb_inv_intransit_recs_vw(){

		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdacct->dashb_inv_intransit_recs($mpages, 10, $txtsearchedrec);
		return view('dashboard/inventory/dashb_inv_intransitdr_recs',$data);
	}
	
	public function dashb_inv_cancelled_recs(){
		$data = $this->mymdacct->dashb_inv_cancelled_recs(1, 10);
		return view('dashboard/inventory/dashb_inv_cancelleddr_recs',$data);
	}

	public function dashb_inv_cancelled_recs_vw(){

		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
        $mpages = $this->request->getVar('mpages');
        $mpages = (empty($mpages) ? 0 : $mpages);
        $data = $this->mymdacct->dashb_inv_cancelled_recs($mpages, 10, $txtsearchedrec);
		return view('dashboard/inventory/dashb_inv_cancelleddr_recs',$data);
	} 


	public function dashb_inv_created_dl(){

		$this->mymdacct->dashb_inv_created_dl();

	}

	public function dashb_inv_created_cancel(){

		$this->mymdacct->dashb_inv_created_cancel();

	}

	public function dashb_inv_intransit_dl(){

		$this->mymdacct->dashb_inv_intransit_dl();

	}

	public function dashb_inv_daily_dl(){

		$this->response->setHeader('Content-Type', 'application/pdf');
		return view('dashboard/inventory/dashb_inv_rcv_print_pdf');

	}

	public function dashb_inv_intransit_rcv(){
		$this->mymdacct->dashb_inv_intransit_rcv();
	}

	public function dashb_inv_intransit_cwo_upld(){
		$this->mymdacct->dashb_inv_intransit_cwo_upld();
	}

	public function dashb_inv_intransit_mn_upld(){
		$this->mymdacct->dashb_inv_intransit_mn_upld();
	}

	public function dashb_inv_intransit_tap_upld(){
		$this->mymdacct->dashb_inv_intransit_tap_upld();
	}

	public function dashb_inv_intransit_old_upld(){
		$this->mymdacct->dashb_inv_intransit_old_upld();
	}
}
	

		    
	

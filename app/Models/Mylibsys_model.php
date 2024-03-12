<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mylibsys_model extends CI_Model { 
	public function __construct()
	{
		parent::__construct();
		
	}
	
	
	
	function number_format($number, $dec_point='.', $thousands_sep=',')
		{

		if(!empty($number)){
			if($dec_point==$thousands_sep){
				trigger_error('2 parameters for ' . __METHOD__ . '() have the same value, that is "' . $dec_point . '" for $dec_point and $thousands_sep', E_USER_WARNING);
		}
		if(preg_match('{\.\d+}', $number, $matches)===1){
			$decimals = strlen($matches[0]) - 1;
		}
		else{
		$decimals = 0;
		}
		return number_format($number, $decimals, $dec_point, $thousands_sep);
		}
		}


	public function mypopulist($myaray,$ccdata,$objname,$onaction='',$moutput='TO_ECHO') {
		
		$obj = '<select class ="form-control form-control-sm" id="' . $objname . '"  ' . $onaction . '>';
//form-control-sm col-lg-3
		$ii=0; 
		$nflag=0;
		
		while($ii < count($myaray)) {
		
			$ddata = explode("xOx",$myaray[$ii]);
			$ment = rtrim($ddata[0]);
			if(strlen($ment) > 0) {
				if($ddata[0] == $ccdata) { 
					$mselected = 'selected="selected" style="font-weight: bold"';
					$nflag = 1;
					
				}
				else {
					$mselected = '';
				}
				$obj .= ' <option ' . $mselected . ' value="' . $ddata[0] . '">' . $ddata[1] . '</option>' . "\n";	
			} 
			$ii++;
		}
		if($nflag == 0) {
			if(!empty($ccdata)) {
				$obj .= ' <option selected="selected" value="' . $ccdata . '"></option>' . "\n";
			} 
		}
		if(empty($ccdata)) {
			$obj .= ' <option selected="selected" value=""></option>' . "\n";
		}
		
		$obj .=	'</select>';
		if($moutput == 'TO_ECHO') { 
			echo $obj;
		} else { 
			return $obj;
		}
	}
	public function mypopulist_2($myaray,$ccdata,$objname,$onaction='',$moutput='TO_ECHO') {
		
		$obj = '<select style= "color: #000000 !important;max-width:100%;" name="' . $objname . '" id="' . $objname . '"  ' . $onaction . '>';

		$ii=0; 
		$nflag=0;
		
		while($ii < count($myaray)) {
		
			$ddata = explode("xOx",$myaray[$ii]);
			$ment = rtrim($ddata[0]);
			if(strlen($ment) > 0) {
				if($ddata[0] == $ccdata) { 
					$mselected = 'selected="selected" style= "color: #000000 !important;"';
					$nflag = 1;
					
				}
				else {
					$mselected = '';
				}
				$obj .= ' <option style= "color: #000000 !important;" ' . $mselected . ' value="' . $ddata[0] . '">' . $ddata[1] . '</option>' . "\n";	
			} 
			$ii++;
		}
		if($nflag == 0) {
			if(!empty($ccdata)) {
				$obj .= ' <option style= "color: #000000 !important;" selected="selected" value="' . $ccdata . '"></option>' . "\n";
			} 
		}
		if(empty($ccdata)) {
			$obj .= ' <option style= "color: #000000 !important;" selected="selected" value=""></option>' . "\n";
		}
		
		$obj .=	'</select>';
		if($moutput == 'TO_ECHO') { 
			echo $obj;
		} else { 
			return $obj;
		}
	}
	public function my_select_list($myaray,$ccdata,$objname,$onaction='',$moutput='TO_ECHO') {
	
		$obj = '<select id="' . $objname . '" ' . $onaction . '>';
		$ii=0; 
		$nflag=0;
		
		while($ii < count($myaray)) {
		
			$ddata = explode("xOx",$myaray[$ii]);
			$ment = rtrim($ddata[0]);
			if(strlen($ment) > 0) {
				if($ddata[0] == $ccdata) { 
					$mselected = 'selected="selected" style="font-weight: bold"';
					$nflag = 1;
					
				}
				else {
					$mselected = '';
				}
				$obj .= ' <option ' . $mselected . ' value="' . $ddata[0] . '">' . $ddata[1] . '</option>' . "\n";	
			} 
			$ii++;
		}
		if($nflag ==0 ) {
			if(!empty($ccdata)) {
				$obj .= ' <option selected="selected" value="' . $ccdata . '"> </option>' . "\n";
			} 
		}
		if(empty($ccdata)) {
			$obj .= ' <option selected="selected" value=""></option>' . "\n";
		}
		
		$obj .=	'</select>';
		if($moutput == 'TO_ECHO') { 
			echo $obj;
		} else { 
			return $obj;
		}
	}	
	public function my_select_list_whse($myaray,$ccdata,$objname,$onaction='',$moutput='TO_ECHO') {
	
		$obj = '<select id="' . $objname . '" ' . $onaction . '>';
		$ii=0; 
		$nflag=1;
		
		while($ii < count($myaray)) {
		
			$ddata = explode("xOx",$myaray[$ii]);
			$ment = rtrim($ddata[0]);
			if(strlen($ment) > 0) {
				if($ddata[0] == $ccdata) { 
					$mselected = 'selected="selected" style="font-weight: bold"';
					$nflag = 1;
					
				}
				else {
					$mselected = '';
				}
				$obj .= ' <option ' . $mselected . ' value="' . $ddata[0] . '">' . $ddata[1] . '</option>' . "\n";	
			} 
			$ii++;
		}
		if($nflag ==0 ) {
			if(!empty($ccdata)) {
				$obj .= ' <option selected="selected" value="' . $ccdata . '"> </option>' . "\n";
			} 
		}
		if(empty($ccdata)) {
			$obj .= ' <option selected="selected" value=""></option>' . "\n";
		}
		
		$obj .=	'</select>';
		if($moutput == 'TO_ECHO') { 
			echo $obj;
		} else { 
			return $obj;
		}
	}	

	public function random_string($length) { 
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $key;
	}	
	
	public function oa_nospchar($cdatame='') {		
		$cddata = str_replace(',','',$cdatame);
		$cddata = str_replace('-','',$cddata);
		$cddata = str_replace('[','',$cddata);
		$cddata = str_replace(']','',$cddata);
		$cddata = str_replace('{','',$cddata);
		$cddata = str_replace('}','',$cddata);
		$cddata = str_replace('(','',$cddata);
		$cddata = str_replace(')','',$cddata);
		$cddata = str_replace('|','',$cddata);
		$cddata = str_replace(';','',$cddata);
		$cddata = str_replace(':','',$cddata);
		$cddata = str_replace('%','',$cddata);
		$cddata = str_replace('@','',$cddata);
		$cddata = str_replace("'",'',$cddata);
		$cddata = str_replace('"','',$cddata);
		$cddata = str_replace('^','',$cddata);
		$cddata = str_replace('&','',$cddata);
		return $cddata;
	}
	
	public function oa_no_commas($xval) { 
		$xval = str_replace(',','',trim($xval));
		return $xval;
	}
	
	public function mydate_yyyymmdd($angdate='') {
		//1234567890
		//08-08-2008
		return substr($angdate,6,4). '-' . substr($angdate,0,2) . '-' .substr($angdate,3,2);
	}
	
	
	public function mydate_mmddyyyy($angdate='') {
		//1234567890
		//2008-08-01
		if(!empty($angdate)){
		return substr($angdate,5,2). '/' . substr($angdate,8,2) . '/' .substr($angdate,0,4);
	    }
	}
	
	public function mydatetime() { 
		$str = "select date_format(now(),'%m/%d/%Y %H:%i:%s') __mdatetime";
		$q = $this->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$q->free_result();
		return $rw['__mdatetime'];
	}
	public function mydatetimedb() { 
		$str = "select date_format(now(),'%Y-%m-%d %H:%i:%s') __mdatetime";
		$q = $this->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$q->free_result();
		return $rw['__mdatetime'];
	}
	
	
	public function myoa_sql_exec($str,$cmisc='') { 
		$qry = $this->dbx->query($str);
		$cuser = $this->session->userdata('xsysid_user');
		$error = $this->dbx->error();
		if(!$qry) { 
			//$cerr = $this->dbx->display_error();
			//_error_message();
			
			//$strq =  chr(13) . chr(10) . $this->dbx->_error_message() . chr(13) . chr(10) . 
			//'error code: ' . $this->dbx->_error_number() . chr(13) . chr(10) . 
			//dirname(__FILE__) . chr(13) . chr(10) . 
			//$cmisc . chr(13) . chr(10) . 
			//$str;
			
			$strq =  chr(13) . chr(10) . $error['message'] . chr(13) . chr(10) . 
			'error code: ' . $error['code'] . chr(13) . chr(10) . 
			dirname(__FILE__) . chr(13) . chr(10) . 
			$cmisc . chr(13) . chr(10) . 
			$str;
			
			$strq = $this->dbx->escape_str($strq);	
			$str = "insert into {$this->db_client}.`syslogs` (LOG_USER,LOG_SQLEXEC,LOG_MODULE,LOG_DATE,LOG_IPADDR) 
			values('$cuser','$strq','SYSINS',now(),'" . self::get_ip_address() . "')";
			$q = $this->dbx->query($str);
			if (!$q) {
				$cerr =  "<div style=\"display: block;
					padding: 3px;
					width: auto;
					height: 20px;
					font: bold 14px Tahoma;
					text-decoration: blink;
					background-color: red;
					color: white;
					border-radius: 5px;
					-moz-border-radius:0.4em;
					-khtml-border-radius:0.4em;
					-webkit-border-radius:0.4em;
					-ms-border-radius:0.4em;
					-o-border-radius:0.4em;
					border: 1px solid #2D657E;
					-moz-box-shadow: 5px 5px 5px grey;
					-webkit-box-shadow: 5px 5px 5px grey;
					-ms-box-shadow: 5px 5px 5px grey;
					box-shadow: 5px 5px 5px grey; 					
					\">
						{$str}
					</div>";
				echo $cerr;
				die();
			} else {
				echo "
				<div class=\"alert alert-danger\"><strong>Error.</strong> Pls see System Logs...</div>
				";
				die();
			}
		}		
		return $qry;
	}
	
	public function  get_ip_address() { 
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						return $ip;
					}
				}
			}
		}
	}  
	//end get_ip_address
	
	public function logs_modi_audit($adatums=array(),$dbname,$dbtbl,$cmodule,$ckeydata,$optn,$crecmark='',$cdblogs='') {
		$cdbm = $dbname;
		$dbmlogs = (!empty($cdblogs) ? $cdblogs : $cdbm);
		$cuser = $this->session->userdata('xsysid_user');
		$cipaddr = $_SERVER['REMOTE_ADDR'];
		$colddata = "";
		
		for($ii = 1;$ii < count($adatums); $ii++) {
			$mdata = explode("xOx",$adatums[$ii]);
			$cfld = $mdata[0];
			$cdata = $mdata[1];
			
			$cstr = "SELECT {$cfld} from {$cdbm}.{$dbtbl} where {$optn} limit 1"; 
			$mmqry = $this->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($mmqry->num_rows() == 0) {
				$cchanges = "A";
				$colddata = "";
			} else {
				$cchanges = "U";
				$rsmm = $mmqry->row_array();
				$colddata = $this->dbx->escape_str($rsmm[$cfld]);
			}
			$mmqry->free_result();
			
			//record for deletion
			if($crecmark == 'DEL_REC') { 
				$cchanges = "D";
			}
			
			
			$cstr = "SELECT {$cfld} from {$cdbm}.{$dbtbl} where {$optn} and {$cfld} = {$cdata}";
			$strq = $this->dbx->escape_str($cstr);	
			$mmqry2 = $this->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($mmqry2->num_rows() == 0) { 
				$cstr = "insert into {$dbmlogs}.`auditlogs` ( 
				LOG_USER,LOG_MODULE,LOG_DATE,LOG_TIME,LOG_TBL,LOG_KEYREC,LOG_ENUMB,LOG_FIELD,LOG_OLDVAL,LOG_NEWVAL,
				LOG_CHANGE,LOG_IPADDR,LOG_SQLEXEC) 
				 values ('$cuser','$cmodule',now(),current_time(),'$dbtbl','$ckeydata','$ckeydata','$cfld','$colddata',$cdata,
				 '$cchanges','$cipaddr','{$strq}')";
				$this->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$mmqry2->free_result();
			if($crecmark == 'DEL_REC') { 
				$cstr = "insert into {$dbmlogs}.`auditlogs` ( 
				LOG_USER,LOG_MODULE,LOG_DATE,LOG_TIME,LOG_TBL,LOG_KEYREC,LOG_ENUMB,LOG_FIELD,LOG_OLDVAL,LOG_NEWVAL,
				LOG_CHANGE,LOG_IPADDR,LOG_SQLEXEC) 
				 values ('$cuser','$cmodule',now(),current_time(),'$dbtbl','$ckeydata','$ckeydata','$cfld','$colddata',$cdata,
				 '$cchanges','$cipaddr','{$strq}')";
				$this->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		}
	}  //ilogs_modi_audit
	
	//audit logs for alphalist tagging
	public function logs_modify_audit($adatums=array(),$dbname,$dbtbl,$cmodule,$ckeydata,$optn,$crecmark='',$cdblogs='') {
		$cdbm = $dbname;
		$dbmlogs = (!empty($cdblogs) ? $cdblogs : $cdbm);
		$cuser = $this->session->userdata('xsysid_user');
		$cipaddr = $_SERVER['REMOTE_ADDR'];
		$colddata = "";
		
		for($ii = 1;$ii < count($adatums); $ii++) {
			$mdata = explode("xOx",$adatums[$ii]);
			$cfld = $mdata[0];
			$cdata = $mdata[1];
			
			$cstr = "SELECT {$cfld} from {$cdbm}.{$dbtbl} where {$optn} limit 1"; 
			$mmqry = $this->mylibz->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($mmqry->num_rows() == 0) {
				$cchanges = "A";
				$colddata = "";
			} else {
				$cchanges = "U";
				$rsmm = $mmqry->row_array();
				$colddata = $this->dbx->escape_str($rsmm[$cfld]);
			}
			$mmqry->free_result();
			
			if($crecmark == 'DEL_REC') { 
				$cchanges = "D";
			}
			
			$cstr = "SELECT {$cfld} from {$cdbm}.{$dbtbl} where {$optn} and {$cfld} = {$cdata}";
			$strq = $this->dbx->escape_str($cstr);	
			$mmqry2 = $this->mylibz->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			if($mmqry2->num_rows() == 0) { 
				$cstr = "insert into {$this->db_pms}.`auditlogs` ( 
				LOG_USER,LOG_MODULE,LOG_DATE,LOG_TIME,LOG_TBL,LOG_KEYREC,LOG_ENUMB,LOG_FIELD,LOG_OLDVAL,LOG_NEWVAL,
				LOG_CHANGE,LOG_IPADDR,LOG_SQLEXEC) 
				 values ('$cuser','$cmodule',now(),current_time(),'$dbtbl','$ckeydata','$ckeydata','$cfld','$colddata',$cdata,
				 '$cchanges','$cipaddr','{$strq}')";
				$this->mylibz->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
			$mmqry2->free_result();
			if($crecmark == 'DEL_REC') { 
				$cstr = "insert into {$this->db_pms}.`auditlogs` ( 
				LOG_USER,LOG_MODULE,LOG_DATE,LOG_TIME,LOG_TBL,LOG_KEYREC,LOG_ENUMB,LOG_FIELD,LOG_OLDVAL,LOG_NEWVAL,
				LOG_CHANGE,LOG_IPADDR,LOG_SQLEXEC) 
				 values ('$cuser','$cmodule',now(),current_time(),'$dbtbl','$ckeydata','$ckeydata','$cfld','$colddata',$cdata,
				 '$cchanges','$cipaddr','{$strq}')";
				$this->mylibz->myoa_sql_exec($cstr,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			}
		}
	}  
	public function user_logs_activity_init($dbname,$cmoduletag='',$crecfld='',$cremk='') { 
		$cuser = $this->session->userdata('xsysid_user');
		$str = "select current_date() __xcurdate,current_time() __xcurtime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdate = $rw['__xcurdate'];
		$xcurtime = $rw['__xcurtime'];
		$q->free_result();
		$xstr = $this->dbx->escape_str($cremk);
		$cmoduletag = $this->dbx->escape_str($cmoduletag);
		$str = "
		insert into {$dbname}.auditlogs (
		LOG_USER,
		LOG_MODULE,
		LOG_DATE,
		LOG_TIME,
		LOG_ENUMB,
		LOG_FIELD,
		LOG_OLDVAL,
		LOG_NEWVAL,
		LOG_CHANGE,
		LOG_IPADDR,
		LOG_SQLEXEC 
		) 
		values('$cuser','$cmoduletag','{$xcurdate}','{$xcurtime}','','$crecfld','','','','" . $this->mylibz->get_ip_address() . "','{$xstr}') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($cmoduletag,$xcurdate,$xcurtime);
	}
	
	public function user_logs_activity_update($dbname,$cmoduletag,$xcurdate,$xcurtime) { 
		$cuser = $this->session->userdata('xsysid_user');
		$str = "
		update {$dbname}.auditlogs set LOG_NEWVAL = current_time() 
		where LOG_USER = '$cuser' and LOG_MODULE = '$cmoduletag' and date(LOG_DATE) = date('$xcurdate') and LOG_TIME = '$xcurtime'
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
	} 
	
	//ualam
	public function user_logs_activity_module($dbname,$cmoduletag='',$cka,$ckmb,$cremk1='',$cremk2='') { 
		$cuser = $this->mysys_user();
		$xstr1 = $this->dbx->escape_str($cremk1);
		$xstr2 = $this->dbx->escape_str($cremk2);
		$cmoduletag = $this->dbx->escape_str($cmoduletag);
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();
		
		$cka = substr($cka,0,60);
		$ckmb = substr($ckmb,0,60);
		
		$str = "
		insert into {$dbname}.ualam (
		`LOG_USER`,`LOG_MODULE`,
		`LOG_KA_KEYREC`,`LOG_MB_KEYREC`,`LOG_REMK1`,
		`LOG_REMK2`,`LOG_IPADDR`
		) 
		values('$cuser','{$cmoduletag}','$cka','$ckmb','$xstr1','$xstr2','" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($cmoduletag,$xcurdatetime);
	}
	//itemcode override in receiving acct
	public function user_logs_override_itemcode($dbname,$cmoduletag='',$cka,$ckmb,$cremk1='',$cremk2='') { 
		$cuser = $this->mysys_user();
		$xstr1 = $this->dbx->escape_str($cremk1);
		$xstr2 = $this->dbx->escape_str($cremk2);
		$cmoduletag = $this->dbx->escape_str($cmoduletag);
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();
		
		$cka = substr($cka,0,60);
		$ckmb = substr($ckmb,0,60);
		
		$str = "
		insert into {$dbname}.trx_manrecs_override (
		`LOG_USER`,`LOG_MODULE`,
		`LOG_KA_KEYREC`,`LOG_MB_KEYREC`,`LOG_REMK1`,
		`LOG_REMK2`,`LOG_IPADDR`
		) 
		values('$cuser','{$cmoduletag}','$cka','$ckmb','$xstr1','$xstr2','" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($cmoduletag,$xcurdatetime);
	}
	//article logs
	public function update_logs_article_module($dbname,$CL_ITEMCODE='',$CL_LOGFIELD='',$CL_OLD='',$CL_NEW='',$CL_TAG='R') { 
		$cuser = $this->mysys_user();
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();

		$str = "
		insert into {$dbname}.mst_article_ulogs (
		`CL_ITEMCODE`,
		`CL_LOGFIELD`,
		`CL_OLD`,
		`CL_NEW`,
		`CL_ISUPDTED`,
		`CL_MUSER`,
		`CL_TAG`
		) 
		values
		('$CL_ITEMCODE',
		'{$CL_LOGFIELD}',
		'$CL_OLD',
		'$CL_NEW',
		'0',
		'$cuser',
		'$CL_TAG') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($CL_LOGFIELD,$xcurdatetime);
	}
	public function print_logs_manrecs_dr_module($dbname,$dr_no='',$branch_id='',$cusername='') { 
		$cuser = $this->mysys_user();
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();

		$str = "
		INSERT INTO {$dbname}.`trx_manrecs_dr_print_logs`
            (`drtrx_no`,
             `branch_id`,
             `muser`,
             `print_time`,
             `print_by`,
             `ipaddr`)
		VALUES
		('$dr_no',
		'$branch_id',
		'$cuser',
		 now(),
		'$cusername',
		'" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($dr_no,$xcurdatetime);
	}
	public function print_logs_manrecs_gr_module($dbname,$dr_no='',$branch_id='',$cusername='') { 
		$cuser = $this->mysys_user();
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();

		$str = "
		INSERT INTO {$dbname}.`trx_wshe_gr_print_logs`
            (`grtrx_no`,
             `branch_id`,
             `muser`,
             `print_time`,
             `print_by`,
             `ipaddr`)
		VALUES
		('$dr_no',
		'$branch_id',
		'$cuser',
		 now(),
		'$cusername',
		'" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($dr_no,$xcurdatetime);
	}
	public function mysys_user() { 
		return $this->session->userdata('__xsys_myiduserclient__');
	}
	
	public function mysys_user_fullname() { 
		return $this->session->userdata('__xsys_myiduserfullname__');
	}

	public function mysys_usergrp() { 
		return $this->session->userdata('__xsys_myidusergroup__');
	}
	public function mysys_userlvl() { 
		return $this->session->userdata('__xsys_myiduserlevel__');
	}
	
	public function mysys_userdept() { 
		return $this->session->userdata('__xsys_myiduserdept__');
	}
	public function mysys_userrema() { 
		return $this->session->userdata('__xsys_myiduserrema__');
	}

	
	public function msys_pw_salt() { 
		return "mysyzaibmy";
	} 
	
	public function msys_is_logged() { 
		return $this->session->userdata('__msysuserclient_is_logged__');
	}
	
	public function mpw_tkn() { 
		return self::mysys_user() . self::msys_pw_salt();
	}
		
		
	public function oa_intonly($nval='0',$nhaba='0') {
		$nval = ((int) $nval);
		return str_pad(trim($nval,' '),$nhaba,'0',STR_PAD_LEFT);
	}
	public function oa_nodot($nval='0',$nhaba='0') {
		$nval = number_format($nval+0,2,'.','');
		$nval = str_replace('.','',$nval);
		return str_pad(trim($nval,' '),$nhaba,'0',STR_PAD_LEFT);
	}
	public function oa_dmenum($nval=0,$nhaba=0) {
		return sprintf('%.2f',$nval);
	}
	
	function ipakitamali($angmali='') {
		$pakitamo = '<script language="javascript" type="text/javascript">';
		$pakitamo .= 'alert("' .  $angmali . '")';
		$pakitamo .= '</script>';
		echo $pakitamo;
	}


	public function oa_dbtime($sdt='') {
		$mytime = array();
		$str_cmd = "select current_date() as mydate,current_time() as mytime";
		$qry = $this->mylibz->myoa_sql_exec($str_cmd,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rs = $qry->row_array();
		$mytime[] = $rs['mydate'];
		$mytime[] = $rs['mytime'];
		if($sdt == 'date')
			return $mytime[0];
		else
			return $mytime[1];
	} 
	
	function oa_fmenum($nval=0,$nhaba=0) {
		//return str_pad(sprintf('%.2f',$nval),$nhaba,' ',STR_PAD_LEFT);
		return str_pad(number_format($nval+0,2,'.',','),$nhaba,' ',STR_PAD_LEFT);
	}
	
	
	public function mypagination($npage_curr,$npage_count,$cjavafunc='__myredirected_search',$moutput='') {  
		$chtml = "
    <div class=\"row\" >
    <ul class=\"pagination\">		
		";
		/******  build the pagination links ******/
		// if not on page 1, don't show back links
		if ($npage_curr > 1) {
			// show << link to go back to page 1
			$chtml .= " 
			<li class=\"page-item pull-left previous\">
			<a class=\"page-link\" href=\"javascript:{$cjavafunc}('1');\";>
			<<
			</a> 
			</li>
			";
			// get previous page num
			$prevpage = $npage_curr - 1;
			// show < link to go back to 1 page
			$chtml .= "
			<li class=\"page-item pull-left\">
			 <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $prevpage . "');\"><</a> 
			</li>
			";
		} // end if

		# range of num links to show
		$range = 3;

		# loop to show links to range of pages around current page
		for ($x = ($npage_curr - $range); $x < (($npage_curr + $range)  + 1); $x++) {
			// if it's a valid page number...
			if (($x > 0) && ($x <= $npage_count)) {
			// if we're on current page...
				if ($x == $npage_curr) {
				// 'highlight' it but don't make a link
					$chtml .= " 
				   	<li class=\"page-item pull-left\">
					 <a class=\"page-link\" href=\"javascript:void(0);\">
					 	<b>" . number_format($x,0,'',',') . "</b>
					 </a> 
					</li>
					";
			// if not current page...
			}
			else {
				// make it a link
				$chtml .= " 
				<li class=\"page-item pull-left\">
				 <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $x . "');\">" . number_format($x,0,'',',') . "</a> 
				</li>
				";
				} // end else
			} // end if 
		} // end for


		// if not on last page, show forward and last page links        
		if ($npage_curr != $npage_count) {
			// get next page
			$nextpage = $npage_curr + 1;
			// echo forward link for next page 
			$chtml .= " 
			<li class=\"page-item pull-left\">
			 <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $nextpage . "');\">></a> 
			</li>
			";
			// echo forward link for lastpage
			$chtml .= " 
			<li class=\"page-item pull-left\">
			 <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $npage_count . "');\">>></a> 
			</li>
			";
		 } // end if
		 # end build pagination links 
		$chtml .= "
			</ul>
		</div>
		";
		if($moutput == 'TO_ECHO') { 
			echo $chtml;
		} else { 
			return $chtml;
		}		
	}  //end mypagination
		
	
	//format on mm/dd/yyyy
	public function __check_date($mdate) { 
		$madate = explode("/",$mdate);
		if(count($madate) > 2) {
			if(checkdate($madate[0] + 0,$madate[1] + 0,$madate[2] + 0)) { 
				return true;
			} else { 
				return false;
			}
		} else { 
			return false;
		}
	}
	//end __check_date
	public function clean($string) {
	   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
	   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

	   return preg_replace('/-+/', '', $string); // Replaces multiple hyphens with single one.
	}//end remove special char
	
	public function getPercentage($count,$total){
		$mypercent = 0;
		if($count > 0 && $total > 0 || !empty($count) && !empty($total)){
		$mypercent = ceil(($count/$total)*100);
		
		}
		return $mypercent;
	
	}
	public function substrBranch($branch){
		$strpos = strpos($branch,'-');

		if($strpos){
			$mbranch = trim(substr($branch,0,$strpos));
		}
		else{
			$mbranch = $branch;
		}

		return $mbranch;
	}
		public function get_user_plant($dbname,$user){
		$usr_plnt = '';
		$str = "SELECT bb.`recid` FROM {$dbname}.`myua_plant` aa
				JOIN {$dbname}.`mst_plant`bb
				ON  aa.`myuaplant` =  bb.`plnt_code` 
				WHERE `myusername` = '$user' AND aa.`ISACTIVE` = 'Y'";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__. chr(13) . chr(10) . 'User: ' . $user);
		if($q->num_rows() > 0):
			foreach ($q->result_array() as $val) {
				$usr_plnt .= '-'. $val['recid'];
			}
			// $len = strlen($usr_plnt);
			// $usr_plnt =substr($usr_plnt,0,$len-1);
		endif;
		return $usr_plnt;
	}
		public function print_logs_rfpcfmodule($dbname,$trx_no='',$branch_id='',$cusername='',$type = '') { 
		$cuser = $this->mysys_user();
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();

		$str = "
		INSERT INTO {$dbname}.`trx_ap_trns_print_logs`
            (`trx_no`,
             `branch_id`,
             `muser`,
             `print_time`,
             `print_by`,
             `trx_type`,
             `ipaddr`)
		VALUES
		('$trx_no',
		'$branch_id',
		'$cuser',
		 now(),
		'$cusername',
		'$type',
		'" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($trx_no,$xcurdatetime);
	}
	public function mysys_usernewui() { 
		return $this->session->userdata('__xsys_myidusernewui__');
	}
	public function upd_logs_pullout_gr($dbname,$cmoduletag='',$pullouttrx='',$grtrx='',$ptyp='',$field_upd='',$sstr='') { 
		$cuser = $this->mysys_user();
		$pullouttrx = $this->dbx->escape_str($pullouttrx);
		$grtrx = $this->dbx->escape_str($grtrx);
		$cmoduletag = $this->dbx->escape_str($cmoduletag);
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();
		$sstr = $this->dbx->escape_str($sstr);
		//$cka = substr($cka,0,60);
		//$ckmb = substr($ckmb,0,60);
		
		$str = "
		insert into {$dbname}.trx_pullout_ulogs (
		`LOG_USER`,
		`LOG_MODULE`,
		`LOG_PULLOUTTRX`,
		`LOG_GRTRX`,
		`LOG_PTYPE`,
		`LOG_FLD_UPD`,
		`LOG_KA_REMK`,
		`LOG_IPADDR`
		) 
		values(
		'$cuser',
		'{$cmoduletag}',
		'$pullouttrx',
		'$grtrx',
		'$ptyp',
		'$field_upd',
		'$sstr',
		'" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($cmoduletag,$xcurdatetime);
	}
	public function upd_logs_tpd_pullout_gr($dbname,$cmoduletag='',$pullouttrx='',$grtrx='',$ptyp='',$sstr='') { 
		$cuser = $this->mysys_user();
		$pullouttrx = $this->dbx->escape_str($pullouttrx);
		$grtrx = $this->dbx->escape_str($grtrx);
		$cmoduletag = $this->dbx->escape_str($cmoduletag);
		$str = "select now() __xcurdatetime";
		$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		$rw = $q->row_array();
		$xcurdatetime = $rw['__xcurdatetime'];
		$q->free_result();
		$sstr = $this->dbx->escape_str($sstr);
		//$cka = substr($cka,0,60);
		//$ckmb = substr($ckmb,0,60);
		
		$str = "
		insert into {$dbname}.trx_tpd_ulogs (
		`LOG_USER`,
		`LOG_MODULE`,
		`LOG_PULLOUTTRX`,
		`LOG_GRTRX`,
		`LOG_PTYPE`,
		`LOG_KA_REMK`,
		`LOG_IPADDR`
		) 
		values(
		'$cuser',
		'{$cmoduletag}',
		'$pullouttrx',
		'$grtrx',
		'$ptyp',
		'$sstr',
		'" . $this->mylibz->get_ip_address() . "') 
		";
		$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		
		return array($cmoduletag,$xcurdatetime);
	}

		public function logs_trx_rcv_audit($dbname,$arrfield='',$cuser='',$trxno='',$tag='',$desc='',$module='') { 
			if(empty($cuser)):
			$cuser = $this->mysys_user();
			endif;
			
			$str = "select now() __xcurdatetime";
			$q = $this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			$rw = $q->row_array();
			$xcurdatetime = $rw['__xcurdatetime'];
			$q->free_result();

			$str = "
			INSERT INTO {$dbname}.`trx_manrecs_rcv_ulogs`
	            (`trx_no`,
	             `data_updated`,
	             `u_tag`,
	             `u_module`,
	             `u_desc`,
	             `muser`
			) 
			values
			('$trxno',
			'{$arrfield}',
			'$tag',
			'$module',
			'$desc',
			'$cuser') 
			";
			$this->mylibz->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
			
			return array($trxno,$xcurdatetime);
		}

}

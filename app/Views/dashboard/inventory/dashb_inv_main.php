<?php
$mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$cuserrema = $this->myusermod->mysys_userrema();
$cuser   = $this->myusermod->mysys_user();
$claims_revised_date = '2022-11-01';
$claims_revised_date2 = '2022-12-21';
$fld_d2brnch    = $request->getVar('fld_d2brnch');
$fld_d2dtfrm    = $request->getVar('fld_d2dtfrm'); 
$fld_d2dtto     = $request->getVar('fld_d2dtto'); 

$BRNCH_NAME="";
// get companybranch
if( $cuserrema == 'B'):
    $ua_branch = $this->myusermod->ua_brnch('ap2',$cuser);

    $str = "
        select cc.`BRNCH_NAME`, bb.`COMP_NAME`
        from `mst_company`bb
        join `mst_companyBranch` cc
        on (bb.`recid` = cc.`COMP_ID`)
        where cc.`recid` = '{$ua_branch[0]}'
    ";
    $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
    $rw = $q->getRowArray();
    $BRNCH_NAME = $rw['BRNCH_NAME'];
endif;

// if(!empty($fld_d2brnch)):
//     $str="
//     SELECT `recid`,`BRNCH_NAME` FROM mst_companyBranch WHERE `BRNCH_NAME` = '$fld_d2brnch';
//     ";
//     $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
//     $rw = $q->getRowArray();
//     $brnch_rid = $rw['recid'];
//     $str_branch = " AND `brnch` = '$fld_d2brnch'";
// else:
//     $brnch_rid = "";
//     $str_branch = "";
// endif;

// if (!empty($fld_d2dtfrm) && !empty($fld_d2dtto)) {
//     //TOTAL CREATED DR
//     $str="
//     SELECT SUM(TOTAL) AS total_sdmn_count
//     FROM (
//         SELECT COUNT(recid) AS TOTAL FROM trx_manrecs_mo_hd WHERE (mo_date >= '$fld_d2dtfrm' AND mo_date <= '$fld_d2dtto')
//         UNION ALL
//         SELECT COUNT(recid) AS TOTAL FROM warehouse_shipdoc_hd WHERE done = '1' (done_date >= '$fld_d2dtfrm' AND done_date <= '$fld_d2dtto'))
//     ) AS subquery
//     ";
//     $q = $mylibzdb->myoa_sql_exec2($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
//     $rw = $q->getRowArray();
//     $total_sdmn_count = $rw['total_sdmn_count'];

//     //TOTAL CREATED DR IN TAP
//     $str="
//     SELECT SUM(TOTAL) AS total_tap_count
//     FROM (
//         SELECT COUNT(recid) AS TOTAL FROM tap_shipdoc_hd WHERE (done_date >= '$fld_d2dtfrm' AND done_date <= '$fld_d2dtto'))
//     ) AS subquery
//     ";
//     $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
//     $rw = $q->getRowArray();
//     $total_tap_count = $rw['total_tap_count'];

//     $total_dr_count = $total_sdmn_count + $total_tap_count;
// }else{
//     //TOTAL CREATED DR
//     $str="
//     SELECT SUM(TOTAL) AS total_sdmn_count
//     FROM (
//         SELECT COUNT(recid) AS TOTAL FROM trx_manrecs_mo_hd WHERE (mo_date >= '2024-01-01' AND mo_date <= NOW())
//         UNION ALL
//         SELECT COUNT(recid) AS TOTAL FROM warehouse_shipdoc_hd WHERE (done_date >= '2024-01-01' AND done_date <= NOW())
//     ) AS subquery
//     ";
//     $q = $mylibzdb->myoa_sql_exec2($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
//     $rw = $q->getRowArray();
//     $total_sdmn_count = $rw['total_sdmn_count'];

//     //TOTAL CREATED DR IN TAP
//     $str="
//     SELECT SUM(TOTAL) AS total_tap_count
//     FROM (
//         SELECT COUNT(recid) AS TOTAL FROM tap_shipdoc_hd WHERE (done_date >= '2024-01-01' AND done_date <= NOW())
//     ) AS subquery
//     ";
//     $q = $mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
//     $rw = $q->getRowArray();
//     $total_tap_count = $rw['total_tap_count'];

//     $total_dr_count = $total_sdmn_count + $total_tap_count;
// }


// //TOTAL IN TRANSIT
// $str="
// SELECT COUNT(aa.`recid`) total_intransit FROM warehouse_shipdoc_hd aa WHERE aa.`is_intransit` = 'Y' and aa.`done` = '1' AND (done_date >= '2024-01-01' AND done_date <= NOW())
// ";
// $q = $mylibzdb->myoa_sql_exec2($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
// $rw = $q->getRowArray();
// $total_intransit = $rw['total_intransit'];

?>
<main id="main" class="main">
    <div class="pagetitle">
		<h1>Dashboard <?=$fld_d2brnch;?></h1>
		<nav>
			<ol class="breadcrumb">
			  <li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
			  <li class="breadcrumb-item"><a href="<?=site_url();?>">Dashboard</a></li>
			  <li class="breadcrumb-item active"><a href="<?=site_url();?>mydashb-dr">DR Monitoring</a></li>
			</ol>
		</nav>
	</div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="row mb-3 me-form-font"> 
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header mb-3">
                        <div class="row">
                            <div class="col-6 text-start">
                                <h3 class="h4 mb-0"> <i class="bi bi-diagram-3"></i> Delivery Receipt Monitoring</h3>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" id="btn-drdailydl" class="btn btn-info btn-sm m-0 rounded px-3 btn-drdailydl" > Download DR Summary </button>
                                <button type="button" id="btn-droverldl" class="btn btn-warning btn-sm m-0 rounded px-3 btn-droverldl" > Download Overall </button>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-4 mx-auto bg-light p-4 rounded">
                                <div class="mt-3">
                                    <input type="hidden" id="rtp_type" value="">
                                    <h6 class="card-title p-0">Branch Account:</h6>
                                    <input type="text"  placeholder="Branch Account" id="branch_account" name="branch_account" class="branch_account form-control form-control-sm " required/>
                                </div>
                                <div class="mt-3">
                                    <input type="hidden" id="rtp_type" value="">
                                    <h6 class="card-title p-0">D/S/M Tag:</h6>
                                    <select name="dsm_tag" id="dsm_tag" class="dsm_tag form-control form-control-sm">
                                        <option value=""></option>
                                        <option value="S">Store Use</option>
                                        <option value="M">Membership Card</option>
                                        <option value="R">RCV In From Pullout</option>
                                        <option value="D">Deliveries</option>
                                        <option value="C">CP</option>
                                        <option value="W">Warehouse Receiving</option>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <input type="hidden" id="rtp_type" value="">
                                    <h6 class="card-title p-0">Branch name:</h6>
                                    <input type="text"  placeholder="Branch Name" id="branch_name" name="branch_name" class="branch_name form-control form-control-sm " value="<?=$BRNCH_NAME;?>" required/>
                                </div>
                                <div class="mt-3">
                                    <h6 class="card-title p-0">Branch area:</h6>
                                    <input type="text"  placeholder="Branch area" id="branch_area" name="branch_area" class="branch_area form-control form-control-sm " required/>
                                </div>
                                <div class="mt-3">
                                    <h6 class="card-title p-0">Date from:</h6>
                                    <input type="date"  placeholder="Date from" id="date_from" name="date_from" class="form-control form-control-sm " required/>
                                </div>
                                <div class="mt-3">
                                    <h6 class="card-title p-0">Date to:</h6>
                                    <input type="date"  placeholder="Date from" id="date_to" name="date_to" class="form-control form-control-sm " required/>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" id="btn-processrecs" class="btn btn-success btn-sm m-0 rounded px-3 btn-processrecs" > Process </button>
                                </div>
                            </div> 
                        </div>

                        <hr>

                        <?php if($cuserrema == 'B'):?>
                            <section class="section dashboard">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title">Created DR</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">WHSE</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-save"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-created">0</h6>
                                                            <span class="small pt-2 ps-1 text-success">Total Created DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-createddr" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title">Intransit</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">TPD</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-truck"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-intransit">0</h6>
                                                            <span class="small pt-2 ps-1 text-danger">Total Intransit DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-intransit" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Received/For Countered</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOO</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-card-checklist"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-receive">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Receiving Encoded</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-received" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Received W/O Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOO</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-card-list"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-receivedwoclaims">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Receiving W/O CLAIMS</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-receivedwoclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Corrected Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOI</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-journal-check"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-correctedclaims">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Corrected Claims</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-correctedclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
                                </div> 
                            </div> 
      
                        </section>    

                        <?php else:?>
                        <section class="section dashboard">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
  
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title">Created DR</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">WSHE</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-save"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-created">0</h6>
                                                            <span class="small pt-2 ps-1 text-success">Total Created DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-createddr" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title">Intransit</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">TPD</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-truck"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-intransit">0</h6>
                                                            <span class="small pt-2 ps-1 text-danger">Total Intransit DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-intransit" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Received/For Countered</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOO</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-card-checklist"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-receive">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Receiving Encoded</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-received" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Received W/O Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOO</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-card-list"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-receivedwoclaims">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Receiving W/O CLAIMS</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-receivedwoclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  					
                                    </div> 
                                </div> 
                            </div> 

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Claims For Validation</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOA</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-check2-all"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-claimsforval">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Claims For Validation</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-claimsforval" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Not Validated Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOA</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-check2"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-notvalclaims">0</h6>
                                                            <span class="text-danger small pt-2 ps-1">Total Not Validated Claims</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-notvalclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  		

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Claims For Verification</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">CAD</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-list-task"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-claimsforveri">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Claims For Verification</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-claimsforveri" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Not Verified Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">CAD</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-list-ul"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-notverclaims">0</h6>
                                                            <span class="text-danger small pt-2 ps-1">Total Not Verified Claims</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-notverclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  	
                                        
                                    </div> 
                                </div> 
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Claims For Review</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOD</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-folder2-open"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-claimsforrev">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Claims For Review</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-claimsforrev" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Not Reviewed Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOD</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-folder-x"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-notrevclaims">0</h6>
                                                            <span class="text-danger small pt-2 ps-1">Total Not Reviewed Claims</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-notrevclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                            
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Claims For Correction</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOI</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-journal-check"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-claimsforcorrect">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Claims For Correction</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-claimsforcorrect" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 	

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Corrected Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOI</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-journal-check"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-correctedclaims">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Corrected Claims</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-correctedclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
        
                                        
                                    </div> 
                                </div> 
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Not Corrected Claims</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOI</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-journal-x"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-notcorclaims">0</h6>
                                                            <span class="text-danger small pt-2 ps-1">Total Not Corrected Claims</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-notcorclaims" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        
                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">For Countered DR</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOI</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-clipboard-check"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-forcountered">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total For Countered DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-forcountered" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Countered DR</h5>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <h5 class="card-title">SOI</h5>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-clipboard-check"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-countered">0</h6>
                                                            <span class="text-success small pt-2 ps-1">Total Countered DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-countered" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-xxl-3 col-md-6">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-6 text-start">
                                                            <h5 class="card-title text-nowrap">Cancelled DR</h5>
                                                        </div>
                                                        <div class="col-6 text-end">

                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-trash"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h6 id="txt-cancelled">0</h6>
                                                            <span class="text-danger small pt-2 ps-1">Total Cancelled DR</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" id="btn-cancelled" class="btn btn-outline-success btn-sm m-0 rounded px-3" >More Info <span><i class="bi bi-arrow-bar-right"></i></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  		            
                                    </div> 
                                </div> 
                            </div>

                        </section>
                        <?php endif?>
 
                        <div class="rcvng-dash2-container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="mydshbrdrecs" >
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rcvng-dash3-container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="mycreatedr" >
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <section class="section dashboard">
    <div class="row">
        <div class="col-md-12">
            <div id="dl_rpt" class="container-fluid">
        
            </div>
        </div> <!-- end col-md-12 -->
    </div>
</main>


<?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
    echo $mylibzsys->memypreloader01('mepreloaderme');
    ?>  

<script type="text/javascript">

    __mysys_apps.mepreloader('mepreloaderme',false);

    $(document).ready(function() {
        get_dashData();
    });

    $('#btn-received').on('click',function(){
        get__report('');
        $('#rtp_type').val('');
    });

    $('#btn-receivedwoclaims').on('click',function(){
        get__report('2');
        $('#rtp_type').val('2');
    });

    $('#btn-claimsforval').on('click',function(){
        get__report('5');
        $('#rtp_type').val('5');
    });

    $('#btn-claimsforveri').on('click',function(){
        get__report('6');
        $('#rtp_type').val('6');
    });

    $('#btn-claimsforcorrect').on('click',function(){
        get__report('3');
        $('#rtp_type').val('3');
    });
    
    $('#btn-correctedclaims').on('click',function(){
        get__report('4');
        $('#rtp_type').val('4');
    });

    $('#btn-notvalclaims').on('click',function(){
        get__report('7');
        $('#rtp_type').val('7');
    });

    $('#btn-notverclaims').on('click',function(){
        get__report('8');
        $('#rtp_type').val('8');
    });

    $('#btn-notcorclaims').on('click',function(){
        get__report('9');
        $('#rtp_type').val('9');
    });

    $('#btn-claimsforrev').on('click',function(){
        get__report('10');
        $('#rtp_type').val('10');
    });

    $('#btn-notrevclaims').on('click',function(){
        get__report('11');
        $('#rtp_type').val('11');
    });

    $('#btn-forcountered').on('click',function(){
        get__report('12');
        $('#rtp_type').val('12');
    });

    $('#btn-countered').on('click',function(){
        get__report('13');
        $('#rtp_type').val('13');
    });

    $('#btn-createddr').on('click',function(){
        get__report('14');
        $('#rtp_type').val('14');
    });

    $('#btn-intransit').on('click',function(){
        get__report('15');
        $('#rtp_type').val('15');
    });

    $('#btn-cancelled').on('click',function(){
        get__report('16');
        $('#rtp_type').val('16');
    });

    
    function get__report(rtp_type){
        try { 
            var fld_d2dtfrm = jQuery('#date_from').val();
            var fld_brancharea = jQuery('#branch_area').val();
            var fld_d2dtto  = jQuery('#date_to').val();
            var fld_d2brnch = jQuery('#branch_name').val();

            __mysys_apps.mepreloader('mepreloaderme',true);

            var mparam = {
            fld_d2dtfrm : fld_d2dtfrm,
            fld_d2dtto : fld_d2dtto,
            fld_d2brnch:fld_d2brnch,
            fld_brancharea:fld_brancharea,
            report:rtp_type,
            mpages:1
            }; 


            $.ajax({ // default declaration of ajax parameters
            type: "POST",
            url: '<?=site_url();?>mydashb-dr-rcvng',
            context: document.body,
            data: eval(mparam),
            global: false,
            cache: false,
            success: function(data){ //display html using divID
                __mysys_apps.mepreloader('mepreloaderme',false);

                // Update the default element
                jQuery('#mydshbrdrecs').html(data);
                
                
                return false;
            },
            error: function() { // display global error on the menu function
            alert('error loading page...');
            return false;
            }   
            }); 
        } catch(err) {
            var mtxt = 'There was an error on this page.\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\nClick OK to continue.';
            alert(mtxt);
            return false;
        }  //end try  
    }

    __mysys_apps.mepreloader('mepreloaderme',false);

    $('#btn-processrecs').on('click',function() {
        get_dashData();
    });

    function get_dashData(){
        try { 
            __mysys_apps.mepreloader('mepreloaderme',true);
            var fld_d2dtfrm = jQuery('#date_from').val();
            var fld_brancharea = jQuery('#branch_area').val();
            var fld_d2dtto  = jQuery('#date_to').val();
            var fld_d2brnch = jQuery('#branch_name').val();

            var mparam = {
            fld_d2dtfrm : fld_d2dtfrm,
            fld_brancharea:fld_brancharea,
            fld_d2dtto : fld_d2dtto,
            fld_d2brnch:fld_d2brnch
            }; 


            $.ajax({ // default declaration of ajax parameters
                type: "POST",
                url: '<?=site_url();?>mydashb-dr-rcvng-process',
                context: document.body,
                data: eval(mparam),
                global: false,
                cache: false,
                success: function(data){ //display html using divID
                    __mysys_apps.mepreloader('mepreloaderme',false);
                    $('#mydshbrdrecs').html(data);
                    return false;
                },
                error: function() { // display global error on the menu function
                    alert('error loading page...');
                    return false;
                }   
            }); 
        } catch(err) {
            var mtxt = 'There was an error on this page.\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\nClick OK to continue.';
            alert(mtxt);
            return false;
        }  //end try   
    }

    // $('#btn-drdailydl').click(function(){
    //     try { 
    //         __mysys_apps.mepreloader('mepreloaderme',true);
    //         var fld_d2dtfrm = jQuery('#date_from').val();
    //         var fld_brancharea = jQuery('#branch_area').val();
    //         var fld_d2dtto  = jQuery('#date_to').val();
    //         var fld_d2brnch = jQuery('#branch_name').val();
    //         var fld_dlsomhd =  jQuery('#dsm_tag').val();
	// 	    var fld_dlbuacct = jQuery('#branch_account').val();

    //         var mparam = {
    //         fld_d2dtfrm : fld_d2dtfrm,
    //         fld_brancharea:fld_brancharea,
    //         fld_d2dtto : fld_d2dtto,
    //         fld_d2brnch:fld_d2brnch,
    //         fld_dlsomhd:fld_dlsomhd,
    //         fld_dlbuacct:fld_dlbuacct
    //         }; 


    //         $.ajax({ // default declaration of ajax parameters
    //             type: "POST",
    //             url: '<?=site_url();?>mydashb-dr-daily-dl',
    //             context: document.body,
    //             data: eval(mparam),
    //             global: false,
    //             cache: false,
    //             success: function(data){ //display html using divID
    //                 __mysys_apps.mepreloader('mepreloaderme',false);
    //                 $('#mydshbrdrecs').html(data);
    //                 return false;
    //             },
    //             error: function() { // display global error on the menu function
    //                 alert('error loading page...');
    //                 return false;
    //             }   
    //         }); 
    //     } catch(err) {
    //         var mtxt = 'There was an error on this page.\n';
    //         mtxt += 'Error description: ' + err.message;
    //         mtxt += '\nClick OK to continue.';
    //         alert(mtxt);
    //         return false;
    //     }  //end try   
    // });

    $('#btn-drdailydl').click(function() {
    try {
        var fld_d2dtfrm = $('#date_from').val();
        var fld_brancharea = $('#branch_area').val();
        var fld_d2dtto = $('#date_to').val();
        var fld_d2brnch = $('#branch_name').val();
        var fld_dlsomhd = $('#dsm_tag').val();
        var fld_dlbuacct = $('#branch_account').val();

        if (!fld_d2brnch) {
            data = "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> PLEASE SELECT A BRANCH!!! </div>"
            jQuery('#memsgtestent_bod').html(data);
            jQuery('#memsgtestent').modal('show');
            return false;
        }

        if (!fld_d2dtfrm || !fld_d2dtto) {
            data = "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> DATE FROM/TO IS REQUIRED!!! </div>"
            jQuery('#memsgtestent_bod').html(data);
            jQuery('#memsgtestent').modal('show');
            return false;
        }


        
        var url = '<?=site_url();?>mydashb-dr-daily-dl' +
                  '?fld_d2dtfrm=' + encodeURIComponent(fld_d2dtfrm) +
                  '&fld_brancharea=' + encodeURIComponent(fld_brancharea) +
                  '&fld_d2dtto=' + encodeURIComponent(fld_d2dtto) +
                  '&fld_d2brnch=' + encodeURIComponent(fld_d2brnch) +
                  '&fld_dlsomhd=' + encodeURIComponent(fld_dlsomhd) +
                  '&fld_dlbuacct=' + encodeURIComponent(fld_dlbuacct);

        window.open(url, '_blank');

            __mysys_apps.mepreloader('mepreloaderme', false);
        } catch (err) {
            __mysys_apps.mepreloader('mepreloaderme', false);
            var mtxt = 'There was an error on this page.\n';
            mtxt += 'Error description: ' + err.message;
            mtxt += '\nClick OK to continue.';
            alert(mtxt);
            return false;
        }
    });

    __mysys_apps.mepreloader('mepreloaderme', false);
    jQuery('#branch_name')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) { 
				event.preventDefault();
			}
			if( event.keyCode === jQuery.ui.keyCode.TAB ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>mydashb-dr-getbranch/',  //mysearchdata/companybranch_v
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();

			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				jQuery('#branch_name').val(terms);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	});	//end branch_name

    __mysys_apps.mepreloader('mepreloaderme',false);

    jQuery('#branch_area')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) { 
				event.preventDefault();
			}
			if( event.keyCode === jQuery.ui.keyCode.TAB ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>mydashb-dr-getbrancharea/',  //mysearchdata/companybranch_v
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();

			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				jQuery('#branch_area').val(terms);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	});	//end branch_name

    __mysys_apps.mepreloader('mepreloaderme',false);

    jQuery('#branch_account')
		// don't navigate away from the field on tab when selecting an item
		.bind( 'keydown', function( event ) {
			if ( event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery( this ).data( 'ui-autocomplete' ).menu.active ) { 
				event.preventDefault();
			}
			if( event.keyCode === jQuery.ui.keyCode.TAB ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: '<?= site_url(); ?>mydashb-dr-getbranchuser/',  //mysearchdata/companybranch_v
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			search: function(oEvent, oUi) {
				var sValue = jQuery(oEvent.target).val();

			},
			select: function( event, ui ) {
				var terms = ui.item.value;
				jQuery('#branch_account').val(terms);
				jQuery(this).autocomplete('search', jQuery.trim(terms));
				return false;
			}
		})
		.click(function() {
			var terms = this.value;
			jQuery(this).autocomplete('search', jQuery.trim(terms));
	});	//end branch_account

    __mysys_apps.mepreloader('mepreloaderme',false);
    

	$('#btn-droverldl').on('click',function(){
	try { 
	  //$('html,body').scrollTop(0);

		var fld_d2dtfrm = jQuery('#date_from').val();
		var fld_brancharea = jQuery('#branch_area').val();
		var fld_d2dtto  = jQuery('#date_to').val();
		var fld_d2brnch = jQuery('#branch_name').val();

		__mysys_apps.mepreloader('mepreloaderme',true);

        // if (!fld_d2brnch) {
        //     data = "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> PLEASE SELECT A BRANCH!!! </div>"
        //     jQuery('#memsgtestent_bod').html(data);
        //     jQuery('#memsgtestent').modal('show');
        //     __mysys_apps.mepreloader('mepreloaderme',false);
        //     return false;
        // }

        if (!fld_d2dtfrm || !fld_d2dtto) {
            data = "<div class=\"alert alert-danger mb-0 fw-bold\" role=\"alert\"> DATE FROM/TO IS REQUIRED!!! </div>"
            jQuery('#memsgtestent_bod').html(data);
            jQuery('#memsgtestent').modal('show');
            __mysys_apps.mepreloader('mepreloaderme',false);
            return false;
        }

		var mparam = {
			fld_d2dtfrm : fld_d2dtfrm,
			fld_brancharea:fld_brancharea,
			fld_d2dtto : fld_d2dtto,
			fld_d2brnch:fld_d2brnch
		}; 
	
	  $.ajax({ // default declaration of ajax parameters
	  type: "POST",
	  url: '<?=site_url();?>mydashb-dr-overall-dl',
	  context: document.body,
	  data: mparam,
	  global: false,
	  cache: false,
	    success: function(data)  { //display html using divID
			__mysys_apps.mepreloader('mepreloaderme',false);

	        $('#dl_rpt').html(data)
	        // $('#dl_rpt').html(data);
	        
	        return false;
	    },
	    error: function() { // display global error on the menu function
	      alert('error loading page...');
	      
	      return false;
	    } 
	  });     
	            
	} catch(err) {
	  var mtxt = 'There was an error on this page.\n';
	  mtxt += 'Error description: ' + err.message;
	  mtxt += '\nClick OK to continue.';
	  alert(mtxt);
	  
	  return false;

	}  //end try
});
</script>

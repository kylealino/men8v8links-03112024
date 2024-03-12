<?php 

$request = \Config\Services::request();
$mydbname = model('App\Models\MyDBNamesModel');
$mylibzdb = model('App\Models\MyLibzDBModel');
$mylibzsys = model('App\Models\MyLibzSysModel');
$this->myusermod = model('App\Models\MyUserModel');
$this->mymdacct = model('App\Models\MyDRRCVModel');
$cuser   = $this->myusermod->mysys_user();
$mpw_tkn = $this->myusermod->mpw_tkn();
$cusergrp = $this->myusermod->mysys_usergrp();
$cuserrema = $this->myusermod->mysys_userrema();
$txtsearchedrec = $request->getVar('txtsearchedrec');

$fld_d2dtfrm    = $request->getVar('fld_d2dtfrm'); 
$fld_d2dtto     = $request->getVar('fld_d2dtto'); 
$fld_d2brnch    = $request->getVar('fld_d2brnch');
$fld_brancharea = $request->getVar('fld_brancharea');


?>

<div class="card">
	<div class="card-header mb-3">
		<h3 class="h4 mb-0"> <i class="bi bi-list-ul"></i> Intransit Dr Records </h3>
	</div>

	<div class="card-body">
		<div class="pt-2  mt-2"> 
			<nav class="nav nav-pills flex-column flex-sm-row border  gap-1 fw-bold">
                <input type="hidden" id="sd_type" value="">
                <input type="hidden" id="fld_d2brnch" value="<?=$fld_d2brnch;?>">
                <input type="hidden" id="fld_d2dtfrm" value="<?=$fld_d2dtfrm;?>">
                <input type="hidden" id="fld_d2dtto" value="<?=$fld_d2dtto;?>">
                <input type="hidden" id="fld_brancharea" value="<?=$fld_brancharea;?>">
				<a id="anchor-cwo" class="flex-sm-fill text-sm-center  active p-2 bg-light rounded-left" > <i class="bi bi-truck"> </i> Intransit Crossdocking </a>
				<a id="anchor-mn" class=" flex-sm-fill text-sm-center   p-2  bg-light "><i class="bi bi-journal-text"></i> Intransit Manual SD </a>
				<a id="anchor-tap" class=" flex-sm-fill text-sm-center   p-2 bg-light rounded-right"><i class="bi bi-truck-front"></i> Intransit TAP </a>
                <a id="anchor-crpl" class=" flex-sm-fill text-sm-center   p-2 bg-light rounded-right"><i class="bi bi-truck-front"></i> Intransit OLD SD </a>
			</nav>
		</div>

		<div id="intransit-vw" class="text-center p-2 rounded-3  mt-3 border-dotted bg-light p-4 ">
			<?php
			?> 
		</div> 
	</div> 
</div>

<?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
?>  

<script type="text/javascript"> 

	 __mysys_apps.mepreloader('mepreloaderme',false);

     $(document).ready(function() {
        $('#anchor-mn').removeClass('active');
        $('#anchor-mn').removeClass('bg-white');
        $('#anchor-mn').addClass('bg-light');
        $('#anchor-cwo').removeClass('bg-light');
        $('#anchor-cwo').addClass('bg-white');
        $('#anchor-cwo').addClass('active');
        $('#anchor-tap').removeClass('active');
        $('#anchor-tap').removeClass('bg-white');
        $('#anchor-tap').addClass('bg-light');
        $('#anchor-crpl').removeClass('active');
        $('#anchor-crpl').removeClass('bg-white');
        $('#anchor-crpl').addClass('bg-light');
        var sd_type = 'CWO';
        var fld_d2brnch = jQuery('#fld_d2brnch').val();
        var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
        var fld_d2dtto = jQuery('#fld_d2dtto').val();
        var fld_brancharea = jQuery('#fld_brancharea').val();

        intransitdr_view_recs(sd_type,fld_d2brnch,fld_d2dtfrm,fld_d2dtto,fld_brancharea);
    });

	 $('#anchor-cwo').on('click',function(){
        $('#anchor-mn').removeClass('active');
        $('#anchor-mn').removeClass('bg-white');
        $('#anchor-mn').addClass('bg-light');
        $('#anchor-cwo').removeClass('bg-light');
        $('#anchor-cwo').addClass('bg-white');
        $('#anchor-cwo').addClass('active');
        $('#anchor-tap').removeClass('active');
        $('#anchor-tap').removeClass('bg-white');
        $('#anchor-tap').addClass('bg-light');
        $('#anchor-crpl').removeClass('active');
        $('#anchor-crpl').removeClass('bg-white');
        $('#anchor-crpl').addClass('bg-light');
        var sd_type = 'CWO';
        var fld_d2brnch = jQuery('#fld_d2brnch').val();
        var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
        var fld_d2dtto = jQuery('#fld_d2dtto').val();
        var fld_brancharea = jQuery('#fld_brancharea').val();

        intransitdr_view_recs(sd_type,fld_d2brnch,fld_d2dtfrm,fld_d2dtto,fld_brancharea);

    });

    $('#anchor-mn').on('click',function(){
        $('#anchor-cwo').removeClass('active');
        $('#anchor-cwo').removeClass('bg-white');
        $('#anchor-cwo').addClass('bg-light');
        $('#anchor-tap').removeClass('active');
        $('#anchor-tap').removeClass('bg-white');
        $('#anchor-tap').addClass('bg-light');
        $('#anchor-mn').removeClass('bg-light');
        $('#anchor-mn').addClass('bg-white');
        $('#anchor-mn').addClass('active');
        $('#anchor-crpl').removeClass('active');
        $('#anchor-crpl').removeClass('bg-white');
        $('#anchor-crpl').addClass('bg-light');
        var sd_type = 'MN';
        var fld_d2brnch = jQuery('#fld_d2brnch').val();
        var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
        var fld_d2dtto = jQuery('#fld_d2dtto').val();
        var fld_brancharea = jQuery('#fld_brancharea').val();

        intransitdr_view_recs(sd_type,fld_d2brnch,fld_d2dtfrm,fld_d2dtto,fld_brancharea);

    });

    $('#anchor-tap').on('click',function(){
        $('#anchor-cwo').removeClass('active');
        $('#anchor-cwo').removeClass('bg-white');
        $('#anchor-cwo').addClass('bg-light');
        $('#anchor-mn').removeClass('active');
        $('#anchor-mn').removeClass('bg-white');
        $('#anchor-mn').addClass('bg-light');
        $('#anchor-tap').removeClass('bg-light');
        $('#anchor-tap').addClass('bg-white');
        $('#anchor-tap').addClass('active');
        $('#anchor-crpl').removeClass('active');
        $('#anchor-crpl').removeClass('bg-white');
        $('#anchor-crpl').addClass('bg-light');
        var sd_type = 'TAP';
        var fld_d2brnch = jQuery('#fld_d2brnch').val();
        var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
        var fld_d2dtto = jQuery('#fld_d2dtto').val();
        var fld_brancharea = jQuery('#fld_brancharea').val();

        intransitdr_view_recs(sd_type,fld_d2brnch,fld_d2dtfrm,fld_d2dtto,fld_brancharea);

    });

    $('#anchor-crpl').on('click',function(){
        $('#anchor-cwo').removeClass('active');
        $('#anchor-cwo').removeClass('bg-white');
        $('#anchor-cwo').addClass('bg-light');
        $('#anchor-mn').removeClass('active');
        $('#anchor-mn').removeClass('bg-white');
        $('#anchor-mn').addClass('bg-light');
        $('#anchor-tap').addClass('bg-light');
        $('#anchor-tap').removeClass('bg-white');
        $('#anchor-tap').removeClass('active');
        $('#anchor-crpl').addClass('active');
        $('#anchor-crpl').addClass('bg-white');
        $('#anchor-crpl').removeClass('bg-light');
        var sd_type = 'OLD';
        var fld_d2brnch = jQuery('#fld_d2brnch').val();
        var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
        var fld_d2dtto = jQuery('#fld_d2dtto').val();
        var fld_brancharea = jQuery('#fld_brancharea').val();

        intransitdr_view_recs(sd_type,fld_d2brnch,fld_d2dtfrm,fld_d2dtto,fld_brancharea);

    });

    function intransitdr_view_recs(sd_type,fld_d2brnch,fld_d2dtfrm,fld_d2dtto,fld_brancharea){ 
        var ajaxRequest;

        ajaxRequest = jQuery.ajax({
            url: "<?=site_url();?>mydashb-dr-intransit-recs",
            type: "post",
            data: {
                sd_type: sd_type,
                fld_d2brnch:fld_d2brnch
                
            }
        });

        ajaxRequest.done(function(response, textStatus, jqXHR) {
            jQuery('#intransit-vw').html(response);

        });
    };
</script>

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
$sd_type = $request->getVar('sd_type');

$fld_d2dtfrm    = $request->getVar('fld_d2dtfrm'); 
$fld_d2dtto     = $request->getVar('fld_d2dtto'); 
$fld_d2brnch    = $request->getVar('fld_d2brnch');
$fld_brancharea = $request->getVar('fld_brancharea');

$mpages = (empty($mylibzsys->oa_nospchar($request->getVar('mpages'))) ? 0 : $mylibzsys->oa_nospchar($request->getVar('mpages')));
$mpages = ($mpages > 0 ? $mpages : 1);
$apages = array();
$mpages = $npage_curr;
$npage_count = $npage_count;
for($aa = 1; $aa <= $npage_count; $aa++) {
	$apages[] = $aa . "xOx" . $aa;
}

?>

<style>
	table.memetable, th.memetable, td.memetable {
		border: 1px solid #F6F5F4;
		border-collapse: collapse;
	}
	thead.memetable, th.memetable, td.memetable {
		padding: 6px;
	}
</style>


<?=form_open('mydashb-dr-created-recs-vw','class="needs-validation-search" id="myfrmsearchrec" ');?>

    <div class="col-md-6 mb-1">
        <div class="input-group input-group-sm">
            <label class="input-group-text fw-bold" for="search">Search:</label>
            <input type="text" id="txtsearchedrec" class="form-control form-control-sm" name="txtsearchedrec" placeholder="Search" value="<?=$txtsearchedrec;?>"/>
            <button type="submit" class="btn btn-dgreen btn-sm" style="background-color:#167F92; color:#fff;"><i class="bi bi-search"></i></button>
			<?=anchor('mydashb-dr', 'Reset',' class="btn btn-primary" ');?>
        </div>
    </div>
<?=form_close();?> <!-- end of ./form -->
<div class="col-md-8">
    <?=$mylibzsys->mypagination($npage_curr,$npage_count,'__myredirected_rsearch','');?>
</div>
<div class="text-end">
	<input type="hidden" name="sd_type" id="sd_type" class="sd_type" value="<?=$sd_type;?>">
	<input type="hidden" name="fld_d2brnch" id="fld_d2brnch" class="fld_d2brnch" value="<?=$fld_d2brnch;?>">
	<input type="hidden" name="fld_d2dtfrm" id="fld_d2dtfrm" class="fld_d2dtfrm" value="<?=$fld_d2dtfrm;?>">
	<input type="hidden" name="fld_d2dtto" id="fld_d2dtto" class="fld_d2dtto" value="<?=$fld_d2dtto;?>">
	<input type="hidden" name="fld_brancharea" id="fld_brancharea" class="fld_brancharea" value="<?=$fld_brancharea;?>">
	<button class="btn btn-success btn-sm btn_createddr_dl text-end"  id="btn_createddr_dl" type="button">Download</button>	
</div>
<div class="table-responsive mt-2">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<table class="table table-striped table-bordered table-sm table-condensed">
			<thead>
				<tr>
					<th> <?php echo $sd_type;?> Transaction No.</th>
					<th>Plate No</th>
					<th>Branch</th>
					<th>User</th>
					<th>Encoded Date</th>
					<?php if($cuserrema != 'B'):?>
					<th>Cancellation</th>
					<?php endif;?>
				</tr>
			</thead>
			<tbody>
				<?php if($rlist !== '' && $sd_type == 'CWO'):
					$nn = 1;
					foreach($rlist as $row):
						$is_cancelled = $row['is_cancelled'];
				    ?>
					<tr>
						<td nowrap="nowrap"><?=$row['crpl_code'];?></td>
						<td nowrap="nowrap"><?=$row['plate_no'];?></td>
						<td nowrap="nowrap"><?=$row['brnch'];?></td>
						<td nowrap="nowrap"><?=$row['user'];?></td>
						<td nowrap="nowrap"><?=$row['date_encd'];?></td>
						<?php if($cuserrema != 'B'):?>
							<?php if($is_cancelled == 'N'):?>
								<td nowrap="nowrap"><button class="btn btn-warning btn-sm btn_cancel" data-trxno="<?=$row['crpl_code'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancel</button></td>
							<?php else:?>
								<td nowrap="nowrap"><button class="btn btn-danger btn-sm btn_cancel disabled" data-trxno="<?=$row['crpl_code'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancelled</button></td>
							<?php endif;?>
						<?php endif;?>
						
					</tr>
				<?php $nn++; endforeach; ?>

				<?php elseif($rlist !== '' && $sd_type == 'MN'):
					$nn = 1;
					foreach($rlist as $row):
						$is_cancelled = $row['is_cancelled'];
				?>
					<tr>
						<td nowrap="nowrap"><?=$row['motrx_no'];?></td>
						<td nowrap="nowrap"><?=$row['mo_plateno'];?></td>
						<td nowrap="nowrap"><?=$row['branch_id'];?></td>
						<td nowrap="nowrap"><?=$row['muser'];?></td>
						<td nowrap="nowrap"><?=$row['encd_date'];?></td>
						<?php if($cuserrema != 'B'):?>
							<?php if($is_cancelled == 'N'):?>
								<td nowrap="nowrap"><button class="btn btn-warning btn-sm btn_cancel" data-trxno="<?=$row['motrx_no'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancel</button></td>
							<?php else:?>
								<td nowrap="nowrap"><button class="btn btn-danger btn-sm btn_cancel disabled" data-trxno="<?=$row['motrx_no'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancelled</button></td>
							<?php endif;?>
						<?php endif;?>
					</tr>
				<?php  $nn++; endforeach;?>

				<?php elseif($rlist !== '' && $sd_type == 'OLD'):
					$nn = 1;
					foreach($rlist as $row):
						$is_cancelled = $row['is_cancelled'];
				?>
					<tr>
						<td nowrap="nowrap"><?=$row['crpl_code'];?></td>
						<td nowrap="nowrap"><?=$row['plate_no'];?></td>
						<td nowrap="nowrap"><?=$row['brnch'];?></td>
						<td nowrap="nowrap"><?=$row['user'];?></td>
						<td nowrap="nowrap"><?=$row['date_encd'];?></td>
						<?php if($cuserrema != 'B'):?>
							<?php if($is_cancelled == 'N'):?>
								<td nowrap="nowrap"><button class="btn btn-warning btn-sm btn_cancel" data-trxno="<?=$row['crpl_code'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancel</button></td>
							<?php else:?>
								<td nowrap="nowrap"><button class="btn btn-danger btn-sm btn_cancel disabled" data-trxno="<?=$row['crpl_code'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancelled</button></td>
							<?php endif;?>
						<?php endif;?>
					</tr>
				<?php  $nn++; endforeach;?>

				<?php elseif($rlist !== '' && $sd_type == 'TAP'):
					$nn = 1;
					foreach($rlist as $row):
						$is_cancelled = $row['is_cancelled'];
				?>
					<tr>
						<td nowrap="nowrap"><?=$row['crpl_code'];?></td>
						<td nowrap="nowrap"><?=$row['plate_no'];?></td>
						<td nowrap="nowrap"><?=$row['brnch'];?></td>
						<td nowrap="nowrap"><?=$row['user'];?></td>
						<td nowrap="nowrap"><?=$row['date_encd'];?></td>
						<?php if($cuserrema != 'B'):?>
							<?php if($is_cancelled == 'N'):?>
								<td nowrap="nowrap"><button class="btn btn-warning btn-sm btn_cancel" data-trxno="<?=$row['crpl_code'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancel</button></td>
							<?php else:?>
								<td nowrap="nowrap"><button class="btn btn-danger btn-sm btn_cancel disabled" data-trxno="<?=$row['crpl_code'];?>"  id="btn_cancel" type="button" onclick="javascript:mbtn_cancel(this)">Cancelled</button></td>
							<?php endif;?>
						<?php endif;?>
					</tr>
				<?php  $nn++; endforeach; else:?>
					<tr>
						<td colspan="18">No data was found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="dl_created" class="container-fluid">
	
		</div>
	</div> <!-- end col-md-12 -->
</div>
<?php
    echo $mylibzsys->memsgbox1('memsgtestent_danger','<i class="bi bi-exclamation-circle"></i> System Alert','...','bg-pdanger');
    echo $mylibzsys->memsgbox1('memsgtestent','System Alert','...');
	echo $mylibzsys->memsgbox_yesno1('mecancel','Cancel Records...','');
?>  

<script type="text/javascript"> 

	 __mysys_apps.mepreloader('mepreloaderme',false);

	$('.btn_createddr_dl').on('click',function(){
		try { 
		//$('html,body').scrollTop(0);

			var sd_type = jQuery('.sd_type').val();
			var fld_d2brnch = jQuery('.fld_d2brnch').val();
			var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
			var fld_d2dtto = jQuery('.fld_d2dtto').val();
			var fld_brancharea = jQuery('.fld_brancharea').val();
			__mysys_apps.mepreloader('mepreloaderme',true);

			var mparam = {
				sd_type : sd_type,
				fld_d2brnch:fld_d2brnch,
				fld_d2dtfrm:fld_d2dtfrm,
				fld_d2dtto:fld_d2dtto,
				fld_brancharea:fld_brancharea
			}; 
		
		$.ajax({ // default declaration of ajax parameters
		type: "POST",
		url: '<?=site_url();?>mydashb-dr-created-dl',
		context: document.body,
		data: eval(mparam),
		global: false,
		cache: false,
			success: function(data)  { //display html using divID
				__mysys_apps.mepreloader('mepreloaderme',false);
				jQuery('#myModSysMsgBod').css({
					display: ''
				});
				$('#dl_created').html(data)
				// $('#dl_created').html(data);
				
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

	function __myredirected_rsearch(mobj) { 
		try { 
			__mysys_apps.mepreloader('mepreloaderme',true);
			var txtsearchedrec = jQuery('#txtsearchedrec').val();
			var sd_type = jQuery('.sd_type').val();
			var fld_d2brnch = jQuery('.fld_d2brnch').val();
			var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
			var fld_d2dtto = jQuery('.fld_d2dtto').val();
			var fld_brancharea = jQuery('.fld_brancharea').val();
		
            //mytrx_sc/mndt_sc2_recs
            var mparam = { 
            	txtsearchedrec: txtsearchedrec,
				sd_type:sd_type,
				fld_d2brnch:fld_d2brnch,
				fld_d2dtfrm:fld_d2dtfrm,
				fld_d2dtto:fld_d2dtto,
				fld_brancharea:fld_brancharea,
            	mpages: mobj 
            };	
			jQuery.ajax({ // default declaration of ajax parameters
				type: "POST",
				url: '<?=site_url();?>mydashb-dr-created-recs-vw',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { //display html using divID
					__mysys_apps.mepreloader('mepreloaderme',false);
					$('#dr-vw').html(data);
					
					return false;
				},
				error: function() { // display global error on the menu function
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					
					return false;
				}	
			});			
			
		} catch(err) {
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			__mysys_apps.mepreloader('mepreloaderme',false);
			alert(mtxt);
			return false;

		}  //end try
	}	
	
	jQuery('#txtsearchedrec').keypress(function(event) { 
		if(event.which == 13) { 
			event.preventDefault(); 
			try { 
				__mysys_apps.mepreloader('mepreloaderme',true);
				var txtsearchedrec = jQuery('#txtsearchedrec').val();
				var sd_type = jQuery('.sd_type').val();
				var fld_d2brnch = jQuery('.fld_d2brnch').val();
				var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
				var fld_d2dtto = jQuery('.fld_d2dtto').val();
				var fld_brancharea = jQuery('.fld_brancharea').val();

				var mparam = {
					txtsearchedrec: txtsearchedrec,
					sd_type:sd_type,
					fld_d2brnch:fld_d2brnch,
					fld_d2dtfrm:fld_d2dtfrm,
					fld_d2dtto:fld_d2dtto,
					fld_brancharea:fld_brancharea,
					mpages: 1 
				};	

				jQuery.ajax({ // default declaration of ajax parameters
					type: "POST",
					url: '<?=site_url();?>mydashb-dr-created-recs-vw',
					context: document.body,
					data: eval(mparam),
					global: false,
					cache: false,
					success: function(data)  { //display html using divID
						jQuery('#dr-vw').html(data);
						__mysys_apps.mepreloader('mepreloaderme',false);
						return false;
					},
					error: function() { // display global error on the menu function
						__mysys_apps.mepreloader('mepreloaderme',false);
						alert('error loading page...');
						return false;
					}	
				});	
			} catch(err) { 
				var mtxt = 'There was an error on this page.\n';
				mtxt += 'Error description: ' + err.message;
				mtxt += '\nClick OK to continue.';
				__mysys_apps.mepreloader('mepreloaderme',false);
				alert(mtxt);
				return false;
			}  //end try	
			
		}
	});	
	

	(function () {
		'use strict'

		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.needs-validation-search')
		// Loop over them and prevent submission
		Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}
				form.classList.add('was-validated') 

				try {
					event.preventDefault();
					event.stopPropagation();


					//start here
					try { 
						__mysys_apps.mepreloader('mepreloaderme',true);
						var txtsearchedrec = jQuery('#txtsearchedrec').val();
						var sd_type = jQuery('.sd_type').val();
						var fld_d2brnch = jQuery('.fld_d2brnch').val();
						var fld_d2dtfrm = jQuery('.fld_d2dtfrm').val();
						var fld_d2dtto = jQuery('.fld_d2dtto').val();
						var fld_brancharea = jQuery('.fld_brancharea').val();
						
						var mparam = {
							txtsearchedrec: txtsearchedrec,
							sd_type:sd_type,
							fld_d2brnch:fld_d2brnch,
							fld_d2dtfrm:fld_d2dtfrm,
							fld_d2dtto:fld_d2dtto,
							fld_brancharea:fld_brancharea,
							mpages: 1 
						};	
						
						jQuery.ajax({ // default declaration of ajax parameters
							type: "POST",
							url: '<?=site_url();?>mydashb-dr-created-recs-vw',
							context: document.body,
							data: eval(mparam),
							global: false,
							cache: false,
							success: function(data)  { //display html using divID
								__mysys_apps.mepreloader('mepreloaderme',false);
								jQuery('#dr-vw').html(data);
								
							},
							error: function() { // display global error on the menu function
								__mysys_apps.mepreloader('mepreloaderme',false);
								alert('error loading page...');
								
							}	
						});			
						
					} catch(err) { 
						__mysys_apps.mepreloader('mepreloaderme',false);
						var mtxt = 'There was an error on this page.\n';
						mtxt += 'Error description: ' + err.message;
						mtxt += '\nClick OK to continue.';
						alert(mtxt);
					}  //end try

					//end here



				} catch(err) { 
					__mysys_apps.mepreloader('mepreloaderme',false);
					var mtxt = 'There was an error on this page.\n';
					mtxt += 'Error description: ' + err.message;
					mtxt += '\nClick OK to continue.';
					alert(mtxt);
					return false;
				}  //end try					
			}, false)
		})
	})();	

	function mbtn_cancel(button) { 
		try { 
			var trxno = $(button).data('trxno');
			console.log(trxno);
			jQuery('#mecancel_yes').show();
			jQuery('#mecancel_bod').html(`<span >This process cannot be undone...<br/>Proceed cancellation [<span style="color: red;">${trxno}</span>] anyway?</span><p id=\"mespromo_cancel_itemdetl_msg\"></p>`);
			jQuery('#mecancel_yes').attr('data-trxno',trxno);
			jQuery('#mecancel').modal('show');
			
		 } catch(err) {
			var mtxt = 'There was an error on this page.<br/>';
			mtxt += 'Error description: ' + err.message;
			__mysys_apps.mepreloader('mepreloaderme',false);
			jQuery('#memsgtestent_bod').html('<span class=fw-bolder text-danger">' + mtxt + '</span>');
			jQuery('#memsgtestent').modal('show');
		 }  //end try
		
	} //end  mbtn_cancel

	jQuery('#mecancel_yes').click(function() { 
		try { 
			var trxno = jQuery(this).attr('data-trxno'); 

			var mparam = { 
				trxno: trxno
			} 

			jQuery.ajax({ 
				type: "POST",
				url: '<?=site_url();?>mydashb-dr-created-cancel',
				context: document.body,
				data: eval(mparam),
				global: false,
				cache: false,
				success: function(data)  { 	
					__mysys_apps.mepreloader('mepreloaderme',false);
					jQuery(this).prop('disabled', false);
					jQuery('#mecancel').modal('hide');
					jQuery('#memsgtestent_bod').html(data);
					jQuery('#memsgtestent').modal('show');
					$('#memsgtestent').on('hidden.bs.modal', function (e) { 

					}) 
				},
				error: function() {
					jQuery('#memsgtestent_bod').html('<span class="fw-bolder text-danger">Error loading...</span>');
					jQuery('#memsgtestent').modal('show');
					__mysys_apps.mepreloader('mepreloaderme',false);
					return false;
				}
			}); 

		 } catch(err) {
			var mtxt = 'There was an error on this page.(DITO MAY ERROR)<br/>';
			mtxt += 'Error description: ' + err.message;
			__mysys_apps.mepreloader('mepreloaderme',false);
			jQuery('#memsgtestent_bod').html('<span class="fw-bolder text-danger">' + mtxt + '</span>');
			jQuery('#memsgtestent').modal('show');
		 }  //end try
	
	});
</script>

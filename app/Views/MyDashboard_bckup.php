<?php
$mylibzsys = model('App\Models\MyLibzSysModel');
?>
<main id="main" class="main">
	<div class="pagetitle">
		<h1>Dashboard</h1>
		<nav>
			<ol class="breadcrumb">
			  <li class="breadcrumb-item"><a href="<?=site_url();?>">Home</a></li>
			  <li class="breadcrumb-item active">Dashboard</li>
			  <li class="breadcrumb-item active"><a href="<?=site_url();?>mydashb-dr">DR Monitoring</a></li>
			</ol>
		</nav>
	</div><!-- End Page Title -->
	<section class="section dashboard">
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<!-- Sales Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card sales-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="#">Today</a></li>
									<li><a class="dropdown-item" href="#">This Month</a></li>
									<li><a class="dropdown-item" href="#">This Year</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Sales <span>| Today</span></h5>
								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-cart"></i>
									</div>
									<div class="ps-3">
										<h6>145</h6>
										<span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Sales Card --> 
					<!-- Revenue Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card revenue-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="#">Today</a></li>
									<li><a class="dropdown-item" href="#">This Month</a></li>
									<li><a class="dropdown-item" href="#">This Year</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Revenue <span>| This Month</span></h5>
								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-currency-dollar"></i>
									</div>
									<div class="ps-3">
										<h6>$3,264</h6>
										<span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Revenue Card -->
					<!-- Customers Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card customers-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Inquire</h6>
									</li>
									<li><a class="dropdown-item" href="#">Reload</a></li>
									<li><a class="dropdown-item" href="#">This Month</a></li>
									<li><a class="dropdown-item" href="#">This Year</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Inventory <span>| This Year</span></h5>
								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-people"></i>
									</div>
									<div class="ps-3">
										<h6>1244</h6>
										<span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Customers Card --> 
					
					<!-- JO Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card customers-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="#">Today</a></li>
									<li><a class="dropdown-item" href="#">This Month</a></li>
									<li><a class="dropdown-item" href="#">This Year</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">JO <span>| This Year</span></h5>
								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-people"></i>
									</div>
									<div class="ps-3">
										<h6>9000</h6>
										<span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span>
									</div>
								</div>
							</div>
						</div>
					</div><!-- End JO Card -->  					
				</div> <!-- end row -->
			</div> <!-- end col-lg-12 -->
		</div> <!-- end row -->
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<!-- Regular Deliveries Items -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card sales-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="javascript:mywg_delvreg();">Latest Records</a></li>
									<li><a class="dropdown-item" href="javascript:wg_all_gets();">All Latest Records</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Regular Deliveries Items</h5>
								<div class="d-flex align-items-center" >
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-file-spreadsheet"></i>
									</div>
									<div class="ps-3" id="mywg_delvreg">
										<h6>0.00</h6>
										<span class="text-success small pt-1 fw-bold">0%</span> <span class="text-muted small pt-2 ps-1">increase</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Deliveries Rular --> 	
					<!-- Receving IN from Pull Out -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card revenue-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="javascript:mywg_rcvdinfpout();">Latest Records</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Receving IN from Pull Out</h5>
								<div class="d-flex align-items-center" >
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-receipt"></i>
									</div>
									<div class="ps-3" id="mywg_rcvdinfpout">
										<h6>0.00</h6>
										<span class="text-success small pt-1 fw-bold">0%</span> <span class="text-muted small pt-2 ps-1">increase</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Receving IN from Pull Out --> 	

					<!-- Pull Out from Other Branch -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card customers-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="javascript:mywg_pouttob();">Latest Records</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Pull Out from Other Branch</h5>
								<div class="d-flex align-items-center" >
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-journal-album"></i>
									</div>
									<div class="ps-3" id="mywg_pouttob">
										<h6>0.00</h6>
										<span class="text-success small pt-1 fw-bold">0%</span> <span class="text-muted small pt-2 ps-1">increase</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Pull Out from Other Branch --> 	

					<!-- Pull Outs -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card sales-card">
							<div class="filter">
								<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
								<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<li class="dropdown-header text-start">
										<h6>Filter</h6>
									</li>
									<li><a class="dropdown-item" href="javascript:mywg_pouts();">Latest Records</a></li>
								</ul>
							</div>
							<div class="card-body">
								<h5 class="card-title">Pull Outs</h5>
								<div class="d-flex align-items-center" >
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-receipt-cutoff"></i>
									</div>
									<div class="ps-3" id="mywg_pouts">
										<h6>0.00</h6>
										<span class="text-success small pt-1 fw-bold">0%</span> <span class="text-muted small pt-2 ps-1">increase</span>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- End Pull Outs --> 	
					
				</div> <!-- end row -->
			</div> <!-- end col-lg-12 -->
		</div> <!-- end row -->
	</section>
</main>
<?php
echo $mylibzsys->memypreloader01('mepreloaderme');
?>
<script type="text/javascript"> 
	
	function wg_all_gets() { 
		mywg_delvreg();
		mywg_rcvdinfpout();
		mywg_pouttob();
		mywg_pouts();
	} //end 
	
	__mysys_apps.mepreloader('mepreloaderme',false);
	function mywg_delvreg() {
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			var mparam = {
				maction: 'mywg-delvreg',
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mywg-delvreg',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						//jQuery('#mywg_delvreg').modal('show');
						jQuery('#mywg_delvreg').html(data);
						
						return false;
				},
				error: function() { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try							
	}  //end mywg_delvreg
	
	function mywg_rcvdinfpout() { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			var mparam = {
				maction: 'mywg-rcvdinfpout',
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mywg-rcvdinfpout',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						//jQuery('#mywg_delvreg').modal('show');
						jQuery('#mywg_rcvdinfpout').html(data);
						
						return false;
				},
				error: function() { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 
	} //end mywg_rcvdinfpout
	
	function mywg_pouttob() { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			var mparam = {
				maction: 'mywg-pouttob',
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mywg-pouttob',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						//jQuery('#mywg_delvreg').modal('show');
						jQuery('#mywg_pouttob').html(data);
						
						return false;
				},
				error: function() { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 
	} //end mywg_pouttob
	
	function mywg_pouts() { 
		try {
			__mysys_apps.mepreloader('mepreloaderme',true);
			
			var mparam = {
				maction: 'mywg-pouttob',
			};
			
			jQuery.ajax({ // default declaration of ajax parameters
			type: "POST",
			url: '<?=site_url();?>mywg-pouts',
			context: document.body,
			data: eval(mparam),
			global: false,
			cache: false,
				success: function(data)  { //display html using divID
						__mysys_apps.mepreloader('mepreloaderme',false);
						jQuery('#mywg_pouts').html(data);
						
						return false;
				},
				error: function() { // display global error on the menu function 
					__mysys_apps.mepreloader('mepreloaderme',false);
					alert('error loading page...');
					return false;
				}	
			});	
		} catch(err) { 
			__mysys_apps.mepreloader('mepreloaderme',false);
			var mtxt = 'There was an error on this page.\n';
			mtxt += 'Error description: ' + err.message;
			mtxt += '\nClick OK to continue.';
			alert(mtxt);
			return false;
		}  //end try 
		
	} //end mywg_pouts
	
	
</script>

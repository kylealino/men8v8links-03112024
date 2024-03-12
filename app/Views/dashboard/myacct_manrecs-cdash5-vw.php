<?php 
/**
*  File        : masterdata/acct_mod/man_recs/myacct_manrecs-cdash5-vw.php
*  Auhtor      : Arnel Oquien
*  Date Created: Nov 2021
*  last update : Nov 23 2021
*  description : Dashboard claims
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$cuser = $this->mylibz->mysys_user();
$mpw_tkn = $this->mylibz->mpw_tkn();
$lbc_stats         = $this->mydataz->lk_lbc_stats($this->db_erp);
$txtstats_tag = '';
$this->mymdacct->auto_update_sys_dash();

?>
<input type="hidden" id="rtp_type" value="">
<div class="row hpanel w-100 m-0 mt-4 p-0">
<div class="col-lg-4  offset-lg-4 offset-md-0 panel-body rounded bg-dlight p-4">
<div class="row form-group">
  <div class="col-lg-12  p-0 pl-1">
    Branch Name:
  </div>
  <div class="col-lg-12 p-0 pl-1">
    <input type="text" class="form-control form-control-sm input-sm fld_d2brnch"  id="fld_d2brnch" name="fld_d2brnch"  required/>
  </div>
</div>

<div class="row form-group">
    <div class="col-lg-12 p-0 pl-1">
        Branch Area :
    </div>
    <div class="col-lg-12 p-0 pl-1">
        <input type="text" class="form-control form-control-sm input-sm" data-id="" id="fld_itmgrparea_s" name="fld_itmgrparea_s" value="" required/>
    </div>
</div>
<div class="row form-group">
    <div class="col-lg-12 p-0 pl-1">
       Date From:
    </div>
    <div class="col-lg-12 p-1">
      <input type="text" class="form_datetime form-control form-control-sm input-sm" data-id="" id="fld_d2dtfrm" name="fld_d3dtfrm" value="" required/>
    </div>
</div>
<div class="row form-group">
  <div class="col-lg-12 p-0 pl-1">
     Date To:
  </div>
  <div class="col-lg-12 p-1">
    <input type="text" class="form_datetime form-control form-control-sm input-sm" data-id="" id="fld_d2dtto" name="fld_d3dtto" value="" required/>
  </div>
</div>
<div class="form-group row pl-3 pr-3">
  <div class="col-md-12 p-1" style="display: grid; place-items:center;">
    <button class="btn btn-success btn-sm  m-0"  id="dl_submit_btn_d3rpt" type="button">Process</button>
  </div>
</div>
</div> <!-- end col-md-6 -->
</div>
<div class="col-md-12 mt-2 p-0">
        <div class="row">
          <!-- 1st -->
          <div class="col-lg-4 col-md-12">
            <div class="box box-primary">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3 id="rcvng">0</h3>
                  <p>TOTAL RECEIVING ENCODED</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="#rcvng_vw" class="small-box-footer rcvng_vw">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <!-- 2nd -->
          <div class="col-lg-4 col-md-12">
            <div class="box box-primary">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3 id="noclaims">0</h3>
                  <p>TOTAL RECEIVING W/O CLAIMS</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="#noclaims_vw" class="small-box-footer noclaims_vw">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <!-- 3rd -->
          <div class="col-lg-4 col-md-12">
            <div class="box box-primary">
              <div class="small-box bg-primary">
                <div class="inner">
                  <h3 id="forvalclaims">0</h3>
                  <p>TOTAL CLAIMS FOR VALIDATION</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="#forvalclaims_vw" class="small-box-footer forvalclaims_vw">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
           <!-- 4th additional CAD-->
          <div class="col-lg-4 col-md-12">
            <div class="box box-primary">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3 id="forverify">0</h3>
                  <p>TOTAL CLAIMS FOR VERIFICATION</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="#forverify_vw" class="small-box-footer forverify_vw">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          
           <!-- 4th -->
          <div class="col-lg-4 col-md-12">
            <div class="box box-primary">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3 id="finalclaims">0</h3>
                  <p>TOTAL CLAIMS FOR CORRECTION</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="#finalclaims_vw" class="small-box-footer finalclaims_vw">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          

          <!-- 5th -->
          <div class="col-lg-4 col-md-12">
            <div class="box box-primary">
              <div class="small-box bg-secondary">
                <div class="inner">
                  <h3 id="validatedclaims">0</h3>
                  <p>TOTAL CORRECTED CLAIMS</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="#validatedclaims_vw" class="small-box-footer validatedclaims_vw">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          
        </div>

        <hr class="prettyline">
  
    <div class="rcvng-dash2-container">
      <div id="mymodoutrecs6" >
      </div>
    </div>
</div>
<script type="text/javascript"> 
  $('.form_datetime').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true,
    format: 'mm/dd/yyyy'
  });

  $(".form_datetime").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});

  $('#dl_submit_btn_d3rpt').on('click',function() {

    get_dashData();
});
get_dashData();
function get_dashData(){
      try { 
      $('html,body').scrollTop(500);
      $.showLoading({name: 'line-pulse', allowHide: false });
      
      
      var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
      var fld_brancharea = jQuery('#fld_itmgrparea_s').val();
      var fld_d2dtto  = jQuery('#fld_d2dtto').val();
      var fld_d2brnch = jQuery('#fld_d2brnch').val();
    
      var mparam = {
        fld_d2dtfrm : fld_d2dtfrm,
        fld_brancharea:fld_brancharea,
        fld_d2dtto : fld_d2dtto,
        fld_d2brnch:fld_d2brnch
      }; 


  $.ajax({ // default declaration of ajax parameters
    type: "POST",
    url: '<?=site_url();?>mytrx_acct/myrcv_claims_dash',
    context: document.body,
    data: eval(mparam),
    global: false,
    cache: false,
  success: function(data){ //display html using divID
    $.hideLoading();
    $('#mymodoutrecs6').html(data);
    return false;
  },
  error: function() { // display global error on the menu function
    alert('error loading page...');
    $.hideLoading();
    return false;
  }   
  }); 
      } catch(err) {
        var mtxt = 'There was an error on this page.\n';
        mtxt += 'Error description: ' + err.message;
        mtxt += '\nClick OK to continue.';
        alert(mtxt);
        $.hideLoading();
        return false;
      }  //end try   
}

jQuery('.fld_d2brnch')
// don't navigate away from the field on tab when selecting an item
.bind( 'keydown', function( event ) {
  if ( event.keyCode === jQuery.ui.keyCode.TAB &&
    jQuery( this ).data( 'autocomplete' ).menu.active ) {
    event.preventDefault();
}
if( event.keyCode === jQuery.ui.keyCode.TAB ) {
  event.preventDefault();
}
})
.autocomplete({
  minLength: 0,
  source: '<?= site_url(); ?>mysearchdata/companybranch_v/',
  focus: function() {
// prevent value inserted on focus
return false;
},
search: function(oEvent, oUi) {
  var sValue = jQuery(oEvent.target).val();
//var comp = jQuery('#fld_Company').val();
//var comp = jQuery('#fld_Company').attr("data-id");
jQuery(this).autocomplete('option', 'source', '<?=site_url();?>mysearchdata/companybranch_v'); 
//jQuery(oEvent.target).val('&mcocd=1' + sValue);

},
select: function( event, ui ) {
  var terms      = ui.item.value;
  var mtkn_comp  = ui.item.mtkn_comp;
  var mtknr_rid  = ui.item.mtknr_rid;
  var mtkn_brnch = ui.item.mtkn_brnch;
  var inputID    = $(this).attr('id');

  $('#'+inputID).val(terms);
  $('#'+inputID).data('id',mtknr_rid);
  jQuery(this).autocomplete('search', jQuery.trim(terms));
  return false;
}
})
.click(function() {
var terms = this.value;
jQuery(this).autocomplete('search', jQuery.trim(terms));

});

 
$('.rcvng_vw').on('click',function(){
get__report('');
$('#rtp_type').val('');
});

$('.noclaims_vw').on('click',function(){
get__report('2');
$('#rtp_type').val('2');
});

$('.forvalclaims_vw').on('click',function(){
get__report('5');
$('#rtp_type').val('5');
});
//CAD NEW
$('.forverify_vw').on('click',function(){
get__report('6');
$('#rtp_type').val('6');
});
$('.finalclaims_vw').on('click',function(){
get__report('3');
$('#rtp_type').val('3');
});

$('.validatedclaims_vw').on('click',function(){
get__report('4');
$('#rtp_type').val('4');
});


function get__report(rtp_type){
    try { 
  $('html,body').scrollTop(500);
  $.showLoading({name: 'line-pulse', allowHide: false });


  var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
  var fld_brancharea = jQuery('#fld_itmgrparea_s').val();
  var fld_d2dtto  = jQuery('#fld_d2dtto').val();
  var fld_d2brnch = jQuery('#fld_d2brnch').val();

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
  url: '<?=site_url();?>mytrx_acct/myrcv_claims_recs',
  context: document.body,
  data: eval(mparam),
  global: false,
  cache: false,
  success: function(data){ //display html using divID
  $.hideLoading();
  $('#mymodoutrecs6').html(data);
  return false;
  },
  error: function() { // display global error on the menu function
  alert('error loading page...');
  $.hideLoading();
  return false;
  }   
  }); 
  } catch(err) {
  var mtxt = 'There was an error on this page.\n';
  mtxt += 'Error description: ' + err.message;
  mtxt += '\nClick OK to continue.';
  alert(mtxt);
  $.hideLoading();
  return false;
  }  //end try  
}

function __myredirected_rsearchc5(mobj) { 
  try { 
    //$('html,body').scrollTop(0);
    $.showLoading({name: 'line-pulse', allowHide: false });
    var fld_d2dtfrm = jQuery('#fld_d2dtfrm').val();
    var fld_d2dtto  = jQuery('#fld_d2dtto').val();
    var fld_d2brnch = jQuery('#fld_d2brnch').val();
    var rtp_type    = jQuery('#rtp_type').val();

    var mparam = {
    fld_d2dtfrm : fld_d2dtfrm,
    fld_d2dtto : fld_d2dtto,
    fld_d2brnch:fld_d2brnch,
    report:rtp_type,
    mpages:mobj
    }; 
  
    $.ajax({ // default declaration of ajax parameters
    type: "POST",
    url: '<?=site_url();?>mytrx_acct/myrcv_claims_recs',
    context: document.body,
    data: eval(mparam),
    global: false,
    cache: false,
      success: function(data)  { //display html using divID
          $.hideLoading();
          $('#mymodoutrecs6').html(data);
          
          return false;
      },
      error: function() { // display global error on the menu function
        alert('error loading page...');
        $.hideLoading();
        return false;
      } 
    });     
              
  } catch(err) {
    var mtxt = 'There was an error on this page.\n';
    mtxt += 'Error description: ' + err.message;
    mtxt += '\nClick OK to continue.';
    alert(mtxt);
    $.hideLoading();
    return false;

  }  //end try
}

jQuery('#fld_itmgrparea_s')
            // don't navigate away from the field on tab when selecting an item
            .bind( 'keydown', function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( 'autocomplete' ).menu.active ) {
                    event.preventDefault();
            }
            if( event.keyCode === jQuery.ui.keyCode.TAB ) {
                event.preventDefault();
            }
        })
            .autocomplete({
                minLength: 0,
                source: '<?= site_url(); ?>mysearchdata/companyBranch_grpareav2/',
                focus: function() {
                        // prevent value inserted on focus
                        return false;
                    },
                    search: function(oEvent, oUi) {
                        var sValue = jQuery(oEvent.target).val();
                        //var comp = jQuery('#fld_Company').val();
                        //var comp = jQuery('#fld_Company').attr("data-id");
                        jQuery(this).autocomplete('option', 'source', '<?=site_url();?>mysearchdata/companyBranch_grpareav2'); 
                        //jQuery(oEvent.target).val('&mcocd=1' + sValue);

                    },
                    select: function( event, ui ) {
                        var terms = ui.item.value;
                        jQuery('#fld_itmgrparea_s').val(terms);
                        jQuery(this).autocomplete('search', jQuery.trim(terms));
                        return false;
                    }
                })
            .click(function() {
                    /*var comp = jQuery('#fld_Company').val();
                    var comp2 = this.value +'XOX'+comp;
                    var terms = comp2.split('XOX');//dto naq 4/25
                    */
                    var terms = this.value;
                    jQuery(this).autocomplete('search', jQuery.trim(terms));

        });

</script>

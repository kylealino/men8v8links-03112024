<?php 
$request = \Config\Services::request();
$mylibzsys = model('App\Models\MyLibzSysModel');

$mylibzdb = model('App\Models\MyLibzDBModel');
$mydatum = model('App\Models\MyDatumModel');
$mydbname = model('App\Models\MyDBNamesModel');
$myusermod = model('App\Models\MyUserModel');

$cuser = $myusermod->mysys_user();
$mpw_tkn = $myusermod->mpw_tkn();
$nmnrecs = 0;
$cuserrema = $myusermod->mysys_userrema();
$str_read='readonly'; 
//$file = $this->input->get('file');
$file = $request->getVar('file');
$view = $myusermod->get_Active_menus($mydbname->medb(0),$cuser,"myuaacct_id='64'","myua_acct");
$result = $myusermod->get_Active_menus($mydbname->medb(0),$cuser,"myuaacct_id='46'","myua_acct");
if($result == 1){
 $str_read=''; 
}

//This tag is for grocery only for expiration date
$txt_mo_d = substr($txtdrno, 0,3);
$exp_date_disp = (("GRO" == $txt_mo_d ) ? '' : "style=\"display:none;\"" );

if($cuserrema ==='B'){
    $str_style=" style=\"display:none;\"";
}
elseif($view != 1){
    $str_style='';
    $str_read_cost='readonly';
}
else{
    $str_style=''; 
}

?> 
<div class="table-responsive">
    <table id="tbl_PayData" class="table mb-0 table-striped table-hover table-bordered table-sm" style="font-size: 0.8rem !important;">  <!-- tbl_rcvng -->
        <thead>
            <th> </th>
            <th width="20px" class="text-center">
                <button id="btn_add" type="button" class="btn btn-info btn-sm" onclick="javascript:my_add_line_item();">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </th>
            <th >Itemcode</th>
            <th>Description</th>
            <th>PKG</th>
            <th <?=$str_style;?>>Unit Cost</th>
            <th <?=$str_style;?>>Total Cost</th>
            <th>SRP</th>
            <th>Total SRP</th>
            <th>DR Qty</th>
            <th>Actual Qty</th>
            <th>O/L/T</th>
            <th  <?=$exp_date_disp;?> >Expiration Date</th>
            <?php if(!empty($file )):?>
            <th>Claims</th>
            <?php endif ?>
        </thead>
        <tbody id="contentArea">
            <?php
            
			if(!empty($mmnhd_rid)) { 
				$str = "
				SELECT
				a.*,
				SHA2(CONCAT(a.`recid`,'{$mpw_tkn}'),384) mtkn_mndttr,
				SHA2(CONCAT(b.`recid`,'{$mpw_tkn}'),384) mtkn_artmtr,
				b.`ART_CODE`,
				b.`ART_DESC`,
				b.`ART_SKU`,
				b.`ART_UCOST`,
				b.`ART_UPRICE`
				FROM
				{$mydbname->medb(0)}.`trx_manrecs_dt` a
				JOIN 
				{$mydbname->medb(0)}.`mst_article` b
				ON
				a.`mat_rid` = b.`recid`
				WHERE
				sha2(concat(a.`mrhd_rid`,'{$mpw_tkn}'),384) = '{$mmnhd_rid}'
				ORDER BY 
				a.`recid`
				";

				$qdt = $mylibzdb->myoa_sql_exec($str, 'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__ . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
				$rlist = $qdt->getResultArray() ;
				$qdt->freeResult(); 
			}
			if($rlist !== ''):
				foreach($rlist as $rdt) { 
					$nmnrecs++;
					$txtstore_mem = $rdt['SM_Tag'];
					$del = $rdt['mtkn_mndttr'];
					$str_onclick = "javascript:__mn_items_drecs('$del','$mmnhd_rid');";
					$str_onchange ="<input type=\"hidden\" id=\"me_tag".$nmnrecs."\" value=\"<?=$nmnrecs;?>\"/>";
					if(empty($del)) {
						$str_onclick ="javascript:confirmalert(this);";
						$str_onchange =" <input type=\"hidden\" id=\"me_tag".$nmnrecs."\" value=\"Y\"/>";
					}
					$mborder = ($rdt['qty_claim'] != '' )?'border border-danger':'';
                ?>
            <tr class="rcvng">
            <td><?=$nmnrecs;?></td>
            <td>
            <button class="btn btn-sm mebtn_itmremove bg-red" data-melinetag="<?=$nmnrecs;?>" type="button" <?=$dis3;?>>
            <i class="bi bi-trash"></i>
            </button>
            <input type="hidden" id="mitemrid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_artmtr'];?>"/> 
            <input type="hidden" id="mid_<?=$nmnrecs;?>" value="<?=$rdt['mtkn_mndttr'];?>"/>
            <input type="hidden" id="__me_uid" value="<?=$nmnrecs;?>"/>
            <?=$str_onchange;?>
            </td>
            <td><input type="text" id="fld_mitemcode_<?=$nmnrecs;?>" size="15" class="mitemcode font-weight-bold" value="<?=$rdt['ART_CODE'];?>" onchange="javascript:__my_item_onchange(this);"/></td> <!--itemcode-->
            <td><input type="text" id="fld_mitemdesc_<?=$nmnrecs;?>" size="45" value="<?=$rdt['ART_DESC'];?>" readonly /></td> <!--item desc-->
            <td><input type="text" id="fld_mitempkg_<?=$nmnrecs;?>" size="5" value="<?=$rdt['ART_SKU'];?>" readonly /></td> <!--packaging-->
            <td <?=$str_style;?>><input type="text" id="fld_ucost_<?=$nmnrecs;?>" size="15" value="<?=number_format($rdt['ucost'],5,'.',',');?>" class="text-end font-weight-bold" onkeypress="return __meNumbersOnly(event)" onchange="javascript:__my_item_onchange(this);"/></td> <!--ucost-->
            <td <?=$str_style;?>><input type="text" class="text-end" id="fld_mitemtcost_<?=$nmnrecs;?>" size="15"  readonly /></td> <!--tcost-->
            <td><input type="text" id="fld_srp_<?=$nmnrecs;?>" size="15" value="<?=number_format($rdt['uprice'],2,'.',',');?>" class="font-weight-bold text-end" onchange="javascript:__my_item_onchange(this);" onkeypress="return __meNumbersOnly(event)" <?=$str_read;?> /></td> <!--srp-->
            <td><input type="text" id="fld_mitemtamt_<?=$nmnrecs;?>" class="text-end" size="15"  readonly /></td> <!--tamtSRP-->
            <td><input type="text" id="fld_mitemqty_<?=$nmnrecs;?>" size="15" value="<?=$rdt['qty'];?>" class="text-end font-weight-bold" onchange="javascript:__my_item_onchange(this);" onkeypress="return __meNumbersOnly(event)" onmouseover="javascript:__mysys_apps.dr_show_totals();;" onmouseout="javascript:__mysys_apps.dr_show_totals();;" onclick="javascript:__mysys_apps.dr_show_totals();;" onblur="javascript:__mysys_apps.dr_show_totals();;" readonly/></td> <!--qty rcvd-->
            <td><input type="text" id="fld_mitemqtycorr_<?=$nmnrecs;?>" size="15" value="<?=$rdt['qty_corrected'];?>" class="text-end font-weight-bold" onchange="javascript:__my_item_onchange(this);" onkeypress="return __meNumbersOnly(event)" onmouseover="javascript:__mysys_apps.dr_show_totals();;" onmouseout="javascript:__mysys_apps.dr_show_totals();;" onclick="javascript:__mysys_apps.dr_show_totals();;" onblur="javascript:__mysys_apps.dr_show_totals();;" <?=$str_read;?> /></td> <!--qty corrected-->
            <td><input type="text" id="fld_mitemolt_<?=$nmnrecs;?>" size="10" readonly required/></td> <!--OLT-->
             <td <?=$exp_date_disp;?> ><input type="text" id="fld_mitemexpdte_<?=$nmnrecs;?>" size="10" value="<?=(empty($rdt['exp_date']) ? '' : $mylibzsys->mydate_mmddyyyy($rdt['exp_date']));?>"  onchange="javascript:__my_item_onchange(this);" class="form_datetime exp_date" required/></td> <!--OLT-->
          <?php if(!empty($file )):?>
           <td><input type="text" class="text-end font-weight-bold <?=$mborder?>" size="5" id="fld_claimsqty_<?=$nmnrecs;?>" onchange="javascript:__my_item_onchange(this);" onkeypress="return __meNumbersOnly(event)" onmouseover="javascript:__mysys_apps.dr_show_totals();;" onmouseout="javascript:__mysys_apps.dr_show_totals();;" onclick="javascript:__mysys_apps.dr_show_totals();;" onblur="javascript:__mysys_apps.dr_show_totals();;" value = '<?=$rdt['qty_claim'];?>'></td>
          <?php endif; ?>

            </tr>
			<?php 
				} //end foreach 
            endif;
            if ($__nores != '') {
            ?>
            <tr class="rcvng">
            <td colspan="12"><?=$__nores;?></td>
            </tr>
            <?php 
            }

?>
<tr style="display:none;" class="rcvng">
    <td></td>
    <td>
        <button type="button" class="btn btn-sm btn-danger mebtn_itmremove" data-melinetag="" onclick="javascript:meitm_remove(this);">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <input type="hidden" value=""/>
        <input type="hidden" value=""/>
        <input type="hidden" value=""/>
        <input type="hidden" value="Y"/>
    </td>
    <td><input type="text" size="15" class="mitemcode font-weight-bold" value="" /></td> <!--itemcode-->
    <td><input type="text" size="45" value="" readonly /></td> <!--item desc-->
    <td><input type="text" size="5" value="" readonly /></td> <!--packaging-->
    <td <?=$str_style;?>><input type="text" size="15" value="" class="text-end font-weight-bold" onkeypress="return __meNumbersOnly(event)" /></td> <!--ucost-->
    <td <?=$str_style;?>><input type="text" size="15" value="" class="text-end" readonly /></td> <!--tcost-->
    <td><input type="text" size="15" value="" class="font-weight-bold" onkeypress="return __meNumbersOnly(event)" <?=$str_read;?> /></td> <!--srp-->
    <td><input type="text" size="15" value="" readonly /></td> <!--tamt-->
    <td><input type="text" size="15" value="" class="text-end font-weight-bold" onkeypress="return __meNumbersOnly(event)"    onmouseover="javascript:__mysys_apps.dr_show_totals();;" onmouseout="javascript:__mysys_apps.dr_show_totals();;" onclick="javascript:__mysys_apps.dr_show_totals();;" onblur="javascript:__mysys_apps.dr_show_totals();;" /></td> <!--dr qty-->
    <td><input type="text" size="15" value="" class="text-end font-weight-bold" onkeypress="return __meNumbersOnly(event)" onmouseover="javascript:__mysys_apps.dr_show_totals();;" onmouseout="javascript:__mysys_apps.dr_show_totals();;" onclick="javascript:__mysys_apps.dr_show_totals();;" onblur="javascript:my_add_line_item();" /></td><!--actual qty-->
    <td><input type="text" size="10"  readonly /></td> <!--OLT-->
    <td <?=$exp_date_disp;?> ><input type="text" size="10"  class="form_datetime exp_date" onmouseover="javascript:date_time_get();" required/></td> <!--OLT-->
    <?php if(!empty($file )):?>
    <td><input type="text" class="text-end font-weight-bold" size="5" onkeypress="return __meNumbersOnly(event)" onmouseover="javascript:__mysys_apps.dr_show_totals();;" onmouseout="javascript:__mysys_apps.dr_show_totals();;" onclick="javascript:__mysys_apps.dr_show_totals();;" onblur="javascript:my_add_line_item();"></td>
    <?php endif; ?>
</tr>                
</tbody>
</table>
</div>
<!-- end table-responsive -->
<script type="text/javascript">
   __mysys_apps.dr_show_totals();
</script>

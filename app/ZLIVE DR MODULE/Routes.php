<?php
use CodeIgniter\Router\RouteCollection;

$routes->get('mylogin', 'Mylogin::index');
$routes->add('mylogin-auth', 'Mylogin::auth');
$routes->get('melogout', 'Mylogin::logout');

$routes->get('mysecretme', 'MyTestMe::index');
$routes->get('mysecret-testlangito', 'MyTestMe::testlangito');
$routes->post('mdata-pos-mtkn', 'MyDataPOS::get_token');
$routes->get('mdata-pos-proc', 'MyDataPOS::mdata_pos_dload');

$routes->get('master-data-artm-dload', 'Md_article::md_dload',['filter' => 'myauthuser']);
$routes->post('master-data-artm-dload-proc', 'Md_article::md_dload_proc',['filter' => 'myauthuser']);

$routes->post('mywg-delvreg', 'MyWidgets::delvreg');
$routes->post('mywg-rcvdinfpout', 'MyWidgets::rcvdinfpout');
$routes->post('mywg-pouttob', 'MyWidgets::pouttob');
$routes->post('mywg-pouts', 'MyWidgets::pouts');

$routes->get('/', 'Home::index',['filter' => 'myauthuser']);
$routes->get('edi-filein', 'Home::meedi_filein');
$routes->get('search-mat-article-vend', 'MySearchData::mat_article_vend');
$routes->get('search-company', 'MySearchData::company_search_v');
$routes->get('search-area-company', 'MySearchData::area_company');
$routes->get('search-vendor', 'MySearchData::vendor_ua');
$routes->get('search-rcv-frm-brnch-pullout', 'MySearchData::companybranch_pout');
$routes->get('search-mat-article-vend', 'MySearchData::mat_article_vend');
$routes->post('search-pick-from-trx', 'MySearchData::select_mo_items_rcv');
$routes->post('search-check-dr', 'MySearchData::drin_dr_checking');
$routes->get('search-mat-article', 'MySearchData::mat_article');

$routes->get('mymd-item-materials', 'Md_article::index');
$routes->post('search-mymd-item-materials', 'Md_article::recs');
$routes->post('mymd-item-materials-profile', 'Md_article::profile');
$routes->get('mymd-article-master-poslink', 'Md_article::POSLink',['filter' => 'myauthuser']);
$routes->post('mymd-article-master-poslink-branch', 'Md_article::POSLink_branch',['filter' => 'myauthuser']);
$routes->post('mymd-article-master-poslink-branch-recs', 'Md_article::POSLink_branch_recs',['filter' => 'myauthuser']);
$routes->post('mymd-article-master-poslink-download', 'Md_article::POSLink_branch_download',['filter' => 'myauthuser']);

$routes->get('dr-trx', 'MyDRTrx::dr_trx');
$routes->post('dr-trx-save', 'MyDRTrx::dr_trx_save');
$routes->post('dr-trx-rcv-recs', 'MyDRTrx::rcvrec_vw');
$routes->post('dr-trx-rcv-claims-save', 'MyDRTrx::dr_claims_save');
$routes->post('dr-trx-rcv-claims-verify', 'MyDRTrx::dr_claims_verify');
$routes->post('dr-trx-rcv-claims-review', 'MyDRTrx::dr_claims_review');

$routes->get('pullout-trx', 'MyPullOutTrx::index');
$routes->post('pullout-trx-save', 'MyPullOutTrx::man_recs_po_sv');
$routes->post('pullout-trx-del-line-item', 'MyPullOutTrx::pout_del_line_item');
$routes->post('search-pullout-trx', 'MyPullOutTrx::poutrec_vw');
$routes->post('pullout-trx-req-inq', 'MyPullOutTrx::pout_req_inq');
$routes->get('mytrx_acct/rpts_print', 'MyPullOutTrx::printing');


$routes->get('mymd-customer', 'Md_customer::index');
$routes->post('search-mymd-customer', 'Md_customer::recs');
$routes->post('mymd-customer-profile', 'Md_customer::profile');
$routes->post('mymd-customer-profile-save', 'Md_customer::profile_save');

$routes->get('mymd-supplier', 'Md_supplier::index');
$routes->post('search-mymd-supplier', 'Md_supplier::recs');
$routes->post('mymd-supplier-profile', 'Md_supplier::profile');
$routes->post('mymd-supplier-profile-save', 'Md_supplier::profile_save');

$routes->get('mymd-quota-rate', 'Md_quotarate::index');
$routes->post('search-mymd-quota-rate', 'Md_quotarate::recs');
$routes->post('mymd-qpr-profile', 'Md_qpr_employees::profile');
$routes->post('mymd-qpr-employees-save', 'Md_qpr_employees::profile_save');
$routes->get('mymd-qpr-employees', 'Md_qpr_employees::index');
$routes->post('search-mymd-qpr-employees', 'Md_qpr_employees::recs');

$routes->get('inventory-dr-in', 'MyInventory::dr_in');
$routes->get('trx-jo-quota', 'Mytrx::jo_quota');
$routes->get('trx-jo-delv-in', 'Mytrx::trx_jo_delv_in');
$routes->post('trx-jo-delv-in-sv', 'Mytrx::trx_jo_delv_in_sv');
$routes->get('search-customer', 'MySearchData::search_customer');
$routes->get('search-proc-quota-rate', 'MySearchData::proc_quota_rate');
$routes->get('search-prod-items', 'MySearchData::prod_items');
$routes->get('search-prod-items-uom', 'MySearchData::prod_items_uom');
$routes->get('search-prod-items-packaging', 'MySearchData::prod_items_packaging');
$routes->get('search-prod-type', 'MySearchData::prod_type');
$routes->get('search-prod-category', 'MySearchData::prod_category');
$routes->get('search-prod-sub-category', 'MySearchData::prod_sub_category');
$routes->get('search-mymd-qpr-prod-services', 'MySearchData::qpr_prod_services');
$routes->get('search-mymd-qpr-prod-operation', 'MySearchData::qpr_prod_operation');
$routes->get('search-mymd-qpr-prod-design-pattern', 'MySearchData::qpr_prod_design_pattern');
$routes->get('search-mymd-qpr-prod-sub-operation', 'MySearchData::qpr_prod_sub_operation');
$routes->get('search-mymd-qpr-prod-processes', 'MySearchData::qpr_prod_processes');

$routes->get('search-mat-article-ho', 'MySearchData::ho_mat_article',['filter' => 'myauthuser']);

//Promo Discount Routes

$routes->get('me-promo', 'Promo_discount::index',['filter' => 'myauthuser']);
$routes->get('me-promo-vw', 'Promo_discount::index',['filter' => 'myauthuser']);
$routes->add('me-promo-save', 'Promo_discount::promo_save',['filter' => 'myauthuser']);
$routes->add('me-promo-recs', 'Promo_discount::promo_recs',['filter' => 'myauthuser']);
$routes->add('me-promo-view', 'Promo_discount::promo_vw',['filter' => 'myauthuser']);
$routes->add('me-promo-print', 'Promo_discount::promo_print');
$routes->add('me-promo-appr', 'Promo_discount::promo_recs_appr',['filter' => 'myauthuser']);
$routes->add('me-promo-view-appr', 'Promo_discount::promo_vw_appr',['filter' => 'myauthuser']);
$routes->add('me-promo-appr-save', 'Promo_discount::promo_save_appr',['filter' => 'myauthuser']);
$routes->add('me-promo-barcode-dl', 'Promo_discount::promo_barcode_dl_proc');
$routes->get('get-promo-itemc','Promo_discount::mat_article');
$routes->get('get-branch-list','Promo_discount::companybranch_v');

$routes->post('mypromo-fpdp-pos-update','Promo_discount::fpdp_pos_update',['filter' => 'myauthuser']);
$routes->post('mypromo-fpdp-pos-update-post-data','Promo_discount::fpdp_pos_update_post_data',['filter' => 'myauthuser']);
$routes->post('mypromo-fpdp-proc-approval', 'Mypromo_spromo::fpdp_proc_approval',['filter' => 'myauthuser']);



$routes->get('mypromo-fppd', 'Mypromo_fppd::index',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-save', 'Mypromo_fppd::promo_save',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-recs', 'Mypromo_fppd::promo_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-view', 'Mypromo_fppd::promo_vw',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-appr', 'Mypromo_fppd::promo_recs_appr',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-view-appr', 'Mypromo_fppd::promo_vw_appr',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-appr-save', 'Mypromo_fppd::promo_save_appr',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-barcode-dl', 'Mypromo_fppd::promo_barcode_dl_proc');
$routes->get('get-promo-itemc','Mypromo_fppd::mat_article');
$routes->get('get-branch-list','Mypromo_fppd::companybranch_v');
$routes->post('mypromo-fppd-pos-update','Mypromo_fppd::fppd_pos_update',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-pos-update-post-data','Mypromo_fppd::fppd_pos_update_post_data',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-upload','Mypromo_fppd::fppd_upload',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-post-upload','Mypromo_fppd::fppd_post_upload',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-validate','Mypromo_fppd::fppd_validate',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-upld-recs', 'Mypromo_fppd::fppd_upld_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-fpdp-dload', 'Mypromo_fppd::fppd_dload',['filter' => 'myauthuser']);
$routes->post('mypromo-fppd-cancel', 'Mypromo_fppd::fppd_cancel');
$routes->post('mypromo-fppd-cancelled-hist', 'Mypromo_fppd::fppd_cancelled_hist');
$routes->post('mypromo-fppd-cancelled-hist-recs', 'Mypromo_fppd::fppd_cancelled_hist_recs');
$routes->add('mypromo-fppd-print', 'Mypromo_fppd::promo_print');
$routes->post('mypromo-fpdp-proc-approval', 'Mypromo_spromo::fpdp_proc_approval',['filter' => 'myauthuser']);

$routes->post('mypromo-fppd-cancel-details', 'Mypromo_fppd::fpdp_proc_cancel_details');

//Promo buy1take1 Routes

$routes->get('me-buy1take1','Promo_buy1take1::index');
$routes->add('me-buy1take1-save', 'Promo_buy1take1::buy1take1_save');
$routes->add('me-buy1take1-view', 'Promo_buy1take1::buy1take1_vw');
$routes->add('me-buy1take1-recs', 'Promo_buy1take1::buy1take1_recs');
$routes->add('me-buy1take1-view-appr', 'Promo_buy1take1::buy1take1_vw_appr');
$routes->add('me-buy1take1-appr', 'Promo_buy1take1::buy1take1_recs_appr');
$routes->add('me-buy1take1-appr-save', 'Promo_buy1take1::buy1take1_save_appr');
$routes->add('me-buy1take1-barcode-dl', 'Promo_buy1take1::buy1take1_dl_proc');
$routes->get('me-buy1take1-vw', 'Promo_buy1take1::index');

$routes->get('mypromo-buy1take1','Mypromo_buy1take1::index');
$routes->post('mypromo-buy1take1-save', 'Mypromo_buy1take1::buy1take1_save');
$routes->post('mypromo-buy1take1-view', 'Mypromo_buy1take1::buy1take1_view');
$routes->post('mypromo-buy1take1-recs', 'Mypromo_buy1take1::buy1take1_recs');
$routes->post('mypromo-buy1take1-view-appr', 'Mypromo_buy1take1::buy1take1_view_appr');
$routes->post('mypromo-buy1take1-appr', 'Mypromo_buy1take1::buy1take1_recs_appr');
$routes->post('mypromo-buy1take1-appr-save', 'Mypromo_buy1take1::buy1take1_save_appr');
$routes->post('mypromo-buy1take1-pos-update','Mypromo_buy1take1::buy1take1_pos_update',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-pos-update-post-data','Mypromo_buy1take1::buy1take1_pos_update_post_data',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-update', 'Mypromo_buy1take1::buy1take1_update');
$routes->post('mypromo-buy1take1-upload','Mypromo_buy1take1::buy1take1_upload',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-post-upload','Mypromo_buy1take1::buy1take1_post_upload',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-upld-recs', 'Mypromo_buy1take1::buy1take1_upld_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-validate', 'Mypromo_buy1take1::buy1take1_validate',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-cancel', 'Mypromo_buy1take1::buy1take1_cancel',['filter' => 'myauthuser']);
$routes->post('mypromo-buy1take1-cancelled-hist', 'Mypromo_buy1take1::buy1take1_cancelled_hist');
$routes->post('mypromo-buy1take1-cancelled-hist-recs', 'Mypromo_buy1take1::buy1take1_cancelled_hist_recs');

//Promotion Buy Any at Price me-buyanyatprice

$routes->get('me-buyanyatprice', 'Promo_buyanyatprice::index');
$routes->add('me-buyanyatprice-save', 'Promo_buyanyatprice::buyanyatprice_save');
$routes->add('me-buyanyatprice-view', 'Promo_buyanyatprice::buyanyatprice_vw');
$routes->add('me-buyanyatprice-recs', 'Promo_buyanyatprice::buyanyatprice_recs');
$routes->add('me-buyanyatprice-view-appr', 'Promo_buyanyatprice::buyanyatprice_vw_appr');
$routes->add('me-buyanyatprice-appr', 'Promo_buyanyatprice::buyanyatprice_recs_appr');
$routes->add('me-buyanyatprice-appr-save', 'Promo_buyanyatprice::buyanyatprice_save_appr');
$routes->add('me-buyanyatprice-barcode-dl', 'Promo_buyanyatprice::buyanyatprice_dl_proc');

$routes->get('mypromo-buyanyatprice', 'Mypromo_buyanyatprice::index');
$routes->post('mypromo-buyanyatprice-save', 'Mypromo_buyanyatprice::buyanyatprice_save');
$routes->post('mypromo-buyanyatprice-view', 'Mypromo_buyanyatprice::buyanyatprice_vw');
$routes->post('mypromo-buyanyatprice-recs', 'Mypromo_buyanyatprice::buyanyatprice_recs');
$routes->post('mypromo-buyanyatprice-view-appr', 'Mypromo_buyanyatprice::buyanyatprice_vw_appr');
$routes->post('mypromo-buyanyatprice-appr', 'Mypromo_buyanyatprice::buyanyatprice_recs_appr');
$routes->post('mypromo-buyanyatprice-appr-save', 'Mypromo_buyanyatprice::buyanyatprice_save_appr');
$routes->post('mypromo-buyanyatprice-barcode-dl', 'Mypromo_buyanyatprice::buyanyatprice_dl_proc');
$routes->post('mypromo-buyanyatprice-pos-update', 'Mypromo_buyanyatprice::buyanyatprice_pos_update');
$routes->post('mypromo-buyanyatprice-pos-update-post-data', 'Mypromo_buyanyatprice::buyanyatprice_pos_update_post_data');
$routes->post('mypromo-buyanyatprice-upload', 'Mypromo_buyanyatprice::buyanyatprice_upload');
$routes->post('mypromo-buyanyatprice-post-upload', 'Mypromo_buyanyatprice::buyanyatprice_post_upload');
$routes->post('mypromo-buyanyatprice-upld-recs', 'Mypromo_buyanyatprice::buyanyatprice_upld_recs');
$routes->post('mypromo-buyanyatprice-validate', 'Mypromo_buyanyatprice::buyanyatprice_validate');
$routes->post('mypromo-buyanyatprice-cancel', 'Mypromo_buyanyatprice::buyanyatprice_cancel');
$routes->post('mypromo-buyanyatprice-cancelled-hist', 'Mypromo_buyanyatprice::buyanyatprice_cancelled_hist');
$routes->post('mypromo-buyanyatprice-cancelled-hist-recs', 'Mypromo_buyanyatprice::buyanyatprice_cancelled_hist_recs');

$routes->post('mypromo-buyanyatprice-cancel-details', 'Mypromo_buyanyatprice::buyanyatprice_cancel_details');

//Promo voucher routes

$routes->get('me-voucher', 'Promo_voucher::index');
$routes->get('me-voucher-vw', 'Promo_voucher::index');
$routes->add('me-voucher-save', 'Promo_voucher::voucher_save');
$routes->add('me-voucher-recs', 'Promo_voucher::voucher_recs');
$routes->add('me-voucher-view', 'Promo_voucher::voucher_vw');
$routes->add('me-voucher-print', 'Promo_voucher::voucher_print');
$routes->add('me-voucher-appr', 'Promo_voucher::voucher_recs_appr');
$routes->add('me-voucher-view-appr', 'Promo_voucher::voucher_vw_appr');
$routes->add('me-voucher-appr-save', 'Promo_voucher::voucher_save_appr');
$routes->add('me-voucher-barcode-dl', 'Promo_voucher::voucher_barcode_dl_proc');

$routes->get('mypromo-voucher', 'Mypromo_voucher::index');
$routes->post('mypromo-voucher-save', 'Mypromo_voucher::voucher_save');
$routes->post('mypromo-voucher-recs', 'Mypromo_voucher::voucher_recs');
$routes->post('mypromo-voucher-view', 'Mypromo_voucher::voucher_vw');
$routes->post('mypromo-voucher-print', 'Mypromo_voucher::voucher_print');
$routes->post('mypromo-voucher-appr', 'Mypromo_voucher::voucher_recs_appr');
$routes->post('mypromo-voucher-view-appr', 'Mypromo_voucher::voucher_vw_appr');
$routes->post('mypromo-voucher-appr-save', 'Mypromo_voucher::voucher_save_appr');
$routes->post('mypromo-voucher-barcode-dl', 'Mypromo_voucher::voucher_barcode_dl_proc');
$routes->post('mypromo-voucher-pos-update', 'Mypromo_voucher::voucher_pos_update');
$routes->post('mypromo-voucher-pos-update-post-data', 'Mypromo_voucher::voucher_pos_update_post_data');
$routes->post('mypromo-voucher-upload', 'Mypromo_voucher::voucher_upload');
$routes->post('mypromo-voucher-post-upload', 'Mypromo_voucher::voucher_post_upload');
$routes->post('mypromo-voucher-upld-recs', 'Mypromo_voucher::voucher_upld_recs');
$routes->post('mypromo-voucher-validate', 'Mypromo_voucher::voucher_validate');
$routes->post('mypromo-voucher-cancel', 'Mypromo_voucher::voucher_cancel');
$routes->post('mypromo-voucher-cancelled-hist', 'Mypromo_voucher::voucher_cancelled_hist');
$routes->post('mypromo-voucher-cancelled-hist-recs', 'Mypromo_voucher::voucher_cancelled_hist_recs');

$routes->post('mypromo-voucher-cancel-details', 'Mypromo_voucher::voucher_cancel_details');

//Promo threshhold routes

$routes->get('me-threshold', 'Promo_threshold::index');
$routes->get('me-threshold-vw', 'Promo_threshold::index');
$routes->add('me-threshold-save', 'Promo_threshold::threshold_save');
$routes->add('me-threshold-recs', 'Promo_threshold::threshold_recs');
$routes->add('me-threshold-view', 'Promo_threshold::threshold_vw');
$routes->add('me-threshold-print', 'Promo_threshold::threshold_print');
$routes->add('me-threshold-appr', 'Promo_threshold::threshold_recs_appr');
$routes->add('me-threshold-view-appr', 'Promo_threshold::threshold_vw_appr');
$routes->add('me-threshold-appr-save', 'Promo_threshold::threshold_save_appr');
$routes->add('me-threshold-barcode-dl', 'Promo_threshold::threshold_barcode_dl_proc');

$routes->get('mypromo-threshold', 'Mypromo_threshold::index');
$routes->post('mypromo-threshold-save', 'Mypromo_threshold::threshold_save');
$routes->post('mypromo-threshold-recs', 'Mypromo_threshold::threshold_recs');
$routes->post('mypromo-threshold-view', 'Mypromo_threshold::threshold_vw');
$routes->add('mypromo-threshold-print', 'Mypromo_threshold::threshold_print');
$routes->post('mypromo-threshold-appr', 'Mypromo_threshold::threshold_recs_appr');
$routes->post('mypromo-threshold-view-appr', 'Mypromo_threshold::threshold_vw_appr');
$routes->post('mypromo-threshold-appr-save', 'Mypromo_threshold::threshold_save_appr');
$routes->post('mypromo-threshold-barcode-dl', 'Mypromo_threshold::threshold_barcode_dl_proc');
$routes->post('mypromo-threshold-pos-update', 'Mypromo_threshold::threshold_pos_update');
$routes->post('mypromo-threshold-pos-update-post-data', 'Mypromo_threshold::threshold_pos_update_post_data');
$routes->post('mypromo-threshold-upload', 'Mypromo_threshold::threshold_upload');
$routes->post('mypromo-threshold-post-upload', 'Mypromo_threshold::threshold_post_upload');
$routes->post('mypromo-threshold-upld-recs', 'Mypromo_threshold::threshold_upld_recs');
$routes->post('mypromo-threshold-validate', 'Mypromo_threshold::threshold_validate');
$routes->post('mypromo-threshold-cancel', 'Mypromo_threshold::threshold_cancel');
$routes->post('mypromo-threshold-cancelled-hist', 'Mypromo_threshold::threshold_cancelled_hist');
$routes->post('mypromo-threshold-cancelled-hist-recs', 'Mypromo_threshold::threshold_cancelled_hist_recs');

$routes->post('mypromo-threshold-cancel-details', 'Mypromo_threshold::threshold_cancel_details');

//Promo wholesale routes

$routes->get('me-wholesale', 'Promo_wholesale::index');
$routes->get('me-wholesale-vw', 'Promo_wholesale::index');
$routes->add('me-wholesale-save', 'Promo_wholesale::wholesale_save');
$routes->add('me-wholesale-recs', 'Promo_wholesale::wholesale_recs');
$routes->add('me-wholesale-view', 'Promo_wholesale::wholesale_vw');
$routes->add('me-wholesale-print', 'Promo_wholesale::wholesale_print');
$routes->add('me-wholesale-appr', 'Promo_wholesale::wholesale_recs_appr');
$routes->add('me-wholesale-view-appr', 'Promo_wholesale::wholesale_vw_appr');
$routes->add('me-wholesale-appr-save', 'Promo_wholesale::wholesale_save_appr');
$routes->add('me-wholesale-barcode-dl', 'Promo_wholesale::wholesale_barcode_dl_proc');


$routes->get('mypromo-wholesale', 'Mypromo_wholesale::index');
$routes->post('mypromo-wholesale-save', 'Mypromo_wholesale::wholesale_save');
$routes->post('mypromo-wholesale-recs', 'Mypromo_wholesale::wholesale_recs');
$routes->post('mypromo-wholesale-view', 'Mypromo_wholesale::wholesale_vw');
$routes->post('mypromo-wholesale-print', 'Mypromo_wholesale::wholesale_print');
$routes->post('mypromo-wholesale-appr', 'Mypromo_wholesale::wholesale_recs_appr');
$routes->post('mypromo-wholesale-view-appr', 'Mypromo_wholesale::wholesale_vw_appr');
$routes->post('mypromo-wholesale-appr-save', 'Mypromo_wholesale::wholesale_save_appr');
$routes->post('mypromo-wholesale-barcode-dl', 'Mypromo_wholesale::wholesale_barcode_dl_proc');
$routes->post('mypromo-wholesale-pos-update', 'Mypromo_wholesale::wholesale_pos_update');
$routes->post('mypromo-wholesale-pos-update-post-data', 'Mypromo_wholesale::wholesale_pos_update_post_data');
$routes->post('mypromo-wholesale-upload', 'Mypromo_wholesale::wholesale_upload');
$routes->post('mypromo-wholesale-post-upload', 'Mypromo_wholesale::wholesale_post_upload');
$routes->post('mypromo-wholesale-upld-recs', 'Mypromo_wholesale::wholesale_upld_recs');
$routes->post('mypromo-wholesale-validate', 'Mypromo_wholesale::wholesale_validate');
$routes->post('mypromo-wholesale-cancel', 'Mypromo_wholesale::wholesale_cancel');
$routes->post('mypromo-wholesale-cancelled-hist', 'Mypromo_wholesale::wholesale_cancelled_hist');
$routes->post('mypromo-wholesale-cancelled-hist-recs', 'Mypromo_wholesale::wholesale_cancelled_hist_recs');

$routes->post('mypromo-wholesale-cancel-details', 'Mypromo_wholesale::wholesale_cancel_details');


//Pos Tally Upload
$routes->get('mybranch-postally-upload', 'Mybranch_postallyUpload::index',['filter' => 'myauthuser']);
$routes->post('mybranch-postally-upload-view', 'Mybranch_postallyUpload::upload_view',['filter' => 'myauthuser']);
$routes->post('mybranch-postally-upload-search-recs', 'Mybranch_postallyUpload::upload_search_recs',['filter' => 'myauthuser']);
$routes->post('mybranch-postally-upload-post', 'Mybranch_postallyUpload::upload_post',['filter' => 'myauthuser']);
$routes->post('mybranch-postally-upload-view-file', 'Mybranch_postallyUpload::upload_view_file',['filter' => 'myauthuser']);
$routes->post('mybranch-postally-upload-delete-file', 'Mybranch_postallyUpload::delete_file',['filter' => 'myauthuser']);



//promotion spromo/qdamage
$routes->get('mypromo-spqd', 'Mypromo_spromoqdamage::index');
$routes->post('me-spqd-save', 'Mypromo_spromoqdamage::save_spqd');
$routes->get('promo_search', 'Mypromo_spromoqdamage::spqd_promo_search');
$routes->post('me-spqd-view', 'Mypromo_spromoqdamage::spqd_vw');
$routes->add('me-spqd-recs', 'Mypromo_spromoqdamage::spqd_recs');
$routes->get('mepromo-spqd-view', 'Mypromo_spromoqdamage::index');
$routes->add('me-spqd-view-appr', 'Mypromo_spromoqdamage::spqd_vw_appr');
$routes->add('me-spqd-dashboard', 'Mypromo_spromoqdamage::spqd_vw_dashboard');
$routes->add('me-spqd-appr', 'Mypromo_spromoqdamage::spqd_recs_appr');
$routes->add('me-spqd-appr-save', 'Mypromo_spromoqdamage::spqd_save_appr');
$routes->add('me-spqd-dashboard-view', 'Mypromo_spromoqdamage::dashboard_recs');
$routes->add('me-spqd-barcode-dl', 'Mypromo_spromoqdamage::spqd_dl_proc');

$routes->get('mypromo-spromo', 'Mypromo_spromo::index',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-codes', 'Mypromo_spromo::spromo_search',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-fromitems', 'Mypromo_spromo::fromspromo_items_search',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-save', 'Mypromo_spromo::save_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-recs', 'Mypromo_spromo::view_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-inactive-recs', 'Mypromo_spromo::view_inactive_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-rundown', 'Mypromo_spromo::spromo_rundown',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-rundown-regitm', 'Mypromo_spromo::spromo_rundown_regitm',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-runup-regitm', 'Mypromo_spromo::spromo_runup_regitm',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-codes-rundown', 'Mypromo_spromo::tospromo_item_rundown_search',['filter' => 'myauthuser']);
$routes->get('mypromo-spromo-codes-runup', 'Mypromo_spromo::tospromo_item_runup_search',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-rundown-save', 'Mypromo_spromo::spromo_rundown_save',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-rundown-save-regitem', 'Mypromo_spromo::spromo_rundown_save_regitem',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-runup-save-regitem', 'Mypromo_spromo::spromo_runup_save_regitem',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-ivty-reg-mapping', 'Mypromo_spromo::spromo_ivty_reg_mapping',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-pos-update', 'Mypromo_spromo::spromo_pos_update',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-pos-update-data', 'Mypromo_spromo::spromo_pos_update_data',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-approval', 'Mypromo_spromo::spromo_approval',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-proc-approval', 'Mypromo_spromo::spromo_proc_approval',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-proc-item-cancel', 'Mypromo_spromo::spromo_proc_item_cancel',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-proc-approve-item-cancel', 'Mypromo_spromo::spromo_proc_approve_item_cancel',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-item-cancel-hist', 'Mypromo_spromo::spromo_item_cancel_hist',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-cancel-hist', 'Mypromo_spromo::spromo_cancel_hist',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-proc-cancel', 'Mypromo_spromo::spromo_proc_cancel',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-upload', 'Mypromo_spromo::spromo_upload',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-post-upload', 'Mypromo_spromo::spromo_post_upload',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-upld-recs', 'Mypromo_spromo::spromo_upld_recs',['filter' => 'myauthuser']);
$routes->post('mypromo-spromo-validate', 'Mypromo_spromo::spromo_validate',['filter' => 'myauthuser']);
$routes->post('me-ivty-getdumpedlb', 'Mypromo_spromo::getDumpedLB',['filter' => 'myauthuser']);

//mypos related module
$routes->get('mypos-reprint-logs', 'MyPOSConn::reprint_logs');
$routes->post('mypos-reprint-recs-logs', 'MyPOSConn::reprint_recs_logs');
//mypos temporary routes
$routes->get('mypos-reprint', 'MyPOSConnx::index');

$routes->add('company-branch-ua', 'My_search::companybranch_v',['filter' => 'myauthuser']);
$routes->add('mat-article-ua', 'My_search::mat_article',['filter' => 'myauthuser']);
$routes->add('company-branch-tap-ua', 'My_search::companybranch_tap',['filter' => 'myauthuser']);
$routes->add('mat-art-section2', 'My_search::mat_art_section2',['filter' => 'myauthuser']);
$routes->add('mat-cg1', 'My_search::mat_cg1',['filter' => 'myauthuser']);
$routes->add('mat-cg2', 'My_search::mat_cg2',['filter' => 'myauthuser']);
$routes->add('mat-cg3', 'My_search::mat_cg3',['filter' => 'myauthuser']);
$routes->add('mat-cg4', 'My_search::mat_cg4',['filter' => 'myauthuser']);

//reports
//sales out daily tab 
$routes->get('sales-out-details', 'MyRpt_sales::sales-out-details',['filter' => 'myauthuser']);
$routes->post('sales-out-details-tab-daily', 'MyRpt_sales::sales-out-details-tab-daily',['filter' => 'myauthuser']);
$routes->post('sales-out-tally-daily', 'MyRpt_sales::sales-out-tally-daily',['filter' => 'myauthuser']);
$routes->post('sales-out-Acct-POS-tally', 'MyRpt_sales::sales-out-Acct-POS-tally',['filter' => 'myauthuser']);

//sales out daily tab generation
$routes->post('sales-out-details-tab-daily-proc', 'MyRpt_sales::sales_out_details_tab_daily_proc',['filter' => 'myauthuser']);
$routes->post('sales-out-details-tab-daily-perpos-proc', 'MyRpt_sales::sales_out_details_tab_daily_perpos_proc',['filter' => 'myauthuser']);
$routes->post('sales-out-details-tab-daily-perpos-download', 'MyRpt_sales::sales_out_details_tab_daily_perpos_download',['filter' => 'myauthuser']);
$routes->post('sales-out-details-tab-daily-rec', 'MyRpt_sales::sales_out_details_tab_daily_rec',['filter' => 'myauthuser']);
$routes->post('sales-out-details-daily-download', 'MyRpt_sales::sales_out_details_daily_download',['filter' => 'myauthuser']);
$routes->post('sales-out-tally-daily-proc', 'MyRpt_sales::sales-out-tally-daily-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-Acct-POS-tally-proc', 'MyRpt_sales::sales-out-Acct-POS-tally-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-Acct-POS-TAXR-proc', 'MyRpt_sales::sales-out-Acct-POS-TAXR-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-itemized-abranch-proc', 'MyRpt_sales::sales-out-itemized-abranch-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-recon-proc', 'MyRpt_sales::sales-out-recon-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-tally-daily-check-proc', 'MyRpt_sales::sales-out-tally-daily-check-proc',['filter' => 'myauthuser']);
$routes->post('sales-out-details-bom-download', 'MyRpt_sales::sales_out_details_bom_download',['filter' => 'myauthuser']);

$routes->get('myua', 'MyUser::user_access',['filter' => 'myauthuser']);
$routes->post('search-myuser', 'MyUser::user_rec',['filter' => 'myauthuser']);
$routes->post('myua-module-save', 'MyUser::user_module_access_save',['filter' => 'myauthuser']);

// reports 
$routes->get('myreport-inventory', 'MyRpt_inventory::index',['filter' => 'myauthuser']);
$routes->post('myreport-stockcard', 'MyRpt_inventory::stockcard',['filter' => 'myauthuser']);
$routes->post('myreport-stockcard-recs', 'MyRpt_inventory::stockcard_recs',['filter' => 'myauthuser']);

//ho version inventory report
$routes->get('myinventory-report', 'MyRpt_inventory::ho',['filter' => 'myauthuser']);
$routes->post('myinventory-report-detailed', 'MyRpt_inventory::ho_detailed',['filter' => 'myauthuser']);
$routes->post('myinventory-report-detailed-gen', 'MyRpt_inventory::ho_detailed_gen',['filter' => 'myauthuser']);
$routes->post('myinventory-report-item-del', 'MyRpt_inventory::ho_detailed_delete',['filter' => 'myauthuser']);
$routes->post('myinventory-report-summary', 'MyRpt_inventory::ho_summary',['filter' => 'myauthuser']);
$routes->get('myinventory-report-live-balance-dashboard', 'MyRpt_inventory::live_inventory_balance');
$routes->post('myreport-inventory-itemized', 'MyRpt_inventory::me_itemized',['filter' => 'myauthuser']);
$routes->post('myreport-inventory-itemized-proc', 'MyRpt_inventory::me_itemized_proc',['filter' => 'myauthuser']);
$routes->get('myreport-inventory-itemized-download', 'MyRpt_inventory::me_itemized_download',['filter' => 'myauthuser']);
$routes->post('myinventory-report-branch-conso', 'MyRpt_inventory::me_branch_conso',['filter' => 'myauthuser']);
$routes->post('myinventory-report-dl', 'MyRpt_inventory::ho_inv_report_dl',['filter' => 'myauthuser']);
$routes->get('myinventory-report-vendor', 'MyRpt_inventory::ho_inv_report_vendor',['filter' => 'myauthuser']);
$routes->post('myinventory-report-br-dl', 'MyRpt_inventory::ho_inv_report_br_dl',['filter' => 'myauthuser']);

$routes->get('mysales-deposit', 'MySalesDeposit::index',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-entry', 'MySalesDeposit::entry',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-save', 'MySalesDeposit::me_save',['filter' => 'myauthuser']);
$routes->get('mysales-deposit-get-group', 'MySalesDeposit::getdepositGroup',['filter' => 'myauthuser']);
$routes->get('mysales-deposit-get-Deposit-BranchAcct', 'MySalesDeposit::getDeposit_BrcnhAcct',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-recs', 'MySalesDeposit::deposit_recs_branch',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-dload-zip-files', 'MySalesDeposit::deposit_download_zip_file',['filter' => 'myauthuser']);
$routes->post('mysales-deposit-delrec', 'MySalesDeposit::me_delrec',['filter' => 'myauthuser']);


$routes->get('myinventory-cycle-count', 'MyInventory::cycle_count',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-proc-upld-files', 'MyInventory::cycle_count_proc_uploaded_files',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-posting-uploaded', 'MyInventory::cycle_count_posting_uploaded',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-posting-uploaded-recs', 'MyInventory::cycle_count_posting_uploaded_recs',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-post-uploaded', 'MyInventory::cycle_count_post_uploaded',['filter' => 'myauthuser']);
$routes->post('myinventory-cycle-count-editing-uploaded-inquiry', 'MyInventory::cycle_count_uploaded_editing',['filter' => 'myauthuser']);

$routes->post('myinventory-proc-balance', 'MyInventory::proc_balance',['filter' => 'myauthuser']);

$routes->get('myinventory-recon-adj', 'MyInventory::recon_adj',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-entry', 'MyInventory::recon_adj_entry',['filter' => 'myauthuser']);
$routes->get('myinventory-recon-adj-search-mat', 'MyInventory::recon_adj_search_mat',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-entry-sv', 'MyInventory::recon_adj_entry_sv',['filter' => 'myauthuser']);
$routes->post('myinventory-report-detailed-download', 'MyRpt_inventory::ho_detailed_download',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-recs', 'MyInventory::recon_adj_recs',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-delrec', 'MyInventory::recon_adj_delrec',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-post-rec', 'MyInventory::recon_adj_postrec',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-upld-dl-template', 'MyInventory::recon_adj_upld_dl_template',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-upld', 'MyInventory::recon_adj_upld',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-upld-proc', 'MyInventory::recon_adj_upld_proc',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-upld-recs', 'MyInventory::recon_adj_upld_recs',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-upld-recs-dload', 'MyInventory::recon_adj_upld_recs_dload',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-upld-proc-data', 'MyInventory::recon_adj_upld_proc_data',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-pdupld', 'MyInventory::recon_adj_pdupld',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-pdupld-recs', 'MyInventory::recon_adj_pdupld_recs',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-pdupld-save-item-detail', 'MyInventory::recon_adj_pdupld_save_item_detail',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-pdupld-save-header', 'MyInventory::recon_adj_pdupld_save_header',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-pdupld-textfile', 'MyInventory::recon_adj_pdupld_textfile',['filter' => 'myauthuser']);
$routes->post('myinventory-recon-adj-pdupld-docfile', 'MyInventory::recon_adj_pdupld_docfile',['filter' => 'myauthuser']);

//RCP-PCF
$routes->get('myrfp','MyRfp::index',['filter' => 'myauthuser']);
$routes->post('myrfp-entry', 'MyRfp::entry',['filter' => 'myauthuser']);
$routes->get('get-expense-type','MyRfp::rfpcf_expense_type',['filter' => 'myauthuser']);
$routes->get('get-branch-name','MyRfp::company_search',['filter' => 'myauthuser']);
$routes->post('myrfp-view', 'MyRfp::myrfp_view',['filter' => 'myauthuser']);
$routes->post('mypcf-view', 'MyRfp::mypcf_view',['filter' => 'myauthuser']);

//PCF-RFP
$routes->get('myexpenses-pcf-rfp','MyExpenses_pcf_rfp::index',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-entry','MyExpenses_pcf_rfp::pcf_rfp_entry',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-select','MyExpenses_pcf_rfp::pcf_rfp_select',['filter' => 'myauthuser']);
$routes->get('get-typ-expense','MyExpenses_pcf_rfp::get_typ_expense');
$routes->post('myexpenses-pcf-rfp-save','MyExpenses_pcf_rfp::pcf_rfp_save',['filter' => 'myauthuser']);
$routes->get('myexpenses-pcf-rfp-print','MyExpenses_pcf_rfp::pcf_rfp_rprint',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-details','MyExpenses_pcf_rfp::pcf_rfp_details',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-details-recs','MyExpenses_pcf_rfp::pcf_rfp_details_recs',['filter' => 'myauthuser']);
$routes->get('infobox-gettotal-dash','MyExpenses_pcf_rfp::infobox_gettotal_dash',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-approve','MyExpenses_pcf_rfp::pcf_rfp_approve',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-delete','MyExpenses_pcf_rfp::pcf_rfp_delete',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-approval','MyExpenses_pcf_rfp::pcf_rfp_approval',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-sent','MyExpenses_pcf_rfp::pcf_rfp_sent',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-disbursement-ent','MyExpenses_pcf_rfp::pcf_rfp_disbursement_ent',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-disbursement-cancel','MyExpenses_pcf_rfp::pcf_rfp_disbursement_cancel',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-disbursement-sv-perline','MyExpenses_pcf_rfp::pcf_rfp_disbursement_sv_perline',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-getFiles','MyExpenses_pcf_rfp::pcf_rfp_getFiles',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-dlFiles','MyExpenses_pcf_rfp::pcf_rfp_dlFiles',['filter' => 'myauthuser']);
$routes->post('myexpenses-pcf-rfp-br-details-recs','MyExpenses_pcf_rfp::pcf_rfp_br_details_recs',['filter' => 'myauthuser']);



//LBC Monitoring
$routes->get('mylbcfiles','MyLbcFiles::index',['filter' => 'myauthuser']);
$routes->post('mylbcfiles-entry','MyLbcFiles::create_entry',['filter' => 'myauthuser']);

//Sub Item Masterdata 
$routes->get('sub-item-masterdata','Md_subitems::index',['filter' => 'myauthuser']);
$routes->post('sub-items-recs','Md_subitems::sub_item_recs',['filter' => 'myauthuser']);
$routes->post('sub-items-save','Md_subitems::sub_item_save',['filter' => 'myauthuser']);
$routes->get('get-main-itemc','Md_subitems::get_main_itemc',['filter' => 'myauthuser']);
$routes->get('get-uom','Md_subitems::get_uom',['filter' => 'myauthuser']);
$routes->post('sub-items-update','Md_subitems::sub_item_update',['filter' => 'myauthuser']);
$routes->add('sub-items-recs-vw', 'Md_subitems::sub_item_recs_vw');

//CS Sub Item Convertion
$routes->get('cs-sub-item-convf','MdCs_subitems_convf::index',['filter' => 'myauthuser']);
$routes->post('cs-sub-convf-recs', 'MdCs_subitems_convf::sub_inv_recs_vw',['filter' => 'myauthuser']);
$routes->add('cs-sub-convf-recs-vw', 'MdCs_subitems_convf::sub_inv_recs_vw',['filter' => 'myauthuser']);
$routes->post('cs-sub-convf-cur-recs', 'MdCs_subitems_convf::sub_inv_recs_convf',['filter' => 'myauthuser']);
$routes->add('cs-sub-convf-cur-recs-vw', 'MdCs_subitems_convf::sub_inv_recs_vw_convf',['filter' => 'myauthuser']);
$routes->post('cs-sub-convf-save','MdCs_subitems_convf::sub_inv_save',['filter' => 'myauthuser']);
$routes->get('get-branch','MdCs_subitems_convf::get_branch',['filter' => 'myauthuser']);

//CS Sub Item BOM
$routes->get('cs-sub-item-bom','MdCs_subitems_bom::index',['filter' => 'myauthuser']);
$routes->post('cs-sub-items-bom-save','MdCs_subitems_bom::sub_item_bom_save',['filter' => 'myauthuser']);
$routes->post('cs-sub-items-bom-update','MdCs_subitems_bom::sub_item_bom_update',['filter' => 'myauthuser']);
$routes->post('cs-sub-items-bom-recs', 'MdCs_subitems_bom::sub_item_bom_recs',['filter' => 'myauthuser']);
$routes->add('cs-sub-items-bom-recs-vw', 'MdCs_subitems_bom::sub_item_bom_recs_vw',['filter' => 'myauthuser']);
$routes->get('get-sub-materials','MdCs_subitems_bom::get_sub_materials',['filter' => 'myauthuser']);
$routes->get('get-sub-itemc','MdCs_subitems_bom::get_sub_itemc',['filter' => 'myauthuser']);

//Sub item Convertion
$routes->get('sub-item-convf','Md_subitems_convf::index',['filter' => 'myauthuser']);
$routes->add('sub-items-convf-vw', 'Md_subitems_convf::sub_item_convf_vw');
$routes->post('sub-items-convf-save','Md_subitems_convf::sub_item_convf_save',['filter' => 'myauthuser']);

$routes->get('mysimul-getdumpedlb', 'MyTestSimul::test_getDumpedLB',['filter' => 'myauthuser']);
$routes->get('mysimul-geterrlogs', 'MyTestSimul::getlogsfromconsole');
$routes->get('mysimul-test1', 'MyTestSimul::test1',['filter' => 'myauthuser']);
$routes->post('mytest-recon-adj-upld-dl-template', 'MyTestSimul::mytest_recon_adj_upld_dl_template',['filter' => 'myauthuser']);
$routes->post('mytest-recon-adj-upld-proc', 'MyTestSimul::mytest_recon_adj_upld_proc',['filter' => 'myauthuser']);
$routes->post('mytest-recon-adj-upld-recs', 'MyTestSimul::recon_adj_upld_recs',['filter' => 'myauthuser']);
$routes->post('mytest-recon-adj-upld-recs-dload', 'MyTestSimul::recon_adj_upld_recs_dload',['filter' => 'myauthuser']);
//CS Sales Report
$routes->get('cs-sales-report','MdCs_sales_report::index',['filter' => 'myauthuser']);
$routes->post('cs-sales-report-recs', 'MdCs_sales_report::sales_report_recs',['filter' => 'myauthuser']);
$routes->add('cs-sales-report-recs-vw', 'MdCs_sales_report::sales_report_recs_vw',['filter' => 'myauthuser']);
$routes->post('cs-sales-report-dl', 'MdCs_sales_report::sales_report_dl',['filter' => 'myauthuser']);

//GRO Sub Item BOM
$routes->get('gro-sub-item-bom','MdGro_subitems_bom::index',['filter' => 'myauthuser']);
$routes->get('gro-sub-item-branch','MdGro_subitems_bom::gro_sub_item_branch',['filter' => 'myauthuser']);

//Accoonting Modules
$routes->get('accounting/mycv','MyAccounting::MyCV',['filter' => 'myauthuser']);

//DR Receiving Monitoring
$routes->get('mydashb-dr','MyDashboard_Inventory::index',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-rcvng','MyDashboard_Inventory::dashb_inv_recs',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-rcvng-dl','MyDashboard_Inventory::dashb_inv_recs_dl',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-rcvng-vw','MyDashboard_Inventory::dashb_inv_recs_vw',['filter' => 'myauthuser']);
$routes->get('mydashb-dr-getbranch','MyDashboard_Inventory::dashb_inv_getbranch',['filter' => 'myauthuser']);
$routes->get('mydashb-dr-getbrancharea','MyDashboard_Inventory::dashb_inv_getbrancharea',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-rcvng-process','MyDashboard_Inventory::dashb_inv_process',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-validate','MyDashboard_Inventory::dashb_inv_validate',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-validate-dl','MyDashboard_Inventory::dashb_inv_validate_dl',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-forcountered','MyDashboard_Inventory::dashb_inv_forcountered',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-created-recs','MyDashboard_Inventory::dashb_inv_created_recs',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-created-recs-vw','MyDashboard_Inventory::dashb_inv_created_recs_vw',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-created-dl','MyDashboard_Inventory::dashb_inv_created_dl',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-created-cancel','MyDashboard_Inventory::dashb_inv_created_cancel',['filter' => 'myauthuser']);
$routes->get('mydashb-dr-getbranchuser','MyDashboard_Inventory::dashb_inv_getbranchuser',['filter' => 'myauthuser']);
$routes->get('mydashb-dr-daily-dl','MyDashboard_Inventory::dashb_inv_daily_dl',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-recs','MyDashboard_Inventory::dashb_inv_intransit_recs',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-recs-vw','MyDashboard_Inventory::dashb_inv_intransit_recs_vw',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-dl','MyDashboard_Inventory::dashb_inv_intransit_dl',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-rcv','MyDashboard_Inventory::dashb_inv_intransit_rcv',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-cwo-upld','MyDashboard_Inventory::dashb_inv_intransit_cwo_upld',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-mn-upld','MyDashboard_Inventory::dashb_inv_intransit_mn_upld',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-tap-upld','MyDashboard_Inventory::dashb_inv_intransit_tap_upld',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-intransit-old-upld','MyDashboard_Inventory::dashb_inv_intransit_old_upld',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-cancelled-recs','MyDashboard_Inventory::dashb_inv_cancelled_recs',['filter' => 'myauthuser']);
$routes->post('mydashb-dr-cancelled-recs-vw','MyDashboard_Inventory::dashb_inv_cancelled_recs_vw',['filter' => 'myauthuser']);


// LBC Monitoring
$routes->get('lbc-monitoring', 'Mylbc_Monitoring::index',['filter' => 'myauthuser']);
$routes->post('rcv', 'Mylbc_Monitoring::test_view',['filter' => 'myauthuser']);
$routes->post('lbc-ent-save', 'Mylbc_Monitoring::lbc_ent_save',['filter' => 'myauthuser']);
$routes->post('vw-trns-lbcrecs', 'Mylbc_Monitoring::vw_trns_lbcrecs',['filter' => 'myauthuser']);
$routes->post('lbc-search-recs', 'Mylbc_Monitoring::lbc_search_recs',['filter' => 'myauthuser']);
$routes->post('lbc-del-rec', 'Mylbc_Monitoring::lbc_del_rec',['filter' => 'myauthuser']);
$routes->post('vw-rcv-lbcrecs', 'Mylbc_Monitoring::vw_lbcfiles_rcv_recs',['filter' => 'myauthuser']);
$routes->post('lbc-search-recs-rcv', 'Mylbc_Monitoring::lbc_search_recs_rcv',['filter' => 'myauthuser']);
$routes->post('lbc-rcvent-save', 'Mylbc_Monitoring::lbc_rcvent_save',['filter' => 'myauthuser']);
$routes->post('lbc-sntfile-dload-zip-files', 'Mylbc_Monitoring::lbc_sntfile_download_zip_file',['filter' => 'myauthuser']);
$routes->post('mylbc-gen-rpt', 'Mylbc_Monitoring::mylbc_gen_rpt',['filter' => 'myauthuser']);
$routes->get('infobox-gettotal-dash2', 'Mylbc_Monitoring::infobox_gettotal_dash',['filter' => 'myauthuser']);
$routes->post('del-dt-rec', 'Mylbc_Monitoring::del_dt_rec',['filter' => 'myauthuser']);
$routes->get('get-file-type','Mylbc_Monitoring::file_article');
$routes->post('lbc-filetype-save', 'Mylbc_Monitoring::mylbc_filetype_sv',['filter' => 'myauthuser']);
$routes->post('lbc-filetype-del', 'Mylbc_Monitoring::mylbc_filetype_del',['filter' => 'myauthuser']);
$routes->post('lbcfiles-concerns-vw', 'Mylbc_Monitoring::mylbcfiles_concerns_vw',['filter' => 'myauthuser']);
// lbc search
$routes->add('company-search', 'My_search::company_search_v2',['filter' => 'myauthuser']);
$routes->add('company-branch-v2', 'My_search::company_branch_v2',['filter' => 'myauthuser']);
$routes->get('mst_filedesc', 'My_search::mst_filedesc',['filter' => 'myauthuser']);

//Intransit tap tagging
$routes->get('instransit','MyIntransit::index',['filter' => 'myauthuser']);
$routes->post('instransit-tap-recs','MyIntransit::intransit_tap_recs',['filter' => 'myauthuser']);
$routes->post('instransit-tap-recs-vw','MyIntransit::intransit_tap_recs_vw',['filter' => 'myauthuser']);
$routes->post('instransit-tap-sv','MyIntransit::intransit_tap_sv',['filter' => 'myauthuser']);
$routes->get('instransit-brnch','MyIntransit::intransit_brnch',['filter' => 'myauthuser']);

// POS Creation Account
$routes->get('pos-creation','MyPosCreation::index',['filter' => 'myauthuser']);
$routes->post('pos-creation-sv','MyPosCreation::pos_create_sv',['filter' => 'myauthuser']);
$routes->post('pos-creation-searc-rec','MyPosCreation::pos_create_search_rec',['filter' => 'myauthuser']);
$routes->post('pos-creation-del-rec','MyPosCreation::pos_create_del_rec',['filter' => 'myauthuser']);
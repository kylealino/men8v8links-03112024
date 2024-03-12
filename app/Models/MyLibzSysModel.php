<?php
namespace App\Models;
use CodeIgniter\Model;

class MyLibzSysModel extends Model
{
    public function oa_nospchar($cdatame='') { 
        $cddata = '';
        if(!empty($cdatame)) { 
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
        }
        return $cddata;
    }
    
    public function oa_no_commas($xval) { 
        $xval = str_replace(',','',trim($xval));
        return $xval;
    }
    
	// check the files uploaded if it is in image format
	public function valid_file_type_image($file_type){ 
		$allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
		return in_array($file_type, $allowed_mime_type_arr);
	} //end valid_file_type_image
    
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
        
        $obj .= '</select>';
        if($moutput == 'TO_ECHO') { 
            echo $obj;
        } else { 
            return $obj;
        }
    }  //end mypopulist_2
    
    public function mypagination($npage_curr,$npage_count,$cjavafunc='__myredirected_search',$moutput='') {  
        $chtml = "
    <ul class=\"pagination pagination-sm flex-wrap\">       
        ";
        /******  build the pagination links ******/
        // if not on page 1, don't show back links
        if ($npage_curr > 1) { 
            // show << link to go back to page 1
            $chtml .= " 
            <li class=\"page-item pull-left previous\">
            <a class=\"page-link\" href=\"javascript:{$cjavafunc}('1');\" aria-label=\"Previous\">
            <span aria-hidden=\"true\">&laquo;</span>
            </a> 
            </li>
            ";
            // get previous page num
            $prevpage = $npage_curr - 1;
            // show < link to go back to 1 page
            $chtml .= "
            <li class=\"page-item pull-left\">
             <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $prevpage . "');\"><span aria-hidden=\"true\">&lsaquo;</span></a> 
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
             <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $nextpage . "');\"><span aria-hidden=\"true\">&rsaquo;</span></a> 
            </li>
            ";
            // echo forward link for lastpage
            $chtml .= " 
            <li class=\"page-item pull-left\">
             <a class=\"page-link\" href=\"javascript:{$cjavafunc}('" . $npage_count . "');\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a> 
            </li>
            ";
         } // end if
         # end build pagination links 
        $chtml .= "
            </ul>
        ";
        if($moutput == 'TO_ECHO') { 
            echo $chtml;
        } else { 
            return $chtml;
        }       
    }  //end mypagination

    public function memypreloader01($domid) {
        $chtml = "
        <div class=\"me-overlay\" id=\"{$domid}\">
            <div class=\"me-overlay-inner\">
                <div class=\"me-overlay-content\"><span class=\"me-spinner\"></span></div>
            </div>
        </div>        
        ";
        return $chtml;

    } //end memypreloader

    public function memsgbox_yesno1($domid,$ctitle='',$cmsg='') {
        $chtml = "
        <div class=\"modal\" id=\"{$domid}\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\" tabindex=\"-1\" aria-labelledby=\"staticBackdrop{$domid}\" aria-hidden=\"true\">
            <div class=\"modal-dialog modal-dialog-centered\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h6 class=\"modal-title fw-bold\" id=\"staticBackdrop{$domid}\">{$ctitle}</h6>
                        <button type=\"button\" class=\"btn btn-close btn-danger\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                    </div>
                    <div class=\"modal-body\" id=\"{$domid}_bod\">
                            {$cmsg}
                    </div>
                    <div class=\"modal-footer\">
                        <button class=\"btn btn-info btn-sm\" id=\"{$domid}_yes\" >Yes</button>
                        <button class=\"btn btn-danger btn-sm\" id=\"{$domid}_no\" data-bs-dismiss=\"modal\">No</button>
                    </div>
                </div>
            </div>
        </div>      
        ";
        return $chtml;
    } //end memsgbox_yesno1

    public function memsgbox1($domid,$ctitle='',$cmsg='') {
        $chtml = "
        <div class=\"modal\" id=\"{$domid}\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\" tabindex=\"-1\" aria-labelledby=\"staticBackdrop{$domid}\" aria-hidden=\"true\">
            <div class=\"modal-dialog\">
                <div class=\"modal-content\">
                    <div class=\"modal-header bg-danger p-1\" id=\"{$domid}_mebgtitle\">
                        <h6 class=\"modal-title fw-bolder\" id=\"staticBackdrop{$domid}\" style=\"color: white !important;\">{$ctitle}</h6>
                        <button type=\"button\" class=\"btn-close btn-sm\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                    </div>
                    <div class=\"modal-body p-1\" id=\"{$domid}_bod\">
                        {$cmsg}
                    </div>
                </div>
            </div>
        </div>      
        ";
        return $chtml;
    } //end memsgbox1

    public function memsgbox2($domid,$ctitle='',$cmsg='') { 
        $chtml = "
        <div class=\"modal\" id=\"{$domid}\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\" tabindex=\"-1\" aria-labelledby=\"staticBackdrop{$domid}\" aria-hidden=\"true\">
            <div class=\"modal-dialog\">
                <div class=\"modal-content\">
                    <div class=\"modal-header bg-success p-1\" id=\"{$domid}_mebgtitle\">
                        <h6 class=\"modal-title fw-bolder\" id=\"staticBackdrop{$domid}\" style=\"color: white !important;\">{$ctitle}</h6>
                        <button type=\"button\" class=\"btn-close btn-sm\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                    </div>
                    <div class=\"modal-body p-1\" id=\"{$domid}_bod\">
                        {$cmsg}
                    </div>
                </div>
            </div>
        </div>      
        ";
        return $chtml;
    } //end memsgbox2

    public function memsgbox3($domid,$ctitle='',$cmsg='') { 
        $chtml = "
        <div class=\"modal\" id=\"{$domid}\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\" tabindex=\"-1\" aria-labelledby=\"staticBackdrop{$domid}\" aria-hidden=\"true\">
            <div class=\"modal-dialog\">
                <div class=\"modal-content\">
                    <div class=\"modal-header bg-secondary p-1\" id=\"{$domid}_mebgtitle\">
                        <h6 class=\"modal-title fw-bolder\" id=\"staticBackdrop{$domid}\" style=\"color: white !important;\">{$ctitle}</h6>
                        <button type=\"button\" class=\"btn-close btn-sm\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                    </div>
                    <div class=\"modal-body p-1\" id=\"{$domid}_bod\">
                        {$cmsg}
                    </div>
                </div>
            </div>
        </div>      
        ";
        return $chtml;
    } //end memsgbox3

    public function random_string($length){ 
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    } //end random_string

}  //end main MyLibzSysModel

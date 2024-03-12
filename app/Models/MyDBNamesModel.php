<?php
namespace App\Models;
use CodeIgniter\Model;

class MyDBNamesModel extends Model
{
	public function medb($nnum=0) { 
		$medb[] = 'd_ap2';
		$medb[] = 'ap2_branch';
		$medb[] = 'pansamantala';
		return $medb[$nnum];
	}  //end medb
} //end MyDBNamesModel

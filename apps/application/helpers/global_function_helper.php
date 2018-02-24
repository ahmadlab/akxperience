<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper global functions
 * @author Faisal Latada mac_[at] gxrg [dot] org
 *		   Agung Iskandar agung [dot] iskandar [at] gmail [dot] com
 *		   Ivan Lubis ihate [dot] haters [at] yahoo [dot] com
 * @Category Helper
 */

/**
 * get day of week
 * @author Faisal Latada
 * @param $n date
 * @param $lang (optional) id language [its support only en and in], default en
 */
function day_of_week($n,$lang=1,$m='m') {
	$days = array(1=>'Sen','Sel','Rab','Kam','Jum','Sab','Min');
	if($lang==2) {
		$n = date('N',strtotime(date("Y-$m-$n")));
		return $days[$n];
	}
	return date('D',strtotime(date("Y-$m-$n")));
}
 
 /**
 * create html select list option for month
 * @author Agung Iskandar
 * @param $selected (optional) default selected month
 */
function list_month($selected=''){
	 $bulan = array(1=>'January','February','March','April','May','June','July','August','September','October','November','December');
	 $opt = '';
	 foreach($bulan as $key => $bln){
		  $z = $key<10 ? 0 : '';
		  $terpilih = ($selected == $key) ? 'selected' : '';
		  $opt .= "<option value=\"$z$key\" $terpilih>$bln</option>";
	 }
	 return $opt;
}
/**
 * create html select list option for year
 * @author Agung Iskandar
 * @param $selected (optional) default selected year, default current year
 * @param $len (optional) jml tahun yg ditampilkan sebelum dan sesudah tahun berjalan
 */
function list_year($start='',$selected='',$len=10){
	 $this_year 		= ($start) ? $start : date('Y');
	 $selected			= ($selected == '') ? $selected : $selected;
	 $year_bef 			= (int)$this_year - $len;
	 $year_aft			= (int)$this_year + $len;
	 $opt 				= '';
	 $year = range($year_bef,$year_aft);
	 foreach($year as $y){
		  $terpilih = ($selected == $y) ? 'selected' : '';
		  $opt .= "<option $terpilih value=\"$y\">$y</option>";
	 }
	 return $opt;
}

/**
 * create html select list option for days of month
 * @author Faisal Latada
 * @param $m (optional) the month you wanna get, default current month
 * @param $skip (optional) how many day you wanna skip. +2days means skip two days from current date
 * @param $lang (optional) id language [its support only en and in], default en
 * @return string list option combo box with current date selected [<option value=date>date day</option>]
 */
function get_days_by_month($m='',$skip='',$lang=false,$selected='d') {
	$m = (!$m) ? date('m') : $m;
	$n = date('d',strtotime(date("Y-$m-$selected")));
	if($skip!='') {
		$n = date('d',strtotime($skip,strtotime(date("Y-$m-d"))));;	
	}
	$days = '';
	for($d=1;$d<=date('t',strtotime(date("Y-$m-d")));$d++) {
		$b = $d<10 ? $b = 0 : '';
		$s =  $n == $d ? 'selected ' : '';
		$days .= '<option '.$s.'value="'.$b.$d."\">".day_of_week($d,$lang,$m)." $b$d</option>";
	}
	return $days;
}

/**
 * create array range date
 * @author Faisal Latada
 * @param $start format date 'Y-m-d'
 * @param $end format date 'Y-m-d'
 * @return array range date 
 */
function get_date_list($start,$end) {
	$start = strtotime($start);
	$end = strtotime($end);
	
	$range = array();
	do {
		$range[] = date('d F Y', $start);
		$start = strtotime("+ 1 day", $start);
	}
	while($start < $end);     
	return $range;
}

/**
 * create html select list option for any number
 * @author Faisal Latada
 * @param $frm (optional) start from
 * @param $to (optional) until to
 * @param $selected (optional) default selected number
 * @return string html list option combo box
 */
function list_number($frm=0,$to=3,$selected='') {
	$lst = '';
	do {
		++$frm;
		$select = ($selected == $frm) ? 'selected' : '';
		$lst .= "<option $select value=\"$frm\">$frm</option>";
		
	}while($frm<$to);
	
	return $lst;
}

/**
 * Export data to excel/csv/txt
 * @author Agung Iskandar
 * @param $fname nama file
 */
function export_to($fname){
	 header("Content-type: application/x-msdownload");
	 $fname = str_replace(' ','_',$fname);
	 header ("Content-Disposition: attachment; filename=$fname");
	 header("Pragma: no-cache");
	 header("Expires: 0");
}
/**
 * Add nomor urut utk array hasil query (result_array)
 * @author Agung Iskandar
 * @param $array datanya
 * @return array dengan tambahan element id urut
 */
function set_nomor_urut($array){
	 $n = 0;
	 $datas = array();
	 foreach($array as $data){
		  $datas[$n]				= $data;
		  $datas[$n]['nomor'] 	= ++$nomor;
		  ++$n;
	 }
	 return $datas;
}

/**
 * Generate Format Date Time dari mysql style ke format standart atau sebaliknya
 * @author Ivan Lubis
 * @param $datetime date time format
 * @param $mark (optional) separator date, default -
 * @return string format date time
 */
function iso_date_time($datetime,$mark='-'){
	 if(!$datetime) return;
	 list($date,$time) = explode(' ', $datetime);
	 list($thn,$bln,$tgl) = explode('-',$date);
	 return $tgl.$mark.$bln.$mark.$thn.' '.$time;
}
/**
 * Generate Format Date dari mysql style ke format standart atau sebaliknya
 * @author Ivan Lubis
 * @param $datetime date format
 * @param $mark (optional) separator date, default -
 * @return string format date
 */
function iso_date($date,$mark='-'){
	 if(!$date) return;
	 list($thn,$bln,$tgl) = explode('-',$date);
	 return $tgl.$mark.$bln.$mark.$thn;
}

/**
 * list option utk combo box / select list
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $tbl nama tabel
 * @param $id primary key tabel
 * @param $name nama field tabel yg digunakan utk list
 * @param @where (optional) where query tabel
 * @param $terpilih (optional) list terpilih (selected)
 * @param $title (optional) title, default -----------------
 * @return string list option combo box <option value='val1>Name 1</option><option value='val2>Name 2</option>...
 *
 */
function select($tbl,$id='id',$name='name',$where='',$terpilih='',$title=''){
	 $CI=& get_instance();
	 $CI->load->database();
	 $list = $CI->db->select("$id , $name")->get_where($tbl,"$id is not null $where order by $name asc")->result_array();
	 $opt = "<option value=''>-----$title-----</option>";
	 foreach($list as $l){
				$selected = ($terpilih == $l[$id]) ? 'selected' : '';
				$opt .= "<option $selected value='$l[$id]'> $l[$name]</option>";
	 }
	 return $opt;
}

/**
 *
 * function select versi 2 - list option utk combo box / select list
 * @author Agung Iskandar <agung.iskandar@gmail.com> Modified params by Faisal Latada
 * @param $tbl nama tabel
 * @param $id primary key tabel
 * @param $name nama field tabel yg digunakan utk list
 * @param $where (optional) where query tabel
 * @param $selected (optional) item selected
 * @param $title (optional) title, default -----------------
 * @return string list option combo box <option value='val1>Name 1</option><option value='val2>Name 2</option>...
 *
 */
function selectlist($tbl,$id='id',$name='name',$where=null,$selected='',$title='------------',$order=false,$prefix=null,$suffix=null){
	$CI=& get_instance();
	$CI->load->database();
	if($order) $CI->db->order_by($order);
	$list 	= $CI->db->select("$id , $name")->get_where($tbl,$where)->result_array();
	$label 	= $title;
	$val 	= '';
	if(is_array($title)) {
		$label 	= $title[0];
		$val 	= 0;
	}
	$opt = "<option value='$val'>$label</option>";
	foreach($list as $l){
		$terpilih = ($selected == $l[$id]) ? 'selected' : '';
		$opt .= "<option $terpilih value='$l[$id]'>$prefix $l[$name] $suffix</option>";
	}	
	return $opt;
}

/**
 *@autor Ivan Lubis
 *@desc Show array data
 *@param $datadebug that var you wanna debug 
 */
function debugvar ($datadebug){
	 echo "<pre>";
	 print_r ($datadebug);
	 echo "</pre>";
}

/**
 *@autor Faisal Latada
 *@desc trace error code for moves authorization
 *@param $error response code
 *@return string message error
 */
function get_moves_auth_message($error) {
	switch($error) {
		case 'access_denied':
			$msg = 'User denied the application access';
			break;
		case 'unauthorized_client':
			$msg = 'The application is either disabled or the client id was wrong';
			break;
		case 'invalid_request':
			$msg = 'A parameter is missing or was invalid.<br>Check that the redirect path';
			break;
		case 'invalid_scope':
			$msg = 'The scope in request is not a valid scope';
			break;
		case 'server_error':
			$msg = 'There was an unexpected error and the authorization request could not be handled';
			break;
		default:
			$msg = 'Unknown error code';
			break;
	}
	return $msg;
}

/**
 *@autor Faisal Latada
 *@desc trace error code for moves token
 *@param $error response code
 *@return string message error
 */
function get_moves_token_message($error) {
	switch($error) {
		case 'invalid_client':
			$msg = '  The client authentication failed, check that client id and client secret are correct.';
			break;
		case 'invalid_grant':
			$msg = 'The provided authorization code is not valid, has been revoked or has expired (after 5 minutes)';
			break;
		case 'invalid_request':
			$msg = 'The request was malformed. Check that all the required parameters are provided.';
			break;
		default:
			$msg = 'Unknown error code';
			break;
	}
	return $msg;
}

function logs($data) {
	$f = fopen('./uploads/moves.txt','a+');
	if($f ) {
		if(is_array($data)) {
			foreach($data as $k => $v) {
				fwrite($f,"$k => $v " . PHP_EOL);
			}
			
		}else {
			fwrite($f,"$data " . PHP_EOL);
		}
		fclose($f);
	}
}

function cutomlogs($file,$data) {
	$f = fopen($file,'a+');
	if($f ) {
		if(is_array($data)) {
			foreach($data as $k => $v) {
				fwrite($f,"$k => $v " . PHP_EOL);
			}
			
		}else {
			fwrite($f,"$data " . PHP_EOL);
		}
		fclose($f);
	}
}

/**
 * fungsi untuk menambah hari dalam format y-m-d. contoh : add_date('2012-01-01', 3) // return 2012-01-04
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $dateSql tanggal dalam format sql (y-m-d)
 * @param $jmlHari jumlah hari yg ditambahkan
 * @return date
 *
 */
function add_date($dateSql,$jmlHari){
	 $sql = "SELECT DATE_ADD('$dateSql', INTERVAL $jmlHari DAY) as tanggal";
	 $CI=& get_instance();
	 return $CI->db->query($sql)->row()->tanggal;
}

function dateTimeDiff($time1, $time2, $precision = 6) {
	if (!is_int($time1)) {
		$time1 = strtotime($time1);
	}
	if (!is_int($time2)) {
		$time2 = strtotime($time2);
	}
	if ($time1 > $time2) {
		$ttime = $time1;
		$time1 = $time2;
		$time2 = $ttime;
	}
	$intervals = array(
		'year',
		'month',
		'day',
		'hour',
		'minute',
		'second'
	);
	$diffs     = array();
	foreach ($intervals as $interval) {
		$diffs[$interval] = 0;
		$ttime            = strtotime("+1 " . $interval, $time1);
		while ($time2 >= $ttime) {
			$time1 = $ttime;
			$diffs[$interval]++;
			$ttime = strtotime("+1 " . $interval, $time1);
		}
	}
	$count = 0;
	$times = array();
	foreach ($diffs as $interval => $value) {
		if ($count >= $precision) {
			break;
		}
		if ($value > 0) {
			if ($value != 1) {
				$interval .= "s";
			}
			$times[] = $value . " " . $interval;
			$count++;
		}
	}
	return implode(", ", $times);
}

/**
 * fungsi mendapatkan data hasil query dalam bentuk string (1 field saja yg return)
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $table nama tabel
 * @param $field nama kolom
 * @param $where (optional) where kondisi
 * @return string
 *
 */
function db_get_one($table,$field,$where=''){
	 $CI=& get_instance();
	 if($where != ''){
	 	 $r = $CI->db->select($field)->get_where($table,$where);
		 return ($r->num_rows()) ? $r->row()->$field : 0;
	 }
	 else{
	  	 $r = $CI->db->select($field)->get($table);
		 return ($r->num_rows()) ? $r->row()->$field : 0;
	 }
	 
}

/**
 * Javascript Alert Function
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $alert_message alert message yg ditampilkan dalam dialog box
 * @return string javascript <script>alert(message)</script>
 */
function alert($alert_message){
	 if($alert_message != ''){
	 	 return "<script>alert('$alert_message');</script>";
	 }
}

/**
 *@autor Faisal Latada
 *@desc to get data via $_GET
 *@param $keyword string ex -> http://example.com/id/1/name/example ;get('id') return 1; get('name') return example
 *@param $return_if_null (optional) return value if keyword is null
 *@return string

 */
function get($keyword,$return_if_null=''){
	 $arr 	= array('http://','https://','https://www.','http://www.');
	 $host	= str_replace($arr,'',base_url());
	 $host 	= array($host,'admpage/','partner/');//for subfolder in controller
	 $redirect = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_URL'];
	 $uri 	= explode('/',str_replace($host,'',$_SERVER['HTTP_HOST'].$redirect));
	 $data = array();
	 foreach ($uri as $key => $val){
		if($key > 0){
			if($key % 2 == 0){
				if($val != ''){
					isset($uri[$key+1]) ? $data[$val] = $uri[$key+1] : '';
				}
			}
		}
	 }
	 if(is_array($keyword)) {
		foreach($keyword as $k) {
			$buff[$k] = isset($data[$k]) ? $data[$k] : $return_if_null;
		}
		return $buff;
	 }else {
		return (!isset($data[$keyword]) || $data[$keyword] =='') ? $return_if_null : $data[$keyword];
	}
}

/**
 *@autor Faisal Latada
 *@desc generate zero number in front of var ex: 0000001, 0000123
 *@param $var your var number
 *@param $len how many number that you wish
 *@example zero_first(1,3) return 001; zero_first(12,5) return 00012;
 */
function zero_first($var,$len){
	return sprintf("%0{$len}s",$var);
}

/**
 *@autor Ivan Lubis
 *@desc Alias for  echo $this->db->last_query();
 *@return string last query
 */
function last_query(){
	 $CI=& get_instance();
	 echo $CI->db->last_query();

}
/**
 *
 *@desc Clear Tag html from string
 *@param $html sting with html tags
 *@retrun string without html tags
 */
function clear_html($html){
	 $html =  str_replace("\n","",$html);
	 $html =  str_replace("\r"," ",$html);
	 return str_replace ("	",'',(trim(strip_tags($html))));
}

/**
 * utk form jika di variable stringnya ada kutip
 * @author Agung Iskandar
 * @param $string string yg ingin ditampilkan dalam form
 */
function quote_form($string){
	 if(is_array($string)){
		  foreach($string as $key=>$val){
				$new_str[$key] = htmlspecialchars($val, ENT_QUOTES);
		  }
		  return $new_str;
	 }
	 else{
		  return htmlspecialchars($string, ENT_QUOTES);
	 }
}

/**
 *@desc range tanggal, mirip fungsi range() php tapi ini utk tanggal
 *@param $strDateFrom start date..
 *@param $strDateTo  sampai dengan..
 *@retrun string without html tags
 */
function date_range($strDateFrom,$strDateTo){
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

/**
 *@autor Faisal Latada
 *@desc calculate days left from current date
 *@param $date date format to be compared
 *@return int days left against $date
 */
function get_days_left($date) {
	$future = strtotime($date);
	$timefromdb = strtotime('now');
	$timeleft = $future-$timefromdb;
	$daysleft = round((($timeleft/24)/60)/60); 
	return $daysleft;
}

/**
 *@autor Faisal Latada
 *@desc calculate days and hours left from current date
 *@param $datestr date format to be compared
 *@return int days left against $datestr
 */
function get_days_hours_left($datestr) {
	$date=strtotime($datestr);

	$diff=$date-time();
	$days=floor($diff/(60*60*24)); // seconds/minute*minutes/hour*hours/day)
	$hours=round(($diff-$days*60*60*24)/(60*60));

	$buff['days'] = $days;
	$buff['hours'] = $hours;
	
	return $buff;
}

/**
 *@autor Faisal Latada
 *@desc merging between content and template
 *@param $view file name
 *@param $data array data sent to view
 */
function render($view,$data='',$layout='front'){
	 $CI=& get_instance();
	 
	 if(is_array($data)){
		  $CI->data = array_merge($CI->data,$data);
	 }
	 if(!$layout){
		  $CI->parser->parse($view.'.html', $CI->data);
	 }
	 else{
		  $CI->data['content'] = $CI->parser->parse($view.'.html', $CI->data,true);
		  $CI->parser->parse('layout/'."$layout.html",$CI->data);
	 }
	 
}

/**
 *@autor Faisal Latada
 *@desc render untuk merge template dengan content
 *@param $path image relative path
 *@param $url url image
 *@param $word word to be displayed
 *@param $exp time to live
 *@param $width width
 *@param $height height
 *@return return expire time
 */
function get_captcha($path,$url,$word,$exp=1800	,$width=145,$height=35) {

	$CI = get_instance();
	$CI->load->helper('captcha');
	$CI->load->model('Captcha_model');

	$conf = array(
		'img_path'   	=> './'. $path .'/',
		'img_url'    	=> $url,
		//'font' 		=> '../../system/fonts/texb.ttf',
		'img_width'  	=> $width,
		'img_height' 	=> $height,
		'word'      	=> $word,
		'expiration' 	=> $exp,
		"time" 			=> time()
	);

	$CI->data['c'] = create_captcha($conf);
		
	$attch = array(
		'ip_addr' 		=> $_SERVER['REMOTE_ADDR'],
		'user_agents'	=> $_SERVER['HTTP_USER_AGENT'],
		'time'			=> $CI->data['c']['time'],
		'key'			=> $CI->data['c']['word']
	);

	$CI->db->query($CI->db->insert_string('captcha', $attch));
	unset($CI->data['c']['key']);
	$CI->data['c'] = array($CI->data['c']);
	
	return (time()-$exp);
}

/**
 *@autor Faisal Latada
 *@desc validate user captcha against DB
 *@param $exp int expired time
 *@return return false on expired or true 
 */
function validate_captcha($exp){
	 $CI = get_instance();
	 $CI->db->query("DELETE FROM ddi_captcha WHERE time < ".$exp);
	
	 //see if a captcha exists:
	 $sql = "SELECT COUNT(*) AS count FROM ddi_captcha WHERE 'key' = ? AND 'ip_addr' = ? AND 'time' > ?";
	 $binds = array($CI->input->post('captcha'), $CI->input->ip_address(), $exp);
	 $query = $CI->db->query($sql, $binds);
	 $row = $query->row();
	
	 if ($row->count == 0)
	 {
		 return false;
	 }
	 return true;
}

/**
 *@autor Faisal Latada
 *@desc general paging
 *@param $total_rec int total all records
 *@param $lmt limit paging perpage
 *@param $path path for paging
 *@param $uri_segment int path uri segment
 *@return html string paging
 */
function paging($total_rec,$lmt,$path,$uri_segment) {
        $CI = get_instance();
	$CI->load->library('pagination');
	$link = "";
	
	
	$conf['base_url'] = $path;
	$conf['total_rows'] = $total_rec;
	$conf['per_page'] = $lmt;
	$conf['num_links'] = 4;
	$conf['full_tag_open'] = '<p class="navp">';
	$conf['full_tag_close'] = '</p>';
	$conf['uri_segment'] = $uri_segment;
	// $conf['use_page_numbers'] = TRUE;
	$conf['first_tag_open'] 	= '<span class="first-page">';
									//<img src="'.$CI->data['base_url'].'img/first.png"/>';
	$conf['first_tag_close']	= '</span>';
	
	$conf['prev_tag_open'] 		= '<span class="prev-page">';
									//<img src="'.$CI->data['base_url'].'img/prev-page.png"/>';
	$conf['prev_tag_close'] 	= '</span>';
	
	$conf['next_tag_open'] 		= '<span class="next-page">';
										//<img src="'.$CI->data['base_url'].'img/next-page.png"/>';
	$conf['next_tag_close'] 	= '</span>';
	
	$conf['last_tag_open'] 	= '<span class="last-page">';
										//<img src="'.$CI->data['base_url'].'img/last.png"/>';
	$conf['last_tag_close'] 	= '</span>';
	
	$config['anchor_class'] = 'class="number" ';
	
	$CI->pagination->initialize($conf);
	$link = $CI->pagination->create_links();
	
	return $link;
}
/**
 *@autor Faisal Latada
 *@desc get id language default
 *@return int id language
 */
function id_language_default(){
	 return db_get_one('language','id_language',array('default'=>1));
}

/**
 *@autor Faisal Latada
 *@desc moving file
 *@param $current_location_file path/filename.ext you wanna move
 *@param $dest_dir direktori tujuan
 *@param $new_file_name new name of file
 */
function move($current_location_file,$dest_dir,$new_file_name){
	 $dest_dir = ($dest_dir[strlen($dest_dir)-1] != '/') ? $dest_dir.'/' : $dest_dir;
	 if(!is_file($current_location_file)){
		  echo 'error|move file : File Not Exsist';
		  exit;
	 }
	 else if(!is_readable($current_location_file)){
		  echo 'error|move file : File is readonly';
		  exit;
	 }
	 else if(!is_dir($dest_dir)){
		  echo 'error|move file : Destination Directory not exsist';
		  exit;
	 }
	 else if(!is_readable($dest_dir)){
		  echo 'error|move file : Destination Directory is readonly';
		  exit;
	 }
	// else if(is_file($dest_dir.$new_file_name)){
	//	  echo 'move file : File Already Exist';
	//	  exit;
	// }
	 else if(@copy($current_location_file,$dest_dir.$new_file_name)){
		  if(!@unlink($current_location_file)){
				echo "error|Move file : Copy success but Can't delete original file";
		  }
	 }
	 else{
		  echo 'error|move file : undefined error';
		  exit;
	 }
}

/**
 *@autor Faisal Latada
 *@desc moving file
 *@param $path the path for creating directory
 *@param $dir directory name
 *@return error message on failure or void on success
 */
function create_dir($path,$dir){
	 if(!is_dir($path.$dir)){
			if(is_writeable($path)){
				if(!@mkdir($path.$dir,0,true)){
					echo 'create directory : undefined error';
					exit;
				}
			}
			else{
				echo "create directory : The directory is readonly";
				exit;
			}
		}
}

/**
 *@autor Faisal Latada
 *@desc make some random value based on length
 *@param $length how many value
 *@return int random
 */
function rands($length){
			$ch = 'aeiuoAIUEO2468bcdfghjklmpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ013579';
			$rnd ='';
			for($a=1;$a<=$length;$a++){
						$rnd .= ($a%2) ? $ch[mt_rand(0,13)] : $ch[mt_rand(14,60)];	
			}
			return $rnd;
}

/**
 *@autor Faisal Latada
 *@desc counting amount of records table
 *@param $table table name
 *@param $where where condition
 *@return int amount of records
 */
function db_get_count($table,$where='') {
	$CI=& get_instance();
	if($where != ''){
	 return $CI->db->select('count(*) as count')->get_where($table,$where)->row()->count;
	}
	else{
	 return $CI->db->select('count(*) as count')->get($table)->row()->count;
	}

}

function get_rss($target,$limit=20) {
	$buffs 		= false;
	$resources 	= cURL_multiple_thread($target);
	if(class_exists('SimpleXMLElement')) {
		foreach($resources as $k => $v) {
			if($v != '') {
				$node  = parse_url($k);
				$obj   = new SimpleXMLElement($v);
				$n 	   = 0;
				foreach($obj->channel->item as $buff) {
					++$n;
					$title 	  = (string) $buff->title;
					$desc  	  = (string) $buff->description;
					$link  	  = (string) $buff->guid;
					$pubDate  = (string) $buff->pubDate;
					$img	  = (string) $buff->enclosure->attributes()->url;
					// $img 	  = '';
					// if( isset($raw['type']) && isset($raw['url']) ){
						// $img = (string) $raw['url'];
					// }
					
					$buffs[] = array('title' => $title, 'content' => $desc,'publish_date' => $pubDate,
								   'thumb' => $img, 'node' => $node['host'], 'link' => $link);
					if($n == $limit) break;
				}
			}
		}
		
	}else {
		die('Please install simple xml extension');
	}
	return $buffs;
}


function get_content($path,$type,$val) {
	
	$options = array(
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_CUSTOMREQUEST 	=> 'GET');
	
	$resources = cURL($path,$options);
	
	$doc = new domDocument;
	@$doc->loadHTML($resources);
	$finder = new DomXPath($doc);
	// $classname="article_copy";
	$html = false;
	$nodes = $finder->query("//*[contains(@$type, '$val')]");

	if (!is_null($nodes)) {
		foreach ($nodes as $element) {
			$nodes1 = $element->childNodes;
			foreach ($nodes1 as $node) {
				if(substr($node->nodeValue,0,6) != 'jQuery') {
					$tmp_doc = new DOMDocument();
					$tmp_doc->appendChild($tmp_doc->importNode($node,true));       
					$html .= $tmp_doc->saveHTML();
					
				}
			}

		}
	}
	return $html;
}

/**
 *@autor Faisal Latada
 *@desc fetch current language based on url
 *@return int amount of records
 */
function get_current_lang() {
	$CI =& get_instance();
	return $CI->uri->segment(1);
}

/**
 *@autor Faisal Latada
 *@desc fetch current language id from DB
 *@return array record from DB
 */
function current_lang_id() {
	return db_get_one('language','id_language',array('lang'=>get_current_lang()));
}

/**
 *@autor Faisal Latada
 *@desc make a format for money
 *@return int format for money 
 */
function rp($val) {
    return number_format($val,"0",",",".");
}

/**
 *@autor Faisal Latada
 *@desc setup the capthca
 *@return void
 */
function set_captcha($str,$sesname){
	// session_start();
	$_SESSION["$sesname"] = $str;
	// Create an image from button.png
}

/**
 *@autor Faisal Latada
 *@desc create capthca depend on user input
 *@return img capthca
 */
function req_img_captcha($str){
	$image = imagecreatefrompng('./button.png');

	// Set the font colour
	$colour = imagecolorallocate($image, 183, 178, 152);

	// Set the font
	$font = './Anorexia.ttf';

	// Set a random integer for the rotation between -15 and 15 degrees
	$rotate = rand(-15, 15);

	// Create an image using our original image and adding the detail
	imagettftext($image, 14, $rotate, 18, 30, $colour, $font, $str);

	// Output the image as a png
	imagepng($image);
	
}

/**
 *@autor Faisal Latada
 *@desc Genarate download prompt to user
 *@params string filename of file 
 *@return void
 */
function download($filename) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($filename));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($filename));
	readfile($filename);
}

function create_oauth_signature($sname=false,$count=10) {
	$oauth_hash  = '';
	$oauth_hash .= ($count) ? "count=$count&" : '';
	$oauth_hash .= 'oauth_consumer_key='.TCUST_KEY.'&';
	$oauth_hash .= 'oauth_nonce=' . time() . '&';
	$oauth_hash .= 'oauth_signature_method=HMAC-SHA1&';
	$oauth_hash .= 'oauth_timestamp=' . time() . '&';
	$oauth_hash .= 'oauth_token='.TACC_TOKEN.'&';
	$oauth_hash .= 'oauth_version=1.0&';
	$oauth_hash .= ($sname) ? 'screen_name='.$sname : '';
	
	$base  = '';
	$base .= 'GET';
	$base .= '&';
	$base .= rawurlencode('https://api.twitter.com/1.1/statuses/user_timeline.json');
	$base .= '&';
	$base .= rawurlencode($oauth_hash);

	$key  = '';
	$key .= rawurlencode(TCUST_SEC_KEY);
	$key .= '&';
	$key .= rawurlencode(TACC_SEC_TOKEN);

	$signature = base64_encode(hash_hmac('sha1', $base, $key, true));
	$signature = rawurlencode($signature);
	
	$oauth_header  = '';
	$oauth_header .= ($count) ? 'count="2", ' : '';
	$oauth_header .= 'oauth_consumer_key="'.TCUST_KEY.'", ';
	$oauth_header .= 'oauth_nonce="' . time() . '", ';
	$oauth_header .= 'oauth_signature="' . $signature . '", ';
	$oauth_header .= 'oauth_signature_method="HMAC-SHA1", ';
	$oauth_header .= 'oauth_timestamp="' . time() . '", ';
	$oauth_header .= 'oauth_token="'.TACC_TOKEN.'", ';
	$oauth_header .= 'oauth_version="1.0", ';
	$oauth_header .= ($sname) ? 'screen_name="'.$sname.'"' : ''; 
	
	$header = array("Authorization: Oauth {$oauth_header}", 'Expect:');
	
	return $header;
}

/**
 *@autor Faisal Latada
 *@desc fetching data from external site
 *@params $target string url
 *@params $options array multidimensional 
 *@return response
 */
function cURL($target,$options) {
	$init = curl_init($target);
	curl_setopt_array($init, $options);
	$data = curl_exec($init);
	if(curl_errno($init))	{
		die ('Got an error occurred while attempt to fetching : <br>' . curl_error($init) );
	}
	curl_close($init);
	return $data;
}

/**
 *@autor Faisal Latada
 *@desc fetching data from external sites
 *@params $node array target
 *@return response
 */
function cURL_multiple_thread($nodes){
        $mh = curl_multi_init();
        $curl_array = array();
        foreach($nodes as $i => $url)
        {
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $running = NULL;
        do {
            usleep(10000);
            curl_multi_exec($mh,$running);
        } while($running > 0);
       
        $res = array();
        foreach($nodes as $i => $url)
        {
            $res[$url] = curl_multi_getcontent($curl_array[$i]);
        }
       
        foreach($nodes as $i => $url){
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);       
        return $res;
}

/**
 *@autor Faisal Latada
 *@desc calculate credit car
 *@params int $ort 
 *@params int $dp 
 *@params int $tenor
 *@params float $flw
 *@params float $insurance
 *@params float $prov
 *@params int $adm
 *@return array detail payment and others info 
 */
function emulator_credit($otr,$dp,$disc,$tenor,$flw,$insurance,$prov,$adm) {
	$dp 			= ($dp/100) * $otr;
	$tdept 			= $otr - $dp - $disc;
	$itsflw 		= ( ($flw/100) * $tenor ) * $tdept;
	$total 			= $tdept + $itsflw;
	$tloan 			= $total / ( $tenor * 12);
	$its_insurance 	= ($otr * ($insurance/100)); //+ 30000;
	$itsprov 		= ($prov/100) * $tdept;
	$first 			= $dp + $tloan + $its_insurance + $adm + $itsprov;
	$result = array_map('rp',array('tenor' 		=> $tenor,
						'otr' 			=> $otr, 
						'dp' 	 		=> $dp,
						'tdept' 		=> $tdept,
						'brate' 		=> $itsflw, 
						'total' 		=> $total,
						'tloan' 		=> $tloan,
						'insurance' 	=> $its_insurance,
						'adm' 			=> $adm,
						'provisi' 		=> $itsprov,
						'first' 		=> $first));
	return $result;
	
}

/**
 *@autor Faisal Latada
 *@desc calculate point based on given price
 *@params int $price
 *@return int total point
 */
function converter_point($price) {
    return floor($price / 500000);
//	$CI			=& get_instance();
//	$quantifier = $CI->config->item('quantifier');
//	$point 		= 1;
//	for($a = $quantifier; $a <= ($quantifier * 40); $a+=$quantifier) {
//		if($price < $quantifier) {
//			return;
//		}
//		if( $price == $a ) {
//			return $point;
//		}
//		if($price != $a && $price > $a && $price < ($a + $quantifier) ) {
//			return $point;
//		}
//		$point++;
//	}
}

function broadcast_gcm($mail,$rid,$msg,$ckey,$return=false,$lname='gcm.log') {
	$fields = array(
		'collapse_key' 		=> $ckey,
		'data'	 			=> $msg,
		'registration_ids' 	=> $rid,
		
	);
	$headers = array(
		'Authorization: key=' . GAPI_KEY,
		'Content-Type: application/json'
	);
	$options = array(CURLOPT_POST 			=> true,
					 CURLOPT_HTTPHEADER 	=> $headers,
					 CURLOPT_RETURNTRANSFER => true,
					 CURLOPT_SSL_VERIFYPEER => false,
					 CURLOPT_POSTFIELDS 	=> json_encode($fields));
	$delim 	= ($return) ? '<br>' : PHP_EOL;
	$resp 	= cURL(GCM_ACT,$options);
	$log 	= '--------------------------------------------------------------------------------------------------------' . $delim;
	$log   .= date('Y-m-d H:i') . $delim;
	$buf 	= json_decode($resp);
	
	if($buf != NULL) {
	
		$log 	.= "Total Broadcast " . ($buf->success + $buf->failure) . $delim;
		$log 	.= "Success " . $buf->success . $delim;
		$log 	.= "Failure " . $buf->failure . $delim;
		$log 	.= "canonical " . $buf->canonical_ids . $delim. $delim;
		$log 	.= $resp . $delim;
		
		if($buf->failure >= 1) {
			$log .= 'Reason' . $delim;
			foreach($buf->results as $k => $bufs) {
				if( $key = key((array)$bufs) == 'error') {
					if(isset($mail[$k]) ) {
						$log .= $mail[$k] .' --> ' . $bufs->error . $delim;
					}
				}
			}
		}
		
	}else {
		if( preg_match_all('/<h1>(.*?)<\/h1>|(\s\d{3})/i',$resp,$got) ) {
			$log .= $got[2][1] . ' ' . $got[1][0] . $delim;
			
		}else {
			$log .= 'Unknown response';
			
		}
		
	}
	$log .= '--------------------------------------------------------------------------------------------------------' . $delim;
	if($return) {
		return $log;
	}else {
		cutomlogs('./'.APPPATH.'logs/'.$lname,$log);
	}
}

function htmlallentities($str){
  $res = '';
  $strlen = strlen($str);
  for($i=0; $i<$strlen; $i++){
    $byte = ord($str[$i]);
    if($byte < 128) // 1-byte char
      $res .= $str[$i];
    elseif($byte < 192); // invalid utf8
    elseif($byte < 224) // 2-byte char
      $res .= '&#'.((63&$byte)*64 + (63&ord($str[++$i]))).';';
    elseif($byte < 240) // 3-byte char
      $res .= '&#'.((15&$byte)*4096 + (63&ord($str[++$i]))*64 + (63&ord($str[++$i]))).';';
    elseif($byte < 248) // 4-byte char
      $res .= '&#'.((15&$byte)*262144 + (63&ord($str[++$i]))*4096 + (63&ord($str[++$i]))*64 + (63&ord($str[++$i]))).';';
  }
  return $res;
}

function utf8_decodes($string, $strip_zeroes = false) {
	$pos = 0;
	$len = strlen($string);
	$result = '';
 
	while ($pos < $len) {
		$code1 = ord($string[$pos++]);
		if ($code1 < 0x80) {
			$result .= chr($code1);
		} elseif ($code1 < 0xE0) {
			// Two byte
			$code1 = 0x1F & $code1;
			$code2 = 0x3F & ord($string[$pos++]);
			$res_code1 = $code1 >> 2;
			if ($res_code1 > 0 || $strip_zeroes) {
				$result .= chr($res_code1);
			}
			$result .= chr( ($code1 << 6) | $code2);
		} elseif ($code1 < 0xF0) {
			// Three byte
			$code1 = $code1; // No need to mask
			$code2 = 0x3F & ord($string[$pos++]);
			$code3 = 0x3F & ord($string[$pos++]);
			$res_code1 = chr( ($code1 << 4) | ($code2 >> 2));
			if ($res_code1 > 0 || $strip_zeroes) {
				$result .= chr($res_code1);
			}
			$result .= chr( ($code2 << 6) | $code3);
		}
	}
 
	return $result;
}

function utf8_cast($str, $ignore_errors=true) {
    $result = '';
    $a = unpack('C*', $str);
    for($i=1; $i<count($a)-1; $i++) {
        $achar = $a[$i];
        $c = 0;
        $shift = 7;
        while(($achar >> $shift--) & 0x1) {
            $c++;
        }
        if ($c) {
            if ($c == 1) {
                #First byte of a utf-8 character is not supposed to start by 0b10xxxxxx.
                if (!$ignore_errors) {
                    return $result;
                }
                continue;
            }
            #We're dealing with a unicode character. Let's find its value.
            $unicode_value = $achar & (63 >> ($c));
            $cd = $c;
            while(--$c) {
                $unicode_value = $unicode_value << 6;
                $unicode_value |= ($a[$i+$cd-$c] & 0x7F);
            }
            #We have the unicode value, let's get a multibyte character.
            $result .= mb_convert_encoding('&#' . intval($unicode_value) . ';', 'UTF-8', 'HTML-ENTITIES');
        }
        else {
            #The character is part of the ASCII table.
            $result .= chr($achar);
        }
    }
    return $result;
}


function broadcast_apn($deviceToken, $message) {

    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', APNS_CERT);

// Open a connection to the APNS server
    $fp = stream_socket_client(
        'ssl://gateway.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

    if (!$fp)
        exit("Failed to connect: $err $errstr" . PHP_EOL);

    echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
    $body['aps'] = array(
        'alert' => $message,
        'sound' => 'default'
    );

// Encode the payload as JSON
    $payload = json_encode($body);

// Build the binary notification
    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
    fwrite($fp, $msg, strlen($msg));

// Close the connection to the server
    fclose($fp);
	
	return false;

}

function checkAppleErrorResponse($fp) {
	if ($fp) {

		$r = false;
		$error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $fp); 
		if ($error_response['status_code'] == '0') {
			$r = true;
		$error_response['status_code'] = '0-No errors encountered';

		} else if ($error_response['status_code'] == '1') {
		$error_response['status_code'] = '1-Processing error';

		} else if ($error_response['status_code'] == '2') {
		$error_response['status_code'] = '2-Missing device token';

		} else if ($error_response['status_code'] == '3') {
		$error_response['status_code'] = '3-Missing topic';

		} else if ($error_response['status_code'] == '4') {
		$error_response['status_code'] = '4-Missing payload';

		} else if ($error_response['status_code'] == '5') {
		$error_response['status_code'] = '5-Invalid token size';

		} else if ($error_response['status_code'] == '6') {
		$error_response['status_code'] = '6-Invalid topic size';

		} else if ($error_response['status_code'] == '7') {
		$error_response['status_code'] = '7-Invalid payload size';

		} else if ($error_response['status_code'] == '8') {
		$error_response['status_code'] = '8-Invalid token';

		} else if ($error_response['status_code'] == '255') {
		$error_response['status_code'] = '255-None (unknown)';

		} else {
		$error_response['status_code'] = $error_response['status_code'].'-Not listed';

		}
		
		fclose($fp);
		
		if($r) {
			return false;
			
		}else {
			return $error_response;
		}
	}
}

function grab_feedback() {
	$nFeedbackTupleLen = 38;
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', APNS_CERT);
	$fp = stream_socket_client(
		GW, $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		
	$_aFeedback = array();
	$sBuffer = '';
	if($fp) {
		while (!feof($fp)) {
			// $this->_log('INFO: Reading...');
			$sBuffer .= fread($fp, 8192);
			// $sBuffer .= $sCurrBuffer = fread($fp, 8192);
			// $nCurrBufferLen = strlen($sCurrBuffer);
			// if ($nCurrBufferLen > 0) {
				// $this->_log("INFO: {$nCurrBufferLen} bytes read.");
			// }
			// unset($sCurrBuffer, $nCurrBufferLen);

			$nBufferLen = strlen($sBuffer);
			if ($nBufferLen >= $nFeedbackTupleLen) {
				$nFeedbackTuples = floor($nBufferLen / $nFeedbackTupleLen);
				for ($i = 0; $i < $nFeedbackTuples; $i++) {
					$sFeedbackTuple = substr($sBuffer, 0, $nFeedbackTupleLen);
					$sBuffer = substr($sBuffer, $nFeedbackTupleLen);
					$_aFeedback[] = $aFeedback = $this->_parseBinaryTuple($sFeedbackTuple);
					// $this->_log(sprintf("INFO: New feedback tuple: timestamp=%d (%s), tokenLength=%d, deviceToken=%s.",
						// $aFeedback['timestamp'], date('Y-m-d H:i:s', $aFeedback['timestamp']),
						// $aFeedback['tokenLength'], $aFeedback['deviceToken']
					// ));
					unset($aFeedback);
				}
			}

			$read = array($fp);
			$null = NULL;
			$nChangedStreams = stream_select($read, $null, $null, 0, 1000000);
			if ($nChangedStreams === false) {
				$this->_log('WARNING: Unable to wait for a stream availability.');
				break;
			}
		}
	}else {
		die('count not connect to apn server');
	}
	fclose($fp);
	return $_aFeedback;
}

function send_sms($destNumber, $msg) {
    $url = 'https://alpha.zenziva.net/apps/smsapi.php?userkey=ovzlhn&passkey=0Mi4kOGb8D&nohp='. $destNumber .'&pesan=' . urlencode($msg);
    exec("curl '$url' > /dev/null &");
}

function send_gcm_async($mail, $rid, $msg, $ckey) {
    $url = base_url() . 'api/broadcast_gcm';
    exec("curl --data \"mail=$mail&rid[]=$rid&msg[message]=" . $msg['message'] . "&msg[description]=" . $msg['description'] . "&ckey=$ckey\" $url 2>/dev/null");
}

function send_apn_async($deviceToken, $message) {
    $message = urlencode($message);
    $url = base_url() . "api/broadcast_apn?deviceToken=$deviceToken&message=$message";
    exec("curl '$url' 2>/dev/null");
}
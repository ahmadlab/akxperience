<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ford Helper functions
 * @author Faisal Latada mac_ [at] gxrg [dot] org
 * @Category Helper
 */
 
/**
 * @desc Get car types
 * @param int $selected
 * @return string $return option of types
 */
function get_car_type($selected='') {
	$return = '<option value="0">--- Choice car product ---</option>';
	$CI =& get_instance();
	$ctypes = $CI->db->from('car_types a')
	->join('car_brands b','a.id_brands = b.id_brands','inner')
	->get();
	if($ctypes->num_rows()>0) {
		foreach($ctypes->result_array() as $types) {
			$selecteds = (isset($types[$selected]) && $selected == $types[$selected]) ? 'selected' : '';
			$return .= "<option $selecteds value=''>".$types['brands'] .' '. $types['types']."</option>";
		}
	}
	return $return;
}

/**
 * @desc Get all car
 * @param int $selected
 * @return string $return option types
 */
function get_carslst($selected='',$where=null) {
    $return = '<option value="0">--- Choice your car ---</option>';
    $CI 	=& get_instance();
    $where  = ($where != null) ? $CI->db->where($where) : '';
    // $ctypes = $CI->db->order_by('create_date,brands asc')->get('view_cars');
    $ctypes = $CI->db->order_by('brands, types, series, model asc')->get('view_cars');
    if($ctypes->num_rows()>0) {
        $ctypes = $ctypes->result_array();
        foreach($ctypes as $n => $types) {
            $opn 		  = "<optgroup label='{$types['brands']}'>";
            $return		 .= ($n==0) ? $opn : '';
            if(isset($ctypes[++$n]) ) {
                $return .= ($ctypes[$n++]['brands'] == $types['brands']) ? '' : '</optgroup>' . $opn;
            }
            $selecteds 	 = ($selected == $types['id']) ? 'selected' : '';
            // $selecteds 	 = (isset($types['id']) && $selected == $types['id']) ? 'selected' : '';
            $return 	.= "<option $selecteds value='{$types['id']}'>".$types['brands'] .' '. $types['types']. ' ' .$types['series'] .' '. $types['model'].' '.$types['transmisi'] .' '. $types['car_cc'].'cc '. $types['engine'].'</option>';

        }
    }
    return $return;
}
function get_car_array() {
    $CI 	=& get_instance();
    $ctypes = $CI->db->order_by('brands, types, series, model asc')->get('view_cars');
    if($ctypes->num_rows()>0) {
        $ctypes = $ctypes->result_array();
        foreach($ctypes as $n => $types) {
            $df[$n]['label'] = $types['brands'] .' '. $types['types']. ' ' .$types['series'] .' '. $types['model'].' '.$types['transmisi'] .' '. $types['car_cc'].'cc '. $types['engine'];
            $df[$n]['val']   = $types['brands'] .' '. $types['types']. ' ' .$types['series'] .' '. $types['model'].' '.$types['transmisi'] .' '. $types['car_cc'].'cc '. $types['engine'];
            $df[$n]['key']   = $types['id'];
        }
    }
    return $df;
}

/**
 * @desc print list transmisi
 * @param int $selected
 * @return string $return transmisi list
 */
function print_car_trans($selected='',$label='Choice Transmisi',$table='jdi_car',$field='transmisi') {
	$return = "<option value='0'>--- $label ---</option>";
	$CI     =& get_instance();
	$trans  = $CI->db->query("SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING(COLUMN_TYPE, 7, LENGTH(COLUMN_TYPE) - 8), \"','\", 1 + units.i + tens.i * 10) , \"','\", -1) as val
							FROM INFORMATION_SCHEMA.COLUMNS
							CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) units
							CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
							WHERE TABLE_NAME = '$table' 
							AND COLUMN_NAME = '$field'");
	if($trans->num_rows()>0) {
		foreach($trans->result_array() as $k => $types) {
			$selecteds 	 = ($selected == $types['val']) ? 'selected' : '';
			$return 	.= "<option $selecteds value='{$types['val']}'>".strtoupper($types['val'])."</option>";
		}
	}
	return $return;
}

/**
 * @desc print list transmisi
 * @param int $selected
 * @return string $return transmisi list
 */
function print_car_cc($selected='') {
	$CI	 	=& get_instance();
	$return = '<option value="0">--- Choice Car CC ---</option>';
	$cc 	= $CI->db->get('car_cc');
	if($cc->num_rows()>0) {		
		foreach($cc->result_array() as $types) {
			$selected 	 = ($selected == $types['id_cc']) ? 'selected' : '';
			$return 	.= "<option $selected value='{$types['id_cc']}'>".$types['cc']."cc</option>";
		}
	}
	return $return;
}

/**
 * @desc print list transmisi
 * @param int $selected
 * @return string $return transmisi list
 */
function print_car_color($id=false,$selected='') {
	$CI	 	=& get_instance();
	$colors = $CI->db->select('a.id_car_colors,b.color')->from('car_colors a')->join('ref_color b','a.ref_color = b.id_ref_color','inner')
	->where_in('a.id_car',array($id))->get();
	$return = '<option value="0">--- Choice car color ---</option>';
	if($colors->num_rows()>0) {
		foreach($colors->result_array() as $color) {
			$select='';
		
			if($selected==$color['id_car_colors'])
			{
				$select='selected';
			}
			$return 	.= "<option value='$color[id_car_colors]' $select>".$color['color']."</option>";
		}
	}
	return $return;
}

/** 
 * @desc get car name by user car id
 * @param int $usercarid
 * @return string $return transmisi list
 */
function get_user_car_by_id($usercarid) {
	$CI	 	=& get_instance();
	
	$sql = "SELECT concat(a.brands,' ',a.types,' ',a.series,' ',a.model,' ', a.transmisi, ' ',a.car_cc) as car 
			FROM (`jdi_user_cars` b) 
			INNER JOIN `jdi_view_cars` a 
			ON `b`.`id_car` = `a`.`id` 
			WHERE `b`.`id_user_cars` = ?";
	$cars = $CI->db->query($sql,$usercarid);
	if($cars->num_rows()>0) {
		$return = $cars->row()->car;
	}else {
		$return = '';
	}
	return $return;
}

function get_available_time_td($type='service',$date='') {
	$CI 	 =& get_instance();
	$date 	 = ($date) ? $date : date('Y-m-d');
	$where 	 = array('datetime >= ' => date('Y-m-d',strtotime("+1 day")) . ' 08:00:00','datetime < ' =>  date('Y-m-d',strtotime("+2 day")) . ' 08:00:00', 'type' => $type);
	$distime = $CI->db->get_where('unavailable_time',$where);
	$tbuff 	 = array();
	if($distime->num_rows()>0) {
		
		foreach($distime->result_array() as $k => $v) {
			$time = @explode(' ',$v['datetime']);
			$time = @explode(':',$time[1]);
			$time = $time[0] . '.' .$time[1];
			$tbuff[substr($time,0,-3)] = $time;
		}
	}
	for($i=8;$i<=17;$i++) {
		$s 		   = ($i>=10) ? $i : '0'.$i;
		$buff[$s]  = ($i>=10) ? $i. '.00' : '0'.$i .'.00';
	}
	return array_diff_assoc($buff,$tbuff);
	
}

function get_time_by_date($type='service',$date='',$locate='') {
	$CI 	 =& get_instance();
	$date 	 = ($date) ? $date : date('Y-m-d');
	$where 	 = array('datetime >= ' => date('Y-m-d',strtotime("$date")) . ' 08:00:00','datetime < ' =>  date('Y-m-d',strtotime("$date +1 day")) . ' 08:00:00', 'type' => $type,'ref_location' => $locate);
	$distime = $CI->db->get_where('unavailable_time',$where);
	$tbuff 	 = array();
	if($distime->num_rows()>0) {
		
		foreach($distime->result_array() as $k => $v) {
			$time = @explode(' ',$v['datetime']);
			$time = @explode(':',$time[1]);
			$time = $time[0] . '.' .$time[1];
			$tbuff[substr($time,0,-3)] = $time;
		}
	}
	for($i=8;$i<=17;$i++) {
		$s 		   = ($i>=10) ? $i : '0'.$i;
		$buff[$s]  = ($i>=10) ? $i. '.00' : '0'.$i .'.00';
	}
	return array_diff_assoc($buff,$tbuff);
}

/*
 * Get time For API 
 */
function get_time_by_daten($type='service',$date='',$locate='') {
	$CI 	 =& get_instance();
	$date 	 = ($date) ? $date : date('Y-m-d');
	$where 	 = array('datetime >= ' => date('Y-m-d',strtotime("$date")) . ' 08:00:00','datetime < ' =>  date('Y-m-d',strtotime("$date +1 day")) . ' 08:00:00', 'type' => $type,'ref_location' => $locate);
	$distime = $CI->db->get_where('unavailable_time', $where);
	$tbuff 	 = array();
	$o		 = 60;
	$start   = 8;
	$end     = 17;
	$intval  = 60;
		
	if($distime->num_rows()>0) {
		
		foreach($distime->result_array() as $k => $v) {
			$time = @explode(' ',$v['datetime']);
			$time = @explode(':',$time[1]);
			$time = $time[0] . '.' .$time[1];
			$tbuff[substr($time,0,-3)][] = $time;
		}
		
        $ntbuff = array();
        foreach($tbuff as $v) {
            if(is_array($v)) {

                foreach($v as $v1) {
                    $ntbuff[$v1] = $v1;
                }
            }
        }
        $tbuff = $ntbuff;
	}

    $time 	= $CI->db->get_where('ref_time_work',array('ref_location' => $locate, 'type' => $type));
    if($time->num_rows()>0) {

        $time  	 = $time->row_array();
        $start 	 = $time['start'];
        $end   	 = $time['end'];
        $intval  = $time['intval'];
    }
    for ($i = $start; $i <= $end; $i++){
        for ($j = 0; $j <= ($o - $intval); $j+=$intval){
            if($i == $end && $j > '0') {
                break;
            }
            $i = (int)$i;
            $j = ($j == '0') ? $j.'0' : $j;
            $i = ($i < 10) ? '0'.$i : $i;
            $buff["$i.$j"] = "$i.$j";
        }
    }

	$a = array_diff_assoc($buff,$tbuff);
	return array_values($a);
}

function get_available_time() {
	$tbuff 	 = array();
	for($i=8;$i<=17;$i++) {
		$buff[$i]  = ($i>=10) ? $i. '.00' : '0'.$i .'.00';
	}
	return $buff;
}

/*
 * Get time For CMS
 */
function global_available_time($date,$type='service',$ctrl,$locate) {
	$CI 	 =& get_instance();
	$where 	 = array('datetime >= ' => date($date,strtotime("+1day")) . ' 08:00:00','datetime <= ' =>  date($date,strtotime("+1day")) . ' 17:00:00', 'type' => $type,'ref_location' => $locate);
	$distime = $CI->db->order_by('datetime','asc')->get_where('unavailable_time',$where);
	$tbuff 	 = array();
	$n 		 = 1;
	$dchk	 = '';
	if($distime->num_rows()>0) {
		$g = $distime->result_array();
		foreach($g as $k => $v) {
			$time = @explode(' ',$v['datetime']);
			$time = @explode(':',$time[1]);
			$time = $time[0] . '.' .$time[1];
			$tbuff[substr($time,0,-3)][] = $time;	
		}
	}

	$o		= 60;
	$start  = 8;
	$end    = 17;
	$intval = 60;
	$time 	= $CI->db->get_where('ref_time_work',array('ref_location' => $locate, 'type' => $type));
	if($time->num_rows()>0) {
		$time  	 = $time->row_array();
		$start 	 = $time['start'];
		$end   	 = $time['end'];
		$intval = $time['intval'];
	}

	for ($i = $start; $i <= $end; $i++){
		for ($j = 0; $j <= ($o - $intval); $j+=$intval){
			if($i == $end && $j > '0') {
				break;
			}
			$i = (int)$i;
			$j = ($j == '0') ? $j.'0' : $j;
			$i = ($i < 10) ? '0'.$i : $i;
			$t = "$i.$j";
			
			$cls 	= 'alert-box';
			$label 	= 'Enabled';
			
			if(isset($tbuff[$i])) {	
				foreach($tbuff[$i] as $v) {
					if($v == $t) {
						$cls 	= 'alert';
						$label  = 'Disabled';
					}
				}
				
			}
			
			$dchk .= '<tbody class="trecords">';
			$dchk .= "<td>".$n."</td><td>".$t."</td><td><a class='$cls' onclick='javascript:void(0);change_schedule(\"".strtotime(date($date . str_replace('.',':',$t).':00'))."\",\"".$locate."\",\"".$ctrl."\",this)'>$label</a></td>";
			$dchk .= '</tbody>';
			$n++;	
		}
	}
	$dchk .= '';
	
	return $dchk;
}

function get_dasboard_tdrive() {
	$CI =& get_instance();
	$CI->load->model(getAdminFolder().'/reserve_model');
	$no 		= 0;
	$list_data 	= array();
	$path_uri 	= getAdminFolder().'/test_drive_reserve';
	$rest 	  	= $CI->reserve_model->GetAllTestDriveReserve(null,'booked',0,20,get_adm_location());
	foreach($rest->result_array() as $buff) {
		$no++;
		$id		 		= $buff['id_test_drive_booking'];
		$usr			= $buff['username'];
		$car			= $buff['car'];
		$timebook		= $buff['datetime_book'];
		$status			= $buff['status_book'];
		$create_date	= $buff['create_date'];
		
		$edit_href 	= site_url($path_uri.'/edit/'.$id);
		
		$list_data[] = array(
			'no'=>$no, 'id'=>$id,'car' => $car, 'stamp' => $create_date,
			'user'=>$usr,'time' => $timebook, 'status' => $status, 'edit_href'=>$edit_href,
		);
	}
	return $list_data;
}

function get_dasboard_hservice() {
	$CI =& get_instance();
	$CI->load->model(getAdminFolder().'/reserve_model');
	$no 		= 0;
	$list_data 	= array();
	$path_uri 	= getAdminFolder().'/hreserve';
	$rest 	  	= $CI->reserve_model->GetAllServiceHomeReserve(null,'booked',null,null,0,20,get_adm_location());
	foreach($rest->result_array() as $buff) {
		$no++;
		$id		 		= $buff['id_service_booking'];
		$usr			= $buff['username'];
		$car			= $buff['car'];
		$timebook		= $buff['datetime_book'];
		$status			= $buff['status_book'];
		$create_date	= $buff['create_date'];
		
		$edit_href 	= site_url($path_uri.'/edit/'.$id);
		
		$list_data[] = array(
			'no'=>$no, 'id'=>$id,'car' => $car, 'stamp' => $create_date,
			'user'=>$usr,'time' => $timebook, 'status' => $status, 'edit_href'=>$edit_href,
		);
	}
	return $list_data;
}

function get_dasboard_wservice() {
	$CI =& get_instance();
	$CI->load->model(getAdminFolder().'/reserve_model');
	$no 		= 0;
	$list_data 	= array();
	$path_uri 	= getAdminFolder().'/wreserve';
	$rest 	  	= $CI->reserve_model->GetAllServiceWorkshopReserve(null,'booked',null,null,0,20,get_adm_location());
//    die(var_dump($rest));
	foreach($rest->result_array() as $buff) {
		$no++;
		$id		 		= $buff['id_service_booking'];
		$usr			= $buff['username'];
		$car			= $buff['car'];
		$timebook		= $buff['datetime_book'];
		$status			= $buff['status_book'];
		$create_date	= $buff['create_date'];
		
		$edit_href 	= site_url($path_uri.'/edit/'.$id);
		
		$list_data[] = array(
			'no'=>$no, 'id'=>$id,'car' => $car, 'stamp' => $create_date,
			'user'=>$usr,'time' => $timebook, 'status' => $status, 'edit_href'=>$edit_href,
		);
	}
	return $list_data;
}

function get_dasboard_tcomplain() {
	$CI =& get_instance();
	$CI->load->model(getAdminFolder().'/technical_model');
	$no 		= 0;
	$list_data 	= array();
	$path_uri 	= getAdminFolder().'/tech_complain';
	$rest 	  	= $CI->technical_model->GetAllTechComplain(null,0,20);
	foreach($rest as $buff) {
		$no++;
		$id		 		= $buff['id_tech_complain'];
		$maker 			= $buff['maker'];
		$from 			= $buff['froms'];
		$title 			= $buff['about'];
		$text 			= word_limiter(strip_tags($buff['text']),3);
		$location		= word_limiter($buff['location'],4);
		$car			= db_get_one('view_cars_series',"concat(series)","id ='".$buff['ref_user_car']."'");
		$stat			= $buff['stat'];
		$stamp			= $buff['stamp'];

		$edit_href 	= site_url($path_uri.'/reply/'.$id);
		if($stat == 'open') {
			$list_data[] = array(
				'no'=>$no, 'id'=>$id,'maker' => $maker, 'from' => $from,'location' => $location,'car' => $car, 
				'title'=>$title,'text' => $text, 'stat' => $stat, 'edit_href'=>$edit_href, 'stamp' => $stamp
			);
		}
	}
	return $list_data;
}

function get_dasboard_tconsult() {
	$CI =& get_instance();
	$CI->load->model(getAdminFolder().'/technical_model');
	$no 		= 0;
	$list_data 	= array();
	$path_uri 	= getAdminFolder().'/tech_consult';
	$rest 	  	= $CI->technical_model->GetAllTechConsult(null,0,20);
	foreach($rest as $buff) {
		$no++;
		$id		 		= $buff['id_tech_consult'];
		$maker 			= $buff['maker'];
		$from 			= $buff['froms'];
		$title 			= $buff['about'];
		$text 			= word_limiter(strip_tags($buff['text']),3);
		$location		= word_limiter($buff['location'],4);
		// $car			= db_get_one('view_cars_series',"concat(series)","id ='".$buff['ref_user_car']."'");
		$stat			= $buff['stat'];
		$stamp			= $buff['stamp'];

		$edit_href 	= site_url($path_uri.'/reply/'.$id);
		if($stat == 'open') {
			$list_data[] = array(
				'no'=>$no, 'id'=>$id,'maker' => $maker, 'from' => $from,'location' => $location,/* 'car' => $car,  */
				'title'=>$title,'text' => $text, 'stat' => $stat, 'edit_href'=>$edit_href, 'stamp' => $stamp
			);
		}
	}
	return $list_data;
}

function get_service_by($type) {
	return selectlist('car_services','id_service','service',"id_service_type = '$type'",$type,'--- Choice Service ---');
}

function update_point($id) {
	$CI =& get_instance();
	$where = array('a.status_book' => 'finished', 'a.id_service_booking' => $id);
    $book = $CI->db->from('car_service_booking a')
	->join('user_cars b','a.ref_user_cars = b.id_user_cars','inner')
	->where($where)
	->get();
    if($book->num_rows()>0){
		$book = $book->row_array();
        $current_point = db_get_one('user_cars', 'point_reward', "id_user_cars = '".$book['id_user_cars']."'");
		$p   = converter_point($book['total_price']);
		if($p) {
            $new_point = $current_point + $p;
			$CI->db->where('id_user_cars',$book['id_user_cars'])->update('user_cars',array('point_reward' => $new_point));
		}
	}
}

function parse_twitter($resp) {
	$html = '';
	foreach(json_decode($resp) as $raw) {
		
		$id	   		 	= $raw->id_str;
		$screen_name	= $raw->user->screen_name;
		$lname			= $raw->user->name;
		$profile_img	= $raw->user->profile_image_url;
		$pubDate		= $raw->created_at;
		$retweet		= $raw->retweet_count;
		$fav			= $raw->favorite_count;
		
		$name			= "<a href='https://twitter.com/$screen_name'>$lname</a>";
		$thumb			= "<img src='$profile_img' />";
		
		$tweet 		 	= preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\./-]*(\?\S+)?)?)?)@', '<a target="blank" title="$1" href="$1">$1</a>', $raw->text);
		$tweet 			= preg_replace('/#([0-9a-zA-Z_-]+)/', "<a target='blank' title='$1' href=\"http://twitter.com/search?q=%23$1\">#$1</a>",  $tweet);
		$tweet 			= preg_replace("/@([0-9a-zA-Z_-]+)/", "<a target='blank' title='$1' href=\"https://twitter.com/$1\">@$1</a>",  $tweet);

		$today          = time();
		$time           = substr($pubDate, 11, 5);
		$day            = substr($pubDate, 0, 3);
		$date           = substr($pubDate, 7, 4);
		$month          = substr($pubDate, 4, 3);
		$year           = substr($pubDate, 25, 5);
		$english_suffix = date('jS', strtotime(preg_replace('/\s+/', ' ', $pubDate)));
		$full_month     = date('F', strtotime($pubDate));

		$default   		= $full_month . $date . $year;
		$full_date 		= $day . $date . $month . $year;
		$ddmmyy    		= $date .' '. $month .' '. $year;
		$mmyy      		= $month . $year;
		$mmddyy    		= $month . $date . $year;
		$ddmm      		= $date . $month;

		$timeDiff = dateTimeDiff($today, $pubDate, 1);
		
		foreach($raw->entities as $fraw) {
			
			$hashtag 	= $raw->entities->hashtags;
			$symbol  	= $raw->entities->symbols;
			$url 	 	= $raw->entities->urls;
			$mention 	= '';
			$media_url  = '';
			$type	 	= '';
			$photo	 	= '';
			
			$r = array_filter( $raw->entities->user_mentions );
			
			if(! empty( $r ) ) {
				foreach($raw->entities->user_mentions as $um) {
					if( isset($um->screen_name) ) {
						$mention = $um->screen_name;
					}
				}
			}
			if(isset($raw->entities->media)) {
				foreach($raw->entities->media as $media) {
					if( isset($media->media_url) ) {
						$photo = $media->media_url;
					}
					if( isset($media->expanded_url) ) {
						$media_url = $media->expanded_url;
					}
					if( isset($media->type) ) {
						$type = $media->type;
					}
				}
			}
		}
		
		$img = ($photo) ? "<img src='$photo' />" : '';
		$html .=
		'<table class="main-tweet">
			<tbody>
			  <tr>
				<td class="avatar">
				<a href="">'.$thumb.'</a>
				</td>
                <td class="user-info">
                <div class="fullname"><strong>'.$name.'</strong></div>
					<a href="/tini_ariqa?p=s"><span class="username"><span>@</span>'.$screen_name.'</span></a>
                </td>      
			  </tr>
			  <tr>
				<td class="tweet-content" colspan="3">
					 <div class="tweet-text" data-id="453052391957344256">
						<div class="dir-ltr" dir="ltr">'.$tweet.'</div>
					 </div>

					 <div class="metadata">
						<a href="#" class="" title="'.$ddmmyy.'">'.$timeDiff.'</a> 
						
						</div>
					 <div class="card-photo">
					 <div class="media">'.$img.'</div>
					<!--
					<div class="title">
						<a href="http://t.co/DOGtBvlnKZ" target="_blank">Twitter</a>
					</div>
					<div class="attribution">
						<span class="cap">by:</span>
						<a href="/tini_ariqa" class="profile-link">
							<span class="attr-fullname">Dhani</span>
							<span class="attr-username">@tini_ariqa</span>
						</a>
					</div> -->
					<div class="description">   </div>
					</div>
				</td>
			  </tr>
			</tbody>
		</table>';
		
		// $buf[] = array('id' => $id, 'name' => $name, 'thumb' => $thumb ,'tweet' => $tweet, 'time' => $timeDiff,
					   // 'retweet' => $retweet, 'fav' => $fav,'hashtag' => $hashtag,'symbol' => $symbol, 'url' => $url,
					   // 'type' => $type,'mention_name' => $mention,'media_url' => $media_url,'photo' => $photo);
	}
	
	return $html;

}

function fetch_template($key) {
	$CI =& get_instance();
	$CI->db->where(array('type' => $key));
	$temp =  $CI->db->get('web_service_template');
	if($temp->num_rows()>0) {
		return $temp->row_array();
	}
}

/**
 * @desc print list status
 * @param int $selected
 * @return string $return transmisi list
 */
function print_status($selected='',$label='Choice Status',$table='jdi_car_service_booking',$field='status_book') {
	$return = "<option value='0'>--- $label ---</option>";
	$CI     =& get_instance();
	$trans  = $CI->db->query("SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING(COLUMN_TYPE, 7, LENGTH(COLUMN_TYPE) - 8), \"','\", 1 + units.i + tens.i * 10) , \"','\", -1) as val
							FROM INFORMATION_SCHEMA.COLUMNS
							CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) units
							CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
							WHERE TABLE_NAME = '$table' 
							AND COLUMN_NAME = '$field'");
	if($trans->num_rows()>0) {
		foreach($trans->result_array() as $k => $types) {
			$selecteds 	 = ($selected == $types['val']) ? 'selected' : '';
			$return 	.= "<option $selecteds value='{$types['val']}'>".ucfirst($types['val'])."</option>";
		}
	}
	return $return;
}
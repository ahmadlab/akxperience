<?php
///////////////////////////////////////// NEW ////////////////////////////////////////////////////////

/**
 *render untuk merge template dengan content
 *@param $view file name
 *@param $data array data sent to view
 */
function admin_render($view,$data='',$layout='front',$folder=''){
	 $CI=& get_instance();
	 
	 if(is_array($data)){
		  $CI->data = array_merge($CI->data,$data);
	 }
	 if(!$layout){
		  $CI->parser->parse($view.'.html', $CI->data);
	 }
	 elseif($layout=='front_admin'){
		  if($CI->session->userdata('ADM_SESS')=='') {
			   $CI->parser->parse('admpage/login.html',$CI->data);
		  }else{
			   if(!$folder){
				 $CI->data['content'] = $CI->parser->parse('admpage/'.$view.'.html', $CI->data,true);   
			   }else{
				  $CI->data['content'] = $CI->parser->parse('admpage/'.$folder.'/'.$view.'.html', $CI->data,true);  
			   }
			   
			   // $CI->data['winner_competition_notif']=get_competition_winner_notif();
			   $CI->data['winner_competition_notif']= '';
			   $CI->parser->parse('layout/admpage/'."$layout.html",$CI->data);
		  }
		  
	 }
	 else{
		  $CI->data['content'] = $CI->parser->parse($view.'.html', $CI->data,true);
		  $CI->parser->parse('layout/'."$layout.html",$CI->data);
	 }
	 
}


function get_competition_winner_notif(){
    $CI=& get_instance();
    $CI->load->database();

    $CI->load->model(getAdminFolder().'/competition_model','Competition_model');
    $query1=$CI->Competition_model->GetAllCompetition();
    $exist=0;
    $competition_name='';
    if($query1->num_rows()>0){
	$loop_total=0;
	foreach($query1->result_array() as $row1){
	    $id_competition=$row1['id_competition'];
	    $themes=$row1['themes'];
	    $start_period=$row1['start_period'];
	    $end_period=$row1['end_period'];
	    
	    $str_end_period = strtotime($end_period);
	    $res_str_end_period=date('Y-m-d', strtotime('-5 days', $str_end_period));
	   
	    $now=date('Y-m-d');
	    
	    //echo $id_competition.'-'.$res_str_end_period.'-'.$now.'<br>';
	    
	    if($now>=$start_period && $now>=$res_str_end_period  && $now<=$end_period){
	    
		$CI->db->where('id_competition',$id_competition);
		$query2=$CI->db->get('competition_user');
		if($query2->num_rows()>0){
		    $arr=array();
		    foreach($query2->result_array() as $row2){
			//$id_participant=$row2['id_competition_user'];
			  
			$is_win=$row2['is_win'];
			$is_run=$row2['is_run'];
			$is_third=$row2['is_third'];
			
			$arr[]=array('is_win'=>$is_win, 'is_run'=>$is_run, 'is_third'=>$is_third,);
		    }
		    
		    $check=0;
		    $err1=$err2=$err3='';
		    foreach($arr as $k=>$v){
			if($arr[$k]['is_win']==1){
			    $check++;
			    $err1='1st';
			}
			if($arr[$k]['is_run']==1){
			    $check++;
			    $err2='2nd';
			}
			if($arr[$k]['is_third']==1){
			    $check++;
			    $err3='3rd';
			}
		    }
		    
		    if($check!=3){
			if($err1!='' && $err2=='' && $err3==''){
			    $err='2nd and 3rd';
			}elseif($err2!='' && $err1=='' && $err3==''){
			    $err='1st and 3rd';
			}elseif($err3!='' && $err1=='' && $err2==''){
			    $err='1st and 2nd';
			}elseif($err1!='' && $err2!='' && $err3==''){
			    $err='3rd';
			}elseif($err1!='' && $err2=='' && $err3!=''){
			    $err='2nd';
			}elseif($err1=='' && $err2!='' && $err3!=''){
			    $err='1st';
			}elseif($err1=='' && $err2=='' && $err3==''){
			    $err='1st, 2nd, and 3rd';
			}
			$exist++;
			
			$loop_total++;
			
			//$edit_href = site_url('webcontrol/competition/edit/'.$id_competition.'#participants');
			//$competition_name.='<a href="'.$edit_href.'">';
			//$themes=$themes.', ';
			//$competition_name.=$themes;
			//$competition_name.='</a>';
			
		    }else{
			
		    }
		}
	    }
	}
	
	$loop=0;
	foreach($query1->result_array() as $row1){
	    $id_competition=$row1['id_competition'];
	    $themes=$row1['themes'];
	    
	    $start_period=$row1['start_period'];
	    $end_period=$row1['end_period'];
	    
	    $str_end_period = strtotime($end_period);
	    $res_str_end_period=date('Y-m-d', strtotime('-5 days', $str_end_period));
	   
	    $now=date('Y-m-d');
	    
	    if($now>=$start_period && $now>=$res_str_end_period  && $now<=$end_period){
		
		$CI->db->where('id_competition',$id_competition);
		$query2=$CI->db->get('competition_user');
		
		if($query2->num_rows()>0){
		
		    foreach($arr as $k=>$v){
			if($arr[$k]['is_win']==1){
			    $check++;
			}
			if($arr[$k]['is_run']==1){
			    $check++;
			}
			if($arr[$k]['is_third']==1){
			    $check++;
			}
		    }
		    
		    if($check!=3){
			$loop++;
			
			$edit_href = site_url('webcontrol/competition/edit/'.$id_competition.'#participants');
			$competition_name.='<a href="'.$edit_href.'">';
			//$themes=$themes.', ';
			
			if($loop_total==1){
			    $themes=$themes;
			}elseif($loop_total==$loop){
			    $themes='and '.$themes.'.';
			}elseif($loop_total==2 && $loop==1){
			    $themes=$themes.' ';
			}else{
			    $themes=$themes.', ';
			}
			
			$competition_name.=$themes;
			$competition_name.='</a>';
			
			//echo $loop.'-'.$loop_total.'<br>';
		    }else{
			
		    }
		}
	    }
	}
    }

    if($exist>0){
	$return = alert_box('Please select the '.$err.' winner of '.$competition_name,'warning');
    }else{
	$return='';
    }
    
    return $return;
}


/**
 * generate alert box notification with close button
 * @author latada
 * @param $msg notification message
 * @param $type type of notofication
 * @return string notification with html tag
 */
function alert_box($msg='',$type='') {
    $html = '';
    if ($msg!='') {
        if ($type == 'warning' || $type == 'error') {
            $type = 'alert';
        }
        $html .= '<div class="alert-box '.$type.'">';
        $html .= $msg;
        $html .= '<a href="#" class="close">&times;</a>';
        $html .= '</div>';
    }
    return $html;
}

/**
 * generate new token
 * @return string $code
 */
function generate_token() {
    $rand = md5(sha1('reg'.date('Y-m-d H:i:s')));
    $acceptedChars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $max = strlen($acceptedChars)-1;
    $tmp_code = null;
    for($i=0; $i < 8; $i++) {
      $tmp_code .= $acceptedChars{mt_rand(0, $max)};
    }
    $code=$rand.$tmp_code;
    return $code;        
}

/**
 * retrieve session of admin user id of forum
 * @author latada
 * @return string admin user id
 */
function adm_sess_userid_with_forum() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('ADM_SESS')=='') $sess = 0;
    else {
        $ADM_SESS = $CI->session->userdata('ADM_SESS');
        $sess = $ADM_SESS['memID'];
    }
    return $sess;
}

/**
 * retrieve session of admin user id
 * @author latada
 * @return string admin user id
 */
function adm_sess_userid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('ADM_SESS')=='') $sess = 0;
    else {
        $ADM_SESS = $CI->session->userdata('ADM_SESS');
        $sess = $ADM_SESS['admin_id_auth_user'];
    }
    return $sess;
}

/**
 * retrieve session of admin user group id
 * @author latada
 * @return string admin user group id
 */
function adm_sess_usergroupid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('ADM_SESS')=='') $sess = 0;
    else {
        $ADM_SESS = $CI->session->userdata('ADM_SESS');
        $sess = $ADM_SESS['admin_id_auth_user_group'];
    }
    return $sess;
}

/**
 *
 * @param int $id_user
 * @return int super admin status 
 */
function adm_is_superadmin($id_user) {
    $return = 0;
    $CI=& get_instance();
    $CI->load->database();
    $CI->db->where('id_auth_user',$id_user);
    $CI->db->where('status',1);
    $CI->db->limit(1);
    $query = $CI->db->get('auth_user');
    if ($query->num_rows()>0) {
        $row = $query->row_array();
        $return = $row['is_superadmin'];
    }
    return $return;
}

/**
 *
 * @param int $id_group
 * @return int super admin group status 
 */
function adm_group_is_superadmin($id_group) {
    $return = 0;
    $CI=& get_instance();
    $CI->load->database();
    $CI->db->where('id_auth_user_group',$id_group);
    $CI->db->limit(1);
    $query = $CI->db->get('auth_user_group');
    if ($query->num_rows()>0) {
        $row = $query->row_array();
        $return = $row['is_superadmin'];
    }
    return $return;
}

/**
 * retrieve session of admin user site id
 * @author latada
 * @return string admin user group id
 */
function adm_sess_siteid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('ADM_SESS')=='') $sess = 0;
    else {
        $ADM_SESS = $CI->session->userdata('ADM_SESS');
        $sess = $ADM_SESS['admin_id_site'];
    }
    return $sess;
}

/**
 * retrieve session of admin microsite user id
 * @author latada
 * @return string admin user id
 */
function adm_microsite_sess_userid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('MICROSITE_SESS')=='') $sess = 0;
    else {
        $MICROSITE_SESS = $CI->session->userdata('MICROSITE_SESS');
        $sess = $MICROSITE_SESS['microadmin_id_auth_user'];
    }
    return $sess;
}

/**
 * retrieve session of admin microsite user group id
 * @author latada
 * @return string admin user group id
 */
function adm_microsite_sess_usergroupid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('MICROSITE_SESS')=='') $sess = 0;
    else {
        $MICROSITE_SESS = $CI->session->userdata('MICROSITE_SESS');
        $sess = $MICROSITE_SESS['microadmin_id_auth_user_group'];
    }
    return $sess;
}

/**
 * retrieve session of admin user site id
 * @author latada
 * @return string admin user group id
 */
function adm_microsite_sess_siteid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('MICROSITE_SESS')=='') $sess = 0;
    else {
        $MICROSITE_SESS = $CI->session->userdata('MICROSITE_SESS');
        $sess = $MICROSITE_SESS['microadmin_id_site'];
    }
    return $sess;
}

/**
 * get administrator folder
 * @return string cms folder
 */
function getAdminFolder() {
    $CI =& get_instance();
    $return = $CI->config->item('admin_folder');
    return $return;
}

/**
 * get microsite administrator folder
 * @return string microsite cms folder
 */
function getMicroAdminFolder() {
    $CI =& get_instance();
    $return = $CI->config->item('micrositeadmin_folder');
    return $return;
}

/**
 * sort management
 * @param type $id_menu
 * @param type $urut
 * @param type $id_parents_menu
 * @param type $path_app
 * @param type $sort
 * @return string $return 
 */
function sort_arrow($id_menu,$urut,$id_parents_menu,$path_app,$sort) {
    if ($sort == 'down') {
        $img = 'desc.gif';
    } else {
        $img = 'asc.gif';
    }
    $return = '<a id="sort_'.$sort.'-'.$id_menu.'" class="sort_arrow" onclick="javascript:change_sort(\''.$urut.'\',\''.$id_menu.'\',\''.$id_parents_menu.'\',\''.$sort.'\',\''.$path_app.'\')">
            <img src="'.base_url().'assets/images/admin/'.$img.'">
    </a>';
    return $return;
}

/**
 * check admin session authorization, return true or false 
 * @author latada
 * @return redirect to cms login page
 */
function auth_admin() {
    $CI=& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('ADM_SESS')=='') {
        $CI->session->set_userdata('tmp_login_redirect',current_url());
        redirect(getAdminFolder().'/login');
    } else {
        $sess = $CI->session->userdata('ADM_SESS');
        if (base_url() != $sess['admin_url']) {
            $CI->session->unset_userdata('ADM_SESS');
            $CI->session->set_userdata('tmp_login_redirect',current_url());
            redirect(getAdminFolder().'/login');
        } else {
            if ($_SERVER['REMOTE_ADDR'] != $sess['admin_ip']) {
                $CI->session->unset_userdata('ADM_SESS');
                $CI->session->set_userdata('tmp_login_redirect',current_url());
                redirect(getAdminFolder().'/login');
            }
        }
    }
}

/**
 * check admin microsite session authorization, return true or false 
 * @author latada
 * @return redirect to microsite cms login page
 */
function auth_microsite_admin() {
    $CI=& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('MICROSITE_SESS')=='') {
        $CI->session->set_userdata('tmp_login_site_redirect',current_url());
        redirect(getMicroAdminFolder().'/login');
    } else {
        $sess = $CI->session->userdata('MICROSITE_SESS');
        if (base_url() != $sess['microadmin_url']) {
            $CI->session->unset_userdata('MICROSITE_SESS');
            $CI->session->set_userdata('tmp_login_site_redirect',current_url());
            redirect(getMicroAdminFolder().'/login');
        } else {
            if ($_SERVER['REMOTE_ADDR'] != $sess['microadmin_ip']) {
                $CI->session->unset_userdata('MICROSITE_SESS');
                $CI->session->set_userdata('tmp_login_site_redirect',current_url());
                redirect(getMicroAdminFolder().'/login');
            }
        }
    }
}

/**
 * check authentication user by group id and menu id
 * @author latada
 * @param $id_group admin group id
 * @param $menu_name path menu or controller
 * @return true or false
 */
function auth_access_validation($id_group,$menu_name) {
    $CI=& get_instance();
    $CI->load->database();

    $CI->db->select('auth_pages.id_auth_user_group,auth_pages.id_menu_admin,menu_admin.file');
    $CI->db->join('menu_admin','menu_admin.id_menu_admin=auth_pages.id_menu_admin','left');
    $CI->db->where('auth_pages.id_auth_user_group',$id_group);
    $CI->db->where('menu_admin.file',$menu_name);
    $query = $CI->db->get('auth_pages');
    if ($query->num_rows()>0) {
        return true;
    } else {
        return false;
    }
}

/**
 * clear browser cache
 * @author latada
 */
function clear_cache() {
    $CI=& get_instance();
    $CI->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
    $CI->output->set_header("Pragma: no-cache");
}

/**
 * check authentication member, return true or false
 * @author latada
 * @return redirect to login page
 */
function auth_member() {
    $CI=& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('M_SESS')=='') {
        redirect('site/login');
    }
}

/**
 * check if user admin is logged
 * @author latada
 * @return redirect to profile page
 */
function admin_microsite_is_loged() {
    $CI=& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('MICROSITE_SESS')!='') {
        redirect(getMicroAdminFolder().'/home');
    }
}

/**
 * check if user admin is logged
 * @author latada
 * @return redirect to profile page
 */
function admin_is_loged() {
    $CI=& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('ADM_SESS')!='') {
        redirect(getAdminFolder().'/home');
    }
}

/**
 * check if user is logged
 * @author latada
 * @return redirect to profile page
 */
function mem_is_loged() {
    $CI=& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('M_SESS')!='') {
        redirect('site/profile');
    }
}

/**
 * retrieve session of user id
 * @author latada
 * @return string user id
 */
function mem_sess_userid() {
    $CI=& get_instance();
    $CI->load->library('session');
    if ($CI->session->userdata('M_SESS')=='') $m_sess = 0;
    else {
        $MEM_SESS = $CI->session->userdata('M_SESS');
        $m_sess = $MEM_SESS['M_ID'];
    }
    return $m_sess;
}

/**
 * global function for pagination
 * @author latada
 * @param $total_records total records data
 * @param $perpage showing total data per page
 * @param $path  path url
 * @param $uri_segment get from uri segment
 * @return string print pagination
 */
function global_paging($total_records,$perpage,$path,$uri_segment) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->library('pagination');
    $link = '';
    $config_paging['base_url'] = $path;
    $config_paging['total_rows'] = $total_records;
    $config_paging['per_page'] = $perpage;
    $config_paging['num_links'] = 5;
    $config_paging['full_tag_open'] = '<ul class="pagination">';
    $config_paging['full_tag_close'] = '</ul>';
    $config_paging['cur_tag_open'] = '<li class="current"><a>';
    $config_paging['cur_tag_close'] = '</a></li>';
    $config_paging['num_tag_open'] = '<li>';
    $config_paging['num_tag_close'] = '</li>';
    $config_paging['first_tag_open'] = '<li>';
    $config_paging['first_tag_close'] = '</li>';
    $config_paging['last_tag_open'] = '<li>';
    $config_paging['last_tag_close'] = '</li>';
    $config_paging['first_link'] = 'First';
    $config_paging['last_link'] = 'Last';
    $config_paging['next_link'] = FALSE;
    $config_paging['prev_link'] = FALSE;
    $config_paging['uri_segment'] = $uri_segment;
    $CI->pagination->initialize($config_paging);
    $paging = $CI->pagination->create_links();
    return $paging;
}

/**
 * global function for pagination front end
 * @author latada
 * @param $total_records total records data
 * @param $perpage showing total data per page
 * @param $path  path url
 * @param $uri_segment get from uri segment
 * @return string print pagination
 */
function global_paging_front($total_records,$perpage,$path,$uri_segment) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->library('pagination');
    $link = '';
    $config_paging['base_url'] = $path;
    $config_paging['total_rows'] = $total_records;
    $config_paging['per_page'] = $perpage;
    $config_paging['num_links'] = 5;
    $config_paging['full_tag_open'] = '';
    $config_paging['full_tag_close'] = '';
    $config_paging['cur_tag_open'] = '<label><a class="paging-case" style="background:#F23400;">';
    $config_paging['cur_tag_close'] = '</a></label>';
    $config_paging['num_tag_open'] = '<label>';
    $config_paging['num_tag_close'] = '</label>';
    $config_paging['anchor_class'] = 'paging-case';
    $config_paging['first_link'] = FALSE;
    $config_paging['last_link'] = FALSE;
    $config_paging['next_link'] = FALSE;
    $config_paging['prev_link'] = FALSE;
    $config_paging['uri_segment'] = $uri_segment;
    $CI->pagination->initialize($config_paging);
    $paging = $CI->pagination->create_links();
    return $paging;
}

function remove_module_directory($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); 
                else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

/**
 * retrieve field value of table
 * @author latada
 * @param $field field of table
 * @param $table table name
 * @param $where condition of query
 * @return string value
 */
function get_value($field,$table,$where)
{
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();

    $val='';
    $sql = "SELECT ".$field." FROM ".$table." WHERE ".$where;
    $query = $CI->db->query($sql);
    foreach($query->result_array() as $r)
    {
        $val = $r[$field];
    }
    return $val;
}

/**
 * retrieve setting value by key
 * @author latada
 * @param $config_key field key
 * @param $id_site (optional) site id
 * @return string value
 */
function get_setting($config_key='',$id_site=8) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();
    $val = '';
    if ($config_key != '') $CI->db->where('type',$config_key);
    $CI->db->where('id_site',$id_site);
    $query = $CI->db->get('setting');
    
    if ($query->num_rows()>1) {
        $val = $query->result_array();
    } elseif($query->num_rows()==1) {
        $row = $query->row_array();
        $val = $row['value'];
    }
    return $val;
}

/**
 * 
 * @return int default site
 */
function get_default_site() {
    $default_site = 1;
    return $default_site;
}

/**
 * retrieve menu admin title
 * @author latada
 * @param $key key menu file, returning blank if empty/false
 * @return string title value
 */
function get_admin_menu_title($key) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();

    $CI->db->where('file',$key);
    $CI->db->limit(1);
    $CI->db->order_by('id_menu_admin','desc');
    $query = $CI->db->get("menu_admin");

    if ($query->num_rows()>0) {
        $row = $query->row_array();
        return $row['menu'];
    } else {
        return '';
    }
}

/**
 * retrieve menu admin id
 * @author latada
 * @param $key key menu file, returning blank if empty/false
 * @return int id menu value
 */
function get_admin_menu_id($key) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();

    $CI->db->where('file',$key);
    $CI->db->limit(1);
    $CI->db->order_by('id_menu_admin','desc');
    $query = $CI->db->get("menu_admin");

    if ($query->num_rows()>0) {
        $row = $query->row_array();
        return $row['id_menu_admin'];
    } else {
        return '0';
    }
}

/**
 * insert log user activity to database
 * @author latada
 * @param $data data array to insert
 */
function insert_to_log($data) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();

    $CI->db->insert('logs',$data);
}

function seo_label() {
    return ' <img src="'.base_url('img/admin/help.png').'" width="16" height="16" class="has-tip" title="leave this field empty if you want the seo link same as menu title" border="0" alt="Help"/>';
}

/**
 * check status of module
 * @param type $module
 * @return type bool
 */
function check_module_installed($module) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();
    $CI->load->library('maintenance_mode');
    if ($CI->maintenance_mode->check_maintenance()) {
        $CI->db->where('module',$module);
        $CI->db->where('is_installed',1);
        $query = $CI->db->get('ddi_modules');
        if ($query->num_rows()>0) {
            return true;
        } else {
            show_404('page');
        }
    }
}

/**
 * return if module is installer
 * @param type $module
 * @return type 
 */
function module_is_installed($module) {
    # load ci instance
    $CI=& get_instance();
    $CI->load->database();
    $CI->load->library('maintenance_mode');
    if ($CI->maintenance_mode->check_maintenance()) {
        $CI->db->where('module',$module);
        $CI->db->where('is_installed',1);
        $query = $CI->db->get('ddi_modules');
        if ($query->num_rows()>0) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * enconding url characters
 * @author latada
 * @param $string  string value to encode
 * @return encoded string value
 */
function myUrlEncode($string) {
    $entities = array(' ', '!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "[", "]");
    $replacements = array('%20', '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%5B', '%5D');
    return str_replace($entities, $replacements, $string);
}

/**
 * decoding url characters
 * @author latada
 * @param $string string value to decode
 * @return decoded string value
 */
function myUrlDecode($string) {
    $entities = array('%20', '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%5B', '%5D');
    $replacements = array(' ', '!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "[", "]"); 
    return str_replace($entities, $replacements, $string);
}

/**
 * form validation : check email
 * @author latada
 * @param $str string value to check
 * @return string true or false
 */
function mycheck_email($str) {
    $str = strtolower($str);
    return preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $str);
}

/**
 * clean data from xss
 * @author latada
 * @return string clean data from xss
 */
function xss_clean_data($string) {
    $CI =& get_instance();
    $return = $CI->security->xss_clean($string);
    return $return;
}

/**
 * check validation of upload file
 * @author latada
 * @param $str string file to check
 * @param $max_size (optional) set maximum of file size, default is 4 MB
 * @return true or false
 */
function check_file_size($str, $max_size=4096000) {
    $file_size = $str['size'];
    if ($file_size > $max_size) return false;
    else return true;
}

/**
 * check validation of image type
 * @author latada
 * @param $source_pic string file to check
 * @return true or false
 */
function check_image_type($source_pic) {
    $image_info = check_mime_type($source_pic);

    switch ($image_info) {
        case 'image/gif':
            return true;
        break;

        case 'image/jpeg':
            return true;
        break;

        case 'image/png':
            return true;
        break;

        case 'image/wbmp':
            return true;
        break;

        default:
            return false;
        break;
    }
}

/**
 * check validation of image type in array
 * @author latada
 * @param $source_pic string file to check
 * @return true or false
 */
function check_image_type_array($source_pic) {
    switch ($source_pic) {
        case 'image/gif':
            return true;
        break;

        case 'image/jpeg':
            return true;
        break;

        case 'image/png':
            return true;
        break;

        case 'image/wbmp':
            return true;
        break;

        default:
            return false;
        break;
    }
}

/**
 * check validation of file type
 * @author latada
 * @param $source string file to check
 * @return true or false
 */
function check_file_type($source) {
    $file_info = check_mime_type($source);

    switch ($file_info) {
        case 'application/pdf':
            return true;
        break;

        case 'application/msword':
            return true;
        break;

        case 'application/rtf':
            return true;
        break;
        case 'application/vnd.ms-excel':
            return true;
        break;

        case 'application/vnd.ms-powerpoint':
            return true;
        break;

        case 'application/vnd.oasis.opendocument.text':
            return true;
        break;

        case 'application/vnd.oasis.opendocument.spreadsheet':
            return true;
        break;
        case 'image/gif':
            return true;
        break;

        case 'image/jpeg':
            return true;
        break;

        case 'image/png':
            return true;
        break;

        case 'image/wbmp':
            return true;
        break;

        default:
            return false;
        break;
    }
}

/**
 * get mime upload file
 * @author latada
 * @param $source string file to check
 * @return string mime type
 */
function check_mime_type($source) {
    $mime_types = array(
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // adobe
        'pdf' => 'application/pdf',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    
    $ext = strtolower(array_pop(explode(".",$source['name'])));
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }
    elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $source['tmp_name']);
        finfo_close($finfo);
        return $mimetype;
    }
    else {
        return 'application/octet-stream';
    }
}



/**
 * upload file to destination folder, return file name
 * @author latada
 * @param $source_file string of source file
 * @param $destination_folder string destination upload folder
 * @param $filename string file name
 * @return string edited filename
 */
function file_copy_to_folder ($source_file, $destination_folder, $filename) {
    $arrext = explode('.',$source_file['name']);
    $jml = count($arrext)-1;
    $ext = $arrext[$jml];
    $ext = strtolower($ext);
    $ret['ext'] = $ext;
    $destination_folder .= $filename . '.' . $ext;

    if(@move_uploaded_file($source_file['tmp_name'], $destination_folder)) { 
        $ret = $filename.".".$ext;
    }
    return $ret;
}

/**
 * upload multiple(array) file to destination folder, return array of file name
 * @author latada
 * @param $source_file array string of source file
 * @param $destination_folder string destination upload folder
 * @param $filename string of file name
 * @return string of edited filename
 */
function file_arr_copy_to_folder ($source_file, $destination_folder, $filename) {
    $tmp_destination = $destination_folder;
    for($index=0; $index<count($source_file['tmp_name']); $index++) {
        $arrext = explode('.',$source_file['name'][$index]);
        $jml = count($arrext)-1;
        $ext = $arrext[$jml];
        $ext = strtolower($ext);
        $destination_folder = $tmp_destination . $filename[$index] . '.' . $ext;

        if(@move_uploaded_file($source_file['tmp_name'][$index], $destination_folder)) {
            $ret[$index] = $filename[$index].".".$ext;
        }
    }
    return $ret;
}

/**
 * upload image to destination folder, return file name
 * @author latada
 * @param $source_file string source file
 * @param $destination_folder string destination upload folder
 * @param $filename string file name
 * @param $max_width string maximum image width
 * @param $max_height string maximum image height
 * @return string of edited file name
 */
function image_resize_to_folder ($source_pic, $destination_folder, $filename, $max_width, $max_height) 
{
    $image_info = getimagesize($source_pic['tmp_name']);
    $source_pic_name = $source_pic['name'];
    $source_pic_tmpname  = $source_pic['tmp_name'];
    $source_pic_size = $source_pic['size'];
    $source_pic_width = $image_info[0];
    $source_pic_height = $image_info[1];

    $x_ratio  = $max_width / $source_pic_width;
    $y_ratio  = $max_height / $source_pic_height;

    if( ($source_pic_width <= $max_width) && ($source_pic_height <= $max_height) ) 
    {
        $tn_width = $source_pic_width;
        $tn_height = $source_pic_height;
    } 
    elseif (($x_ratio * $source_pic_height) < $max_height) 
    {
        $tn_height = ceil($x_ratio * $source_pic_height);
        $tn_width = $max_width;
    } 
    else 
    {
        $tn_width = ceil($y_ratio * $source_pic_width);
        $tn_height = $max_height;
    }

    switch ($image_info['mime']) {
    case 'image/gif':
        if (imagetypes() & IMG_GIF)  
        {
            $src = imageCreateFromGIF($source_pic['tmp_name']) ;
            $destination_folder.="$filename.gif";
            //$destination_folder.=$filename;
            $namafile ="$filename.gif";
        }
    break;

    case 'image/jpeg':
        if (imagetypes() & IMG_JPG)  
        {
            $src = imageCreateFromJPEG($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.jpg";
            //$destination_folder.=$filename;
            $namafile ="$filename.jpg";
        }
    break;

    case 'image/pjpeg':
        if (imagetypes() & IMG_JPG)  
        {
            $src = imageCreateFromJPEG($source_pic['tmp_name']) ; 
                    $destination_folder.="$filename.jpg";
                    //$destination_folder.=$filename;
                    $namafile ="$filename.jpg";
        }
    break;

    case 'image/png':
        if (imagetypes() & IMG_PNG)  
        {
            $src = imageCreateFromPNG($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.png";
            //$destination_folder.=$filename;
            $namafile ="$filename.png";
        }
    break;

    case 'image/wbmp':
        if (imagetypes() & IMG_WBMP)
        {
            $src = imageCreateFromWBMP($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.bmp";
            //$destination_folder.=$filename;
            $namafile ="$filename.bmp";
        }
    break;
    }

    //chmod($destination_pic,0777);
    $tmp=imagecreatetruecolor($tn_width,$tn_height);
    imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$source_pic_width,$source_pic_height);

    //**** 100 is the quality settings, values range from 0-100.
    switch ($image_info['mime']) 
    {
    case 'image/jpeg':
        imagejpeg($tmp,$destination_folder,100); 
    break;

    case 'image/gif':
        imagegif($tmp,$destination_folder,100); 
    break;

    case 'image/png':
        imagepng($tmp,$destination_folder); 
    break;

    default:
        imagejpeg($tmp,$destination_folder,100); 
    break;
    }

    return ($namafile);
}

/**
 * upload image to destination folder, return file name
 * @author latada
 * @param $source_file string source file
 * @param $destination_folder string destination upload folder
 * @param $filename string file name
 * @param $max_width string maximum image width
 * @param $max_height string maximum image height
 * @return string of edited file name
 */
function fixed_image_resize_to_folder ($source_pic, $destination_folder, $filename, $max_width, $max_height) 
{
    $image_info = @getimagesize($source_pic['tmp_name']);
    $source_pic_name = $source_pic['name'];
    $source_pic_tmpname  = $source_pic['tmp_name'];
    $source_pic_size = $source_pic['size'];
    $source_pic_width = $image_info[0];
    $source_pic_height = $image_info[1];

    $x_ratio  = $max_width / $source_pic_width;
    $y_ratio  = $max_height / $source_pic_height;

    if( ($source_pic_width <= $max_width) && ($source_pic_height <= $max_height) ) 
    {
        $tn_width = $source_pic_width;
        $tn_height = $source_pic_height;
    } 
    elseif (($x_ratio * $source_pic_height) < $max_height) 
    {
        $tn_height = ceil($x_ratio * $source_pic_height);
        $tn_width = $max_width;
    } 
    else 
    {
        $tn_width = ceil($y_ratio * $source_pic_width);
        $tn_height = $max_height;
    }

    switch ($image_info['mime']) {
    case 'image/gif':
        if (@imagetypes() & IMG_GIF)  
        {
            $src = @imageCreateFromGIF($source_pic['tmp_name']) ;
            $destination_folder.="$filename.gif";
            //$destination_folder.=$filename;
            $namafile ="$filename.gif";
        }
    break;

    case 'image/jpeg':
        if (@imagetypes() & IMG_JPG)  
        {
            $src = @imageCreateFromJPEG($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.jpg";
            //$destination_folder.=$filename;
            $namafile ="$filename.jpg";
        }
    break;

    case 'image/pjpeg':
        if (@imagetypes() & IMG_JPG)  
        {
            $src = @imageCreateFromJPEG($source_pic['tmp_name']) ; 
                    $destination_folder.="$filename.jpg";
                    //$destination_folder.=$filename;
                    $namafile ="$filename.jpg";
        }
    break;

    case 'image/png':
        if (@imagetypes() & IMG_PNG)  
        {
            $src = @imageCreateFromPNG($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.png";
            //$destination_folder.=$filename;
            $namafile ="$filename.png";
        }
    break;

    case 'image/wbmp':
        if (@imagetypes() & IMG_WBMP)
        {
            $src = @imageCreateFromWBMP($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.bmp";
            //$destination_folder.=$filename;
            $namafile ="$filename.bmp";
        }
    break;
    }

    //chmod($destination_pic,0777);
    $tmp=@imagecreatetruecolor($max_width,$max_height);
    @imagecopyresampled($tmp,$src,0,0,0,0,$max_width, $max_height,$source_pic_width,$source_pic_height);

    //**** 100 is the quality settings, values range from 0-100.
    switch ($image_info['mime']) 
    {
    case 'image/jpeg':
        @imagejpeg($tmp,$destination_folder,100); 
    break;

    case 'image/gif':
        @imagegif($tmp,$destination_folder,100); 
    break;

    case 'image/png':
        @imagepng($tmp,$destination_folder); 
    break;

    default:
        @imagejpeg($tmp,$destination_folder,100); 
    break;
    }

    return ($namafile);
}

/**
 * upload image to destination folder, return file name
 * @author latada
 * @param $source_file string source file
 * @param $destination_folder string destination upload folder
 * @param $filename string file name
 * @param $max_width string maximum image width
 * @param $max_height string maximum image height
 * @return string of edited file name
 */
function trans_image_resize_to_folder ($source_pic, $destination_folder, $filename, $max_width, $max_height) 
{
    $image_info = getimagesize($source_pic['tmp_name']);
    $source_pic_name = $source_pic['name'];
    $source_pic_tmpname  = $source_pic['tmp_name'];
    $source_pic_size = $source_pic['size'];
    $source_pic_width = $image_info[0];
    $source_pic_height = $image_info[1];

    $x_ratio  = $max_width / $source_pic_width;
    $y_ratio  = $max_height / $source_pic_height;

    if( ($source_pic_width <= $max_width) && ($source_pic_height <= $max_height) ) 
    {
        $tn_width = $source_pic_width;
        $tn_height = $source_pic_height;
    } 
    elseif (($x_ratio * $source_pic_height) < $max_height) 
    {
        $tn_height = ceil($x_ratio * $source_pic_height);
        $tn_width = $max_width;
    } 
    else 
    {
        // $tn_width = ceil($y_ratio * $source_pic_width);
        // $tn_height = $max_height;
		$tn_width = $max_width;
        $tn_height = $max_height;
    }

    switch ($image_info['mime']) {
    case 'image/gif':
        if (imagetypes() & IMG_GIF)  
        {
            $src = imageCreateFromGIF($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.gif";
            $namafile ="$filename.gif";
        }
    break;

    case 'image/jpeg':
        if (imagetypes() & IMG_JPG)  
        {
            $src = imageCreateFromJPEG($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.jpg";
            $namafile ="$filename.jpg";
        }
    break;

    case 'image/pjpeg':
        if (imagetypes() & IMG_JPG)  
        {
            $src = imageCreateFromJPEG($source_pic['tmp_name']) ; 
                    $destination_folder.="$filename.jpg";
                    $namafile ="$filename.jpg";
        }
    break;

    case 'image/png':
        if (imagetypes() & IMG_PNG)  
        {
            $src = imageCreateFromPNG($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.png";
            $namafile ="$filename.png";
        }
    break;

    case 'image/wbmp':
        if (imagetypes() & IMG_WBMP)
        {
            $src = imageCreateFromWBMP($source_pic['tmp_name']) ; 
            $destination_folder.="$filename.bmp";
            $namafile ="$filename.bmp";
        }
    break;
    }

    //chmod($destination_pic,0777);
    $tmp=imagecreatetruecolor($tn_width,$tn_height);
    
    switch ($image_info['mime']) {
    
    case 'image/png':
        // integer representation of the color black (rgb: 0,0,0)
        $background = imagecolorallocate($tmp, 0, 0, 0);
        // removing the black from the placeholder
        imagecolortransparent($tmp, $background);

        // turning off alpha blending (to ensure alpha channel information 
        // is preserved, rather than removed (blending with the rest of the 
        // image in the form of black))
        imagealphablending($tmp, false);

        // turning on alpha channel information saving (to ensure the full range 
        // of transparency is preserved)
        imagesavealpha($tmp, true);

        break;
    }
    
    imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$source_pic_width,$source_pic_height);

    //**** 100 is the quality settings, values range from 0-100.
    switch ($image_info['mime']) 
    {
    case 'image/jpeg':
        imagejpeg($tmp,$destination_folder,100); 
    break;

    case 'image/gif':
        imagegif($tmp,$destination_folder,100); 
    break;

    case 'image/png':
        imagepng($tmp,$destination_folder); 
    break;

    default:
        imagejpeg($tmp,$destination_folder,100); 
    break;
    }

    return ($namafile);
}

/**
 * upload image to destination folder, return file name
 * @author latada
 * @param $source_file array string source file
 * @param $destination_folder string destination upload folder
 * @param $filename string file name
 * @param $max_width string maximum image width
 * @param $max_height string maximum image height
 * @return array string of edited file name
 */
function image_arr_resize_to_folder ($source_pic, $destination_folder, $filename, $max_width, $max_height)
{
    $tmp_dest = $destination_folder;
    for($index=0; $index<count($source_pic['tmp_name']); $index++)
    {
        $destination_folder = $tmp_dest;
        $image_info = getimagesize($source_pic['tmp_name'][$index]);
        $source_pic_name = $source_pic['name'][$index];
        $source_pic_tmpname  	= $source_pic['tmp_name'][$index];
        $source_pic_size = $source_pic['size'][$index];
        $source_pic_width = $image_info[0];
        $source_pic_height = $image_info[1];
        $x_ratio  = $max_width / $source_pic_width;
        $y_ratio  = $max_height / $source_pic_height;

        if( ($source_pic_width <= $max_width) && ($source_pic_height <= $max_height) )
        {
                $tn_width = $source_pic_width;
                $tn_height = $source_pic_height;
        }
        elseif (($x_ratio * $source_pic_height) < $max_height)
        {
            $tn_height = ceil($x_ratio * $source_pic_height);
            $tn_width = $max_width;
        }
        else
        {
             // $tn_width = ceil($y_ratio * $source_pic_width);
			// $tn_height = $max_height;
			$tn_width = $max_width;
			$tn_height = $max_height;
        }

        switch ($image_info['mime'])
        {
            case 'image/gif':
                if (imagetypes() & IMG_GIF)  { 
                    $src = imageCreateFromGIF($source_pic['tmp_name'][$index]) ; 
                    $destination_folder.="$filename[$index].gif";
                    $namafile ="$filename[$index].gif";
                }
            break;

            case 'image/jpeg':
                if (imagetypes() & IMG_JPG)  { 
                    $src = imageCreateFromJPEG($source_pic['tmp_name'][$index]) ; 
                    $destination_folder.="$filename[$index].jpg";
                    $namafile ="$filename[$index].jpg";
                }
            break;

            case 'image/pjpeg':
                if (imagetypes() & IMG_JPG)  { 
                    $src = imageCreateFromJPEG($source_pic['tmp_name'][$index]) ; 
                    $destination_folder.="$filename[$index].jpg";
                    $namafile ="$filename[$index].jpg";
                }
            break;

            case 'image/png':
                if (imagetypes() & IMG_PNG)  { 
                    $src = imageCreateFromPNG($source_pic['tmp_name'][$index]) ; 
                    $destination_folder.="$filename[$index].png";
                    $namafile ="$filename[$index].png";
                }
            break;

            case 'image/wbmp':
                if (imagetypes() & IMG_WBMP)  { 
                    $src = imageCreateFromWBMP($source_pic['tmp_name'][$index]) ; 
                    $destination_folder.="$filename[$index].bmp";
                    $namafile ="$filename[$index].bmp";
                }
            break;
        }

        //chmod($destination_pic,0777);
        $tmp=imagecreatetruecolor($tn_width,$tn_height);
        imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$source_pic_width,$source_pic_height);

        //**** 100 is the quality settings, values range from 0-100.
        switch ($image_info['mime'])
        {
            case 'image/jpeg':
                imagejpeg($tmp,$destination_folder,100); 
            break;

            case 'image/gif':
                imagegif($tmp,$destination_folder,100); 
            break;

            case 'image/png':
                imagepng($tmp,$destination_folder); 
            break;

            default:
                imagejpeg($tmp,$destination_folder,100); 
            break;
        }
        $url[] = $namafile;
    }
    return ($url);
}

/**
 * crop image 
 * @author latada
 * @param $nw string new width
 * @param $nh string new height
 * @param $source string source file
 * @param $dest string destination folder
 */
function cropImage($nw, $nh, $source, $dest) {
    $image_info = getimagesize($source);
    $w = $image_info[0];
    $h = $image_info[1];

    switch($image_info['mime']) {
        case 'image/gif':
            $simg = imagecreatefromgif($source);
        break;
        case 'image/jpeg':
            $simg = imagecreatefromjpeg($source);
        break;
        case 'image/pjpeg':
            $simg = imagecreatefromjpeg($source);
        break;
        case 'png':
            $simg = imagecreatefrompng($source);
        break;
    }

    $dimg = imagecreatetruecolor($nw, $nh);
    $wm = $w/$nw;
    $hm = $h/$nh;
    $h_height = $nh/2;
    $w_height = $nw/2;

    if($w> $h) {
        $adjusted_width = $w / $hm;
        $half_width = $adjusted_width / 2;
        $int_width = $half_width - $w_height;

        imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
    } elseif(($w <$h) || ($w == $h)) {
        $adjusted_height = $h / $wm;
        $half_height = $adjusted_height / 2;
        $int_height = $half_height - $h_height;
        imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
    } else {
        imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
    }
    imagejpeg($dimg,$dest,100);
}

/**
 * get option list
 * @param type $options
 * @param type $selected
 * @param type $type
 * @param type $name
 * @return string $temp_list
 */
function getOptions($options=array(),$selected='',$type='option',$name='option_list') {
    $tmp_list  = '';
    for($a=0; $a<count($options); $a++)
    {
        $set_select = '';
        if ($selected == $options[$a]) {
            $set_select = 'selected="selected"';
        }
        
        if ($type=='option') {
            $tmp_list .= '<option value="'.$options[$a].'" '.$set_select.'>'.$options[$a].'</option>';
        } else {
            $tmp_list .= '<label for="opt-'.$a.'"><input name="'.$name.'" id="opt-'.$a.'" value="'.$options[$a].'" type="'.$type.'"/>'.$options[$a].'&nbsp; </label>';
        } 
    }
    return $tmp_list;
}


function get_formatted_time($date){
    $CI=& get_instance();
    $CI->load->helper('date');
    $time=human_to_unix($date);

    $month_string="%F";
    $month_text=mdate($month_string,$time);

    $date_string="%j%S";
    $date_text=mdate($date_string,$time);
	      
    $year_string="%Y";
    $year_text=mdate($year_string,$time);
		
    return $month_text.' '.$date_text.', '.$year_text;
}

/**
* get cms breadcrumbs
* @param string $path_menu
* @return array $return 
*/
function getBreadcrumbs($path_menu) {
   $CI=& get_instance();
   $CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
   $return[] = array(
       'text'  => 'Home',
       'href'  => site_url(getAdminFolder().'/home'),
       'class' => ''
   );
   $id_parent = $CI->Adminmenu_model->getAdminMenuIdByFile($path_menu);
   $menu = $CI->Adminmenu_model->getBreadcrumbs($id_parent);
   $return = array_merge($return,$menu);
   return $return;
}
    
/**
 * get cms breadcrumbs
 * @param string $path_menu
 * @return array $return 
 */
function getMicrositeBreadcrumbs($path_menu) {
    $CI=& get_instance();
    $CI->load->model(getMicroAdminFolder().'/adminmenu_model','Adminmenu_model');
    $return[] = array(
	'text'  => 'Home',
	'href'  => site_url(getMicroAdminFolder().'/home'),
	'class' => ''
    );
    $id_parent = $CI->Adminmenu_model->getAdminMenuIdByFile($path_menu);
    $menu = $CI->Adminmenu_model->getBreadcrumbs($id_parent);
    $return = array_merge($return,$menu);
    return $return;
}

/**
 * get cms front breadcrumbs
 * @param string $keyword
 * @return array $return 
 */
function getFrontBreadcrumbs($keyword) {
    $CI=& get_instance();
    $CI->load->model('Pages_model');
    $CI->load->model('Site_model');
    $return[] = array(
	'text'  => 'Home',
	'href'  => base_url(),
	'class' => ''
    );
    return $return;
}

/**
 * get cms child menu
 * @param int $id_group
 * @param int $id_parent
 * @param string $sub_menu
 * @return string $sub_menu 
 */
function print_child_menu($id_group, $id_parent, $sub_menu='') {
   $CI=& get_instance();
   $CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
   $query = $CI->Adminmenu_model->GetMenuAdminByGroup($id_group,$id_parent);

   if ($query->num_rows()>0)
   {
       $sub_menu .= '<ul class="dropdown">';
       foreach($query->result_array() as $row)
       {
	   $has_child = '';
	   
	   if ($CI->Adminmenu_model->CheckMenuChild($id_group,$row['id_menu_admin'])) {
	       $has_child = ' class="has-dropdown"';
	   }
	   $sub_menu .= '<li'.$has_child.'>';
	   if ($row['file'] == "#" || $row['file'] == '') $sub_menu .= '<a href="#">'.$row['menu'].'</a>';
	   else  $sub_menu .= '<a href="'.site_url(getAdminFolder().'/'.$row['file']).'">'.$row['menu'].'</a>';

	   $sub_menu .= print_child_menu($id_group, $row['id_menu_admin']);

	   $sub_menu .= '</li>';
	   //$sub_menu .= '<li class="divider"></li>';
       }
       $sub_menu .= '</ul>';
   }
   return $sub_menu;
}

function get_adm_location() {
	$CI =& get_instance();
	$CI->load->library('session');
	$id = adm_sess_userid();
	if($id) {
		$where = array('id_auth_user' => $id);
		$cuser = $CI->db->get_where('auth_user',$where);
		if($cuser->num_rows()>0) {
			$cuser 		= $cuser->row_array();
			$alocation 	= $CI->db->select('id_ref_location as id')->get('ref_location')->result_array();
			$buf 		= array();	
			foreach($alocation as $v){
				if(is_array($v)) {
					foreach($v as $v1) {
						$buf[] = $v1;
					}
				}
			}
			return ($cuser['ref_location'] == 0) ? $buf : array($cuser['ref_location']);
		}
	}
	$CI->session->set_flashdata('error_login',alert_box('Sorry Your session has expired,please relogin','alert'));
	redirect('admpage/login');
}


?>
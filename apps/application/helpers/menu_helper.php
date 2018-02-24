<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper functions that handle menu
 * @author Faisal Latada mac_ [at] gxrg [dot] org
 * @Category Helper
 */

 /**
 * Generate Top menu
 * @author Latada
 * @param Void
 * @param Void
 * @return string html list menu
 */
function menus(){
	page_title();
	$CI = & get_instance();
	$ctrl = $CI->uri->segment(1);
	$func = $CI->uri->segment(2);
	// get_footer_menu();
	$CI->load->database();
	$menu	= '';
	if($ctrl=='admpage'){
		$user_sess	= $CI->session->userdata('ADM_SESS');
		$id_group 	= $user_sess['admin_id_auth_user_group'];
		$id_user 	= $user_sess['admin_id_auth_user'];
		$ROOT 		= 0;
		
		$CI->data['nama_admin'] 	= $user_sess['admin_name'];
		$CI->data['logout_url'] 	= site_url(getAdminFolder().'/login/logout');
		$CI->data['profile_url'] 	= site_url(getAdminFolder().'/profile');
		$CI->data['head_title'] 	= get_setting('app_title');

		$CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
		
		$query = $CI->Adminmenu_model->GetMenuAdminByGroup($id_group,$ROOT);
		$admin_menu = '';
		$a = 0;
		foreach($query->result_array() as $row) {
			$a++;
			$has_child = '';
			if ($CI->Adminmenu_model->CheckMenuChild($id_group,$row['id_menu_admin'])) {
			    $has_child = ' class="has-dropdown"';
			}
			$admin_menu .= '<li'.$has_child.'>';
			if ($row['file'] == '#' || $row['file'] == '') $admin_menu .= '<a href="#" title="'.$row['menu'].'">'.$row['menu'].'</a>';
			else $admin_menu .= '<a href="'.site_url(getAdminFolder().'/'.$row['file']).'" title="'.$row['menu'].'">'.$row['menu'].'</a>';
			
			$admin_menu .= print_child_menu($id_group, $row['id_menu_admin']);
			
			$admin_menu .= '</li>';
			$admin_menu .= '<li class="divider"></li>';
		}
		
		$menu = $admin_menu;
	}
	else{
			
		$CI->load->model("model_ref_menu");

		// get banner only for ctrl
		$CI->data['banner'] = '';
		if( $ctrl && !$func ) {
			
			// $CI->db->select('banner.*,themes.themes,themes.collumns');
			// $CI->db->join('banner','ref_menu.id_ref_menu=banner.id_ref_menu');
			// $CI->db->join('themes','themes.id_themes=banner.theme');
			// $CI->db->where('path',$ctrl);
			
			// $banner_query=$CI->db->get('ref_menu');
			// if($banner_query->num_rows()>0){
				// foreach($banner_query->result_array() as $banner_row){
					// $id_banner=$banner_row['id_banner'];
					// $CI->db->where('id_banner',$id_banner);
					// $image_query=$CI->db->get('banner_image');
					
				// }
			// }
			
			// if($banner_query->num_rows()) {
				// $CI->data['banner'] = $image_query->result_array();
			// }
			
		}
		
		$query 			= $query = $CI->model_ref_menu->getMenuHeader();
		$query 			= $query->result_array();
		foreach($query as $k => $row){
			$has_drop 	= '';
			$query2 	= $CI->model_ref_menu->getMenuHeaderBy($row['id_menu']);
			$sub_menu	= '';
			if ($query2->num_rows() > 0){
				$sub_menu.='<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$row['menu'].' <b class="caret"></b></a>
							<ul class="dropdown-menu img-nav">
							';
				foreach($query2->result_array() as $row2){
					$new		= '';
					$sub_menu2= '';
					$query3 = $CI->model_ref_menu->getMenuHeaderBy($row2['id_menu']);
					if ($query3->num_rows() > 0){
						$sub_menu2.='<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$row2['menu'].' <b class="caret"></b></a>
									<ul class="dropdown-menu img-nav">
									';
						foreach($query3->result_array() as $row3){
							 $sub_menu2.=item_menu($row3['menu'],$row3['path'],'',$row3['menu']);
						}
						$sub_menu2.='</ul></li>';
					}
					$sub_menu .= item_menu($row2['menu'],$row2['path'],$sub_menu2,$row2['menu']);
				}
				$sub_menu.='</ul></li>';
			}
			
			if(!isset($query[$k+1])) {
				$last = 'class="last"';
			}else {
				$last = '';
			}
			
			if($row['is_static']) {
				$isstatic = base_url() . 'statics/view/' . $row['path'];
			}else {
				$isstatic = '';
			}
			
			$menu.= item_menu($row['menu'],$row['path'],$sub_menu,'',$last,$isstatic);
			
		}
	}
	
	$CI->data['base_url'] 			= base_url();
	$CI->data['menu'] 				= $menu;
	$ctrl							= $CI->uri->segment(1);
	if($ctrl=='admpage' || $ctrl == 'partner' || $ctrl == 'member'){
		$file							= $CI->uri->segment(2);
		$ctrl							= base_url().$ctrl.'/'.$file.'/';
	}
		elseif($ctrl){
			$file							= $ctrl;
			$ctrl							= ($CI->uri->segment(2)) ? base_url().$ctrl.'/'.$CI->uri->segment(2).'/' : base_url().$ctrl.'/';
		}
	$CI->data['this_controller']		= $ctrl;
}

/**
 * create HTML Item Menu
 * @author Latada
 * @param $menu string nama menu
 * @param $file string link menu
 * @param $sub_menu (optional) string html submenu
 * @return item menu dengan sub menu(jika ada)
 */
function item_menu($menu,$file,$sub_menu='',$pict='',$last='',$isstatic=''){
	$CI 			= & get_instance();
	$ctrl 			= $CI->uri->segment(1);
	$funct			= $CI->uri->segment(2);
	$active = '';
	$is_admin = ($ctrl == 'webcontrol') ? 'webcontrol/' : '';//$lang.'/';
	if($ctrl == $menu || $ctrl == $file) {
	$active = 'active';

	}

	if(!$ctrl && $file == 'home'){
	$active = 'active';
	}

	if($file == 'home') {
	$menu = "<img src='".base_url()."img/home-link.png' title='$file' width='18' height='16' />";
	}

	$n_p = '';

	// $link 	= ($file != '#') ? base_url().$is_admin.$file: $file;
	if($file != '#') {
	if($isstatic) {
		$link = $isstatic;
	}else {
		$link = base_url().$is_admin.$file;
	}

	}else {
		$link = $file;
	}


	if($sub_menu != '') {
		return $sub_menu;
	}else {
	 
		if($file == 'gallery/photo' || $file == 'gallery/video') {
			return "<li $last><a href='".$link."'>
                                    <img src='".base_url() . "uploads/banner/$pict' width='48' height='48' >
                                    <div class='gal-title'>$menu</div>
                                    </a></li>";
		}else {
	 
			 return '<li '.$last.'>
						 <a class="'.$active.'" title="'.$menu.'" href="'.$link.'" '.$n_p.'>
						 '.$menu.'</a>'.$sub_menu.'	
					</li>';
						//<span class="text">'.$menu.'</span></a>'.$sub_menu.'	
		}
	}
	
}

/**
 * create HTML Item Menu
 * @author Latada
 * @param $menu string nama menu
 * @param $file string link menu
 * @param $sub_menu (optional) string html submenu
 * @return item menu dengan sub menu(jika ada)
 */
function get_footer_menu() {
	$CI 	=& get_instance();
	$foot 	= $CI->db->get_where('ref_menu',"type_menu = 'footer' AND is_static = '1' AND id_ref_publish = '2'");
	if($foot->num_rows()) {
		
		$foot = $foot->result_array();
		$buff = '';
		foreach($foot as $k => $v) {
			$buff .= '<li><a href="'.base_url() .'statics/view/'.$v['path'].'">' . $v['menu_title'] . '</a></li>';
		}
		
	}
	$CI->data['footer'] = $buff;
}

/**
 * Create HTML Breadcrumb
 * @author Latada
 * @param $menu string nama menu
 * @param $file string link menu
 * @param $sub_menu (optional) string html submenu
 * @return string Breadcrumb
 */
function breadcrumb($name='',$link=''){
	$CI 									 = & get_instance();
	$ctrl								 = $CI->uri->segment(1);
	//$funct							 = $CI->uri->segment(3);

	if($name != ''){
		$breadcrumb 					.= ($link != '') ? " &raquo <a href='$link'>$name</a>" : " &raquo $name";
	  
	}else{
		$breadcrumb						.= "<a href='".base_url()."webcontrol/home'>Home</a>";
		$data 							 = $CI->db->get_where('ref_menu',"path = '$ctrl'")->row_array();
		// not root
	if($data['id_parents_menu_admin'] != ''){
		$parent 					 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin = '$data[id_parents_menu_admin]'")->row_array();
		if($parent['id_parents_menu_admin'] != ''){
			$parent_lagi 		 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin = '$parent[id_parents_menu_admin]'")->row_array();
			$breadcrumb 		.= link_breadcrumb($parent_lagi['file'],$parent_lagi['menu_title']);
		}
		$breadcrumb 			.= link_breadcrumb($parent['path'],$parent['menu_title']);
	}
	$breadcrumb 					.= link_breadcrumb($data['path'],$data['menu_title']);
	}
	if($ctrl == 'home') $breadcrumb = '';
	$CI->data['breadcrumb'] 		.= $breadcrumb;
}

/**
 * Create Link Breadcrumb
 * @author Latada
 * @param $menu string nama menu
 * @param $file string link menu
 * @param $sub_menu (optional) string html submenu
 * @return string link or text Breadcrumb
 */
function link_breadcrumb($link,$name){
	if($name != ''){	 
		if($link =='#'){
			return ' &raquo; '.$name;
		}
		else{
			return " &raquo; <a href='".base_url()."webcontrol/$link'>$name</a>";
		}
	}
}

/**
 * Fetch Language
 * @author Latada
 * @param $void
 * @return void
 */
function get_lang(){
	$CI = & get_instance();
	$lang = $CI->uri->segment(1);
	$func = $CI->uri->segment(2);
	$current_link	= current_url();
	if($lang != 'webcontrol' && $lang != 'partner' && $lang != 'member'){
		$lang = ($lang) ? $lang : 'in';
		$CI->lang->load('text', $lang);
		$CI->data = array_merge($CI->lang->language,$CI->data);
	}
	$list_lang = $CI->db->get('language')->result_array();
	$n = 0;
	$slash = ($func) ? '/' : '';
	$curlang = '/'.$lang.$slash;
	foreach($list_lang as $bhs){
		$CI->data['list_lang'][$n]['language'] 		= $bhs['language'];
		$CI->data['list_lang'][$n]['current_link']	= str_replace($curlang,'/'.$bhs['lang'].$slash,$current_link);
		$CI->data['list_lang'][$n]['sp'] 				= ($n == count($list_lang)-1) ? '' : '|';
		++$n;
	}
	 
}

/**
 * Create Page Title
 * @author Latada
 * @param Void
 * @return Void
 */
function page_title(){
	$CI 		= & get_instance();
	$ctrl		= $CI->uri->segment(1);
	$fnc		= $CI->uri->segment(2);

	if($ctrl && !isset($fnc) ){
		$title=$CI->db->get_where('menu',"path = '$ctrl'")->row_array();
		$CI->data['breadcrumb_name'] = ($title) ? $title['menu'] : $ctrl;
	} 
	if(isset($fnc) ) {
		$title=$CI->db->get_where('menu',"path = '$fnc'")->row_array();
		$CI->data['breadcrumb_name'] = ($title) ? $title['menu'] : $ctrl;
	}
	if(!$ctrl) {
		$CI->data['breadcrumb_name'] = 'Home';
	}
}

// function init() {
		// $CI =& get_instance();
        // $CI->vbulletin = $GLOBALS['vbulletin'];
		// $CI->userdm =& datamanager_init('User', $CI->vbulletin, 'ERRTYPE_ARRAY');

// }
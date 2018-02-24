<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*************************************//**
  * Global_libs Class
  * @author latada
  * @email mac_ [at] gxrg [dot] org
  * @category Library
  * @desc this library contain header and footer for template page and auth access page (acl)
*************************************/

class Global_libs {
    private $folder;
    private $login_url;
    private $profile;
    
    /**
     * contructor
     */
    public function __construct() {
        $CI=& get_instance();
        $CI->load->library("parser");
        $CI->load->library("session");
        $CI->load->helper("url");
        $CI->load->database("default",TRUE);
		  
        $this->folder 	 = getAdminFolder();
        $this->microsite = getMicroAdminFolder();
        $this->login_url = 'login/logout';
        $this->profile 	 = 'profile';
		$ctrl = $CI->uri->segment(1);
		$fnct = $CI->uri->segment(2);
		if($ctrl == 'admpage' && !($fnct)) {
			redirect($ctrl . '/login');
		}
    }
    
    public function print_front_header(){
		$CI=& get_instance();
		
		//Header Stuffs
			$head_title = get_setting('app_title',$id_site=8);
			$head_keywords=get_setting('app_title',$id_site=8);
			$head_desc=get_setting('web_description',$id_site=8);
			$head_title=get_setting('head_title',$id_site=8);
			//End Header Stuffs
		
		//Slideshow at landing
		$CI->load->model('slideshow_model');
			$slideshow_markup=$CI->slideshow_model->generate_slideshow_image_markup($limit=10,$id_site=8);
			//End Slideshow at landing
		
		$data_footer=array(
				   'base_url'=>base_url(),
				   'head_desc'=>$head_desc,
				   'slideshow_markup'=>$slideshow_markup,
				   'head_title' => $head_title
				   );
		$CI->parser->parse('site/layout/header.html', $data_footer);
    }
    
    public function print_front_footer(){
		$CI=& get_instance();
		$data_footer=array('base_url'=>base_url());
		$CI->parser->parse('site/layout/footer.html', $data_footer);
    }
    
    /**
     * get cms breadcrumbs
     * @param string $path_menu
     * @return array $return 
     */
    public function getBreadcrumbs($path_menu) {
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
    public function getMicrositeBreadcrumbs($path_menu) {
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
    public function getFrontBreadcrumbs($keyword) {
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
     * print header cms
     */
    public function print_header() {
        $CI=& get_instance();
        $CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
        # auth access
        $this->auth_menu();
		  
        $user_sess = $CI->session->userdata('ADM_SESS');
        $id_group = $user_sess['admin_id_auth_user_group'];
        $id_user = $user_sess['admin_id_auth_user'];
        $ROOT = 0;
		  
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
            else $admin_menu .= '<a href="'.site_url($this->folder.'/'.$row['file']).'" title="'.$row['menu'].'">'.$row['menu'].'</a>';
            
            $admin_menu .= $this->print_child_menu($id_group, $row['id_menu_admin']);
            
            $admin_menu .= '</li>';
            $admin_menu .= '<li class="divider"></li>';
        }
        
        //$menu_pages = $this->print_front_menu();
		$menu_pages = $this->menu_front();
        
        $logout_url 	= site_url($this->folder.'/'.$this->login_url);
        $profile_url = site_url($this->folder.'/'.$this->profile);
        $data_header = array(
            'base_url'   => base_url(),
            'current_url'   => current_url(),
            'logout_url' => $logout_url,
            'profile_url' => $profile_url,
            'menu' => $admin_menu,
            'menu_small' => $admin_menu,
            'menu_pages'=>$menu_pages,
            'head_title' => get_setting('app_title'),
            'nama_admin' => $user_sess['admin_name']
        );
        $CI->parser->parse($this->folder.'/layout/header.html', $data_header);
    }

    /**
     * print child menu
     * @param int $id_group
     * @param int $id_parent
     * @param int $sub_menu
     * @return string $sub_menu sub menu
     */
    public function print_child_menu($id_group, $id_parent, $sub_menu='') {
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
                else  $sub_menu .= '<a href="'.site_url($this->folder.'/'.$row['file']).'">'.$row['menu'].'</a>';

                $sub_menu .= $this->print_child_menu($id_group, $row['id_menu_admin']);

                $sub_menu .= '</li>';
                //$sub_menu .= '<li class="divider"></li>';
            }
            $sub_menu .= '</ul>';
        }
        return $sub_menu;
    }
    
    /**
     *
     * @param int $id_parent
     * @return string $tmp_return 
     */
    public function print_front_menu($id_parent=0) {
        $tmp_return = '';
        $CI=& get_instance();
        $CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
        $query = $CI->Adminmenu_model->getAllFrontMenu($id_parent);
        if ($query->num_rows()>0) {
            foreach($query->result_array() as $row) {
                $tmp_return .= '<li>';
                if ($row['page_type'] == "2") 
                {
                    $links = site_url($this->folder.'/'.$row['module']);
                } else {
                    if ($row['menu_path'] == '') {
                        $links = '#';
                    } else {
                        $links = site_url($this->folder.'/pages/edit/'.$row['id_static_pages']);
                    }
                }
                $tmp_return .= '<a href="'.$links.'">'.$row['menu_title'].'</a>';
                
                if ($this->front_menu_haschild($row['id_static_pages'])) {
                    $tmp_return .= '<ul>';
                    $tmp_return .= $this->print_front_menu($row['id_static_pages']);
                    $tmp_return .= '</ul>';
                } else {
                    $tmp_return .= $this->print_front_menu($row['id_static_pages']);
                }
            }
            $tmp_return .= '</li>';
        }
        return $tmp_return;
    }
    
    /**
     * check if front menu has child
     * @param int $id_parent
     * @return bool true/false 
     */
    private function front_menu_haschild($id_parent) {
        $CI=& get_instance();
        $CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
        $query = $CI->Adminmenu_model->getAllFrontMenu($id_parent);
        if ($query->num_rows()>0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function menu_front(){
	$print_front='';
	$CI=& get_instance();
	$CI->load->model(getAdminFolder().'/Adminmenu_model');
	$CI->load->model(getAdminFolder().'/Pages_model');
	$query=$CI->Pages_model->getAllSites();
	if($query){
		foreach($query as $row_site){
			$id_site=$row_site['id_site'];
			$site_name=$row_site['site_name'];
			$site_url=($site_name=="Nava+")?'navaplus':$row_site['site_url'];
			$print_front .='<button type="button" class="sidenav" data-toggle="collapse" data-target="#'.$site_url.'">';
			$print_front .=$site_name.'<img src="'.base_url().'assets/images/admin/down.png" style="height:20px; float:right;"></button>';
			// $print_front .=$CI->Adminmenu_model->Get_Front_Page($id_site,0,$site_url,'');
			
		}
	}
	
	return $print_front;
		  
    }


    /**
     * print footer cms
     */
    public function print_footer() {
        $dt = date("Y");
	$CI=& get_instance();
        $data_footer['footer_text']=sprintf(get_setting('app_footer'),date('Y'));
        $data_footer['base_url']=base_url();
        $CI->parser->parse($this->folder.'/layout/footer.html', $data_footer);
    }
    
    /**
     * print header cms
     */
    public function print_microsite_header() {
        $CI=& get_instance();
        $CI->load->model('Adminmenu_model','MenuAdmin');
        # auth access
        $set_session = 'MICROSITE_SESS';
        $this->auth_menu($set_session);
		  
        $user_sess = $CI->session->userdata('MICROSITE_SESS');
        $id_group = $user_sess['microadmin_id_auth_user_group'];
        $id_user = $user_sess['microadmin_id_auth_user'];
        $ROOT = 0;
		  
        $query = $CI->MenuAdmin->GetMenuAdminByGroup($id_group,$ROOT);
        $admin_menu = '';
        $a = 0;
        foreach($query->result_array() as $row) {
            $has_child = '';
            if ($CI->MenuAdmin->CheckMenuChild($id_group,$row['id_menu_admin'])) {
                $has_child = ' class="has-dropdown"';
            }
            $admin_menu .= '<li'.$has_child.'>';
            if ($row['file'] == '#' || $row['file'] == '') $admin_menu .= '<a href="#" title="'.$row['menu'].'">'.$row['menu'].'</a>';
            else $admin_menu .= '<a href="'.site_url($this->microsite.'/'.$row['file']).'" title="'.$row['menu'].'">'.$row['menu'].'</a>';
            
            $admin_menu .= $this->print_child_menu($id_group, $row['id_menu_admin']);
            
            $admin_menu .= '</li>';
            $admin_menu .= '<li class="divider"></li>';
        }
        $logout_url 	= site_url($this->microsite.'/'.$this->login_url);
        $profile_url = site_url($this->microsite.'/'.$this->profile);
        
        // left widget
        $CI->load->model('micrositecontrol/article_model','Article_model');
        $member_name = $user_sess['microadmin_name'];
        $member_email = $user_sess['microadmin_email'];
        $member_last_login = iso_date_time($user_sess['microadmin_last_login']);
        $total_article_user = $CI->Article_model->countAllUserArticle($id_user);
        $total_draft_article_user = $CI->Article_model->countAllUserDraftArticle($id_user);
        $total_published_article_user = $CI->Article_model->countAllUserPublishedArticle($id_user);
        
        
        $data_header = array(
            'base_url'   => base_url(),
            'current_url'   => current_url(),
            'logout_url' => $logout_url,
            'profile_url' => $profile_url,
            'menu' => $admin_menu,
            'head_title' => get_setting('app_title'),
            'nama_admin' => $user_sess['microadmin_name'],
            'member_name' => $member_name,
            'member_email' => $member_email,
            'member_last_login' => $member_last_login,
            'total_article_user' => $total_article_user,
            'total_draft_article_user' => $total_draft_article_user,
            'total_published_article_user' => $total_published_article_user,
        );
        $CI->parser->parse($this->microsite.'/layout/header.html', $data_header);
    }
    
    /**
     * print footer cms
     */
    public function print_microsite_footer() {
        $dt = date("Y");
	$CI=& get_instance();
        $data_footer['footer_text']=sprintf(get_setting('app_footer'),date('Y'));
        $data_footer['base_url']=base_url();
        $CI->parser->parse($this->microsite.'/layout/footer.html', $data_footer);
    }
    
    /**
     * authenticate menu
     */
    public function auth_menu($set_session='ADM_SESS') {
		$CI=& get_instance();
        $CI->load->model(getAdminFolder().'/adminmenu_model','Adminmenu_model');
        $CI->load->model(getAdminFolder().'/authgroup_model','Authgroup_model');
        
        $user_sess = $CI->session->userdata($set_session);
        if ($set_session == 'ADM_SESS') {
            $group 	 = $user_sess['admin_id_auth_user_group'];
            $id_user = $user_sess['admin_id_auth_user'];
        } else {
            $group 	 = $user_sess['microadmin_id_auth_user_group'];
            $id_user = $user_sess['microadmin_id_auth_user'];
        }
        
        if ($user_sess != "") 
        {
            $id_ref_menu = NULL;
            $ref_menu = $CI->uri->segment(2);
            $ref_menu_detail = $CI->uri->segment(3);
            
            $query = $CI->Adminmenu_model->GetMenuAdminByFile($ref_menu);
            
            if($query->num_rows() > 0)
            {
                $row = $query->row_array();
                $id_menu_admin = $row['id_menu_admin'];
                
                $query2 = $CI->Authgroup_model->GetMenuByRef($id_menu_admin,$group);
                
                if($query2->num_rows() == 0)
                {
                    show_404('page');
                }
            } 
            else 
            {
                if ($ref_menu != 'forbiden' && $ref_menu != 'home' && $ref_menu != 'profile' && $ref_menu != 'page404') {
                    show_404('page');
                }
            }
        } 
        else 
        {
            //redirect("webcontrol/login");
        }
    }
    
    
    
    
    ///**
    // * front end header 
    // * @param type $id_site
    // * @param type $site
    // * @param type $pathmenu 
    // */
    //public function print_front_header($id_site=1,$pathmenu='home',$module='') {
    //    $CI=& get_instance();
    //    $CI->load->database();
    //    $CI->load->library('session');
    //    $CI->load->model('Pages_model');
    //    $id_parent = 0;
    //    $list_navigation = array();
    //    $meta_tags = array();
    //    $action_subscribe = site_url('subscribe');
    //    $search_action = site_url('search/q');//http://www.wapikweb.org/kb/search
    //    $register_href = site_url('register');
    //    $login_href = site_url(getMicroAdminFolder().'/login');
    //    $user_profile_href = site_url(getMicroAdminFolder().'/home');
    //    
    //    if ($module != '') {
    //        $meta_tags = $CI->Pages_model->getModuleMetaTags($module,$pathmenu);
    //    } else {
    //        if ($pathmenu) {
    //            $meta_tags = $CI->Pages_model->getMetaTags($pathmenu);
    //        } else {
    //            $meta_tags = array(
    //                'keywords'=>get_setting('app_title',$id_site),
    //                'desc'=>get_setting('web_description',$id_site),
    //                'image'=>base_url('images/logo.png'),
    //            );
    //        }
    //    }
    //    $c_headerwrapp = ($pathmenu=='home') ? '' : 'inside';
    //    $c_sectionheader = ($pathmenu=='home') ? 'home' : 'inside';
    //    
    //    if (adm_microsite_sess_userid()) {
    //        $data_user = $CI->session->userdata('MICROSITE_SESS');
    //        
    //        $print_user_href = '
    //                        <a href="'.$user_profile_href.'">Welcome, '.$data_user['microadmin_name'].'</a>
    //                        <span></span>
    //                        <a href="'.site_url(getMicroAdminFolder().'/login/logout').'">Logout</a>
    //        ';
    //    } else {
    //        $print_user_href = '
    //                        <a href="'.$login_href.'">Login</a>
    //                        <span></span>
    //                        <a href="'.$register_href.'">Registrasi</a>
    //        ';
    //    }
    //    
    //    $navigations = $CI->Pages_model->getMenuNavigation($id_site);
    //    
    //    if ($navigations->num_rows()>0) {
    //        $j = 0;
    //        foreach ($navigations->result_array() as $navigation) {
    //            $j++;
    //            if ($j == $navigations->num_rows()) {
    //                $menu_span = '';
    //            } else {
    //                $menu_span = '<span></span>';
    //            }
    //            $menu_active = '';
    //            $menu_path = $navigation['menu_path'];
    //            $menu_title = $navigation['menu_title'];
    //            $is_type = $navigation['page_type'];
    //            if ($is_type == 2) {
    //                $menu_href = site_url($navigation['module']);
    //                $mod = 'module';
    //            } else {
    //                $menu_href = site_url('static/'.$navigation['menu_path']);
    //                $mod = 'path';
    //            }
    //            $ParentNav = $CI->Pages_model->GetParentNavigation($pathmenu,$mod,$id_site);
    //            if ($navigation['id_static_pages'] == $ParentNav)
    //            {
    //                $menu_active = 'class="active"';
    //            }
    //            //$menu_href = site_url('site/'.$site.'/'.$menu_path);
    //            //$menu_href_front = site_url('site/pages/'.$menu_path);
    //            $list_navigation[] = array(
    //                'menu_path'=>$menu_path,
    //                'menu_title'=>$menu_title,
    //                'menu_href'=>$menu_href,
    //                'menu_active'=>$menu_active,
    //                'menu_span'=>$menu_span,
    //            );
    //        }
    //    }
    //    
    //    $head_title = (!empty($meta_tags['keywords']) && $meta_tags['keywords'] != get_setting('app_title',$id_site)) ? $meta_tags['keywords'] : get_setting('app_title',$id_site);
    //    
    //    $data_header = array(
    //        'base_url'=>base_url(),
    //        'current_url'=>current_url(),
    //        'landing_url'=>site_url(),
    //        'head_title'=>$head_title,
    //        'meta_tag_keywords'=>$meta_tags['keywords'],
    //        'meta_tag_desc'=>$meta_tags['desc'],
    //        'meta_tag_image'=>$meta_tags['image'],
    //        'list_navigation'=>$list_navigation,
    //        'action_subscribe'=>$action_subscribe,
    //        'search_action'=>$search_action,
    //        'c_headerwrapp'=>$c_headerwrapp,
    //        'c_sectionheader'=>$c_sectionheader,
    //        'register_href'=>$register_href,
    //        'print_user_href'=>$print_user_href,
    //    );
    //    $CI->parser->parse('layout/header.html', $data_header);
    //}
    //
    //// front end footer 
    ///**
    // * front end footer 
    // * @param type $site
    // * @param type $id_site 
    // */
    //public function print_front_footer($id_site=1) {
    //    $CI=& get_instance();
    //    
    //    $CI->load->library('online_libs'); 
    //    
    //    $count_visitor = $CI->online_libs->countOnline();
    //    
    //    $list_navigation = array();
    //    
    //    $navigations = $CI->Pages_model->getFooterNavigation($id_site);
    //    
    //    if ($navigations->num_rows()>0) {
    //        foreach ($navigations->result_array() as $navigation) {
    //            $menu_path = $navigation['menu_path'];
    //            $menu_title = $navigation['menu_title'];
    //            $is_type = $navigation['page_type'];
    //            if ($is_type == 2) {
    //                $menu_href = site_url($navigation['module']);
    //            } else {
    //                $menu_href = site_url('static/'.$navigation['menu_path']);
    //            }
    //            //$menu_href = site_url('site/'.$site.'/'.$menu_path);
    //            //$menu_href_front = site_url('site/pages/'.$menu_path);
    //            $list_navigation[] = array(
    //                'menu_path'=>$menu_path,
    //                'menu_title'=>$menu_title,
    //                'menu_href'=>$menu_href,
    //            );
    //        }
    //    }
    //    
    //    $data_footer = array(
    //        'base_url'=>base_url(),
    //        'current_url'=>current_url(),
    //        'landing_url'=>site_url('landing'),
    //        'footer_title'=>  sprintf(get_setting('app_footer',$id_site),date('Y')),
    //        'list_navigation'=>$list_navigation,
    //        'count_visitor'=>$count_visitor,
    //    );
    //    $CI->parser->parse('layout/footer.html', $data_footer);
    //}
    
    
    /**
     * front end microsite header 
     * @param type $id_site
     * @param type $site
     * @param type $pathmenu 
     */
    public function print_site_header($id_site,$pathmenu='',$module='',$child='',$css_id_name='') {
        $CI=& get_instance();
        $CI->load->database();
        $CI->load->model('Pages_model');
        $id_parent = 0;
        $list_navigation = array();
        $meta_tags = array();
        $site_img = '';
        $action_subscribe = site_url('subscribe');
        $search_action = site_url('search/keywords');
	
	if($pathmenu==""){
	    $CI->db->where('static_pages.id_ref_publish',1);
	    $CI->db->where('static_pages.is_delete',0);
	    $CI->db->where('static_pages.is_header',0);
	    $CI->db->where('static_pages.is_footer',0);
	    $CI->db->join('pages_sites','pages_sites.id_static_pages=static_pages.id_static_pages');
	    $CI->db->where('pages_sites.id_site',$id_site);
	    $CI->db->limit(1);
	    $q=$CI->db->get('static_pages');
	    if($q->num_rows()>0){
		$pathmenu=$q->row()->menu_path;
	    }else{
		$pathmenu='';
	    }
	    
	}
	
	$site = $CI->Site_model->get_site_path_by_ID($id_site);
        if ($module != '') {
            $meta_tags = $CI->Pages_model->get_module_meta_tags($module,$pathmenu,$id_site);
        } else {
            if ($pathmenu) {
                $meta_tags = $CI->Pages_model->get_meta_tags($pathmenu,$id_site);
            } else {
                $meta_tags = array(
                    'keywords'=>get_setting('app_title',$id_site),
                    'desc'=>get_setting('web_description',$id_site),
                    'image'=>base_url('images/logo.png'),
                );
            }
	    
        }
	//echo $pathmenu;
	//print_r($meta_tags);
        
        // site logo
        $site_logo = $CI->Site_model->get_site_logo($id_site);
        if ($site_logo != '') $site_img = '<img src="'.base_url('uploads/site/'.$site_logo).'" width="115" alt="'.get_setting('app_title',$id_site).'"/>';
        else $site_img = '<span>'.get_setting('app_title',$id_site).'</span>';
        
        $navigations = $CI->Pages_model->get_navigation_menu($id_site);
        
        if ($navigations->num_rows()>0) {
            foreach ($navigations->result_array() as $navigation) {
                $menu_active = '';
                $menu_path = $navigation['menu_path'];
                $menu_title = $navigation['menu_title'];
                $is_type = $navigation['page_type'];
                if ($is_type == 2) {
                    $menu_href = site_url('site/'.$site.'/'.$navigation['module']);
                    $mod = 'module';
                } else {
                    $menu_href = site_url('site/'.$site.'/pages/'.$navigation['menu_path'].'/'.$css_id_name);
                    $mod = 'path';
                }
                $ParentNav = $CI->Pages_model->get_parent_navigation($pathmenu,$mod,$id_site);
                if ($navigation['id_static_pages'] == $ParentNav)
                {
                    $menu_active = 'class="active"';
                }
                //$menu_href = site_url('site/'.$site.'/'.$menu_path);
                //$menu_href_front = site_url('site/pages/'.$menu_path);
                $list_navigation[] = array(
                    'menu_path'=>$menu_path,
                    'menu_title'=>$menu_title,
                    'menu_href'=>$menu_href,
                    'menu_active'=>$menu_active,
                );
            }
        }
	
	
	$head_title = (!empty($meta_tags['keywords']) && $meta_tags['keywords'] != get_setting('app_title',$id_site)) ? $meta_tags['keywords'] : get_setting('app_title',$id_site);
	
	//echo $meta_tags['keywords'];
	
	$site=$CI->Site_model->get_site_path_by_ID($id_site);
	$site_landing_url=site_url('site/'.$site.'/return/'.$css_id_name);
	
	if ($CI->session->userdata('tmp_back_redirect') != '') {

	    if($child!=""){
	    
		$child_query=$CI->Pages_model->get_page_by_path($id_site,$pathmenu);
		if($child_query->num_rows()>0){
		    $id_parent=$child_query->row()->id_parent_pages;
		    $parent_query=$CI->Pages_model->get_page_by_id($id_parent,$is_content=1,$id_site);
		    if($parent_query->num_rows()>0){
			$parent_path=$parent_query->row()->menu_path;
			$parent_url=site_url('site/'.$site.'/pages/'.$parent_path.'/'.$css_id_name);
		    }else{
			$parent_url=site_url('site/'.$site.'/pages/'.$pathmenu.'/'.$css_id_name);
		    }
		}else{
		    if($module=="our_work"){
			$parent_url=site_url('site/'.$site.'/pages/'.$child.'/'.$css_id_name);
		    }else{
			$parent_url=site_url('site/'.$site.'/pages/'.$pathmenu.'/'.$css_id_name);
		    }
		}
		
		$back_link='<div class="back-to-case"><a  data-transition="flip" href="'.$parent_url.'"><img src="'.base_url().'assets/images/members/back-to-case.png" width="25px"/><span style="position: relative;top: -25px;left:30px;">Go Back</span></a></div>';
	    }else{
		$back_link='<div class="reverse"><a class="reverse" href="'.$CI->session->userdata('tmp_back_redirect').'" data-transition="flow" rel="external">Back to<br/><img src="'.base_url().'assets/images/nava-plus.png" width="150"/></a></div>';
	    }
	    $CI->session->unset_userdata('tmp_back_redirect');
        } else {
            if($child!=""){
	    
		$child_query=$CI->Pages_model->get_page_by_path($id_site,$pathmenu);
		if($child_query->num_rows()>0){
		    $id_parent=$child_query->row()->id_parent_pages;
		    $parent_query=$CI->Pages_model->get_page_by_id($id_parent,$is_content=1,$id_site);
		    if($parent_query->num_rows()>0){
			$parent_path=$parent_query->row()->menu_path;
			$parent_url=site_url('site/'.$site.'/pages/'.$parent_path.'');
		    }else{
			$parent_url=site_url('site/'.$site.'/pages/'.$pathmenu.'');
		    }
		}else{
		    $parent_url=site_url('site/'.$site.'/pages/'.$pathmenu.'');
		}
		
		$back_link='<div class="back-to-case"><a  data-transition="flip" href="'.$parent_url.'"><img src="'.base_url().'assets/images/members/back-to-case.png" width="25px"/><span style="position: relative;top: -25px;left:30px;">Go Back</span></a></div>';
	    }else{
		$back_link='<div class="reverse"><a class="reverse" href="'.base_url().'#key-member" data-transition="flow" rel="external">Back to<br/><img src="'.base_url().'assets/images/nava-plus.png" width="150"/></a></div>';
	    }
        }
	
	//if($child!=""){
	//    
	//    $child_query=$CI->Pages_model->get_page_by_path($id_site,$pathmenu);
	//    if($child_query->num_rows()>0){
	//	$id_parent=$child_query->row()->id_parent_pages;
	//	$parent_query=$CI->Pages_model->get_page_by_id($id_parent,$is_content=1,$id_site);
	//	if($parent_query->num_rows()>0){
	//	    $parent_path=$parent_query->row()->menu_path;
	//	    $parent_url=site_url('site/'.$site.'/pages/'.$parent_path.'');
	//	}else{
	//	    $parent_url=site_url('site/'.$site.'/pages/'.$pathmenu.'');
	//	}
	//    }else{
	//	$parent_url=site_url('site/'.$site.'/pages/'.$pathmenu.'');
	//    }
	//    
	//    $back_link='<div class="back-to-case"><a  data-transition="flip" href="'.$parent_url.'"><img src="'.base_url().'assets/images/members/back-to-case.png" width="25px"/><span style="position: relative;top: -25px;left:30px;">Go Back</span></a></div>';
	//}else{
	//    $back_link='<div class="reverse"><a class="reverse" href="'.base_url().'#key-member" data-transition="flow" rel="external">Back to<br/><img src="'.base_url().'assets/images/nava-plus.png" width="150"/></a></div>';
	//}
	//     
        $data_header = array(
            'base_url'=>base_url(),
            'current_url'=>current_url(),
            'landing_url'=>site_url(),
            'site_url'=>site_url('site/'.$site),
            'head_title'=>$head_title,
            'meta_tag_keywords'=>$meta_tags['keywords'],
            'meta_tag_desc'=>$meta_tags['desc'],
            'meta_tag_image'=>$meta_tags['image'],
            'site_img'=>$site_img,
            'list_navigation'=>$list_navigation,
            'action_subscribe'=>$action_subscribe,
            'search_action'=>$search_action,
	    'site_landing_url'=>$site_landing_url,
	    'back_link'=>$back_link,
        );
        $CI->parser->parse('microsite/layout/header.html', $data_header);
    }
    
    // front end microsite footer 
    /**
     * front end footer 
     * @param type $site
     * @param type $id_site 
     */
    public function print_site_footer($id_site) {
        $CI=& get_instance();
	
	$site = $CI->Site_model->get_site_path_by_ID($id_site);
        
        $list_navigation = array();
        
        $navigations = $CI->Pages_model->get_footer_avigation($id_site);
	
	if ($navigations->num_rows()>0) {
            foreach ($navigations->result_array() as $navigation) {
                $menu_path = $navigation['menu_path'];
                $menu_title = $navigation['menu_title'];
                $is_type = $navigation['page_type'];
                if ($is_type == 2) {
                    $menu_href = site_url('site/'.$site.'/'.$navigation['module']);
                } else {
                    $menu_href = site_url('site/'.$site.'/pages/'.$navigation['menu_path']);
                }

                $list_navigation[] = array(
                    'menu_path'=>$menu_path,
                    'menu_title'=>$menu_title,
                    'menu_href'=>$menu_href,
                );
            }
        }
        
        $data_footer = array(
            'base_url'=>base_url(),
            'current_url'=>current_url(),
            'site_url'=>site_url('site_url/'.$site),
            'landing_url'=>site_url(),
            'footer_title'=>  sprintf(get_setting('app_footer',$id_site),date('Y')),
            'list_navigation'=>$list_navigation,
        );
        $CI->parser->parse('microsite/layout/footer.html', $data_footer);
    }
}


/* End of file global_libs.php */
/* Location: ./application/libraries/global_libs.php */


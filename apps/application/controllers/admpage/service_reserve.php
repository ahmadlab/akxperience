<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Service Reserve Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Service Reserve Management
 *********************************************/
class Service_reserve extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $template;
    private $path;
    private $path_uri;
    private $title;
    private $id_menu_admin;
    private $is_superadmin;
    private $location_id;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
        $this->ctrl = 'service_reserve';
        $this->template = getAdminFolder() . '/modules/service_reserve';
        $this->path_uri = getAdminFolder() . '/service_reserve';
        $this->path = site_url(getAdminFolder() . '/service_reserve');
        $this->title = get_admin_menu_title('service_reserve');
        $this->id_menu_admin = get_admin_menu_id('service_reserve');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->location_id = get_adm_location();
        $this->load->model(getAdminFolder() . '/Reserve_model', 'Reserve_model');
    }

    /**
     * index page = main page
     */
    public function index()
    {
        $this->main();
    }

    /**
     * main page = index page
     */
    public function main()
    {
        auth_admin();
        $this->session->set_userdata("referrer", current_url());
        $this->global_libs->print_header();
        $s_date = $this->uri->segment(4);
        $s_status = $this->uri->segment(5);
        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 7;
        $no = 0;
        $path = $this->path . '/main/a/b/';
        $list_admin_arr = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $path_uri = $this->path_uri;
        $template = $this->template;
        $breadcrumbs = array();
        $add_btn = site_url($path_uri . '/add');
        $is_superadmin = $this->is_superadmin;
        $list_data = array();
        $wtype = $wseries = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_date) > 1) {
            $s_date = substr($s_date, 1);

        } else {
            $s_date = "";

        }
        if (strlen($s_status) > 1) {
            $s_status = substr($s_status, 1);

        } else {
            $s_status = "";

        }

        $l_date = selectlist('user', 'id_user', 'username', null, $s_date, '--- Chooice Date ---');
        $l_status = $this->list_status($s_status);
        $total_records = $this->Reserve_model->TotalServiceReserve(myUrlDecode($s_date), myUrlDecode($s_status),
            $this->location_id);

        if ($s_date) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_date . "/", $path);
        }
        if ($s_status) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_status . "/", $path);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Reserve_model->GetAllServiceReserve(myUrlDecode($s_date), myUrlDecode($s_status), $lmt,
            $per_page, $this->location_id);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id = $buffs['id_service_booking'];
            $usr = $buffs['username'];
            $car = $buffs['car'];
            $timebook = $buffs['datetime_book'];
            $status = $buffs['status_book'];
            $create_date = $buffs['create_date'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'car' => $car,
                'create_date' => $create_date,
                'usr' => $usr,
                'timebook' => $timebook,
                'status' => $status,
                'edit_href' => $edit_href,
            );
        }

        // paging
        $paging = global_paging($total_records, $per_page, $path, $uri_segment);
        if (!$paging) {
            $paging = '<ul class="pagination"><li class="current"><a>1</a></li></ul>';
        }
        //end of paging

        $error_msg = alert_box($this->session->flashdata('error_msg'), 'error');
        $success_msg = alert_box($this->session->flashdata('success_msg'), 'success');
        $info_msg = alert_box($this->session->flashdata('info_msg'), 'warning');

        $datelist = $this->generate_datelist();

        // echo $datelist;

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            'l_date' => $l_date,
            'l_status' => $l_status,
            'list_data' => $list_data,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
            'datelist' => $datelist,
        );
        $this->parser->parse($template . '/list_' . $file_app . '.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * search post s_name and s_email
     */
    public function search()
    {
        auth_admin();
        $s_date = myUrlEncode(trim($this->input->post('s_date')));
        $s_status = myUrlEncode(trim($this->input->post('s_status')));
        redirect($this->path . '/main/a' . $s_date . '/b' . $s_status);
    }

    /**
     * add page
     */
    // public function add()
    // {
    // auth_admin();
    // if ( ($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm() )
    // {

    // $post 	= purify($this->input->post());
    // $data_post = array(
    // 'ref_user_car'	=> $post['ref_user_car'],
    // 'ref_location'	=> $post['ref_location'],
    // 'datetime_book'	=> $post['datetime_book'],
    // 'status_book'	=> $post['status_book'],
    // );

    // $id = $this->Reserve_model->InsertTestDriveReserve($data_post);


    // #insert to log
    // $log_id_user = adm_sess_userid();
    // $log_id_group = adm_sess_usergroupid();
    // $log_action = 'Add '.$this->title.' ID : '.$id;
    // $log_desc = 'Add '.$this->title.' ID : '.$id.'; Ref User Car:'.$post['ref_user_car'].'; Ref Location:'.$post['ref_location'] . '; Time Book :'.$post['datetime_book'] . '; Status Book :'.$post['status_book'];
    // $data_logs = array(
    // 'id_user'=>$log_id_user,
    // 'id_group'=>$log_id_group,
    // 'action'=>$log_action,
    // 'desc'=>$log_desc,
    // 'create_date'=>date('Y-m-d H:i:s'),
    // );
    // insert_to_log($data_logs);

    // $this->session->set_flashdata('success_msg',$this->title.' has been added');

    // redirect($this->path_uri);
    // }
    // $this->getForm();
    // }

    public function get_schedule()
    {
        echo global_available_time(date('Y-m-d', $this->input->post('time')), 'service', $this->path);
    }

    public function change_sch()
    {
        $stamp = $this->input->post('stamp');
        $label = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $stamp) {
            $where = array('datetime' => date('Y-m-d H:i:s', $stamp), 'type' => 'service');
            $sch = $this->db->get_where('unavailable_time', $where);

            if ($sch->num_rows() > 0) {
                $sch = $sch->row();
                $where['datetime'] = $sch->datetime;
                $this->db->where($where)->delete('unavailable_time');
                $label = 'Enabled';
            } else {
                $where['datetime'] = date('Y-m-d H:i:s', $stamp);
                $this->db->insert('unavailable_time', $where);
                $label = 'Disabled';
            }
        }
        echo $label;
    }

    public function list_status($selected = false)
    {
        $opt = "<option value=''>--- Status Book ---</option>";
        foreach (array('book', 'confirmed', 'cancel', 'finished') as $l) {
            $terpilih = ($selected == $l) ? 'selected' : '';
            $opt .= "<option $terpilih value='$l'>$l</option>";
        }
        return $opt;
    }


    /**
     * edit page
     * @param int $get_id
     */
    public function edit($get_id = 0)
    {
        auth_admin();
        $id = (int)$get_id;
        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->path_uri);
        }


        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $post = purify($this->input->post());

            $data_post = array(
                'date_next_visit' => $post['next_visit'],
                'service_notes' => $post['service_note'],
                'status_book' => $post['status_book'],
            );

            $this->Reserve_model->UpdateServiceReserve($data_post, $id);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $desc = '';
            foreach ($post as $k => $v) {
                $desc .= " $k :$v;";
            }
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; ' . $desc;
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', $this->title . ' has been updated');

            if ($this->session->userdata('referrer') != '') {
                redirect($this->session->userdata('referrer'));
            } else {
                redirect($this->path_uri);
            }
        }
        $this->getForm($id);
    }

    /**
     * get print form
     * @param int $id
     */
    private function getForm($id = 0)
    {
        $this->global_libs->print_header();

        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $path_uri = $this->path_uri;
        $template = $this->template;
        $id = (int)$id;
        $error_msg = array();
        $post = array();
        $group_list = '';
        $site_list = '';
        $cancel_btn = site_url($path_uri);
        $breadcrumbs = array();
        $idgroup = adm_sess_usergroupid();

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Reserve_model->GetServiceReserveById($id);
            if ($query->num_rows() > 0) {
                $info = $query->row_array();
                $info['ocheck'] = $info['dservice_note'] = $info['dnvisit'] = $info['dstat'] = 'disabled';
            } else {
                $this->session->set_flashdata('info_msg', 'There is no record in our database.');
                redirect($path_uri);
            }
        }

        if ($id) {
            $breadcrumbs[] = array(
                'text' => 'Edit',
                'href' => '#',
                'class' => 'class="current"'
            );
            $err_c = 'error-info ';
            $required = '';
            $pass_msg = '<small class="error-info">Please ignore this field if you don\'t want to change Password.</small>';
            $action = site_url($path_uri . '/edit/' . $id);
        } else {
            $breadcrumbs[] = array(
                'text' => 'Add',
                'href' => '#',
                'class' => 'class="current"'
            );
            $pass_msg = '';
            $err_c = '';
            $required = 'required';
            $action = site_url($path_uri . '/add');
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        // if (isset($this->error['ref_location'])) {
        // $error_msg['ref_location'] = alert_box($this->error['ref_location'],'error');
        // } else {
        // $error_msg['ref_location'] = '';
        // }

        if (isset($this->error['status_book'])) {
            $error_msg['status_book'] = alert_box($this->error['status_book'], 'error');
        } else {
            $error_msg['status_book'] = '';
        }

        if (isset($this->error['next_visit'])) {
            $error_msg['next_visit'] = alert_box($this->error['next_visit'], 'error');
        } else {
            $error_msg['next_visit'] = '';
        }

        if (isset($this->error['service_note'])) {
            $error_msg['service_note'] = alert_box($this->error['service_note'], 'error');
        } else {
            $error_msg['service_note'] = '';
        }


        // set value
        if ($this->input->post('status_book') != '') {
            $post['status_book'] = $this->input->post('status_book');
        } elseif ((int)$id > 0) {
            $post['status_book'] = $info['status_book'];
        } else {
            $post['status_book'] = '';
        }

        if ($this->input->post('service_note') != '') {
            $post['service_note'] = $this->input->post('service_note');
        } elseif ((int)$id > 0) {
            $post['service_note'] = $info['service_notes'];
        } else {
            $post['service_note'] = '';
        }

        if ($this->input->post('next_visit') != '') {
            $post['next_visit'] = $this->input->post('next_visit');
        } elseif ((int)$id > 0) {
            $post['next_visit'] = $info['next_visit'];
        } else {
            $post['next_visit'] = '';
        }


        if ($this->input->post('status_book') != '') {
            $l_status = $this->list_status($this->input->post('status_book'));

        } elseif ((int)$id > 0) {
            // $l_usrcar		= selectlist('user','id_user','username',null,$info['ref_user_car'],'--- Chooice User Car ---');
            // $l_location		= selectlist('ref_location','id_ref_location','location',null,$info['ref_location'],'--- Chooice Location ---');
            $l_status = $this->list_status($info['status_book']);

        } else {
            // $l_usrcar		= selectlist('user','id_user','username',null,null,'--- Chooice User Car ---');
            // $l_location		= selectlist('ref_location','id_ref_location','location',null,null,'--- Chooice Location ---');
            $l_status = $this->list_status();
        }

        // if($info['user_type'] != 'sales') {
        $info['uiname'] = $info['username'];
        $info['uimail'] = $info['email'];
        $info['sales'] = '';
        // }else {
        // $info['uiname'] = $info['name'];
        // $info['uimail'] = $info['email'];
        // $info['sales']  = '';
        // }

        // default
        // if($idgroup) {
        // switch($idgroup) {
        // case '4':
        // $info['ocheck'] = '';
        // $info['dservice_note'] = '';
        // $info['dnvisit'] = '';
        // break;
        // case '5':
        // case '3':
        // $info['dstat'] = '';
        // break;
        // }
        // }

        // new ref
        $info['ocheck'] = '';
        $info['dservice_note'] = '';
        $info['dnvisit'] = '';
        $info['dstat'] = '';


        $post = array($post);
        $info = array($info);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'info' => $info,
            'l_status' => $l_status,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,
        );
        $this->parser->parse($template . '/list_' . $file_app . '_form.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     *
     * @param int $id
     * @return string $this->error error
     */
    private function validateForm($id = 0)
    {
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['next_visit'] == '') {
            $this->error['next_visit'] = 'Please fill the Next Visit Field';
        }

        if ($post['service_note'] == '') {
            $this->error['service_note'] = 'Please fill the Service Notes Field';
        }


        if ($post['status_book'] == '' || $post['status_book'] == '0') {
            $this->error['status_book'] = 'Please select Status Book.<br/>';
        }


        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function generate_datelist()
    {
        $datelist = get_date_list(date('Y-m-d', strtotime('+1days')), date('Y-m-d', strtotime('+8days')));
        $dchk = '';
        foreach ($datelist as $i => $v) {
            $dchk .= "<li class='small radius secondary button' time='" . strtotime($v) . "'>" . date('D, j M Y',
                    strtotime($v)) . '</li>';

        }
        return $dchk;
    }
}

/* End of file trans_spare_part.php */
/* Location: ./application/controllers/admpage/trans_spare_part.php */
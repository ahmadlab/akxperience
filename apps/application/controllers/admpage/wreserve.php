<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Service Workshop Reserve Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Service Workshop Reserve Management
 *********************************************/
class Wreserve extends CI_Controller
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
        $this->ctrl = 'wreserve';
        $this->template = getAdminFolder() . '/modules/wreserve';
        $this->path_uri = getAdminFolder() . '/wreserve';
        $this->path = site_url(getAdminFolder() . '/wreserve');
        $this->title = get_admin_menu_title('wreserve');
        $this->id_menu_admin = get_admin_menu_id('wreserve');
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
        $s_name = $this->uri->segment(4);
        $s_status = $this->uri->segment(5);
        $s_sdate = $this->uri->segment(6);
        $s_edate = $this->uri->segment(7);
        $pg = $this->uri->segment(8);
        $per_page = 25;
        $uri_segment = 8;
        $no = 0;
        $path = $this->path . '/main/a/b/c/d/';
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
        $search_query = '';

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_name) > 1) {
            $s_name = substr($s_name, 1);

        } else {
            $s_name = "";

        }
        if (strlen($s_status) > 1) {
            $s_status = substr($s_status, 1);

        } else {
            $s_status = "";

        }
        if (strlen($s_sdate) > 1) {
            $s_sdate = substr($s_sdate, 1);

        } else {
            $s_sdate = "";

        }
        if (strlen($s_edate) > 1) {
            $s_edate = substr($s_edate, 1);

        } else {
            $s_edate = "";

        }


        $l_status = $this->list_status($s_status);
        $total_records = $this->Reserve_model->TotalServiceWorkshopReserve(myUrlDecode($s_name), myUrlDecode($s_status),
            myUrlDecode($s_sdate), myUrlDecode($s_edate), $this->location_id);

        if ($s_name) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_name . "/", $path);
            ($search_query == '' ? $search_query .= $s_name : $search_query .= ' + ' . $s_name);
        }
        if ($s_status) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_status . "/", $path);
            ($search_query == '' ? $search_query .= $s_status : $search_query .= ' + ' . $s_status);
        }
        if ($s_sdate) {
            $dis_urut = "none";
            $path = str_replace("/c/", "/c" . $s_sdate . "/", $path);
            ($search_query == '' ? $search_query .= $s_sdate : $search_query .= ' + ' . $s_sdate);
        }
        if ($s_edate) {
            $dis_urut = "none";
            $path = str_replace("/d/", "/d" . $s_edate . "/", $path);
            ($search_query == '' ? $search_query .= $s_edate : $search_query .= ' + ' . $s_edate);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Reserve_model->GetAllServiceWorkshopReserve(myUrlDecode($s_name), myUrlDecode($s_status),
            myUrlDecode($s_sdate), myUrlDecode($s_edate), $lmt, $per_page, $this->location_id);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id = $buffs['id_service_booking'];
            $usr = $buffs['username'];
            $car = $buffs['car'];
            $location = $buffs['location'];
            $timebook = $buffs['datetime_book'];
            $status = $buffs['status_book'];
            $create_date = $buffs['create_date'];

            // $edit_href 	= ($status !== 'finished') ? site_url($path_uri.'/edit/'.$id) : '#';
            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'car' => $car,
                'create_date' => $create_date,
                'location' => $location,
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

        if ($search_query != '') {
            $search_query = '<h5>Search Query: ' . $search_query . '</h5>';
        }

        $datelist = $this->generate_datelist();

        // echo $datelist;

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            's_name' => $s_name,
            'l_status' => $l_status,
            's_status' => $s_status,
            's_sdate' => $s_sdate,
            's_edate' => $s_edate,
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
            'q_total' => $total_records,
            'search_query' => $search_query,
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
        $s_name = myUrlEncode(trim($this->input->post('s_name')));
        $s_status = myUrlEncode(trim($this->input->post('s_status')));
        $s_sdate = myUrlEncode(trim($this->input->post('s_sdate')));
        $s_edate = myUrlEncode(trim($this->input->post('s_edate')));
        redirect($this->path . '/main/a' . $s_name . '/b' . $s_status . '/c' . $s_sdate . '/d' . $s_edate);
    }

    /**
     * add page
     */
    public function add()
    {

        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $post = purify($this->input->post());
            $data_post = array(
                'ref_user_cars' => $post['ref_user_cars'],
                'ref_service' => $post['ref_service'],
                'id_ref_location' => $post['id_ref_location'],
                'datetime_book' => $post['date'] . ' ' . str_replace('.', ':', $post['time']) . ':00',

            );

            $id = $this->Reserve_model->AddServiceReserve($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id;
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', $this->title . ' has been added');

            redirect($this->path_uri);
        }
        $this->getForm();
    }

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
        $user_sess = $this->session->userdata('ADM_SESS');
        $group = $user_sess['admin_id_auth_user_group'];

        if ($group == 3) {
            $status_array = array('booked', 'confirmed', 'canceled', 'finished');
        } elseif ($group == 10) {
            $status_array = array('service in progress', 'service is done');
        } elseif ($group == 1) {
            $status_array = array(
                'booked',
                'confirmed',
                'canceled',
                'service in progress',
                'service is done',
                'finished'
            );
        }

        $opt = "<option value=''>--- Status Book ---</option>";
        foreach ($status_array as $l) {
            $terpilih = ($selected == $l) ? 'selected' : '';
            $opt .= "<option $terpilih value='$l'>$l</option>";
        }
        return $opt;
    }

    public function list_eta($selected = false)
    {
        $opt = "<option value=''>--- Etimated Time ---</option>";
        $eta = array();
        for ($i = 1; $i <= 12; $i++) {
            $eta[] = $i . ' Hour';
        }
        for ($i = 1; $i <= 14; $i++) {
            $eta[] = $i . ' Day';
        }
        foreach ($eta as $l) {
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
                'date_next_visit' => iso_date($post['next_visit']),
                'service_notes' => $post['service_note'],
                'eta' => $post['eta'],
            );

            $b = db_get_one('car_service_booking', 'status_book', "id_service_booking = '$id'");

            if ($b != 'finished' && $b != 'cancel') {
                $data_post['status_book'] = $post['status_book'];
                $data_post['total_price'] = $post['total_price'];

                $this->Reserve_model->UpdateServiceReserve($data_post, $id);
            }


            // $this->Reserve_model->UpdateServiceReserve($data_post,$id);


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
        $df = array();
        $group_list = '';
        $site_list = '';
        $cancel_btn = site_url($path_uri);
        $breadcrumbs = array();
        $info = array();
        $idgroup = adm_sess_usergroupid();

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Reserve_model->GetServiceWorkshopReserveById($id);
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

        if (isset($this->error['service_type'])) {
            $error_msg['service_type'] = alert_box($this->error['service_type'], 'error');
        } else {
            $error_msg['service_type'] = '';
        }

        if (isset($this->error['service'])) {
            $error_msg['service'] = alert_box($this->error['service'], 'error');
        } else {
            $error_msg['service'] = '';
        }

        if (isset($this->error['total_price'])) {
            $error_msg['total_price'] = alert_box($this->error['total_price'], 'error');
        } else {
            $error_msg['total_price'] = '';
        }

        if (isset($this->error['eta'])) {
            $error_msg['eta'] = alert_box($this->error['eta'], 'error');
        } else {
            $error_msg['eta'] = '';
        }


        if (isset($this->error['user'])) {
            $error_msg['user'] = alert_box($this->error['user'], 'error');
        } else {
            $error_msg['user'] = '';
        }
        if (isset($this->error['ref_user_cars'])) {
            $error_msg['ref_user_cars'] = alert_box($this->error['ref_user_cars'], 'error');
        } else {
            $error_msg['ref_user_cars'] = '';
        }
        if (isset($this->error['date'])) {
            $error_msg['date'] = alert_box($this->error['date'], 'error');
        } else {
            $error_msg['date'] = '';
        }
        if (isset($this->error['id_ref_location'])) {
            $error_msg['id_ref_location'] = alert_box($this->error['id_ref_location'], 'error');
        } else {
            $error_msg['id_ref_location'] = '';
        }
        if (isset($this->error['ref_service'])) {
            $error_msg['ref_service'] = alert_box($this->error['ref_service'], 'error');
        } else {
            $error_msg['ref_service'] = '';
        }
        if (isset($this->error['time'])) {
            $error_msg['time'] = alert_box($this->error['time'], 'error');
        } else {
            $error_msg['time'] = '';
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
            $post['next_visit'] = iso_date($info['next_visit']);
        } else {
            $post['next_visit'] = '';
        }

        if ($this->input->post('total_price') != '') {
            $post['total_price'] = $this->input->post('total_price');
        } elseif ((int)$id > 0) {
            $post['total_price'] = $info['total_price'];
        } else {
            $post['total_price'] = '';
        }

        if ($this->input->post('eta') != '') {
            $post['eta'] = $this->input->post('eta');
        } elseif ((int)$id > 0) {
            $post['eta'] = $info['eta'];
        } else {
            $post['eta'] = '';
        }

        if ($this->input->post('ref_service') != '') {
            $refservlst = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null,
                $this->input->post('ref_service'), '--- Choice Service Type ---');

        } elseif ((int)$id > 0) {
            $refservlst = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null,
                $info['ref_service'], '--- Choice Service Type ---');

        } else {
            $refservlst = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null, null,
                '--- Choice Service Type ---');
        }

        if ($this->input->post('service_type') != '') {
            $servicelst = selectlist('car_services', 'id_service', 'service',
                "id_service_type = '" . $this->input->post('ref_service') . "'", $this->input->post('service_type'),
                '--- Choice Service ---');

        } elseif ((int)$id > 0) {
            $servicelst = selectlist('car_services', 'id_service', 'service',
                "id_service_type = '" . $info['ref_service'] . "'", $info['service_id'], '--- Choice Service  ---');

        } else {
            $servicelst = selectlist('car_services', 'id_service', 'service', null, null, '--- Choice Service ---');
        }


        if ($this->input->post('status_book') != '') {
            $l_status = $this->list_status($this->input->post('status_book'));

        } elseif ((int)$id > 0) {
            $l_status = $this->list_status($info['status_book']);

        } else {
            $l_status = $this->list_status();
        }

        // if($info['user_type'] != 'sales') {
        $info['uiname'] = (isset($info['username'])) ? $info['username'] : '';
        $info['uimail'] = (isset($info['email'])) ? $info['email'] : '';
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

        if (!$id) {
            $post['user'] = '';
            if ($this->input->post('usern')) {
                $post['user'] = $this->input->post('usern');
            } else {
                $user = null;
            }
            if ($this->input->post('service')) {
                $service = $this->input->post('service');
            } else {
                $service = null;
            }
            if ($this->input->post('ref_service')) {
                $rservice = $this->input->post('ref_service');
            } else {
                $rservice = null;
            }
            if ($this->input->post('ref_user_cars')) {
                $rcar = $this->input->post('ref_user_cars');
            } else {
                $rcar = null;
            }
            if ($this->input->post('id_ref_location')) {
                $rlocation = $this->input->post('id_ref_location');
            } else {
                $rlocation = null;
            }
            if ($this->input->post('date') && $this->input->post('id_ref_location')) {
                $time = $this->input->post('time');
            } else {
                $time = null;
            }
            if ($this->input->post('date')) {
                $sdate = $this->input->post('date');
            } else {
                $sdate = null;
            }


            $post['service'] = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null, $service,
                '--- Choice Service Type ---', "service_type asc");
            $post['ref_location'] = selectlist('ref_location', 'id_ref_location', 'location', "company_code = 'AK'",
                $rlocation, '--- Choice Location ---', "location asc");
            $post['carlist'] = selectlist('user_cars', 'id_user_cars', 'police_number', "police_number != ''", $rcar,
                '--- Choice Car ---', "police_number asc");
            $post['ref_service'] = selectlist('car_services', 'id_service', 'service', null, $rservice,
                '--- Choice Service ---', 'service asc');

            $post['date'] = "<option value='0'>--- Choice Date ---</option>";
            foreach (get_date_list(date('Y-m-d'), date('Y-m-d', strtotime('+8days'))) as $v) {
                $date = date('Y-m-d', strtotime($v));
                $selected = ($date == $sdate) ? 'selected' : '';
                $post['date'] .= "<option $selected value='$date'>$v</option>";
            }

            $user = $this->db->where("username != '' and username != '-' and user_type != 'sales' and status = '1'")
                ->order_by('username asc')->get('user');
            if ($user->num_rows() > 0) {
                foreach ($user->result_array() as $k => $f) {
                    $df[$k]['label'] = $f['username'];
                    $df[$k]['val'] = $f['username'];
                    $df[$k]['key'] = $f['id_user'];
                }
            }

            $post['time'] = "<option value='0'>--- Choice Time ---</option>";
            if ($time != null) {
                foreach (get_time_by_daten('service', $date, $rlocation) as $v) {
                    $selected = ($time == $v) ? 'selected' : '';
                    $post['time'] .= "<option $selected value='$v'>$v</option>";
                }
            }
        }

        // new ref
        $info['ocheck'] = '';
        $info['dservice_note'] = '';
        $info['dnvisit'] = '';
        $info['dstat'] = '';
        $info['list_eta'] = $this->list_eta($post['eta']);


        $user_sess = $this->session->userdata('ADM_SESS');
        $group = $user_sess['admin_id_auth_user_group'];

        if ($group == 3) {
            $info['dcrc_disabled'] = 'disabled';
        } else {
            $info['dcrc_disabled'] = '';
        }

        $post = array($post);
        $info = array($info);
        $error_msg = array($error_msg);

        $form = ($id) ? $file_app : $file_app . '_add';

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'info' => $info,
            'l_status' => $l_status,
            'refservlst' => $refservlst,
            'servicelst' => $servicelst,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'code' => json_encode($df),
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,
        );
        $this->parser->parse($template . '/list_' . $form . '_form.html', $data);
        $this->global_libs->print_footer();
    }

    public function get_service()
    {
        echo get_service_by($this->input->post('id'));
    }

    public function get_car()
    {
        $id = $this->input->post('id');
        if ($id) {
            echo selectlist('user_cars', 'id_user_cars', 'police_number', "police_number != '' and id_user = '$id'",
                null, '--- Choice Car ---', 'police_number asc');
        }
    }

    public function detail_service()
    {
        $id = $this->input->post('id');
        if ($id) {
            echo selectlist('car_services', 'id_service', 'service', "id_service_type = '$id'", null,
                '--- Choice Service ---', 'service asc'), exit;
        }
        echo "<option value='0'>--- Choice Service ---</option>";
    }

    public function get_time()
    {
        $id = $this->input->post('id');
        $locate = $this->input->post('locate');
        if ($id && $locate) {
            $d = "<option value='0'>--- Choice Time ---</option>";
            foreach (get_time_by_daten('service', $id, $locate) as $v) {
                $d .= "<option value='$v'>$v</option>";
            }
            echo $d, exit;

        }
        echo '0';
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

        if ($id) {
            if ($post['next_visit'] == '') {
                $this->error['next_visit'] = 'Please fill the Next Visit Field';
            }


            if ($post['status_book'] == 'finished') {
                if ($post['total_price'] == '' || $post['total_price'] == '0') {
                    $this->error['total_price'] = 'Please fill the Service Price Field';
                }
            }

            if ($post['status_book'] == 'service in progress') {
                if ($post['eta'] == '' || $post['eta'] == '0') {
                    $this->error['eta'] = 'Please fill the estimated time field.<br/>';
                }
                if ($post['service_note'] == '') {
                    $this->error['service_note'] = 'Please fill the Service Notes Field';
                }

            }

            if ($post['status_book'] == '' || $post['status_book'] == '0') {
                $this->error['status_book'] = 'Please select Status Book.<br/>';
            }


        } else {
            if ($post['usern'] == '' || $post['usern'] == '0') {
                $this->error['user'] = 'Please fill user field';
            }
            if ($post['date'] == '' || $post['date'] == '0') {
                $this->error['date'] = 'Please select date ';
            }
            if ($post['id_ref_location'] == '' || $post['id_ref_location'] == '0') {
                $this->error['id_ref_location'] = 'Please select workshop';
            }
            if ($post['ref_service'] == '' || $post['ref_service'] == '0') {
                $this->error['ref_service'] = 'Please select service';
            }
            // if($post['service'] == '' || $post['service'] == '0') {
            // $this->error['service'] = 'Please select service type';
            // }
            if ($post['ref_user_cars'] == '' || $post['ref_user_cars'] == '0') {
                $this->error['ref_user_cars'] = 'Please select user car';
            }
            if ($post['time'] == '' || $post['time'] == '0') {
                $this->error['time'] = 'Please select time';
            }

        }

        // if ($post['service_type'] == '' || $post['service_type'] == '0') {
        // $this->error['service_type'] = 'Please select Service Type.<br/>';
        // }

        if ($post['service'] == '' || $post['service'] == '0') {
            $this->error['service'] = 'Please select Service.<br/>';
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

    public function exportoxl()
    {
        $s_name = $this->uri->segment(4);
        $s_status = $this->uri->segment(5);
        $s_sdate = $this->uri->segment(6);
        $s_edate = $this->uri->segment(7);
        $pg = $this->uri->segment(8);
        $per_page = 1000;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/a/b/c/d/';
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

        if (strlen($s_name) > 1) {
            $s_name = substr($s_name, 1);

        } else {
            $s_name = "";

        }
        if (strlen($s_status) > 1) {
            $s_status = substr($s_status, 1);

        } else {
            $s_status = "";

        }
        if (strlen($s_sdate) > 1) {
            $s_sdate = substr($s_sdate, 1);

        } else {
            $s_sdate = "";

        }
        if (strlen($s_edate) > 1) {
            $s_edate = substr($s_edate, 1);

        } else {
            $s_edate = "";

        }

        // $l_status		= $this->list_status($s_status);
        $total_records = $this->Reserve_model->TotalServiceWorkshopReserve(myUrlDecode($s_name), myUrlDecode($s_status),
            $this->location_id);

        if ($s_name) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_name . "/", $path);
        }
        if ($s_status) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_status . "/", $path);
        }
        if ($s_sdate) {
            $dis_urut = "none";
            $path = str_replace("/c/", "/c" . $s_sdate . "/", $path);
        }
        if ($s_edate) {
            $dis_urut = "none";
            $path = str_replace("/d/", "/d" . $s_edate . "/", $path);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }

        $query = $this->Reserve_model->GetAllServiceWorkshopReserve(myUrlDecode($s_name), myUrlDecode($s_status),
            myUrlDecode($s_sdate), myUrlDecode($s_edate), $lmt, $per_page, $this->location_id);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id = $buffs['id_service_booking'];
            $usr = $buffs['username'];
            $car = $buffs['car'];
            $timebook = '="' . $buffs['datetime_book'] . '"';
            $status = $buffs['status_book'];
            $pn = '="' . $buffs['police_number'] . '"';
            $st = $buffs['service_type'];
            $service = $buffs['service'];
            $create_date = '="' . $buffs['create_date'] . '"';

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'car' => $car,
                'create_date' => $create_date,
                'pn' => $pn,
                'service' => $service,
                'usr' => $usr,
                'timebook' => $timebook,
                'status' => $status,
                'edit_href' => $edit_href,
                'st' => $st,
            );
        }


        $datelist = $this->generate_datelist();

        // echo $datelist;

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            // 'l_status'			=> $l_status,
            'list_data' => $list_data,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
            'datelist' => $datelist,
        );
        export_to('Service_Booking_Workshop_Date_' . date('Ymd') . '.xls');
        $data = $this->parser->parse($template . '/exp.html', $data, true);
        echo $data;
    }

}

/* End of file trans_spare_part.php */
/* Location: ./application/controllers/admpage/trans_spare_part.php */
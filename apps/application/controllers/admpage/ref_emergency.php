<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Ref Emergency Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Ref Emergency Management
 *********************************************/
class Ref_emergency extends CI_Controller
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
    private $max_car_width;
    private $max_car_height;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
        $this->ctrl = 'ref_emergency';
        $this->template = getAdminFolder() . '/modules/ref_emergency';
        $this->path_uri = getAdminFolder() . '/ref_emergency';
        $this->path = site_url(getAdminFolder() . '/ref_emergency');
        $this->title = get_admin_menu_title('ref_emergency');
        $this->id_menu_admin = get_admin_menu_id('ref_emergency');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/ref_emergency/';
        $this->load->model(getAdminFolder() . '/ref_emergency_model', 'Ref_emergency');
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
        $this->load->helper('text');
        $s_emergen = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/a/';
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

        if (strlen($s_emergen) > 1) {
            $s_emergen = substr($s_emergen, 1);

        } else {
            $s_emergen = "";

        }
        $total_records = $this->Ref_emergency->TotalRefEmergency(myUrlDecode($s_emergen));


        if ($s_emergen) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_emergen . "/", $path);
            ($search_query == '' ? $search_query .= $s_emergen : $search_query .= ' + ' . $s_emergen);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Ref_emergency->GetAllRefEmergency(myUrlDecode($s_emergen), $lmt, $per_page);

        foreach ($query->result_array() as $bufss) {
            $no++;
            $id = $bufss['id_emergency'];
            // $ref_publish	= ($bufss['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $name = $bufss['name'];
            $addr = word_limiter($bufss['address'], 4);
            $hotline = $bufss['phone'];
            $fax = $bufss['fax'];
            $lat = $bufss['lat'];
            $lng = $bufss['lng'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'addr' => $addr,
                'hotline' => $hotline,
                'fax' => $fax,
                'name' => $name,
                'edit_href' => $edit_href,
                'lat' => $lat,
                'long' => $lng
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

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            's_emergen' => $s_emergen,
            'list_data' => $list_data,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
            'q_total' => $total_records,
            'search_query' => $search_query,
        );
        $this->parser->parse($template . '/list_' . $file_app . '.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * public function change_publish.
     *
     * change publish page by request get.
     */
    // public function change_publish($id=0)
    // {
    // if ($id)
    // {
    // if (ctype_digit($id))
    // {
    // $return = $this->Cars_model->ChangePublishBrand($id);
    // echo $return;
    // }
    // }
    // }

    /**
     * search post s_name and s_email
     */
    public function search()
    {
        auth_admin();
        $s_emergen = myUrlEncode(trim($this->input->post('s_emergen')));
        redirect($this->path . '/main/a' . $s_emergen);
    }

    /**
     * delete page post id
     */
    public function delete()
    {
        auth_admin();
        if ($this->input->post('id') != "") {
            if (($this->input->post('id') > 0)) {
                if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                    $id = array_filter(explode('-', $this->input->post('id')));
                    $this->Ref_emergency->DeleteRefEmergency($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete ' . $this->ctrl . ' ID : ' . $id;
                    $log_desc = 'Delete ' . $this->ctrl . ' ID : ' . $id . ';';
                    $data = array(
                        'id_user' => $log_id_user,
                        'id_group' => $log_id_group,
                        'action' => $log_action,
                        'desc' => $log_desc,
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);
                    $this->session->set_flashdata('success_msg', $this->title . ' (s) has been deleted.');

                } else {
                    $this->session->set_flashdata('error_msg', 'You can\'t manage this content.<br/>');
                }
            } else {
                $this->session->set_flashdata('error_msg', 'Delete failed. Please try again.');
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Delete failed. Please try again.');
        }
    }

    public function list_company($selected = '')
    {
        $opt = "<option value='0'>--- Select Company Code ---</option>";
        foreach (array('AK', 'CV') as $k => $v) {
            $selected = $selected == $v ? 'selected' : '';
            $opt .= "<option $selected value='$v'>$v</option>";
        }
        return $opt;
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
                'name' => $post['name'],
                'address' => $post['address'],
                'phone' => $post['phone'],
                'fax' => $post['fax'],
                'lat' => $post['lat'],
                'lng' => $post['lng'],
                // 'company_code' 	=> $post['company_code'],
            );

            // insert data
            $id = $this->Ref_emergency->InsertRefEmergency($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Name :' . $post['name'];
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
            $ico = $_FILES['thumb'];
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');


            $data_post = array(
                'name' => $post['name'],
                'address' => $post['address'],
                'phone' => $post['phone'],
                'fax' => $post['fax'],
                'lat' => $post['lat'],
                'lng' => $post['lng'],
                // 'company_code' 	=> $post['company_code'],
            );
            $this->Ref_emergency->UpdateRefEmergency($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Name :' . $post['name'];
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

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Ref_emergency->GetRefEmergencyById($id);
            if ($query->num_rows() > 0) {
                $info = $query->row_array();

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
            // $Post['id'] = $id;
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
            // $Post['id'] = 0;
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['name'])) {
            $error_msg['name'] = alert_box($this->error['name'], 'error');
        } else {
            $error_msg['name'] = '';
        }
        // if (isset($this->error['company_code'])) {
        // $error_msg['company_code'] = alert_box($this->error['company_code'],'error');
        // } else {
        // $error_msg['company_code'] = '';
        // }
        if (isset($this->error['address'])) {
            $error_msg['address'] = alert_box($this->error['address'], 'error');
        } else {
            $error_msg['address'] = '';
        }
        if (isset($this->error['phone'])) {
            $error_msg['phone'] = alert_box($this->error['phone'], 'error');
        } else {
            $error_msg['phone'] = '';
        }
        if (isset($this->error['fax'])) {
            $error_msg['fax'] = alert_box($this->error['fax'], 'error');
        } else {
            $error_msg['fax'] = '';
        }
        if (isset($this->error['lat'])) {
            $error_msg['lat'] = alert_box($this->error['lat'], 'error');
        } else {
            $error_msg['lat'] = '';
        }
        if (isset($this->error['lng'])) {
            $error_msg['lng'] = alert_box($this->error['lng'], 'error');
        } else {
            $error_msg['lng'] = '';
        }
        if (isset($this->error['lng'])) {
            $error_msg['lng'] = alert_box($this->error['lng'], 'error');
        } else {
            $error_msg['lng'] = '';
        }


        // set value
        if ($this->input->post('name') != '') {
            $post['name'] = $this->input->post('name');
        } elseif ((int)$id > 0) {
            $post['name'] = $info['name'];
        } else {
            $post['name'] = '';
        }
        // if ($this->input->post('company_code') != '') {
        // $post['company_code'] = $this->list_company($this->input->post('company_code'));
        // } elseif ((int)$id>0) {
        // $post['company_code'] = $this->list_company($info['company_code']);
        // } else {
        // $post['company_code'] = $this->list_company();
        // }
        if ($this->input->post('address') != '') {
            $post['address'] = $this->input->post('address');
        } elseif ((int)$id > 0) {
            $post['address'] = $info['address'];
        } else {
            $post['address'] = '';
        }
        if ($this->input->post('phone') != '') {
            $post['phone'] = $this->input->post('phone');
        } elseif ((int)$id > 0) {
            $post['phone'] = $info['phone'];
        } else {
            $post['phone'] = '';
        }
        if ($this->input->post('fax') != '') {
            $post['fax'] = $this->input->post('fax');
        } elseif ((int)$id > 0) {
            $post['fax'] = $info['fax'];
        } else {
            $post['fax'] = '';
        }
        if ($this->input->post('lat') != '') {
            $post['lat'] = $this->input->post('lat');
        } elseif ((int)$id > 0) {
            $post['lat'] = $info['lat'];
        } else {
            $post['lat'] = '';
        }
        if ($this->input->post('lng') != '') {
            $post['lng'] = $this->input->post('lng');
        } elseif ((int)$id > 0) {
            $post['lng'] = $info['lng'];
        } else {
            $post['lng'] = '';
        }


        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
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


        if ($post['name'] == '') {
            $this->error['name'] = 'Please insert Name.<br/>';
        } else {
            if (!$this->Ref_emergency->CheckExistsRefEmergency($post['name'], $id)) {
                $this->error['name'] = 'Name already exists, please input different name.<br/>';
            } else {
                if (utf8_strlen($post['name']) < 4) {
                    $this->error['name'] = 'Name length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['address'] == '') {
            $this->error['address'] = 'Please insert Address.<br/>';
        }
        if ($post['phone'] == '') {
            $this->error['phone'] = 'Please insert Phone.<br/>';
        }
        if ($post['fax'] == '') {
            $this->error['fax'] = 'Please insert Fax.<br/>';
        }

        // if($post['company_code'] == '' || $post['company_code'] == '0') {
        // $this->error['company_code'] = 'Please select Company Code.<br/>';
        // }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
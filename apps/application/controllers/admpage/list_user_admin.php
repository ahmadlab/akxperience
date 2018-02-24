<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * List_user_admin Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : List of User Admin management
 *********************************************/
class List_user_admin extends CI_Controller
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

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'list_user_admin';
        $this->template = getAdminFolder() . '/list_user_admin';
        $this->path_uri = getAdminFolder() . '/list_user_admin';
        $this->path = site_url(getAdminFolder() . '/list_user_admin');
        $this->title = get_admin_menu_title('list_user_admin');
        $this->id_menu_admin = get_admin_menu_id('list_user_admin');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
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
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');

        $s_name = $this->uri->segment(4);
        $s_email = $this->uri->segment(5);
        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 6;
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
        $search_query = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

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

        if (strlen($s_email) > 1) {
            $s_email = substr($s_email, 1);
        } else {
            $s_email = "";
        }

        $total_records = $this->Admin_model->TotalUserAdmin(myUrlDecode($s_name), myUrlDecode($s_email),
            $is_superadmin);

        if ($s_name) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_name . "/", $path);
            ($search_query == '' ? $search_query .= $s_name : $search_query .= ' + ' . $s_name);
        }

        if ($s_email) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_email . "/", $path);
            ($search_query == '' ? $search_query .= $s_email : $search_query .= ' + ' . $s_email);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Admin_model->GetAllUsersAdmin(myUrlDecode($s_name), myUrlDecode($s_email), $is_superadmin, $lmt,
            $per_page);

        foreach ($query->result_array() as $row_auth_user) {
            $no++;
            $id_auth_user = $row_auth_user['id_auth_user'];
            $id_auth_user_group = $row_auth_user['id_auth_user_group'];
            $username = $row_auth_user['username'];
            $name = $row_auth_user['name'];
            $email = $row_auth_user['email'];
            $phone = $row_auth_user['phone'];
            $location = db_get_one('ref_location', 'location',
                "id_ref_location = '" . $row_auth_user['ref_location'] . "'");
            $location = ($location == '0') ? 'All' : $location;

            $group = $this->Admin_model->GetGroupNameById($id_auth_user_group);
            $edit_href = site_url($path_uri . '/edit/' . $id_auth_user);

            $list_admin_arr[] = array(
                'no' => $no,
                'id_auth_user' => $id_auth_user,
                'email' => $email,
                'location' => $location,
                'group' => $group,
                'username' => $username,
                'name' => $name,
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

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            'list_user_admin' => $list_admin_arr,
            's_name' => myUrlDecode($s_name),
            's_email' => myUrlDecode($s_email),
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
        $this->parser->parse($template . '/list_user_admin.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * add page
     */
    public function add()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');
            $this->load->library('encrypt');
            $pass = $this->encrypt->encode($post['userpass']);

            // if (!empty($post['is_superadmin'])) {
            // $post['is_superadmin'] = $post['is_superadmin'];
            // } else {
            // $post['is_superadmin'] = 0;
            // }

            $data_post = array(
                'id_auth_user_group' => (int)$post['id_auth_user_group'],
                'username' => strtolower($post['username']),
                'userpass' => $pass,
                'name' => $post['name'],
                'email' => strtolower($post['email']),
                'phone' => $post['phone'],
                'phone2' => $post['phone2'],
                'modify_date' => $now,
                'create_date' => $now,
                'status' => 1,
                'ref_location' => $post['ref_location'],
            );

            // insert data
            $id_auth_user = $this->Admin_model->InsertAdminUser($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_auth_user;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_auth_user . '; Admin Name:' . $post['name'] . '; Admin Email:' . $post['email'] . '; Admin Username:' . $post['username'] . ';';
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
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');
        $this->load->library('encrypt');
        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->path_uri);
        }

        // if (!$this->Admin_model->check_is_superadmin($id, $this->is_superadmin)) {
        // redirect($this->path_uri);
        // }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');

            if (!empty($post['is_superadmin'])) {
                $post['is_superadmin'] = $post['is_superadmin'];
            } else {
                $post['is_superadmin'] = 0;
            }

            $data_post = array(
                'id_auth_user_group' => (int)$post['id_auth_user_group'],
                'username' => strtolower($post['username']),
                'name' => $post['name'],
                'email' => strtolower($post['email']),
                'phone' => $post['phone'],
                'phone2' => $post['phone2'],
                'modify_date' => $now,
                'ref_location' => $post['ref_location'],
            );

            if (($post['userpass'] != '') && (utf8_strlen($post['userpass']) >= 5)) {
                $pass = $this->encrypt->encode($post['userpass']);
                $data_pass = array('userpass' => $pass);
                $data_post = array_merge($data_post, $data_pass);
            }

            // update data
            $this->Admin_model->UpdateAdminUser($id, $data_post);

            //update session if user update his profile
            $id_admin = adm_sess_userid();
            if ($id_admin == $id) {
                $user_sess = array(
                    'admin_name' => $data_post['name'],
                    'admin_id_auth_user_group' => $data_post['id_auth_user_group'],
                    'admin_id_auth_user' => $id_admin,
                    'admin_id_site' => $data_post['id_site'],
                    'admin_email' => $data_post['email'],
                    'admin_type' => 'admin',
                    'admin_url' => base_url(),
                    'admin_ip' => $_SERVER['REMOTE_ADDR'],
                );
                $this->session->set_userdata('ADM_SESS', $user_sess);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Admin Name:' . $post['name'] . '; Admin Email:' . $post['email'] . '; Admin Username:' . $post['username'] . ';';
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
     * search post s_name and s_email
     */
    public function search()
    {
        auth_admin();
        $s_name = myUrlEncode(trim($this->input->post('s_name')));
        $s_email = myUrlEncode(trim($this->input->post('s_email')));
        redirect($this->template . '/main/a' . $s_name . '/b' . $s_email);
    }

    /**
     * delete page post id
     */
    public function delete()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');
        if ($this->input->post('id') != "") {
            $id = array_filter(explode('-', $this->input->post('id')));
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                foreach ($id as $d) {
                    if ($d == adm_sess_userid()) {
                        $this->session->set_flashdata('error_msg',
                            'Delete failed. You can\'t delete your own account.');
                        exit;
                    }
                }

                $id_auth_user = $this->input->post('id');
                $this->Admin_model->DeleteAdminUser($id_auth_user);
                #insert to log
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete Admin User ID : ' . $id_auth_user;
                $log_desc = 'Delete Admin User ID : ' . $id_auth_user . ';';
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
    }


    /////////////////////////////////////////////////////////////////
    /////////////////////////// private /////////////////////////////
    /////////////////////////////////////////////////////////////////

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
        $is_superadmin = $this->is_superadmin;

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Admin_model->GetUsersAdminById($id);
            if ($query->num_rows() > 0) {
                $admin_info = $query->row_array();
            } else {
                $this->session->set_flashdata('info_msg', 'There is no record in our database.');
                redirect($template);
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
            $action = site_url($template . '/edit/' . $id);
        } else {
            $breadcrumbs[] = array(
                'text' => 'Add',
                'href' => '#',
                'class' => 'class="current"'
            );
            $pass_msg = '';
            $err_c = '';
            $required = 'required';
            $action = site_url($template . '/add');
        }

        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['id_auth_user_group'])) {
            $error_msg['id_auth_user_group'] = alert_box($this->error['id_auth_user_group'], 'error');
        } else {
            $error_msg['id_auth_user_group'] = '';
        }
        if (isset($this->error['id_site'])) {
            $error_msg['id_site'] = alert_box($this->error['id_site'], 'error');
        } else {
            $error_msg['id_site'] = '';
        }
        if (isset($this->error['username'])) {
            $error_msg['username'] = alert_box($this->error['username'], 'error');
        } else {
            $error_msg['username'] = '';
        }
        if (isset($this->error['userpass'])) {
            $error_msg['userpass'] = alert_box($this->error['userpass'], 'error');
        } else {
            $error_msg['userpass'] = '';
        }
        if (isset($this->error['name'])) {
            $error_msg['name'] = alert_box($this->error['name'], 'error');
        } else {
            $error_msg['name'] = '';
        }
        if (isset($this->error['email'])) {
            $error_msg['email'] = alert_box($this->error['email'], 'error');
        } else {
            $error_msg['email'] = '';
        }
        if (isset($this->error['phone'])) {
            $error_msg['phone'] = alert_box($this->error['phone'], 'error');
        } else {
            $error_msg['phone'] = '';
        }
        if (isset($this->error['phone2'])) {
            $error_msg['phone2'] = alert_box($this->error['phone2'], 'error');
        } else {
            $error_msg['phone2'] = '';
        }
        if (isset($this->error['ref_location'])) {
            $error_msg['ref_location'] = alert_box($this->error['ref_location'], 'error');
        } else {
            $error_msg['ref_location'] = '';
        }

        // set value
        if ($this->input->post('id_auth_user_group') != '') {
            $post['id_auth_user_group'] = $this->input->post('id_auth_user_group');
        } elseif ((int)$id > 0) {
            $post['id_auth_user_group'] = $admin_info['id_auth_user_group'];
        } else {
            $post['id_auth_user_group'] = '';
        }
        if ($this->input->post('id_site') != '') {
            $post['id_site'] = $this->input->post('id_site');
        } elseif ((int)$id > 0) {
            $post['id_site'] = $admin_info['id_site'];
        } else {
            $post['id_site'] = '';
        }

        if ($this->input->post('username') != '') {
            $post['username'] = $this->input->post('username');
        } elseif ((int)$id > 0) {
            $post['username'] = $admin_info['username'];
        } else {
            $post['username'] = '';
        }

        if ($this->input->post('username') != '') {
            $post['username'] = $this->input->post('username');
        } elseif ((int)$id > 0) {
            $post['username'] = $admin_info['username'];
        } else {
            $post['username'] = '';
        }

        if ($this->input->post('name') != '') {
            $post['name'] = $this->input->post('name');
        } elseif ((int)$id > 0) {
            $post['name'] = $admin_info['name'];
        } else {
            $post['name'] = '';
        }

        if ($this->input->post('email') != '') {
            $post['email'] = $this->input->post('email');
        } elseif ((int)$id > 0) {
            $post['email'] = $admin_info['email'];
        } else {
            $post['email'] = '';
        }

        if ($this->input->post('phone') != '') {
            $post['phone'] = $this->input->post('phone');
        } elseif ((int)$id > 0) {
            $post['phone'] = $admin_info['phone'];
        } else {
            $post['phone'] = '';
        }

        if ($this->input->post('phone2') != '') {
            $post['phone2'] = $this->input->post('phone2');
        } elseif ((int)$id > 0) {
            $post['phone2'] = $admin_info['phone2'];
        } else {
            $post['phone2'] = '';
        }

        if ($this->input->post('is_superadmin') != '') {
            $post['is_superadmin'] = $this->input->post('is_superadmin');
        } elseif ((int)$id > 0) {
            $post['is_superadmin'] = $admin_info['is_superadmin'];
        } else {
            $post['is_superadmin'] = 0;
        }

        if ($this->input->post('ref_location') != '') {
            $post['locationlst'] = selectlist('ref_location', 'id_ref_location', 'location', "company_code = 'AK'",
                $this->input->post('ref_location'), array('--- All Workshop ---'));
        } elseif ((int)$id > 0) {
            $post['locationlst'] = selectlist('ref_location', 'id_ref_location', 'location', "company_code = 'AK'",
                $admin_info['ref_location'], array('--- All Workshop ---'));
        } else {
            $post['locationlst'] = selectlist('ref_location', 'id_ref_location', 'location', "company_code = 'AK'",
                null, '--- Choice Location ---');
        }


        // generate menu parent
        $group_list = $this->getGroupSelect($post['id_auth_user_group'], $is_superadmin);

        // generate site list
        $site_list = $this->getSiteSelect($post['id_site']);

        // print super admin status option
        $superadmin_status = ($is_superadmin == 1) ? $this->print_option_superadmin($post['is_superadmin']) : '';

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'group_list' => $group_list,
            'site_list' => $site_list,
            'pass_msg' => $pass_msg,
            'err_c' => $err_c,
            'required' => $required,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,
            'superadmin_status' => $superadmin_status,
        );
        $this->parser->parse($template . '/list_user_admin_form.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     *
     * @param int $selected
     * @return string $return option of superadmin status
     */
    private function print_option_superadmin($selected = '')
    {
        $checked = '';
        if ($selected == 1) {
            $checked = 'checked="checked"';
        }
        $return = '<label for="is_superadmin">Super Administrator :
            <input type="checkbox" value="1" name="is_superadmin" id="is_superadmin" ' . $checked . ' /> </label>';
        return $return;
    }

    /**
     *
     * @param int $id
     * @return string $this->error error
     */
    private function validateForm($id = 0)
    {
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }


        if ($post['id_auth_user_group'] == '') {
            $this->error['id_auth_user_group'] = 'Please insert Group.<br/>';
        } else {
            if (!ctype_digit($post['id_auth_user_group'])) {
                $this->error['id_auth_user_group'] = 'Please insert Group.<br/>';
            }
        }
        // if ($post['id_site'] == '')
        // {
        // $this->error['id_site'] = 'Please insert Site.<br/>';
        // }
        // else
        // {
        // if (!ctype_digit($post['id_site']))
        // {
        // $this->error['id_site'] = 'Please insert Site.<br/>';
        // }
        // }

        if ($post['username'] == '') {
            $this->error['username'] = 'Please insert Username.<br/>';
        } else {
            if (!$this->Admin_model->CheckExistsUsername($post['username'], $id)) {
                $this->error['username'] = 'Username already exists, please input different Username.<br/>';
            } else {
                if (utf8_strlen($post['username']) < 5) {
                    $this->error['userpass'] = 'Username length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['name'] == '') {
            $this->error['name'] = 'Please insert Name.<br/>';
        } else {
            if ((utf8_strlen($post['name']) < 1) || (utf8_strlen($post['name']) > 32)) {
                $this->error['name'] = 'Please insert Name.<br/>';
            }
        }

        if ($post['email'] == '') {
            $this->error['email'] = 'Please insert Email.<br/>';
        } else {
            if (!mycheck_email($post['email'])) {
                $this->error['email'] = 'Please insert correct Email.<br/>';
            } else {
                if (!$this->Admin_model->CheckExistsEmail($post['email'], $id)) {
                    $this->error['email'] = 'Email already exists, please input different Email.<br/>';
                }
            }
        }

        if (($post['phone'] != '') && (!ctype_digit($post['phone']))) {
            $this->error['phone'] = 'Please insert correct Phone.<br/>';
        }

        if ($id) {
            if ($post['userpass'] != '') {
                if ($post['userpass'] != $post['confpass']) {
                    $this->error['userpass'] = 'Confirm Password didn\'t match with New Password.<br/>';
                } else {
                    if (utf8_strlen($post['userpass']) < 5) {
                        $this->error['userpass'] = 'Password length must be at least 5 character(s).<br/>';
                    }
                }
            }
        } else {
            if ($post['userpass'] == '') {
                $this->error['userpass'] = 'Please insert Password.<br/>';
            } else {
                if (utf8_strlen($post['userpass']) < 5) {
                    $this->error['userpass'] = 'Password length must be at least 5 character(s).<br/>';
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param int $selected_id
     * @param int $is_superadmin
     * @return string option list of auth group admin
     */
    private function getGroupSelect($selected_id = '', $is_superadmin = 0)
    {
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');
        $query = $this->Admin_model->ListUsersGroup($is_superadmin);
        $return = '';
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $sel = '';
                if ($row['id_auth_user_group'] == $selected_id) {
                    $sel = ' selected';
                }
                $return .= '<option value="' . $row['id_auth_user_group'] . '"' . $sel . '>' . $row['auth_user_group'] . '</option>';
            }
        }
        return $return;
    }

    /**
     *
     * @param int $selected_id
     * @return string option list of site
     */
    private function getSiteSelect($selected_id = '')
    {
        $this->load->model(getAdminFolder() . '/admin_model', 'Admin_model');
        $query = $this->Admin_model->getSites();
        $return = '';
        // if ($query->num_rows()>0)
        if ($query) {
            foreach ($query->result_array() as $row) {
                $sel = '';
                if ($row['id_site'] == $selected_id) {
                    $sel = ' selected';
                }
                $return .= '<option value="' . $row['id_site'] . '"' . $sel . '>' . $row['site_name'] . '</option>';
            }
        }
        return $return;
    }

}

/* End of file list_user_admin.php */
/* Location: ./application/controllers/webcontrol/list_user_admin.php */



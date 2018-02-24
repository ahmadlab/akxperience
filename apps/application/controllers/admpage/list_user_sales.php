<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * List_user_sales Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : List of User Member management
 *********************************************/
class List_user_sales extends CI_Controller
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
    private $max_ava_width;
    private $max_ava_height;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'list_user_sales';
        $this->template = getAdminFolder() . '/list_user_sales';
        $this->path_uri = getAdminFolder() . '/list_user_sales';
        $this->path = site_url(getAdminFolder() . '/list_user_sales');
        $this->title = get_admin_menu_title('list_user_sales');
        $this->id_menu_admin = get_admin_menu_id('list_user_sales');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_ava_height = 64;
        $this->max_ava_width = 64;
        $this->path_ava = './uploads/ava/';
        $this->path_car = './uploads/cars/';
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
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');

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

        $total_records = $this->Member_model->TotalUserSales(myUrlDecode($s_name), myUrlDecode($s_email));

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

        $query = $this->Member_model->GetAllUsersSales(myUrlDecode($s_name), myUrlDecode($s_email), $is_superadmin,
            $lmt, $per_page);

        foreach ($query->result_array() as $row_auth_user) {
            $no++;
            $id_user = $row_auth_user['id_user'];
            $username = $row_auth_user['username'];
            $email = $row_auth_user['email'];
            $edit_href = site_url($path_uri . '/edit/' . $id_user);
            $vip = ucfirst($row_auth_user['user_type']);

            $list_admin_arr[] = array(
                'no' => $no,
                'id_user' => $id_user,
                'email' => $email,
                'vip' => $vip,
                'username' => $username,
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
            'list_user_member' => $list_admin_arr,
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
        $this->parser->parse($template . '/list_user_sales.html', $data);
        $this->global_libs->print_footer();
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
     * add page
     */
    public function add()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');
        $this->load->library('encrypt');
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $ico = $_FILES['ava'];
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');
            $pass = $this->encrypt->encode($post['userpass']);


            $data_post = array(
                'username' => strtolower($post['username']),
                'password' => $pass,
                'email' => $post['email'],
                'card_id' => $post['card_id'],
                'birthday' => iso_date($post['birthday']),
                'phone_number' => $post['phone_number'],
                'phone' => (isset($post['phone'])) ? $post['phone'] : '',
                'religion' => $post['religion'],
                'sex' => $post['sexlist'],
                'address' => $post['address'],
                'city' => (isset($post['city'])) ? $post['city'] : '',
                'nearest_workshop' => $post['nearest_workshop'],
                'user_type' => 'sales',
                'status' => '1'
            );

            // insert data
            $id_user = $this->Member_model->InsertMemberUser($data_post);

            if ($ico['tmp_name'] != "") {
                $filename = 'member_ava_' . $id_user . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_ava, $filename, $this->max_ava_width,
                    $this->max_ava_height);

                $data_pic_content = array('avatar' => $content_to_db);

                // update to database
                $this->Member_model->UpdateMemberUser($data_pic_content, $id_user);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_user;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_user . '; Member Username:' . $post['username'] . '; Member Email:' . $post['email'];
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
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');
        $this->load->library('encrypt');
        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->path_uri);
        }


        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $ico = $_FILES['ava'];
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');

            $data_post = array(
                'username' => strtolower($post['username']),
                'email' => $post['email'],
                'card_id' => $post['card_id'],
                'birthday' => iso_date($post['birthday']),
                'phone_number' => $post['phone_number'],
                'phone' => (isset($post['phone'])) ? $post['phone'] : '',
                'religion' => $post['religion'],
                'sex' => $post['sexlist'],
                'address' => $post['address'],
                'city' => (isset($post['city'])) ? $post['city'] : '',
                'nearest_workshop' => $post['nearest_workshop'],
                'user_type' => 'sales'
            );

            if (($post['userpass'] != '') && (utf8_strlen($post['userpass']) >= 5)) {
                $pass = $this->encrypt->encode($post['userpass']);
                $data_pass = array('password' => $pass);
                $data_post = array_merge($data_post, $data_pass);
            }

            $this->Member_model->UpdateMemberUser($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $filename = 'member_ava_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_ava, $filename, $this->max_ava_width,
                    $this->max_ava_height);

                $data_pic_content = array('avatar' => $content_to_db);

                // update to database
                $this->Member_model->UpdateMemberUser($data_pic_content, $id);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_user . '; Member Username:' . $post['username'] . '; Member Email:' . $post['email'];
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
     * delete page post id
     */
    public function delete()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');
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

                $this->Member_model->DeleteMemberUser($id);
                $id_auth_user = implode(', ', $id);
                #insert to log
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete Member User ID : ' . $id_auth_user;
                $log_desc = 'Delete Member User ID : ' . $id_auth_user . ';';
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

    /**
     * public function delete_gallery.
     *
     * delete gallery page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete_gallery()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/Member_model');
        if ($this->input->post('id') != '' && ctype_digit($this->input->post('id'))) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $this->Member_model->DeleteAvatarByID($this->input->post('id'));
                #insert to log
                $log_last_user_id = $this->input->post('id');
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete picture ' . $this->title . ' ID : ' . $log_last_user_id;
                $log_desc = 'Delete picture ' . $this->title . ' ID : ' . $log_last_user_id . ';';
                $data_log = array(
                    'id_user' => $log_id_user,
                    'id_group' => $log_id_group,
                    'action' => $log_action,
                    'desc' => $log_desc,
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data_log);
                $this->session->set_flashdata('success_msg', $this->title . ' Avatar has been deleted.');
            } else {
                $this->session->set_flashdata('error_msg', 'You can\'t manage this content.');
            }
        }
    }

    /**
     *
     * @param int $selected
     * @return string $return option of user type
     */
    private function print_usertype($selected = '')
    {
        $return = '<option value="0">--- Choice User Type ---</option>';
        foreach (array('non vip', 'vip') as $types) {
            $selecteds = ($selected == $types) ? 'selected' : '';
            $return .= "<option $selecteds value='$types'>" . ucfirst($types) . "</option>";
        }
        return $return;
    }

    /**
     *
     * @param int $selected
     * @return string $return option of sex
     */
    private function print_sex($selected = '')
    {
        $return = '<option value="0">--- Choice Gender ---</option>';
        foreach (array('male', 'female', 'other') as $types) {
            $selecteds = ($selected == $types) ? 'selected' : '';
            $return .= "<option $selecteds value='$types'>" . ucfirst($types) . "</option>";
        }
        return $return;
    }

    /**
     *
     * @param int $selected
     * @return string $return option of religion
     */
    private function print_religion($selected = '')
    {
        $return = '<option value="0">--- Choice Religion ---</option>';
        foreach (array('islam', 'kristen', 'budha', 'hindu', 'katolik') as $types) {
            $selecteds = ($selected == $types) ? 'selected' : '';
            $return .= "<option $selecteds value='$types'>" . ucfirst($types) . "</option>";
        }
        return $return;
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
        $pic_thumbnail = '';
        $cars = $usrcar = $carsl = array();
        $insertcar = site_url($path_uri . '/insert_cars');

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Member_model->GetUsersMemberById($id);
            if ($query->num_rows() > 0) {
                $admin_info = $query->row_array();

                if ($admin_info['avatar'] != '' && file_exists($this->path_ava . $admin_info['avatar'])) {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/ava/' . $admin_info['avatar'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $admin_info['id_user'] . '"style="cursor:pointer;" >Delete Picture</a>
                    </div>';
                }
                // $usrcar = $this->db->get_where('user_cars',array('id_user' => $admin_info['id_user']))->result_array();
                $cars = $this->db->where_in('id_user', array($admin_info['id_user']))->order_by('create_date',
                    'asc')->get('view_user_cars');

                if ($cars->num_rows() > 0) {
                    $cars = $cars->result_array();
                    foreach ($cars as $k => $v) {
                        $carsl[$k]['thumb'] = base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'];
                        $carsl[$k]['ids'] = $v['id'];
                        $carsl[$k]['brands'] = $v['brands'];
                        $carsl[$k]['types'] = $v['types'];
                        $carsl[$k]['series'] = $v['series'];
                        $carsl[$k]['model'] = $v['model'];
                        $carsl[$k]['transmisi'] = $v['transmisi'];
                        $carsl[$k]['cc'] = $v['car_cc'];
                        $carsl[$k]['pn'] = $v['police_number'];
                        $carsl[$k]['vn'] = $v['vin_number'];
                        $carsl[$k]['stnk'] = $v['stnk_date'];
                        $carsl[$k]['ins_date'] = $v['insurance_date'];
                        $carsl[$k]['lmileage'] = $v['last_mileage'];
                        $carsl[$k]['delete'] = '<a class="delete_usercar" ida="' . $v['id'] . '"style="cursor:pointer;" >Delete Car</a>';
                    }
                }
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
            $action = site_url($template . '/add');
            // $Post['id'] = 0;
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
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
        if (isset($this->error['email'])) {
            $error_msg['email'] = alert_box($this->error['email'], 'error');
        } else {
            $error_msg['email'] = '';
        }
        if (isset($this->error['address'])) {
            $error_msg['address'] = alert_box($this->error['address'], 'error');
        } else {
            $error_msg['address'] = '';
        }
        if (isset($this->error['user_type'])) {
            $error_msg['user_type'] = alert_box($this->error['user_type'], 'error');
        } else {
            $error_msg['user_type'] = '';
        }
        if (isset($this->error['phone_number'])) {
            $error_msg['phone_number'] = alert_box($this->error['phone_number'], 'error');
        } else {
            $error_msg['phone_number'] = '';
        }
        if (isset($this->error['birthday'])) {
            $error_msg['birthday'] = alert_box($this->error['birthday'], 'error');
        } else {
            $error_msg['birthday'] = '';
        }
        if (isset($this->error['birthday'])) {
            $error_msg['birthday'] = alert_box($this->error['birthday'], 'error');
        } else {
            $error_msg['birthday'] = '';
        }
        if (isset($this->error['card_id'])) {
            $error_msg['card_id'] = alert_box($this->error['card_id'], 'error');
        } else {
            $error_msg['card_id'] = '';
        }
        if (isset($this->error['religion'])) {
            $error_msg['religion'] = alert_box($this->error['religion'], 'error');
        } else {
            $error_msg['religion'] = '';
        }
        if (isset($this->error['sex'])) {
            $error_msg['sex'] = alert_box($this->error['sex'], 'error');
        } else {
            $error_msg['sex'] = '';
        }

        // set value
        if ($this->input->post('username') != '') {
            $post['username'] = $this->input->post('username');
        } elseif ((int)$id > 0) {
            $post['username'] = $admin_info['username'];
        } else {
            $post['username'] = '';
        }

        if ($this->input->post('email') != '') {
            $post['email'] = $this->input->post('email');
        } elseif ((int)$id > 0) {
            $post['email'] = $admin_info['email'];
        } else {
            $post['email'] = '';
        }

        if ($this->input->post('address') != '') {
            $post['address'] = $this->input->post('address');
        } elseif ((int)$id > 0) {
            $post['address'] = $admin_info['address'];
        } else {
            $post['address'] = '';
        }

        if ($this->input->post('city') != '') {
            $post['citylist'] = $this->print_city($this->input->post('city'));
        } elseif ((int)$id > 0) {
            $post['citylist'] = $this->print_city($admin_info['city']);
        } else {
            $post['citylist'] = $this->print_city();
        }

        if ($this->input->post('nearest_workshop') != '') {
            $post['workshoplist'] = $this->print_workshop($this->input->post('nearest_workshop'));
        } elseif ((int)$id > 0) {
            $post['workshoplist'] = $this->print_workshop($admin_info['nearest_workshop']);
        } else {
            $post['workshoplist'] = $this->print_workshop();
        }

        if ($this->input->post('user_type') != '') {
            $post['usertypelist'] = $this->print_usertype($this->input->post('user_type'));
        } elseif ((int)$id > 0) {
            $post['usertypelist'] = $this->print_usertype($admin_info['user_type']);
        } else {
            $post['usertypelist'] = $this->print_usertype();
        }

        if ($this->input->post('sexlist') != '') {
            $post['sexlist'] = $this->print_sex($this->input->post('sexlist'));
        } elseif ((int)$id > 0) {
            $post['sexlist'] = $this->print_sex($admin_info['sex']);
        } else {
            $post['sexlist'] = $this->print_sex();
        }

        if ($this->input->post('phone_number') != '') {
            $post['phone_number'] = $this->input->post('phone_number');
        } elseif ((int)$id > 0) {
            $post['phone_number'] = $admin_info['phone_number'];
        } else {
            $post['phone_number'] = '';
        }

        if ($this->input->post('birthday') != '') {
            $post['birthday'] = $this->input->post('birthday');
        } elseif ((int)$id > 0) {
            $post['birthday'] = $admin_info['birthday'];
        } else {
            $post['birthday'] = '';
        }

        if ($this->input->post('religion') != '') {
            $post['religion'] = $this->print_religion($this->input->post('religion'));
        } elseif ((int)$id > 0) {
            $post['religion'] = $this->print_religion($admin_info['religion']);
        } else {
            $post['religion'] = $this->print_religion();
        }

        if ($this->input->post('card_id') != '') {
            $post['card_id'] = $this->input->post('card_id');
        } elseif ((int)$id > 0) {
            $post['card_id'] = $admin_info['card_id'];
        } else {
            $post['card_id'] = '';
        }

        if ($this->input->post('phone') != '') {
            $post['phone'] = $this->input->post('phone');
        } elseif ((int)$id > 0) {
            $post['phone'] = $admin_info['phone'];
        } else {
            $post['phone'] = '';
        }

        $carlst = get_carslst();
        $colorlst = print_car_color();

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'id' => $id,
            'carlst' => $carlst,
            'colorlst' => $colorlst,
            'post' => $post,
            'pic_thumbnail' => $pic_thumbnail,
            'pass_msg' => $pass_msg,
            'err_c' => $err_c,
            'required' => $required,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,
            'carlist' => $carsl,
            'insertcar' => $insertcar,
        );
        $this->parser->parse($template . '/list_user_sales_form.html', $data);
        $this->global_libs->print_footer();
    }

    private function print_city($selected = '')
    {
        $return = '<option value="0">--- Choice City ---</option>';
        $current_province = '';
        $this->db->select('ref_province.name AS pName, ref_city.name AS cName, ref_city.id');
        $this->db->join('ref_province', 'ref_province.id = ref_city.province_id');
        $cities = $this->db->get('ref_city');
        foreach ($cities->result_array() as $city) {
            if ($city["pName"] != $current_province) { # if category has changed
                if ($current_province != "") { # if there was already a category active
                    $return .= "</optgroup>"; # close it
                }
                $return .= '<optgroup label="' . $city['pName'] . '">'; # open a new group
                $current_province = $city['pName'];
            }
            $selecteds = ($selected == $city['id']) ? 'selected' : '';
            $return .= "<option $selecteds value='" . $city['id'] . "'>" . $city['cName'] . "</option>";
        }
        echo "</optgroup>"; # close the final group
        return $return;
    }

    /**
     *
     * @param int $selected
     * @return string $return option of user type
     */
    private function print_workshop($selected = '')
    {
        $return = '<option value="0">--- Choice Nearest Workshop ---</option>';
        $this->db->where('company_code', 'AK');
        $workshops = $this->db->get('ref_location');
        foreach ($workshops->result_array() as $workshop) {
            $selecteds = ($selected == $workshop['id_ref_location']) ? 'selected' : '';
            $return .= "<option $selecteds value='" . $workshop['id_ref_location'] . "'>" . $workshop['location'] . "</option>";
        }
        return $return;
    }

    /**
     *
     * @param int $id
     * @return string $this->error error
     */
    private function validateForm($id = 0)
    {
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['username'] == '') {
            $this->error['username'] = 'Please insert Username.<br/>';
        } else {
            // if(!$this->Member_model->CheckExistsUsername($post['username'],$id))
            // {
            // $this->error['username'] = 'Username already exists, please input different Username.<br/>';
            // }
            // else
            // {
            // if (utf8_strlen($post['username']) < 5)
            // {
            // $this->error['userpass'] = 'Username length must be at least 5 character(s).<br/>';
            // }
            // }
        }

        if ($post['card_id'] == '') {
            $this->error['card_id'] = 'Please insert Card Id.<br/>';
        } else {
            if ((utf8_strlen($post['card_id']) < 15) || (utf8_strlen($post['card_id']) > 16)) {
                // $this->error['card_id'] = 'Card Id length must be at least 15 character(s).<br/>';

            } else {

                if (!$this->Member_model->CheckExistsCardId($post['card_id'], $id)) {
                    $this->error['card_id'] = 'Card Id already in Please insert different.<br/>';
                }

            }
        }

        if ($post['phone_number'] == '') {
            $this->error['phone_number'] = 'Please insert Phone Number.<br/>';
        } else {
            // if ((utf8_strlen($post['phone_number']) < 7) || (utf8_strlen($post['phone_number']) > 12))
            // {
            // $this->error['phone_number'] = 'Phone Number length must be at least 7 number(s).<br/>';

            // }
        }

        if ($post['birthday'] == '') {
            $this->error['birthday'] = 'Please insert Birth Day.<br/>';
        } else {
            if ((utf8_strlen($post['birthday']) < 10) || (utf8_strlen($post['birthday']) > 10)) {
                $this->error['birthday'] = 'Please insert Birth Day.<br/>';

            }
        }

        if ($post['religion'] == '') {
            $this->error['religion'] = 'Please insert Religion.<br/>';
        } else {
            if ((utf8_strlen($post['religion']) == '') || (utf8_strlen($post['religion']) < 1)) {
                $this->error['religion'] = 'Please insert Religion.<br/>';

            }
        }

        if ($post['email'] == '') {
            $this->error['email'] = 'Please insert Email.<br/>';
        } else {
            if (!mycheck_email($post['email'])) {
                $this->error['email'] = 'Please insert correct Email.<br/>';
            } else {
                if (!$this->Member_model->CheckExistsEmail($post['email'], $id)) {
                    $this->error['email'] = 'Email already exists, please input different Email.<br/>';
                }
            }
        }


        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }


}

/* End of file list_user_sales.php */
/* Location: ./application/controllers/admpage/list_user_sales.php */
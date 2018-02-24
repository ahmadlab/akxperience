<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/***********************************************************
 * User Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : User management
 ************************************************************/
class User extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $modules;
    private $path_uri;
    private $template;
    private $path;
    private $title;
    private $max_width;
    private $max_height;
    private $thumb_width;
    private $thumb_height;
    private $thumb2_width;
    private $thumb2_height;
    private $path_pict;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        check_module_installed('user');

        $this->folder = getAdminFolder();
        $this->ctrl = 'user';
        $this->modules = 'modules';
        $this->path_uri = getAdminFolder() . '/user';
        $this->template = getAdminFolder() . '/modules/user';
        $this->path = site_url(getAdminFolder() . '/user');
        $this->max_width = 281;
        $this->max_height = 392;
        $this->thumb_width = 75;
        $this->thumb_height = 75;
        $this->thumb2_width = 150;
        $this->thumb2_height = 150;
        $this->path_pict = './uploads/user/';
        $this->title = get_admin_menu_title('user');
        $this->id_admin_menu = get_admin_menu_id('auth_user_group');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
    }

    /**
     * public function index.
     *
     * this function is same as main
     */
    public function index()
    {
        // load function main
        $this->main();
    }

    /**
     * public funcion main
     */
    public function main()
    {
        auth_admin();
        $this->session->set_userdata("referrer", current_url());
        $this->global_libs->print_header();
        $this->load->model(getAdminFolder() . '/user_model', 'User_model');

        $s_name = $this->uri->segment(4);
        $s_email = $this->uri->segment(5);
        $s_ref_publish = $this->uri->segment(6);
        $pg = $this->uri->segment(7);
        $per_page = 25;
        $uri_segment = 7;
        $no = 0;
        $path = $this->path . '/main/a/b/c/';
        $list_user = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $path_uri = $this->path_uri;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $template = $this->template;
        $breadcrumbs = array();
        $publish_list = array();
        $add_btn = site_url($path_uri . '/add');
        $is_superadmin = $this->is_superadmin;
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

        if (strlen($s_ref_publish) > 1) {
            $s_ref_publish = substr($s_ref_publish, 1);
        } else {
            $s_ref_publish = "";
        }

        $total_records = $this->User_model->getTotalUser(myUrlDecode($s_name), myUrlDecode($s_email), $s_ref_publish);

        if ($s_name) {
            $path = str_replace("/a/", "/a" . $s_name . "/", $path);
        }

        if ($s_email) {
            $path = str_replace("/b/", "/b" . $s_email . "/", $path);
        }

        if ($s_ref_publish) {
            $path = str_replace("/c/", "/c" . $s_ref_publish . "/", $path);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }

        $no = $lmt;

        $query = $this->User_model->GetAllUser(myUrlDecode($s_name), myUrlDecode($s_email), $s_ref_publish, $lmt,
            $per_page);

        foreach ($query->result_array() as $row_user) {
            $no++;
            $id_user = $row_user['id_user'];
            $name = $row_user['name'];
            $email = $row_user['email'];
            $sex = $row_user['sex'];
            $dob = iso_date($row_user['dob']);
            $last_login = iso_date_time($row_user['last_login']);
            $create_date = iso_date_time($row_user['join_date']);
            $activation_status = $row_user['activation_status'];
            $status = $activation_status == 1 ? "Active" : "Not Active";

            $edit_href = site_url($path_uri . '/edit/' . $id_user);

            $list_user[] = array(
                'no' => $no,
                'id_user' => $id_user,
                'name' => $name,
                'email' => $email,
                'sex' => $sex,
                'dob' => $dob,
                'last_login' => $last_login,
                'join_date' => $create_date,
                'activation_status' => $activation_status,
                'status' => $status,
                'viewed' => $viewed,
                'edit_href' => $edit_href,
            );
        }

        $publish_list = $this->getRefStatusSelect($s_ref_publish);

        // paging
        $paging = global_paging($total_records, $per_page, $path, $uri_segment);
        if (!$paging) {
            $paging = '<ul class="pagination"><li class="unavailable">1</a></li></ul>';
        }
        //end of paging

        $error_msg = alert_box($this->session->flashdata('error_msg'), 'error');
        $success_msg = alert_box($this->session->flashdata('success_msg'), 'success');
        $info_msg = alert_box($this->session->flashdata('info_msg'), 'warning');

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            'list_user' => $list_user,
            'publish_list' => $publish_list,
            's_name' => myUrlDecode($s_name),
            's_email' => myUrlDecode($s_email),
            's_ref_publish' => $s_ref_publish,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
        );
        $this->parser->parse($template . '/user.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * private function getRefPublishSelect.
     *
     * retrieve publish status reference from database.
     *
     * @param int $selectitem
     *  (Optional) set selected item
     * @return string $tmp_publish generated list option
     */
    private function getRefStatusSelect($selected = '')
    {
        $tmp_publish = '';
        $pub[0] = 'Not Active';
        $pub[1] = 'Active';
        for ($a = 1; $a >= 0; $a--) {
            $sel = '';
            if ($selected == $a && $selected != '') {
                $sel = 'selected="selected"';
            }
            $tmp_publish .= '<option value="' . $a . '" ' . $sel . '>' . $pub[$a] . '</option>';
        }
        return $tmp_publish;

    }

    /**
     * get all template to select list
     * @param int $selected
     * @return string $tmp_template template option list
     */
    private function getSelectTemplate($selected = '')
    {
        $tmp_template = '';
        $this->load->model(getAdminFolder() . '/user_model', 'User_model');
        $query = $this->User_model->getTemplates();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($selected == $row['id_template']) {
                    $tmp_template .= '<option value="' . $row['id_template'] . '" selected="selected">' . $row['title'] . '</option>';
                } else {
                    $tmp_template .= '<option value="' . $row['id_template'] . '">' . $row['title'] . '</option>';
                }
            }
        }
        return $tmp_template;
    }

    /**
     * public function search.
     *
     * search content from User Controller.
     * @post string $s_title
     *  post title of user
     * @post int $s_parent
     *  post parent of user
     * @post int $s_ref_publish
     *  post publish reference of user
     * @return redirect to main page with search key
     */
    public function search()
    {
        auth_admin();
        $s_name = myUrlEncode($this->input->post('s_name'));
        $s_email = myUrlEncode($this->input->post('s_email'));
        $s_ref_publish = $this->input->post('s_ref_publish');
        redirect($this->path_uri . '/main/a' . $s_name . '/b' . $s_email . '/c' . $s_ref_publish);
    }

    /**
     * public function edit.
     *
     * edit content page
     * @param int $get_id
     *  id of editing content
     */
    public function edit($get_id = 0)
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/user_model', 'User_model');

        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->path_uri);
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $post = purify($this->input->post());
            $image = $_FILES['image'];
            $post['username'] = strtolower($post['username']);
            $post['plurk'] = strtolower($post['plurk']);
            $now = date('Y-m-d H:i:s');
            unset ($post['submit_save']);

            $data_post = $post;

            // update data
            $this->User_model->UpdateUser($id, $data_post);

            /* upload pic if post */
            $data_pic_image = array();

            // upload picture content
            if ($image['tmp_name'] != "") {
                $filename = url_title($post['username']);
                $path_pic = $this->path_pict . $post['username'] . '/';

                $path_pic = "./uploads/user/" . $post['username'] . "/";

                $pic_foto = image_resize_to_folder($image, $path_pic, $filename, $this->max_width, $this->max_height);
                $pic_thumb = image_resize_to_folder($image, $path_pic, $filename . '-thumb', $this->thumb_width,
                    $this->thumb_height);
                $pic_thumb2 = image_resize_to_folder($image, $path_pic, $filename . '-thumb2', $this->thumb2_width,
                    $this->thumb2_height);

                $data_pic_image = array(
                    'image' => $pic_foto,
                    'thumbnail' => $pic_thumb,
                );

                // update to database
                $this->User_model->UpdateUser($id, $data_pic_image);
            }

            #insert to log
            $log_last_user_id = $id;
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Edit ' . $this->title . ' ID : ' . $log_last_user_id;
            $log_desc = 'Edit ' . $this->title . ' ID : ' . $log_last_user_id . '; Name:' . $post['name'] . '; Username : ' . $post['username'] . ' ';
            $data_log = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_log);

            $this->session->set_flashdata('success_msg', $this->title . ' has been updated.');
            if ($this->session->userdata('referrer') != '') {
                redirect($this->session->userdata('referrer'));
            } else {
                redirect($this->path_uri);
            }
        }
        $this->getForm($id);
    }

    /**
     * private function getForm.
     *
     * generate process of add / edit page.
     *
     * @param int $id
     *  (Optional) id of page.
     * @return print/show form for add/edit page
     */
    private function getForm($id = 0)
    {
        $this->global_libs->print_header();

        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $template = $this->template;
        $path_uri = $this->path_uri;
        $id = (int)$id;
        $best_w = $this->max_width;
        $best_h = $this->max_height;
        $error_msg = array();
        $post = array();
        $status_list = array();
        $breadcrumbs = array();
        $pic_user = '';
        $cancel_btn = site_url($path_uri);
        $success_msg = ($this->session->flashdata('success_msg') != '') ? $this->session->flashdata('success_msg') : '';

        $breadcrumbs[] = array(
            'text' => 'Home',
            'href' => site_url($folder . '/home'),
            'class' => ''
        );

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->User_model->GetUserByID($id);
            if ($query->num_rows() > 0) {
                $user_info = $query->row_array();
                if ($user_info['image'] != '') {
                    $pic_user = '<div id="print-picture-image">
                            <img src="' . base_url('uploads/' . $file_app . '/' . $user_info['username'] . '/' . $user_info['image'] . '') . '" width="150"><br/>
                            <a id="delete-pic-image" style="cursor:pointer;" >Delete Picture</a>
                    </div>';
                }
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
            $action = site_url($path_uri . '/edit/' . $id);
        } else {
            $breadcrumbs[] = array(
                'text' => 'Add',
                'href' => '#',
                'class' => 'class="current"'
            );
            $action = site_url($path_uri . '/add');
        }

        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }

        if (isset($this->error['activation_status'])) {
            $error_msg['activation_status'] = alert_box($this->error['activation_status'], 'error');
        } else {
            $error_msg['activation_status'] = '';
        }
        if (isset($this->error['username'])) {
            $error_msg['username'] = alert_box($this->error['username'], 'error');
        } else {
            $error_msg['username'] = '';
        }
        if (isset($this->error['password'])) {
            $error_msg['password'] = alert_box($this->error['password'], 'error');
        } else {
            $error_msg['password'] = '';
        }
        if (isset($this->error['name'])) {
            $error_msg['name'] = alert_box($this->error['name'], 'error');
        } else {
            $error_msg['name'] = '';
        }
        if (isset($this->error['display_name'])) {
            $error_msg['display_name'] = alert_box($this->error['display_name'], 'error');
        } else {
            $error_msg['display_name'] = '';
        }
        if (isset($this->error['email'])) {
            $error_msg['email'] = alert_box($this->error['email'], 'error');
        } else {
            $error_msg['email'] = '';
        }
        if (isset($this->error['sex'])) {
            $error_msg['sex'] = alert_box($this->error['sex'], 'error');
        } else {
            $error_msg['sex'] = '';
        }
        if (isset($this->error['dob'])) {
            $error_msg['dob'] = alert_box($this->error['dob'], 'error');
        } else {
            $error_msg['dob'] = '';
        }
        if (isset($this->error['mobile'])) {
            $error_msg['mobile'] = alert_box($this->error['mobile'], 'error');
        } else {
            $error_msg['mobile'] = '';
        }
        if (isset($this->error['plurk'])) {
            $error_msg['plurk'] = alert_box($this->error['plurk'], 'error');
        } else {
            $error_msg['plurk'] = '';
        }
        if (isset($this->error['image'])) {
            $error_msg['image'] = alert_box($this->error['image'], 'error');
        } else {
            $error_msg['image'] = '';
        }

        // set value
        if ($this->input->post('username') != '') {
            $post['username'] = $this->input->post('username');
        } elseif ((int)$id > 0) {
            $post['username'] = $user_info['username'];
        } else {
            $post['username'] = '';
        }
        if ($this->input->post('name') != '') {
            $post['name'] = $this->input->post('name');
        } elseif ((int)$id > 0) {
            $post['name'] = $user_info['name'];
        } else {
            $post['name'] = '';
        }
        if ($this->input->post('display_name') != '') {
            $post['display_name'] = $this->input->post('display_name');
        } elseif ((int)$id > 0) {
            $post['display_name'] = $user_info['display_name'];
        } else {
            $post['display_name'] = '';
        }
        if ($this->input->post('email') != '') {
            $post['email'] = $this->input->post('email');
        } elseif ((int)$id > 0) {
            $post['email'] = $user_info['email'];
        } else {
            $post['email'] = '';
        }
        if ($this->input->post('sex') != '') {
            $post['sex'] = $this->input->post('sex');
        } elseif ((int)$id > 0) {
            $post['sex'] = $user_info['sex'];
        } else {
            $post['sex'] = '';
        }
        if ($this->input->post('dob') != '') {
            $post['dob'] = $this->input->post('dob');
        } elseif ((int)$id > 0) {
            $post['dob'] = $user_info['dob'];
        } else {
            $post['dob'] = '';
        }
        if ($this->input->post('interested') != '') {
            $post['interested'] = $this->input->post('interested');
        } elseif ((int)$id > 0) {
            $post['interested'] = iso_date($user_info['interested'], '-');
        } else {
            $post['interested'] = '';
        }
        if ($this->input->post('hometown') != '') {
            $post['hometown'] = $this->input->post('hometown');
        } elseif ((int)$id > 0) {
            $post['hometown'] = $user_info['hometown'];
        } else {
            $post['hometown'] = '';
        }
        if ($this->input->post('current_location') != '') {
            $post['current_location'] = $this->input->post('current_location');
        } elseif ((int)$id > 0) {
            $post['current_location'] = $user_info['current_location'];
        } else {
            $post['current_location'] = '';
        }
        if ($this->input->post('relationship') != '') {
            $post['relationship'] = $this->input->post('relationship');
        } elseif ((int)$id > 0) {
            $post['relationship'] = $user_info['relationship'];
        } else {
            $post['relationship'] = '';
        }
        if ($this->input->post('mobile') != '') {
            $post['mobile'] = $this->input->post('mobile');
        } elseif ((int)$id > 0) {
            $post['mobile'] = $user_info['mobile'];
        } else {
            $post['mobile'] = '';
        }
        if ($this->input->post('mobile2') != '') {
            $post['mobile2'] = $this->input->post('mobile2');
        } elseif ((int)$id > 0) {
            $post['mobile2'] = $user_info['mobile2'];
        } else {
            $post['mobile2'] = '';
        }
        if ($this->input->post('landline') != '') {
            $post['landline'] = $this->input->post('landline');
        } elseif ((int)$id > 0) {
            $post['landline'] = $user_info['landline'];
        } else {
            $post['landline'] = '';
        }
        if ($this->input->post('yahoo') != '') {
            $post['yahoo'] = $this->input->post('yahoo');
        } elseif ((int)$id > 0) {
            $post['yahoo'] = $user_info['yahoo'];
        } else {
            $post['yahoo'] = '';
        }
        if ($this->input->post('msn') != '') {
            $post['msn'] = $this->input->post('msn');
        } elseif ((int)$id > 0) {
            $post['msn'] = $user_info['msn'];
        } else {
            $post['msn'] = '';
        }
        if ($this->input->post('website') != '') {
            $post['website'] = $this->input->post('website');
        } elseif ((int)$id > 0) {
            $post['website'] = $user_info['website'];
        } else {
            $post['website'] = '';
        }
        if ($this->input->post('blackberry') != '') {
            $post['blackberry'] = $this->input->post('blackberry');
        } elseif ((int)$id > 0) {
            $post['blackberry'] = $user_info['blackberry'];
        } else {
            $post['blackberry'] = '';
        }
        if ($this->input->post('facebook') != '') {
            $post['facebook'] = $this->input->post('facebook');
        } elseif ((int)$id > 0) {
            $post['facebook'] = $user_info['facebook'];
        } else {
            $post['facebook'] = '';
        }
        if ($this->input->post('friendster') != '') {
            $post['friendster'] = $this->input->post('friendster');
        } elseif ((int)$id > 0) {
            $post['friendster'] = $user_info['friendster'];
        } else {
            $post['friendster'] = '';
        }
        if ($this->input->post('plurk') != '') {
            $post['plurk'] = $this->input->post('plurk');
        } elseif ((int)$id > 0) {
            $post['plurk'] = $user_info['plurk'];
        } else {
            $post['plurk'] = '';
        }
        if ($this->input->post('twitter') != '') {
            $post['twitter'] = $this->input->post('twitter');
        } elseif ((int)$id > 0) {
            $post['twitter'] = $user_info['twitter'];
        } else {
            $post['twitter'] = '';
        }
        if ($this->input->post('instagram') != '') {
            $post['instagram'] = $this->input->post('instagram');
        } elseif ((int)$id > 0) {
            $post['instagram'] = $user_info['instagram'];
        } else {
            $post['instagram'] = '';
        }
        if ($this->input->post('activation_status') != '') {
            $post['activation_status'] = $this->input->post('activation_status');
        } elseif ((int)$id > 0) {
            $post['activation_status'] = $user_info['activation_status'];
        } else {
            $post['activation_status'] = 1;
        }

        // active reference load
        if ($post['activation_status'] == '' || $post['activation_status'] == 1) {
            $pub_1 = 'checked="checked"';
            $pub_2 = '';
        } else {
            $pub_1 = '';
            $pub_2 = 'checked="checked"';
        }
        $status_list[] = array(
            'val' => 1,
            'text' => 'Active',
            'sel' => $pub_1,
            'id' => 'active_yes',
            'class' => 'left',
        );
        $status_list[] = array(
            'val' => 0,
            'text' => 'Not Active',
            'sel' => $pub_2,
            'id' => 'active_none',
            'class' => '',
        );

        $opt_gender[] = 'Male';
        $opt_gender[] = 'Female';
        $gender_list = $this->getOptionSelect($opt_gender, $post['sex']);

        $opt_relationship[] = 'Single';
        $opt_relationship[] = 'Married';
        $opt_relationship[] = 'In A Relationship';
        $relationship_list = $this->getOptionSelect($opt_relationship, $post['relationship']);

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'best_w' => $best_w,
            'best_h' => $best_h,
            'id_user' => $id,
            'post' => $post,
            'status_list' => $status_list,
            'gender_list' => $gender_list,
            'relationship_list' => $relationship_list,
            'pic_user' => $pic_user,
            'action' => $action,
            'success_msg' => $success_msg,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,
        );
        $this->parser->parse($template . '/user_form.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     *
     * @param array $opt
     * @param string $selected
     * @return string print option list
     */
    private function getOptionSelect($opt = array(), $selected = '')
    {
        $return = '';
        for ($a = 0; $a < count($opt); $a++) {
            if ($selected != '' && $selected == $opt[$a]) {
                $return .= '<option value="' . $opt[$a] . '" selected="selected">' . $opt[$a] . '</option>';
            } else {
                $return .= '<option value="' . $opt[$a] . '">' . $opt[$a] . '</option>';
            }
        }
        return $return;
    }

    /**
     * private function validateForm.
     *
     * validation post value from page form
     * @param int $id
     *  (Optional) set id of page
     * @return string $this->error
     *  returning error value of each field
     */
    private function validateForm($id = 0)
    {
        $this->load->model(getAdminFolder() . '/user_model', 'User_model');
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['name'] == '') {
            $this->error['name'] = 'Please insert Full Name.<br/>';
        } else {
            if (utf8_strlen($post['name']) < 3) {
                $this->error['name'] = 'Full Name length must be at least 3 character(s).<br/>';
            }
        }
        if ($post['display_name'] == '') {
            $this->error['display_name'] = 'Please insert Display Name.<br/>';
        }
        if ($post['sex'] == '') {
            $this->error['sex'] = 'Please choose Gender.<br/>';
        }
        if ($post['relationship'] == '') {
            $this->error['relationship'] = 'Please choose Relationship.<br/>';
        }
        if ($post['dob'] == '') {
            $this->error['dob'] = 'Please input Date of Birth.<br/>';
        } else {
            if (!mycheck_isodate($post['dob'])) {
                $this->error['dob'] = 'Please input correct Date of Birth.<br/>';
            }
        }
        if ($post['username'] == '') {
            $this->error['username'] = 'Please input Username.<br/>';
        } else {
            if (!$this->User_model->CheckExistsUsername($post['username'], $id)) {
                $this->error['username'] = 'Username is already taken. Please input another Username.<br/>';
            }
        }
        if ($post['plurk'] == '') {
            $this->error['plurk'] = 'Please input Username.<br/>';
        } else {
            if (!$this->User_model->CheckExistsPlurk($post['plurk'], $id)) {
                $this->error['plurk'] = 'Plurk is already use. Please input another Plurk.<br/>';
            }
        }
        if ($post['email'] == '') {
            $this->error['email'] = 'Please input Email.<br/>';
        } else {
            if (!mycheck_email($post['email'])) {
                $this->error['email'] = 'Please input correct Email.<br/>';
            } else {
                if (!$this->User_model->CheckExistsEmail($post['email'], $id)) {
                    $this->error['email'] = 'Email is already taken. Please input another Email.<br/>';
                }
            }
        }
        if ($post['mobile'] == '') {
            $this->error['mobile'] = 'Please insert Mobile.<br/>';
        } else {
            if (!ctype_digit($post['mobile'])) {
                $this->error['mobile'] = 'Please input correct Mobile Number (digits only).<br/>';
            } else {
                if (utf8_strlen($post['mobile']) < 10) {
                    $this->error['mobile'] = 'Mobile Number length must be at least 10 digits (with area code).<br/>';
                }
            }
        }
        if ($post['mobile2'] != '' && !ctype_digit($post['mobile2'])) {
            $this->error['mobile'] = 'Please insert correct Mobile 2 Number (digits only).<br/>';
        } else {
            if (utf8_strlen($post['mobile2']) < 10) {
                $this->error['mobile'] = 'Mobile 2 Number length must be at least 10 digits (with area code).<br/>';
            }
        }
        if ($post['landline'] == '') {
            $this->error['landline'] = 'Please insert Mobile.<br/>';
        } else {
            if (!ctype_digit($post['landline'])) {
                $this->error['landline'] = 'Please input correct Mobile Number (digits only).<br/>';
            } else {
                if (utf8_strlen($post['landline']) < 10) {
                    $this->error['landline'] = 'Mobile Number length must be at least 10 digits (with area code).<br/>';
                }
            }
        }

        $picture = $_FILES['image'];
        if (!empty($picture['tmp_name'])) {
            $check_pic = $this->validatePicture('image');
            if (!empty($check_pic)) {
                $this->error['image'] = $check_pic . '<br/>';
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * private function validatePicture.
     *
     * validation for file upload from form
     *
     * @param string $fieldname
     *  fieldname of input file form
     */
    private function validatePicture($fieldname)
    {
        $error = '';
        if (!empty($_FILES[$fieldname]['error'])) {
            switch ($_FILES[$fieldname]['error']) {
                case '1':
                    $error = 'Upload maximum file is 4 MB.';
                    break;
                case '2':
                    $error = 'File is too big, please upload with smaller size.';
                    break;
                case '3':
                    $error = 'File uploaded, but only halef of file.';
                    break;
                case '4':
                    $error = 'There is no File to upload';
                    break;
                case '6':
                    $error = 'Temporary folder not exists, Please try again.';
                    break;
                case '7':
                    $error = 'Failed to record File into disk.';
                    break;
                case '8':
                    $error = 'Upload file has been stop by extension.';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }
        } elseif (empty($_FILES[$fieldname]['tmp_name']) || $_FILES[$fieldname]['tmp_name'] == 'none') {
            $error = 'There is no File to upload.';
        } elseif ($_FILES[$fieldname]['size'] > 4096000) {
            $error = 'Upload maximum file is 4 MB.';
        } else {
            //$get_ext = substr($_FILES[$fieldname]['name'],strlen($_FILES[$fieldname]['name'])-3,3);	
            $cekfileformat = check_image_type($_FILES[$fieldname]);
            if (!$cekfileformat) {
                $error = 'Upload Picture only allow (jpg, gif, png)';
            }
        }

        return $error;
    }


    /**
     * public function delete.
     *
     * delete page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/user_model', 'User_model');
        if ($this->input->post('id') != "" && ctype_digit($this->input->post('id')) && $this->input->post('id') > 0) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {

                $this->User_model->DeleteUser($this->input->post('id'));

                #insert to log
                $log_last_user_id = $this->input->post('id');
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete ' . $this->title . ' ID : ' . $log_last_user_id;
                $log_desc = 'Delete ' . $this->title . ' ID : ' . $log_last_user_id . ';';
                $data_log = array(
                    'id_user' => $log_id_user,
                    'id_group' => $log_id_group,
                    'action' => $log_action,
                    'desc' => $log_desc,
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data_log);
                $this->session->set_flashdata('success_msg', $this->title . ' (s) has been deleted.');
            } else {
                $this->session->set_flashdata('error_msg', 'You can\'t manage this content.<br/>');
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Delete failed. Please try again.');
        }
    }

    /**
     * public function delete_picture.
     *
     * delete picture page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete_picture()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/user_model', 'User_model');
        if ($this->input->post('id') != '' && ctype_digit($this->input->post('id'))) {
            $id = $this->input->post('id');
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $this->User_model->DeletePictureByID($id);
                #insert to log
                $log_last_user_id = $id;
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
                echo 'Picture ' . $this->title . ' has been deleted.';
            } else {
                echo 'You can\'t manage this content.<br/>';
            }
        }
    }

    /**
     * public function change_publish.
     *
     * change publish page by request get.
     */
    public function change_publish($id = 0)
    {
        if ($id) {
            if (ctype_digit($id)) {
                $this->load->model(getAdminFolder() . '/user_model', 'User_model');
                $return = $this->User_model->ChangeStatus($id);
                echo $return;
            }
        }
    }

}

/* End of file user.php */
/* Location: ./application/controllers/webcontrol/user.php */


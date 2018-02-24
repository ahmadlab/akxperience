<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * List_user_member Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : List of User Member management
 *********************************************/
class List_user_member extends CI_Controller
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
        $this->ctrl = 'list_user_member';
        $this->template = getAdminFolder() . '/list_user_member';
        $this->path_uri = getAdminFolder() . '/list_user_member';
        $this->path = site_url(getAdminFolder() . '/list_user_member');
        $this->title = get_admin_menu_title('list_user_member');
        $this->id_menu_admin = get_admin_menu_id('list_user_member');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_ava_height = 64;
        $this->max_ava_width = 64;
        $this->path_ava = './uploads/ava/';
        $this->path_car = './uploads/cars/';
        $this->load->helper('mail');
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');
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

        // if($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('x')){
        //$this->exportoxl();
        //exit;
        // }

        $this->global_libs->print_header();
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');

        $s_name = $this->uri->segment(4);
        $s_email = $this->uri->segment(5);
        $s_stat = $this->uri->segment(6);
        $s_type = $this->uri->segment(7);
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

        if (strlen($s_stat) > 1) {
            $s_stat = substr($s_stat, 1);
        } else {
            $s_stat = "";
        }

        if (strlen($s_type) > 1) {
            $s_type = substr($s_type, 1);
        } else {
            $s_type = "";
        }

        $stat = "<option value=''>--- Select Status ---</option>";
        foreach (array('1', '2') as $v) {
            $label = ($v == '1') ? 'Active' : 'Non Active';
            $selected = ($s_stat == $v) ? 'selected' : '';
            $stat .= "<option $selected value='$v'>$label</option>";
        }

        $type = "<option value=''>--- Select Type ---</option>";
        foreach (array('reguler', 'vip') as $v) {
            // $label 		= ($v == '1') ? 'Active' : 'Non Active';
            $selected = ($s_type == $v) ? 'selected' : '';
            $type .= "<option $selected value='$v'>$v</option>";
        }

        $total_records = $this->Member_model->TotalUserMember(myUrlDecode($s_name), myUrlDecode($s_email), $s_stat,
            myUrlDecode($s_type));

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

        if ($s_stat) {
            $dis_urut = "none";
            $path = str_replace("/c/", "/c" . $s_stat . "/", $path);
            ($search_query == '' ? $search_query .= $s_stat : $search_query .= ' + ' . $s_stat);
        }

        if ($s_type) {
            $dis_urut = "none";
            $path = str_replace("/d/", "/d" . $s_type . "/", $path);
            ($search_query == '' ? $search_query .= $s_type : $search_query .= ' + ' . $s_type);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Member_model->GetAllUsersMember(myUrlDecode($s_name), myUrlDecode($s_email), $s_stat,
            myUrlDecode($s_type), $is_superadmin, $lmt, $per_page);

        foreach ($query->result_array() as $row_auth_user) {
            $no++;
            $id_user = $row_auth_user['id_user'];
            $username = $row_auth_user['username'];
            $email = $row_auth_user['email'];
            $edit_href = site_url($path_uri . '/edit/' . $id_user);
            $vip = ucfirst($row_auth_user['user_type']);
            $dvip = $row_auth_user['vip_date'] != '' ? date('D, d M Y', $row_auth_user['vip_date']) : '';
            $status = $row_auth_user['status'] != '0' ? 'Active' : 'Not Active';
            $expire = ($row_auth_user['expire_date']) ? date('D,d M Y', $row_auth_user['expire_date']) : '';

            $list_admin_arr[] = array(
                'no' => $no,
                'id_user' => $id_user,
                'email' => $email,
                'vip' => $vip,
                'status' => $status,
                'username' => $username,
                'edit_href' => $edit_href,
                'dvip' => $dvip,
                'id' => $id_user,
                'expire' => $expire,
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
            's_stat' => $stat,
            's_type' => $type,
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
        $this->parser->parse($template . '/list_user_member.html', $data);
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
        $s_stat = myUrlEncode(trim($this->input->post('s_stat')));
        $s_type = myUrlEncode(trim($this->input->post('s_type')));
        redirect($this->template . '/main/a' . $s_name . '/b' . $s_email . '/c' . $s_stat . '/d' . $s_type);
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
                $return = $this->Member_model->ChangePublishMember($id);
                echo $return;
            }
        }
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
            $n = date('Y-m-d');
            $pass = $this->encrypt->encode($post['userpass']);


            $data_post = array(
                'username' => strtolower($post['username']),
                'password' => $pass,
                'email' => $post['email'],
                'card_id' => $post['card_id'],
                'birthday' => $this->reverseDate($post['birthday']),
                'phone_number' => $post['phone_number'],
                'phone' => (isset($post['phone'])) ? $post['phone'] : '',
                'religion' => $post['religion'],
                'sex' => $post['sexlist'],
                'address' => $post['address'],
                'province' => $post['province'],
                'city' => (isset($post['city'])) ? $post['city'] : '',
                'nearest_workshop' => $post['nearest_workshop'],
                'user_type' => $post['user_type']
            );

            // insert data
            $id_user = $this->Member_model->InsertMemberUser($data_post);

            if ($post['user_type'] == 'vip') {
                $update = array('vip_date' => strtotime($now), 'expire_date' => strtotime($n . ' + 365 day'));
                $this->db->where('id_user', $id_user)->update('user', $update);

                $slug = ($post['username'] != '' && $post['username'] != '-') ? $post['username'] : $post['email'];
                $conf['from_name'] = $slug;
                $conf['subject'] = 'Congratulation, you are now VIP Member!';
                $conf['to'] = $post['email'];
                $conf['content'] = "Dear Mr./Mrs. " . $slug . "<br>";
                $conf['content'] .= "Congratulation,<br>";
                $conf['content'] .= "You are now Kreasi Auto Kencana VIP Member, <br><br>";
                $conf['content'] .= "Here is your Login info,<br>";
                $conf['content'] .= "Username : " . $data_post['username'] . "<br>";
                $conf['content'] .= "Password : " . $data_post['password'] . "<br><br>";
                $conf['content'] .= "Our special services on AK Experience Mobile Apps has been activated.<br><br>";
                $conf['content'] .= "Good Day!<br><br>";
                $conf['content'] .= "Best Regards,<br>";
                $conf['content'] .= "Kreasi Auto Kencana, PT";

                sent_mail($conf);

            }

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
            $n = date('Y-m-d');

            $data_post = array(
                'username' => $post['username'],
                'email' => $post['email'],
                'card_id' => $post['card_id'],
                'birthday' => $this->reverseDate($post['birthday']),
                'phone_number' => $post['phone_number'],
                'phone' => (isset($post['phone'])) ? $post['phone'] : '',
                'religion' => $post['religion'],
                'sex' => $post['sexlist'],
                'address' => $post['address'],
                'province' => $post['province'],
                'city' => (isset($post['city'])) ? $post['city'] : '',
                'nearest_workshop' => $post['nearest_workshop'],
                'user_type' => $post['user_type']
            );

            if (($post['userpass'] != '') && (utf8_strlen($post['userpass']) >= 5)) {
                $pass = $this->encrypt->encode($post['userpass']);
                $data_pass = array('password' => $pass);
                $data_post = array_merge($data_post, $data_pass);
            }

            if (db_get_one('user', 'user_type',
                    "id_user = '$id'") != $post['user_type'] && $post['user_type'] == 'vip'
            ) {

                $update = array('vip_date' => strtotime($now), 'expire_date' => strtotime($n . ' + 365 day'));
                $this->db->where('id_user', $id)->update('user', $update);


                $slug = ($post['username'] != '' && $post['username'] != '-') ? $post['username'] : $post['email'];
                $conf['from_name'] = $slug;
                $conf['subject'] = 'Congratulation, you are now VIP Member!';
                $conf['to'] = $post['email'];
                $conf['content'] = "Dear Mr./Mrs. " . $slug . "<br>";
                $conf['content'] .= "Congratulation,<br>";
                $conf['content'] .= "You are now Kreasi Auto Kencana VIP Member, <br><br>";
                $conf['content'] .= "Here is your Login info,<br>";
                $conf['content'] .= "Username : " . $data_post['username'] . "<br>";
                $conf['content'] .= "Password : " . $data_post['password'] . "<br><br>";
                $conf['content'] .= "Our special services on AK Experience Mobile Apps has been activated.<br><br>";
                $conf['content'] .= "Good Day!<br><br>";
                $conf['content'] .= "Best Regards,<br>";
                $conf['content'] .= "Kreasi Auto Kencana, PT";

                sent_mail($conf);
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
     * insert user car page
     * @param int $get_id
     */
    public function insert_user_car()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');
        $post = purify($this->input->post());

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($post['car']) && isset($post['id_user']) /* && isset($post['last_mileage']) */
            && isset($post['police_number']) && isset($post['vin_number']) && isset($post['stnk_date']) /* && isset($post['insurance_date']) */ && isset($post['car_color'])
        ) {
            $post['id_car'] = $post['car'];
            $post['id_car_color'] = $post['car_color'];
            $post['stnk_date'] = iso_date($post['stnk_date']);
            // $post['insurance_date'] = iso_date($post['insurance_date']);

            if (isset($post['insurance_date']) && $post['insurance_date'] != '') {
                $post['insurance_date'] = iso_date($post['insurance_date']);
            }

            if (isset($post['last_mileage']) && $post['last_mileage'] != '') {
                $post['last_mileage'] = $post['last_mileage'];
            }

            unset($post['car'], $post['car_color']);

            if ($post['id']) {
                $id = $post['id'];
                unset($post['id']);
                $this->db->where('id_user_cars', $id)->update('user_cars', $post);

                if ($this->db->_error_message()) {
                    echo 0, exit;
                } else {
                    $mode = 'updated';
                }

            } else {
                unset($post['id']);
                if (isset($post['buy_from'])) {
                    $post['point_reward'] = 3;
                    $post['is_from_ak'] = 1;
                    unset($post['buy_from']);
                }
                $this->db->insert('user_cars', $post);
                if ($this->db->_error_message()) {
                    echo 0, exit;
                } else {
                    $mode = 'inserted';
                }
            }

            $id = ($mode == 'inserted') ? $this->db->insert_id() : $id;
            $ucar = $this->db->get_where('view_user_cars', array('id' => $id));
            if ($ucar->num_rows() > 0) {
                $ucar = $ucar->row_array();

                $thumb = ($ucar['car_color_thumb'] != '' && file_exists($this->path_car . '/colors/' . $ucar['car_color_thumb'])) ? base_url() . 'uploads/cars/colors/' . $ucar['car_color_thumb'] : '';
                $html = ($mode == 'inserted') ? "<div id='wrapper-" . $ucar['id'] . "-car' class='four columns' style='float:left;'>" : ' ';
                $html .= "<div>&nbsp;<img src='$thumb' class='img-" . $id . "-car' idc='" . $post['id_car'] . "' idsc='" . $id . "' /></div>
					<div style='margin-left:0px;'>
						<span style='font-size:8pt;display:inline;'>" . $ucar['brands'] . ' ' . $ucar['types'] . ', ' . $ucar['series'] . '<br>' . $ucar['model'] . ' ' . $ucar['transmisi'] . ' ' . $ucar['car_cc'] . ' ' . $ucar['engine'] . "<br>
						Police Number : {$ucar['police_number']} <br>
						Vin Number : {$ucar['vin_number']} <br>
						Stnk Date : {$ucar['stnk_date']} <br>
						Insurance Date : {$ucar['insurance_date']} <br>
						Last Mileage : {$ucar['last_mileage']} 
						<a class='delete_usercar' ida='" . $ucar['id'] . "' style='cursor:pointer;'>Delete Car</a>
						
						</span>
					</div><br>";
                $html .= ($mode == 'inserted') ? '</div>' : ' ';
                $html = $mode . '|' . $html;
                echo $html;

            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $post['id_user'];
            $log_desc = 'Add ' . $this->title . ' ID : ' . $post['id_user'] . '; Car Id:' . $post['id_car'] . '; Police Number:' . $post['police_number'];
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);
        }
    }

    function get_user_cars()
    {
        $post = $this->input->post();
        if ($post['id'] && $post['idc']) {
            $ucars = $this->db->get_where('view_user_cars', array('id' => $post['id']));

            if ($ucars->num_rows() > 0) {
                $carlst = get_car_array();
                $ucars = $ucars->row_array();
                $ucars['carlst'] = get_carslst($ucars['id_car']);
                // $ucars['colorlst']	= print_car_color($post['idc'],$ucars['id_car_color']);
                $ucars['colorlst'] = print_car_color($post['idc'], $ucars['id_car_color']);
                $ucars['stnk_date'] = iso_date($ucars['stnk_date']);
                $ucars['insurance_date'] = iso_date($ucars['insurance_date']);
                $ucars['path_app'] = $this->path;
                $ucars['carlst'] = json_encode($carlst);
                $ucars['car-name'] = $ucars['brands'] . ' ' . $ucars['types'] . ' ' . $ucars['series'] . ' ' . $ucars['model'] . ' ' . $ucars['transmisi'] . ' ' . $ucars['car_cc'] . 'cc ' . $ucars['engine'];

                // echo $ucars['id_car_color'] . '<br>';

                // echo $ucars['colorlst'];
                // debugvar($ucars);
                echo $this->parser->parse($this->template . '/form.html', $ucars, true), exit;

            }

        }
        echo 'false';
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
     * public function delete user car.
     *
     * delete user car page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete_user_car()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/Member_model');
        if ($this->input->post('id') != '' && ctype_digit($this->input->post('id'))) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $this->Member_model->DeleteUserCarByID($this->input->post('id'));
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
                echo '1', exit;
            } else {
                echo '0', exit;
            }
        }
    }

    public function get_color_car()
    {
        $post = $this->input->post('id');
        $type = $this->input->post('t');
        if ($post && $type) {
            switch ($type) {
                case 'search' :
                    $ctype = db_get_one('car_types', 'id_car_types', "types = '$post'");
                    echo selectlist('car_series', 'series', 'series', "id_type = '$ctype'", null,
                        '--- Choice Series ---', 'id_car_series');
                    break;
                default :
                    echo print_car_color($post), exit;
                    break;
            }
        }
        echo 0;

    }

    /**
     *
     * @param int $selected
     * @return string $return option of user type
     */
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
     * @param int $selected
     * @return string $return option of user type
     */
    private function print_usertype($selected = '')
    {
        $return = '<option value="0">--- Choice User Type ---</option>';
        foreach (array('reguler', 'vip') as $types) {
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
     *
     * @param int $selected
     * @return string $return option of religion
     */
    private function car_details($id_user)
    {
        $return = '<option value="0">--- Choice Car ---</option>';
        $cars = $this->db->where('id_user', $id_user)->order_by('create_date', 'asc')->get('view_user_cars');
        foreach ($cars->result_array() as $car) {
            $return .= "<option data-point='" . $car['point_reward'] . "' value='" . $car['id'] . "'>" . $car['brands'] . " " . $car['types'] . " " . $car['series'] . " [" . $car['point_reward'] . " Point]</option>";
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
                $cars = $this->db->where('id_user', $admin_info['id_user'])->order_by('create_date',
                    'asc')->get('view_user_cars');

                if ($cars->num_rows() > 0) {
                    $cars = $cars->result_array();
                    setlocale(LC_MONETARY, "id_ID");
                    $i = 0;
                    foreach ($cars as $k => $v) {
                        $i++;
                        $carsl[$k]['thumb'] = base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'];
                        $carsl[$k]['ids'] = $v['id'];
                        $carsl[$k]['idc'] = $v['id_car'];
                        $carsl[$k]['brands'] = $v['brands'];
                        $carsl[$k]['types'] = $v['types'];
                        $carsl[$k]['series'] = $v['series'];
                        $carsl[$k]['model'] = $v['model'];
                        $carsl[$k]['transmisi'] = $v['transmisi'];
                        $carsl[$k]['cc'] = $v['car_cc'];
                        $carsl[$k]['engine'] = $v['engine'];
                        $carsl[$k]['pn'] = $v['police_number'];
                        $carsl[$k]['vn'] = $v['vin_number'];
                        $carsl[$k]['stnk'] = $v['stnk_date'];
                        $carsl[$k]['ins_date'] = $v['insurance_date'];
                        $carsl[$k]['lmileage'] = $v['last_mileage'];
                        $carsl[$k]['points'] = $v['point_reward'];
                        $carsl[$k]['total_transacion'] = money_format('%i', $v['total_transaction']);
                        if ($v['is_from_ak']) {
                            $carsl[$k]['is_from_ak'] = 'This car was bought from Auto Kencana<br>';
                        } else {
                            $carsl[$k]['is_from_ak'] = '';
                        }
                        $carsl[$k]['delete'] = '<a class="delete_usercar" ida="' . $v['id'] . '"style="cursor:pointer;" >Delete Car</a>';
                        if ($i == 3) {
                            $i = 0;
                            $carsl[$k]['separator'] = '</div><div class="twelve columns" id="carlst">';
                        } else {
                            $carsl[$k]['separator'] = '';
                        }
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
        if (isset($this->error['city'])) {
            $error_msg['city'] = alert_box($this->error['city'], 'error');
        } else {
            $error_msg['city'] = '';
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

        $post['expire'] = (isset($admin_info['expire_date']) && $admin_info['expire_date'] != '') ? date('D,d M Y',
            $admin_info['expire_date']) : '';

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

        if ($this->input->post('city') != '') {
            $post['city'] = $this->input->post('city');
        } elseif ((int)$id > 0) {
            $post['city'] = $admin_info['city'];
        } else {
            $post['city'] = '';
        }

        if ($this->input->post('user_type') != '') {
            $post['user_type'] = $this->input->post('user_type');
        } elseif ((int)$id > 0) {
            $post['user_type'] = $admin_info['user_type'];
        } else {
            $post['user_type'] = '';
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
            $post['birthday'] = $this->reverseDate($admin_info['birthday']);
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

        if ($this->input->post('phone') != '') {
            $post['invoice_number'] = $this->input->post('invoice_number');
        } elseif ((int)$id > 0) {
            $post['invoice_number'] = $admin_info['invoice_number'];
        } else {
            $post['invoice_number'] = '';
        }

        if ($post['user_type'] == 'vip') {
            $post['disable_vip_button'] = "disabled";
        } else {
            $post['disable_vip_button'] = '';
        }

        $post['car_details'] = $this->car_details($admin_info['id_user']);

        $hide_add = ($id == 0 ? 'hide' : '');

        $carlst = get_car_array();
        $colorlst = print_car_color();

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'id' => $id,
            'carlst' => json_encode($carlst),
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
            'hide_add' => $hide_add,
        );
        $this->parser->parse($template . '/list_user_member_form.html', $data);
        $this->global_libs->print_footer();
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

        if (($post['user_type'] == '') || ($post['user_type'] == '0')) {
            $this->error['user_type'] = 'Please choice user type.<br/>';
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

    public function exportoxld()
    {
        $this->exportoxl(true);
    }

    public function exportoxl($withDetail = false)
    {
        // echo 'asd';
        // $url = $this->input->get('url');

        // auth_admin();

        // if($url) {

        $this->load->model(getAdminFolder() . '/member_model', 'Member_model');

        $s_name = $this->uri->segment(4);
        $s_email = $this->uri->segment(5);
        $s_stat = $this->uri->segment(6);
        $s_type = $this->uri->segment(7);
        $pg = $this->uri->segment(8);

        $per_page = 1000;
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

        $add_btn = site_url($path_uri . '/add');
        $is_superadmin = $this->is_superadmin;


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

        if (strlen($s_stat) > 1) {
            $s_stat = substr($s_stat, 1);
        } else {
            $s_stat = "";
        }

        if (strlen($s_type) > 1) {
            $s_type = substr($s_type, 1);
        } else {
            $s_type = "";
        }

        $stat = "<option value='-'>--- Select Status ---</option>";
        foreach (array('1', '2') as $v) {
            $label = ($v == '1') ? 'Active' : 'Non Active';
            $selected = ($s_stat == $v) ? 'selected' : '';
            $stat .= "<option $selected value='$v'>$label</option>";
        }

        $type = "<option value='-'>--- Select Type ---</option>";
        foreach (array('reguler', 'vip') as $v) {
            // $label 		= ($v == '1') ? 'Active' : 'Non Active';
            $selected = ($s_type == $v) ? 'selected' : '';
            $type .= "<option $selected value='$v'>$v</option>";
        }

        $total_records = $this->Member_model->TotalUserMember(myUrlDecode($s_name), myUrlDecode($s_email), $s_stat,
            myUrlDecode($s_type));

        if ($s_name) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_name . "/", $path);
        }

        if ($s_email) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_email . "/", $path);
        }

        if ($s_stat) {
            $dis_urut = "none";
            $path = str_replace("/c/", "/c" . $s_stat . "/", $path);
        }

        if ($s_type) {
            $dis_urut = "none";
            $path = str_replace("/d/", "/d" . $s_type . "/", $path);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Member_model->GetAllUsersMember(myUrlDecode($s_name), myUrlDecode($s_email), $s_stat,
            myUrlDecode($s_type), $is_superadmin, $lmt, $per_page);

        foreach ($query->result_array() as $row_auth_user) {
            if (!isset($i)) {
                $i = 1;
            }
            $no++;
            $id_user = $row_auth_user['id_user'];
            $username = $row_auth_user['username'];
            $email = $row_auth_user['email'];
            $edit_href = site_url($path_uri . '/edit/' . $id_user);
            $vip = ucfirst($row_auth_user['user_type']);
            // $dvip		= $row_auth_user['vip_date'] != '' ? date('D, d M Y',$row_auth_user['vip_date']) : ''; // vip joined
            $dvip = $row_auth_user['vip_date'] != '' ? '="' . date('d-m-Y',
                    $row_auth_user['vip_date']) . '"' : ''; // vip joined
            $status = $row_auth_user['status'] != '0' ? 'Active' : 'Not Active';
            // $expire		= ($row_auth_user['expire_date']) ? date('D,d M Y',$row_auth_user['expire_date']) : '';
            $expire = ($row_auth_user['expire_date']) ? '="' . date('d-m-Y', $row_auth_user['expire_date']) . '"' : '';
            // $birthday	= '="'.date('d-m-Y',strtotime($row_auth_user['birthday'])).'"';
            $religion = $row_auth_user['religion'];
            $gender = $row_auth_user['sex'];
            $phone = '="' . $row_auth_user['phone_number'] . '"';
            $hp = '="' . $row_auth_user['phone'] . '"';
            $addr = $row_auth_user['address'];
            $cardid = '="' . $row_auth_user['card_id'] . '"';
            $workshop = $row_auth_user['location'];

            list($y, $m, $d) = explode('-', $row_auth_user['birthday']);
            $birthday = '="' . "$d-$m-$y" . '"';

            $carsl = array();

            if ($withDetail == true) {
                $cars = $this->db->where('id_user', $id_user)->order_by('create_date', 'asc')->get('view_user_cars');

                if ($cars->num_rows() > 0) {
                    $cars = $cars->result_array();
                    setlocale(LC_MONETARY, "id_ID");
                    foreach ($cars as $k => $v) {
                        $carsl[$k]['num'] = $i++;
                        $carsl[$k]['card_id'] = $cardid;
                        $carsl[$k]['username'] = $username;
                        $carsl[$k]['email'] = $email;
                        $carsl[$k]['vip'] = $vip;
                        $carsl[$k]['workshop'] = $workshop;
                        $carsl[$k]['brands'] = $v['brands'];
                        $carsl[$k]['types'] = $v['types'];
                        $carsl[$k]['series'] = $v['series'];
                        $carsl[$k]['model'] = $v['model'];
                        $carsl[$k]['transmisi'] = $v['transmisi'];
                        $carsl[$k]['cc'] = $v['car_cc'];
                        $carsl[$k]['engine'] = $v['engine'];
                        $carsl[$k]['pn'] = $v['police_number'];
                        $carsl[$k]['vn'] = $v['vin_number'];
                        $carsl[$k]['points'] = $v['point_reward'];
                        $carsl[$k]['last_entry'] = $v['create_date'];
                        $carsl[$k]['total_transacion'] = money_format('%i', $v['total_transaction']);
                    }
                }
            }

            $list_admin_arr[] = array(
                'no' => $no,
                'id_user' => $id_user,
                'email' => $email,
                'vip' => $vip,
                'status' => $status,
                'username' => $username,
                'edit_href' => $edit_href,
                'dvip' => $dvip,
                'id' => $id_user,
                'expire' => $expire,
                'card_id' => $cardid,
                'birthday' => $birthday,
                'religion' => $religion,
                'sex' => $gender,
                'phone' => $phone,
                'hp' => $hp,
                'addr' => $addr,
                'carsl' => $carsl
            );
        }

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            's_stat' => $stat,
            's_type' => $type,
            'list_user_member' => $list_admin_arr,
            's_name' => myUrlDecode($s_name),
            's_email' => myUrlDecode($s_email),
            'file_app' => $file_app,
            'path_app' => $path_app,

        );

        if ($withDetail == true) {
            export_to('User_Member_Date_' . date('Ymd') . '_details.xls');
            $data = $this->parser->parse($template . '/expd.html', $data, true);
        } else {
            export_to('User_Member_Date_' . date('Ymd') . '.xls');
            $data = $this->parser->parse($template . '/exp.html', $data, true);
        }

        echo $data;


        // }
    }

    private function reverseDate($date)
    {

        $date = explode("-", $date);
        $date = array($date[2], $date[1], $date[0]);

        return $n_date = implode("-", $date);
    }

    public function upgrade_vip()
    {
        $user_id = $this->input->post('user_id');
        $car_id = $this->input->post('car_id');

        if ($user_id != '' && $car_id != '') {
            $current_point = db_get_one('user_cars', 'point_reward', "id_user_cars = '$car_id'");
            $new_point = $current_point - 2;

            $vip_expired = db_get_one('user', 'expire_date', "id_user = '$user_id'");
            if ($vip_expired >= strtotime(date('Y-m-d H:i:s'))) {
                $expired_date = strtotime('+1 year', $vip_expired);
            } else {
                $expired_date = strtotime(date('Y-m-d') . ' + 365 day');
            }

            $update = array(
                'user_type' => 'vip',
                'vip_date' => strtotime(date('Y-m-d H:i:s')),
                'expire_date' => $expired_date
            );
            $this->db->where('id_user', $user_id)->update('user', $update);

            $update = array('point_reward' => $new_point);
            $this->db->where('id_user_cars', $car_id)->update('user_cars', $update);

            echo 'OK';
        } else {
            return false;
        }

    }

    public function upgrade_vip_first_time()
    {
        $user_id = $this->input->post('user_id');
        $invoice_number = $this->input->post('invoice_number');

        if ($user_id != '') {

            $update = array(
                'user_type' => 'vip',
                'vip_date' => strtotime(date('Y-m-d H:i:s')),
                'expire_date' => strtotime(date('Y-m-d') . ' + 365 day'),
                'invoice_number' => $invoice_number
            );
            $this->db->where('id_user', $user_id)->update('user', $update);

            echo 'OK';
        } else {
            return false;
        }

    }

}

/* End of file list_user_admin.php */
/* Location: ./application/controllers/admpage/list_user_member.php */
<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
error_reporting(E_ALL);

/********************************************
 * News Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : News Management
 *********************************************/
class News extends CI_Controller
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
        $this->ctrl = 'news';
        $this->template = getAdminFolder() . '/modules/news';
        $this->path_uri = getAdminFolder() . '/news';
        $this->path = site_url(getAdminFolder() . '/news');
        $this->title = get_admin_menu_title('news');
        $this->id_menu_admin = get_admin_menu_id('news');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_thumb_width = 175;
        $this->max_thumb_height = 125;
        $this->max_detail_height_hxx = 350;
        $this->max_detail_height_hx = 233;
        $this->max_detail_height_h = 155;
        $this->max_detail_width_hxx = 926;
        $this->max_detail_width_hx = 617;
        $this->max_detail_width_h = 411;
        $this->path_thumb = './uploads/news/';
        $this->load->model(getAdminFolder() . '/News_model', 'News_model');
        $this->load->helper('text');
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


        $s_news = $this->uri->segment(4);
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
        $search_query = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_news) > 1) {
            $s_news = substr($s_news, 1);

        } else {
            $s_news = "";

        }
        $total_records = $this->News_model->TotalNews(myUrlDecode($s_news));


        if ($s_news) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_news . "/", $path);
            ($search_query == '' ? $search_query .= $s_news : $search_query .= ' + ' . $s_news);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->News_model->GetAllNews(myUrlDecode($s_news), $lmt, $per_page);

        foreach ($query->result_array() as $buff) {
            $no++;
            $id = $buff['id_news'];
            $ref_publish = ($buff['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $title = $buff['title'];
            $news = word_limiter(strip_tags($buff['content']), 5);
            $type = $buff['type'];
            $new = ($buff['is_new'] == 1) ? 'Yes' : 'No';
            $create_date = $buff['create_date'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'type' => $type,
                'title' => $title,
                'news' => $news,
                'edit_href' => $edit_href,
                'ref_stat' => $new,
                'create_date' => $create_date,
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
            's_news' => $s_news,
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
    public function change_publish($id = 0)
    {
        if ($id) {
            if (ctype_digit($id)) {
                $return = $this->News_model->ChangePublishNews($id);
                echo $return;
            }
        }
    }

    /**
     * public function change_stat.
     *
     * change publish page by request get.
     */
    public function change_stat($id = 0)
    {
        if ($id) {
            if (ctype_digit($id)) {
                $return = $this->News_model->ChangeStatNews($id);
                echo $return;
            }
        }
    }

    /**
     * search post s_name and s_email
     */
    public function search()
    {
        auth_admin();
        $s_news = myUrlEncode(trim($this->input->post('s_news')));
        redirect($this->path . '/main/a' . $s_news);
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
                    $this->News_model->DeleteNews($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete News ID : ' . $id;
                    $log_desc = 'Delete News ID : ' . $id . ';';
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
        if ($this->input->post('id') != '' && ctype_digit($this->input->post('id'))) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $this->News_model->DeleteNewsThumbByID($this->input->post('id'));
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
                $this->session->set_flashdata('success_msg', $this->title . ' Thumb has been deleted.');
            } else {
                $this->session->set_flashdata('error_msg', 'You can\'t manage this content.');
            }
        }
    }

    /**
     * add page
     */
    public function add()
    {
        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $post = $this->input->post();
            $ico = $_FILES['thumb'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'title' => $post['title'],
                'content' => $post['content'],
                'type' => $post['type'],
            );

            // insert data
            $id = $this->News_model->InsertNews($data_post);


            if ($ico['tmp_name'] != "") {


                $filename = 'news_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename,
                    $this->max_detail_width_hxx, $this->max_detail_height_hxx);
                trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename . '_XHDPI',
                    $this->max_detail_width_hx, $this->max_detail_height_hx);
                trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename . '_HDPI',
                    $this->max_detail_width_h, $this->max_detail_height_h);

                // $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename, $this->max_thumb_width, $this->max_thumb_height);

                $data_pic_content = array('thumb' => $content_to_db);

                // update to database
                $this->News_model->UpdateNews($data_pic_content, $id);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . "; $this->ctrl Title :" . $post['title'] . "; $this->ctrl";
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

    public function move()
    {
        auth_admin();
        $ids = $this->input->post('id');
        if ($ids) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $ids = array_filter(explode('-', $ids));
                if (is_array($ids) && count($ids) > 0) {
                    $this->News_model->MoveIntoNews($ids);
                    $id = implode(', ', $ids);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Move News Temp ID : ' . $id;
                    $log_desc = 'Move News Temp ID : ' . $id . ';';
                    $data = array(
                        'id_user' => $log_id_user,
                        'id_group' => $log_id_group,
                        'action' => $log_action,
                        'desc' => $log_desc,
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);
                    $this->session->set_flashdata('success_msg', $this->title . ' (s) has been moved.');
                } else {
                    $this->session->set_flashdata('error_msg', 'Moving failed. Please try again.');
                }
            } else {
                $this->session->set_flashdata('error_msg', 'You can\'t manage this content.<br/>');
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Moving failed. Please try again.');
        }
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
            $ico = $_FILES['thumb'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'title' => $post['title'],
                'content' => $post['content'],
                'type' => $post['type'],

            );

            if ($id == '18') {
                $data_post['type'] = 'bvm';
            }

            // insert data
            $this->News_model->UpdateNews($data_post, $id);


            if ($ico['tmp_name'] != "") {
                $filename = 'news_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->max_thumb_width, $this->max_thumb_height);

                $data_pic_content = array('thumb' => $content_to_db);

                // update to database
                $this->News_model->UpdateNews($data_pic_content, $id);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . "; $this->ctrl Title :" . $post['title'] . "; $this->ctrl";
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', $this->title . ' has been updated');

            if ($id == '18') {
                redirect(current_url());
            }

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
        $iface = 'multiple';
        $list_data = array();
        $pic_thumbnail = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->News_model->GetNewsById($id);
            if ($query->num_rows() > 0) {
                $info = $query->row_array();
                $iface = 'single';
                if ($info['thumb'] != '' && file_exists($this->path_thumb . $info['thumb'])) {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/news/' . $info['thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $info['id_news'] . '"style="cursor:pointer;" >Delete Picture</a>
                    </div>';

                } elseif ($info['thumb_url'] != '') {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . $info['thumb_url'] . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $info['id_news'] . '"style="cursor:pointer;" >Delete Picture</a>
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
            $err_c = 'error-info ';
            $required = '';
            $pass_msg = '<small class="error-info">Please ignore this field if you don\'t want to change Password.</small>';
            $action = site_url($path_uri . '/edit/' . $id);
            $label = 'Edit';
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
            $label = 'Add';
            $no = 0;

            $query = $this->News_model->GetAllNewsTemp();

            foreach ($query->result_array() as $buff) {
                $no++;
                $idt = $buff['id'];
                $published_date = $buff['published_date'];
                $title = $buff['title'];
                $news = word_limiter(strip_tags($buff['content']), 5);
                $link = $buff['link'];

                $list_data[] = array(
                    'no' => $no,
                    'id' => $idt,
                    'published_date' => $published_date,
                    'title' => $title,
                    'news' => $news,
                    'link' => $link
                );
            }
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['title'])) {
            $error_msg['title'] = alert_box($this->error['title'], 'error');
        } else {
            $error_msg['title'] = '';
        }
        if (isset($this->error['content'])) {
            $error_msg['content'] = alert_box($this->error['content'], 'error');
        } else {
            $error_msg['content'] = '';
        }
        if (isset($this->error['img'])) {
            $error_msg['img'] = alert_box($this->error['img'], 'error');
        } else {
            $error_msg['img'] = '';
        }
        if (isset($this->error['type'])) {
            $error_msg['type'] = alert_box($this->error['type'], 'error');
        } else {
            $error_msg['type'] = '';
        }


        // set value
        if ($this->input->post('title') != '') {
            $post['title'] = $this->input->post('title');
        } elseif ((int)$id > 0) {
            $post['title'] = $info['title'];
        } else {
            $post['title'] = '';
        }
        if ($this->input->post('content') != '') {
            $post['content'] = $this->input->post('content');
        } elseif ((int)$id > 0) {
            $post['content'] = $info['content'];
        } else {
            $post['content'] = '';
        }
        if ($this->input->post('type') != '') {
            $post['type'] = print_car_trans($this->input->post('type'), 'Choice Type', 'jdi_news', 'type');
        } elseif ((int)$id > 0) {
            $post['type'] = print_car_trans($info['type'], 'Choice Type', 'jdi_news', 'type');
        } else {
            $post['type'] = print_car_trans('', 'Choice Type', 'jdi_news', 'type');
        }

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'iface' => $iface,
            'label' => $label,
            'list_data' => $list_data,
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
            'current_url' => $path_app,
            'news_id' => $id,
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
        $ico = $_FILES['thumb'];


        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }


        if ($post['title'] == '') {
            $this->error['title'] = 'Please insert News Title.<br/>';
        } else {
            if (!$this->News_model->CheckExistsNews($post['title'], $id)) {
                $this->error['title'] = 'News Title already exists, please input different Title.<br/>';
            } else {
                if (utf8_strlen($post['title']) < 5) {
                    $this->error['title'] = 'News Title length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['content'] == '') {
            $this->error['content'] = 'Please insert News Content.<br/>';
        }

        if ($post['type'] == '' || $post['type'] == '0') {
            $this->error['type'] = 'Please select type of news.<br/>';
        }

        if ($ico['tmp_name'] != "") {

            $info = getimagesize($ico['tmp_name']);
            if ($info[0] < 100 || $info[1] < 100 || !in_array($info['mime'],
                    array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))
            ) {
                $this->error['img'] = 'Minimum Image width 100px and Height 100px <br> The Image type is PNG, JPG, JPEG, GIF only. Your image is ' . $info['mime'];
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function broadcast($id)
    {

        if ($id != '') {
            $this->db->where('id_news', $id);
            $news = $this->db->get('news')->row_array();
            $message = 'AK News: ' . $news['title'];

            // Select GCM Users from database
            $this->db->select('gcm_id');
            $this->db->where('gcm_id !=', '');
            $gcm_user = $this->db->get('user');

            //Send Push notification for GCM Users
            $i = 1;
            $gcm_ids = '';
            $count = count($gcm_user->result_array());
            foreach ($gcm_user->result_array() as $user) {
                if ($i % 1000 != 0) {
                    $gcm_ids[] = $user['gcm_id'];
                } else {
                    $fields = array(
                        'data' => array('message' => $message),
                        'registration_ids' => $gcm_ids,

                    );
                    $headers = array(
                        'Authorization: key=' . GAPI_KEY,
                        'Content-Type: application/json'
                    );
                    $options = array(
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_POSTFIELDS => json_encode($fields)
                    );
                    $resp = cURL(GCM_ACT, $options);

                    $i = 0;
                    $gcm_ids = '';
                }
                $i++;
            }

            $fields = array(
                'data' => array('message' => $message),
                'registration_ids' => $gcm_ids,

            );
            $headers = array(
                'Authorization: key=' . GAPI_KEY,
                'Content-Type: application/json'
            );
            $options = array(
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => json_encode($fields)
            );
            $resp = cURL(GCM_ACT, $options);
            // END GCM Broadcast

            // START APNS Broadcast
            $this->load->model('apns_model');
            $this->apns_model->broadcats_apns($message, array('page' => 'news'));
            // END APNS Broadcast
        }
    }

    public function broadcast_async($id)
    {
        $url = "http://akxperience.com/admpage/news/broadcast/$id";
        exec("curl '$url' > /dev/null &");
    }

}


/* End of file news.php */
/* Location: ./application/controllers/admpage/news.php */
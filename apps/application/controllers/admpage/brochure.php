<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Brochure Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Brochure Management
 *********************************************/
class Brochure extends CI_Controller
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
        $this->ctrl = 'brochure';
        $this->template = getAdminFolder() . '/modules/brochure';
        $this->path_uri = getAdminFolder() . '/brochure';
        $this->path = site_url(getAdminFolder() . '/brochure');
        $this->title = get_admin_menu_title('brochure');
        $this->id_menu_admin = get_admin_menu_id('brochure');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        // $this->max_thumb_width			= 175;
        // $this->max_thumb_height			= 125;
        // $this->max_detail_height_hxx	= 350;
        // $this->max_detail_height_hx		= 233;
        // $this->max_detail_height_h		= 155;
        // $this->max_detail_width_hxx		= 926;
        // $this->max_detail_width_hx		= 617;
        // $this->max_detail_width_h		= 411;
        $this->width_thumb = '175';
        $this->height_thumb = '125';

        $this->width_thumb_detail = '926';
        $this->height_thumb_detial = '350';

        $this->path_thumb = './uploads/brochure/';
        $this->load->model(getAdminFolder() . '/News_model', 'News_model');
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

        $s_bro = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
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
        $list_data = array();
        $wtype = $wseries = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_bro) > 1) {
            $s_bro = substr($s_bro, 1);

        } else {
            $s_bro = "";

        }
        $total_records = $this->News_model->TotalBrochure(myUrlDecode($s_bro));


        if ($s_bro) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_bro . "/", $path);
            ($search_query == '' ? $search_query .= $s_bro : $search_query .= ' + ' . $s_bro);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->News_model->GetAllBrochure(myUrlDecode($s_bro), $lmt, $per_page);

        foreach ($query->result_array() as $buff) {
            $no++;
            $id = $buff['id_brochure'];
            $ref_publish = ($buff['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $title = $buff['title'];
            $broc = word_limiter(strip_tags($buff['content']), 5);
            // $type			= $buff['type'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'title' => $title,
                'broc' => $broc,
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
            's_bro' => $s_bro,
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
                $return = $this->News_model->ChangePublishBrochure($id);
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
        $s_bro = myUrlEncode(trim($this->input->post('s_bro')));
        redirect($this->path . '/main/a' . $s_bro);
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
                    $this->News_model->DeleteBrochure($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Brochure ID : ' . $id;
                    $log_desc = 'Delete Brochure ID : ' . $id . ';';
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
                $this->News_model->DeleteBrochureThumbByID($this->input->post('id'));
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
     * public function delete file.
     *
     * delete gallery page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete_file()
    {
        auth_admin();
        if ($this->input->post('id') != '' && ctype_digit($this->input->post('id'))) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $this->News_model->DeleteBrochureFileByID($this->input->post('id'));
                #insert to log
                $log_last_user_id = $this->input->post('id');
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete File ' . $this->title . ' ID : ' . $log_last_user_id;
                $log_desc = 'Delete File ' . $this->title . ' ID : ' . $log_last_user_id . ';';
                $data_log = array(
                    'id_user' => $log_id_user,
                    'id_group' => $log_id_group,
                    'action' => $log_action,
                    'desc' => $log_desc,
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data_log);
                $this->session->set_flashdata('success_msg', $this->title . ' File has been deleted.');
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
            $file = $_FILES['pdf'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'title' => $post['title'],
                'content' => $post['content'],
                // 'type'		=> $post['type'],
            );

            // insert data
            $id = $this->News_model->InsertBrochure($data_post);


            if ($ico['tmp_name'] != "") {
                $filename = 'brochure_' . $id . date('dmYHis');

                trans_image_resize_to_folder($ico, $this->path_thumb, $filename . '_thumb', $this->width_thumb,
                    $this->height_thumb);
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->width_thumb_detail, $this->height_thumb_detial);

                $data_pic_content = array('thumb' => $content_to_db);

                $this->News_model->UpdateBrochure($data_pic_content, $id);
            }

            if ($file['tmp_name'] != "") {

                $filename = 'brochure_file_' . $id . date('dmYHis');

                $content_to_db = file_copy_to_folder($file, $this->path_thumb . 'file/', $filename);

                $data_file = array('file' => $content_to_db);

                $this->News_model->UpdateBrochure($data_file, $id);
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
            $file = $_FILES['pdf'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'title' => $post['title'],
                'content' => $post['content'],
                // 'type'	=> $post['type'],

            );

            // insert data
            $this->News_model->UpdateBrochure($data_post, $id);


            if ($ico['tmp_name'] != "") {
                $filename = 'brochure_' . $id . date('dmYHis');

                trans_image_resize_to_folder($ico, $this->path_thumb . $ext, $filename . '_thumb', $this->width_thumb,
                    $this->height_thumb);
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->width_thumb_detail, $this->height_thumb_detial);

                $data_pic_content = array('thumb' => $content_to_db);

                // update to database
                $this->News_model->UpdateBrochure($data_pic_content, $id);
            }

            if ($file['tmp_name'] != "") {
                $config['upload_path'] = $this->path_thumb . 'file/';
                $config['allowed_types'] = 'pdf';
                $config['file_name'] = $post['title'] . '_' . date('YmdHi');
                $config['max_size'] = 0;
                $config['overwrite'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('pdf')) {
                    $error = $this->upload->display_errors();
                    die(var_dump($error));
                } else {
                    $data = $this->upload->data();
                    $data_file = array('file' => $data['file_name']);
                    $this->News_model->UpdateBrochure($data_file, $id);

                }
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
        $pic_thumbnail = $files = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->News_model->GetBrochureById($id);
            if ($query->num_rows() > 0) {
                $info = $query->row_array();
                $iface = 'single';
                if ($info['thumb'] != '' && file_exists($this->path_thumb . $info['thumb'])) {
                    $pic_thumbnail = '<div id="print-img-thumbnail">
                            <img src="' . base_url('uploads/brochure/' . $info['thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" types="img" ida="' . $info['id_brochure'] . '"style="cursor:pointer;" >Delete Picture</a>
                    </div>';
                }
                if ($info['file'] != '' && file_exists($this->path_thumb . 'file/' . $info['file'])) {
                    $files = '<div id="print-file-thumbnail">
                            <img src="' . base_url('uploads/brochure/file/' . $info['file'] . '') . '" width="150" alt="' . $info['file'] . '" ><br/>
                            <a id="delete-file-thumbnail" types="file" ida="' . $info['id_brochure'] . '"style="cursor:pointer;" >Delete File</a>
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
            $no = 1;

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
        if (isset($this->error['file'])) {
            $error_msg['file'] = alert_box($this->error['file'], 'error');
        } else {
            $error_msg['file'] = '';
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
            'files' => $files,
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
        $file = $_FILES['pdf'];


        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }


        if ($post['title'] == '') {
            $this->error['title'] = 'Please insert Brochure Title.<br/>';
        } else {
            if (!$this->News_model->CheckExistsNews($post['title'], $id)) {
                $this->error['title'] = 'Brochure Title already exists, please input different Title.<br/>';
            } else {
                if (utf8_strlen($post['title']) < 5) {
                    $this->error['title'] = 'Brochure Title length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['content'] == '') {
            $this->error['content'] = 'Please insert Brochure Content.<br/>';
        }

        // if($post['type'] == '' || $post['type'] == '0') {
        // $this->error['type'] = 'Please select type of news.<br/>';
        // }

        if ($ico['tmp_name'] != "") {


            $info = getimagesize($ico['tmp_name']);
            if ($info[0] < $this->width_thumb_detail || $info[1] < $this->height_thumb_detial) {
                $this->error['img'] = 'Minimum Image width 926px and Height 350px';
            }
        }

        if ($file['tmp_name'] != '' && $file['type'] != 'application/pdf') {
            $this->error['file'] = 'only pdf file will allowed';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}


/* End of file news.php */
/* Location: ./application/controllers/admpage/news.php */
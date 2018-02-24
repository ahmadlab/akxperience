<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Template Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Template
 *********************************************/
class Template extends CI_Controller
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
        $this->ctrl = 'template';
        $this->template = getAdminFolder() . '/modules/template';
        $this->path_uri = getAdminFolder() . '/template';
        $this->path = site_url(getAdminFolder() . '/template');
        $this->title = get_admin_menu_title('template');
        $this->id_menu_admin = get_admin_menu_id('template');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->width_thumb = '175';
        $this->height_thumb = '125';

        $this->width_thumb_detail = '926';
        $this->height_thumb_detial = '350';

        $this->path_thumb = './uploads/template/';
        $this->load->model(getAdminFolder() . '/Template_model', 'Template_model');
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

        $s_temp = $this->uri->segment(4);
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
        $list_data = array();
        $wtype = $wseries = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_temp) > 1) {
            $s_temp = substr($s_temp, 1);

        } else {
            $s_temp = "";

        }
        $total_records = $this->Template_model->TotalTemp(myUrlDecode($s_temp));


        if ($s_temp) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_temp . "/", $path);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Template_model->GetAllTemp(myUrlDecode($s_temp), $lmt, $per_page);

        foreach ($query->result_array() as $buff) {
            $no++;
            $id = $buff['id'];
            // $stamp		 	= $buff['create_date'];
            $type = $buff['type'];
            $payload = word_limiter(strip_tags($buff['message']), 10);

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'type' => $type,
                'payload' => $payload,
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

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            's_temp' => $s_temp,
            'list_data' => $list_data,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
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
        $s_temp = myUrlEncode(trim($this->input->post('s_temp')));
        redirect($this->path . '/main/a' . $s_temp);
    }

    /**
     * add page
     */
    public function add()
    {
        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $post = $this->input->post();

            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'type' => $post['type'],
                'message' => $post['payload'],

            );

            // insert data
            $id = $this->Template_model->InsertTemp($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . "; $this->ctrl Type :" . $post['type'];
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

            $data_post = array(
                'message' => strip_tags($post['payload']),

            );

            // insert data
            $this->Template_model->UpdateTemp($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . "; $this->ctrl Type :" . $post['type'];
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
            $query = $this->Template_model->GetTempById($id);
            if ($query->num_rows() > 0) {
                $post['key'] = '';
                $info = $query->row_array();
                if ($info['key'] != '') {
                    $post['key'] .= '<code><i>Heres the key reference that can be used for this template : </i></code> <br><br>';
                    // $keys = explode(','$info['key']);
                    foreach (explode(',', $info['key']) as $key) {
                        $post['key'] .= $key . '<br>';
                    }
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
        if (isset($this->error['payload'])) {
            $error_msg['payload'] = alert_box($this->error['payload'], 'error');
        } else {
            $error_msg['payload'] = '';
        }
        if (isset($this->error['type'])) {
            $error_msg['type'] = alert_box($this->error['type'], 'error');
        } else {
            $error_msg['type'] = '';
        }


        // set value
        if ($this->input->post('payload') != '') {
            $post['payload'] = $this->input->post('payload');
        } elseif ((int)$id > 0) {
            $post['payload'] = $info['message'];
        } else {
            $post['payload'] = '';
        }
        if ($this->input->post('type') != '') {
            $post['type'] = $this->input->post('type');
        } elseif ((int)$id > 0) {
            $post['type'] = $info['type'];
        } else {
            $post['type'] = '';
        }


        $post = array($post);

        // debugvar($post);

        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'list_data' => $list_data,
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


        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }


        if ($post['payload'] == '') {
            $this->error['payload'] = 'Please insert the message <br/>';
        }

        if ($post['type'] == '') {
            $this->error['type'] = 'Please insert the type <br/>';
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
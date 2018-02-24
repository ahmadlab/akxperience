<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Transaction Spare Part Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Transaction Spare Part Management
 *********************************************/
class Trans_spare_part extends CI_Controller
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
    private $max_thumb_width;
    private $max_thumb_height;
    private $path_thumb;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
        $this->ctrl = 'trans_spare_part';
        $this->template = getAdminFolder() . '/modules/trans_spare_part';
        $this->path_uri = getAdminFolder() . '/trans_spare_part';
        $this->path = site_url(getAdminFolder() . '/trans_spare_part');
        $this->title = get_admin_menu_title('trans_spare_part');
        $this->id_menu_admin = get_admin_menu_id('trans_spare_part');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_thumb_height = 105;
        $this->max_thumb_width = 148;
        $this->path_thumb = './uploads/trans_spare_part/';
        $this->load->model(getAdminFolder() . '/Transaction_model', 'Trans_model');
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
        $s_usr = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/u/';
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

        if (strlen($s_usr) > 1) {
            $s_usr = substr($s_usr, 1);

        } else {
            $s_usr = "";

        }

        $l_usr = selectlist('user', 'id_user', 'username', null, $s_usr, '--- Chooice User ---');
        $total_records = $this->Trans_model->TotalTransSparePart(myUrlDecode($s_usr));

        if ($s_usr) {
            $dis_urut = "none";
            $path = str_replace("/u/", "/u" . $s_usr . "/", $path);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Trans_model->GetAllTransSparePart(myUrlDecode($s_usr), $lmt, $per_page);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id = $buffs['id_car_spare_part_transaction'];
            $usr = db_get_one('user', 'username', "id_user = '" . $buffs['id_user'] . "'");
            $car_spare_part = db_get_one('car_spare_parts', 'spare_part',
                "id_car_spare_part = '" . $buffs['id_car_spare_part'] . "'");
            $create_date = $buffs['create_date'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'car_spare_part' => $car_spare_part,
                'create_date' => $create_date,
                'usr' => $usr,
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
            'l_usr' => $l_usr,
            's_usr' => $s_usr,
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
        $s_usr = myUrlEncode(trim($this->input->post('s_usr')));
        redirect($this->path . '/main/u' . $s_usr);
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
                'id_user' => $post['ref_user'],
                'id_car_spare_part' => $post['ref_spare_part'],
            );

            $id = $this->Trans_model->InsertTransSparePart($data_post);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Ref User:' . $post['ref_user'] . '; Ref Spare Part:' . $post['ref_spare_part'];
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
                'id_user' => $post['ref_user'],
                'id_car_spare_part' => $post['ref_accessories'],
            );

            $id = $this->Trans_model->UpdateTransSparePart($data_post, $id);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Ref User:' . $post['ref_user'] . '; Ref Spare Part:' . $post['ref_spare_part'];
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
            $query = $this->Trans_model->GetTransSparePartById($id);
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
        if (isset($this->error['ref_user'])) {
            $error_msg['ref_user'] = alert_box($this->error['ref_user'], 'error');
        } else {
            $error_msg['ref_user'] = '';
        }
        if (isset($this->error['ref_spare_part'])) {
            $error_msg['ref_spare_part'] = alert_box($this->error['ref_spare_part'], 'error');
        } else {
            $error_msg['ref_spare_part'] = '';
        }


        if ((int)$id > 0) {
            $usrlist = selectlist('user', 'id_user', 'username', null, $info['id_user'], '--- Chooice User ---');
            $sparepartlist = selectlist('car_spare_parts', 'id_car_spare_part', 'spare_part', null,
                $info['id_car_spare_part'], '--- Chooice Spare Part ---');
        } else {
            $usrlist = selectlist('user', 'id_user', 'username', null, null, '--- Chooice User ---');
            $sparepartlist = selectlist('car_spare_parts', 'id_car_spare_part', 'spare_part', "ref_publish = '1'", null,
                '--- Chooice Spare Part ---');
        }

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'usrlist' => $usrlist,
            'sparepartlist' => $sparepartlist,
            'pass_msg' => $pass_msg,
            'err_c' => $err_c,
            'required' => $required,
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

        if ($post['ref_user'] == '' || $post['ref_user'] == 0) {
            $this->error['ref_user'] = 'Please select User.<br/>';
        }
        if ($post['ref_spare_part'] == '' || $post['ref_spare_part'] == 0) {
            $this->error['ref_spare_part'] = 'Please select Spare Part.<br/>';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }


}

/* End of file trans_spare_part.php */
/* Location: ./application/controllers/admpage/trans_spare_part.php */
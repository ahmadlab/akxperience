<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Ref Service Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Ref Service Management
 *********************************************/
class Ref_service extends CI_Controller
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
        $this->ctrl = 'ref_service';
        $this->template = getAdminFolder() . '/modules/ref_service';
        $this->path_uri = getAdminFolder() . '/ref_service';
        $this->path = site_url(getAdminFolder() . '/ref_service');
        $this->title = get_admin_menu_title('ref_service');
        $this->id_menu_admin = get_admin_menu_id('ref_service');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_sp_height = 105;
        $this->max_sp_width = 148;
        $this->path_sparepart = './uploads/ref_service/';
        $this->load->model(getAdminFolder() . '/Service_model', 'Service_model');
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
        $s_type = $this->uri->segment(4);
        $s_service = $this->uri->segment(5);
        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/a/b/';
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

        if (strlen($s_type) > 1) {
            $s_type = substr($s_type, 1);

        } else {
            $s_type = "";

        }
        if (strlen($s_service) > 1) {
            $s_service = substr($s_service, 1);

        } else {
            $s_service = "";

        }

        $l_type = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null, $s_type,
            '--- Choice Type of Service ---');
        $total_records = $this->Service_model->TotalService(myUrlDecode($s_type), myUrlDecode($s_service));

        if ($s_type) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_type . "/", $path);
            ($search_query == '' ? $search_query .= $s_type : $search_query .= ' + ' . $s_type);
        }

        if ($s_service) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_service . "/", $path);
            ($search_query == '' ? $search_query .= $s_service : $search_query .= ' + ' . $s_service);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Service_model->GetAllService(myUrlDecode($s_type), myUrlDecode($s_service), $lmt, $per_page);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id = $buffs['id_service'];
            $ref_publish = ($buffs['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $type = db_get_one('ref_service_type', 'service_type',
                "id_ref_service_type = '" . $buffs['id_service_type'] . "'");
            $service = $buffs['service'];
            $price = $buffs['service_price'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'service' => $service,
                'type' => $type,
                'price' => $price,
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
            'l_type' => $l_type,
            's_service' => $s_service,
            's_type' => $s_type,
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
                $return = $this->Service_model->ChangePublishService($id);
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
        $s_type = myUrlEncode(trim($this->input->post('s_type')));
        $s_service = myUrlEncode(trim($this->input->post('s_service')));
        redirect($this->path . '/main/a' . $s_type . '/b' . $s_service);
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
                    $this->Service_model->DeleteService($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Car Spare Part ID : ' . $id;
                    $log_desc = 'Delete Car Spare Part ID : ' . $id . ';';
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
     * add page
     */
    public function add()
    {
        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'service' => $post['service'],
                'id_service_type' => $post['ref_service_type'],
                'oil_refill' => (isset($post['oil_refill'])) ? $post['oil_refill'] : 0,
                'service_price' => $post['service_price'],
                'detail' => $post['detail'],

            );
            // insert data
            $id = $this->Service_model->InsertService($data_post);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Service:' . $post['service'] . '; Price :' . $post['service_price'];
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
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'service' => $post['service'],
                'id_service_type' => $post['ref_service_type'],
                'oil_refill' => (isset($post['oil_refill'])) ? $post['oil_refill'] : 0,
                'service_price' => $post['service_price'],
                'detail' => $post['detail'],

            );

            $this->Service_model->UpdateService($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Service:' . $post['service'] . '; Price :' . $post['service_price'];
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
        $pic_thumbnail = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Service_model->GetServiceById($id);
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
        if (isset($this->error['service'])) {
            $error_msg['service'] = alert_box($this->error['service'], 'error');
        } else {
            $error_msg['service'] = '';
        }
        if (isset($this->error['service_price'])) {
            $error_msg['service_price'] = alert_box($this->error['service_price'], 'error');
        } else {
            $error_msg['service_price'] = '';
        }
        if (isset($this->error['ref_service_type'])) {
            $error_msg['ref_service_type'] = alert_box($this->error['ref_service_type'], 'error');
        } else {
            $error_msg['ref_service_type'] = '';
        }
        if (isset($this->error['detail'])) {
            $error_msg['detail'] = alert_box($this->error['detail'], 'error');
        } else {
            $error_msg['detail'] = '';
        }


        // set value
        if ($this->input->post('service') != '') {
            $post['service'] = $this->input->post('service');
        } elseif ((int)$id > 0) {
            $post['service'] = $info['service'];
        } else {
            $post['service'] = '';
        }
        if ($this->input->post('service_price') != '') {
            $post['service_price'] = $this->input->post('service_price');
        } elseif ((int)$id > 0) {
            $post['service_price'] = $info['service_price'];
        } else {
            $post['service_price'] = '';
        }
        if ($this->input->post('detail') != '') {
            $post['detail'] = $this->input->post('detail');
        } elseif ((int)$id > 0) {
            $post['detail'] = $info['detail'];
        } else {
            $post['detail'] = '';
        }

        if ($this->input->post('ref_service_type') != '') {
            $post['ref_service_type'] = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null,
                $this->input->post('ref_service_type'), '--- Choice Type of Service ---', 'id_ref_service_type');
            $post['ocheck'] = ($this->input->post('oil_refill')) ? 'checked' : '';
        } elseif ((int)$id > 0) {
            $post['ref_service_type'] = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null,
                $info['id_service_type'], '--- Choice Type of Service ---', 'id_ref_service_type');
            $post['ocheck'] = ($info['oil_refill']) ? 'checked' : '';

        } else {
            $post['ref_service_type'] = selectlist('ref_service_type', 'id_ref_service_type', 'service_type', null,
                null, '--- Choice Type of Service ---', 'id_ref_service_type');
            $post['ocheck'] = '';

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


        if ($post['service'] == '') {
            $this->error['service'] = 'Please insert Service name.<br/>';
        } else {
            if (!$this->Service_model->CheckExistsRefService($post['service'], $post['ref_service_type'], $id)) {
                $this->error['service'] = 'Service name already exists, please input different name.<br/>';
            } else {
                if (utf8_strlen($post['service']) < 5) {
                    $this->error['service'] = 'Service name length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['detail'] == '') {
            $this->error['detail'] = 'Please insert Service Notes.<br/>';

        }

        if ($post['service_price'] == '') {
            $this->error['service_price'] = 'Please insert Service Price.<br/>';

        } else {
            // if (!validate_price($post['service_price'])) {
            // $this->error['service_price'] = 'Please try again with the right format Price.<br/>';
            // }
        }

        if ($post['ref_service_type'] == '' || $post['ref_service_type'] == '0') {
            $this->error['ref_service_type'] = 'Please choice Service Type';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
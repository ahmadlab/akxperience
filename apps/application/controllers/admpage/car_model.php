<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Car Series Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Car Model Management
 *********************************************/
class Car_model extends CI_Controller
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
        $this->ctrl = 'car_model';
        $this->template = getAdminFolder() . '/modules/car_model';
        $this->path_uri = getAdminFolder() . '/car_model';
        $this->path = site_url(getAdminFolder() . '/car_model');
        $this->title = get_admin_menu_title('car_model');
        $this->id_menu_admin = get_admin_menu_id('car_model');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->load->model(getAdminFolder() . '/ref_car_model', 'Ref_car_model');
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

        $s_model = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/t/';
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
        $list_cars_arr = array();
        $wtype = $wseries = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);
        $search_query = '';

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );


        if (strlen($s_model) > 1) {
            $s_model = substr($s_model, 1);

        } else {
            $s_model = "";
        }

        // $l_series 		= selectlist('car_series','series','series',null,$s_series,'--- Pilih Series ---');

        $total_records = $this->Ref_car_model->TotalCarsModel(myUrlDecode($s_model));

        if ($s_model) {
            $dis_urut = "none";
            $path = str_replace("/t/", "/t" . $s_model . "/", $path);
            ($search_query == '' ? $search_query .= $s_model : $search_query .= ' + ' . $s_model);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Ref_car_model->GetAllCarsModel(myUrlDecode($s_model), $lmt, $per_page);

        foreach ($query->result_array() as $cseries) {
            $no++;
            $id = $cseries['id_car_model'];
            $ref_publish = ($cseries['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $model = $cseries['model'];
            // $types 			= $cseries['types'];
            // $series 		= $cseries['series'];
            // $brands 		= $cseries['brands'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'model' => $model,
                /* 'brand'=>$brands,'type'=>$types,'series'=>$series, */
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
            'list_cars' => $list_cars_arr,
            // 'l_series' 			=> $l_series,
            's_model' => $s_model,
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
        $this->parser->parse($template . '/list_' . $this->ctrl . '.html', $data);
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
                $return = $this->Ref_car_model->ChangePublishModel($id);
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
        $s_model = myUrlEncode(trim($this->input->post('s_model')));
        redirect($this->path . '/main/t' . $s_model);
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
                    $this->Ref_car_model->DeleteCarsModel($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Car Series ID : ' . $id;
                    $log_desc = 'Delete Car Series ID : ' . $id . ';';
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

            $data_post = array(
                'model' => $post['model'],
            );

            // insert data
            $id = $this->Ref_car_model->InsertCarsModel($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Car Model :' . $post['model'];
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
                'model' => $post['model'],
            );
            $this->Ref_car_model->UpdateCarsModel($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Car Model :' . $post['model'];
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
            $query = $this->Ref_car_model->GetCarsModelById($id);
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
        if (isset($this->error['model'])) {
            $error_msg['model'] = alert_box($this->error['model'], 'error');
        } else {
            $error_msg['model'] = '';
        }

        // set value
        if ($this->input->post('model') != '') {
            $post['model'] = $this->input->post('model');
        } elseif ((int)$id > 0) {
            $post['model'] = $info['model'];
        } else {
            $post['model'] = '';
        }


        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
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
        $this->parser->parse($template . '/list_' . $this->ctrl . '_form.html', $data);
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


        if ($post['model'] == '') {
            $this->error['model'] = 'Please insert Car Model.<br/>';
        } else {
            if (!$this->Ref_car_model->CheckExistsCarsModel($post['model'], $id)) {
                $this->error['model'] = 'Car Model already exists, please input different Car Model.<br/>';
            } else {
                if (utf8_strlen($post['model']) < 3) {
                    $this->error['model'] = 'Car Model length must be at least 5 character(s).<br/>';
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


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
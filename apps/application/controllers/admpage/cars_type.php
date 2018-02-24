<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Car Types Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Car Types Management
 *********************************************/
class Cars_type extends CI_Controller
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
        $this->ctrl = 'cars_type';
        $this->template = getAdminFolder() . '/modules/cars_type';
        $this->path_uri = getAdminFolder() . '/cars_type';
        $this->path = site_url(getAdminFolder() . '/cars_type');
        $this->title = get_admin_menu_title('cars_type');
        $this->id_menu_admin = get_admin_menu_id('cars_type');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/cars_type/';
        $this->load->model(getAdminFolder() . '/cars_model', 'Cars_model');
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

        $s_brands = $this->uri->segment(4);
        $s_types = $this->uri->segment(5);
        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/b/t/';
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

        if (strlen($s_brands) > 1) {
            $s_brands = substr($s_brands, 1);
            // $idb 		= db_get_one('car_brands','id_brands',"brands = '$s_brands'");
            // $wtype 		= "AND id_brands = '$idb'";

        } else {
            $s_brands = "";

        }

        if (strlen($s_types) > 1) {
            $s_types = substr($s_types, 1);

        } else {
            $s_types = "";
        }

        $l_brands = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'", $s_brands,
            '--- Pilih Brand ---');
        $total_records = $this->Cars_model->TotalCarsType(myUrlDecode($s_brands), myUrlDecode($s_types));

        if ($s_brands) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_brands . "/", $path);
            ($search_query == '' ? $search_query .= $s_brands : $search_query .= ' + ' . $s_brands);
        }

        if ($s_types) {
            $dis_urut = "none";
            $path = str_replace("/t/", "/t" . $s_types . "/", $path);
            ($search_query == '' ? $search_query .= $s_types : $search_query .= ' + ' . $s_types);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Cars_model->GetAllCarsType(myUrlDecode($s_brands), myUrlDecode($s_types), $lmt, $per_page);

        foreach ($query->result_array() as $ctype) {
            $no++;
            $id = $ctype['id_car_types'];
            $ref_publish = ($ctype['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            // $car 			= $cbrand['cars'];
            $brand = db_get_one('car_brands', 'brands', "id_brands = '{$ctype['id_brands']}'");
            $types = $ctype['types'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'brand' => $brand,
                'type' => $types,
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
            'l_brands' => $l_brands,
            's_brands' => $s_brands,
            's_types' => $s_types,
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
        $this->parser->parse($template . '/list_cars_type.html', $data);
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
                $return = $this->Cars_model->ChangePublishType($id);
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
        $s_brands = myUrlEncode(trim($this->input->post('s_brands')));
        $s_types = myUrlEncode(trim($this->input->post('s_types')));
        redirect($this->path . '/main/b' . $s_brands . '/t' . $s_types);
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
                    $this->Cars_model->DeleteCarType($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Car Type ID : ' . $id;
                    $log_desc = 'Delete Car Type ID : ' . $id . ';';
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
                'id_brands' => $post['car_brand'],
                'types' => $post['car_type'],
            );

            // insert data
            $id_types = $this->Cars_model->InsertCarType($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_types;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_types . '; Car Type:' . $post['brands'] . '; Car Type Ref Publish:' . $post['ref_publish'];
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

        // if (!$this->Member_model->check_is_superadmin($id, $this->is_superadmin)) {
        // redirect($this->path_uri);
        // }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            // $ico	= $_FILES['thumb'];
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');


            $data_post = array(
                'id_brands' => $post['car_brand'],
                'types' => $post['car_type'],
            );
            $this->Cars_model->UpdateCarType($data_post, $id);

            // if ($ico['tmp_name'] != "") {
            // $filename = 'cars_'.$id.date('dmYHis');
            // $content_to_db = trans_image_resize_to_folder($ico, $this->path_car, $filename, $this->max_car_width, $this->max_car_height);

            // $data_pic_content = array('car_thumb'=>$content_to_db);

            // update to database
            // $this->Cars_model->UpdateCar($data_pic_content,$id);
            // }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Member Username:' . $post['username'] . '; Member Email:' . $post['email'];
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
            $query = $this->Cars_model->GetCarTypeById($id);
            if ($query->num_rows() > 0) {
                $type_info = $query->row_array();

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
        if (isset($this->error['car_type'])) {
            $error_msg['car_type'] = alert_box($this->error['car_type'], 'error');
        } else {
            $error_msg['car_type'] = '';
        }


        // set value
        if ($this->input->post('car_type') != '') {
            $post['car_type'] = $this->input->post('car_type');
        } elseif ((int)$id > 0) {
            $post['car_type'] = $type_info['types'];
        } else {
            $post['car_type'] = '';
        }

        if ((int)$id > 0) {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'",
                $type_info['id_brands'], '--- Pilih Brand ---', 'id_brands');
        } else {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'", null,
                '--- Pilih Brand ---', 'id_brands');
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
        $this->parser->parse($template . '/list_cars_type_form.html', $data);
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


        if ($post['car_type'] == '') {
            $this->error['car_type'] = 'Please insert Car Type.<br/>';
        } else {
            if (!$this->Cars_model->CheckExistsCarType($post['car_type'], $post['car_brand'], $id)) {
                $this->error['car_type'] = 'Car Type already exists, please input different Car Type.<br/>';
            }
            // else
            // {
            // if (utf8_strlen($post['car_type']) < 5)
            // {
            // $this->error['car_type'] = 'Car Type length must be at least 5 character(s).<br/>';
            // }
            // }
        }

        if ($post['car_brand'] == '' || $post['car_brand'] == 0) {
            $this->error['car_brand'] = 'Please select Car Brand.<br/>';
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
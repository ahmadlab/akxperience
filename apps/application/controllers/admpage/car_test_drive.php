<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Car Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Car Management
 *********************************************/
class Car_test_drive extends CI_Controller
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
        $this->ctrl = 'car_test_drive';
        $this->template = getAdminFolder() . '/modules/car_test_drive';
        $this->path_uri = getAdminFolder() . '/car_test_drive';
        $this->path = site_url(getAdminFolder() . '/car_test_drive');
        $this->title = get_admin_menu_title('car_test_drive');
        $this->id_menu_admin = get_admin_menu_id('car_test_drive');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/car_test_drive/';
        $this->load->model(getAdminFolder() . '/cars_taxonomy_model', 'Cars_taxonomy');

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

        // $s_msc         = $this->uri->segment(4);
        // $s_brands       = $this->uri->segment(5);
        // $s_types        = $this->uri->segment(6);
        // $s_series       = $this->uri->segment(7);
        $pg = $this->uri->segment(8);
        $per_page = 25;
        $uri_segment = 8;
        $no = 0;
        $path = $this->path . '/main/c/b/t/s/';
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

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        // if(strlen($s_msc) > 1) $s_msc = substr($s_msc,1);	
        // else $s_msc = "";

        // if(strlen($s_brands) > 1) {
        // $s_brands 	= substr($s_brands,1);
        // $idb 		= db_get_one('car_brands','id_brands',"brands = '$s_brands'");
        // $wtype 		= "AND id_brands = '$idb'";

        // }else {
        // $s_brands = "";

        // }

        // if(strlen($s_types) > 1){
        // $s_types 	= substr($s_types,1);
        // $idt 		= db_get_one('car_types','id_car_types',"types = '$s_types'");
        // $wseries 	= "AND id_type = '$idt'";

        // }else {
        // $s_types = "";
        // $wseries = "AND ref_publish = '3'";
        // }

        // if(strlen($s_series) > 1) $s_series = substr($s_series,1);	
        // else $s_series = "";
        // $l_brands 	= selectlist('car_brands','brands','brands',"ref_publish = '1'",$s_brands,'--- Pilih Brand ---');
        // $l_types 	= selectlist('car_types','types','types',"ref_publish = '1' $wtype",$s_types,'--- Pilih Type ---');
        // $l_series 	= selectlist('car_series','series','series',"ref_publish = '1' $wseries",$s_series,'--- Pilih Series ---');

        // $total_records = $this->Cars_model->TotalCars(myUrlDecode($s_msc), myUrlDecode($s_brands),myUrlDecode($s_types),myUrlDecode($s_series));
        $total_records = $this->Cars_taxonomy->TotalTestDrive();

        // if($s_msc)
        // {
        // $dis_urut = "none";
        // $path = str_replace("/c/","/c".$s_msc."/",$path);
        // }

        // if($s_brands)
        // {
        // $dis_urut = "none";
        // $path = str_replace("/b/","/b".$s_brands."/",$path);
        // }

        // if($s_types)
        // {
        // $dis_urut = "none";
        // $path = str_replace("/t/","/t".$s_types."/",$path);
        // }

        // if($s_series)
        // {
        // $dis_urut = "none";
        // $path = str_replace("/s/","/s".$s_series."/",$path);
        // }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Cars_taxonomy->GetAllTestDrive($lmt, $per_page);
        // $query = $this->Cars_model->GetAllCars(myUrlDecode($s_msc), myUrlDecode($s_brands),myUrlDecode($s_types),myUrlDecode($s_series),$is_superadmin,$lmt,$per_page);

        foreach ($query->result_array() as $cars) {
            $no++;
            $id = $cars['id'];
            $ref_publish = ($cars['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $msc_code = $cars['msc_code'];
            $brand = $cars['brands'];
            $types = $cars['types'];
            $series = $cars['series'];
            $model = $cars['model'];
            $trans = $cars['transmisi'];
            $cc = $cars['car_cc'];
            $color = db_get_one('car_colors', 'car_color_thumb', "id_car = '" . $cars['id'] . "'");

            // $stock 			= $cars['stock'];
            // $mdate 			= ($cars['modified_date'] == '0000-00-00 00:00:00') ? '-' : $cars['modified_date'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'msc_code' => $msc_code,/* 'modified' => $mdate,'stock' => $stock, */
                'brand' => $brand,
                'type' => $types,
                'series' => $series,
                'edit_href' => $edit_href,
                'color' => $color,
                'trans' => $trans,
                'cc' => $cc,
                'model' => $model
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
            'list_cars' => $list_cars_arr,
            // 's_msc' 			=> myUrlDecode($s_msc),
            // 'l_brands' 			=> $l_brands,
            // 'l_types' 			=> $l_types,
            // 'l_series' 			=> $l_series,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
        );
        $this->parser->parse($template . '/list_car_test_drive.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * add page
     */
    public function add()
    {
        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $ico = $_FILES['thumb'];
            $post = purify($this->input->post());

            $data_post = array(
                'id_car' => $post['ref_car'],
                // 'taxonomy'		=> 'tdrive',
            );

            // insert data
            $id_tcar = $this->Cars_taxonomy->InsertTestDrive($data_post);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_tcar;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_tcar . '; Taxonomy: Test Drive;';
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
                'id_car' => $post['ref_car'],
                // 'taxonomy'		=> 'tdrive',
            );
            $this->Cars_taxonomy->UpdateTestDrive($data_post, $id);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Taxonomy : Test Drive;';
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
        if ($this->input->post('id') != "") {
            if (($this->input->post('id') > 0)) {
                if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                    $id = array_filter(explode('-', $this->input->post('id')));
                    $this->Cars_taxonomy->DeleteTestDrive($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Car ID : ' . $id;
                    $log_desc = 'Delete Car ID : ' . $id . ';';
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
        $cars = $usrcar = $colors = array();

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Cars_taxonomy->GetTestDriveById($id);
            if ($query->num_rows() > 0) {
                $car_info = $query->row_array();

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
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }

        if (isset($this->error['ref_car'])) {
            $error_msg['ref_car'] = alert_box($this->error['ref_car'], 'error');
        } else {
            $error_msg['ref_car'] = '';
        }


        if ($this->input->post('ref_car') != '') {
            $post['carlst'] = get_carslst($this->input->post('ref_car'), "ref_publish = '1'");

        } elseif ((int)$id > 0) {
            $post['carlst'] = get_carslst($car_info['id_car'], "ref_publish = '1'");

        } else {
            $post['carlst'] = get_carslst('', "ref_publish = '1'");
        }
        // debugvar($post);
        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'ids' => $id,
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
        );
        $this->parser->parse($template . '/list_cars_test_drive_form.html', $data);
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

        if ($post['ref_car'] == '' || $post['ref_car'] == 0) {
            $this->error['ref_car'] = 'Please select Car Brand.<br/>';
        }

        if (!$this->Cars_taxonomy->CheckExistsCarName($post['ref_car'], $id)) {
            $this->error['ref_car'] = 'This car already on test drive, choice different car';
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
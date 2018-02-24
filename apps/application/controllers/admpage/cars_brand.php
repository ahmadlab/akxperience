<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Car Brands Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Car Brands Management
 *********************************************/
class Cars_brand extends CI_Controller
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
        $this->ctrl = 'cars_brand';
        $this->template = getAdminFolder() . '/modules/cars_brand';
        $this->path_uri = getAdminFolder() . '/cars_brand';
        $this->path = site_url(getAdminFolder() . '/cars_brand');
        $this->title = get_admin_menu_title('cars_brand');
        $this->id_menu_admin = get_admin_menu_id('cars_brand');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/cars_brand/';
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
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
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
        $search_query = '';

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_brands) > 1) {
            $s_brands = substr($s_brands, 1);

        } else {
            $s_brands = "";

        }
        $total_records = $this->Cars_model->TotalCarsBrand(myUrlDecode($s_brands));


        if ($s_brands) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_brands . "/", $path);
            ($search_query == '' ? $search_query .= $s_brands : $search_query .= ' + ' . $s_brands);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Cars_model->GetAllCarsBrand(myUrlDecode($s_brands), $lmt, $per_page);

        foreach ($query->result_array() as $cbrand) {
            $no++;
            $id = $cbrand['id_brands'];
            $ref_publish = ($cbrand['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $brand = $cbrand['brands'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'brand' => $brand,
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
            'breadcrumbs' => $breadcrumbs,
            's_brand' => $s_brands,
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
        $this->parser->parse($template . '/list_cars_brand.html', $data);
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
                $return = $this->Cars_model->ChangePublishBrand($id);
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
        $s_brands = myUrlEncode(trim($this->input->post('s_brand')));
        redirect($this->path . '/main/b' . $s_brands);
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
                    $this->Car_model->DeleteCarBrand($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Car Brand ID : ' . $id;
                    $log_desc = 'Delete Car Brand ID : ' . $id . ';';
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
            $ico = $_FILES['thumb'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'brands' => $post['car_brand'],

            );

            // insert data
            $id_carbrands = $this->Cars_model->InsertCarBrand($data_post);


            if ($ico['tmp_name'] != "") {
                $filename = 'cars_brand_' . $id_carbrands . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_carbrand, $filename,
                    $this->max_carbrand_width, $this->max_carbrand_height);

                $data_pic_content = array('car_brand_thumb' => $content_to_db);

                // update to database
                $this->Car_model->UpdateCarBrand($data_pic_content, $id_carbrands);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_carbrands;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_carbrands . '; Car Brand:' . $post['brands'] . '; Car Ref Publish:' . $post['ref_publish'];
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
            $ico = $_FILES['thumb'];
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');


            $data_post = array(
                'car_brand' => $post['car_brand'],
            );
            $this->Car_model->UpdateCarBrand($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $filename = 'cars_brand_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_carbrand, $filename,
                    $this->max_carbrand_width, $this->max_carbrand_height);

                $data_pic_content = array('car_brand_thumb' => $content_to_db);

                // update to database
                $this->Car_model->UpdateCarBrand($data_pic_content, $id);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Car Brand:' . $post['brands'] . '; Car Ref Publish:' . $post['ref_publish'];
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
            $query = $this->Cars_model->GetCarBrandById($id);
            if ($query->num_rows() > 0) {
                $carbrands_info = $query->row_array();

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
        if (isset($this->error['car_brands'])) {
            $error_msg['car_brands'] = alert_box($this->error['car_brands'], 'error');
        } else {
            $error_msg['car_brands'] = '';
        }


        // set value
        if ($this->input->post('car_brands') != '') {
            $post['car_brands'] = $this->input->post('car_brands');
        } elseif ((int)$id > 0) {
            $post['car_brands'] = $carbrands_info['brands'];
        } else {
            $post['car_brands'] = '';
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
        $this->parser->parse($template . '/list_cars_brand_form.html', $data);
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


        if ($post['car_brand'] == '') {
            $this->error['car_brand'] = 'Please insert Car Brand.<br/>';
        } else {
            if (!$this->Cars_model->CheckExistsCarBrand($post['car_brand'], $id)) {
                $this->error['car_brand'] = 'Car name already exists, please input different Car name.<br/>';
            } else {
                if (utf8_strlen($post['car_brand']) < 5) {
                    $this->error['car_brand'] = 'Car brand length must be at least 5 character(s).<br/>';
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
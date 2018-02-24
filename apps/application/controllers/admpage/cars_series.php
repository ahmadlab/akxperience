<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Car Series Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Car Series Management
 *********************************************/
class Cars_series extends CI_Controller
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
        $this->ctrl = 'cars_series';
        $this->template = getAdminFolder() . '/modules/cars_series';
        $this->path_uri = getAdminFolder() . '/cars_series';
        $this->path = site_url(getAdminFolder() . '/cars_series');
        $this->title = get_admin_menu_title('cars_series');
        $this->id_menu_admin = get_admin_menu_id('cars_series');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/cars_series/';
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

        $s_types = $this->uri->segment(4);
        $s_series = $this->uri->segment(5);
        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/t/s/';
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

        if (strlen($s_series) > 1) {
            $s_series = substr($s_series, 1);

        } else {
            $s_series = "";

        }

        if (strlen($s_types) > 1) {
            $s_types = substr($s_types, 1);

        } else {
            $s_types = "";
        }

        $l_types = selectlist('car_types', 'types', 'types', "ref_publish = '1'", $s_types, '--- Pilih Product ---');
        $total_records = $this->Cars_model->TotalCarsSeries(myUrlDecode($s_types), myUrlDecode($s_series));

        if ($s_series) {
            $dis_urut = "none";
            $path = str_replace("/s/", "/s" . $s_series . "/", $path);
            ($search_query == '' ? $search_query .= $s_series : $search_query .= ' + ' . $s_series);
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

        $query = $this->Cars_model->GetAllCarsSeries(myUrlDecode($s_types), myUrlDecode($s_series), $lmt, $per_page);

        foreach ($query->result_array() as $cseries) {
            $no++;
            $id = $cseries['id'];
            $ref_publish = ($cseries['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $types = $cseries['types'];
            $series = $cseries['series'];
            $brands = $cseries['brands'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'brand' => $brands,
                'type' => $types,
                'series' => $series,
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
            'l_types' => $l_types,
            's_series' => $s_series,
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
        $this->parser->parse($template . '/list_cars_series.html', $data);
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
                $return = $this->Cars_model->ChangePublishSeries($id);
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
        $s_series = myUrlEncode(trim($this->input->post('s_series')));
        $s_types = myUrlEncode(trim($this->input->post('s_types')));
        redirect($this->path . '/main/t' . $s_types . '/s' . $s_series);
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
                    $this->Cars_model->DeleteCarSeries($id);
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
                'id_type' => $post['car_types'],
                'series' => $post['car_series'],
            );

            // insert data
            $id_series = $this->Cars_model->InsertCarSeries($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_series;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_series . '; Car Series:' . $post['series'] . '; Car Type :' . $post['car_types'];
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
                'id_type' => $post['car_types'],
                'series' => $post['car_series'],
            );
            $this->Cars_model->UpdateCarSeries($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Car Series:' . $post['series'] . '; Car Type :' . $post['car_types'];
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
            };
        }
        $this->getForm($id);
    }


    public function get_type_car()
    {
        $post = $this->input->post('id');
        $type = $this->input->post('t');
        if ($post && $type) {
            switch ($type) {
                case 'search' :
                    $ctype = db_get_one('car_brands', 'id_brands', "brands = '$post'");
                    echo selectlist('car_types', 'types', 'types', "id_brands = '$ctype'", null,
                        '--- Pilih Product ---', 'id_car_types');
                    break;
                default :
                    echo selectlist('car_types', 'id_car_types', 'types', "ref_publish = '1' AND id_brands = '$post'",
                        null, '--- Pilih Product ---', 'id_car_types');
                    break;
            }
        }
        echo 0;

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
            $query = $this->Cars_model->GetCarSeriesById($id);
            if ($query->num_rows() > 0) {
                $series_info = $query->row_array();

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
        if (isset($this->error['car_series'])) {
            $error_msg['car_series'] = alert_box($this->error['car_series'], 'error');
        } else {
            $error_msg['car_series'] = '';
        }
        if (isset($this->error['car_types'])) {
            $error_msg['car_types'] = alert_box($this->error['car_types'], 'error');
        } else {
            $error_msg['car_types'] = '';
        }
        if (isset($this->error['car_brand'])) {
            $error_msg['car_brand'] = alert_box($this->error['car_brand'], 'error');
        } else {
            $error_msg['car_brand'] = '';
        }

        // set value
        if ($this->input->post('car_series') != '') {
            $post['car_series'] = $this->input->post('car_series');
        } elseif ((int)$id > 0) {
            $post['car_series'] = $series_info['series'];
        } else {
            $post['car_series'] = '';
        }

        if ($this->input->post('car_brand') != '') {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'",
                $this->input->post('car_brand'), '--- Pilih Brand ---', 'id_brands');

        } elseif ((int)$id > 0) {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'",
                $series_info['id_brands'], '--- Pilih Brand ---', 'id_brands');

        } else {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'", null,
                '--- Pilih Brand ---', 'id_brands');
        }

        if ($this->input->post('car_types') != '') {
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types',
                "ref_publish = '1' and id_brands = '" . $this->input->post('car_brand') . "'",
                $this->input->post('car_types'), '--- Choice Product ---', 'id_car_types');

        } elseif ((int)$id > 0) {
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types',
                "ref_publish = '1' and id_brands = '" . $series_info['id_brands'] . "'", $series_info['id_type'],
                '--- Choice Product ---', 'id_car_types');

        } else {
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types', "ref_publish = '3'", null,
                '--- Choice Product ---', 'id_car_types');
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
        $this->parser->parse($template . '/list_cars_series_form.html', $data);
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


        if ($post['car_series'] == '') {
            $this->error['car_series'] = 'Please insert Car Series.<br/>';
        } else {
            if (!$this->Cars_model->CheckExistsCarSeries($post['car_types'], $post['car_series'], $id)) {
                $this->error['car_series'] = 'Car Series already exists, please input different Car Series.<br/>';
            } else {
                if (utf8_strlen($post['car_series']) < 3) {
                    $this->error['car_series'] = 'Car Series length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['car_types'] == '' || $post['car_types'] == 0) {
            $this->error['car_types'] = 'Please select Car Type.<br/>';
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
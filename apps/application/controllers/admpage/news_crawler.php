<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * New Crawler Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : News Crawler Management
 *********************************************/
class News_crawler extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $template;
    private $path;
    private $path_uri;
    private $title;
    private $total;
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
        $this->ctrl = 'news_crawler';
        $this->template = getAdminFolder() . '/modules/news_crawler';
        $this->path_uri = getAdminFolder() . '/news_crawler';
        $this->path = site_url(getAdminFolder() . '/news_crawler');
        $this->title = get_admin_menu_title('news_crawler');
        $this->id_menu_admin = get_admin_menu_id('news_crawler');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->total = 0;
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/news_crawler/';
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
        $list_admin_arr = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $path_uri = $this->path_uri;
        $template = $this->template;
        $breadcrumbs = array();
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
            'result' => $result
        );
        $this->parser->parse($template . '/news_crawler.html', $data);
        $this->global_libs->print_footer();
    }

    public function buffs()
    {
        $words = $this->input->post('search');
        $pg = $this->uri->segment(4);
        $per_page = 25;
        $uri_segment = 4;
        $no = 0;
        $path = $this->path . '/main/';
        $total = $this->total;
        $add_btn = site_url($path_uri . '/add');
        $template = $this->template;


        /* attempt to fetching data from determined webs
         * if more than one, insert it into db:temp
         *
         */
        $total = $this->News_model->TotalNews($words);

        if ($total <= 0) {
            echo $this->parser->parse($template . '/not_found.html', array(), true), exit;
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $buffs = $this->News_model->getAllNews($words, $lmt, $per_page);
        foreach ($buffs->result_array() as $buff) {
            $no++;
            $id = $buff['id'];
            $title = $buff['title'];
            $content = $buff['content'];
            $thumb = "<img src='" . $buff['thumb'] . "' width='130' height='130' />";

            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'title' => $title,
                'content' => $content,
                'thumb' => $thumb
            );
        }

        // paging
        $paging = global_paging($total, $per_page, $path, $uri_segment);
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
            'pagination' => $paging,
            '' => $content,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'add_btn' => $add_btn,
            'result' => $result
        );
        echo $this->parser->parse($template . '/holder.html', $data, true);

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
                $return = $this->Cars_model->ChangePublish($id);
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
        $s_cars = myUrlEncode(trim($this->input->post('s_cars')));
        $s_brands = myUrlEncode(trim($this->input->post('s_brands')));
        $s_types = myUrlEncode(trim($this->input->post('s_types')));
        $s_series = myUrlEncode(trim($this->input->post('s_series')));
        redirect($this->path . '/main/c' . $s_cars . '/b' . $s_brands . '/t' . $s_types . '/s' . $s_series);
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
                    foreach ($id as $ids) {
                        if (!$this->Adminmenu_model->CheckAdminMenuByGroupId(adm_sess_usergroupid(),
                                $id) || $this->is_superadmin
                        ) {
                            $this->session->set_flashdata('info_msg',
                                'You don\'t have access to delete ' . $this->title . '.');
                        }
                    }
                    $this->Car_model->DeleteCar($id);
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
                $this->Car_model->DeleteCarThumbByID($this->input->post('id'));
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
                $this->session->set_flashdata('success_msg', $this->title . ' Avatar has been deleted.');
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
            $ico = $_FILES['thumb'];
            $post = purify($this->input->post());

            $data_post = array(
                'car_namme' => $post['car_name'],
                'car_price' => $post['car_price'],
                'car_brand' => $post['car_brand'],
                'car_type' => $post['car_type'],
                'car_series' => $post['car_series'],
                'car_ref_publis' => $post['car_ref_publis'],

            );

            // insert data
            $id_car = $this->Cars_model->InsertCar($data_post);

            if ($ico['tmp_name'] != "") {
                $filename = 'cars_' . $id_car . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_car, $filename, $this->max_car_width,
                    $this->max_car_height);

                $data_pic_content = array('car_thumb' => $content_to_db);

                // update to database
                $this->Car_model->UpdateCar($data_pic_content, $id_car);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_car;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_car . '; Member Username:' . $post['username'] . '; Member Email:' . $post['email'];
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
            $ico = $_FILES['thumb'];
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');


            $data_post = array(
                'car_namme' => $post['car_name'],
                'car_price' => $post['car_price'],
                'car_brand' => $post['car_brand'],
                'car_type' => $post['car_type'],
                'car_series' => $post['car_series'],
                'car_ref_publis' => $post['car_ref_publis'],
            );
            $this->Car_model->UpdateCar($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $filename = 'cars_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_car, $filename, $this->max_car_width,
                    $this->max_car_height);

                $data_pic_content = array('car_thumb' => $content_to_db);

                // update to database
                $this->Car_model->UpdateCar($data_pic_content, $id);
            }


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

    public function get_type_car()
    {
        $post = $this->input->post('id');
        $type = $this->input->post('t');
        if ($post && $type) {
            switch ($type) {
                case 'search' :
                    $ctype = db_get_one('car_brands', 'id_brands', "brands = '$post'");
                    echo selectlist('car_types', 'types', 'types', "id_brands = '$ctype'", null, '--- Pilih Type ---',
                        'id_car_types');
                    break;
                default :
                    echo selectlist('car_types', 'id_car_types', 'types', "ref_publish = '1' AND id_brands = '$post'",
                        null, '--- Pilih Type ---', 'id_car_types');
                    break;
            }
        }
        echo 0;

    }

    public function get_series_car()
    {
        $post = $this->input->post('id');
        $type = $this->input->post('t');
        if ($post && $type) {
            switch ($type) {
                case 'search' :
                    $ctype = db_get_one('car_types', 'id_car_types', "types = '$post'");
                    echo selectlist('car_series', 'series', 'series', "id_type = '$ctype'", null,
                        '--- Pilih Series ---', 'id_car_series');
                    break;
                default :
                    echo selectlist('car_series', 'id_car_series', 'series', "ref_publish = '1' AND id_type = '$post'",
                        null, '--- Pilih Series ---', 'id_car_series'), exit;
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
        $pic_thumbnail = '';
        $cars = $usrcar = array();

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Cars_model->GetCarById($id);
            if ($query->num_rows() > 0) {
                $car_info = $query->row_array();

                if ($car_info['car_thumb'] != '') {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/cars/' . $car_info['car_thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $car_info['id_cars'] . '"style="cursor:pointer;" >Delete Picture</a>
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
        if (isset($this->error['car_name'])) {
            $error_msg['car_name'] = alert_box($this->error['car_name'], 'error');
        } else {
            $error_msg['car_name'] = '';
        }
        if (isset($this->error['car_price'])) {
            $error_msg['car_price'] = alert_box($this->error['car_price'], 'error');
        } else {
            $error_msg['car_price'] = '';
        }
        if (isset($this->error['car_brand'])) {
            $error_msg['car_brand'] = alert_box($this->error['car_brand'], 'error');
        } else {
            $error_msg['car_brand'] = '';
        }
        if (isset($this->error['car_type'])) {
            $error_msg['car_type'] = alert_box($this->error['car_type'], 'error');
        } else {
            $error_msg['car_type'] = '';
        }
        if (isset($this->error['car_series'])) {
            $error_msg['car_series'] = alert_box($this->error['car_series'], 'error');
        } else {
            $error_msg['car_series'] = '';
        }

        // set value
        if ($this->input->post('car_name') != '') {
            $post['car_name'] = $this->input->post('car_name');
        } elseif ((int)$id > 0) {
            $post['car_name'] = $car_info['car_name'];
        } else {
            $post['car_name'] = '';
        }

        if ($this->input->post('car_price') != '') {
            $post['car_price'] = $this->input->post('car_price');
        } elseif ((int)$id > 0) {
            $post['car_price'] = $car_info['car_price'];
        } else {
            $post['car_price'] = '';
        }

        if ((int)$id > 0) {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'",
                $car_info['car_brand'], '--- Pilih Brand ---', 'id_brands');
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types', "ref_publish = '1'",
                $car_info['car_type'], '--- Pilih Type ---', 'id_car_types');
            $post['serieslist'] = selectlist('car_series', 'id_car_series', 'series', "ref_publish = '1'",
                $car_info['car_series'], '--- Pilih Series ---', 'id_car_series');
        } else {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'", null,
                '--- Pilih Brand ---', 'id_brands');
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types', "ref_publish = '3'", null,
                '--- Pilih Type ---', 'id_car_types');
            $post['serieslist'] = selectlist('car_series', 'id_car_series', 'series', "ref_publish = '3'", null,
                '--- Pilih Series ---', 'id_car_series');
        }

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
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
            'carlist' => $cars,
        );
        $this->parser->parse($template . '/list_cars_form.html', $data);
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


        if ($post['car_name'] == '') {
            $this->error['car_name'] = 'Please insert Car name.<br/>';
        } else {
            if (!$this->Cars_model->CheckExistsCarName($post['car_name'], $id)) {
                $this->error['car_name'] = 'Car name already exists, please input different Car name.<br/>';
            } else {
                if (utf8_strlen($post['car_name']) < 5) {
                    $this->error['car_name'] = 'Car name length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['car_price'] == '') {
            $this->error['car_price'] = 'Please insert Car Price.<br/>';
        } else {
            // $car_price = (int)$post['car_price'];


        }

        if ($post['car_brand'] == '' || $post['car_brand'] == 0) {
            $this->error['car_brand'] = 'Please select Car Brand.<br/>';
        }
        if ($post['car_types'] == '' || $post['car_types'] == 0) {
            $this->error['car_type'] = 'Please select Car Type.<br/>';
        }
        if ($post['car_series'] == '' || $post['car_series'] == 0) {
            $this->error['car_series'] = 'Please select Car Series.<br/>';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function crawling_news()
    {
        $this->News_model->crawling();
    }


}


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
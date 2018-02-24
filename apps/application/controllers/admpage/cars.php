<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Car Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Car Management
 *********************************************/
class Cars extends CI_Controller
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
        $this->ctrl = 'cars';
        $this->template = getAdminFolder() . '/modules/cars';
        $this->path_uri = getAdminFolder() . '/cars';
        $this->path = site_url(getAdminFolder() . '/cars');
        $this->title = get_admin_menu_title('cars');
        $this->id_menu_admin = get_admin_menu_id('cars');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height_hxx = 478;
        $this->max_car_height_hx = 320;
        $this->max_car_height_h = 213;
        $this->max_car_width_hxx = 890;
        $this->max_car_width_hx = 593;
        $this->max_car_width_h = 395;
        $this->path_car = './uploads/cars/';
        $this->load->model(getAdminFolder() . '/cars_model', 'Cars_model');
        $this->load->model(getAdminFolder() . '/cars_price_model', 'Cars_price_model');
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

        $s_msc = $this->uri->segment(4);
        $s_brands = $this->uri->segment(5);
        $s_types = $this->uri->segment(6);
        $s_series = $this->uri->segment(7);
        $s_model = $this->uri->segment(8);
        $pg = $this->uri->segment(9);
        $per_page = 25;
        $uri_segment = 9;
        $no = 0;
        $path = $this->path . '/main/c/b/t/s/m/';
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
        $list_cars_arr = array();
        $wtype = $wseries = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_msc) > 1) {
            $s_msc = substr($s_msc, 1);
        } else {
            $s_msc = "";
        }

        if (strlen($s_brands) > 1) {
            $s_brands = substr($s_brands, 1);
            $idb = db_get_one('car_brands', 'id_brands', "brands = '$s_brands'");
            $wtype = "AND id_brands = '$idb'";

        } else {
            $s_brands = "";

        }

        if (strlen($s_types) > 1) {
            $s_types = substr($s_types, 1);
            $idt = db_get_one('car_types', 'id_car_types', "types = '$s_types'");
            $wseries = "AND id_type = '$idt'";

        } else {
            $s_types = "";
            $wseries = "AND ref_publish = '3'";
        }

        if (strlen($s_series) > 1) {
            $s_series = substr($s_series, 1);
        } else {
            $s_series = "";
        }

        if (strlen($s_model) > 1) {
            $s_model = substr($s_model, 1);
        } else {
            $s_model = "";
        }
        $l_brands = selectlist('car_brands', 'brands', 'brands', "ref_publish = '1'", $s_brands, '--- Pilih Brand ---');
        $l_types = selectlist('car_types', 'types', 'types', "ref_publish = '1' $wtype", $s_types,
            '--- Pilih Type ---');
        $l_series = selectlist('car_series', 'series', 'series', "ref_publish = '1' $wseries", $s_series,
            '--- Pilih Series ---');
        $l_model = selectlist('car_model', 'model', 'model', "ref_publish = '1'", $s_model, '--- Pilih Model ---');

        $total_records = $this->Cars_model->TotalCars(myUrlDecode($s_msc), myUrlDecode($s_brands),
            myUrlDecode($s_types), myUrlDecode($s_series), myUrlDecode($s_model));

        if ($s_msc) {
            $dis_urut = "none";
            $path = str_replace("/c/", "/c" . $s_msc . "/", $path);
            ($search_query == '' ? $search_query .= $s_msc : $search_query .= ' + ' . $s_msc);
        }

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

        if ($s_series) {
            $dis_urut = "none";
            $path = str_replace("/s/", "/s" . $s_series . "/", $path);
            ($search_query == '' ? $search_query .= $s_series : $search_query .= ' + ' . $s_series);
        }

        if ($s_model) {
            $dis_urut = "none";
            $path = str_replace("/m/", "/m" . $s_model . "/", $path);
            ($search_query == '' ? $search_query .= $s_model : $search_query .= ' + ' . $s_model);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Cars_model->GetAllCars(myUrlDecode($s_msc), myUrlDecode($s_brands), myUrlDecode($s_types),
            myUrlDecode($s_series), myUrlDecode($s_model), $is_superadmin, $lmt, $per_page);

        $stock_total = 0;

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
            $engine = $cars['engine'];
            $price = $cars['car_price'];
            $stock = $cars['stock'];
            $color = $cars['colors'];
            $on_sale = ucfirst($cars['on_sale']);

            $mdate = ($cars['modified_date'] == '0000-00-00 00:00:00') ? '-' : $cars['modified_date'];

            $this->db->select_sum('stock');
            $this->db->where('id_car', $id);
            $this->db->where('status', 'sale');
            $query2 = $this->db->get('jdi_car_colors');
            foreach ($query2->result_array() as $carcolors)
            {
                $stock_total = $carcolors['stock'];
            }

            if ($stock_total==null) $stock_total=0;

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'msc_code' => $msc_code,
                'modified' => $mdate,
                'model' => $model,
                'trans' => $trans,
                'cc' => $cc,
                'on_sale' => $on_sale,
                'stock' => $stock_total,
                'brand' => $brand,
                'type' => $types,
                'series' => $series,
                'edit_href' => $edit_href,
                'color' => $color,
                'price' => $price,
                'engine' => $engine
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
            's_msc' => myUrlDecode($s_msc),
            's_brands' => myUrlDecode($s_brands),
            's_types' => myUrlDecode($s_types),
            's_series' => myUrlDecode($s_series),
            's_model' => myUrlDecode($s_model),
            'l_brands' => $l_brands,
            'l_types' => $l_types,
            'l_series' => $l_series,
            'l_model' => $l_model,
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
        $this->parser->parse($template . '/list_cars.html', $data);
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
        $s_msc = myUrlEncode(trim($this->input->post('s_msc')));
        $s_brands = myUrlEncode(trim($this->input->post('s_brands')));
        $s_types = myUrlEncode(trim($this->input->post('s_types')));
        $s_series = myUrlEncode(trim($this->input->post('s_series')));
        $s_model = myUrlEncode(trim($this->input->post('s_model')));
        redirect($this->path . '/main/c' . $s_msc . '/b' . $s_brands . '/t' . $s_types . '/s' . $s_series . '/m' . $s_model);
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
                    $this->Cars_model->DeleteCar($id);
                    $this->Cars_price_model->delete($id);
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
                $this->Cars_model->DeleteCarThumbByID($this->input->post('id'));
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
     * public function delete car color.
     *
     * delete car color page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete_car_color()
    {
        auth_admin();
        if ($this->input->post('id') != '' && ctype_digit($this->input->post('id'))) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                $id = $this->input->post('id');
                $this->Cars_model->DeleteCarColorByID($id);
                #insert to log
                $log_last_user_id = $id;
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete Car Color ' . $this->title . ' ID : ' . $log_last_user_id;
                $log_desc = 'Delete Car Color ' . $this->title . ' ID : ' . $log_last_user_id . ';';
                $data_log = array(
                    'id_user' => $log_id_user,
                    'id_group' => $log_id_group,
                    'action' => $log_action,
                    'desc' => $log_desc,
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data_log);
                echo '1', exit;
            }
        }
        echo '0', exit;
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
                'msc_code' => $post['msc_code'],
                'car_price' => $post['car_price'],
                'car_brand' => $post['car_brand'],
                'car_type' => $post['car_types'],
                'car_model' => $post['car_model'],
                'car_series' => $post['car_series'],
                'transmisi' => $post['transmisi'],
                'car_cc' => $post['car_cc'],
                'car_engine' => $post['car_engine'],
                'stock' => $post['stock'],
                'on_sale' => $post['on_sale'],
                'notes' => $post['notes'],
            );

            // insert data
            $id_car = $this->Cars_model->InsertCar($data_post);

//            die(var_dump($id_car));

            if ($id_car) {
                $data_post = array();
                $branch = $this->db->select('id_ref_location, location')
                    ->from('jdi_ref_location')
                    ->where('company_code', 'AK')->get()->result_array();

                foreach ($branch as $v) {
                    $data_post[] = array(
                        'car_id' => $id_car,
                        'branch_id' => $v['branch_id'],
                        'price' => $post['branch_' . $v['branch_id']],
                    );
                }

                $this->Cars_price_model->add($data_post);
            }

            if ($ico['tmp_name'] != "") {

                $filename = 'cars_' . $id_car . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_car, $filename, $this->max_car_width,
                    $this->max_car_height);

                $data_pic_content = array('car_thumb' => $content_to_db);

                // update to database
                $this->Cars_model->UpdateCar($data_pic_content, $id_car);
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
            if (isset($_FILES['thumb'])) {
                $ico = $_FILES['thumb'];
            }
            $post = purify($this->input->post());
            $now = date('Y-m-d H:i:s');


            $data_post = array(
                'msc_code' => $post['msc_code'],
                'car_price' => $post['car_price'],
                'car_brand' => $post['car_brand'],
                'car_type' => $post['car_types'],
                'car_model' => $post['car_model'],
                'car_series' => $post['car_series'],
                'transmisi' => $post['transmisi'],
                'car_cc' => $post['car_cc'],
                'car_engine' => $post['car_engine'],
                'stock' => $post['stock'],
                'modified_date' => $now,
                'on_sale' => $post['on_sale'],
                'notes' => $post['notes'],
            );
            $this->Cars_model->UpdateCar($data_post, $id);

            $data_post = array();
            $branch = $this->Cars_price_model->populateBranch();

            foreach ($branch as $v) {
                $data_post[] = array(
                    'car_id' => $id,
                    'branch_id' => $v['id_ref_location'],
                    'price' => $post['branch_' . $v['id_ref_location']],
                );
            }

            $this->Cars_price_model->add($data_post, $id);


            if ($ico['tmp_name'] != "") {
                $filename = 'cars_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_car, $filename, $this->max_car_width,
                    $this->max_car_height);

                $data_pic_content = array('car_thumb' => $content_to_db);

                // update to database
                $this->Cars_model->UpdateCar($data_pic_content, $id);
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

    /**
     * insert car color page
     * @param int $get_id
     */
    public function insert_car_color()
    {
        auth_admin();
        $post = purify($this->input->post());
        $resp = $error = $type = '';
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($post['color']) && isset($post['id_car']) && isset($post['status'])) {
            $ico = $_FILES['color_thumb'];

            if ($ico['tmp_name'] == "") {
                $error = 'Please select thumb color';
            } elseif ($post['id_car'] == '0' || $post['id_car'] == '') {
                $error = 'To add the color for a car, Please Insert a car first';
            } else {
                $exist = db_get_one('car_colors', 'id_car',
                    "id_car = '" . $post['id_car'] . "' and ref_color = '" . $post['color'] . "'");
                if ($exist) {
                    $error = 'Color for this car already exist, please try different color';

                } else {
                    $info = getimagesize($ico['tmp_name']);

                    // debugvar($info);

                    if ($info[0] >= $this->max_car_width_hxx && $info[1] >= $this->max_car_height_hxx && $info['mime'] == 'image/png') {


                        $data = array(
                            'id_car' => $post['id_car'],
                            'ref_color' => $post['color'],
                            'stock' => $post['stock'],
                            'status' => $post['status'],
                        );

                        $this->db->insert('car_colors', $data);

                        if ($this->db->_error_message()) {
                            $error = 'Got an error occured while insert color, please try again,' . $this->db->_error_message();

                        } else {
                            $stat = $post['status'];
                            $type = 'inserted';
                            $ext = 'colors/';
                            $id = $this->db->insert_id();
                            $cname = db_get_one('ref_color', 'color', "id_ref_color = '" . $post['color'] . "'");
                            $cn = db_get_one('car', 'msc_code', "id_cars = '" . $post['id_car'] . "'");
                            $cname = ($cname) ? $cname : 'unknown';
                            $cn = ($cn != '-') ? $cn : 'unknown_car';

                            // $filename 			= 'car_coloru'.$cname.'_'.$post['id_car'].'_'.$post['color'].'_'.date('dmYHis');
                            $filename = $cn . date('dmYHis') . '_' . strtolower(str_replace(' ', '_', $cname));
                            $content_to_db = trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename,
                                $this->max_car_width_hxx, $this->max_car_height_hxx);
                            trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename . '_XHDPI',
                                $this->max_car_width_hx, $this->max_car_height_hx);
                            trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename . '_HDPI',
                                $this->max_car_width_h, $this->max_car_height_h);
                            $data_pic_content = array('car_color_thumb' => $content_to_db);

                            $this->Cars_model->UpdateCarColor($data_pic_content, $id);

                            $thumb = (file_exists($this->path_car . $ext . $content_to_db)) ? base_url() . 'uploads/cars/' . $ext . $content_to_db : '';

                            $resp = "<div id='wrapper-" . $id . "-color' class='four columns' style='float:left;'>
										<div>&nbsp;<img src='$thumb' class='img-$id-car' idcc='$id' idc='" . $post['id_car'] . "' /></div>
										<div style='margin-left:0px;'>
											<span style='font-size:8pt;display:inline;'>
												$cname<br>Stock: " . $post['stock'] . "<br>$stat<br>
												<a class='delete_color' ida='" . $id . "' style='cursor:pointer;'>Delete Color</a>
											</span>
										</div><br>
									</div>";
                            $type = 'inserted';
                            #insert to log
                            $log_id_user = adm_sess_userid();
                            $log_id_group = adm_sess_usergroupid();
                            $log_action = 'Add ' . $this->title . ' Car ID : ' . $post['id_car'];
                            $log_desc = 'Add ' . $this->title . '; Car Id:' . $post['id_car'] . '; Color Id :' . $post['color'] . '; Color Name :' . $cname;
                            $data_logs = array(
                                'id_user' => $log_id_user,
                                'id_group' => $log_id_group,
                                'action' => $log_action,
                                'desc' => $log_desc,
                                'create_date' => date('Y-m-d H:i:s'),
                            );
                            insert_to_log($data_logs);
                        }

                    } else {
                        $error = 'Minimum Image width 890px and Height 480px <br> Image type : PNG only';
                    }

                }
            }
        } else {
            $error = 'Please try with the right method';
        }

        if ($error) {
            $html = array('status' => 500, 'msg' => $error, 'type' => $type);
        } else {
            $html = array('status' => 200, 'msg' => htmlentities($resp), 'type' => $type);
        }
        echo json_encode($html);
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
                        null, '--- Pilih Series ---', 'id_car_series');
                    break;
            }
        }
        echo 0;

    }

    public function get_model_car()
    {
        $post = $this->input->post('id');
        $type = $this->input->post('t');
        if ($post && $type) {
            switch ($type) {
                case 'search' :
                    $ctype = db_get_one('car_model', 'id_car_model', "types = '$post'");
                    echo selectlist('car_model', 'model', 'model', "id_series = '$ctype'", null, '--- Choice Model ---',
                        'id_car_model');
                    break;
                default :
                    echo selectlist('car_model', 'id_car_model', 'model', null, null, '--- Choice Model ---',
                        'id_car_model'), exit;
                    break;
            }
        }
        echo 0;

    }

    public function get_color_car()
    {
        $post = $this->input->post();
        if ($post['idc'] && $post['idcc']) {
            $ccolor = $this->db->get_where('car_colors',
                array('id_car_colors' => $post['idcc'], 'id_car' => $post['idc']));

            if ($ccolor->num_rows() > 0) {
                $ccolor = $ccolor->row_array();
                $ccolor['refcolorlst'] = selectlist('ref_color', 'id_ref_color', 'color', null, $ccolor['ref_color'],
                    '--- Choice Color ---', 'id_ref_color');
                $ccolor['thumb'] = ($ccolor['car_color_thumb'] != '' && file_exists('./uploads/cars/colors/' . $ccolor['car_color_thumb'])) ? base_url() . 'uploads/cars/colors/' . $ccolor['car_color_thumb'] : '';
                $ccolor['statuslst'] = print_car_trans($ccolor['status'], $label = '--- Choice Status Sale ---',
                    'jdi_car_colors', 'status');
                $ccolor['ids'] = $ccolor['id_car'];
                $ccolor['idcc'] = $ccolor['id_car_colors'];

                echo $this->parser->parse($this->template . '/form.html', $ccolor, true), exit;
            }

        }
        echo 'false';
    }

    public function update_car_color()
    {
        auth_admin();
        $post = purify($this->input->post());
        $resp = $error = $type = '';
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($post['color']) && isset($post['id_car']) && isset($post['id_car_color']) && isset($post['status'])) {
            $exist = db_get_one('car_colors', 'id_car',
                "id_car = '" . $post['id_car'] . "' and ref_color = '" . $post['color'] . "' and id_car_colors NOT IN('" . $post['id_car_color'] . "')");
            if ($exist) {
                $error = 'Color for this car already exist, please try different color';
            } else {
                $ico = $_FILES['color_thumb'];
                if ($ico['tmp_name'] != "") {
                    $info = getimagesize($ico['tmp_name']);
                    if ($info[0] < $this->max_car_width_hxx || $info[1] < $this->max_car_height_hxx || $info['mime'] != 'image/png') {
                        $error = 'Minimum Image width 890px and Height 480px <br> Image type : PNG only';
                        $html = array('status' => 500, 'msg' => $error, 'type' => 'updated');
                        echo json_encode($html), exit;
                    }
                }

                // else
                // {
                $data = array(
                    'id_car' => $post['id_car'],
                    'ref_color' => $post['color'],
                    'stock' => $post['stock'],
                    'status' => $post['status'],
                );

                $this->db->where('id_car_colors', $post['id_car_color'])->update('car_colors', $data);

                if ($this->db->_error_message()) {
                    $error = 'Got an error occured while updating color, please try again';
                } else {
                    $ext = 'colors/';
                    if ($ico['tmp_name'] != "") {

                        $c = db_get_one('car_colors', 'car_color_thumb',
                            "id_car_colors = '" . $post['id_car_color'] . "'");
                        if ($c) {
                            $img = substr($c, 0, -4);
                            $ex = substr($c, -4);
                            $locate = $this->path_car . $ext;
                            unlink($locate . $c);
                            unlink($locate . $img . '_HDPI' . $ex);
                            unlink($locate . $img . '_XHDPI' . $ex);
                        }

                        $id = $post['id_car_color'];
                        $cname = db_get_one('ref_color', 'color', "id_ref_color = '" . $post['color'] . "'");
                        $cn = db_get_one('car', 'msc_code', "id_cars = '" . $post['id_car'] . "'");
                        $cname = ($cname) ? $cname : 'unknown';
                        $cn = ($cn != '-') ? $cn : 'unknown_car';
                        $stock = $post['stock'];

                        $filename = $cn . date('dmYHis') . '_' . strtolower(str_replace(' ', '_', $cname));
                        $content_to_db = trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename,
                            $this->max_car_width_hxx, $this->max_car_height_hxx);
                        trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename . '_XHDPI',
                            $this->max_car_width_hx, $this->max_car_height_hx);
                        trans_image_resize_to_folder($ico, $this->path_car . $ext, $filename . '_HDPI',
                            $this->max_car_width_h, $this->max_car_height_h);
                        $data_pic_content = array('car_color_thumb' => $content_to_db);

                        $this->Cars_model->UpdateCarColor($data_pic_content, $id);

                        $thumb = (file_exists($this->path_car . $ext . $content_to_db)) ? base_url() . 'uploads/cars/' . $ext . $content_to_db : '';


                    } else {
                        $where = array('a.id_car_colors' => $post['id_car_color']);
                        $colors = $this->db->select('a.*,b.color')->from('car_colors a')
                            ->join('ref_color b', 'a.ref_color = b.id_ref_color', 'inner')->where($where)->get();
                        $thumb = $id = '';
                        if ($colors->num_rows() > 0) {
                            $colors = $colors->row_array();
                            $id = $colors['id_car_colors'];
                            $cname = $colors['color'];
                            $stock = $colors['stock'];

                            $thumb = (file_exists($this->path_car . $ext . $colors['car_color_thumb'])) ? base_url() . 'uploads/cars/' . $ext . $colors['car_color_thumb'] : '';
                        }

                    }
                    $stat = $post['status'];
                    // <div id='wrapper-".$id."-color' class='four columns' style='float:left;'>
                    $resp = "
									<div>&nbsp;<img src='$thumb' class='img-$id-car' idcc='$id' idc='" . $post['id_car'] . "' /></div>
									<div style='margin-left:0px;'>
										<span style='font-size:8pt;display:inline;'>
											$cname<br>Stock: $stock<br>$stat<br>
											<a class='delete_color' ida='" . $id . "' style='cursor:pointer;'>Delete Color</a>
										</span>
									</div><br>
								";
                    // </div>
                    $type = 'updated';
                }
                // }
            }
        } else {
            $error = 'Please try with the right method';
        }

        if ($error) {
            $html = array('status' => 500, 'msg' => $error, 'type' => $type);
        } else {
            $html = array('status' => 200, 'msg' => htmlentities($resp), 'type' => $type);
        }
        echo json_encode($html);
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

        $branch = $this->db->select('id_ref_location, location')
            ->from('jdi_ref_location')
            ->where('company_code', 'AK')->get()->result_array();

        foreach ($branch as $k => $v) {
            $branch[$k]['price'] = '';
        }

        if ($id) {
            $query = $this->Cars_model->GetCarById($id);
            if ($query->num_rows() > 0) {
                $car_info = $query->row_array();

                if ($car_info['car_thumb'] != '' && file_exists($this->path_car)) {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/cars/' . $car_info['car_thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $car_info['id_cars'] . '"style="cursor:pointer;" >Delete Picture</a>
                    </div>';
                }

                $colors = $this->db->select('a.*,b.color')->from('car_colors a')
                    ->join('ref_color b', 'a.ref_color = b.id_ref_color', 'inner')
                    ->where_in('a.id_car', array($car_info['id_cars']))->get();
                if ($colors->num_rows() > 0) {
                    $colors = $colors->result_array();
                    $i = 1;
                    foreach ($colors as $k => $v) {
                        $opath = base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'];
                        $colors[$k]['car_color_thumb'] = ($v['car_color_thumb'] != '' && file_exists($this->path_car . 'colors/' . $v['car_color_thumb'])) ? $opath : '';
                        $colors[$k]['delete'] = '<a class="delete_color" ida="' . $v['id_car_colors'] . '"style="cursor:pointer;" >Delete Color</a>';
                        $colors[$k]['stat'] = $v['status'];
                        $colors[$k]['stock'] = $v['stock'];
                        if ($i == 3) {
                            $i = 0;
                            $colors[$k]['separator'] = '</div><div class="twelve columns" id="colorlst">';
                        } else {
                            $colors[$k]['separator'] = '';
                        }
                        $i++;
                    }
                } else {
                    $colors = array();
                }

                $cprice = $this->db->from('jdi_car_price')
                    ->where('car_id', $id)->get();
                $cprice = $cprice->result_array();

                foreach ($branch as $k => $v) {
                    foreach ($cprice as $val) {
                        if ($v['id_ref_location'] == $val['branch_id']) {
                            $branch[$k]['price'] = $val['price'];
                        }
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
        if (isset($this->error['msc_code'])) {
            $error_msg['msc_code'] = alert_box($this->error['msc_code'], 'error');
        } else {
            $error_msg['msc_code'] = '';
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
        if (isset($this->error['car_model'])) {
            $error_msg['car_model'] = alert_box($this->error['car_model'], 'error');
        } else {
            $error_msg['car_model'] = '';
        }
        if (isset($this->error['transmisi'])) {
            $error_msg['transmisi'] = alert_box($this->error['transmisi'], 'error');
        } else {
            $error_msg['transmisi'] = '';
        }
        if (isset($this->error['car_cc'])) {
            $error_msg['car_cc'] = alert_box($this->error['car_cc'], 'error');
        } else {
            $error_msg['car_cc'] = '';
        }
        if (isset($this->error['stock'])) {
            $error_msg['stock'] = alert_box($this->error['stock'], 'error');
        } else {
            $error_msg['stock'] = '';
        }
        if (isset($this->error['car_price'])) {
            $error_msg['car_price'] = alert_box($this->error['car_price'], 'error');
        } else {
            $error_msg['car_price'] = '';
        }
        if (isset($this->error['car_engine'])) {
            $error_msg['car_engine'] = alert_box($this->error['car_engine'], 'error');
        } else {
            $error_msg['car_engine'] = '';
        }
        if (isset($this->error['on_sale'])) {
            $error_msg['on_sale'] = alert_box($this->error['on_sale'], 'error');
        } else {
            $error_msg['on_sale'] = '';
        }

        // set value
        if ($this->input->post('msc_code') != '') {
            $post['msc_code'] = $this->input->post('msc_code');
        } elseif ((int)$id > 0) {
            $post['msc_code'] = $car_info['msc_code'];
        } else {
            $post['msc_code'] = '';
        }
        if ($this->input->post('stock') != '') {
            $post['stock'] = $this->input->post('stock');
        } elseif ((int)$id > 0) {
            $post['stock'] = $car_info['stock'];
        } else {
            $post['stock'] = '';
        }
        if ($this->input->post('car_price') != '') {
            $post['car_price'] = $this->input->post('car_price');
        } elseif ((int)$id > 0) {
            $post['car_price'] = $car_info['car_price'];
        } else {
            $post['car_price'] = '';
        }
        if ($this->input->post('notes') != '') {
            $post['notes'] = $this->input->post('notes');
        } elseif ((int)$id > 0) {
            $post['notes'] = $car_info['notes'];
        } else {
            $post['notes'] = '';
        }

        if ($this->input->post('car_brand') != '') {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'",
                $this->input->post('car_brand'), '--- Choice Brand ---', 'id_brands');

        } elseif ((int)$id > 0) {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1' ",
                $car_info['car_brand'], '--- Choice Brand ---', 'id_brands');
        } else {
            $post['brandlist'] = selectlist('car_brands', 'id_brands', 'brands', "ref_publish = '1'", null,
                '--- Choice Brand ---', 'id_brands');
        }

        if ($this->input->post('car_type') != '') {
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types',
                "ref_publish = '1' and id_brands = '" . $this->input->post('car_brand') . "'",
                $this->input->post('car_type'), '--- Choice Product ---', 'id_car_types');

        } elseif ((int)$id > 0) {
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types',
                "id_brands = '" . $car_info['car_brand'] . "'", $car_info['car_type'], '--- Choice Product ---',
                'id_car_types');

        } else {
            $post['typelist'] = selectlist('car_types', 'id_car_types', 'types', "ref_publish = '3'", null,
                '--- Choice Product ---', 'id_car_types');
        }

        if ($this->input->post('serieslist') != '') {
            $post['serieslist'] = selectlist('car_series', 'id_car_series', 'series',
                "ref_publish = '1' and id_type = '" . $this->input->post('serieslist') . "'",
                $this->input->post('serieslist'), '--- Choice Type ---', 'id_car_series');

        } elseif ((int)$id > 0) {
            $post['serieslist'] = selectlist('car_series', 'id_car_series', 'series',
                "ref_publish = '1' and id_type in ('" . $car_info['car_type'] . "')", $car_info['car_series'],
                '--- Choice Type ---', 'id_car_series');

        } else {
            $post['serieslist'] = selectlist('car_series', 'id_car_series', 'series', "ref_publish = '3'", null,
                '--- Choice Type ---', 'id_car_series');
        }

        if ($this->input->post('car_model') != '') {
            $post['modellist'] = selectlist('car_model', 'id_car_model', 'model', null, $this->input->post('car_model'),
                '--- Choice Varian ---', 'id_car_model');

        } elseif ((int)$id > 0) {
            $post['modellist'] = selectlist('car_model', 'id_car_model', 'model', null, $car_info['car_model'],
                '--- Choice Varian ---', 'id_car_model');
        } else {
            $post['modellist'] = selectlist('car_model', 'id_car_model', 'model', "id_car_model = '0'", null,
                '--- Choice Varian ---', 'id_car_model');
        }

        if ($this->input->post('car_cc') != '') {
            $post['cclist'] = selectlist('car_cc', 'id_cc', 'cc', null, $this->input->post('car_cc'),
                '--- Choice Car cc ---', 'id_cc');

        } elseif ((int)$id > 0) {
            $post['cclist'] = selectlist('car_cc', 'id_cc', 'cc', null, $car_info['car_cc'], '--- Choice Car cc ---',
                'id_cc');
        } else {
            $post['cclist'] = selectlist('car_cc', 'id_cc', 'cc', null, null, '--- Choice Car cc ---', 'id_cc');
        }

        if ($this->input->post('transmisi') != '') {
            $post['translist'] = print_car_trans($this->input->post('transmisi'));

        } elseif ((int)$id > 0) {
            $post['translist'] = print_car_trans($car_info['transmisi']);
        } else {
            $post['translist'] = print_car_trans();
        }

        if ($this->input->post('car_engine') != '') {
            $post['enginelist'] = selectlist('car_engines', 'id_car_engine', 'engine', null,
                $this->input->post('car_engine'), '--- Choice Car Engine ---', 'id_car_engine');

        } elseif ((int)$id > 0) {
            $post['enginelist'] = selectlist('car_engines', 'id_car_engine', 'engine', null, $car_info['car_engine'],
                '--- Choice Car Engine ---', 'id_car_engine');
        } else {
            $post['enginelist'] = selectlist('car_engines', 'id_car_engine', 'engine', null, null,
                '--- Choice Car Engine ---', 'id_car_engine');
        }

        if ($this->input->post('on_sale') != '') {
            $post['on_sale'] = print_car_trans($this->input->post('on_sale'), 'Choice On Sale', 'jdi_car', 'on_sale');

        } elseif ((int)$id > 0) {
            $post['on_sale'] = print_car_trans($car_info['on_sale'], 'Choice On Sale', 'jdi_car', 'on_sale');
        } else {
            $post['on_sale'] = print_car_trans('', 'Choice On Sale', 'jdi_car', 'on_sale');
        }

        $refcolorlst = selectlist('ref_color', 'id_ref_color', 'color', null, null, '--- Choice Color ---',
            'id_ref_color');
        $statuslst = print_car_trans('', $label = '--- Choice Status Sale ---', 'jdi_car_colors', 'status');

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
            'carlist' => $cars,
            'clist' => $colors,
            'blist' => $branch,
            'refcolorlst' => $refcolorlst,
            'statuslst' => $statuslst,
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

        // if ($post['msc_code'] == '')
        // {
        // $this->error['msc_code'] = 'Please insert Car name.<br/>';
        // }
        // else
        // {
        // if(!$this->Cars_model->CheckExistsCarName($post['msc_code'],$id))
        // {
        // $this->error['msc_code'] = 'Msc Code already exists, please input different Msc Code.<br/>';
        // }
        // else
        // {
        // if (utf8_strlen($post['msc_code']) < 5)
        // {
        // $this->error['msc_code'] = 'Msc Code length must be at least 5 character(s).<br/>';
        // }
        // }
        // }	

//        if ($post['car_price'] == '') {
//            $this->error['car_price'] = 'Please insert Car Price.<br/>';
//        } else {
//            // $car_price = (int)$post['car_price'];
//
//
//        }

        if ($post['car_brand'] == '' || $post['car_brand'] == '0') {
            $this->error['car_brand'] = 'Please select Brand.<br/>';
        }
        if ($post['car_types'] == '' || $post['car_types'] == '0') {
            $this->error['car_type'] = 'Please select Product.<br/>';
        }
        if ($post['car_series'] == '' || $post['car_series'] == '0') {
            $this->error['car_series'] = 'Please select Type.<br/>';
        }
        if ($post['car_model'] == '' || $post['car_model'] == '0') {
            $this->error['car_model'] = 'Please select Varian.<br/>';
        }
        if ($post['car_cc'] == '' || $post['car_cc'] == '0') {
            $this->error['car_cc'] = 'Please select Car cc.<br/>';
        }
        if ($post['transmisi'] == '' || $post['transmisi'] == '0') {
            $this->error['transmisi'] = 'Please select Transmission.<br/>';
        }
        if ($post['car_engine'] == '' || $post['car_engine'] == '0') {
            $this->error['car_engine'] = 'Please select Car Engine.<br/>';
        }
        if ($post['on_sale'] == '' || $post['on_sale'] == '0') {
            $this->error['on_sale'] = 'Please select On Sale Field.<br/>';
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
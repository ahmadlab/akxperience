<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
error_reporting(-1);

/**
 * Car Accessories Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Accessories Management
 *********************************************/
class Accessories extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $template;
    private $path_thumb;
    private $path_uri;
    private $title;
    private $id_menu_admin;
    private $is_superadmin;
    private $max_thumb_width;
    private $max_thumb_height;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
        $this->ctrl = 'accessories';
        $this->template = getAdminFolder() . '/modules/accessories';
        $this->path_uri = getAdminFolder() . '/accessories';
        $this->path = site_url(getAdminFolder() . '/accessories');
        $this->title = get_admin_menu_title('accessories');
        $this->id_menu_admin = get_admin_menu_id('accessories');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_thumb_height = 105;
        $this->max_thumb_width = 148;
        $this->max_asc_height_hxx = 478;
        $this->max_asc_height_hx = 320;
        $this->max_asc_height_h = 213;
        $this->max_asc_width_hxx = 890;
        $this->max_asc_width_hx = 593;
        $this->max_asc_width_h = 395;
        $this->path_thumb = './uploads/accessories/';
        $this->load->model(getAdminFolder() . '/Inventory_model', 'Inventory_model');
        $this->load->model(getAdminFolder() . '/Vendor_model', 'Vendor_model');
        $this->load->model(getAdminFolder() . '/acc_price_model', 'Acc_price_model');
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
        $s_accessories = $this->uri->segment(5);
        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/t/a/';
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
        $list_data = array();
        $wtype = $wseries = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

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
        if (strlen($s_accessories) > 1) {
            $s_accessories = substr($s_accessories, 1);

        } else {
            $s_accessories = "";

        }

//		$l_accessories	= selectlist('ref_accessories','id_ref_accessories','ref_accessories',null,$s_type,'--- Chooice Type of Accessories ---');
        $total_records = $this->Inventory_model->TotalAccessories(myUrlDecode($s_type), myUrlDecode($s_accessories));

        if ($s_type) {
            $dis_urut = "none";
            $path = str_replace("/t/", "/t" . $s_type . "/", $path);
            ($search_query == '' ? $search_query .= $s_type : $search_query .= ' + ' . $s_type);
        }

        if ($s_accessories) {
            $dis_urut = "none";
            $path = str_replace("/s/", "/s" . $s_accessories . "/", $path);
            ($search_query == '' ? $search_query .= $s_accessories : $search_query .= ' + ' . $s_accessories);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Inventory_model->GetAllAccessories(myUrlDecode($s_type), myUrlDecode($s_accessories), $lmt,
            $per_page);

        foreach ($query->result_array() as $csaccessories) {
            $no++;
            $id = $csaccessories['id_car_accessories'];
            $ref_publish = ($csaccessories['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $type = db_get_one('car_types', 'types', "id_car_types = '" . $csaccessories['id_type'] . "'");
            $vendor = db_get_one('ref_vendor', 'name', "id = '" . $csaccessories['id_vendor'] . "'");
            $category = db_get_one('ref_accessories', 'name', "id = '" . $csaccessories['id_ref_accessories'] . "'");
            $accessories = $csaccessories['accessories'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'accessories' => $accessories,
                'type' => $type,
                'vendor' => $vendor,
                'price' => $csaccessories['price'],
                'category' => $category,
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
//			'l_accessories'		=> $l_accessories,
            's_accessories' => $s_accessories,
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
        $this->parser->parse($template . '/list_accessories.html', $data);
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
                $return = $this->Inventory_model->ChangePublishAccessories($id);
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
        $s_accessories_type = myUrlEncode(trim($this->input->post('s_accessories_type')));
        $s_accessories = myUrlEncode(trim($this->input->post('s_accessories')));
        redirect($this->path . '/main/t' . $s_accessories_type . '/s' . $s_accessories);
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
                    $this->Inventory_model->DeleteCarAccessories($id);
                    $this->Acc_price_model->delete($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Car Accessories ID : ' . $id;
                    $log_desc = 'Delete Car Accessories ID : ' . $id . ';';
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
                $this->Inventory_model->DeleteAccessoriesThumbByID($this->input->post('id'));
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

            $post = purify($this->input->post());
            $ico = $_FILES['thumb'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'accessories' => $post['accessories'],
                'id_ref_accessories' => $post['reflist'],
                'id_type' => $post['car_type'],
                'id_vendor' => $post['id_vendor'],
//                'price'					=> $post['price'],

            );

            // insert data
            $id = $this->Inventory_model->InsertAccessories($data_post);
            if ($id) {
                $data_post = array();
                $branch = $this->db->select('id_ref_location, location')
                    ->from('ref_location')
                    ->where('company_code', 'AK')->get()->result_array();

                foreach ($branch as $v) {
                    $data_post[] = array(
                        'acc_id' => $id,
                        'branch_id' => $v['id_ref_location'],
                        'vendor_id' => $post['id_vendor'],
                        'price' => $post['branch_' . $v['id_ref_location']],
                    );
                }

                $this->Acc_price_model->add($data_post);
            }

            if ($ico['tmp_name'] != "") {
                $config['upload_path'] = $this->path_thumb;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['file_name'] = 'accessories_' . $id . '_' . date('YmdHi');
                $config['max_size'] = 0;
                $config['overwrite'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('thumb')) {
                    $error = $this->upload->display_errors();
                    die(var_dump($error));
                } else {
                    $data = $this->upload->data();
                    $data_file = array('thumb' => $data['file_name']);
                    $this->Inventory_model->UpdateAccessories($data_file, $id);
                }
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Accessories:' . $post['accessories'] . '; Category :' . $post['ref_accessories'] . ';Type : ' . $post['type_accessories'];
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
                'accessories' => $post['accessories'],
                'id_ref_accessories' => $post['reflist'],
                'id_type' => $post['car_type'],
                'id_vendor' => $post['id_vendor'],
                'price' => $post['price'],

            );
            $this->Inventory_model->UpdateAccessories($data_post, $id);

            $data_post = array();
            $branch = $this->Acc_price_model->populateBranch();

            foreach ($branch as $v) {
                $data_post[] = array(
                    'acc_id' => $id,
                    'branch_id' => $v['id_ref_location'],
                    'vendor_id' => $post['id_vendor'],
                    'price' => $post['branch_' . $v['id_ref_location']],
                );
            }

            $this->Acc_price_model->add($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $config['upload_path'] = $this->path_thumb;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['file_name'] = 'accessories_' . $id . '_' . date('YmdHi');
                $config['max_size'] = 0;
                $config['overwrite'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('thumb')) {
                    $error = $this->upload->display_errors();
                    die(var_dump($error));
                } else {
                    $data = $this->upload->data();
                    $data_file = array('thumb' => $data['file_name']);
                    $this->Inventory_model->UpdateAccessories($data_file, $id);
                }
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Accessories:' . $post['accessories'] . '; Category :' . $post['ref_accessories'] . ';Type : ' . $post['type_accessories'];
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

        $branch = $this->db->select('id_ref_location, location')
            ->from('jdi_ref_location')
            ->where('company_code', 'AK')->get()->result_array();

        foreach ($branch as $k => $v) {
            $branch[$k]['acc_price'] = '';
        }

        if ($id) {
            $query = $this->Inventory_model->GetAccessoriesById($id);
            if ($query->num_rows() > 0) {
                $info = $query->row_array();
                if ($info['thumb'] != '' && file_exists($this->path_thumb . $info['thumb'])) {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/accessories/' . $info['thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $info['id_car_accessories'] . '"style="cursor:pointer;" >Delete Picture</a>
                    </div>';
                }

                $acc_price = $this->db->from('jdi_acc_price')
                    ->where('acc_id', $id)->get();
                $acc_price = $acc_price->result_array();

                foreach ($branch as $k => $v) {
                    foreach ($acc_price as $val) {
                        if ($v['id_ref_location'] == $val['branch_id']) {
                            $branch[$k]['acc_price'] = $val['price'];
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
        if (isset($this->error['accessories'])) {
            $error_msg['accessories'] = alert_box($this->error['accessories'], 'error');
        } else {
            $error_msg['accessories'] = '';
        }
        if (isset($this->error['ref_accessories'])) {
            $error_msg['ref_accessories'] = alert_box($this->error['ref_accessories'], 'error');
        } else {
            $error_msg['ref_accessories'] = '';
        }
        if (isset($this->error['type_accessories'])) {
            $error_msg['type_accessories'] = alert_box($this->error['type_accessories'], 'error');
        } else {
            $error_msg['type_accessories'] = '';
        }
        if (isset($this->error['vendor'])) {
            $error_msg['vendor'] = alert_box($this->error['vendor'], 'error');
        } else {
            $error_msg['vendor'] = '';
        }
        if (isset($this->error['price'])) {
            $error_msg['price'] = alert_box($this->error['price'], 'error');
        } else {
            $error_msg['price'] = '';
        }
        if (isset($this->error['thumb'])) {
            $error_msg['thumb'] = alert_box($this->error['thumb'], 'error');
        } else {
            $error_msg['thumb'] = '';
        }


        // set value
        if ($this->input->post('accessories') != '') {
            $post['accessories'] = $this->input->post('accessories');
        } elseif ((int)$id > 0) {
            $post['accessories'] = $info['accessories'];
        } else {
            $post['accessories'] = '';
        }
        if ($this->input->post('price') != '') {
            $post['price'] = $this->input->post('price');
        } elseif ((int)$id > 0) {
            $post['price'] = $info['price'];
        } else {
            $post['price'] = '';
        }

        if ((int)$id > 0) {
            $post['reflist'] = selectlist('ref_accessories', 'id', 'name', "is_published = '1'",
                $info['id_ref_accessories'], '--- Choose Accessory ---', 'id');
            $a = $this->db->query("SELECT a.id_car_types, concat(b.brands,' ',a.types) as name
								   FROM jdi_car_types a, jdi_car_brands b 
								   WHERE a.ref_publish = '1' AND b.ref_publish AND a.id_brands = b.id_brands
								   ORDER BY a.id_car_types")->result_array();
            $post['typelist'] = $this->lists($a, 'id_car_types', 'name', $info['id_type'], '--- Pilih Type ---');
            $vendors = $this->Vendor_model->showAll()->result_array();
            $post['vendor_list'] = $this->lists($vendors, 'id', 'name', $info['id_vendor'], '--- Pilih Vendor ---');
        } else {
            $post['reflist'] = selectlist('ref_accessories', 'id', 'name', "is_published = '1'", null,
                '--- Choose Accessory ---', 'id');

            $a = $this->db->query("SELECT a.id_car_types, concat(b.brands,' ',a.types) as name
								   FROM jdi_car_types a, jdi_car_brands b 
								   WHERE a.ref_publish = '1' AND b.ref_publish AND a.id_brands = b.id_brands
								   ORDER BY a.id_car_types")->result_array();
            $post['typelist'] = $this->lists($a, 'id_car_types', 'name', null, '--- Pilih Type ---');
            $vendors = $this->Vendor_model->showAll()->result_array();
            $post['vendor_list'] = $this->lists($vendors, 'id', 'name', null, '--- Pilih Vendor ---');
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
            'blist' => $branch,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,
        );
        $this->parser->parse($template . '/list_accessories_form.html', $data);
        $this->global_libs->print_footer();
    }

    public function lists($data, $id, $name, $selected = '', $title = '------------')
    {
        $opt = "<option value=''>$title</option>";
        foreach ($data as $l) {
            $terpilih = ($selected == $l[$id]) ? 'selected' : '';
            $opt .= "<option $terpilih value='$l[$id]'> $l[$name]</option>";
        }
        return $opt;
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

//		if($post['price'] == '') {
//			$this->error['price'] = 'Please insert Price.<br/>';
//
//        }
//		else
//		{
//			// if (!validate_price($post['price'])) {
//				// $this->error['price'] = 'Please try again with the right format Price.<br/>';
//			// }
//		}

        if ($post['car_type'] == '' || $post['car_type'] == '0') {
            $this->error['car_type'] = 'Please select Car Type';
        }

        if ($post['reflist'] == '' || $post['reflist'] == '0') {
            $this->error['reflist'] = 'Please select accesory';
        }


        // $info = getimagesize($ico['tmp_name']);
        // if ($ico['tmp_name'] != "") {
        // if($info[0] >= $this->max_car_width_hxx && $info[1] >= $this->max_car_height_hxx && $info['mime'] == 'image/png') {

        // }
        // }


        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
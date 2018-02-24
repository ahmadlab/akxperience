<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Car Spare Part Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Spare Part Management
 *********************************************/
class Spare_part extends CI_Controller
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
        $this->ctrl = 'spare_part';
        $this->template = getAdminFolder() . '/modules/spare_part';
        $this->path_uri = getAdminFolder() . '/spare_part';
        $this->path = site_url(getAdminFolder() . '/spare_part');
        $this->title = get_admin_menu_title('spare_part');
        $this->id_menu_admin = get_admin_menu_id('spare_part');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_sp_height = 105;
        $this->max_sp_width = 148;
        $this->path_sparepart = './uploads/spare_part/';
        $this->load->model(getAdminFolder() . '/Inventory_model', 'Inventory_model');
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
        $s_sparepart = $this->uri->segment(5);
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
        if (strlen($s_sparepart) > 1) {
            $s_sparepart = substr($s_sparepart, 1);

        } else {
            $s_sparepart = "";

        }

        $l_sparepart = selectlist('ref_spare_part', 'id_ref_spare_part', 'ref_spare_part', null, $s_type,
            '--- Chooice Type of Spare Part ---');
        $total_records = $this->Inventory_model->TotalSparePart(myUrlDecode($s_type), myUrlDecode($s_sparepart));

        if ($s_type) {
            $dis_urut = "none";
            $path = str_replace("/t/", "/t" . $s_type . "/", $path);
        }

        if ($s_sparepart) {
            $dis_urut = "none";
            $path = str_replace("/s/", "/s" . $s_sparepart . "/", $path);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Inventory_model->GetAllSparePart(myUrlDecode($s_type), myUrlDecode($s_sparepart), $lmt,
            $per_page);

        foreach ($query->result_array() as $csparepart) {
            $no++;
            $id = $csparepart['id_car_spare_part'];
            $ref_publish = ($csparepart['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $type = db_get_one('car_types', 'types', "id_car_types = '" . $csparepart['id_type'] . "'");
            $category = db_get_one('ref_spare_part', 'ref_spare_part',
                "id_ref_spare_part = '" . $csparepart['id_ref_spare_part'] . "'");
            $sparepart = $csparepart['spare_part'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_cars_arr[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'spare_part' => $sparepart,
                'type' => $type,
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

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            'l_sparepart' => $l_sparepart,
            's_sparepart' => $s_sparepart,
            's_type' => $s_type,
            'list_cars' => $list_cars_arr,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
        );
        $this->parser->parse($template . '/list_spare_part.html', $data);
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
                $return = $this->Inventory_model->ChangePublishSparePart($id);
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
        $s_sparepart_type = myUrlEncode(trim($this->input->post('s_sparepart_type')));
        $s_sparepart = myUrlEncode(trim($this->input->post('s_sparepart')));
        redirect($this->path . '/main/t' . $s_sparepart_type . '/s' . $s_sparepart);
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
                    $this->Car_model->DeleteCarSparePart($id);
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
                $this->Car_model->DeleteSparePartThumbByID($this->input->post('id'));
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
                'spare_part' => $post['spare_part'],
                'id_ref_spare_part' => $post['categlist'],
                'id_type' => $post['car_type'],
                'price' => $post['price'],

            );

            // insert data
            $id_sparepart = $this->Inventory_model->InsertSparePart($data_post);


            if ($ico['tmp_name'] != "") {
                $filename = 'spare_part_' . $id_sparepart . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_sparepart, $filename,
                    $this->max_sp_width, $this->max_sp_height);

                $data_pic_content = array('thumb' => $content_to_db);

                // update to database
                $this->Inventory_model->UpdateSparePart($data_pic_content, $id_sparepart);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_sparepart;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_sparepart . '; Spare Part:' . $post['spare_part'] . '; Category :' . $post['ref_spare_part'] . ';Type : ' . $post['type_spare_part'];
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
                'spare_part' => $post['spare_part'],
                'id_ref_spare_part' => $post['categlist'],
                'id_type' => $post['car_type'],
                'price' => $post['price'],
            );
            $this->Inventory_model->UpdateSparePart($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $filename = 'spare_part_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_sparepart, $filename,
                    $this->max_sp_width, $this->max_sp_height);

                $data_pic_content = array('thumb' => $content_to_db);

                // update to database
                $this->Inventory_model->UpdateSparePart($data_pic_content, $id);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Spare Part:' . $post['spare_part'] . '; Category :' . $post['ref_spare_part'] . ';Type : ' . $post['type_spare_part'];
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
            $query = $this->Inventory_model->GetSparePartById($id);
            if ($query->num_rows() > 0) {
                $sparepart_info = $query->row_array();
                if ($sparepart_info['thumb'] != '' && file_exists($this->path_sparepart . $sparepart_info['thumb'])) {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/spare_part/' . $sparepart_info['thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $sparepart_info['id_car_spare_part'] . '"style="cursor:pointer;" >Delete Picture</a>
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
        if (isset($this->error['spare_part'])) {
            $error_msg['spare_part'] = alert_box($this->error['spare_part'], 'error');
        } else {
            $error_msg['spare_part'] = '';
        }
        if (isset($this->error['price'])) {
            $error_msg['price'] = alert_box($this->error['price'], 'error');
        } else {
            $error_msg['price'] = '';
        }


        // set value
        if ($this->input->post('spare_part') != '') {
            $post['spare_part'] = $this->input->post('spare_part');
        } elseif ((int)$id > 0) {
            $post['spare_part'] = $sparepart_info['spare_part'];
        } else {
            $post['spare_part'] = '';
        }
        if ($this->input->post('price') != '') {
            $post['price'] = $this->input->post('price');
        } elseif ((int)$id > 0) {
            $post['price'] = $sparepart_info['price'];
        } else {
            $post['price'] = '';
        }

        if ((int)$id > 0) {
            $post['categlist'] = selectlist('ref_spare_part', 'id_ref_spare_part', 'ref_spare_part',
                "ref_publish = '1'", $sparepart_info['id_ref_spare_part'], '--- Pilih Category ---',
                'id_ref_spare_part');
            $a = $this->db->query("SELECT a.id_car_types, concat(b.brands,' ',a.types) as name
								   FROM jdi_car_types a, jdi_car_brands b 
								   WHERE a.ref_publish = '1' AND b.ref_publish AND a.id_brands = b.id_brands
								   ORDER BY a.id_car_types")->result_array();
            $post['typelist'] = $this->lists($a, 'id_car_types', 'name', $sparepart_info['id_type'],
                '--- Pilih Type ---');
        } else {
            $post['categlist'] = selectlist('ref_spare_part', 'id_ref_spare_part', 'ref_spare_part',
                "ref_publish = '1'", null, '--- Pilih Category ---', 'id_ref_spare_part');

            $a = $this->db->query("SELECT a.id_car_types, concat(b.brands,' ',a.types) as name
								   FROM jdi_car_types a, jdi_car_brands b 
								   WHERE a.ref_publish = '1' AND b.ref_publish AND a.id_brands = b.id_brands
								   ORDER BY a.id_car_types")->result_array();
            $post['typelist'] = $this->lists($a, 'id_car_types', 'name', null, '--- Pilih Type ---');
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
        );
        $this->parser->parse($template . '/list_sparepart_form.html', $data);
        $this->global_libs->print_footer();
    }

    public function lists($data, $id, $name, $selected = '', $title = '------------')
    {
        // $list = $CI->db->select("$id , $name")->get_where($tbl,$where)->result_array();
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


        if ($post['spare_part'] == '') {
            $this->error['spare_part'] = 'Please insert Spare Part.<br/>';
        } else {
            if (!$this->Inventory_model->CheckExistsSparePart($post['spare_part'], $id)) {
                $this->error['spare_part'] = 'Spare Part already exists, please input different Spare Part.<br/>';
            } else {
                if (utf8_strlen($post['spare_part']) < 5) {
                    $this->error['spare_part'] = 'Spare Part length must be at least 5 character(s).<br/>';
                }
            }
        }

        if ($post['price'] == '') {
            $this->error['price'] = 'Please insert Price.<br/>';

        } else {
            // if (!validate_price($post['price'])) {
            // $this->error['price'] = 'Please try again with the right format Price.<br/>';
            // }
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
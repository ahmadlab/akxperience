<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Ref Spare Parts Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Ref Spare Parts Management
 *********************************************/
class Ref_sparepart extends CI_Controller
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
    private $max_thumb_width;
    private $max_thumb_height;
    private $path_thumb;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
        $this->ctrl = 'ref_sparepart';
        $this->template = getAdminFolder() . '/modules/ref_sparepart';
        $this->path_uri = getAdminFolder() . '/ref_sparepart';
        $this->path = site_url(getAdminFolder() . '/ref_sparepart');
        $this->title = get_admin_menu_title('ref_sparepart');
        $this->id_menu_admin = get_admin_menu_id('ref_sparepart');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_thumb_height = 105;
        $this->max_thumb_width = 148;
        $this->path_thumb = './uploads/ref_sparepart/';
        $this->load->model(getAdminFolder() . '/Ref_inventory_model', 'Ref_inventory_model');
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
        $s_sparepart = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/a/';
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
        $list_data = array();
        $wtype = $wseries = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_sparepart) > 1) {
            $s_sparepart = substr($s_sparepart, 1);

        } else {
            $s_sparepart = "";

        }
        $total_records = $this->Ref_inventory_model->TotalRefSparePart(myUrlDecode($s_sparepart));


        if ($s_sparepart) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_sparepart . "/", $path);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Ref_inventory_model->GetAllRefSparePart(myUrlDecode($s_sparepart), $lmt, $per_page);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $path = $this->path_thumb . $buffs['ref_spare_part'];
            $id = $buffs['id_ref_spare_part'];
            $ref_publish = ($buffs['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $sparepart = $buffs['ref_spare_part'];
            $thumb = (file_exists($path)) ? "<img src='" . base_url() . 'uploads/' . $this->ctrl . '/' . $buffs['thumb_ref'] . "'/>" : 'img not found';

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'sparepart' => $sparepart,
                'edit_href' => $edit_href,
                'thumb' => $thumb
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
            's_sparepart' => $s_sparepart,
            'list_data' => $list_data,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
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
                $return = $this->Ref_inventory_model->ChangePublishRefSparePart($id);
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
        $s_sparepart = myUrlEncode(trim($this->input->post('s_sparepart')));
        redirect($this->path . '/main/a' . $s_sparepart);
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
                    $this->Ref_inventory_model->DeleteRefSparePart($id);
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
                'ref_spare_part' => $post['ref_spare_part'],

            );

            // insert data
            $id = $this->Ref_inventory_model->InsertRefSparePart($data_post);


            if ($ico['tmp_name'] != "") {
                $filename = 'ref_spare_part_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->max_thumb_width, $this->max_thumb_height);

                $data_pic_content = array('thumb_ref' => $content_to_db);

                // update to database
                $this->Ref_inventory_model->UpdateRefSparePart($data_pic_content, $id);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Ref Spare Part :' . $post['ref_spare_part'];
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
                'ref_spare_part' => $post['ref_spare_part'],

            );
            $this->Ref_inventory_model->UpdateRefSparePart($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $filename = 'ref_spare_part_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->max_thumb_width, $this->max_thumb_height);

                $data_pic_content = array('thumb_ref' => $content_to_db);

                // update to database
                $this->Ref_inventory_model->UpdateRefSparePart($data_pic_content, $id);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Ref Spare Part :' . $post['ref_spare_part'];
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
            $query = $this->Ref_inventory_model->GetRefSparePartById($id);
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
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['ref_spare_part'])) {
            $error_msg['ref_spare_part'] = alert_box($this->error['ref_spare_part'], 'error');
        } else {
            $error_msg['ref_spare_part'] = '';
        }


        // set value
        if ($this->input->post('ref_spare_part') != '') {
            $post['ref_spare_part'] = $this->input->post('ref_spare_part');
        } elseif ((int)$id > 0) {
            $post['ref_spare_part'] = $info['ref_spare_part'];
        } else {
            $post['ref_spare_part'] = '';
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


        if ($post['ref_spare_part'] == '') {
            $this->error['ref_spare_part'] = 'Please insert category Spare Part.<br/>';
        } else {
            if (!$this->Ref_inventory_model->CheckExistsRefSparePart($post['ref_spare_part'], $id)) {
                $this->error['ref_spare_part'] = 'Spare Part category name already exists, please input different name.<br/>';
            } else {
                if (utf8_strlen($post['ref_spare_part']) < 5) {
                    $this->error['ref_spare_part'] = 'Spare Part category name length must be at least 5 character(s).<br/>';
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


/* End of file ref_sparepart.php */
/* Location: ./application/controllers/admpage/ref_sparepart.php */
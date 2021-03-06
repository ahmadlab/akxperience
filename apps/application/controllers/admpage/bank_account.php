<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Ref Bank Account Supported Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Ref Bank Account Supported Management
 *********************************************/
class Bank_account extends CI_Controller
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
        $this->ctrl = 'bank_account';
        $this->template = getAdminFolder() . '/modules/bank_account';
        $this->path_uri = getAdminFolder() . '/bank_account';
        $this->path = site_url(getAdminFolder() . '/bank_account');
        $this->title = get_admin_menu_title('bank_account');
        $this->id_menu_admin = get_admin_menu_id('bank_account');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_thumb_height = 100;
        $this->max_thumb_width = 100;
        $this->path_thumb = './uploads/bank_account/';
        $this->load->model(getAdminFolder() . '/simulation_model', 'Simulation_model');
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
        $s_bank = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/b/';
        $list_admin_arr = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $path_uri = $this->path_uri;
        $template = $this->template;
        $breadcrumbs = array();
        $add_btn = site_url($path_uri . '/add');
        // $is_superadmin			= $this->is_superadmin;
        $list_data = array();
        $wtype = $wseries = '';
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);
        $search_query = '';

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_bank) > 1) {
            $s_bank = substr($s_bank, 1);

        } else {
            $s_bank = "";

        }

        $total_records = $this->Simulation_model->TotalBankAccount(myUrlDecode($s_bank));

        if ($s_bank) {
            $dis_urut = "none";
            $path = str_replace("/d/", "/d" . $s_bank . "/", $path);
            ($search_query == '' ? $search_query .= $s_bank : $search_query .= ' + ' . $s_bank);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Simulation_model->GetAllBankAccount(myUrlDecode($s_bank), $lmt, $per_page);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id_parents = 0;
            $path = $this->path_thumb . $buffs['bank_thumb'];
            $id = $buffs['id_ref_bank'];
            $ref_publish = ($buffs['ref_publish'] == '1') ? 'Published' : 'Not Published';
            $bank = $buffs['bank_name'];
            $thumb = ($buffs['bank_thumb'] != '' && file_exists($path)) ? "<img src='" . base_url() . 'uploads/' . $this->ctrl . '/' . $buffs['bank_thumb'] . "' width='90' height='35' />" : 'img not found';

            $sort = $buffs['sort'];

            $last_sort = $this->Simulation_model->GetRefSortMax();
            $first_sort = $this->Simulation_model->GetRefSortMin();

            // get sorting
            $sort_up = $sort_down = '';
            if (myUrlDecode($s_bank) == "") {
                if ($total_records == 1) {
                    $sort_up = $sort_down = '';
                } elseif ($last_sort == 1) {
                    $sort_up = $sort_down = '';
                } else {
                    if ($sort == 1 && $pg == 1) {
                        $sort_down = sort_arrow($id, $sort, 0, $path_app, 'down');
                    } elseif ($sort == $first_sort) {
                        $sort_down = sort_arrow($id, $sort, 0, $path_app, 'down');
                    } elseif ($sort == $last_sort) {
                        $sort_up = sort_arrow($id, $sort, 0, $path_app, 'up');
                    } elseif ($no == $total_records) {
                        $sort_up = sort_arrow($id, $sort, 0, $path_app, 'up');
                    } else {
                        $sort_up = sort_arrow($id, $sort, 0, $path_app, 'up');
                        $sort_down = sort_arrow($id, $sort, 0, $path_app, 'down');
                    }
                }
            }

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'ref_publish' => $ref_publish,
                'thumb' => $thumb,
                'bank' => $bank,
                'edit_href' => $edit_href,
                'urut' => $sort,
                'sort_down' => $sort_down,
                'sort_up' => $sort_up
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
            's_bank' => $s_bank,
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
        $this->parser->parse($template . '/list_' . $file_app . '.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * search post s_name and s_email
     */
    public function search()
    {
        auth_admin();
        $s_bank = myUrlEncode(trim($this->input->post('s_bank')));
        redirect($this->path . '/main/d' . $s_bank);
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
                    $this->Simulation_model->DeleteBankAccount($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete ' . $this->title . ' ID : ' . $id;
                    $log_desc = 'Delete ' . $this->title . ' ID : ' . $id . ';';
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
                $this->Simulation_model->DeleteBankThumbByID($this->input->post('id'));
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
     * change sort
     */
    public function change_sort()
    {
        auth_admin();

        if ($this->input->post('id') != '' && $this->input->post('urut') != '' && $this->input->post('direction') != '') {
            $id = $this->input->post('id');
            $urut = $this->input->post('urut');
            $direction = $this->input->post('direction');

            if (((ctype_digit($id)) && ($id > 0)) &&
                ((ctype_digit($urut)) && ($urut > 0)) &&
                ($direction != '')
            ) {
                $this->Simulation_model->ChangeSort($id, $urut, $direction);

                #insert to log
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Change Sort ' . $this->title . ' ID : ' . $id;
                $log_desc = 'Change Sort ' . $this->title . ' ID : ' . $id . ';';
                $data = array(
                    'id_user' => $log_id_user,
                    'id_group' => $log_id_group,
                    'action' => $log_action,
                    'desc' => $log_desc,
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data);
                $this->session->set_flashdata('success_msg', $this->title . ' has been sort ' . $direction . '.');
            } else {
                $this->session->set_flashdata('error_msg', 'Change sort failed. Please try again.');
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Change sort failed. Please try again.');
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

            $max_sort = $this->Simulation_model->GetRefSortMax();
            $sort = $max_sort + 1;

            $data_post = array(
                'bank_name' => $post['bank'],
                'sort' => $sort,
            );


            $id = $this->Simulation_model->InsertBankAccount($data_post);

            if ($ico['tmp_name'] != "") {
                $filename = 'bank_' . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->max_thumb_width, $this->max_thumb_height);
                trans_image_resize_to_folder($ico, $this->path_thumb, str_replace(' ', '_', $filename . '_HDPI'),
                    $this->max_thumb_width, $this->max_thumb_height);
                trans_image_resize_to_folder($ico, $this->path_thumb, str_replace(' ', '_', $filename . '_XHDPI'),
                    $this->max_thumb_width, $this->max_thumb_height);

                $data_pic_content = array('bank_thumb' => $content_to_db);

                // update to database
                $this->Simulation_model->UpdateBankAccount($data_pic_content, $id);
            }


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Bank Name:' . $post['bank_name'];
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
            $data_post = array(
                'bank_name' => $post['bank'],
            );
            $this->Simulation_model->UpdateBankAccount($data_post, $id);

            if ($ico['tmp_name'] != "") {
                $filename = 'bank_' . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->max_thumb_width, $this->max_thumb_height);
                trans_image_resize_to_folder($ico, $this->path_thumb, str_replace(' ', '_', $filename . '_HDPI'),
                    $this->max_thumb_width, $this->max_thumb_height);
                trans_image_resize_to_folder($ico, $this->path_thumb, str_replace(' ', '_', $filename . '_XHDPI'),
                    $this->max_thumb_width, $this->max_thumb_height);
                $data_pic_content = array('bank_thumb' => $content_to_db);

                // update to database
                $this->Simulation_model->UpdateBankAccount($data_pic_content, $id);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Bank Name:' . $post['bank_name'];
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
     * public function change_publish.
     *
     * change publish page by request get.
     */
    public function change_publish($id = 0)
    {
        if ($id) {
            if (ctype_digit($id)) {
                $return = $this->Simulation_model->ChangePublishBank($id);
                echo $return;
            }
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
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Simulation_model->GetBankAccountById($id);
            if ($query->num_rows() > 0) {
                $info = $query->row_array();

                if ($info['bank_thumb'] != '' && file_exists($this->path_thumb . $info['bank_thumb'])) {
                    $pic_thumbnail = '<div id="print-picture-thumbnail">
                            <img src="' . base_url('uploads/' . $file_app . '/' . $info['bank_thumb'] . '') . '" width="150"><br/>
                            <a id="delete-pic-thumbnail" ida="' . $info['id_ref_bank'] . '"style="cursor:pointer;" >Delete Picture</a>
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
        if (isset($this->error['bank'])) {
            $error_msg['bank'] = alert_box($this->error['bank'], 'error');
        } else {
            $error_msg['bank'] = '';
        }
        if (isset($this->error['bank_thumb'])) {
            $error_msg['bank_thumb'] = alert_box($this->error['bank_thumb'], 'error');
        } else {
            $error_msg['bank_thumb'] = '';
        }


        // set value
        if ($this->input->post('bank') != '') {
            $post['bank'] = $this->input->post('bank');
        } elseif ((int)$id > 0) {
            $post['bank'] = $info['bank_name'];
        } else {
            $post['bank'] = '';
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
        $ico = $_FILES['thumb'];

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['bank'] == '') {
            $this->error['bank'] = 'Please insert Bank Name.<br/>';

        } else {
            if (!$this->Simulation_model->check_exist_bank('ref_bank', 'bank_name', $post['bank'], $id)) {
                $this->error['bank'] = 'Bank Name already exists, please try different Bank Name<br/>';
            }
        }

        if ($ico['tmp_name'] != '') {
            $info = getimagesize($ico['tmp_name']);
            if ($info[0] < $this->max_thumb_width || $info[1] < $this->max_thumb_height || $info['mime'] != 'image/png') {
                $this->error['bank_thumb'] = 'Minimum Image width 100px and Height 100px <br> Image type : PNG only';
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}


/* End of file credit_simulation.php */
/* Location: ./application/controllers/admpage/credit_simulation.php */
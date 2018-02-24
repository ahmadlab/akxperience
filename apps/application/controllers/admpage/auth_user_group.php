<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Auth_user_group Class
 * @author    : Latada
 * @Email   : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Auth user group management
 *********************************************/
class Auth_user_group extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $template;
    private $path;
    private $title;
    private $id_admin_menu;
    private $is_superadmin;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'auth_user_group';
        $this->template = getAdminFolder() . '/auth_user_group';
        $this->path_uri = getAdminFolder() . '/auth_user_group';
        $this->path = site_url(getAdminFolder() . '/auth_user_group');
        $this->title = get_admin_menu_title('auth_user_group');
        $this->id_admin_menu = get_admin_menu_id('auth_user_group');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
    }

    /**
     * index page
     */
    public function index()
    {
        $this->main();
    }

    /**
     * main page
     */
    public function main()
    {
        auth_admin();
        $this->session->set_userdata("referrer", current_url());
        $this->global_libs->print_header();
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');

        $pg = $this->uri->segment(6);
        $per_page = 25;
        $uri_segment = 6;
        $no = 0;
        $path = $this->path . '/main/';
        $list_auth_arr = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $id_menu_admin = $this->id_admin_menu;
        $file_app = $this->ctrl;
        $path_uri = $this->path_uri;
        $path_app = $this->path;
        $template = $this->template;
        $breadcrumbs = array();
        $add_btn = site_url($path_uri . '/add');
        $is_superadmin = $this->is_superadmin;
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        $total_records = $this->Authgroup_model->GetTotalGroup($is_superadmin);

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Authgroup_model->ListGroup($is_superadmin, $per_page, $lmt);

        foreach ($query->result_array() as $row_auth_user_group) {
            $no++;
            $id_auth_user_group = $row_auth_user_group['id_auth_user_group'];
            $group = $row_auth_user_group['auth_user_group'];
            $edit_href = site_url($path_uri . '/edit/' . $id_auth_user_group);
            $edit_auth_href = site_url($path_uri . '/auth_pages_edit/' . $id_auth_user_group);
            $list_auth_arr[] = array(
                'no' => $no,
                'id_auth_user_group' => $id_auth_user_group,
                'group' => $group,
                'edit_href' => $edit_href,
                'edit_auth_href' => $edit_auth_href,
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
            'list_auth_arr' => $list_auth_arr,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'path_uri' => $path_uri,
            'add_btn' => $add_btn,
        );
        $this->parser->parse($template . '/auth_user_group.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * add function
     */
    public function add()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post = purify($this->input->post());

            if (!empty($post['is_superadmin'])) {
                $post['is_superadmin'] = $post['is_superadmin'];
            } else {
                $post['is_superadmin'] = 0;
            }

            $data_post = array(
                'auth_user_group' => $post['group'],
                'is_superadmin' => $post['is_superadmin'],
            );

            // insert data
            $id_auth_user_group = $this->Authgroup_model->InsertAdminGroup($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_auth_user_group;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_auth_user_group . '; Group Name:' . $post['group'] . ';';
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
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');

        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->template);
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $post = purify($this->input->post());

            if (!empty($post['is_superadmin'])) {
                $post['is_superadmin'] = $post['is_superadmin'];
            } else {
                $post['is_superadmin'] = 0;
            }

            $data_post = array(
                'auth_user_group' => $post['group'],
                'is_superadmin' => $post['is_superadmin'],
            );

            // update data
            $this->Authgroup_model->UpdateAdminGroup($id, $data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Edit ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Edit ' . $this->title . ' ID : ' . $id . '; Group Name:' . $post['group'] . ';';
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
     * delete page
     */
    public function delete()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');
        if ($this->input->post('id') != "") {
            if ((ctype_digit($this->input->post('id'))) && ($this->input->post('id') > 0)) {
                if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                    if ($this->input->post('id') != adm_sess_usergroupid()) {
                        $id_auth_user_group = $this->input->post('id');
                        $this->Authgroup_model->DeleteAdminGroup($id_auth_user_group);
                        $this->Authgroup_model->DeleteAuthGroup($id_auth_user_group);
                        #insert to log
                        $log_id_user = adm_sess_userid();
                        $log_id_group = adm_sess_usergroupid();
                        $log_action = 'Delete Admin Group ID : ' . $id_auth_user_group;
                        $log_desc = 'Delete Admin Group ID : ' . $id_auth_user_group . ';';
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
                        $this->session->set_flashdata('error_msg', 'Delete failed. You can\'t delete your own Group.');
                    }
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



    ///////// ACL /////////
    /**
     * acl page
     * @param int $get_id
     */
    public function auth_pages_edit($get_id = 0)
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');
        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->template);
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateFormAuth()) {
            $post = purify($this->input->post());

            $this->Authgroup_model->DeleteAuthGroup($id);
            for ($a = 0; $a < count($post['auth_admin']); $a++) {
                $data_post[$a] = array(
                    'id_menu_admin' => $post['auth_admin'][$a],
                    'id_auth_user_group' => $id,
                );
                // update data
                $this->Authgroup_model->InsertAuthGroup($data_post[$a]);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Manage Authentication Group ID : ' . $id;
            $log_desc = 'Manage Authentication Group ID : ' . $id . ';';
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', 'Group Authentication has been updated');

            redirect($this->template);
        }
        $this->getFormAuth($id);
    }


    /////////////////////////////////////////////////////////////////
    /////////////////////////// private /////////////////////////////
    /////////////////////////////////////////////////////////////////

    /**
     * get form
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
        $breadcrumbs = array();
        $cancel_btn = site_url($path_uri);
        $is_superadmin = adm_is_superadmin(adm_sess_userid());

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Authgroup_model->GetAdminGroupById($id);
            if ($query->num_rows() > 0) {
                $group_info = $query->row_array();
            } else {
                $this->session->set_flashdata('info_msg', 'There is no record in our database.');
                redirect($path_uri);
            }
        }

        if ($id) {
            $breadcrumbs[] = array(
                'text' => 'Edit',
                'href' => current_url() . '#',
                'class' => 'class="current"'
            );
            $action = site_url($path_uri . '/edit/' . $id);
        } else {
            $breadcrumbs[] = array(
                'text' => 'Add',
                'href' => current_url() . '#',
                'class' => 'class="current"'
            );
            $action = site_url($path_uri . '/add');
        }

        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['group'])) {
            $error_msg['group'] = alert_box($this->error['group'], 'error');
        } else {
            $error_msg['group'] = '';
        }

        // set value
        if ($this->input->post('group') != '') {
            $post['group'] = $this->input->post('group');
        } elseif ((int)$id > 0) {
            $post['group'] = $group_info['auth_user_group'];
        } else {
            $post['group'] = '';
        }
        if ($this->input->post('is_superadmin') != '') {
            $post['is_superadmin'] = $this->input->post('is_superadmin');
        } elseif ((int)$id > 0) {
            $post['is_superadmin'] = $group_info['is_superadmin'];
        } else {
            $post['is_superadmin'] = 0;
        }

        // print super admin status option
        $superadmin_status = ($is_superadmin == 1) ? $this->print_option_superadmin($post['is_superadmin']) : '';

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
            'path_uri' => $path_uri,
            'cancel_btn' => $cancel_btn,
            'superadmin_status' => $superadmin_status,
        );
        $this->parser->parse($template . '/auth_user_group_form.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     *
     * @param int $selected
     * @return string $return option of superadmin status
     */
    private function print_option_superadmin($selected = '')
    {
        $checked = '';
        if ($selected == 1) {
            $checked = 'checked="checked"';
        }
        $return = '<label for="is_superadmin">Super Administrator :
            <input type="checkbox" value="1" name="is_superadmin" id="is_superadmin" ' . $checked . ' /> </label>';
        return $return;
    }

    /**
     * validate form group admin
     * @param int $id
     * @return bool true / false $this->error
     */
    private function validateForm($id = 0)
    {
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['group'] == '') {
            $this->error['group'] = 'Please insert Admin Group.<br/>';
        } else {
            if (utf8_strlen($post['group']) < 3) {
                $this->error['group'] = 'Group Name length must be at least 3 character(s).<br/>';
            } else {
                if (!$this->Authgroup_model->CheckExistsAdminGroup($post['group'], $id)) {
                    $this->error['group'] = 'Admin Group Name already exists, please input different Group Name.<br/>';
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get form acl
     * @param int $id
     */
    private function getFormAuth($id = 0)
    {
        $this->global_libs->print_header();

        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_uri = $this->path_uri;
        $path_app = $this->path;
        $template = $this->template;
        $id = (int)$id;
        $error_msg = array();
        $auth_list = '';
        $breadcrumbs = array();
        $is_superadmin = $this->is_superadmin;
        $cancel_btn = site_url($path_uri);
        $is_group_superadmin = adm_group_is_superadmin($id);
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );

        $breadcrumbs[] = array(
            'text' => 'Edit Auth Group',
            'href' => '#',
            'class' => 'class="current"'
        );

        $action = site_url($path_uri . '/auth_pages_edit/' . $id);

        $auth_list = $this->getParentAdminMenu($id, $is_group_superadmin);

        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['auth_admin'])) {
            $error_msg['auth_admin'] = alert_box($this->error['auth_admin'], 'error');
        } else {
            $error_msg['auth_admin'] = '';
        }

        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'auth_list' => $auth_list,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'path_uri' => $path_uri,
            'cancel_btn' => $cancel_btn,
        );
        $this->parser->parse($template . '/auth_pages_form.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     *
     * @return bool $this->error
     */
    private function validateFormAuth()
    {
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');

        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['auth_admin'] == '') {
            $this->error['auth_admin'] = 'Please select Authentication Group.<br/>';
        } else {
            if (count($post['auth_admin']) == 0) {
                $this->error['auth_admin'] = 'Please select Authentication Group.<br/>';
            } else {
                foreach ($post['auth_admin'] as $row) {
                    if (!ctype_digit($row)) {
                        $this->error['auth_admin'] = 'Please select correct Authentication Group.<br/>';
                    }
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * parent menu selector
     * @param int $parent
     * @param string $prefix
     * @param int $id_group
     * @return option list of admin menu
     */
    private function getParentAdminMenu($id_group, $is_group_superadmin = 0, $parent = 0, $prefix = '')
    {
        $this->load->model(getAdminFolder() . '/authgroup_model', 'Authgroup_model');
        $tmp_menu = '';
        $query = $this->Authgroup_model->getAllAdminMenu($parent, $is_group_superadmin);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $selected = '';
                $tree = '';
                $divider = '<span class="span-taxo">&nbsp;</span>';
                $id_auth_pages = $this->Authgroup_model->getAuthPages($id_group, $row["id_menu_admin"]);
                if ($id_auth_pages) {
                    $selected = 'checked="checked"';
                }
                if ($parent != 0) {
                    $tree = '<img src="' . base_url('assets/images/admin/tree-tax.png') . '" class="tree-tax" alt="taxo"/>';
                }
                $tmp_menu .= '<label for="menu-admin-' . $row["id_menu_admin"] . '" >
                                    <input type="checkbox" value="' . $row["id_menu_admin"] . '" ' . $selected . '" id="menu-admin-' . $row["id_menu_admin"] . '" name="auth_admin[]">
                            ' . $prefix . ' ' . $tree . ' &nbsp;&nbsp;' . $row["menu"] . '</label>';

                $tmp_menu .= $this->getParentAdminMenu($id_group, $is_group_superadmin, $row["id_menu_admin"],
                    $prefix . $divider);
            }
        }
        return $tmp_menu;
    }


}

/* End of file auth_user_group.php */
/* Location: ./application/controllers/webcontrol/auth_user_group.php */




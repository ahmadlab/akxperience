<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * List_menu_admin Class
 * @Author  : Latada
 * @Email   : mac_ [at] gxrg [dot] org
 * @Type    : Controller
 * @desc    : List of Menu Admin management
 *********************************************/
class List_menu_admin extends CI_Controller
{
    private $error = array();
    private $folder;
    private $ctrl;
    private $template;
    private $path;
    private $path_uri;
    private $title;
    private $is_superadmin;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'list_menu_admin';
        $this->path_uri = getAdminFolder() . '/list_menu_admin';
        $this->template = getAdminFolder() . '/list_menu_admin';
        $this->path = site_url(getAdminFolder() . '/list_menu_admin');
        $this->title = get_admin_menu_title('list_menu_admin');
        $this->id_menu_admin = get_admin_menu_id('list_menu_admin');
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
        $this->load->model(getAdminFolder() . '/adminmenu_model', 'Adminmenu_model');

        $s_title = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/a/';
        $list_menu_admin_arr = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $path_uri = $this->path_uri;
        $template = $this->template;
        $breadcrumbs = array();
        $is_superadmin = $this->is_superadmin;
        $add_btn = site_url($path_uri . '/add');
        $search_query = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_title) > 1) {
            $s_title = substr($s_title, 1);
        } else {
            $s_title = "";
        }

        $total_records = $this->Adminmenu_model->getTotalAdminMenu(myUrlDecode($s_title), $is_superadmin);

        if ($s_title) {
            $path = str_replace("/a", "/a" . $s_title, $path);
            ($search_query == '' ? $search_query .= $s_title : $search_query .= ' + ' . $s_title);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Adminmenu_model->getAllAdminMenu(myUrlDecode($s_title), $is_superadmin, $lmt, $per_page);

        foreach ($query->result_array() as $row_menu_admin) {
            $no++;
            $id_menu_admin = $row_menu_admin['id_menu_admin'];
            $id_parents_menu_admin = $row_menu_admin['id_parents_menu_admin'];
            $menu_admin = $row_menu_admin['menu'];

            $urut = $row_menu_admin['urut'];

            $last_urut = $this->Adminmenu_model->GetRefUrutMax();
            $first_urut = $this->Adminmenu_model->GetRefUrutMin();

            $parents_menu_admin = $this->Adminmenu_model->getParentNameById($id_parents_menu_admin);

            // get sorting
            $sort_up = $sort_down = '';
            if (myUrlDecode($s_title) == "") {
                if ($total_records == 1) {
                    $sort_up = $sort_down = '';
                } elseif ($last_urut == 1) {
                    $sort_up = $sort_down = '';
                } else {
                    if ($urut == 1 && $pg == 1) {
                        $sort_down = sort_arrow($id_menu_admin, $urut, $id_parents_menu_admin, $path_app, 'down');
                    } elseif ($urut == $first_urut) {
                        $sort_down = sort_arrow($id_menu_admin, $urut, $id_parents_menu_admin, $path_app, 'down');
                    } elseif ($urut == $last_urut) {
                        $sort_up = sort_arrow($id_menu_admin, $urut, $id_parents_menu_admin, $path_app, 'up');
                    } elseif ($no == $total_records) {
                        $sort_up = sort_arrow($id_menu_admin, $urut, $id_parents_menu_admin, $path_app, 'up');
                    } else {
                        $sort_up = sort_arrow($id_menu_admin, $urut, $id_parents_menu_admin, $path_app, 'up');
                        $sort_down = sort_arrow($id_menu_admin, $urut, $id_parents_menu_admin, $path_app, 'down');
                    }
                }
            }

            $list_menu_admin_arr[] = array(
                'no' => $no,
                'id_menu_admin' => $id_menu_admin,
                'parents_menu_admin' => $parents_menu_admin,
                'id_parents_menu_admin' => $id_parents_menu_admin,
                'menu' => $menu_admin,
                'urut' => $urut,
                'sort_down' => $sort_down,
                'sort_up' => $sort_up,
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
            'list_menu_admin' => $list_menu_admin_arr,
            's_title' => myUrlDecode($s_title),
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'path_uri' => $path_uri,
            'add_btn' => $add_btn,
            'q_total' => $total_records,
            'search_query' => $search_query,
        );
        $this->parser->parse($template . '/list_menu_admin.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * add page
     */
    public function add()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/adminmenu_model', 'Adminmenu_model');

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post = purify($this->input->post());

            if (!empty($post['is_superadmin'])) {
                $post['is_superadmin'] = $post['is_superadmin'];
            } else {
                $post['is_superadmin'] = 0;
            }

            $data_post = array(
                'id_parents_menu_admin' => $post['id_parents_menu_admin'],
                'menu' => $post['menu'],
                'file' => strtolower($post['file']),
                'is_superadmin' => $post['is_superadmin'],
            );
            $last_urut = $this->Adminmenu_model->GetRefUrutMax();
            $urut = $last_urut + 1;
            $data_urut = array('urut' => $urut);
            $data = array_merge($data_post, $data_urut);

            // insert data
            $id_menu_admin = $this->Adminmenu_model->InsertAdminMenu($data);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id_menu_admin;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id_menu_admin . '; Menu Name:' . $post['menu'] . ';';
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', $this->title . ' has been added');

            redirect($this->template);
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
        $this->load->model(getAdminFolder() . '/adminmenu_model', 'Adminmenu_model');

        if (!$this->Adminmenu_model->CheckAdminMenuByGroupId(adm_sess_usergroupid(),
                $get_id) && !$this->is_superadmin
        ) {
            $this->session->set_flashdata('info_msg', 'You don\'t have access to change this menu.');
            redirect($this->path_uri);
        }

        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->path_uri);
        }

        if (!$this->Adminmenu_model->check_is_superadmin($id, $this->is_superadmin)) {
            redirect($this->path_uri);
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $post = purify($this->input->post());

            if (!empty($post['is_superadmin'])) {
                $post['is_superadmin'] = $post['is_superadmin'];
            } else {
                $post['is_superadmin'] = 0;
            }

            $data_post = array(
                'id_parents_menu_admin' => $post['id_parents_menu_admin'],
                'menu' => $post['menu'],
                'file' => strtolower($post['file']),
                'is_superadmin' => $post['is_superadmin'],
            );

            // update data
            $this->Adminmenu_model->UpdateAdminMenu($id, $data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Edit ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Edit ' . $this->title . ' ID : ' . $id . '; Menu Name:' . $post['menu'] . ';';
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
     * search page
     */
    public function search()
    {
        auth_admin();
        $s_title = myUrlEncode(trim($this->input->post('s_title')));
        redirect($this->template . '/main/a' . $s_title);
    }

    /**
     * change sort
     */
    public function change_sort()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/adminmenu_model', 'Adminmenu_model');

        if ($this->input->post('id') != '' && $this->input->post('parent_id') != '' && $this->input->post('urut') != '' && $this->input->post('direction') != '') {
            $id = $this->input->post('id');
            $parent_id = $this->input->post('parent_id');
            $urut = $this->input->post('urut');
            $direction = $this->input->post('direction');

            if (((ctype_digit($id)) && ($id > 0)) &&
                (ctype_digit($parent_id)) &&
                ((ctype_digit($urut)) && ($urut > 0)) &&
                ($direction != '')
            ) {
                if ($this->Adminmenu_model->CheckAdminMenuByGroupId(adm_sess_usergroupid(), $id)) {
                    $this->Adminmenu_model->ChangeSort($id, $parent_id, $urut, $direction);

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
                    $this->session->set_flashdata('info_msg', 'You don\'t have access to change ' . $this->title . '.');
                }
            } else {
                $this->session->set_flashdata('error_msg', 'Change sort failed. Please try again.');
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Change sort failed. Please try again.');
        }
    }

    /**
     * delete page
     */
    public function delete()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/adminmenu_model', 'Adminmenu_model');

        if ($this->input->post('id') != "") {
            if (($this->input->post('id') > 0)) {
                if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
                    $id = array_filter(explode('-', $this->input->post('id')));
                    foreach ($id as $ids) {
                        if (!$this->Adminmenu_model->CheckAdminMenuByGroupId(adm_sess_usergroupid(),
                                $ids) || $this->is_superadmin
                        ) {
                            $this->session->set_flashdata('info_msg',
                                'You don\'t have access to delete ' . $this->title . '.');
                        }
                    }
                    $this->Adminmenu_model->DeleteAdminMenu($id);
                    $idc = implode(',', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete ' . $this->title . ' ID : ' . $idc;
                    $log_desc = 'Delete ' . $this->title . ' ID : ' . $idc . ';';
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
        $breadcrumbs = array();
        $is_superadmin = $this->is_superadmin;
        $cancel_btn = site_url($path_uri);

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Adminmenu_model->GetAdminMenuById($id);
            if ($query->num_rows() > 0) {
                $admin_menu = $query->row_array();
            } else {
                $this->session->set_flashdata('info_msg', 'There is no record in our database.');
                redirect($template);
            }
        }

        if ($id) {
            $breadcrumbs[] = array(
                'text' => 'Edit',
                'href' => '#',
                'class' => 'class="current"'
            );
            $action = site_url($path_uri . '/edit/' . $id);
        } else {
            $breadcrumbs[] = array(
                'text' => 'Add',
                'href' => '#',
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
        if (isset($this->error['id_parents_menu_admin'])) {
            $error_msg['id_parents_menu_admin'] = alert_box($this->error['id_parents_menu_admin'], 'error');
        } else {
            $error_msg['id_parents_menu_admin'] = '';
        }
        if (isset($this->error['menu'])) {
            $error_msg['menu'] = alert_box($this->error['menu'], 'error');
        } else {
            $error_msg['menu'] = '';
        }
        if (isset($this->error['file'])) {
            $error_msg['file'] = alert_box($this->error['file'], 'error');
        } else {
            $error_msg['file'] = '';
        }

        // set value
        if ($this->input->post('id_parents_menu_admin') != '') {
            $post['id_parents_menu_admin'] = $this->input->post('id_parents_menu_admin');
        } elseif ((int)$id > 0) {
            $post['id_parents_menu_admin'] = $admin_menu['id_parents_menu_admin'];
        } else {
            $post['id_parents_menu_admin'] = 0;
        }

        if ($this->input->post('menu') != '') {
            $post['menu'] = $this->input->post('menu');
        } elseif ((int)$id > 0) {
            $post['menu'] = $admin_menu['menu'];
        } else {
            $post['menu'] = '';
        }

        if ($this->input->post('file') != '') {
            $post['file'] = $this->input->post('file');
        } elseif ((int)$id > 0) {
            $post['file'] = $admin_menu['file'];
        } else {
            $post['file'] = '';
        }

        if ($this->input->post('is_superadmin') != '') {
            $post['is_superadmin'] = $this->input->post('is_superadmin');
        } elseif ((int)$id > 0) {
            $post['is_superadmin'] = $admin_menu['is_superadmin'];
        } else {
            $post['is_superadmin'] = 0;
        }

        // generate menu parent
        $list_parent_option = $this->getParentSelect(0, $is_superadmin, "--", $post['id_parents_menu_admin'], $id);

        // print super admin status option
        $superadmin_status = ($is_superadmin == 1) ? $this->print_option_superadmin($post['is_superadmin']) : '';

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'list_parent_option' => $list_parent_option,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'path_uri' => $path_uri,
            'cancel_btn' => $cancel_btn,
            'superadmin_status' => $superadmin_status,
        );
        $this->parser->parse($template . '/list_menu_admin_form.html', $data);
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
     * form validation
     * @param int $id
     * @return int
     */
    private function validateForm($id = 0)
    {
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($id) {
            if ($post['id_parents_menu_admin'] == $id) {
                $this->error['id_parents_menu_admin'] = 'Please set correct Parent.<br/>';
            }
        }

        if ($post['id_parents_menu_admin'] == '') {
            $this->error['id_parents_menu_admin'] = 'Please insert Parent.<br/>';
        }

        if (!ctype_digit($post['id_parents_menu_admin'])) {
            $this->error['id_parents_menu_admin'] = 'Please insert Parent.<br/>';
        }

        if ($post['menu'] == '') {
            $this->error['menu'] = 'Please insert Menu Title.<br/>';
        }

        if ($post['file'] == '') {
            $this->error['file'] = 'Please insert Menu File.<br/>';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param int $parent
     * @param int $is_superadmin
     * @param string $prefix
     * @param int $selectitem
     * @param int $disable
     * @return string option list of admin menu
     */
    private function getParentSelect($parent, $is_superadmin = null, $prefix = '', $selectitem = '', $disable = '')
    {
        $tmp_menu = '';
        $this->load->model(getAdminFolder() . '/adminmenu_model', 'Adminmenu_model');
        $query = $this->Adminmenu_model->getAdminMenuList($parent, $is_superadmin);
        foreach ($query->result_array() as $row) {
            if ($disable > 0) {
                if ($row["id_menu_admin"] == $disable) {
                    $tmp_menu .= '';
                    //$tmp_menu .=  '<option value="'.$row["id_menu_admin"].'" disabled="disabled">'.$prefix.' '.$row["menu"].'</option>';
                } elseif ($row["id_parents_menu_admin"] == $disable) {
                    $tmp_menu .= '';
                    //$tmp_menu .=  '<option value="'.$row["id_menu_admin"].'" disabled="disabled">'.$prefix.' '.$row["menu"].'</option>';
                    //$disable = $row["id_menu_admin"];
                } else {
                    if ($selectitem == $row["id_menu_admin"]) {
                        $tmp_menu .= '<option value="' . $row["id_menu_admin"] . '" selected="selected">' . $prefix . ' ' . $row["menu"] . '</option>';
                    } else {
                        $tmp_menu .= '<option value="' . $row["id_menu_admin"] . '">' . $prefix . ' ' . $row["menu"] . '</option>';
                    }
                }
                //$tmp_menu .= '';
            } else {
                if ($selectitem == $row["id_menu_admin"]) {
                    $tmp_menu .= '<option value="' . $row["id_menu_admin"] . '" selected="selected">' . $prefix . ' ' . $row["menu"] . '</option>';
                } else {
                    $tmp_menu .= '<option value="' . $row["id_menu_admin"] . '">' . $prefix . ' ' . $row["menu"] . '</option>';
                }
            }
            $tmp_menu .= $this->getParentSelect($row["id_menu_admin"], $is_superadmin, $prefix . "--", $selectitem,
                $disable);
        }
        return $tmp_menu;
    }

}


/* End of file menu_admin.php */
/* Location: ./application/controllers/webcontrol/menu_admin.php */



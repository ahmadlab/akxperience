<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/***********************************************************
 * Module Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : module content and single content management.
 ************************************************************/
class Module extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $path_uri;
    private $template;
    private $path;
    private $title;
    private $id_menu_admin;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'module';
        $this->path_uri = getAdminFolder() . '/module';
        $this->template = getAdminFolder() . '/module';
        $this->path = site_url(getAdminFolder() . '/module');
        $this->title = get_admin_menu_title('module');
        $this->id_menu_admin = get_admin_menu_id('module');
    }

    /**
     * public function index.
     *
     * this function is same as main
     */
    public function index()
    {
        // load function main
        $this->main();
    }

    /**
     * public funcion main
     */
    public function main()
    {
        auth_admin();
        $this->session->set_userdata("referrer", current_url());
        $this->global_libs->print_header();
        $this->load->model(getAdminFolder() . '/module_model', 'Module_model');

        $s_title = $this->uri->segment(4);
        $pg = $this->uri->segment(5);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/a/';
        $list_module = array();
        $folder = $this->folder;
        $menu_title = $this->title;
        $path_uri = $this->path_uri;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $template = $this->template;
        $breadcrumbs = array();
        $publish_list = array();
        $dep_list = array();
        $add_btn = site_url($path_uri . '/add');

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

        $total_records = $this->Module_model->getTotalModule(myUrlDecode($s_title));

        if ($s_title) {
            $path = str_replace("/a/", "/a" . $s_title . "/", $path);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }

        $no = $lmt;

        $query = $this->Module_model->GetAllModule(myUrlDecode($s_title), $lmt, $per_page);

        foreach ($query->result_array() as $row_module) {
            $no++;
            $id_module = $row_module['id_module'];
            $module = $row_module['module'];
            $module_title = $row_module['module_title'];
            $module_link = $row_module['module_link'];
            $is_installed = $row_module['is_installed'];
            $status = $is_installed == 1 ? "Uninstall" : "Install";
            $edit_href = site_url($path_uri . '/edit/' . $id_module);

            $list_module[] = array(
                'no' => $no,
                'id_module' => $id_module,
                'module' => $module,
                'module_title' => $module_title,
                'module_link' => $module_link,
                'status' => $status,
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
            'list_module' => $list_module,
            's_title' => myUrlDecode($s_title),
            'breadcrumbs' => $breadcrumbs,
            'pagination' => $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
        );
        $this->parser->parse($template . '/module.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * public function search.
     *
     * search content from Module Controller.
     * @post string $s_title
     *  post title of module
     * @return redirect to main page with search key
     */
    public function search()
    {
        auth_admin();
        $s_title = myUrlEncode(trim($this->input->post('s_title')));
        redirect($this->path_uri . '/main/a' . $s_title);
    }

    /**
     * install module
     */
    public function set_action()
    {
        auth_admin();
        if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->load->model(getAdminFolder() . '/module_model', 'Module_model');
            if ($this->input->post('id') != "" && ctype_digit($this->input->post('id')) && $this->input->post('id') > 0) {
                $id = $this->input->post('id');
                $check = $this->Module_model->CheckModuleInstall($id);
                if ($check == 'installed') {
                    $class = $this->Module_model->getModuleClassName($id);
                    $class = $class . '_model';

                    $this->load->model(getAdminFolder() . '/' . $class, ucfirst($class));
                    $class = ucfirst($class);
                    $model = new $class;

                    if (method_exists($model, 'uninstall')) {
                        $this->{$class}->uninstall();
                        $this->Module_model->UpdateModule($id, array('is_installed' => 0));
                        $this->session->set_flashdata('success_msg', $this->title . ' has been Uninstalled.');
                        echo 'Uninstalled';
                    } else {
                        $this->session->set_flashdata('error_msg',
                            $this->title . ' failed to Uninstalled. Please try again.');
                        echo 'Failed to Unistall yo';
                    }
                } elseif ($check == 'uninstall') {
                    $class = $this->Module_model->getModuleClassName($id);
                    $class = $class . '_model';

                    $this->load->model(getAdminFolder() . '/' . $class, ucfirst($class));
                    $class = ucfirst($class);
                    $model = new $class;

                    if (method_exists($model, 'install')) {
                        $this->{$class}->install();
                        $this->Module_model->UpdateModule($id, array('is_installed' => 1));
                        $this->session->set_flashdata('success_msg', $this->title . ' has been Installed.');
                        echo 'Installed';
                    } else {
                        $this->session->set_flashdata('error_msg',
                            $this->title . ' failed to Installed. Please try again.');
                        echo 'Failed to Install';
                    }
                }
            } else {
                echo 'Failed to Install';
                $this->session->set_flashdata('error_msg', 'Action failed. Please try again.');
            }
        } else {
            echo 'Failed to Install';
            $this->session->set_flashdata('error_msg', 'You can\'t manage this content.<br/>');
        }
    }

    /**
     * public funtion add.
     *
     * adding a new content.
     *
     */
    public function add()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/module_model', 'Module_model');

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post = purify($this->input->post());

            $data_post = array(
                'module' => strtolower($post['module']),
                'module_title' => $post['module_title'],
                'module_link' => strtolower($post['module_link']),
            );

            // insert data
            $id_module = $this->Module_model->InsertModule($data_post);

            #insert to log
            $log_last_user_id = $id_module;
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $log_last_user_id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $log_last_user_id . '; Title:' . $post['module_title'] . '; Module:' . $post['module'] . '; ';
            $data_log = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_log);

            $this->session->set_flashdata('success_msg', $this->title . ' has been added.');

            redirect($this->path_uri);
        }
        $this->getForm();
    }

    /**
     * public function edit.
     *
     * edit content page
     * @param int $get_id
     *  id of editing content
     */
    public function edit($get_id = 0)
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/module_model', 'Module_model');

        $id = (int)$get_id;

        if (!$id) {
            $this->session->set_flashdata('error_msg', 'Please try again with the right method.');
            redirect($this->template);
        }

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id)) {
            $post = purify($this->input->post());

            $data_post = array(
                'module' => strtolower($post['module']),
                'module_title' => $post['module_title'],
                'module_link' => strtolower($post['module_link']),
            );

            // update data
            $this->Module_model->UpdateModule($id, $data_post);

            #insert to log
            $log_last_user_id = $id;
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Edit ' . $this->title . ' ID : ' . $log_last_user_id;
            $log_desc = 'Edit ' . $this->title . ' ID : ' . $log_last_user_id . '; Title:' . $post['module_title'] . '; Module:' . $post['module'] . '; ';
            $data_log = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_log);

            $this->session->set_flashdata('success_msg', $this->title . ' has been updated.');

            if ($this->session->userdata('referrer') != '') {
                redirect($this->session->userdata('referrer'));
            } else {
                redirect($this->path_uri);
            }
        }
        $this->getForm($id);
    }

    /**
     * private function getForm.
     *
     * generate process of add / edit page.
     *
     * @param int $id
     *  (Optional) id of page.
     * @return print/show form for add/edit page
     */
    private function getForm($id = 0)
    {
        $this->global_libs->print_header();

        $folder = $this->folder;
        $menu_title = $this->title;
        $file_app = $this->ctrl;
        $path_app = $this->path;
        $template = $this->template;
        $path_uri = $this->path_uri;
        $id = (int)$id;
        $error_msg = array();
        $post = array();
        $breadcrumbs = array();
        $cancel_btn = site_url($path_uri);

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => site_url($path_uri),
            'class' => ''
        );


        if ($id) {
            $query = $this->Module_model->GetModuleByID($id);
            if ($query->num_rows() > 0) {
                $module_info = $query->row_array();
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

        if (isset($this->error['module'])) {
            $error_msg['module'] = alert_box($this->error['module'], 'error');
        } else {
            $error_msg['module'] = '';
        }
        if (isset($this->error['module_title'])) {
            $error_msg['module_title'] = alert_box($this->error['module_title'], 'error');
        } else {
            $error_msg['module_title'] = '';
        }
        if (isset($this->error['module_link'])) {
            $error_msg['module_link'] = alert_box($this->error['module_link'], 'error');
        } else {
            $error_msg['module_link'] = '';
        }

        // set value
        if ($this->input->post('module') != '') {
            $post['module'] = $this->input->post('module');
        } elseif ((int)$id > 0) {
            $post['module'] = $module_info['module'];
        } else {
            $post['module'] = '';
        }
        if ($this->input->post('module_title') != '') {
            $post['module_title'] = $this->input->post('module_title');
        } elseif ((int)$id > 0) {
            $post['module_title'] = $module_info['module_title'];
        } else {
            $post['module_title'] = '';
        }
        if ($this->input->post('module_link') != '') {
            $post['module_link'] = $this->input->post('module_link');
        } elseif ((int)$id > 0) {
            $post['module_link'] = $module_info['module_link'];
        } else {
            $post['module_link'] = '';
        }

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'id' => $id,
            'post' => $post,
            'action' => $action,
            'error_msg' => $error_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'path_uri' => $path_uri,
            'cancel_btn' => $cancel_btn,
        );
        $this->parser->parse($template . '/module_form.html', $data);
        $this->global_libs->print_footer();
    }

    /**
     * private function validateForm.
     *
     * validation post value from page form
     * @param int $id
     *  (Optional) set id of page
     * @return string $this->error
     *  returning error value of each field
     */
    private function validateForm($id = 0)
    {
        $this->load->model(getAdminFolder() . '/module_model', 'Module_model');
        $id = (int)$id;
        $post = purify($this->input->post());

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['module'] == '') {
            $this->error['module'] = 'Please insert Module.<br/>';
        } else {
            if (utf8_strlen($post['module']) < 3) {
                $this->error['module'] = 'Title length must be at least 3 character(s).<br/>';
            } else {
                if (!$this->Module_model->CheckExistsModule($post['module'], $id)) {
                    $this->error['module'] = 'Module is exists. Please type another Module.<br/>';
                }
            }
        }
        if ($post['module_link'] == '') {
            $this->error['module_link'] = 'Please insert Title.<br/>';
        } else {
            if (utf8_strlen($post['module_link']) < 3) {
                $this->error['module'] = 'Title length must be at least 3 character(s).<br/>';
            } else {
                if (!$this->Module_model->CheckExistsModule($post['module_link'], $id)) {
                    $this->error['module_link'] = 'Module is exists. Please type another Module.<br/>';
                }
            }
        }
        if ($post['module_title'] == '') {
            $this->error['module_title'] = 'Please insert Module Title.<br/>';
        } else {
            if (utf8_strlen($post['module_title']) < 3) {
                $this->error['module_title'] = 'Please insert Module Title.<br/>';
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * public function delete.
     *
     * delete page by request post.
     *
     * @post int $id
     *  post id
     */
    public function delete()
    {
        auth_admin();
        $this->load->model(getAdminFolder() . '/module_model', 'Module_model');
        if ($this->input->post('id') != "" && ctype_digit($this->input->post('id')) && $this->input->post('id') > 0) {
            if (auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {

                $this->Module_model->DeleteModule($this->input->post('id'));

                #insert to log
                $log_last_user_id = $this->input->post('id');
                $log_id_user = adm_sess_userid();
                $log_id_group = adm_sess_usergroupid();
                $log_action = 'Delete ' . $this->title . ' ID : ' . $log_last_user_id;
                $log_desc = 'Delete ' . $this->title . ' ID : ' . $log_last_user_id . ';';
                $data_log = array(
                    'id_user' => $log_id_user,
                    'id_group' => $log_id_group,
                    'action' => $log_action,
                    'desc' => $log_desc,
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data_log);
                $this->session->set_flashdata('success_msg', $this->title . ' (s) has been deleted.');
            } else {
                $this->session->set_flashdata('error_msg', 'You can\'t manage this content.<br/>');
            }
        } else {
            $this->session->set_flashdata('error_msg', 'Delete failed. Please try again.');
        }
    }

}

/* End of file module.php */
/* Location: ./application/controllers/webcontrol/module.php */


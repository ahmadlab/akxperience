<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Ref Redeem Point Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Ref Redeem Point Management
 *********************************************/
class Ref_redeem_point extends CI_Controller
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

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->folder = getAdminFolder();
        $this->ctrl = 'ref_redeem_point';
        $this->template = getAdminFolder() . '/modules/ref_redeem_point';
        $this->path_uri = getAdminFolder() . '/ref_redeem_point';
        $this->path = site_url(getAdminFolder() . '/ref_redeem_point');
        $this->title = get_admin_menu_title('ref_redeem_point');
        $this->id_menu_admin = get_admin_menu_id('ref_redeem_point');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->load->model(getAdminFolder() . '/Redeem_point_model', 'Ref_redeem_point');
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
        $this->load->helper('text');
        $s_reward = $this->uri->segment(4);
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
        $search_query = '';

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_reward) > 1) {
            $s_reward = substr($s_reward, 1);

        } else {
            $s_reward = "";

        }
        $total_records = $this->Ref_redeem_point->TotalRefRedeem(myUrlDecode($s_reward));


        if ($s_reward) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_reward . "/", $path);
            ($search_query == '' ? $search_query .= $s_reward : $search_query .= ' + ' . $s_reward);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Ref_redeem_point->GetAllRefRedeem(myUrlDecode($s_reward), $lmt, $per_page);

        foreach ($query->result_array() as $bufss) {
            $no++;
            $id = $bufss['id_ref_redeem_point'];
            $ref_publish = ($bufss['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $reward = $bufss['reward'];
            $point = $bufss['point'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'reward' => $reward,
                'edit_href' => $edit_href,
                'point' => $point,
                'ref_publish' => $ref_publish
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
            's_reward' => $s_reward,
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
     * public function change_publish.
     *
     * change publish page by request get.
     */
    public function change_publish($id = 0)
    {
        if ($id) {
            if (ctype_digit($id)) {
                $return = $this->Ref_redeem_point->ChangePublishRedeem($id);
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
        $s_reward = myUrlEncode(trim($this->input->post('s_reward')));
        redirect($this->path . '/main/a' . $s_reward);
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

                    foreach ($id as $k => $f) {
                        if ($f == '3') {
                            unset($id[$k]);
                        }
                    }

                    if ($id) {
                        echo 'door';
                        $this->Ref_redeem_point->DeleteRefRedeem($id);
                        $this->session->set_flashdata('success_msg', $this->title . ' (s) has been deleted.');

                    } else {
                        $this->session->set_flashdata('info_msg', 'Redeem VIP Renewal cannot be delete.');
                    }

                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete ' . $this->ctrl . ' ID : ' . $id;
                    $log_desc = 'Delete ' . $this->ctrl . ' ID : ' . $id . ';';
                    $data = array(
                        'id_user' => $log_id_user,
                        'id_group' => $log_id_group,
                        'action' => $log_action,
                        'desc' => $log_desc,
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);


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
                'reward' => $post['reward'],
                'point' => $post['point'],
            );

            // insert data
            $id = $this->Ref_redeem_point->InsertRefRedeem($data_post);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Reward :' . $post['reward'] . '; Point :' . $post['point'];
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
                'reward' => $post['reward'],
                'point' => $post['point'],
            );

            if ($id == '3') {
                unset($data_post['reward']);
            }

            $this->Ref_redeem_point->UpdateRefRedeem($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Reward :' . $post['reward'] . '; Point :' . $post['point'];
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
            $query = $this->Ref_redeem_point->GetRefRedeemById($id);
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
            // $Post['id'] = 0;
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['point'])) {
            $error_msg['point'] = alert_box($this->error['point'], 'error');
        } else {
            $error_msg['point'] = '';
        }
        if (isset($this->error['reward'])) {
            $error_msg['reward'] = alert_box($this->error['reward'], 'error');
        } else {
            $error_msg['reward'] = '';
        }


        // set value
        if ($this->input->post('point') != '') {
            $post['point'] = $this->input->post('point');
        } elseif ((int)$id > 0) {
            $post['point'] = $info['point'];
        } else {
            $post['point'] = '';
        }
        if ($this->input->post('reward') != '') {
            $post['reward'] = $this->input->post('reward');
        } elseif ((int)$id > 0) {
            $post['reward'] = $info['reward'];
        } else {
            $post['reward'] = '';
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


        if ($post['reward'] == '') {
            $this->error['reward'] = 'Please insert Reward.<br/>';
        } else {
            if (!$this->Ref_redeem_point->CheckExistsRefRedeem($post['reward'], $id)) {
                $this->error['reward'] = 'Reward name already exists, please input different name.<br/>';
            } else {
                if (utf8_strlen($post['reward']) < 3) {
                    $this->error['reward'] = 'Reward length must be at least 3 character(s).<br/>';
                }
            }
        }

        if ($post['point'] == '' || $post['point'] < '1') {
            $this->error['point'] = 'Please insert point.<br/>';
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
<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Credit Simulation Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Credit Simulation Management
 *********************************************/
class Credit_simulation extends CI_Controller
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
        $this->ctrl = 'credit_simulation';
        $this->template = getAdminFolder() . '/modules/credit_simulation';
        $this->path_uri = getAdminFolder() . '/credit_simulation';
        $this->path = site_url(getAdminFolder() . '/credit_simulation');
        $this->title = get_admin_menu_title('credit_simulation');
        $this->id_menu_admin = get_admin_menu_id('credit_simulation');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_thumb_height = 105;
        $this->max_thumb_width = 148;
        $this->path_thumb = './uploads/credit_simulation/';
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
        $s_tenor = $this->uri->segment(5);
        $s_dp = $this->uri->segment(6);
        $pg = $this->uri->segment(7);
        $per_page = 25;
        $uri_segment = 7;
        $no = 0;
        $path = $this->path . '/main/b/t/d/';
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
        if (strlen($s_tenor) > 1) {
            $s_tenor = substr($s_tenor, 1);

        } else {
            $s_tenor = "";

        }
        if (strlen($s_dp) > 1) {
            $s_dp = substr($s_dp, 1);

        } else {
            $s_dp = "";

        }

        $l_bank = selectlist('ref_bank', 'id_ref_bank', 'bank_name', null, $s_bank, '--- Chooice Bank Account ---');
        $l_tenor = selectlist('ref_tenor', 'id_ref_tenor', 'tenor', null, $s_tenor, '--- Chooice Tenor ---');
        $l_dp = selectlist('ref_down_payment', 'id_ref_down_payment', 'down_payment', null, $s_dp,
            '--- Chooice Down Payment ---');
        $total_records = $this->Simulation_model->TotalCreditSimulation(myUrlDecode($s_bank), myUrlDecode($s_tenor),
            myUrlDecode($s_dp));

        if ($s_bank) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_bank . "/", $path);
            ($search_query == '' ? $search_query .= $s_bank : $search_query .= ' + ' . $s_bank);
        }
        if ($s_tenor) {
            $dis_urut = "none";
            $path = str_replace("/t/", "/t" . $s_tenor . "/", $path);
            ($search_query == '' ? $search_query .= $s_tenor : $search_query .= ' + ' . $s_tenor);
        }
        if ($s_dp) {
            $dis_urut = "none";
            $path = str_replace("/t/", "/t" . $s_dp . "/", $path);
            ($search_query == '' ? $search_query .= $s_dp : $search_query .= ' + ' . $s_dp);
        }


        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Simulation_model->GetAllCreditSimulation(myUrlDecode($s_bank), myUrlDecode($s_tenor),
            myUrlDecode($s_dp), $lmt, $per_page);

        foreach ($query->result_array() as $buffs) {
            $no++;
            $id = $buffs['id_credit_simulation'];
            // $ref_publish	= ($buffs['ref_publish'] == 1) ? 'publish' : 'Not Publish';
            $bank = db_get_one('ref_bank', 'bank_name', "id_ref_bank = '" . $buffs['ref_bank'] . "'");
            $tenor = db_get_one('ref_tenor', 'tenor', "id_ref_tenor = '" . $buffs['ref_tenor'] . "'") . ' Tahun';
            $dp = db_get_one('ref_down_payment', 'down_payment',
                    "id_ref_down_payment = '" . $buffs['ref_down_payment'] . "'") . '% ';
            $insurance = $buffs['asuransi'];
            $adm = $buffs['administrasi'];
            $provisi = $buffs['provisi'];
            $bunga = $buffs['bunga'];

            $edit_href = site_url($path_uri . '/edit/' . $id);
            $list_data[] = array(
                'no' => $no,
                'id' => $id,
                'insurance' => $insurance,
                'adm' => $adm,
                'provisi' => $provisi,
                'bank' => $bank,
                'tenor' => $tenor,
                'dp' => $dp,
                'edit_href' => $edit_href,
                'bunga' => $bunga
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
            'l_bank' => $l_bank,
            'l_tenor' => $l_tenor,
            'l_dp' => $l_dp,
            's_bank' => $s_bank,
            's_tenor' => $s_tenor,
            's_dp' => $s_dp,
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
        $s_tenor = myUrlEncode(trim($this->input->post('s_tenor')));
        $s_dp = myUrlEncode(trim($this->input->post('s_dp')));
        redirect($this->path . '/main/b' . $s_bank . '/t' . $s_tenor . '/d' . $s_dp);
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
                    $this->Simulation_model->DeleteCreditSimulation($id);
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
     * add page
     */
    public function add()
    {
        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $post = purify($this->input->post());
            $data_post = array(
                'ref_bank' => $post['ref_bank'],
                'ref_tenor' => $post['ref_tenor'],
                'ref_down_payment' => $post['ref_dp'],
                'asuransi' => $post['asuransi'],
                'administrasi' => $post['administrasi'],
                'provisi' => $post['provisi'],
                'bunga' => $post['bunga'],
            );

            // check if ref_bank, tenor, dp not same
            // true then show info msg to explain why
            // false continue insert
            $id = $this->Simulation_model->InsertCreditSimulation($data_post);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; ref_bank:' . $post['ref_bank'] . '; ref_tenor :' . $post['ref_tenor'] . ';ref_down_payment : ' . $post['ref_down_payment'];
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
            $post = purify($this->input->post());
            $data_post = array(
                'ref_bank' => $post['ref_bank'],
                'ref_tenor' => $post['ref_tenor'],
                'ref_down_payment' => $post['ref_dp'],
                'asuransi' => $post['asuransi'],
                'administrasi' => $post['administrasi'],
                'provisi' => $post['provisi'],
                'bunga' => $post['bunga'],
            );
            $this->Simulation_model->UpdateCreditSimulation($data_post, $id);

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; ref_bank:' . $post['ref_bank'] . '; ref_tenor :' . $post['ref_tenor'] . ';ref_down_payment : ' . $post['ref_down_payment'];
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
            $query = $this->Simulation_model->GetCreditSimulationById($id);
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
        if (isset($this->error['ref_bank'])) {
            $error_msg['ref_bank'] = alert_box($this->error['ref_bank'], 'error');
        } else {
            $error_msg['ref_bank'] = '';
        }
        if (isset($this->error['ref_tenor'])) {
            $error_msg['ref_tenor'] = alert_box($this->error['ref_tenor'], 'error');
        } else {
            $error_msg['ref_tenor'] = '';
        }
        if (isset($this->error['ref_dp'])) {
            $error_msg['ref_dp'] = alert_box($this->error['ref_dp'], 'error');
        } else {
            $error_msg['ref_dp'] = '';
        }
        if (isset($this->error['asuransi'])) {
            $error_msg['asuransi'] = alert_box($this->error['asuransi'], 'error');
        } else {
            $error_msg['asuransi'] = '';
        }
        if (isset($this->error['administrasi'])) {
            $error_msg['administrasi'] = alert_box($this->error['administrasi'], 'error');
        } else {
            $error_msg['administrasi'] = '';
        }
        if (isset($this->error['provisi'])) {
            $error_msg['provisi'] = alert_box($this->error['provisi'], 'error');
        } else {
            $error_msg['provisi'] = '';
        }
        if (isset($this->error['bunga'])) {
            $error_msg['bunga'] = alert_box($this->error['bunga'], 'error');
        } else {
            $error_msg['bunga'] = '';
        }
        if (isset($this->error['redudant'])) {
            $error_msg['redudant'] = alert_box($this->error['redudant'], 'error');
        } else {
            $error_msg['redudant'] = '';
        }


        // set value
        if ($this->input->post('asuransi') != '') {
            $post['asuransi'] = $this->input->post('asuransi');
        } elseif ((int)$id > 0) {
            $post['asuransi'] = $info['asuransi'];
        } else {
            $post['asuransi'] = '';
        }
        if ($this->input->post('administrasi') != '') {
            $post['administrasi'] = $this->input->post('administrasi');
        } elseif ((int)$id > 0) {
            $post['administrasi'] = $info['administrasi'];
        } else {
            $post['administrasi'] = '';
        }
        if ($this->input->post('provisi') != '') {
            $post['provisi'] = $this->input->post('provisi');
        } elseif ((int)$id > 0) {
            $post['provisi'] = $info['provisi'];
        } else {
            $post['provisi'] = '';
        }
        if ($this->input->post('bunga') != '') {
            $post['bunga'] = $this->input->post('bunga');
        } elseif ((int)$id > 0) {
            $post['bunga'] = $info['bunga'];
        } else {
            $post['bunga'] = '';
        }

        if ($this->input->post('ref_bank') != '') {
            $post['banklist'] = selectlist('ref_bank', 'id_ref_bank', 'bank_name', "ref_publish = '1'",
                $this->input->post('ref_bank'), '--- Chooice Bank Account ---', 'id_ref_bank');
        } elseif ((int)$id > 0) {
            $post['banklist'] = selectlist('ref_bank', 'id_ref_bank', 'bank_name', "ref_publish = '1'",
                $info['ref_bank'], '--- Chooice Bank Account ---', 'id_ref_bank');
        } else {
            $post['banklist'] = selectlist('ref_bank', 'id_ref_bank', 'bank_name', "ref_publish = '1'", null,
                '--- Chooice Bank Account ---', 'id_ref_bank');
        }

        if ($this->input->post('ref_tenor') != '') {
            $post['tenorlist'] = selectlist('ref_tenor', 'id_ref_tenor', 'tenor', null, $this->input->post('ref_tenor'),
                '--- Chooice Tenor ---', 'id_ref_tenor', null, 'Tahun');
        } elseif ((int)$id > 0) {
            $post['tenorlist'] = selectlist('ref_tenor', 'id_ref_tenor', 'tenor', null, $info['ref_tenor'],
                '--- Chooice Tenor ---', 'id_ref_tenor', null, 'Tahun');
        } else {
            $post['tenorlist'] = selectlist('ref_tenor', 'id_ref_tenor', 'tenor', null, null, '--- Chooice Tenor ---',
                'id_ref_tenor', null, 'Tahun');
        }

        if ($this->input->post('ref_dp') != '') {
            $post['dplist'] = selectlist('ref_down_payment', 'id_ref_down_payment', 'down_payment', null,
                $this->input->post('ref_dp'), '--- Chooice Down Payment ---', 'id_ref_down_payment', null, '%');
        } elseif ((int)$id > 0) {
            $post['dplist'] = selectlist('ref_down_payment', 'id_ref_down_payment', 'down_payment', null,
                $info['ref_down_payment'], '--- Chooice Down Payment ---', 'id_ref_down_payment', null, '%');
        } else {
            $post['dplist'] = selectlist('ref_down_payment', 'id_ref_down_payment', 'down_payment', null, null,
                '--- Chooice Down Payment ---', 'id_ref_down_payment', null, '%');
        }

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
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

        if (!auth_access_validation(adm_sess_usergroupid(), $this->ctrl)) {
            $this->error['warning'] = 'You can\'t manage this content.<br/>';
        }

        if ($post['asuransi'] == '') {
            $this->error['asuransi'] = 'Please insert Asuransi.<br/>';
        }
        if ($post['administrasi'] == '') {
            $this->error['administrasi'] = 'Please insert Administrasi.<br/>';
        }
        if ($post['provisi'] == '') {
            $this->error['provisi'] = 'Please insert Provisi.<br/>';
        }
        if ($post['bunga'] == '') {
            $this->error['bunga'] = 'Please insert Bunga.<br/>';
        }

        if ($post['ref_bank'] == '' || $post['ref_bank'] == 0) {
            $this->error['ref_bank'] = 'Please select Ref Bank.<br/>';
        }
        if ($post['ref_tenor'] == '' || $post['ref_tenor'] == 0) {
            $this->error['ref_tenor'] = 'Please select Ref Tenor.<br/>';
        }
        if ($post['ref_dp'] == '' || $post['ref_dp'] == 0) {
            $this->error['ref_dp'] = 'Please select Ref Down Payment.<br/>';
        }


        if ($this->Simulation_model->already_in($id, $post['ref_bank'], $post['ref_tenor'], $post['ref_dp'])) {
            $this->error['redudant'] = 'Credit Simulation already exists, please make different Credit Simulation.<br/>';
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
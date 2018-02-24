<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Techinal Consult Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Techinal Consult Management
 *********************************************/
class Tech_consult extends CI_Controller
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
        $this->ctrl = 'tech_consult';
        $this->template = getAdminFolder() . '/modules/tech_consult';
        $this->path_uri = getAdminFolder() . '/tech_consult';
        $this->path = site_url(getAdminFolder() . '/tech_consult');
        $this->title = get_admin_menu_title('tech_consult');
        $this->id_menu_admin = get_admin_menu_id('tech_consult');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->load->model(getAdminFolder() . '/Technical_model', 'Technical_model');
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

        $s_about = $this->uri->segment(4);
        $s_status = $this->uri->segment(5);
        $s_sdate = $this->uri->segment(6);
        $s_edate = $this->uri->segment(7);
        $pg = $this->uri->segment(8);
        $per_page = 25;
        $uri_segment = 5;
        $no = 0;
        $path = $this->path . '/main/a/b/c/d/';
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
        $search_query = '';

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if (strlen($s_about) > 1) {
            $s_about = substr($s_about, 1);

        } else {
            $s_about = "";

        }
        if (strlen($s_status) > 1) {
            $s_status = substr($s_status, 1);

        } else {
            $s_status = "";

        }
        if (strlen($s_sdate) > 1) {
            $s_sdate = substr($s_sdate, 1);

        } else {
            $s_sdate = "";

        }
        if (strlen($s_edate) > 1) {
            $s_edate = substr($s_edate, 1);
        } else {
            $s_edate = "";

        }
        $total_records = $this->Technical_model->TotalTechConsult(myUrlDecode($s_about), myUrlDecode($s_status),
            myUrlDecode($s_sdate), myUrlDecode($s_edate));
        $l_status = $this->list_status($s_status);

        if ($s_about) {
            $dis_urut = "none";
            $path = str_replace("/a/", "/a" . $s_about . "/", $path);
            ($search_query == '' ? $search_query .= $s_about : $search_query .= ' + ' . $s_about);
        }
        if ($s_status) {
            $dis_urut = "none";
            $path = str_replace("/b/", "/b" . $s_status . "/", $path);
            ($search_query == '' ? $search_query .= $s_status : $search_query .= ' + ' . $s_status);
        }
        if ($s_sdate) {
            $dis_urut = "none";
            $path = str_replace("/c/", "/c" . $s_sdate . "/", $path);
            ($search_query == '' ? $search_query .= $s_sdate : $search_query .= ' + ' . $s_sdate);
        }
        if ($s_edate) {
            $dis_urut = "none";
            $path = str_replace("/d/", "/d" . $s_edate . "/", $path);
            ($search_query == '' ? $search_query .= $s_edate : $search_query .= ' + ' . $s_edate);
        }

        if (!$pg) {
            $lmt = 0;
            $pg = 1;
        } else {
            $lmt = $pg;
        }
        $no = $lmt;

        $query = $this->Technical_model->GetAllTechConsult(myUrlDecode($s_about), myUrlDecode($s_status),
            myUrlDecode($s_sdate), myUrlDecode($s_edate), $lmt, $per_page);
        if (is_array($query)) {

            $query = $this->record_sort($query, 'stamp', true);

            foreach ($query as $buff) {
                $no++;
                $id = $buff['id_tech_consult'];
                $maker = $buff['maker'];
                $from = $buff['froms'];
                $title = $buff['about'];
                $text = word_limiter(strip_tags($buff['text']), 5);
                $location = $buff['location'];
                $stat = $buff['stat'];
                $stamp = $buff['stamp'];
                // $car			= db_get_one('view_cars_series',"concat(series)","id ='".$buff['ref_user_car']."'");

                $edit_href = site_url($path_uri . '/reply/' . $buff['id_tech_consult']);
                $list_data[] = array(
                    'no' => $no,
                    'id' => $id,
                    'maker' => $maker,
                    'from' => $from,/* 'car'=>$car, */
                    'stat' => $stat,
                    'stamp' => $stamp,
                    'title' => $title,
                    'text' => $text,
                    'edit_href' => $edit_href,
                    'location' => $location
                );
            }

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
            's_about' => $s_about,
            'l_status' => $l_status,
            's_status' => $s_status,
            's_sdate' => $s_sdate,
            's_edate' => $s_edate,
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
        $s_about = myUrlEncode(trim($this->input->post('s_about')));
        $s_status = myUrlEncode(trim($this->input->post('s_status')));
        $s_sdate = myUrlEncode(trim($this->input->post('s_sdate')));
        $s_edate = myUrlEncode(trim($this->input->post('s_edate')));
        redirect($this->path . '/main/a' . $s_about . '/b' . $s_status . '/c' . $s_sdate . '/d' . $s_edate);
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
                $return = $this->Technical_model->ChangePublishConsult($id);
                echo $return;
            }
        }
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
                    $this->Technical_model->DeleteTechComplain($id);
                    $id = implode(', ', $id);
                    #insert to log
                    $log_id_user = adm_sess_userid();
                    $log_id_group = adm_sess_usergroupid();
                    $log_action = 'Delete Tech Complain ID : ' . $id;
                    $log_desc = 'Delete Tech Complain ID : ' . $id . ';';
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
     * Message Reply
     */
    public function reply($Id)
    {
        auth_admin();
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($Id)) {
            $post = $this->input->post();
            $admid = adm_sess_userid();
            $data_post = array(
                'parent_id' => $post['parent'],
                'from_id' => $admid,
                'dest_id' => $post['dst'],
                'text' => strip_tags($post['text'], '<strong><strike><ol><ul><li>'),
                'consult_id' => $post['cons_id'],
                'type' => 're',
            );

            $client = $this->db->get_where('user', array('id_user' => $post['dst']))->row();
            $apn = $client->apn_id;
            $gcm = $client->gcm_id;
            $mail = $client->email;
            $ckey = 'message_replied';
            $message = 'You received new message on menu Technical Consultation';
//            echo var_dump($client); exit;
            if ($gcm != '') {
                $msg = array(
                    'message' => $message,
                    'description' => 'testing description'
                );
                send_gcm_async($mail, $gcm, $msg, $ckey);
            }
            if ($apn != '') {
                send_apn_async($apn, $message);
            }

            // insert data
            $id = $this->Technical_model->InsertTechConsult($data_post);

            #insert to log
            $log_id_user = $admid;
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . "; $this->ctrl From :" . $post['from'] . "; To:" . $post['dest'] . "; Msg:" . $post['text'] . ";$this->ctrl";
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', 'Message has been sent');
            redirect($this->path_uri);
        }
        $this->getForm($Id);
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
            $ico = $_FILES['thumb'];
            $now = date('Y-m-d H:i:s');
            $data_post = array(
                'title' => $post['title'],
                'content' => $post['content'],

            );

            // insert data
            $this->News_model->UpdateNews($data_post, $id);


            if ($ico['tmp_name'] != "") {
                $filename = 'news_' . $id . date('dmYHis');
                $content_to_db = trans_image_resize_to_folder($ico, $this->path_thumb, $filename,
                    $this->max_thumb_width, $this->max_thumb_height);

                $data_pic_content = array('thumb' => $content_to_db);

                // update to database
                $this->News_model->UpdateNews($data_pic_content, $id);
            }

            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . "; $this->ctrl Title :" . $post['title'] . "; $this->ctrl";
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
        $post = array();
        $cancel_btn = site_url($path_uri);
        $breadcrumbs = array();
        $list_data = array();
        $history = $heads = array();

        if (!$id) {
            $this->session->set_flashdata('info_msg', "Please try again with the right method");
            redirect($this->path_uri);
        }

        $history = $this->Technical_model->getAllConsultHistory($id);
        if ($history) {
            $heads = array(
                array(
                    'about' => $history[0]['about'],
                    // 'car'		=> get_user_car_by_id($history[0]['ref_user_car']),
                    // 'location'=> $history[0]['location']
                )
            );

            $post['parent_id'] = $history[0]['id_chat'];
            $post['dest_id'] = $history[0]['from_id'];
            $post['cons_id'] = $id;
        } else {
            $this->session->set_flashdata('info_msg', 'There is no record in our database.');
            redirect($path_uri);
        }

        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs = array(
            array(
                'text' => $menu_title,
                'href' => site_url($path_uri),
                'class' => ''
            ),
            array(
                'text' => 'Reply',
                'href' => '#',
                'class' => 'class="current"'
            )
        );


        $pass_msg = '';
        $err_c = '';
        $required = 'required';
        $action = site_url($path_uri . '/reply');
        $label = 'Reply';
        $no = 1;

        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }
        if (isset($this->error['text'])) {
            $error_msg['text'] = alert_box($this->error['text'], 'error');
        } else {
            $error_msg['text'] = '';
        }
        if (isset($this->error['stat'])) {
            $error_msg['stat'] = alert_box($this->error['stat'], 'error');
        } else {
            $error_msg['stat'] = '';
        }

        if ($this->input->post('text') != '') {
            $post['text'] = $this->input->post('text');
        } else {
            $post['text'] = '';
        }

        $post = array($post);
        $error_msg = array($error_msg);

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'history' => $history,
            'heads' => $heads,
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


        if ($post['text'] == '') {
            $this->error['text'] = 'Please insert Message field.<br/>';
        }

        if ($post['stat'] == 'closed') {
            $this->error['stat'] = 'this chat has been closed';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function list_status($selected = 'open')
    {
        $opt = "<option value=''>--- Status Thread ---</option>";
        foreach (array('all' => 'All', 'closed' => 'Closed', 'open' => 'Open') as $k => $v) {
            $terpilih = ($selected == $k) ? 'selected' : '';
            $opt .= "<option $terpilih value='$k'>$v</option>";
        }
        return $opt;
    }

    function record_sort($records, $field, $reverse = false)
    {
        $hash = array();
        foreach ($records as $record) {
            $hash[$record[$field]] = $record;
        }
        ($reverse) ? krsort($hash) : ksort($hash);
        $records = array();
        foreach ($hash as $record) {
            $records [] = $record;
        }

        return $records;
    }
}


/* End of file news.php */
/* Location: ./application/controllers/admpage/news.php */
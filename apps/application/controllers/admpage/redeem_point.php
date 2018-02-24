<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Redeem Point Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Redeem Point Management
 *********************************************/
class Redeem_point extends CI_Controller
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
        $this->ctrl = 'redeem_point';
        $this->template = getAdminFolder() . '/modules/redeem_point';
        $this->path_uri = getAdminFolder() . '/redeem_point';
        $this->path = site_url(getAdminFolder() . '/redeem_point');
        $this->title = get_admin_menu_title('redeem_point');
        $this->id_menu_admin = get_admin_menu_id('redeem_point');
        $this->is_superadmin = adm_is_superadmin(adm_sess_userid());
        $this->max_car_height = 105;
        $this->max_car_width = 148;
        $this->path_car = './uploads/redeem_point/';
        $this->load->model(getAdminFolder() . '/redeem_point_model', 'Redeem_Point');
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

        $this->global_libs->print_header();
        $this->load->helper('text');
        $cardid = $this->uri->segment(4);
        $uname = $this->uri->segment(5);
        $p_num = $this->uri->segment(6);
        $v_num = $this->uri->segment(7);
        $pg = $this->uri->segment(8);
        $per_page = 25;
        $uri_segment = 8;
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
        $breadcrumbs = $this->global_libs->getBreadcrumbs($file_app);

        $breadcrumbs[] = array(
            'text' => $menu_title,
            'href' => '#',
            'class' => 'class="current"'
        );

        $error_msg = alert_box($this->session->flashdata('error_msg'), 'error');
        $success_msg = alert_box($this->session->flashdata('success_msg'), 'success');
        $info_msg = alert_box($this->session->flashdata('info_msg'), 'warning');


        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $menu_title,
            // 's_location'		=> $s_location,
            // 'list_data' 		=> $list_data,
            'breadcrumbs' => $breadcrumbs,
            // 'pagination' 		=> $paging,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'info_msg' => $info_msg,
            'data' => '',
            'username' => '',
            'police_number' => '',
            'vin_number' => '',
            'card_id' => '',
            'file_app' => $file_app,
            'path_app' => $path_app,
            'add_btn' => $add_btn,
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = $this->input->post();
            $query = $this->Redeem_Point->GetAllRedeem($post['card_id'], $post['username'], $post['police_number'],
                $post['vin_number']);
            if ($query->num_rows() > 0) {
                $no = 0;
                $path_uri = $this->path_uri;
                foreach ($query->result_array() as $buf) {
                    $no++;
                    $id = $buf['id'];
                    $uname = $buf['username'];
                    $car = $buf['brands'] . ' ' . $buf['types'] . ' ' . $buf['series'] . ' ' . $buf['model']; // . ' ' . $buf['transmisi'] . ' ' . $buf['engine'] . ' ' . $buf['car_cc'] . ' ' .$buf['color'];
                    $pnumber = $buf['police_number'];
                    $vnumber = $buf['vin_number'];
                    $reward = $buf['point_reward'];
                    // $reward		= $buf['stnk_date'];

                    $edit_href = ($reward > 0) ? site_url($path_uri . '/edit/' . $id) : '#';
                    $list_data[] = array(
                        'no' => $no,
                        'id' => $id,
                        'uname' => $uname,
                        'car' => $car,
                        'reward' => $reward,
                        'pnumber' => $pnumber,
                        'vnumber' => $vnumber,
                        'edit_href' => $edit_href,
                        'hist' => '<a href="javascript:void(0)" onclick="show(\'' . $id . '\');">view</a>'
                    );
                }
                $dbuf = array(
                    'base_url' => base_url(),
                    'file_app' => $this->ctrl,
                    'menu_title' => $this->title,
                    'list_data' => $list_data,
                    'path_app' => $this->path,
                );

                $data = $this->parser->parse($this->template . '/list_item.html', $dbuf, true);
                echo $data, exit;
            }
        }
        echo '0', exit;
    }

    /**
     * history view
     */
    public function history()
    {
        auth_admin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = $this->input->post();
            $query = $this->Redeem_Point->GetAllRedeemHist($post['id']);
            if ($query->num_rows() > 0) {
                $no = 0;
                $path_uri = $this->path_uri;
                foreach ($query->result_array() as $buf) {
                    $no++;
                    $id = $buf['id'];
                    $id_redeem = $buf['id_redeem_point'];
                    $uname = $buf['username'];
                    $car = $buf['brands'] . ' ' . $buf['types'] . ' ' . $buf['series'] . ' ' . $buf['model']; // . ' ' . $buf['transmisi'] . ' ' . $buf['engine'] . ' ' . $buf['car_cc'] . ' ' .$buf['color'];
                    $pnumber = $buf['police_number'];
                    $vnumber = $buf['vin_number'];
                    $reward = $buf['point_reward'];
                    // $reward		= $buf['stnk_date'];
                    $notes = $buf['notes'];
                    $point = $buf['point'];
                    $last_point = $buf['lastpoint'];
                    $created = $buf['create_date'];

                    $list_user[0] = array(
                        'no' => $no,
                        'id' => $id,
                        'uname' => $uname,
                        'car' => $car,
                        'reward' => $reward,
                        'pnumber' => $pnumber,
                        'vnumber' => $vnumber,
                    );
                    $list_point[] = array(
                        'no' => $no,
                        'pnumber' => $pnumber,
                        'id_redeem' => $id_redeem,
                        'point' => $point,
                        'notes' => $notes,
                        'last_point' => $last_point,
                        'created' => $created,
                    );
                }
                $dbuf = array(
                    'base_url' => base_url(),
                    'file_app' => $this->ctrl,
                    'menu_title' => $this->title,
                    'list_user' => $list_user,
                    'list_point' => $list_point,
                    'path_app' => $this->path,
                );

                $data = $this->parser->parse($this->template . '/list_history.html', $dbuf, true);
                echo $data, exit;
            }
        }
        echo '0', exit;
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
            redirect($this->path_uri . '/edit/' . $id);

        }

        $post = purify($this->input->post());

        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm($id) && isset($post['type_list'])) {

            $now = date('Y-m-d H:i:s');
            // $point  = (isset($post['redeem_point']) && $post['redeem_point'] != '') ? $post['redeem_point'] : 2;

            // if($post['type_list'] == '1') {
            // $currentp = $post['current_point'] - $point;
            // $notes    = 'Redeem VIP Renewal';
            // $this->Redeem_Point->update_point($id,$currentp);

            // }else {
            // $currentp = $post['current_point'] - $point;
            // $notes    = $post['notes'];
            // $this->Redeem_Point->update_point($id,$currentp,'other');
            // }

            if ($post['type_list'] == '99999') {
                $point = $post['redeem_point'];
                $currentp = $post['current_point'] - $point;
                $notes = $post['notes'];
                $this->Redeem_Point->update_point($id, $currentp, 'other');
            } else {
                $ref = $this->db->get_where('ref_redeem_point', array('id_ref_redeem_point' => $post['type_list']));
                if ($ref->num_rows() > 0) {
                    $ref = $ref->row();
                    $point = $ref->point;
                    $slug = ($ref->reward == 'Redeem VIP Renewal') ? 'renewal' : 'nope';
                    $currentp = $post['current_point'] - $point;
                    $notes = $ref->reward;
                    $this->Redeem_Point->update_point($id, $currentp, $slug);
                }
            }

            $data = array(
                'ref_user_car' => $id,
                'notes' => $notes,
                'point' => $point,
                'lastpoint' => $post['current_point']
            );

            $id = $this->Redeem_Point->insertHistory($data);


            #insert to log
            $log_id_user = adm_sess_userid();
            $log_id_group = adm_sess_usergroupid();
            $log_action = 'Add ' . $this->title . ' ID : ' . $id;
            $log_desc = 'Add ' . $this->title . ' ID : ' . $id . '; Redeem :' . $notes;
            $data_logs = array(
                'id_user' => $log_id_user,
                'id_group' => $log_id_group,
                'action' => $log_action,
                'desc' => $log_desc,
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_logs);

            $this->session->set_flashdata('success_msg', $this->title . ' has been updated');

            redirect($this->path_uri . '/edit/' . $get_id);
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
        $info = array();
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
            $query = $this->Redeem_Point->GetRedeemById($id);
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
            // $Post['id'] = 0;
        }


        // set error
        if (isset($this->error['warning'])) {
            $error_msg['warning'] = alert_box($this->error['warning'], 'warning');
        } else {
            $error_msg['warning'] = '';
        }

        if (isset($this->error['cpoint'])) {
            $error_msg['cpoint'] = alert_box($this->error['cpoint'], 'error');
        } else {
            $error_msg['cpoint'] = '';
        }


        // set value
        if ($this->input->post('type_list') != '') {
            $post['typelst'] = $this->list_type_redeem($this->input->post('type_list'));
        } elseif ((int)$id > 0) {
            $post['typelst'] = $this->list_type_redeem();
        } else {
            $post['typelst'] = $this->list_type_redeem();
        }
        if ($this->input->post('notes') != '') {
            $post['notes'] = $this->input->post('notes');
        } elseif ((int)$id > 0) {
            $post['notes'] = '';
        } else {
            $post['notes'] = '';
        }
        if ($this->input->post('current_point') != '') {
            $post['current_point'] = $this->input->post('current_point');
        } elseif ((int)$id > 0) {
            $post['current_point'] = $info['point_reward'];
        } else {
            $post['current_point'] = '';
        }

        if ($this->input->post('redeem_point') != '') {
            $post['redeem_point'] = $this->input->post('current_point');
        } elseif ((int)$id > 0) {
            $post['redeem_point'] = '';//$info['point_reward'];
        } else {
            $post['redeem_point'] = '';
        }


        $post = array($post);
        $info = array($info);
        $error_msg = array($error_msg);
        $success_msg = alert_box($this->session->flashdata('success_msg'), 'success');

        $data = array(
            'base_url' => base_url(),
            'menu_title' => $menu_title,
            'post' => $post,
            'info' => $info,
            'action' => $action,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'breadcrumbs' => $breadcrumbs,
            'file_app' => $file_app,
            'path_app' => $path_app,
            'cancel_btn' => $cancel_btn,

        );
        $this->parser->parse($template . '/list_' . $file_app . '_form.html', $data);
        $this->global_libs->print_footer();
    }

    private function list_type_redeem($selected = '')
    {
        $d = "<option value='0'> --- Choice Type --- </option>";
        $ref = $this->db->get_where('ref_redeem_point', array('ref_publish' => 1));
        if ($ref->num_rows() > 0) {
            $refs = $ref->result_array();
            foreach ($refs as $k => $v) {
                $select = ($selected == $v['id_ref_redeem_point']) ? 'selected' : '';
                $d .= "<option $select value='{$v['id_ref_redeem_point']}' point='{$v['point']}' >{$v['reward']}</option>";
//				if(!isset($refs[$k+1])) $d .= "<option $select value='99999' >Others</option>";
            }
            // foreach(array(1=>'VIP Renewal','Others') as $k => $v) {
            // $select = ($selected == $k) ? 'selected' : '';
            // $d .= "<option $select value='$k' >$v</option>";
            // }
        }
        return $d;
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

        if (isset($post['redeem_point'])) {
            if ($post['current_point'] < $post['redeem_point']) {
                $this->error['cpoint'] = 'Your Point is not enough';
            }
        } else {
            if ($post['current_point'] < 2) {
                $this->error['cpoint'] = 'Your Point is not enough';
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function print_history()
    {
        $id = $this->input->get('id');
        $data = $this->db->from('redeem_point a')->join('view_user_cars b', 'a.ref_user_car = b.id', 'inner')
            ->where('a.id_redeem_point', $id)->get()->row_array();
        $data['print_date'] = date('d M Y');

        $user_sess = $this->session->userdata('ADM_SESS');
        $data['cms_user'] = $user_sess['admin_name'];

        $this->parser->parse($this->template . '/print.html', $data);
    }


}


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
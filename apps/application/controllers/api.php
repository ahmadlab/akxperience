<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
//error_reporting(E_ALL);
/**
 * Global API Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Global API Management
 *********************************************/
class Api extends CI_Controller
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_model', 'Api_model');
        $this->load->model('apns_model');
        // echo BASEPATH;

    }

    public function index()
    {

    }

    public function homepage()
    {
        $mail = $this->input->get('mail');
        $html = $stat = 'fail';
        if (!$mail) {
            echo $html, exit;
        }
        $ucars = $this->Api_model->fetch_user_cars($mail);
        if ($ucars) {
            $html = $ucars;
            $stat = 'Ok';
        }
        echo json_encode(array('buff' => $html, 'status' => $stat));
    }

    public function user_login()
    {
        $get = $this->input->get();
        $html = json_encode(array('buff' => array('status' => 'fail')));
        if (isset($get['mail']) && isset($get['pass'])) {
            $exist = $this->Api_model->check_login($get['mail'], $get['pass']);
            if ($exist) {
                if ($exist['status'] == '1') {
                    $exist['status'] = 'ok';
                    $html = json_encode(array('buff' => $exist));
                }
            }
        }
        echo $html;
    }

    public function is_login()
    {
        $uname = get('uname', 0);
        if ($uname) {
            if ($this->Api_model->is_login($uname)) {
                echo 'true', exit;
            }
        }
        echo 'false';
    }

    public function logout()
    {
        $uname = get('uname', 0);
        if ($uname) {
            $this->Api_model->logout($uname);
        }
    }


    public function registration()
    {
        $response = 'fail';
        $stat = '500';
        $post = $this->input->get();
        if (isset($post['card_id']) && $post['username'] && isset($post['password']) && isset($post['email']) && isset($post['address'])
            && isset($post['birthday']) && isset($post['phone_number']) && isset($post['religion'])
            && isset($post['sex']) /* && isset($post['avatar'])  && isset($post['user_type']) && isset($post['ref_location'])  */
        ) {
            list($y, $m, $d) = explode('-', $post['birthday']);
            // list($d,$m,$y) = explode('-',$post['birthday']);
            $m = ($m < 10) ? '0' . $m : $m;
            $d = ($d < 10) ? '0' . $d : $d;

            $mailexist = db_get_one('user', 'email', "email = '" . $post['email'] . "'");

            $now = date('Y-m-d H:i:s');
            $ttl = strtotime($now . '+14days');

            if ($mailexist == '0') {
                $this->load->library('encrypt');
                $this->load->helper('mail');

                $pass = $this->encrypt->encode($post['password']);

                if (!isset($post['province'])) {
                    $post['province'] = '';
                }
                if (!isset($post['city'])) {
                    $post['city'] = '';
                }

                $data = array(
                    'username' => $post['username'],
                    'password' => $pass,
                    'address' => $post['address'],
                    'city' => $post['city'],
                    'nearest_workshop' => $post['nearest_workshop'],
                    'email' => $post['email'],
                    'birthday' => "$y-$m-$d",
                    'phone_number' => $post['phone_number'],
                    'religion' => $post['religion'],
                    // 'ref_location'	=> $post['ref_location'],
                    'card_id' => $post['card_id'],
                    'sex' => $post['sex'],
                    // 'avatar'	  		=> $post['avatar'],
                    // 'user_type'	  	=> $post['user_type']
                    'create_date' => $now,
                    'activation_code' => $ttl,

                );

                if (isset($post['os_key']) && isset($post['device_key'])) {
                    $data['os_key'] = $post['os_key'];
                    $data['device_key'] = $post['device_key'];
                }

                if (!$this->Api_model->regist($data)) {
                    $stat = '200';
                    // $response = 'successfull registered';
                    $response = 'Please check your email to activate';
                    $slug = ($post['username']) ? $post['username'] : $post['email'];
                    $link = site_url('api/user_activation/?mail=' . $post['email'] . '&activation_code=' . $ttl);
                    $conf['from_name'] = $slug;
                    $conf['subject'] = 'Auto Kencana Experience Activation';
                    $conf['to'] = $post['email'];
                    $conf['content'] = "Dear Mr/Mrs " . $slug . ",<br>";
                    $conf['content'] .= "Your login to Auto Kencana Experience Mobile Application not yet activated,<br>";
                    $conf['content'] .= "to activate please click the following URL*<br>";
                    $conf['content'] .= "<a href='$link'>$link</a> <br><br>";
                    $conf['content'] .= "*URL will be expired within 2 (two) weeks<br><br>";
                    $conf['content'] .= "Best Regards,<br>";
                    $conf['content'] .= "Kreasi Auto Kencana, PT<br>";

                    sent_mail($conf);
                } else {
                    $response = 'Register fail, please try again';
                }

            } else {
                $check = $this->db->get_where('user', array('email' => $post['email']));
                if ($check->num_rows() > 0) {
                    $check = $check->row_array();
                    if ($check['status'] == '0') {
                        $this->db->where('id_user', $check['id_user'])->update('user',
                            array('activation_code' => $ttl));

                        $response = 'Please Activate, we resend you the activation link to your email, please check';
                        $slug = ($post['username']) ? $post['username'] : $post['email'];
                        $link = site_url('api/user_activation/?mail=' . $post['email'] . '&activation_code=' . $ttl);
                        $conf['from_name'] = $slug;
                        $conf['subject'] = 'Auto Kencana Experience Activation';
                        $conf['to'] = $post['email'];
                        $conf['content'] = "Dear Mr/Mrs " . $slug . ",<br>";
                        $conf['content'] .= "Your login to Auto Kencana Experience Mobile Application not yet activated,<br>";
                        $conf['content'] .= "to activate please click the following URL*<br>";
                        $conf['content'] .= "<a href='$link'>$link</a> <br><br>";
                        $conf['content'] .= "*URL will be expired within 2 (two) weeks<br><br>";
                        $conf['content'] .= "Best Regards,<br>";
                        $conf['content'] .= "Kreasi Auto Kencana, PT<br>";

                        sent_mail($conf);

                    } else {
                        $response = 'Email is Existed, please continue to login or forgot password';
                    }
                }
            }
        }
        echo json_encode(array('buff' => $response, 'status' => $stat));
    }

    public function reg_ios()
    {
        $response = 'fail';
        $post = $this->input->get();
        if (isset($post['pass']) && $post['email'] && isset($post['full_name'])) {
            $mailexist = db_get_one('user', 'email', "email = '" . $post['email'] . "'");
            $stat = true;

            // $mailcheck  = $this->db->get_where('user',array('email' => $post['email']));
            // $mailcheck  = $this->db_get_one('user','email','email' => $post['email']));

            // if($mailcheck->num_rows()>0) {
            // foreach($mailcheck->result_array() as $k => $v) {
            // if($v['status'] == '1') {
            // $stat = false;
            // }
            // }
            // }

            if ($mailexist == '0') {
                $this->load->library('encrypt');
                $this->load->helper('mail');
                $now = date('Y-m-d H:i:s');
                $ttl = strtotime($now . '+14days');
                $pass = $this->encrypt->encode($post['pass']);

                $data = array(
                    'username' => $post['full_name'],
                    'password' => $pass,
                    'email' => $post['email'],
                    'activation_code' => $ttl,
                    'create_date' => $now
                );

                if (isset($post['os_key']) && isset($post['device_key'])) {
                    $data['os_key'] = $post['os_key'];
                    $data['device_key'] = $post['device_key'];
                }

                if (!$this->Api_model->regist($data)) {
                    $response = 'Please check your email to activate';
                    $slug = ($post['full_name'] != '' && $post['full_name'] != '-') ? $post['full_name'] : $post['email'];
                    $link = site_url('api/user_activation/?mail=' . $post['email'] . '&activation_code=' . $ttl);
                    $conf['from_name'] = $slug;
                    $conf['subject'] = 'Auto Kencana Experience Activation';
                    $conf['to'] = $post['email'];
                    $conf['content'] = "Dear Mr/Mrs " . $slug . ",<br>";
                    $conf['content'] .= "Your login to Auto Kencana Experience Mobile Application not yet activated,<br>";
                    $conf['content'] .= "to activate please click the following URL*<br>";
                    $conf['content'] .= "<a href='$link'>$link</a> <br><br>";
                    $conf['content'] .= "*URL will be expired within 2 (two) weeks<br><br>";
                    $conf['content'] .= "Best Regards,<br>";
                    $conf['content'] .= "Kreasi Auto Kencana, PT<br>";

                    sent_mail($conf);

                } else {
                    $response = 'Register fail, please try again';
                }

            } else {
                $response = 'This Email has been registered, please continue to login or forgot password.';
            }
        }
        echo json_encode(array('buff' => $response));
    }


    public function user_profile()
    {
        $response = json_encode(array('buff' => 'fail'));
        $post = $this->input->get('mail');
        if ($post) {
            $profile = $this->Api_model->fetch_user_profile($post);
            if ($profile) {
                unset($profile['password']);
                $response = json_encode(array('buff' => $profile));
            }
        }
        echo $response;
    }

    public function store_user_profile()
    {
        $get = $this->input->get();
        $resp = false;
        if (isset($get['cardid']) && isset($get['uname']) && isset($get['mail'])
            && isset($get['addr']) && isset($get['phone']) && isset($get['birthday'])
            && isset($get['religion']) && isset($get['sex'])
        ) {
            // list($d,$m,$y) = explode('-',$get['birthday']);
            list($y, $m, $d) = explode('-', $get['birthday']);
            $m = ($m < 10) ? '0' . $m : $m;
            $d = ($d < 10) ? '0' . $d : $d;

            $buf = array(
                'card_id' => $get['cardid'],
                'username' => $get['uname'],
                'email' => $get['mail'],
                'birthday' => "$y-$m-$d",
                'phone_number' => $get['phone'],
                'address' => $get['addr'],
                'city' => $get['city'],
                'nearest_workshop' => $get['nearest_workshop'],
                'religion' => $get['religion'],
                'sex' => $get['sex']
            );


            if (isset($get['userid'])) {
                $this->db->where('id_user', $get['userid'])->update('user', $buf);
                if ($this->db->_error_message()) {
                    $resp = $this->db->_error_message();
                } else {
                    $resp = 'Successfull Updated';
                }
            } else {
                if (!db_get_one('user', 'email', "email = '" . $get['mail'] . "'")) {
                    $this->db->insert('user', $buf);
                    if ($this->db->_error_message()) {
                        $resp = $this->db->_error_message();
                    } else {
                        $resp = 'Successfull Added';
                    }
                }
            }
        }

        echo json_encode(array('buff' => $resp));
    }

    /*
     * For All Brands Only
    */
    public function c_brand()
    {
        $response = json_encode(array('buff' => 'fail'));
        $brand = $this->db->where('ref_publish', 1)
            ->get('car_brands');
        if ($brand->num_rows() > 0) {
            $response = json_encode(array('buff' => $brand->result_array()));
        }
        echo $response;
    }

    /*
     * For All Types or By Brands Only
    */
    public function c_model()
    {
        $brand = $this->input->get('brand');
        $response = json_encode(array('buff' => 'fail'));

        $this->db->where('ref_publish', 1);
        if ($brand) {
            $this->db->where('id_brands', $brand);
        }

        $types = $this->db->get('car_types');
        if ($types->num_rows() > 0) {
            $response = json_encode(array('buff' => $types->result_array()));
        }
        echo $response;
    }

    public function c_series()
    {
        $type = $this->input->get('type');
        $response = json_encode(array('buff' => 'fail'));

        $this->db->select('id,series,model,transmisi,car_cc')
            ->where('ref_publish', 1);
        if ($type) {
            $this->db->where('id_type', $type);
        }

        $series = $this->db->get('view_cars');

        if ($series->num_rows() > 0) {
            $series = $series->result_array();
            foreach ($series as $k => $v) {
                $series[$k]['thumb'] = base_url() . DEFAULT_CAR_IMG;

                $icolor = db_get_one('car_colors', 'car_color_thumb', "id_car = '" . $v['id'] . "'");
                if ($icolor) {
                    if (file_exists('./uploads/cars/colors/' . $icolor)) {
                        $series[$k]['thumb'] = base_url() . 'uploads/cars/colors/' . $icolor;
                    }
                }
            }

            $response = json_encode(array('buff' => $series));
        }
        echo $response;
    }

    public function news()
    {
        $response = json_encode(array('buff' => 'fail'));
        $post = $this->input->get('limit');
        $type = $this->input->get('type');
        $pg = $this->input->get('pg');

        // $post = ($post == '') ? 'all' : $post;

        $type = ($type == '') ? 'news' : $type;

        if ($post) {
            $pg = ($pg == '') ? 0 : $pg;

        } else {
            $post = 'all';

            $pg = false;
        }

        if ($post && $type) {
            $total = $this->Api_model->fetch_total_news($post, $pg, $type);
            if ($post == 'all') {
                $pgs = 0;
            } else {
                $pgs = $post * $pg;

            }

            $news = $this->Api_model->fetch_news($post, $pgs, $type);

            $last_item = @ceil($total / $post) - $pg - 1;
            $last_item = ($last_item == '0' || $last_item < '0') ? 'end' : $last_item;

            if ($news) {
                $response = json_encode(array('buff' => $news, 'last_item' => "$last_item"));
            }
        }
        echo $response;
    }

    public function contact()
    {
        echo json_encode(array('buff' => $this->Api_model->fetch_contact()));
    }

    public function car_model()
    {
        $response = json_encode(array('buff' => 'fail'));
        $model = $this->Api_model->fetch_car_model();
        if ($model) {
            $response = json_encode(array('buff' => $model));
        }
        echo $response;
    }

    public function car_spare_part()
    {
        $model = $this->input->get('model');
        $part = $this->input->get('part');


        $response = json_encode(array('buff' => 'fail'));
        if (!$model) {
            echo $response, exit;
        }

        $sp = $this->Api_model->fetch_car_spare_part($model, $part);
        if ($sp) {
            $response = json_encode(array('buff' => $sp));
        }
        echo $response;
    }

    public function car_accessories()
    {
        $model = $this->input->get('model');
        $asc = $this->input->get('acs');
        $vendor = $this->input->get('vendor');

        $response = json_encode(array('buff' => 'fail', 'status' => 'fail'));
        if (!$model) {
            echo $response, exit;
        }

        $sp = $this->Api_model->fetch_car_accessories($model, $vendor, $asc);
        if ($sp) {
            $response = json_encode(array('buff' => $sp, 'status' => 'Ok'));
        }
        echo $response;
    }

    public function acc_price()
    {
        $get = $this->input->get();
        $resp = array('buff' => 'fail');
        if (isset($get['acc_id']) && isset($get['vendor_id'])) {
            $price = $this->db->from('acc_price')
                ->select('thumb, acc_price.price, location')
                ->where('acc_id', $get['acc_id'])
                ->where('vendor_id', $get['vendor_id'])
                ->where('ref_location.is_published', '1')
                ->join('car_accessories', 'car_accessories.id_car_accessories = acc_price.acc_id')
                ->join('ref_location', 'ref_location.id_ref_location = acc_price.branch_id')
                ->order_by('location')
                ->get()->result_array();
            if (count($price) > 0) {
                $acc_price['image'] = base_url() . 'uploads/accessories/' . $price[0]['thumb'];

                foreach ($price as $v) {
                    $acc_price['price_list'][] = array(
                        'branch' => $v['location'],
                        'price' => $v['price'],
                    );
                }

                $resp['buff'] = $acc_price;
            }
        }
        echo json_encode($resp);

    }

    public function all_workshop()
    {
        $response = json_encode(array('buff' => 'fail'));
        $get = $this->input->get();
        $code = 'ak';
        if (isset($get['companycode'])) {
            $code = $get['companycode'];
        }
        if (isset($get['is_published'])) {
            $is_published = $get['is_published'];
        } else {
            $is_published = -1;
        }
        $ws = $this->Api_model->fetch_all_workshop($code, $is_published);
        if ($ws) {
            $response = json_encode(array('buff' => $ws));
        }

        echo $response;
    }

    public function user_cars()
    {
        $get = $this->input->get();
        $resp = $status = 'fail';
        if (isset($get['mail'])) {
            $id = db_get_one('user', 'id_user', "email = '" . $get['mail'] . "' and user_type = 'VIP'");
            if ($id) {
                $sql = "select id, concat(police_number,' - ',types,' ',series,' (',color,')')as model FROM jdi_view_user_cars ";
                $sql .= "where id_user IN ($id) ";

                $ucars = $this->db->query($sql);
                if ($ucars->num_rows() > 0) {
                    $resp = $ucars->result_array();
                    $status = 'Ok';
                }
            }
        }
        echo json_encode(array('buff' => $resp, 'status' => $resp));
    }

    public function workshop_service()
    {
        $get = $this->input->get();
        $resp = 'fail';
        if (isset($get['service_type'])) {
            $this->db->where_in('id_service_type', array($get['service_type']));
            $this->db->where('ref_publish', '1');
            $service = $this->db->get('car_services');
            if ($service->num_rows() > 0) {
                $buff['servicelst'] = $service->result_array();
            }

            // $buff['date'] = get_date_list(date('Y-m-d',strtotime('+1days')),date('Y-m-d',strtotime('+8days')) );


            foreach (get_date_list(date('Y-m-d', strtotime('+1days')),
                date('Y-m-d', strtotime('+8days'))) as $k => $v) {
                $buff['date'][] = date('Y-m-d', strtotime($v));
            }


            foreach (get_available_time() as $k => $v) {
                $buff['time'][] = str_replace('.', ':', $v);
            }

            $resp = $buff;
        }

        echo json_encode(array('buff' => $resp));
    }

    public function get_time_by_date()
    {
        $get = $this->input->get();
        $resp = $status = 'false';
        if (isset($get['date']) && isset($get['type']) && isset($get['locate'])) {
            // list($y,$m,$d) = explode('-',$get['date']);
            // $m 	  = ($m >= 10) ? $m : '0'.$m;
            // $d 	  = ($d >= 10) ? $d : '0'.$d;
            // foreach( get_time_by_date($get['type'],"$y-$m-$d",$get['locate']) as $v) {

            // foreach( get_time_by_date($get['type'],$get['date'],$get['locate']) as $v) {
            // $buff[] = $v;
            // }
            $buff = get_time_by_daten($get['type'], $get['date'], $get['locate']);
            // $buff = array_values($dates);

            $resp = $buff;
            $status = 'Ok';
        }
        echo json_encode(array('buff' => $resp, 'status' => $status));
    }

    public function get_time_by_date_old()
    {
        $get = $this->input->get();
        $resp = $status = 'false';
        if (isset($get['date']) && isset($get['type']) && isset($get['locate'])) {
            // list($y,$m,$d) = explode('-',$get['date']);
            // $m 	  = ($m >= 10) ? $m : '0'.$m;
            // $d 	  = ($d >= 10) ? $d : '0'.$d;
            // foreach( get_time_by_date($get['type'],"$y-$m-$d",$get['locate']) as $v) {

            // foreach( get_time_by_date($get['type'],$get['date'],$get['locate']) as $v) {
            // $buff[] = $v;
            // }
            $buff = get_time_by_daten($get['type'], $get['date'], $get['locate']);
            // $buff = array_values($dates);

            $resp = $buff;
            $status = 'Ok';
        }
        echo json_encode(array('buff' => $resp, 'status' => $status));
    }

    public function consult_index()
    {
        $mail = $this->input->get('mail');
        $buff = $status = 'fail';
        if ($mail) {
            $threads = $this->Api_model->consult_thread($mail);

            if ($threads) {
                $buff = $threads;
                $status = 'Ok';
            }
        }
        echo json_encode(array('buff' => $buff, 'status' => $status));
    }

    public function consult_detail()
    {
        $thread = $this->input->get('threadid');
        $resp = json_encode(array('buff' => 'fail', 'status' => 'fail'));
        if (!$thread) {
            echo $resp, exit;
        }

        $detail = $this->Api_model->consult_detail($thread);

        if ($detail) {
            $resp = json_encode(array('buff' => $detail, 'status' => 'Ok'));

        }
        echo $resp, exit;

    }

    public function complain_index()
    {
        $mail = $this->input->get('mail');
        $buff = $status = 'fail';
        if ($mail) {
            $threads = $this->Api_model->complain_thread($mail);
            if ($threads) {
                $buff = $threads;
                $status = 'Ok';
            }
        }
        echo json_encode(array('buff' => $buff, 'status' => $status));
    }

    public function complain_detail()
    {
        $thread = $this->input->get('threadid');
        $resp = json_encode(array('buff' => 'fail', 'status' => 'fail'));
        if (!$thread) {
            echo $resp, exit;
        }

        $detail = $this->Api_model->complain_detail($thread);

        if ($detail) {
            $resp = json_encode(array('buff' => $detail, 'status' => 'Ok'));

        }
        echo $resp, exit;
    }

    public function store_complain()
    {
        $get = $this->input->get();
        $resp = 'false';
        $status = 'fail';
        if (isset($get['about']) && isset($get['user_car']) && isset($get['location']) && $get['parent'] == '0' && isset($get['from']) && isset($get['text'])) {

            $complain['about'] = $get['about'];
            $complain['ref_user_car'] = $get['user_car'];
            $complain['ref_location'] = $get['location'];

            $this->db->insert('tech_complain', $complain);

            if (!$this->db->_error_message()) {

                $from = db_get_one('user', 'id_user', "email = '" . $get['from'] . "'");

                if ($from) {

                    $chat['complain_id'] = $this->db->insert_id();
                    $chat['parent_id'] = $get['parent'];
                    $chat['text'] = $get['text'];
                    $chat['dest_id'] = 1;
                    $chat['from_id'] = $from;

                    $this->db->insert('chat', $chat);

                    if (!$this->db->_error_message()) {
                        $resp = 'Our staff will reply your message very soon';
                        $status = 'Ok';
                    } else {
                        $resp = $this->db->_error_message();
                    }
                }

            } else {
                $resp = $this->db->_error_message();

            }

        } elseif (isset($get['parent']) && $get['parent'] != '0' && isset($get['from']) && isset($get['text']) && isset($get['complain_id'])) {
            if (db_get_one('tech_complain', 'id_tech_complain',
                "id_tech_complain = '" . $get['complain_id'] . "' and stat = 'open'")) {

                $from = db_get_one('user', 'id_user', "email = '" . $get['from'] . "'");

                if ($from) {

                    $chat['complain_id'] = $get['complain_id'];
                    $chat['parent_id'] = $get['parent'];
                    $chat['text'] = $get['text'];
                    $chat['dest_id'] = 1;
                    $chat['from_id'] = $from;
                    // $chat['parent_id'] 	 = $get['parent_id'];

                    $this->db->insert('chat', $chat);

                    if (!$this->db->_error_message()) {
                        $resp = 'successfuly sent';
                        $status = 'Ok';
                    } else {
                        $resp = $this->db->_error_message();
                    }

                } else {
                    $resp = 'this email not related with this thread!';
                }

            } else {
                $resp = 'wrong complain!';
            }
        }

        echo json_encode(array('buff' => $resp, 'status' => $status));
    }

    public function store_consult()
    {
        $get = $this->input->get();
        $resp = 'false';
        $status = 'fail';
        if (isset($get['about']) && isset($get['user_car']) && isset($get['location']) && $get['parent'] == '0' && isset($get['from'])) {

            $cons['about'] = $get['about'];
            $cons['ref_location'] = $get['location'];
            $cons['ref_user_car'] = $get['user_car'];

            $this->db->insert('tech_consult', $cons);

            if (!$this->db->_error_message()) {

                $from = db_get_one('user', 'id_user', "email = '" . $get['from'] . "'");

                if ($from) {
                    $chat['consult_id'] = $this->db->insert_id();
                    $chat['parent_id'] = $get['parent'];
                    $chat['text'] = $get['text'];
                    $chat['dest_id'] = 1;
                    $chat['from_id'] = $from;

                    $this->db->insert('chat', $chat);
                    if (!$this->db->_error_message()) {
                        $resp = 'Our staff will reply your message very soon';
                        $status = 'Ok';
                    } else {
                        $resp = $this->db->_error_message();
                    }

                } else {
                    $resp = 'this email not related with this thread!';
                }

            } else {
                $resp = 'wrong consult!';
            }


        } elseif (isset($get['parent']) && $get['parent'] != '0' && isset($get['from']) && isset($get['text']) && isset($get['consult_id'])) {
            if (db_get_one('tech_consult', 'id_tech_consult',
                "id_tech_consult = '" . $get['consult_id'] . "' and stat = 'open'")) {

                $from = db_get_one('user', 'id_user', "email = '" . $get['from'] . "'");

                if ($from) {

                    $chat['consult_id'] = $get['consult_id'];
                    $chat['parent_id'] = $get['parent'];
                    $chat['text'] = $get['text'];
                    $chat['dest_id'] = 1;
                    $chat['from_id'] = $from;

                    $this->db->insert('chat', $chat);
                    if (!$this->db->_error_message()) {
                        $resp = 'successfuly sent';
                        $status = 'Ok';
                    } else {
                        $resp = $this->db->_error_message();
                    }

                } else {
                    $resp = 'this email not related with this thread!';
                }

            } else {
                $resp = 'wrong consult!';
            }
        }

        echo json_encode(array('buff' => $resp, 'status' => $status));
    }

    public function bank_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $bank = $this->db->select('id_ref_bank as id,bank_name,bank_thumb')->order_by('sort', 'asc')
                ->get_where('ref_bank', "ref_publish = '1'");
            if ($bank->num_rows() > 0) {
                $bank = $bank->result_array();
                foreach ($bank as $k => $v) {
                    $bank[$k]['bank_thumb'] = ($v['bank_thumb'] != '' && file_exists('./uploads/bank_account/' . $v['bank_thumb'])) ? substr(base_url() . 'uploads/bank_account/' . $v['bank_thumb'],
                        0, -4) : '';
                }
                $resp = $bank;
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function vendor_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $vendor = $this->db->select('id, name, thumb')->order_by('urut', 'asc')
                ->get_where('ref_vendor', "is_published = '1'");
            if ($vendor->num_rows() > 0) {
                $vendor = $vendor->result_array();
                foreach ($vendor as $k => $v) {
                    $vendor[$k]['thumb'] = ($v['thumb'] != '' && file_exists('./uploads/vendor/' . $v['thumb'])) ? base_url() . 'uploads/vendor/' . $v['thumb'] : '';
                }
                $resp = $vendor;
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function vendor()
    {
        $resp = false;
        $model = $this->input->get('model');
        $item = $this->input->get('item');

        if ($model != '' && $item != '') {
            $this->db->where('id_type', $model);
            $this->db->where('id_ref_accessories', $item);
            $this->db->join('ref_vendor', 'car_accessories.id_vendor = ref_vendor.id');
            $acc = $this->db->get('car_accessories')->result_array();

            if (count($acc) > 0) {
                foreach ($acc as $v) {
                    $resp[] = array(
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'thumb' => ($v['thumb'] != '' && file_exists('./uploads/vendor/' . $v['thumb'])) ? base_url() . 'uploads/vendor/' . $v['thumb'] : '',
                    );
                }
            }
        }

        echo json_encode(array('buff' => $resp));
    }

    public function branch_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            if ($this->input->get('is_published') != '') {
                if ($this->input->get('is_published') == 1) {
                    $this->db->where('is_published', '1');
                } elseif ($this->input->get('is_published') == 0) {
                    $this->db->where('is_published', '0');
                }
            }
            $this->db->select('id_ref_location, location');
            $this->db->where('company_code', 'AK');
            $this->db->order_by('sort', 'asc');
            $branch = $this->db->get('ref_location');

            if ($branch->num_rows() > 0) {
                $resp = $branch->result_array();
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function city_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $province = $this->db->get('ref_city');
            if ($province->num_rows() > 0) {
                $resp = $province->result_array();
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function province_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $province = $this->db->get('ref_province');
            if ($province->num_rows() > 0) {
                $resp = $province->result_array();
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function dp_list()
    {
        $resp = 'fail';
        if ($this->input->get('i')) {
            $resp = $this->db->order_by('down_payment', 'asc')->get('ref_down_payment')->result_array();
            $resp = array_map(function ($a) {
                $a['id_ref_down_payment'] = $a['id_ref_down_payment'];
                $a['down_payment'] = $a['down_payment'] . ' %';
                return $a;
            }, $resp);
        }
        echo json_encode(array('buff' => $resp));
    }

    public function tenor_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $tenor = $this->db->order_by('tenor', 'asc')->get('ref_tenor');
            if ($tenor->num_rows() > 0) {
                $tenor = $tenor->result_array();
                foreach ($tenor as $k => $v) {
                    $tenor[$k]['tenor'] = $v['tenor'] . ' Tahun';
                }
                $resp = $tenor;
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function car_list()
    {
        $resp = 'fail';
        if ($this->input->get('i')) {
            $cars = $this->db->order_by('brands, types, series, model asc')->get_where('view_cars',
                "ref_publish = '1'");
            if ($cars->num_rows() > 0) {
                foreach ($cars->result_array() as $k => $v) {
                    $buff[$k]['id'] = $v['id'];
                    $buff[$k]['label'] = $v['types'] . ' ' . $v['series'] . ' ' . $v['model'] . ' ' . $v['engine'] . ' ' . $v['transmisi'];
                    $buff[$k]['otrk'] = $v['car_price'];
                    $buff[$k]['otrv'] = rp($v['car_price']);
                    $resp = $buff;
                }
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function cs_get_tenor()
    {
        $get = $this->input->get();
        $resp = json_encode(array('buff' => 'fail'));

        if ($get['bank'] != '') {
            $this->db->where('ref_bank', $get['bank']);
            $this->db->select('ref_tenor');
            $this->db->distinct();
            $this->db->order_by('ref_tenor', 'asc');
            $tenor = $this->db->get('credit_simulation');

            if ($tenor->num_rows() > 0) {
                $tenor = $tenor->result_array();
                foreach ($tenor as $v) {
                    $list[] = array(
                        'id' => $v['ref_tenor'],
                        'tenor' => $v['ref_tenor'] . ' Tahun',
                    );
                }

                $resp = json_encode(array('buff' => $list));
            }
        }

        echo $resp;
    }

    public function cs_get_dp()
    {
        $get = $this->input->get();
        $resp = json_encode(array('buff' => 'fail'));

        if ($get['bank'] != '') {
            $this->db->select('*');
            $this->db->from('credit_simulation');
            $this->db->where('ref_bank', $get['bank']);
            $this->db->where('ref_tenor', $get['tenor']);
            $this->db->distinct();
            $this->db->join('ref_down_payment',
                'credit_simulation.ref_down_payment = ref_down_payment.id_ref_down_payment');
            $this->db->order_by('ref_down_payment', 'asc');
            $tenor = $this->db->get();

            if ($tenor->num_rows() > 0) {
                $tenor = $tenor->result_array();
                foreach ($tenor as $v) {
                    $list[] = array(
                        'id' => $v['ref_down_payment'],
                        'dp' => $v['down_payment'] . ' %'
                    );
                }

                $resp = json_encode(array('buff' => $list));
            }
        }

        echo $resp;
    }

    public function credit_simulation()
    {
        // choose car model
        $get = $this->input->get();
        $disc = ($get['discount'] != '' ? $get['discount'] : 0);
        $resp = json_encode(array('buff' => 'fail'));
        if (isset($get['otr'], $get['tenor'], $get['dp'], $get['bank'])) {
            $where = array(
                'a.ref_bank' => $get['bank'],
                'a.ref_tenor' => $get['tenor'],
                'a.ref_down_payment' => $get['dp']
            );
            $sim = $this->db->select('a.*,b.bank_name,c.tenor,d.down_payment')->from('credit_simulation a')
                ->join('ref_bank b', 'a.ref_bank = b.id_ref_bank', 'inner')
                ->join('ref_tenor c', 'a.ref_tenor = c.id_ref_tenor', 'inner')
                ->join('ref_down_payment d', 'a.ref_down_payment = d.id_ref_down_payment', 'inner')
                ->where($where)->get();

            if ($sim->num_rows() > 0) {
                $sim = $sim->row_array();
                $calc = emulator_credit($get['otr'], $sim['down_payment'], $disc, $get['tenor'], $sim['bunga'],
                    $sim['asuransi'], $sim['provisi'], $sim['administrasi']);
                $calc['tenor'] = $calc['tenor'] . ' Tahun (' . $calc['tenor'] * 12 . ' bulan)';


                $resp = json_encode($calc);
            }
        }
        echo $resp;
    }

    public function religion_lst()
    {
        echo json_encode(array(
            'buff' => array(
                'islam' => 'islam',
                'kristen' => 'kristen',
                'budha' => 'budha',
                'hindu' => 'hindu',
                'katolik' => 'katolik'
            )
        ));

    }

    public function sex_lst()
    {
        echo json_encode(array('buff' => array('male' => 'male', 'female' => 'female', 'other' => 'other')));
    }

    /* for ford brand only */
    public function car_type()
    {
        $get = $this->input->get();
        $resp = json_encode(array('buff' => 'fail'));

        if ($get['page'] == 'car') {
            $this->db->select('id_car_types, brands, types');
            $this->db->where('ref_publish', 1);
            $this->db->group_by('id_car_types');
            $product = $this->db->get('view_cars');
        } elseif ($get['page'] == 'acc') {
            $this->db->select('id_type as id_car_types, brands, types');
            $this->db->where('ref_publish', 1);
            $this->db->group_by('id_type');
            $product = $this->db->get('view_car_accessories');
        } else {
            $where = array('ref_publish' => 1, 'brand_publish' => 1, 'id_brands' => 1);
            $product = $this->db->select('id_car_types,brands,types')->get_where('view_car_types', $where);
        }

        if ($product->num_rows() > 0) {
            foreach ($product->result_array() as $k => $v) {
                $buff[$k]['id'] = $v['id_car_types'];
                $buff[$k]['product'] = $v['brands'] . ' ' . $v['types'];
            }
            $resp = json_encode(array('buff' => $buff));
        }

        echo $resp;
    }

    public function car_new_type()
    {
        $resp = json_encode(array('buff' => 'fail'));
        $where = array('a.ref_publish' => 1);
        $product = $this->db->distinct()->select('a.id_type as id_car_types,c.brands,b.types')->from('car_accessories a')
            ->join('car_types b', 'a.id_type = b.id_car_types', 'inner')
            ->join('car_brands c', 'b.id_brands = c.id_brands', 'inner')
            ->where($where)->get();

        if ($product->num_rows() > 0) {
            foreach ($product->result_array() as $k => $v) {
                $buff[$k]['id'] = $v['id_car_types'];
                $buff[$k]['product'] = $v['brands'] . ' ' . $v['types'];
            }
            $resp = json_encode(array('buff' => $buff));
        }
        echo $resp;
    }

    public function car_detail()
    {
        $get = $this->input->get();
        $resp = array('buff' => 'fail', 'status' => 'fail');
        if (isset($get['id']) && !isset($get['carid'])) {
            // $model = $this->db->order_by('model','asc')->where_in('id_type',array($get['id']))->where_in('id_brands',array('1'))->where('ref_publish',1)->get('view_cars');
            $model = $this->db->order_by('car_price asc, model asc')->where_in('id_type',
                array($get['id']))->where_in('id_brands', array('1'))->where('ref_publish', 1)->get('view_cars');

            if ($model->num_rows() > 0) {
                $model = $model->result_array();
                foreach ($model as $k => $v) {
                    $buff[$k]['id'] = $v['id'];
                    // $buff[$k]['label'] = /* $v['brands'] . ' ' . $v['types'] . ' ' . */ $v['model'] . ' ' . $v['series'] . ' ' . $v['transmisi'] . ' ' . $v['car_cc'];
                    $buff[$k]['label'] = $v['model'] . ' ' . $v['series'] . ' ' . $v['engine'] . ' ' . $v['transmisi'] . ' ' . $v['car_cc'];
                }
                $resp['buff'] = $buff;
                $resp['status'] = 'Ok';
            }

        } elseif (isset($get['id']) && isset($get['carid'])) {
            $colors = $this->db->from('car_colors a')
                ->join('ref_color b', 'a.ref_color = b.id_ref_color', 'inner')
                ->where_in('id_car', array($get['carid']))
                ->where('a.status', 'sale')
                ->get();

            $stock_total = 0;
            if ($colors->num_rows() > 0) {
                foreach ($colors->result_array() as $k => $v) {
                    $c = $this->db->get_where('view_cars',
                        array('id' => $get['carid'], 'on_sale' => 'sale'))->row_array();
                    $buff[$k]['id'] = $v['ref_color'];
                    $buff[$k]['color'] = $v['color'];
                    $buff[$k]['thumb'] = (file_exists('./uploads/cars/colors/' . $v['car_color_thumb']) && $v['car_color_thumb'] != '') ? substr(base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'],
                        0, -4) : '';
                    $buff[$k]['price'] = 'OTR Price Rp.' . rp($c['car_price']);
                    $buff[$k]['detail'] = $c['model'] . ' ' . $c['series'] . ' ' . $c['engine'] . ' ' . $c['transmisi'] . ' ' . $c['car_cc'];
                    $buff[$k]['notes'] = $c['notes'];
                    //$buff[$k]['stock'] = $c['stock'];

                    $c2 = $this->db->get_where('jdi_car_colors',
                        array('id_car' => $get['carid'], 'id_car_colors' => $v['id_car_colors'],'status' => 'sale'))->row_array();
                    $stock_total = $stock_total + $c2['stock'];
                    $buff[$k]['stock'] = $stock_total;
                    //$buff[$k]['color'] = $v['color']." - Stock : ".$c2['stock'];
                    
                    $branch = $this->db->select('id_ref_location, location')
                        ->from('jdi_ref_location')
                        ->where('company_code', 'AK')->get()->result_array();
                    $cprice = $this->db->from('jdi_car_price')
                        ->where('car_id', $get['carid'])->get();
                    $cprice = $cprice->result_array();

                    foreach ($branch as $key => $v) {
                        foreach ($cprice as $val) {
                            if ($v['id_ref_location'] == $val['branch_id']) {
                                $buff[$k][$v['location']]['price'] = $val['price'];
                            }
                        }
                    }

                }
                $buff[0]['stock'] = $stock_total;
                $resp['buff'] = $buff;
                $resp['status'] = 'Ok';

            } else {
                $c = $this->db->get_where('view_cars', array('id' => $get['carid'], 'on_sale' => 'sale'));

                if ($c->num_rows() > 0) {
                    $c = $c->row_array();
                    $k = 0;
                    $buff[$k]['id'] = $c['id'];
                    $buff[$k]['color'] = '';//$v['color'];
                    $buff[$k]['thumb'] = ''; //( file_exists('./uploads/cars/colors/'.$v['car_color_thumb']) && $v['car_color_thumb'] != '') ? substr(base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'],0,-4) : '';
                    $buff[$k]['price'] = 'OTR Price Rp.' . rp($c['car_price']);
                    $buff[$k]['detail'] = $c['model'] . ' ' . $c['series'] . ' ' . $c['engine'] . ' ' . $c['transmisi'] . ' ' . $c['car_cc'];
                    $buff[$k]['notes'] = $c['notes'];

                    $branch = $this->db->select('id_ref_location, location')
                        ->from('jdi_ref_location')
                        ->where('company_code', 'AK')->get()->result_array();
                    $cprice = $this->db->from('jdi_car_price')
                        ->where('car_id', $get['carid'])->get();
                    $cprice = $cprice->result_array();

                    foreach ($branch as $key => $v) {
                        foreach ($cprice as $val) {
                            if ($v['id_ref_location'] == $val['branch_id']) {
                                $buff[$k][$v['location']]['price'] = $val['price'];
                            }
                        }
                    }

                    $resp['buff'] = $buff;
                    $resp['status'] = 'Ok';
                }
            }
        }
        echo json_encode($resp);
    }

    public function car_price()
    {
        $get = $this->input->get();
        $resp = array('buff' => 'fail');
        if (isset($get['car_id']) && isset($get['branch_id'])) {
            $price = $this->db->from('car_price')
                ->where('car_id', $get['car_id'])
                ->where('branch_id', $get['branch_id'])
                ->get()->result_array();
            $resp['buff'] = $price;
        }
        echo json_encode($resp);
    }

    /* for reference color by car */
    public function car_color()
    {
        $get = $this->input->get();
        $resp = array('buff' => 'fail');
        if (isset($get['id'])) {
            $colors = $this->db->from('car_colors a')
                ->join('ref_color b', 'a.ref_color = b.id_ref_color', 'inner')
                ->where_in('id_car', array($get['id']))->get();
            if ($colors->num_rows() > 0) {
                foreach ($colors->result_array() as $k => $v) {
                    $buff[$k]['id'] = $v['ref_color'];
                    $buff[$k]['color'] = $v['color'];
                }
                $resp['buff'] = $buff;
            }
        }
        echo json_encode($resp);
    }

    public function store_usercar()
    {
        $get = $this->input->get();
        $resp = 'false';
        // if(isset($get['car']) && isset($get['user']) && isset($get['color']) && isset($get['pn'])
        // && isset($get['vn']) && isset($get['sd']) && isset($get['ins_date']) && isset($get['lm']) && isset($get['act']) ) {
        // $buff = array('id_user' 		=> $get['user'],
        // 'id_car'			=> $get['car'],
        // 'id_car_color'	=> $get['color'],
        // 'police_number'	=> $get['pn'],
        // 'vin_number'		=> $get['vn'],
        // 'stnk_date'		=> $get['sd'],
        // 'insurance_date'	=> $get['ins_date'],
        // 'last_mileage'	=> $get['lm']);
        // if( isset($get['user_car_id']) && $get['act'] == 'u' ) {
        // $this->db->where( array('id_user' => $get['user'], 'id_car' => $get['car'], 'id_user_cars' => $get['user_car_id'] ) )->update('user_car',$buff);
        // if($this->db->_error->message()){
        // $resp = $this->db->_error->message();
        // }else {
        // $resp = 'Successfull Updated';
        // }

        // }elseif($get['act'] == 'c') {
        // $this->db->insert('user_car',$buff);
        // if($this->db->_error->message()){
        // $resp = $this->db->_error->message();
        // }else {
        // $resp = 'Successfull Updated';
        // }
        // }
        // }
        // echo json_encode(array('buff' => $resp));


        if (isset($get['id']) && isset($get['id_car']) && isset($get['mail']) && isset($get['lm'])) {
            $uid = db_get_one('user', 'id_user', "email = '" . $get['mail'] . "'");
            if ($uid) {
                $data = array('last_mileage' => $get['lm']);
                $where = array(
                    'id_user' => $uid,
                    'id_car' => $get['id_car'],
                    'id_user_cars' => $get['id']
                );
                $this->db->where($where)->update('user_cars', $data);

                if (!$this->db->_error_message()) {
                    $resp = 'Successfull Updated';
                } else {
                    $resp = $this->db->_error_message();
                }
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function car_test_drive_lst()
    {
        $resp = json_encode(array('buff' => 'fail'));
        // $ct = $this->db->get_where('view_cars_test_drive',array('ref_publish'=>1));

        // $ct = $this->db->select('a.*,b.id,b.msc_code,b.ref_publish,b.brands,b.types,b.series,b.model,b.transmisi,b.car_cc')
        // ->from('car_taxonomy a')
        // ->join('view_cars b','a.ref_car = b.id','inner')
        // ->where('b.ref_publish',1)->get();

        $ct = $this->db->select('a.*,b.id,b.msc_code,b.ref_publish,b.brands,b.types,b.series,b.model,b.transmisi,b.car_cc')
            ->from('car_test_drive a')
            ->join('view_cars b', 'a.id_car = b.id', 'inner')
            ->where('b.ref_publish', 1)->get();

        // echo $this->db->_error_message();
        if ($ct->num_rows() > 0) {
            $ct = $ct->result_array();
            foreach ($ct as $k => $v) {
                $color = db_get_one('car_colors', 'car_color_thumb', "id_car = '" . $v['id'] . "'");

                $buff[$k]['id'] = $v['id'];
                $buff[$k]['car'] = $v['brands'] . ' ' . $v['types'] . ' ' . $v['series'] . ' ' . $v['transmisi'];
                $buff[$k]['thumb'] = ($color && file_exists('./uploads/cars/colors/' . $color)) ? substr(base_url() . 'uploads/cars/colors/' . $color,
                    0, -4) : '';
            }

            // $time = get_available_time_td('tdrive');
            $date = get_date_list(date('Y-m-d', strtotime('+1days')), date('Y-m-d', strtotime('+8days')));

            // foreach($time as $k => $v) {
            // $time_val[] = str_replace('.',':',$v);
            // }

            foreach ($date as $k => $v) {
                $date_val[$k] = date('Y-m-d', strtotime($v));
            }


            // $resp = json_encode(array('buff' => $buff,/* 'time' => $time,  */'date' => $date, 'time_val' => $time_val, 'date_val' => $date_val ));
            $resp = json_encode(array('buff' => $buff, 'date_val' => $date_val));
        }

        echo $resp;
    }

    public function booking_list()
    {
        $get = $this->input->get();
        $resp = false;

        if (isset($get['email'])) {
            $id = db_get_one('user', 'id_user',
                "email = '" . $get['email'] . "' and (user_type = 'vip' or user_type = 'sales')");
            $test_drive_list = $this->Api_model->get_test_drive_list($id);
            $service_list = $this->Api_model->get_service_list($id);

            $booking_list = array_merge($test_drive_list, $service_list);
            $booking_list = $this->record_sort($booking_list, 'create_date', true);

            $resp = array();
            foreach ($booking_list as $v) {
                if (array_key_exists('id_service_booking', $v)) {
                    $id = $v['id_service_booking'];
                    $type = 'service';
                } else {
                    $id = $v['id_test_drive_booking'];
                    $type = 'test_drive';
                }

                $resp[] = array(
                    'id' => $id,
                    'type' => $type,
                    'date' => $v['create_date'],
                    'location' => $v['location'],
                    'status' => $v['status_book'],
                );
            }

            $resp = json_encode($resp);

        }

//        echo '<pre>';
//        var_dump($booking_list);
//        echo '</pre>';
//
        echo $resp;
    }

    /**
     * Menampilkan detail booking berdasarkan ID
     * @param int $id
     * @param string $type
     */
    public function booking_detail()
    {
        $get = $this->input->get();
        $resp = false;

        if (isset($get['type'])) {
            if ($get['type'] == 'test_drive') {
                $resp = $this->Api_model->get_test_drive_detail($get['id']);
            } elseif ($get['type'] == 'service') {
                $resp = $this->Api_model->get_service_detail($get['id']);
            }

            $resp = json_encode($resp);
        }

        echo $resp;
    }

    public function store_test_drive_book()
    {
        /**
         * I don't know why, but for this method, we actually call the api_temp.php (Seto, August 2014)
         */
        $get = $this->input->get();
        $resp = false;
        if (isset($get['user']) && isset($get['car']) && isset($get['location']) && isset($get['time']) && isset($get['date']) /* && isset($get['un']) */) {
            $u = db_get_one('user', 'user_type', "email = '" . $get['user'] . "'");
            $id = db_get_one('user', 'id_user', "email = '" . $get['user'] . "'");
            $location = db_get_one('ref_location', 'location', "id_ref_location = '" . $get['location'] . "'");
            $dcrc_email = db_get_one('auth_user', 'email', "ref_location = '" . $get['location'] . "'");
            $dcrc_number = db_get_one('auth_user', 'phone', "ref_location = '" . $get['location'] . "'");
            $dcrc_number2 = db_get_one('auth_user', 'phone2', "ref_location = '" . $get['location'] . "'");

            if ($u == 'sales') {
                $client_name = $get['name'];
                $client_number = $get['phone'];
            } else {
                $client_name = db_get_one('user', 'username', "email = '" . $get['user'] . "'");
                $client_number = db_get_one('user', 'phone_number', "email = '" . $get['user'] . "'");
            }

            $book_date = $get['date'];
            $book_time = str_replace('.', ':', $get['time']);
            $booktime = $book_date . ' ' . $book_time;

            $book = array(
                'ref_user' => $id,
                'ref_car' => $get['car'],
                'ref_location' => $get['location'],
                'datetime_book' => $booktime,
            );

            if (isset($get['un'])) {
                $book['notes'] = $get['un'];
            }

            if ($u != '0') {
                switch ($u) {
                    case 'vip':
                    case 'reguler':
                        $where = array('ref_user' => $id, 'datetime_book' => $booktime, 'ref_car' => $get['car']);
                        if (db_get_one('test_drive_booking', 'ref_user', $where) == '0') {
                            $this->db->insert('test_drive_booking', $book);
                            if ($this->db->_error_message()) {
                                $resp = $this->db->_error_message();
                            } else {
                                $resp_status = true;
                                $resp = 'Thank You, Our Customer Service will contact you soon';
                            }
                        } else {
                            $resp = 'You cannot booking the same schedule';
                        }
                        break;
                    case 'sales':
                        $where = array('ref_user' => $id, 'datetime_book' => $booktime, 'ref_car' => $get['car']);
                        if (db_get_one('test_drive_booking', 'ref_user', $where) == '0') {
                            if (isset($get['name']) && isset($get['addr']) && isset($get['mail']) && isset($get['phone'])) {
                                $book['name'] = $get['name'];
                                $book['alamat'] = $get['addr'];
                                $book['email'] = $get['mail'];
                                $book['phone_number'] = $get['phone'];
                                // $book['notes'] 			= $get['un'];
                                $this->db->insert('test_drive_booking', $book);
                                if ($this->db->_error_message()) {
                                    $resp = $this->db->_error_message();
                                } else {
                                    $resp = 'Thank You, Our Customer Service will contact you soon';
                                }
                            }
                        } else {
                            $resp = 'You cannot booking the same schedule';
                        }
                        break;
                }
            } else {
                $resp = 'user Not Found';
            }
        } else {
            $resp = 'Please try with the right method';
        }
        echo json_encode(array('buff' => $resp));

        if (isset($resp_status) && $resp_status === true) {
            $msg_client = "Terimakasih, Request Test Drive Anda

Tanggal: " . iso_date($book_date) . "
Jam: " . substr($book_time, 0, -3) . "
Lokasi: $location

Akan segera kami proses.

Salam,
AK EXPERIENCE";

            $msg_dcrc = "Request Test Drive

$client_name
$client_number
" . iso_date($book_date) . "
" . substr($book_time, 0, -3) . "
$location

Segera proses dan hubungi customer.";

            send_sms($client_number, $msg_client);
            if ($u != 'sales') {
                send_sms($dcrc_number, $msg_dcrc);
                send_sms($dcrc_number2, $msg_dcrc);

                $this->load->library('email');

                $this->email->from('noreply@akxperience.com', 'AK Experience CMS');
                $this->email->to($dcrc_email);

                $this->email->subject('AK Experience : Test Drive Request');
                $this->email->message($msg_dcrc);

                $this->email->send();
            }

            $user = $this->db->get_where('user', array('id_user' => $id))->row_array();

            $payloads = 'Test Drive Booked on ' . iso_date($book_date) . ' ' . substr($book_time, 0,
                    -3) . ', check menu Service Status';
            $ckey = 'Reminder';
            $msg = array(
                'message' => $payloads,
                'description' => 'testing description'
            );
            if ($user['gcm_id'] != '') {
                send_gcm_async($user['email'], $user['gcm_id'], $msg, $ckey);
            }
            if ($user['apn_id'] != '') {
                send_apn_async($user['apn_id'], $payloads);
            }
        }
    }

    public function store_test_drive_notes()
    {
        $id = $this->input->get('id');
        $notes = $this->input->get('notes');
        $resp = 'fail';

        if ($id != '' && $notes != '') {
            $data = array(
                'comments' => $notes,
            );
            $this->db->where('id_test_drive_booking', $id);
            $this->db->update('test_drive_booking', $data);

            $resp = 'ok';
        }

        echo json_encode(array('buff' => $resp));
    }

    public function store_repair_service()
    {

        $get = $this->input->get();
        $resp = false;

        if (isset($get['usercar']) && isset($get['service']) && isset($get['un']) && isset($get['time_book']) && isset($get['location'])) {

            $u = db_get_one('user_cars', 'id_user', "id_user_cars = '" . $get['usercar'] . "'");
            $t = db_get_one('user', 'user_type', "id_user = '$u'");
            $location = db_get_one('ref_location', 'location', "id_ref_location = '" . $get['location'] . "'");
            $dcrc_number = db_get_one('auth_user', 'phone', "ref_location = '" . $get['location'] . "'");
            $dcrc_number2 = db_get_one('auth_user', 'phone2', "ref_location = '" . $get['location'] . "'");
            $dcrc_email = db_get_one('auth_user', 'email', "ref_location = '" . $get['location'] . "'");
            $client_number = db_get_one('user', 'phone_number', "id_user = '$u'");
            $client_name = db_get_one('user', 'username', "id_user = '$u'");
            $service_type = db_get_one('car_services', 'id_service_type', "id_service = '" . $get['service'] . "'");

            if ($service_type == 1) {
                $service_type = 'Workshop';
            } elseif ($service_type == 2) {
                $service_type = 'Home';
            }

            $booktime = $get['time_book'];
            $booktime_array = explode(' ', $booktime);

            if ($u != '0' && $t != '0') {
                $where = array('ref_user_cars' => $get['usercar'], 'datetime_book' => $get['time_book']);
                if (db_get_one('car_service_booking', 'ref_user_cars', $where) == '0') {

                    $book = array(
                        'ref_user_cars' => $get['usercar'],
                        'ref_service' => $get['service'],
                        // 'current_mileage'	 => $get['cm'],
                        'user_notes' => $get['un'],
                        // 'date_next_visit'	 => $get['date_nvisit'],
                        'id_ref_location' => $get['location'],
                        'datetime_book' => $get['time_book'],
                    );

                    switch ($t) {
                        case 'vip':
                            $this->db->insert('car_service_booking', $book);
                            if ($this->db->_error_message()) {
                                $resp = $this->db->_error_message();
                            } else {
                                // $resp = 'Successfull booked';
                                $resp_status = true;
                                $resp = 'Thank You, Our Customer Service will contact you soon';
                            }
                            break;
                    }
                } else {
                    $resp = 'you cannot booking the same schedule';
                }
            }
        }
        echo json_encode(array('buff' => $resp));

        if (isset($resp_status) && $resp_status === true) {
            $msg_client = "Terimakasih, Request $service_type Service Anda

Tanggal: " . iso_date($booktime_array[0]) . "
Jam: " . substr($booktime_array[1], 0, -3) . "
Lokasi: $location

Akan segera kami proses.

Salam,
AK EXPERIENCE";

            $msg_dcrc = "Request $service_type Service

$client_name
$client_number
" . iso_date($booktime_array[0]) . "
" . substr($booktime_array[1], 0, -3) . "
$location

Segera proses dan hubungi customer.";

            send_sms($client_number, $msg_client);
            send_sms($dcrc_number, $msg_dcrc);
            send_sms($dcrc_number2, $msg_dcrc);

            $this->load->library('email');

            $this->email->from('noreply@akxperience.com', 'AK Experience CMS');
            $this->email->to($dcrc_email);

            $this->email->subject('AK Experience : Service Request');
            $this->email->message($msg_dcrc);

            $this->email->send();

            $user = $this->db->get_where('user', array('id_user' => $u))->row_array();
            $police_number = db_get_one('user_cars', 'police_number', "id_user_cars = '" . $get['usercar'] . "'");
            $ckey = 'Reminder';
            $payloads = ucfirst($service_type) . ' Service Booked for ' . $police_number . ' on ' . iso_date($booktime_array[0]) . ' ' . substr($booktime_array[1],
                    0, -3) . ', check menu Service Status';
            $msg = array(
                'message' => $payloads,
                'description' => 'testing description'
            );
            if ($user['gcm_id'] != '') {
                send_gcm_async($user['email'], $user['gcm_id'], $msg, $ckey);
            }
            if ($user['apn_id'] != '') {
                send_apn_async($user['apn_id'], $payloads);
            }
        }


    }

    public function user_activation()
    {
        $get = $this->input->get();
        $html = array('resp' => '', 'status' => 'fail');
        if (isset($get['activation_code']) && isset($get['mail'])) {
            $act = $this->db->get_where('user',
                array('status' => '0', 'email' => $get['mail'], 'activation_code' => $get['activation_code']));
            if ($act->num_rows() > 0) {
                $act = $act->row();
                // if(strtotime($act->create_date.'+2days') == $get['activation_code']){
                $this->load->helper('mail');
                $this->db->where('id_user', $act->id_user)->update('user',
                    array('status' => '1', 'activation_code' => ''));
                if (!$this->db->_error_message()) {
                    $slug = ($act->username == '' || $act->username == '-') ? $act->email : $act->username;
                    $thml['status'] = 'ok';
                    $html['resp'] = 'successfull actived';
                    $conf['from_name'] = $slug;
                    $conf['subject'] = 'Auto Kencana Experience Activation';
                    $conf['to'] = $act->email;
                    $conf['content'] = "";

                    $conf['content'] .= "Dear Mr./Mrs. $slug<br>";
                    // $conf['content']   = "Dear Mr/Mrs $act->username!!<br>";
                    $conf['content'] .= "Congratulation,<br>Your login to Auto Kencana Experience Mobile Application has been activated.";
                    $conf['content'] .= "<br><br>Best Regards,<br>Kreasi Auto Kencana, PT";

                    sent_mail($conf);
                }
                // }
            }
        }
        $data = array(
            'base_url' => base_url(),
        );
        $this->parser->parse(getAdminFolder() . '/layout/account_activated_mobile.html', $data);
        // echo json_encode($html);
    }

    public function reset_my_pass()
    {
        // param email
        // send mail to user attach link
        // generate form based on link sent
        $get = $this->input->get();
        $html = array('resp' => 'seem like your email not in our DB', 'status' => 'fail');
        if (isset($get['mail'])) {
            $usr = $this->db->get_where('user', array('email' => $get['mail']));
            if ($usr->num_rows() > 0) {
                $usr = $usr->row();
                $slug = ($usr->username == '' || $usr->username == '-') ? $usr->email : $usr->username;
                $this->load->helper('mail');

                $key = md5($usr->email . strtotime($usr->create_date));
                $lnk = site_url("reset_my_pass/?umail=$usr->email&key=$key");
                $html['status'] = 'Ok';
                $html['resp'] = 'Link password changing already sent to your email';
                $conf['from_name'] = $slug;
                $conf['subject'] = 'Ford User Request Password Change';
                $conf['to'] = $usr->email;
                $conf['content'] = "";

                $conf['content'] .= "Dear Mr./Mrs. $slug<br>";
                $conf['content'] .= "A very good day to you.<br>";
                $conf['content'] .= "As requested, please click link below to reset your password.<br>";
                $conf['content'] .= "<a href='$lnk'>$lnk</a><br>";
                $conf['content'] .= "Should you require further assistance, please feel free to contact Customer Care Team.<br><br>";
                $conf['content'] .= "Have a wonderful day ahead!<br><br>";
                $conf['content'] .= "Best regards,<br>";
                $conf['content'] .= "Auto Kencana Group<br>";

                sent_mail($conf);
            }
        }
        echo json_encode($html);
    }

    public function brochure()
    {
        $html = array('resp' => '', 'status' => 'fail');
        $get = $this->input->get();
        if (isset($get['i'])) {
            $bro = $this->db->get_where('brochure', array('ref_publish' => '1'));
            if ($bro->num_rows() > 0) {
                $bro = $bro->result_array();
                foreach ($bro as $k => $v) {
                    $bro[$k]['ext'] = '';
                    if ($v['thumb'] != '' && file_exists('./uploads/brochure/' . $v['thumb'])) {
                        $bro[$k]['thumb'] = base_url() . 'uploads/brochure/' . $v['thumb'];
                    }
                    if ($v['file'] != '' && file_exists('./uploads/brochure/file/' . $v['file'])) {
                        $bro[$k]['file'] = base_url() . 'uploads/brochure/file/' . $v['file'];
                        $s = explode('.', $v['file']);
                        $bro[$k]['ext'] = $s[1];
                    }

                    // $where = array('id_brochure' => $v['id_brochure'],'file !=' => '');
                    // $file = $this->db->select('id,file')->get_where('brochure_file',$where);
                    // if($file->num_rows()>0) {
                    // $file = $file->result_array();
                    // foreach($file as $i => $n) {
                    // $bro[$k]['attach'][$i]['id'] 	= $n['id'];
                    // $bro[$k]['attach'][$i]['file'] 	= '';
                    // if( $n['file'] != '' && file_exists('./uploads/brochure/'.$n['file'])) {
                    // $bro[$k]['attach'][$i]['file'] = base_url() . 'uploads/brochure/'.$n['file'];

                    // }
                    // }
                    // }
                }
                $html = array('resp' => $bro, 'status' => 'Ok');
            }
        }
        echo json_encode($html);
    }

    public function fetching_point()
    {
        $this->load->model('api_model', 'api');
        $resp = $status = 'fail';
        $ucid = $this->input->get('ucid');
        $check = db_get_one('user_cars', 'id_user_cars', "id_user_cars = '$ucid'");
        if ($ucid && $check) {
            $status = 'Ok';
            $resp = array('buff' => $this->api->fetching_point($ucid));
        }
        echo json_encode(array('resp' => $resp, 'status' => $status));
    }

    public function store_device_id()
    {
        $resp = 'fail';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = $this->input->post();
        } else {
            $post = $this->input->get();
        }
        if (isset($post['mail']) && isset($post['device_id']) && isset($post['type'])) {
            switch ($post['type']) {
                case 'apn':
                    $post['device_id'] = str_replace(array('<', '>', ' '), '', $post['device_id']);
                    if ($this->db->where('email', $post['mail'])->update('user',
                        array('apn_id' => $post['device_id']))
                    ) {
                        $resp = 'ok';
                    }
                    break;
                case 'gcm':
                    if ($this->db->where('email', $post['mail'])->update('user',
                        array('gcm_id' => $post['device_id']))
                    ) {
                        $resp = 'ok';
                    }
                    break;
            }
        }
        echo json_encode(array('resp' => $resp));
    }

    public function test()
    {
        $this->load->helper('global_function');
        $get = $this->input->get();
        if (isset($get['msg']) && isset($get['mail'])) {
            $mails = $get['mail'];
            $usr = $this->db->get_where('user', "gcm_id != '' and email = '$mails'");
            if ($usr->num_rows() > 0) {
                $msg = array(
                    'message' => $get['msg'],
                    'description' => 'testing description'
                );
                foreach ($usr = $usr->result_array() as $usrs) {
                    $rid[] = $usrs['gcm_id'];
                    $mail[] = $usrs['email'];
                }
                $ckey = 'Announcement';


                echo broadcast_gcm($mail, $rid, $msg, $ckey, true);
                echo 'success';

            } else {
                echo "seem like there's no registered user in DB";
            }
        }
    }

    public function view_service_book()
    {
        $html = array('resp' => '', 'status' => 'fail');
        $post = $this->input->get();
        if (isset($post['user_car_id']) && isset($post['mail'])) {
            $where = array(
                'email' => $post['mail'],
                'id_user_cars' => $post['user_car_id'],
                'status_book' => 'finished'
            );
            $hist = $this->db->order_by('datetime_book',
                'desc')->select('datetime_book,service_type,service,service_notes,user_notes,location')->get_where('view_service_booking',
                $where);
            if ($hist->num_rows() > 0) {
                $hist = $hist->result_array();
                // foreach($hist as $k => $v) {

                // }
                $html = array('resp' => $hist, 'status' => 'ok');
            }
        }
        echo json_encode($html);
    }

    public function bvm()
    {
        $news = $this->db->limit(1)->get_where('news', array('type' => 'bvm'));
        if ($news->num_rows() > 0) {
            $news = $news->row_array();
            $title = $news['title'];
            $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1',
                strip_tags(htmlspecialchars_decode($news['content']), '<p>,<br>,<div>,<b>,<strong>,<li>,<ol>'));
            $thumb = ($news['thumb'] != '' && file_exists('./uploads/news/' . $news['thumb'])) ? base_url() . 'uploads/news/' . $news['thumb'] : '';
            $thumb = ($thumb) ? ($thumb) : substr($news['thumb_url'], 0, -11) . 'w=800&h=600';
            $thumb = ($thumb) ? "<img src='$thumb' />" : '';
            echo "
			<html>
			<head>
			 <title>$title </title>
			 <style> 
				body { font-family: Verdana, sans-serif; line-height: 30px; background-color: rgb(248, 249, 249); }
				img { width: 100%;}
				p { font-family: arial; font-size: 13pt; font-color:blue; }
				h2 { font-size: bold 32px/36px Georgia,Century,Times,serif; color:#111111}
				#wrapper { width: 100%; }
			 </style>
			</head>
			<body>
				<div id='wrapper'>
					<h2> $title </h2>
					$thumb <br>
					$content
				</div>
			</body>
			</html>";
        }
    }

    public function detail_news()
    {
        $html = array('resp' => '', 'status' => 'fail');
        $post = $this->input->get();
        if (isset($post['idn'])) {
            $news = $this->db->get_where('news', array('id_news' => $post['idn']));
            if ($news->num_rows() > 0) {
                $news = $news->row_array();
                $title = $news['title'];
                $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1',
                    strip_tags(htmlspecialchars_decode($news['content']), '<p>,<br>,<div>,<b>,<strong><img>'));
                $thumb = ($news['thumb'] != '' && file_exists('./uploads/news/' . $news['thumb'])) ? base_url() . 'uploads/news/' . $news['thumb'] : '';
                $thumb = ($thumb) ? ($thumb) : substr($news['thumb_url'], 0, -11) . 'w=800&h=600';
                $thumb = ($thumb) ? "<img src='$thumb' />" : '';
                // <!--$thumb <br> -->

                echo "
				<html>
				<head>
				 <title>$title </title>
				 <style> 
					body { font-family: Verdana, sans-serif; line-height: 30px; background-color: #fff; }
					img { width: 100%;}
					p { font-family: arial; font-size: 13pt; font-color:blue; }
					h2 { font-size: bold 32px/36px Georgia,Century,Times,serif; color:#111111}
					#wrapper { width: 100%; }
				 </style>
				</head>
				<body>
					<div id='wrapper'>
						<h2> $title </h2>
						
						$content
					</div>
				</body>
				</html>", exit;
            }
        }
        echo $html;
    }

    public function twitter_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $twitter = $this->db->select('id, name, twitter_username')->order_by('sort', 'asc')
                ->get_where('ref_twitter', "is_published = '1'");
            if ($twitter->num_rows() > 0) {
                $twitter = $twitter->result_array();
                $resp = $twitter;
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function tweet()
    {
        $get = $this->input->get();
        $count = ($get['count'] != '' && is_numeric($get['count'])) ? $get['count'] : 25;
        $sname = ($get['sname'] != '') ? $get['sname'] : 'TMCPoldaMetro';
        $header = create_oauth_signature($sname, $count);

        $options = array(
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => ""
        );

        $resp = cURL(TSTATUS . '?count=' . $count . '&screen_name=' . $sname, $options);
        $html =
            '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		 <html>
		  <head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<title>Untitled Document</title>
			<link href="' . base_url('./assets/css/as.css') . '" inline="false" media="screen" rel="stylesheet" type="text/css" />
		  </head>
		  <body>
			<div id="main_content">
				<div id="main-content" class="tweet-detail">
					<div class="main-tweet-container"> 
		';
        $html .= parse_twitter($resp);
        // $html .= '</body></html>';
        $html .=
            '</div>
		</div>
		</div>
		</body>
		</html>';

        echo $html;
    }

    public function repair_service_()
    {
        // update status based on action request

    }

    public function test_drive_()
    {
        // update status based on action request

    }

    public function mileage_info_()
    {
        // update mileage based on action request

    }

    public function apn_send()
    {
        $device = $this->input->get('device_id');
        $msg = $this->input->get('alert');
        if ($device && $msg) {

            // $body = array('aps' => array(
            // 'alert' => $msg,
            // 'sound' => 'default'
            // ),'alert' => 'message here','sound' => 'seterah');

            // Encode the payload as JSON
            // $payload = json_encode($body);

            // die($payload);

            // die('./'.APPPATH.'cert/apn-akexperience-cer.pem');
            $gw = 'ssl://gateway.sandbox.push.apple.com:2195';
            $gw = 'ssl://gateway.push.apple.com:2195';


            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert',
                '/var/www/html/ford/application/' . 'cert/apns_ak_push.pem');
            // stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/ford/application/'.'cert/apns_ak_dev.pem');
            //stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase); // use this if you are using a passphrase

            // Open a connection to the APNS server
            $fp = stream_socket_client(
                $gw, $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);


            if (!$fp) {
                //header ("Refresh: 4; login_success.php");
                //exit("<span style=\"font-family: Verdana, Geneva, sans-serif; font-size: 14px;\">Could not connect to server. Please check the passphrase. You are being redirected...</span>" . PHP_EOL);
                die($err . ' : could not connect to server');
                die('could not connect to server');
            }

            // Create the payload body
            // $body['aps'] = array(
            // 'alert' => $msg,
            // 'sound' => 'default'
            // );

            $body = array(
                'aps' => array(
                    'alert' => $msg,
                    'sound' => 'default'
                ),
                'alert' => $msg,
                'sound' => 'seterah'
            );
            // Encode the payload as JSON
            $payload = json_encode($body);

            $errCounter = 0;


            $bodyError = '';


            $deviceToken = array($device);

            $message['identifier'] = 'co.id.jayadata.akexperience';
            $message['expire'] = time() + (1 * 24 * 60 * 60);
            $message['deviceToken'] = $device; //'f808c903e68c317d8541ef237414777c79ed2fac58396b13f2b65efa92aebd7b';

            $n = 0;
            while ($n < '1') {
                // echo $n . '<br>' . $deviceToken[$n];
                // Build the binary notification
                // $msg = chr(0) . pack('n', 32) . pack('H*', ''.$deviceToken[0].'') . pack('n', strlen($payload)) . $payload;
                // $msg = chr(1) . pack("N", $message['identifier']) . pack("N", $message['expire']) . pack("n", 32) . pack('H*', str_replace(' ', '', $message['deviceToken'])) . pack("n",strlen($payload)) . $payload;
                $msg = chr(1) . pack("N", $message['identifier']) . pack("N", $message['expire']) . pack("n",
                        32) . pack('H*', str_replace(' ', '', $message['deviceToken'])) . pack("n",
                        strlen($payload)) . $payload;

                // Send it to the server
                $result = fwrite($fp, $msg, strlen($msg));

                if (!$result) {
                    echo 'fail to write message on device id : ' . $deviceToken[$n];
                }

                $bodyError .= 'result: ' . $result . ', devicetoken: ' . $deviceToken[0] . '';

                usleep(500000);

                checkAppleErrorResponse($fp);

                $n++;

                // debugvar($this->_grab_feedback());
                grab_feedback();
            }

            fclose($fp);
        }
    }

    function checkAppleErrorResponse($fp)
    {

        //byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID).
        // Should return nothing if OK.

        //NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait
        // forever when there is no response to be sent.

        $apple_error_response = fread($fp, 6);

        if ($apple_error_response) {

            $r = false;
            // unpack the error response (first byte 'command" should always be 8)
            $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);


            if ($error_response['status_code'] == '0') {
                $r = true;
                $error_response['status_code'] = '0-No errors encountered';

            } else {
                if ($error_response['status_code'] == '1') {
                    $error_response['status_code'] = '1-Processing error';

                } else {
                    if ($error_response['status_code'] == '2') {
                        $error_response['status_code'] = '2-Missing device token';

                    } else {
                        if ($error_response['status_code'] == '3') {
                            $error_response['status_code'] = '3-Missing topic';

                        } else {
                            if ($error_response['status_code'] == '4') {
                                $error_response['status_code'] = '4-Missing payload';

                            } else {
                                if ($error_response['status_code'] == '5') {
                                    $error_response['status_code'] = '5-Invalid token size';

                                } else {
                                    if ($error_response['status_code'] == '6') {
                                        $error_response['status_code'] = '6-Invalid topic size';

                                    } else {
                                        if ($error_response['status_code'] == '7') {
                                            $error_response['status_code'] = '7-Invalid payload size';

                                        } else {
                                            if ($error_response['status_code'] == '8') {
                                                $error_response['status_code'] = '8-Invalid token';

                                            } else {
                                                if ($error_response['status_code'] == '255') {
                                                    $error_response['status_code'] = '255-None (unknown)';

                                                } else {
                                                    $error_response['status_code'] = $error_response['status_code'] . '-Not listed';

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($r) {
                return false;

            } else {
                return $error_response;
            }

            //echo '<br><b>+ + + + + + ERROR</b> Response Command:<b>' . $error_response['command'] . '</b>&nbsp;&nbsp;&nbsp;Identifier:<b>' . $error_response['identifier'] . '</b>&nbsp;&nbsp;&nbsp;Status:<b>' . $error_response['status_code'] . '</b><br>';

            //echo 'Identifier is the rowID (index) in the database that caused the problem, and Apple will disconnect you from server. To continue sending Push Notifications, just start at the next rowID after this Identifier.<br>';

        }
    }


    function grab_feedback()
    {
        debugvar($this->_grab_feedback());
    }

    function _grab_feedback()
    {
        $nFeedbackTupleLen = 38;

        $gw = 'ssl://feedback.sandbox.push.apple.com:2196'; // Sandbox environment';
        $gw = 'ssl://feedback.push.apple.com:2196'; // Production environment


        $ctx = stream_context_create();
        // stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/ford/application/'.'cert/apns_ak_dev.pem');
        stream_context_set_option($ctx, 'ssl', 'local_cert',
            '/var/www/html/ford/application/' . 'cert/apns_ak_push.pem');

        //stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase); // use this if you are using a passphrase

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            $gw, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);


        $_aFeedback = array();
        $sBuffer = '';
        if ($fp) {
            while (!feof($fp)) {
                // $this->_log('INFO: Reading...');
                $sBuffer .= fread($fp, 8192);
                // $sBuffer .= $sCurrBuffer = fread($fp, 8192);
                // $nCurrBufferLen = strlen($sCurrBuffer);
                // if ($nCurrBufferLen > 0) {
                // $this->_log("INFO: {$nCurrBufferLen} bytes read.");
                // }
                // unset($sCurrBuffer, $nCurrBufferLen);

                $nBufferLen = strlen($sBuffer);
                if ($nBufferLen >= $nFeedbackTupleLen) {
                    $nFeedbackTuples = floor($nBufferLen / $nFeedbackTupleLen);
                    for ($i = 0; $i < $nFeedbackTuples; $i++) {
                        $sFeedbackTuple = substr($sBuffer, 0, $nFeedbackTupleLen);
                        $sBuffer = substr($sBuffer, $nFeedbackTupleLen);
                        $_aFeedback[] = $aFeedback = $this->_parseBinaryTuple($sFeedbackTuple);
                        // $this->_log(sprintf("INFO: New feedback tuple: timestamp=%d (%s), tokenLength=%d, deviceToken=%s.",
                        // $aFeedback['timestamp'], date('Y-m-d H:i:s', $aFeedback['timestamp']),
                        // $aFeedback['tokenLength'], $aFeedback['deviceToken']
                        // ));
                        unset($aFeedback);
                    }
                }

                $read = array($fp);
                $null = null;
                $nChangedStreams = stream_select($read, $null, $null, 0, 1000000);
                if ($nChangedStreams === false) {
                    $this->_log('WARNING: Unable to wait for a stream availability.');
                    break;
                }
            }
        } else {
            die('count not connect to apn server');
        }
        fclose($fp);
        return $_aFeedback;
    }

    /**
     * Parses binary tuples.
     *
     * @param  $sBinaryTuple @type string A binary tuple to parse.
     * @return @type array Array with timestamp, tokenLength and deviceToken keys.
     */
    protected function _parseBinaryTuple($sBinaryTuple)
    {
        return unpack('Ntimestamp/ntokenLength/H*deviceToken', $sBinaryTuple);
    }

    public function view_service_proc()
    {
        $html = array('resp' => '', 'status' => 'fail');
        $post = $this->input->get();
        if (isset($post['mail'])) {
            $where = array('email' => $post['mail'], 'status_book' => 'service in progress');
            $hist = $this->db->order_by('datetime_book',
                'desc')->select('datetime_book,progress,eta,police_number,status_book,eta,user_notes,service_type,service,service_notes,location')->get_where('view_service_booking',
                $where);
            if ($hist->num_rows() > 0) {
                $hist = $hist->result_array();
                foreach ($hist as $k => $v) {
                    if ($v['progress'] != '' && $v['eta'] != '') {
                        list($date, $time) = explode(' ', $v['datetime_book']);
                        $prog = $v['progress'];
                        $eta = date('Y-m-d H:i:s', strtotime($date . ' ' . $prog . ' +' . $v['eta'] . 'hours'));

                        $hist[$k]['etas'] = "$eta ( in {$v['eta']} hours )";
                    }
                }
                $html = array('resp' => $hist, 'status' => 'ok');
            }
        }
        echo json_encode($html);
    }

    public function testing()
    {
        $headers = array(
            'Authorization: key=' . GAPI_KEY,
            'Content-Type: application/json'
        );
        $options = array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );
        echo cURL('http://103.244.206.213/jdiweb/api_score_hist.php/api_sh/test', $options);
    }

    private function record_sort($records, $field, $reverse = false)
    {
        $hash = array();
        $i = 0;
        foreach ($records as $record) {
            $hash[$record[$field] . $i] = $record;
            $i++;
        }

        ($reverse) ? krsort($hash) : ksort($hash);

        $records = array();

        foreach ($hash as $record) {
            $records[] = $record;
        }

        return $records;
    }

    function array_merge_to_indexed()
    {
        $result = array();

        foreach (func_get_args() as $arg) {
            foreach ($arg as $innerArr) {
                $result[] = array_values($innerArr);
            }
        }

        return $result;
    }

    private function prettyPrint($json)
    {
        $result = '';
        $level = 0;
        $in_quotes = false;
        $in_escape = false;
        $ends_line_level = null;
        $json_length = strlen($json);

        for ($i = 0; $i < $json_length; $i++) {
            $char = $json[$i];
            $new_line_level = null;
            $post = "";
            if ($ends_line_level !== null) {
                $new_line_level = $ends_line_level;
                $ends_line_level = null;
            }
            if ($in_escape) {
                $in_escape = false;
            } else {
                if ($char === '"') {
                    $in_quotes = !$in_quotes;
                } else {
                    if (!$in_quotes) {
                        switch ($char) {
                            case '}':
                            case ']':
                                $level--;
                                $ends_line_level = null;
                                $new_line_level = $level;
                                break;

                            case '{':
                            case '[':
                                $level++;
                            case ',':
                                $ends_line_level = $level;
                                break;

                            case ':':
                                $post = " ";
                                break;

                            case " ":
                            case "\t":
                            case "\n":
                            case "\r":
                                $char = "";
                                $ends_line_level = $new_line_level;
                                $new_line_level = null;
                                break;
                        }
                    } else {
                        if ($char === '\\') {
                            $in_escape = true;
                        }
                    }
                }
            }
            if ($new_line_level !== null) {
                $result .= "\n" . str_repeat("\t", $new_line_level);
            }
            $result .= $char . $post;
        }

        return $result;
    }

    function broadcast_apn()
    {
        $deviceToken = $this->input->get('deviceToken');
        $message = urldecode($this->input->get('message'));

        $this->apns_model->send_apns($deviceToken, $message);
    }

    function broadcast_gcm()
    {

        $mail = $this->input->post('mail');
        $rid = $this->input->post('rid');
        $msg = $this->input->post('msg');
        $ckey = $this->input->post('ckey');
        $return = false;
        $lname = 'gcm.log';


        $fields = array(
            'collapse_key' => $ckey,
            'data' => $msg,
            'registration_ids' => $rid,

        );
        $headers = array(
            'Authorization: key=' . GAPI_KEY,
            'Content-Type: application/json'
        );
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($fields)
        );
        $delim = ($return) ? '<br>' : PHP_EOL;
        $resp = cURL(GCM_ACT, $options);
        $log = '--------------------------------------------------------------------------------------------------------' . $delim;
        $log .= date('Y-m-d H:i') . $delim;
        $buf = json_decode($resp);

        if ($buf != null) {

            $log .= "Total Broadcast " . ($buf->success + $buf->failure) . $delim;
            $log .= "Success " . $buf->success . $delim;
            $log .= "Failure " . $buf->failure . $delim;
            $log .= "canonical " . $buf->canonical_ids . $delim . $delim;
            $log .= $resp . $delim;

            if ($buf->failure >= 1) {
                $log .= 'Reason' . $delim;
                foreach ($buf->results as $k => $bufs) {
                    if ($key = key((array)$bufs) == 'error') {
                        if (isset($mail[$k])) {
                            $log .= $mail[$k] . ' --> ' . $bufs->error . $delim;
                        }
                    }
                }
            }

        } else {
            if (preg_match_all('/<h1>(.*?)<\/h1>|(\s\d{3})/i', $resp, $got)) {
                $log .= $got[2][1] . ' ' . $got[1][0] . $delim;

            } else {
                $log .= 'Unknown response';

            }

        }
        $log .= '--------------------------------------------------------------------------------------------------------' . $delim;
        if ($return) {
            return $log;
        } else {
            cutomlogs('./' . APPPATH . 'logs/' . $lname, $log);
        }
    }

    public function service_status()
    {
        $email = $this->input->get('email');

        $resp = false;

        if ($email != '') {
            $status_book = array();
            $this->db->where('email', $email);
            $service = $this->db->get('view_service_booking')->result_array();

            foreach ($service as $v) {
                if (($v['status_book'] == 'finished' || $v['status_book'] == 'canceled') && strtotime($v['datetime_book']) < strtotime('yesterday')) {
                    continue;
                }

                if ($v['eta'] != '' && count(explode(' ', $v['eta'])) == 2) {
                    $eta = explode(' ', $v['eta']);
                    if ($eta[1] == 'Hour') {
                        $eta = date('Y-m-d H:i', strtotime('+' . $eta[0] . ' hour', strtotime($v['datetime_book'])));
                    } else {
                        if ($eta[1] == 'Day') {
                            $eta = date('Y-m-d H:i', strtotime('+' . $eta[0] . ' day', strtotime($v['datetime_book'])));
                        } else {
                            if ($eta[1] == 'Week') {
                                $eta = date('Y-m-d H:i',
                                    strtotime('+' . $eta[0] . ' week', strtotime($v['datetime_book'])));
                            } else {
                                if ($eta[1] == 'Month') {
                                    $eta = date('Y-m-d H:i',
                                        strtotime('+' . $eta[0] . ' month', strtotime($v['datetime_book'])));
                                }
                            }
                        }
                    }
                } else {
                    $eta = '';
                }

                $status_book[] = array(
                    'id' => $v['id_service_booking'],
                    'when' => $v['datetime_book'],
                    'service' => ucfirst($v['service_type']) . ', ' . $v['service'],
                    'location' => $v['location'],
                    'car' => $v['police_number'],
                    'status' => ucfirst($v['status_book']),
                    'eta' => $eta,
                    'user_notes' => $v['user_notes'],
                    'service_notes' => $v['service_notes'],
                    'type' => 'service',
                );
            }

            $this->db->where('ref_user_mail', $email);
            $tdrive = $this->db->get('view_test_drive_booking')->result_array();

            foreach ($tdrive as $v) {
                if (($v['status_book'] == 'finished' || $v['status_book'] == 'canceled') && strtotime($v['datetime_book']) < strtotime('yesterday')) {
                    continue;
                }

                $status_book[] = array(
                    'id' => $v['id_test_drive_booking'],
                    'when' => $v['datetime_book'],
                    'service' => 'Test Drive',
                    'location' => $v['location'],
                    'car' => $v['car'],
                    'status' => ucfirst($v['status_book']),
                    'pre_notes' => $v['notes'],
                    'after_notes' => $v['comments'],
                    'type' => 'tdrive',
                );
            }

            $resp = $this->record_sort($status_book, 'when', true);
        }

        echo json_encode($resp);
    }

    public function service_status_detail()
    {
        $id = $this->input->get('id');
        $email = $this->input->get('email');
        $type = $this->input->get('type');
        $resp = false;

        if ($email != '' && $id != '' && $type != '') {

            if ($type == 'service') {
                $this->db->where('id_service_booking', $id);
                $this->db->where('email', $email);
                $service = $this->db->get('view_service_booking')->row_array();

                if (count($service) > 0) {
                    $eta = explode(' ', $service['eta']);
                    if ($eta[1] == 'Hour') {
                        $eta = [
                            date('Y-m-d H:i', strtotime('+' . $eta[0] . ' hour', strtotime($service['datetime_book']))),
                            ''
                        ];
                    } else {
                        if ($eta[1] == 'Day') {
                            $eta = [
                                date('Y-m-d H:i',
                                    strtotime('+' . $eta[0] . ' day', strtotime($service['datetime_book']))),
                                ''
                            ];
                        }
                    }
                    $resp = array(
                        'id' => $service['id_service_booking'],
                        'when' => $service['datetime_book'],
                        'service' => $service['service_type'] . ', ' . $service['service'],
                        'location' => $service['location'],
                        'car' => $service['police_number'],
                        'status' => $service['status_book'],
                        'eta' => $eta,
                        'user_notes' => $service['user_notes'],
                        'service_notes' => $service['service_notes'],
                    );
                }
            }

            if ($type == 'tdrive') {
                $this->db->where('id_test_drive_booking', $id);
                $this->db->where('ref_user_mail', $email);
                $tdrive = $this->db->get('view_test_drive_booking')->row_array();

                if (count($tdrive) > 0) {
                    $resp = array(
                        'id' => $tdrive['id_test_drive_booking'],
                        'when' => $tdrive['datetime_book'],
                        'service' => 'Test Drive',
                        'location' => $tdrive['location'],
                        'car' => $tdrive['car'],
                        'status' => $tdrive['status_book'],
                        'pre_notes' => $tdrive['notes'],
                        'after_notes' => $tdrive['comments'],
                    );
                }
            }
        }

        echo json_encode(array($resp));
    }
}

/* End of file api.php */
/* Location: ./application/controllers/api.php */
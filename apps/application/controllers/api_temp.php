<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Global API Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Global API Management
 *********************************************/
class Api_temp extends CI_Controller
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_model', 'Api_model');
        // echo BASEPATH;
    }

    public function index()
    {
    }

    public function homepage()
    {
        $mail = $this->input->get('mail');
        $html = 'fail';
        if (!$mail) {
            echo $html, exit;
        }
        $ucars = $this->Api_model->fetch_user_cars($mail);
        if ($ucars) {
            $html = $ucars;
        }
        echo json_encode(array('buff' => $html));
    }

    public function user_login()
    {
        $get = $this->input->get();
        $html = json_encode(array('buff' => array('status' => 'fail')));
        if (isset($get['mail']) && isset($get['pass'])) {
            $exist = $this->Api_model->check_login($get['mail'], $get['pass']);
            if ($exist) {
                $exist['status'] = 'ok';
                $html = json_encode(array('buff' => $exist));
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
        $post = $this->input->get();
        if (isset($post['card_id']) && $post['username'] && isset($post['password']) && isset($post['email']) && isset($post['address'])
            && isset($post['birthday']) && isset($post['phone_number']) && isset($post['religion'])
            && isset($post['sex']) /* && isset($post['avatar'])  && isset($post['user_type']) && isset($post['ref_location'])  */
        ) {
            $data = array(
                'username' => $post['username'],
                'password' => $post['password'],
                'address' => $post['address'],
                'email' => $post['email'],
                'birthday' => $post['birthday'],
                'phone_number' => $post['phone_number'],
                'religion' => $post['religion'],
                // 'ref_location'	=> $post['ref_location'],
                'card_id' => $post['card_id'],
                'sex' => $post['sex'],
                // 'avatar'	  		=> $post['avatar'],
                // 'user_type'	  	=> $post['user_type']
            );
            $this->Api_model->regist($data);
        } else {
            echo 'param error';
        }
    }

    public function user_profile()
    {
        $response = json_encode(array('buff' => 'fail'));
        $post = $this->input->get('mail');
        if ($post) {
            $profile = $this->Api_model->fetch_user_profile($post);
            if ($profile) {
                $response = json_encode(array('buff' => $profile));
            }
        }
        echo $response;
    }

    public function user_profile_request($mail)
    {
        $response = 0;
        if ($mail) {
            $profile = $this->Api_model->fetch_user_profile($mail);
        }
        return $profile;
    }

    public function store_user_profile()
    {
        $get = $this->input->get();
        $resp = false;
        if (isset($get['cardid']) && isset($get['uname']) && isset($get['mail'])
            && isset($get['addr']) && isset($get['phone']) && isset($get['birthday'])
            && isset($get['religion']) && isset($get['sex'])
        ) {
            $buf = array(
                'card_id' => $get['cardid'],
                'username' => $get['uname'],
                'email' => $get['mail'],
                'birthday' => $get['birthday'],
                'phone_number' => $get['phone'],
                'address' => $get['addr'],
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
        $post = ($post == '') ? 'all' : $post;
        if ($post) {
            $news = $this->Api_model->fetch_news($post);
            if ($news) {
                $response = json_encode(array('buff' => $news));
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

    public function all_workshop()
    {
        $response = json_encode(array('buff' => 'fail'));
        $ws = $this->Api_model->fetch_all_workshop();
        if ($ws) {
            $response = json_encode(array('buff' => $ws));
        }
        echo $response;
    }

    public function user_cars()
    {
        $get = $this->input->get();
        $resp = 'fail';
        if (isset($get['mail'])) {
            $id = db_get_one('user', 'id_user', "email = '" . $get['mail'] . "' and user_type = 'VIP'");
            if ($id) {
                $sql = "select id, concat(brands,' ',types,' ',series)as model FROM jdi_view_user_cars ";
                $sql .= "where id_user IN ($id) ";

                $ucars = $this->db->query($sql);
                if ($ucars->num_rows() > 0) {
                    $resp = $ucars->result_array();
                }
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function workshop_service()
    {
        $get = $this->input->get();
        $resp = 'fail';
        if (isset($get['service_type'])) {
            $this->db->where_in('id_service_type', array($get['service_type']));
            $service = $this->db->get('car_services');
            if ($service->num_rows() > 0) {
                $buff['servicelst'] = $service->result_array();
            }

            $buff['date'] = get_date_list(date('Y-m-d', strtotime('+1days')), date('Y-m-d', strtotime('+8days')));
            $buff['time'] = get_available_time();

            $resp = $buff;
        }

        echo json_encode(array('buff' => $resp));
    }

    public function consult_index()
    {
        $i = $this->input->get('i');
        if ($i) {
            echo json_encode(array('buff' => $this->Api_model->consult_thread())), exit;
        }
        echo json_encode(array('buff' => 'fail')), exit;
    }

    public function consult_detail()
    {
        $thread = $this->input->get('threadid');
        $resp = json_encode(array('buff' => 'fail'));
        if (!$thread) {
            echo $resp, exit;
        }

        $detail = $this->Api_model->consult_detail($thread);

        if ($detail) {
            $resp = json_encode(array('buff' => $detail));

        }
        echo $resp, exit;

    }

    public function complain_index()
    {
        $i = $this->input->get('i');
        if ($i) {
            echo json_encode(array('buff' => $this->Api_model->complain_thread()));
        }
        echo json_encode(array('buff' => 'fail')), exit;
    }

    public function complain_detail()
    {
        $thread = $this->input->get('threadid');
        $resp = json_encode(array('buff' => 'fail'));
        if (!$thread) {
            echo $resp, exit;
        }

        $detail = $this->Api_model->complain_detail($thread);

        if ($detail) {
            $resp = json_encode(array('buff' => $detail));

        }
        echo $resp, exit;
    }

    public function store_complain()
    {
        $get = $this->input->get();
        $resp = false;
        if (isset($get['about']) && isset($get['user_car']) && isset($get['location']) && $get['parent'] == '0' && isset($get['from']) && isset($get['text'])) {

            $complain['about'] = $get['about'];
            $complain['ref_user_car'] = $get['user_car'];
            $complain['ref_location'] = $get['location'];

            $this->db->insert('tech_complain', $complain);

            $chat['complain_id'] = $this->db->insert_id();
            $chat['parent_id'] = $get['parent'];
            $chat['text'] = $get['text'];
            $chat['dest_id'] = 1;
            $chat['from_id'] = $get['from'];

            $this->db->insert('chat', $chat);

            $resp = 'successfuly created';

        } elseif (isset($get['parent']) && $get['parent'] != '0' && isset($get['from']) && isset($get['text']) && isset($get['complain_id'])) {
            if (db_get_one('tech_complain', 'id_tech_complain',
                "id_tech_complain = '" . $get['complain_id'] . "' and status = 'open'")) {
                $chat['complain_id'] = $get['complain_id'];
                $chat['parent_id'] = $get['parent'];
                $chat['text'] = $get['text'];
                $chat['dest_id'] = '0';
                $chat['from_id'] = $get['from'];
                $chat['parent_id'] = $get['parent_id'];

                $this->db->insert('chat', $chat);

                $resp = 'successfuly sent';
            }
        }

        echo json_encode(array('buff' => $resp));
    }

    public function store_consult()
    {
        $get = $this->input->get();
        $resp = false;
        if (isset($get['about']) && isset($get['location']) && $get['parent'] == '0' && isset($get['from'])) {

            $cons['about'] = $get['about'];
            $cons['ref_location'] = $get['location'];

            $this->db->insert('tech_consult', $cons);

            $chat['consult_id'] = $this->db->insert_id();
            $chat['parent_id'] = $get['parent'];
            $chat['text'] = $get['text'];
            $chat['dest_id'] = 1;
            $chat['from_id'] = $get['from'];

            $this->db->insert('chat', $chat);

            $resp = 'successfuly created';

        } elseif (isset($get['parent_id']) && $get['parent'] != '0' && isset($get['from']) && isset($get['text']) && isset($get['consult_id'])) {
            if (db_get_one('tech_consult', 'id_tech_consult',
                "id_tech_consult = '" . $get['consult_id'] . "' and status = 'open'")) {
                $chat['consult_id'] = $get['consult_id'];
                $chat['parent_id'] = $get['parent'];
                $chat['text'] = $get['text'];
                $chat['dest_id'] = '0';
                $chat['from_id'] = $get['from_id'];

                $this->db->insert('chat', $chat);

                $resp = 'successfuly sent';
            }
        }

        echo json_encode(array('buff' => $resp));
    }

    public function bank_list()
    {
        $resp = false;
        if ($this->input->get('i')) {
            $bank = $this->db->select('id_ref_bank as id,bank_name,bank_thumb')->order_by('urut', 'asc')
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

    public function dp_list()
    {
        $resp = 'fail';
        if ($this->input->get('i')) {
            $resp = $this->db->order_by('down_payment', 'asc')->get('ref_down_payment')->result_array();
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
            $cars = $this->db->order_by('create_date,brands asc')->get_where('view_cars', "ref_publish = '1'");
            if ($cars->num_rows() > 0) {
                foreach ($cars->result_array() as $k => $v) {
                    $buff[$k]['label'] = $v['types'] . ' ' . $v['series'] . ' ' . $v['model'] . ' ' . $v['transmisi'];
                    $buff[$k]['otrk'] = $v['car_price'];
                    $buff[$k]['otrv'] = rp($v['car_price']);
                    $resp = $buff;
                }
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    public function credit_simulation()
    {
        $get = $this->input->get();
        $resp = json_encode(array('buff' => 'fail'));
        if (isset($get['otr'], $get['tenor'], $get['dp'], $get['bank'])) {
            $where = array('a.ref_bank' => $get['bank'], 'a.ref_tenor' => $get['tenor']);
            $sim = $this->db->select('a.*,b.bank_name,c.tenor')->from('credit_simulation a')
                ->join('ref_bank b', 'a.ref_bank = b.id_ref_bank', 'inner')
                ->join('ref_tenor c', 'a.ref_tenor = c.id_ref_tenor', 'inner')
                ->where($where)->get();

            if ($sim->num_rows() > 0) {
                $sim = $sim->row_array();
                $calc = emulator_credit($get['otr'], $get['dp'], $get['tenor'], $sim['bunga'], $sim['asuransi'],
                    $sim['provisi'], $sim['administrasi']);
                $calc['tenor'] = $calc['tenor'] . ' Tahun (' . $calc['tenor'] * 12 . ' bulan)';

                $resp = json_encode($calc);
            }
        }
        echo $resp;
    }

    function religion_lst()
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

    function sex_lst()
    {
        echo json_encode(array('buff' => array('male' => 'male', 'female' => 'female', 'other' => 'other')));
    }

    /* for ford brand only */
    function car_type()
    {
        $resp = json_encode(array('buff' => 'fail'));

        $where = array('ref_publish' => 1, 'brand_publish' => 1, 'id_brands' => 1);
        $product = $this->db->select('id_car_types,brands,types')->get_where('view_car_types', $where);

        if ($product->num_rows() > 0) {
            foreach ($product->result_array() as $k => $v) {
                $buff[$k]['id'] = $v['id_car_types'];
                $buff[$k]['product'] = $v['brands'] . ' ' . $v['types'];
            }
            $resp = json_encode(array('buff' => $buff));
        }
        echo $resp;
    }

    function car_detail()
    {
        $get = $this->input->get();
        $resp = array('buff' => 'fail', 'status' => 'fail');
        if (isset($get['id']) && !isset($get['carid'])) {
            $model = $this->db->where_in('id_type', array($get['id']))->where_in('id_brands',
                array('1'))->where('ref_publish', 1)->get('view_cars');

            if ($model->num_rows() > 0) {
                $model = $model->result_array();
                foreach ($model as $k => $v) {
                    $buff[$k]['id'] = $v['id'];
                    $buff[$k]['label'] = $v['brands'] . ' ' . $v['types'] . ' ' . $v['series'] . ' ' . $v['model'] . ' ' . $v['transmisi'] . ' ' . $v['car_cc'];
                }
                $resp['buff'] = $buff;
                $resp['status'] = 'Ok';
            }

        } elseif (isset($get['id']) && isset($get['carid'])) {
            $colors = $this->db->from('car_colors a')
                ->join('ref_color b', 'a.ref_color = b.id_ref_color', 'inner')
                ->where_in('id_car', array($get['carid']))->get();

            if ($colors->num_rows() > 0) {
                foreach ($colors->result_array() as $k => $v) {
                    $c = $this->db->get_where('view_cars', array('id' => $get['carid']))->row_array();
                    $buff[$k]['id'] = $v['ref_color'];
                    $buff[$k]['color'] = $v['color'];
                    $buff[$k]['thumb'] = (file_exists('./uploads/cars/colors/' . $v['car_color_thumb']) && $v['car_color_thumb'] != '') ? substr(base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'],
                        0, -4) : '';
                    $buff[$k]['price'] = 'OTR Price Rp.' . rp($c['car_price']);
                    $buff[$k]['detail'] = $c['brands'] . ' ' . $c['types'] . ' ' . $c['series'] . ' ' . $c['model'] . ' ' . $c['transmisi'] . ' ' . $c['car_cc'];

                }
                $resp['buff'] = $buff;
                $resp['status'] = 'Ok';
            }
        }
        echo json_encode($resp);
    }

    /* for reference color by car */
    function car_color()
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

    function store_usercar()
    {
        $get = $this->input->get();
        $resp = false;
        if (isset($get['car']) && isset($get['user']) && isset($get['color']) && isset($get['pn'])
            && isset($get['vn']) && isset($get['sd']) && isset($get['ins_date']) && isset($get['lm']) && isset($get['act'])
        ) {
            $buff = array(
                'id_user' => $get['user'],
                'id_car' => $get['car'],
                'id_car_color' => $get['color'],
                'police_number' => $get['pn'],
                'vin_number' => $get['vn'],
                'stnk_date' => $get['sd'],
                'insurance_date' => $get['ins_date'],
                'last_mileage' => $get['lm']
            );
            if (isset($get['user_car_id']) && $get['act'] == 'u') {
                $this->db->where(array(
                    'id_user' => $get['user'],
                    'id_car' => $get['car'],
                    'id_user_cars' => $get['user_car_id']
                ))->update('user_car', $buff);
                if ($this->db->_error->message()) {
                    $resp = $this->db->_error->message();
                } else {
                    $resp = 'Successfull Updated';
                }

            } elseif ($get['act'] == 'c') {
                $this->db->insert('user_car', $buff);
                if ($this->db->_error->message()) {
                    $resp = $this->db->_error->message();
                } else {
                    $resp = 'Successfull Updated';
                }
            }
        }
        echo json_encode(array('buff' => $resp));
    }

    function car_test_drive_lst()
    {
        $resp = json_encode(array('buff' => 'fail'));
        $ct = $this->db->get_where('view_cars_test_drive', array('ref_publish' => 1));
        echo $this->db->_error_message();
        if ($ct->num_rows() > 0) {
            $ct = $ct->result_array();
            foreach ($ct as $k => $v) {
                $color = db_get_one('car_colors', 'car_color_thumb', "id_car = '" . $v['id'] . "'");

                $buff[$k]['id'] = $v['id_car_test_drive']; //$v['id'];
                $buff[$k]['car'] = $v['brands'] . ' ' . $v['types'] . ' ' . $v['series'] . ' ' . $v['transmisi'];
                $buff[$k]['thumb'] = ($color && file_exists('./uploads/cars/colors/' . $color)) ? substr(base_url() . 'uploads/cars/colors/' . $color,
                    0, -4) : '';
            }

            $time = get_available_time_td();
            $date = get_date_list(date('Y-m-d', strtotime('+1days')), date('Y-m-d', strtotime('+8days')));

            foreach ($time as $k => $v) {
                $time_val[] = str_replace('.', ':', $v);
            }

            foreach ($date as $k => $v) {
                $date_val[$k] = date('Y-m-d', strtotime($v));
            }


            // $resp = json_encode(array('buff' => $buff,/* 'time' => $time,  */'date' => $date, 'time_val' => $time_val, 'date_val' => $date_val ));
            $resp = json_encode(array(
                'buff' => $buff,/* 'time' => $time, */
                'date' => $date,
                'time_val' => $time_val,
                'date_val' => $date_val
            ));
        }

        echo $resp;
    }

    function store_test_drive_book()
    {
        $get = $this->input->get();
        $resp = false;
        if (isset($get['user']) && isset($get['car']) && isset($get['location']) && isset($get['time']) && isset($get['date']) /* && isset($get['un']) */) {
            $u = db_get_one('user', 'user_type', "email = '" . $get['user'] . "'");
            $id = db_get_one('user', 'id_user', "email = '" . $get['user'] . "'");
            $location = db_get_one('ref_location', 'location', "id_ref_location = '" . $get['location'] . "'");
            $dcrc_number = db_get_one('auth_user', 'phone', "ref_location = '" . $get['location'] . "'");
            $client_number = db_get_one('user', 'phone_number', "email = '" . $get['user'] . "'");
            $client_name = db_get_one('user', 'username', "email = '" . $get['user'] . "'");


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
                            $resp = 'you cannot booking the same schedule';
                        }
                        break;
                    case 'sales':
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
            send_sms($dcrc_number, $msg_dcrc);

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

    function store_repair_service_book()
    {
        ob_start();
        $get = $this->input->get();
        $resp = false;
        if (isset($get['user']) && isset($get['car']) && isset($get['service']) && isset($get['location']) && isset($get['time']) && isset($get['date'])) {

            $profile = $this->user_profile_request($get['user']);
            $profile_id = $profile["id_user"];
            $get['user'] = $profile_id;
            $location = db_get_one('ref_location', 'location', "id_ref_location = '" . $get['location'] . "'");

            $u = db_get_one('user', 'user_type', "id_user = '" . $get['user'] . "' and (user_type = 'vip')");
            $book = array(
                'ref_user_cars' => $get['car'],
                'ref_service' => $get['service'],
                'id_ref_location' => $get['location'],
                'datetime_book' => $get['date'] . ' ' . $get['time'],
            );
            if ($u != '0') {
                switch ($u) {
                    case 'vip':
                        $this->db->insert('car_service_booking', $book);
                        if ($this->db->_error_message()) {
                            $resp = $this->db->_error_message();
                        } else {
                            $resp_status = true;
                            $resp = 'Successfull booked';
                        }
                        break;
                }
            }
        }
        echo json_encode(array('buff' => $resp));

        // now force PHP to output to the browser...
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Connection: close');
        ob_end_flush();
        ob_flush();
        flush(); // yes, you need to call all 3 flushes!

        if (isset($resp_status) && $resp_status === true) {
            $msg = '[TEMP] An user book a service at ' . $location . ' on ' . $get['date'] . ' ' . $get['time'];
            $url = 'https://alpha.zenziva.net/apps/smsapi.php?userkey=ovzlhn&passkey=0Mi4kOGb8D&nohp=081584040918&pesan=' . urlencode($msg);
            exec("curl '$url' > /dev/null &");
        }

    }

}

/* End of file api.php */
/* Location: ./application/controllers/api.php */
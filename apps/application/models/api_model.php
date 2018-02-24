<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Global API  Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Global API model
 ***********************************/
class Api_model extends CI_Model
{

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /* Check User Login
     * @param string $uname
     * @param string $pass
     * @return false on failure or detail info user on success
     */
    function check_login($mail, $pass)
    {
        // $username = strtolower($mail);
        $query = $this->db->query("SELECT * FROM " . $this->db->dbprefix('user') . " WHERE LCASE(email) = ?",
            array($mail));
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $this->load->library('encrypt');
            $userpass = $this->encrypt->decode($row['password']);
            if (($pass == $userpass) && ($pass != "")) {
                # insert to log
                $data = array(
                    'id_user' => $row['id_user'],
                    'action' => 'Login',
                    'desc' => 'Login:succeed; IP:' . $_SERVER['SERVER_ADDR'] . '; Email:' . $mail . ';',
                    'create_date' => date('Y-m-d H:i:s'),
                );
                $this->db->where('id_user', $row['id_user'])->update('user', array('is_logged_in' => 1));
                insert_to_log($data);
                $return = $row;
            } else {
                # insert to log
                $data = array(
                    'id_user' => 0,
                    'action' => 'Login',
                    'desc' => 'Login:failed; IP:' . $_SERVER['SERVER_ADDR'] . '; Email:' . $mail . ';',
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data);
                $return = false;
            }
        } else {

            #insert to log
            $data = array(
                'id_user' => 0,
                'action' => 'Login',
                'desc' => 'Login:failed; IP:' . $_SERVER['SERVER_ADDR'] . '; Email:' . $mail . ';',
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data);
            $return = false;
        }

        return $return;
    }

    /* Check whether user logging in or not
     * @param string $uname
     * @return false on failure or true on success
     */
    function is_login($uname)
    {
        return ($uname && $this->db->get_where('user',
                array('is_logged_in' => 1, 'username' => strtolower($uname)))->num_rows() > 0) ? 'true' : 'false';
    }

    /* Logout user
     * @param string $uname
     * @return void
     */
    function logout($uname)
    {
        $usr = $this->db->get_where('user', array('username' => $uname, 'is_logged_in' => 1));
        if ($usr->num_rows() > 0) {
            $usr = $usr->row();
            $this->db->where('id_user', $usr->id_user)->update('user', array('is_logged_in' => 0));
        }
    }

    /**
     * Register user
     * @param array $data
     * @return void
     */
    function regist($data)
    {
        $this->db->insert('user', $data);
        return $this->db->_error_message();
    }

    /**
     * Fetching user data profile
     * @param array $data
     * @return array user profile on success or false on failure
     */
    function fetch_user_profile($data)
    {
        // $data['is_logged_in'] = 1;
        // $data = array('username' => $data);
        $this->load->library('encrypt');
        $profile = $this->db->get_where('user', array('email' => $data));
        if ($profile->num_rows() > 0) {
            $profile = $profile->row_array();
            $profile['avatar'] = ($profile['avatar'] != '' && file_exists('./uploads/ava/' . $profile['avatar'])) ? base_url() . 'uploads/ava/' . $profile['avatar'] : '';
            $profile['expire_date'] = ($profile['expire_date']) ? date('Y-m-d', $profile['expire_date']) : '';
            $profile['password'] = $this->encrypt->decode($profile['password']);
            $profile['city_name'] = db_get_one('ref_city', 'name', "id = '" . $profile['city'] . "'");;
            $profile['workshop_name'] = db_get_one('ref_location', 'location',
                "id_ref_location = '" . $profile['nearest_workshop'] . "'");;
        } else {
            $profile = false;
        }
        return $profile;
    }

    /**
     * Fetching user car
     * @param array $data
     * @return array user car on success or false on failure
     */
    function fetch_user_cars($mail)
    {
        // $data['is_logged_in'] = 1;
        $profile = $this->db->get_where('view_user_cars', array('email' => $mail));
        if ($profile->num_rows() > 0) {
            $profile = $profile->result_array();
            foreach ($profile as $k => $v) {

                $profile[$k]['car_color_thumb'] = ($v['car_color_thumb'] != '' && file_exists('./uploads/cars/colors/' . $v['car_color_thumb'])) ? substr(base_url() . 'uploads/cars/colors/' . $v['car_color_thumb'],
                    0, -4) : '';
                $profile[$k]['insurance_date'] = (string)max(get_days_left($v['insurance_date']), 0);
                $profile[$k]['stn_date'] = $v['stnk_date'];
                $profile[$k]['stnk_date'] = (string)max(get_days_left($v['stnk_date']), 0);
                $profile[$k]['ins_date'] = (string)$v['insurance_date'];


            }
            $buff['usercar'] = $profile;
        } else {
            $buff = false;
        }
        // $buff['news'] = $this->fetch_news(3);

        return $buff;
    }

    /**
     * Fetching news
     * @param string $limit
     * @return array user profile on success or false on failure
     */
    function fetch_news($limit, $pg = false, $type = false)
    {
        // $data['is_logged_in'] = 1;
        // $data = array('username' => $data);

        if ($limit != 'all') {
            $this->db->limit($limit);
        }


        if ($pg && $limit) {
            $this->db->limit($limit, $pg);

        }

        $type = ($type) ? $type : 'news';


        $this->db->where('type', $type);
        $this->db->where('ref_publish', 1);
        // $this->db->order_by('create_date','desc');
        $this->db->order_by('is_new desc, create_date desc');
        $news = $this->db->get('news');


        // echo last_query();

        $this->load->helper('text');
        if ($news->num_rows() > 0) {
            $news = $news->result_array();
            foreach ($news as $k => $v) {
                // $news[$k]['title'] = substr(word_limiter($v['title'],6),0,-7);
                // $news[$k]['title'] = substr(word_limiter($v['title'],6),0,-7) . '. . .';
                $news[$k]['title'] = substr($v['title'], 0, 100) . ' ...';
                $news[$k]['content'] = ''; //preg_replace('/(<[^>]+) style=".*?"/i', '$1', strip_tags(htmlspecialchars_decode($v['content']),'<p>,<br>'));
                $news[$k]['thumb'] = ($v['thumb'] != '' && file_exists('./uploads/news/' . $v['thumb'])) ? base_url() . 'uploads/news/' . $v['thumb'] : '';
                $news[$k]['link'] = site_url("api/detail_news/?idn=" . $v['id_news']);

            }

        } else {
            $news = false;
        }
        return $news;
    }

    /**
     * Fetching news
     * @param string $limit
     * @return array user profile on success or false on failure
     */
    function fetch_total_news($limit, $pg = false, $type = false)
    {
        // $data['is_logged_in'] = 1;
        // $data = array('username' => $data);

        // if($limit != 'all') {
        // $this->db->limit($limit);
        // }

        // if($pg && $limit) {
        // $this->db->limit($limit,$pg);

        // }

        $type = ($type) ? $type : 'news';


        $this->db->select('count(*) as total');
        $this->db->where('type', $type);
        $this->db->where('ref_publish', 1);
        $this->db->order_by('create_date', 'desc');
        $news = $this->db->get('news');

        // $this->load->helper('text');

        // echo last_query();

        if ($news->num_rows() > 0) {

            $news = $news->row()->total;

        } else {
            $news = 0;
        }
        return $news;
    }


    /**
     * Fetching contact
     * @return array contact on success or false on failure
     */
    function fetch_contact()
    {
        // $this->db->where('ref_publish',1);
        // $this->db->order_by('create_date','desc');
        $contact = $this->db->get('contacts');
        if ($contact->num_rows() > 0) {
            $contact = $contact->result_array();
        } else {
            $contact = false;
        }
        return $contact;
    }

    /**
     * Fetching car model
     * @return array car model on success or false on failure
     */
    function fetch_car_model()
    {
        $this->db->where('ref_publish', 1);
        // $this->db->order_by('create_date','desc');
        $model = $this->db->select('id_car_types,brands,types')->get('view_car_types');
        if ($model->num_rows() > 0) {
            $n = 0;
            $m = $model->result_array();
            foreach ($m as $k => $v) {
                $n++;
                $models[$n]['id'] = $v['id_car_types'];
                $models[$n]['model'] = $v['brands'] . ' ' . $v['types'];
            }
        } else {
            $models = false;
        }
        return $models;
    }

    /**
     * Fetching car spare part
     * @return array car spare part on success or false on failure
     */
    function fetch_car_spare_part($model, $part = false)
    {
        $this->db->where('ref_publish', 1);

        if ($model && !$part) {
            $this->db->distinct()->select('id_ref_spare_part,ref_spare_part')->where_in('id_type', array($model));
        }

        if ($model && $part) {
            $this->db->select('id_car_spare_part,spare_part,thumb')->where_in('id_type',
                array($model))->where_in('id_ref_spare_part', array($part));
        }

        $sp = $this->db->get('view_car_spare_parts');

        if ($sp->num_rows() > 0) {
            $sp = $sp->result_array();
            foreach ($sp as $n => $g) {
                if ($part) {
                    $thumb = ($g['thumb'] != '' && file_exists('./uploads/spare_part/' . $g['thumb'])) ? base_url() . 'uploads/spare_part/' . $g['thumb'] : '';
                    $sp[$n]['thumb'] = $thumb;
                }
            }

        } else {
            $sp = false;
        }
        return $sp;
    }


    function fetch_car_accessories($model, $vendor = null, $asc = null)
    {
        $this->db->where('ref_publish', 1);
        $this->db->group_by('id_ref_accessories');

        if ($model && $asc == null) {
            $this->db->distinct()->select('id_ref_accessories AS id_car_accessories,accessories')->where_in('id_type',
                array($model));
        }

        if ($model) {
            $this->db->distinct()->select('id_ref_accessories AS id_car_accessories,accessories,thumb,price')->where_in('id_type',
                array($model));
        }

        if ($vendor != null) {
            $this->db->where_in('id_vendor', array($vendor));
        }

        if ($asc != null) {
            $this->db->where_in('id_ref_accessories', array($asc));
        }

        $sp = $this->db->get('view_car_accessories');


        if ($sp->num_rows() > 0) {
            $sp = $sp->result_array();
            foreach ($sp as $n => $g) {
                $sp[$n]['accessories'] = html_entity_decode($g['accessories']);
                if ($asc) {
                    $thumb = ($g['thumb'] != '' && file_exists('./uploads/accessories/' . $g['thumb'])) ? base_url() . 'uploads/accessories/' . $g['thumb'] : '';
                    $sp[$n]['thumb'] = $thumb;
                    $sp[$n]['accessories'] = html_entity_decode($g['accessories']);
                    $sp[$n]['price'] = rp($g['price']);

                }
            }

        } else {
            $sp = false;
        }
        return $sp;
    }

    // function fetch_car_

    /**
     * Fetching all workshop
     * @return array all workshop on success or false on failure
     */
    function fetch_all_workshop($code = 'ak', $is_published = '-1')
    {
        // if($model) $this->db->where_in('id_type',array($model));
        $this->db->order_by('sort', 'asc');
        if ($is_published != -1) {
            $this->db->where('is_published', $is_published);
        }
        $location = $this->db->where('company_code', $code)->get('ref_location');
        // echo last_query();
        if ($location->num_rows() > 0) {
            $location = $location->result_array();
        } else {
            $location = false;
        }
        return $location;
    }

    function consult_thread($mail)
    {
        // $comp = $this->db->select('a.*,b.location')->from('tech_consult a')
        // ->join('ref_location b','a.ref_location = b.id_ref_location')->get();

        // $comp = $this->db->where('email',$mail)->get('view_tech_consult');

        $comp = $this->db->select('a.*,b.location,c.username,c.avatar,c.brands, c.types,c.series,d.id_chat as parent')->from('tech_consult a')
            ->join('ref_location b', 'a.ref_location = b.id_ref_location', 'inner')
            ->join('view_user_cars c', 'a.ref_user_car = c.id', 'inner')
            ->join('chat d', 'a.id_tech_consult = d.consult_id', 'inner')
            ->where("c.email = '$mail' AND d.parent_id = '0'")
            ->order_by('a.create_date', 'desc')
            ->get();

        if ($comp->num_rows() > 0) {
            $comp = $comp->result_array();
            foreach ($comp as $k => $v) {
                // fetch img car here if needed
            }
            return $comp;
        }
        return false;
    }

    function complain_thread($mail)
    {
        $comp = $this->db->select('a.*,b.location,c.username,c.avatar,c.brands, c.types,c.series,d.id_chat as parent')->from('tech_complain a')
            ->join('ref_location b', 'a.ref_location = b.id_ref_location', 'inner')
            ->join('view_user_cars c', 'a.ref_user_car = c.id', 'inner')
            ->join('chat d', 'a.id_tech_complain = d.complain_id', 'inner')
            ->where("c.email = '$mail' AND d.parent_id = '0'")
            ->order_by('a.create_date', 'desc')
            ->get();
        if ($comp->num_rows() > 0) {
            $comp = $comp->result_array();
            foreach ($comp as $k => $v) {
                $comp[$k]['avatar'] = ($v['avatar'] != '' && file_exists('./uploads/ava/' . $v['avatar'])) ? base_url() . 'uploads/ava/' . $v['avatar'] : '';
                $comp[$k]['car'] = $v['brands'] . ' ' . $v['types'] . ' ' . $v['series'];
            }
            return $comp;
        }
        return false;
    }

    function consult_detail($thread)
    {
        $result = false;
        $cons = $this->db->select('a.*,b.about,c.location,')->from('chat a')
            ->join('tech_consult b', 'b.id_tech_consult = a.consult_id', 'inner')
            ->join('ref_location c', 'c.id_ref_location = b.ref_location', 'inner')
            ->where("a.complain_id is null AND b.id_tech_consult = '$thread' ")
            ->order_by('a.create_date', 'asc')->get();

        if ($cons->num_rows() > 0) {
            $cons = $cons->result_array();
            foreach ($cons as $k => $v) {
                $type = $cons[$k]['type'];
                if ($type == 'msg') {
                    $table = 'user';
                    $field = 'id_user';
                    $path = 'ava';
                } else {
                    $table = 'auth_user';
                    $field = 'id_auth_user';
                    $path = 'adm_ava';
                }
                $cons[$k]['from_uname'] = db_get_one($table, 'username', "$field = '" . $v['from_id'] . "'");
                $ava = db_get_one($table, 'avatar', "$field = '" . $v['from_id'] . "'");

                $cons[$k]['from_ava'] = ($ava && file_exists("./uploads/$path/$ava")) ? base_url() . "uploads/$path/$ava" : '';

            }
            $result = $cons;
        }
        return $result;
    }

    function complain_detail($thread)
    {
        $result = false;
        $cons = $this->db->select('a.*,b.about,c.location,')->from('chat a')
            ->join('tech_complain b', 'b.id_tech_complain = a.complain_id', 'inner')
            ->join('ref_location c', 'c.id_ref_location = b.ref_location', 'inner')
            ->where("a.consult_id is null AND b.id_tech_complain = '$thread' ")
            ->order_by('a.create_date', 'asc')->get();

        if ($cons->num_rows() > 0) {
            $cons = $cons->result_array();
            foreach ($cons as $k => $v) {
                $type = $cons[$k]['type'];
                if ($type == 'msg') {
                    $table = 'user';
                    $field = 'id_user';
                    $path = 'ava';
                } else {
                    $table = 'auth_user';
                    $field = 'id_auth_user';
                    $path = 'adm_ava';
                }
                $cons[$k]['from_uname'] = db_get_one($table, 'username', "$field = '" . $v['from_id'] . "'");
                $ava = db_get_one($table, 'avatar', "$field = '" . $v['from_id'] . "'");

                $cons[$k]['from_ava'] = ($ava && file_exists("./uploads/$path/$ava")) ? base_url() . "uploads/$path/$ava" : '';

            }
            $result = $cons;
        }
        return $result;
    }

    function fetching_point($u)
    {
        $this->load->model('admpage/reserve_model', 'reserve');
        return $this->reserve->fetching_point($u);

    }

    function get_test_drive_list($uid)
    {
        $this->db->where('ref_user', $uid);
        $this->db->order_by('create_date', 'desc');
        $query = $this->db->get('jdi_view_test_drive_booking');
        return $query->result_array();
    }

    function get_test_drive_detail($id)
    {
        $this->db->where('id_test_drive_booking', $id);
        $query = $this->db->get('jdi_view_test_drive_booking');
        return $query->result_array();
    }

    function get_service_list($uid)
    {
        $this->db->where('id_user', $uid);
        $this->db->order_by('id_service_booking', 'desc');
        $query = $this->db->get('view_service_booking');
        return $query->result_array();
    }

    function get_service_detail($id)
    {
        $this->db->where('id_service_booking', $id);
        $query = $this->db->get('view_service_booking');
        return $query->result_array();
    }


}
    
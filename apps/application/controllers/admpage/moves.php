<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Ref Location Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Ref Location Management
 *********************************************/
class Moves extends CI_Controller
{

    private $error = array();
    private $folder;
    private $ctrl;
    private $template;
    private $path;
    private $path_uri;
    private $title;
    private $id_menu_admin;
    private $clientID;
    private $clientSec;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('moves_libs');
        $this->clientID = MVS_ID;
        $this->clientSec = MVS_SEC;
        $this->folder = getAdminFolder();
        $this->ctrl = 'moves';
        $this->template = getAdminFolder() . '/modules/moves';
        $this->path_uri = getAdminFolder() . '/moves';
        $this->path = site_url(getAdminFolder() . '/moves');
        $this->title = get_admin_menu_title('moves');
        $this->id_menu_admin = get_admin_menu_id('moves');
    }

    /**
     * index page = main page
     */
    public function index()
    {
        $this->main();
    }

    public function main()
    {

    }

    public function init()
    {
        $sid = $this->input->get('salesid');
        $sales = db_get_one('user_sales', 'id_user_sales', "id_user_sales = '$sid'");
        if ($sid && $sales) {
            $this->load->library('session');
            $this->session->set_userdata('salesId', $sid);
            header("Location: https://api.moves-app.com/oauth/v1/authorize?response_type=code&client_id=$this->clientID&scope=activity%20location");
        } else {
            echo 'Try with the right method';
        }
    }

    public function callback()
    {
        $gets = $this->input->get();
        if ($gets) {
            if (isset($gets['code'])) {
                $resp = $this->moves_libs->authorize($gets['code']);
                logs($resp);
                if ($resp) {
                    if (isset($resp['error'])) {
                        $resp = get_moves_token_message($resp['error']);

                    } else {
                        $this->db->where('id_user_sales', $this->session->userdata('salesId'))->update('user_sales',
                            $resp);
                        $resp = 'Ok';
                    }
                }
            }
            if (isset($get['error'])) {
                $resp = get_moves_auth_message($get['error']);
            }
            logs($resp);

        }
        header("Location: LufthansaSalesForce://app/authorize?client_id=$this->clientID&scope=location activity");
    }

    public function dailyActivities($token)
    {
        debugvar(json_decode($this->moves_libs->dailyActivities(date('Y-m-18'), $token), true));
    }

    public function dailyPlaces($token)
    {
        debugvar(json_decode($this->moves_libs->dailyPlaces(date('Y-m-18'), $token), true));
    }

    public function exchange($code)
    {
        debugvar($this->moves_libs->authorize($code));
    }

    public function validate($token)
    {
        $val = $this->moves_libs->validating_token($token);
        debugvar($val);
    }

    public function test()
    {
        $sales = $this->db->get_where('user_sales_activity',
            array('type' => 'place', 'stamp' => strtotime(date('Y-m-17')), 'id_user_sales' => '1'))->row_array();

        debugvar(json_decode(unserialize($sales['raw']), true));

    }

    public function extend($token)
    {
        debugvar($this->moves_libs->extending_token($token));
    }

    public function update_moves_resources()
    {
        $usales = $this->db->get_where('user_sales', array('access_token !=' => ''));
        if ($usales->num_rows() > 0) {
            foreach ($usales->result_array() as $k => $v) {
                $now = date('Y-m-d');
                $where = array('id_user_sales' => $v['id_user_sales'], 'stamp' => strtotime($now));
                $activity = $this->moves_libs->dailyActivities($now, $v['access_token']);
                $place = $this->moves_libs->dailyPlaces($now, $v['access_token']);

                if (db_get_one('user_sales_activity', 'id_user_sales', $where)) {
                    if ($activity && $place) {
                        $where['type'] = 'activity';
                        $this->db->where($where)->update('user_sales_activity', array('raw' => serialize($activity)));
                        $where['type'] = 'place';
                        $this->db->where($where)->update('user_sales_activity', array('raw' => serialize($place)));

                    }
                } else {
                    if ($activity && $place) {
                        $where['raw'] = serialize($activity);
                        $where['type'] = 'activity';
                        $this->db->insert('user_sales_activity', $where);
                        $where['raw'] = serialize($place);
                        $where['type'] = 'place';
                        $this->db->insert('user_sales_activity', $where);
                    }
                }
            }
        }
    }
}


/* End of file cars.php */
/* Location: ./application/controllers/admpage/cars.php */
<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Reset My Pass Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Reset My Pass Management
 *********************************************/
class Reset_my_pass extends CI_Controller
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * index page
     */
    public function index()
    {
        $get = $this->input->get();
        // echo md5('userdev@jdi.co.ids'.strtotime('2014-01-09 23:52:26')) . '<br>';
        if (isset($get['key']) && isset($get['umail'])) {
            $usr = $this->db->get_where('user', array('email' => $get['umail']));
            if ($usr->num_rows() > 0) {
                $usr = $usr->row();
                if (md5($usr->email . strtotime($usr->create_date)) == $get['key']) {
                    $this->load->library('parser');
                    $data = array(
                        'email' => $usr->email,
                        'file_app' => site_url('reset_my_pass'),
                        'base_url' => base_url()
                    );

                    $this->parser->parse('reset_pass_mobile.html', $data);
                }
            }
        } else {
            echo 'Error occoured while processing';
        }
    }

    public function act()
    {
        $post = $this->input->post();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['pass']) && isset($post['passconf']) && $post['pass'] != '' && $post['passconf'] != '') {
            if (db_get_one('user', 'email', "email = '" . $post['email'] . "'")) {
                if ($post['pass'] == $post['passconf']) {
                    $this->load->library('encrypt');
                    $buff = array('password' => $this->encrypt->encode($post['pass']));
                    $this->db->where('email', $post['email'])->update('user', $buff);
                    echo 'Congratulation your password has been reset!', exit;
                } else {
                    echo 'error|Password not match with passconf', exit;
                }

            } else {
                echo 'error|Email not in our database', exit;
            }
        } else {
            echo 'error|Please try with the right method!', exit;
        }
    }
}
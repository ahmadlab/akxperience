<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * login Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Login page
 *********************************************/
class Forgot_password extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'forgot_password';
        $this->template = getAdminFolder() . '/layout';
        $this->path = site_url(getAdminFolder() . '/forgot_password');
        $this->path_uri = getAdminFolder() . '/forgot_password';
        $this->title = 'Forgot Password';

        // $this->load->library('encrypt');
        // echo $this->encrypt->encode('123');

    }

    public function index()
    {
        admin_is_loged();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->load->model(getAdminFolder() . '/auth_model', 'Auth_model');
            if ($this->Auth_model->forgot_password($this->input->post('email'))) {
                $form_message = alert_box('Please check your email', 'success');
            } else {
                $form_message = alert_box('Email not found', 'alert');
            }
        }

        $data = array(
            'base_url' => base_url(),
            'login' => '',
            'password' => '',
            'form_message' => $form_message,
            'footer_text' => sprintf(get_setting('app_footer'), date('Y')),
            'head_title' => $this->title . ' - ' . get_setting('app_title')
        );

        $this->parser->parse($this->template . '/forgot_password.html', $data);
    }

    public function reset()
    {
        $get = $this->input->get();

        if (isset($get['key']) && isset($get['umail'])) {
            $usr = $this->db->get_where('auth_user', array('email' => $get['umail']));
            if ($usr->num_rows() > 0) {
                $usr = $usr->row();
                if (md5($usr->email . strtotime($usr->create_date)) == $get['key']) {
                    $this->load->library('parser');
                    $data = array(
                        'email' => $usr->email,
                        'form_message' => '',
                        'file_app' => site_url('reset_my_pass'),
                        'base_url' => base_url(),
                        'action_url' => base_url() . 'index.php/admpage/forgot_password/reset_post',
                    );

                    $this->parser->parse('admpage/layout/reset_pass.html', $data);
                }
            }
        } else {
            echo 'Error occoured while processing';
        }
    }

    public function reset_post()
    {
        $post = $this->input->post();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['pass']) && isset($post['passconf']) && $post['pass'] != '' && $post['passconf'] != '') {
            if (db_get_one('auth_user', 'email', "email = '" . $post['email'] . "'")) {
                if ($post['pass'] == $post['passconf']) {
                    $this->load->library('encrypt');
                    $buff = array('userpass' => $this->encrypt->encode($post['pass']));
                    $this->db->where('email', $post['email'])->update('auth_user', $buff);

                    $form_message = alert_box('Congratulation your password has been reset!', 'success');
                } else {
                    $form_message = alert_box('Password not match with passconf', 'alert');
                }

            } else {
                $form_message = alert_box('Email not in our database', 'alert');
            }
        } else {
            $form_message = alert_box('Please try with the right method!', 'alert');
        }

        $email = $post['email'];

        $data = array(
            'form_message' => $form_message,
            'base_url' => base_url(),
            'action_url' => base_url() . 'index.php/admpage/forgot_password/reset_post',
            'email' => $email,
        );

        $this->parser->parse('admpage/layout/reset_pass.html', $data);
    }

}


/* End of file login.php */
/* Location: ./application/controllers/webcontrol/login.php */
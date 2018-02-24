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
class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->folder = getAdminFolder();
        $this->ctrl = 'login';
        $this->template = getAdminFolder() . '/layout';
        $this->path = site_url(getAdminFolder() . '/login');
        $this->path_uri = getAdminFolder() . '/login';
        $this->title = 'Login';

        // $this->load->library('encrypt');
        // echo $this->encrypt->encode('123');

    }

    public function index()
    {
        admin_is_loged();

        $error_login = ($this->session->flashdata('error_login') != '') ? $this->session->flashdata('error_login') : '';
        $data = array(
            'base_url' => base_url(),
            'login' => '',
            'password' => '',
            'error_login' => $error_login,
            'footer_text' => sprintf(get_setting('app_footer'), date('Y')),
            'head_title' => $this->title . ' - ' . get_setting('app_title')
        );
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           // echo 'post';
	    $this->load->model(getAdminFolder() . '/auth_model', 'Auth_model');
            $this->Auth_model->check_login($this->input->post('username'), $this->input->post('password'));
        }

        $this->parser->parse($this->template . '/login.html', $data);
    }

    public function log_in()
    {
        $this->index();
    }

    /**
     * logout page
     */
    public function logout()
    {
        $this->session->sess_destroy();
        session_destroy();
        redirect($this->path_uri);
    }
}


/* End of file login.php */
/* Location: ./application/controllers/webcontrol/login.php */

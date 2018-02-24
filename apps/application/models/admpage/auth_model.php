<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Auth_model Class
 * @author : Latada
 * @Email    : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : authentication model
 *************************************/
class Auth_model extends CI_Model
{
    // error login admin message
    private $err_login_adm;

    // error login member message
    private $err_login_mem;

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->err_login_adm = alert_box('Username or Password is incorrect', 'alert');
        $this->err_login_mem = alert_box('Username or Password is incorrect', 'alert');
    }

    /**
     * check login admin
     * @param type $username
     * @param type $password
     */
    function check_login($username, $password)
    {
        if ($username != '' && $password != ''):
            //$this->db->where("username",$username);
            //$query=$this->db->get("auth_user");
            $username = strtolower($username);
            $this->db->where('username', $username);
            $query = $this->db->get('auth_user');
//            $query=$this->db->query("SELECT * FROM ".$this->db->dbprefix('auth_user')." WHERE LCASE(username) = ?", array($username));
            // $this->load->library('encrypt');
            // echo $this->encrypt->encode('123');exit;
            // echo $this->encrypt->decode('xHeQwfeFWktWvE4O7C22M5bFu+MYmKgU39Crh/93o5FIitHWG3pRMgS+LpF2gl+B0CxKIKHUmf5RBeeCkrhgcQ==');exit;
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $this->load->library('encrypt');
                //$userpass = $this->encrypt->decode($row->userpass);
                $userpass   = $row->userpass;
		//var_dump($query);
		//die();
		if (($password == $userpass) && ($password != "")) {
                    //$this->load->library("session");

                    $user_sess = array();
                    $user_sess = array(
                        'admin_name' => $row->name,
                        'admin_id_auth_user_group' => $row->id_auth_user_group,
                        'admin_id_auth_user' => $row->id_auth_user,
                        'admin_id_site' => $row->id_site,
                        'admin_email' => $row->email,
                        'admin_type' => 'admin',
                        'admin_url' => base_url(),
                        'admin_ip' => $_SERVER['REMOTE_ADDR'],
                        'admin_last_login' => $row->last_login,
                    );
                    $this->session->set_userdata('ADM_SESS', $user_sess);
                    $_SESSION['ADM_SESS'] = $this->session->userdata('ADM_SESS');

                    # insert to log
                    $data = array(
                        'id_user' => $row->id_auth_user,
                        'id_group' => $row->id_auth_user_group,
                        'action' => 'Login',
                        'desc' => 'Login:succeed; IP:' . $_SERVER['SERVER_ADDR'] . '; username:' . $username . ';',
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);
                    if ($this->session->userdata('tmp_login_redirect') != '') {
                        redirect($this->session->userdata('tmp_login_redirect'));
                    } else {
                        redirect(getAdminFolder() . '/home');
                    }
                } else {
                    # insert to log
                    $data = array(
                        'id_user' => 0,
                        'id_group' => 0,
                        'action' => 'Login',
                        'desc' => 'Login:failed; IP:' . $_SERVER['SERVER_ADDR'] . '; username:' . $username . ';',
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);
                    $this->session->set_flashdata('error_login', $this->err_login_adm);
                    redirect(getAdminFolder() . '/login');
                }
            } /*else if((strtolower($this->input->post("username")) == "administrator") && (strtolower($this->input->post("password")) == "admin"))
            {
                    $this->session->set_userdata('admin','Ivan');
                    $this->session->set_userdata('id_auth_user_group','1');
                    $this->session->set_userdata('id_auth_user','99999');
                    redirect('webcontrol/home');
            }*/
            else {
                #insert to log
                $data = array(
                    'id_user' => 0,
                    'id_group' => 0,
                    'action' => 'Login',
                    'desc' => 'Login:failed; IP:' . $_SERVER['SERVER_ADDR'] . '; username:' . $username . ';',
                    'create_date' => date('Y-m-d H:i:s'),
                );
                insert_to_log($data);

                $this->session->set_flashdata('error_login', $this->err_login_adm);
                redirect(getAdminFolder() . '/login');
            }
        else:
            $this->session->set_flashdata('error_login', $this->err_login_adm);
            redirect(getAdminFolder() . '/login');
        endif;

    }

    public function forgot_password($email)
    {
        $usr = $this->db->get_where('auth_user', array('email' => $email));
        if ($usr->num_rows() > 0) {
            $usr = $usr->row();
            $slug = ($usr->username == '' || $usr->username == '-') ? $usr->email : $usr->username;
            $this->load->helper('mail');

            $key = md5($usr->email . strtotime($usr->create_date));
            $lnk = site_url("admpage/forgot_password/reset/?umail=$usr->email&key=$key");
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

            return true;
        } else {
            return false;
        }
    }

}

/* End of file auth_model.php */
/* Location: ./application/model/webcontrol/auth_model.php */

<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Admin_menu Model Class
 * @Author  : Latada
 * @Email   : mac_ [at] gxrg [dot] org
 * @Type     : Controller
 * @Desc     : home page after login succed (welcome page admin)
 ***********************************/
class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        auth_admin();
        $this->global_libs->print_header();
        $data = array();
        $user_sess = $this->session->userdata('ADM_SESS');
        $group = $user_sess['admin_id_auth_user_group'];
        $data['app_title'] = get_setting('app_title');
        $data['uname'] = ($user_sess['admin_name']) ? $user_sess['admin_name'] . ' !' : '';

        $data['ltdrive'] = array();
        $data['lhservice'] = array();
        $data['lwservice'] = array();
        $data['ltcomplain'] = array();
        $data['ltconsult'] = array();
        $data['tdrive'] = 'fade';
        $data['hserve'] = 'fade';
        $data['wserve'] = 'fade';
        $data['tcomp'] = 'fade';
        $data['tcons'] = 'fade';
        $data['hide_td'] = 'hide';
        $data['hide_hs'] = 'hide';
        $data['hide_ws'] = 'hide';
        $data['hide_ch'] = 'hide';
        $data['hide_tc'] = 'hide';

        if ($group == 3 || $group == 4) { // dcrc
            $data['ltdrive'] = get_dasboard_tdrive();
            $data['lhservice'] = get_dasboard_hservice();
            $data['lwservice'] = get_dasboard_wservice();
            $data['ctdrive'] = count($data['ltdrive']);
            $data['chservice'] = count($data['lhservice']);
            $data['cwservice'] = count($data['lwservice']);
            $data['tdrive'] = '';
            $data['hserve'] = '';
            $data['wserve'] = '';
            $data['hide_td'] = '';
            $data['hide_hs'] = '';
            $data['hide_ws'] = '';
            $data['hide_ch'] = '';
        }

        if ($group == 1 || $group == 2 || $group == 9) { // Admins and developers
            $data['ltdrive'] = get_dasboard_tdrive();
            $data['lhservice'] = get_dasboard_hservice();
            $data['lwservice'] = get_dasboard_wservice();
            $data['ltcomplain'] = get_dasboard_tcomplain();
            $data['ltconsult'] = get_dasboard_tconsult();
            $data['ctdrive'] = count($data['ltdrive']);
            $data['chservice'] = count($data['lhservice']);
            $data['cwservice'] = count($data['lwservice']);
            $data['ctcomplain'] = count($data['ltcomplain']);
            $data['ctconsult'] = count($data['ltconsult']);
            $data['hide_td'] = '';
            $data['hide_hs'] = '';
            $data['hide_ws'] = '';
            $data['hide_ch'] = '';
            $data['hide_tc'] = '';
        }

        if ($group == 8) { // technical
            $data['ltcomplain'] = get_dasboard_tcomplain();
            $data['ltconsult'] = get_dasboard_tconsult();
            $data['ctcomplain'] = count($data['ltcomplain']);
            $data['ctconsult'] = count($data['ltconsult']);
            $data['tcomp'] = '';
            $data['tcons'] = '';
            $data['hide_tc'] = '';
        }


        $data['ltcomplain'] = get_dasboard_tcomplain();
        $data['ltconsult'] = get_dasboard_tconsult();
        $data['ctcomplain'] = count($data['ltcomplain']);
        $data['ctconsult'] = count($data['ltconsult']);
        $this->parser->parse(getAdminFolder() . '/layout/home.html', $data);
        $this->global_libs->print_footer();
    }
}

/* End of file home.php */
/* Location: ./application/controllers/webcontrol/home.php */




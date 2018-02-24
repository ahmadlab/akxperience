<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
error_reporting(E_ALL);

class Executive_summary extends CI_Controller
{
    private $ctrl;
    private $template;
    private $title;

    public function __construct()
    {
        parent::__construct();
        $this->template = getAdminFolder() . '/modules/executive_summary';
        $this->ctrl = 'executive_summary';
        $this->title = get_admin_menu_title('executive_summary');
        $this->path = site_url(getAdminFolder() . '/executive_summary');
        $this->load->model(getAdminFolder() . '/executive_summary_model');
    }

    public function index()
    {
        auth_admin();
        $this->global_libs->print_header();
        $breadcrumbs = $this->global_libs->getBreadcrumbs($this->ctrl);

        $breadcrumbs[] = array(
            'text' => $this->title,
            'href' => '#',
            'class' => 'class="current"'
        );

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $s_year = purify($this->input->post('s_year'));
        } else {
            $s_year = date('Y');
        }

        $l_years = $this->list_year($s_year);

        // MEMBER
        $totalUser = $this->executive_summary_model->totalUser($s_year);
        for ($i = 1; $i <= 12; $i++) {
            $totalUserMonth['totalUserMonth_' . $i] = $this->executive_summary_model->totalUserMonth($s_year, $i);
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalVIPMonth['totalVIPMonth_' . $i] = $this->executive_summary_model->totalUserMonth($s_year, $i, 'vip');
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalRegulerMonth['totalRegulerMonth_' . $i] = $this->executive_summary_model->totalUserMonth($s_year, $i,
                'reguler');
        }

        $totalMaleUser = $this->executive_summary_model->totalUser($s_year, 'male');
        $totalFemaleUser = $this->executive_summary_model->totalUser($s_year, 'female');
        $totalUndefinedUser = $this->executive_summary_model->totalUser($s_year, '');
        $userAge17to20 = $this->executive_summary_model->totalUserInAge($s_year, 17, 20);
        $userAge21to30 = $this->executive_summary_model->totalUserInAge($s_year, 21, 30);
        $userAge31to40 = $this->executive_summary_model->totalUserInAge($s_year, 31, 40);
        $userAge41to50 = $this->executive_summary_model->totalUserInAge($s_year, 41, 50);
        $userAge50 = $this->executive_summary_model->totalUserInAge($s_year, 50, 9999);
        $userSummary = $this->executive_summary_model->userSummary($s_year);
        $userTopFive = $this->executive_summary_model->userTopFive();

        foreach ($userTopFive as $k => $v) {
            $i = $k + 1;
            $userTopCity['topCity_' . $i] = $v['name'];
            $userTopCity['topTotal_' . $i] = $v['total'];
        }

//        die(var_dump($userTopCity));

        // TEST DRIVE
        $totalTestDrive = $this->executive_summary_model->totalTestDrive($s_year);
        for ($i = 1; $i <= 12; $i++) {
            $totalTestDriveMonth['totalTestDriveMonth_' . $i] = $this->executive_summary_model->totalTestDriveMonth($s_year,
                $i);
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalTestDriveFinishedMonth['totalTestDriveFinishedMonth_' . $i] = $this->executive_summary_model->totalTestDriveMonth($s_year,
                $i, 'finished');
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalTestDriveCanceledMonth['totalTestDriveCanceledMonth_' . $i] = $this->executive_summary_model->totalTestDriveMonth($s_year,
                $i, 'canceled');
        }

        // WORKSHOP SERVICE
        $totalWorkshopService = $this->executive_summary_model->totalService('workshop', $s_year);
        for ($i = 1; $i <= 12; $i++) {
            $totalWorkshopServiceMonth['totalWorkshopServiceMonth_' . $i] = $this->executive_summary_model->totalServiceMonth('workshop',
                $s_year, $i);
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalWorkshopServiceFinishedMonth['totalWorkshopServiceFinishedMonth_' . $i] = $this->executive_summary_model->totalServiceMonth('workshop',
                $s_year, $i, 'finished');
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalWorkshopServiceCanceledMonth['totalWorkshopServiceCanceledMonth_' . $i] = $this->executive_summary_model->totalServiceMonth('workshop',
                $s_year, $i, 'canceled');
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalWorkshopServicePriceMonth['totalWorkshopServicePriceMonth_' . $i] = $this->executive_summary_model->totalServicePriceMonth('workshop',
                $s_year, $i);
        }

        // HOME SERVICE
        $totalHomeService = $this->executive_summary_model->totalService('home', $s_year);
        for ($i = 1; $i <= 12; $i++) {
            $totalHomeServiceMonth['totalHomeServiceMonth_' . $i] = $this->executive_summary_model->totalServiceMonth('home',
                $s_year, $i);
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalHomeServiceFinishedMonth['totalHomeServiceFinishedMonth_' . $i] = $this->executive_summary_model->totalServiceMonth('home',
                $s_year, $i, 'finished');
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalHomeServiceCanceledMonth['totalHomeServiceCanceledMonth_' . $i] = $this->executive_summary_model->totalServiceMonth('home',
                $s_year, $i, 'canceled');
        }
        for ($i = 1; $i <= 12; $i++) {
            $totalHomeServicePriceMonth['totalHomeServicePriceMonth_' . $i] = $this->executive_summary_model->totalServicePriceMonth('home',
                $s_year, $i);
        }

        $data = array(
            'base_url' => base_url(),
            'current_url' => current_url(),
            'menu_title' => $this->title,
            'year' => $s_year,
            'l_years' => $l_years,
            'breadcrumbs' => $breadcrumbs,
            'totalUser' => $totalUser,
            'totalUserMonth' => $totalUserMonth,
            'totalMaleUser' => $totalMaleUser,
            'totalFemaleUser' => $totalFemaleUser,
            'totalUndefinedUser' => $totalUndefinedUser,
            'userAge17to20' => $userAge17to20,
            'userAge21to30' => $userAge21to30,
            'userAge31to40' => $userAge31to40,
            'userAge41to50' => $userAge41to50,
            'userAge50' => $userAge50,
            'totalTestDrive' => $totalTestDrive,
            'totalWorkshopService' => $totalWorkshopService,
            'totalHomeService' => $totalHomeService,
            'path_app' => $this->path,
        );

        $data = array_merge(
            $data,
            $totalUserMonth,
            $totalVIPMonth,
            $userTopCity,
            $totalRegulerMonth,
            $totalTestDriveMonth,
            $totalTestDriveFinishedMonth,
            $totalTestDriveCanceledMonth,
            $totalWorkshopServiceMonth,
            $totalWorkshopServiceFinishedMonth,
            $totalWorkshopServiceCanceledMonth,
            $totalHomeServiceMonth,
            $totalHomeServiceFinishedMonth,
            $totalHomeServiceCanceledMonth,
            $totalWorkshopServicePriceMonth,
            $totalHomeServicePriceMonth
        );

        $this->parser->parse($this->template . '/executive_summary.html', $data);
        $this->global_libs->print_footer();
    }

    public function list_year($selected = false)
    {
        for ($i = 2014; $i <= date('Y'); $i++) {
            $array_year[] = $i;
        }
        $opt = "<option value=''>--- Select Year ---</option>";
        foreach ($array_year as $l) {
            $terpilih = ($selected == $l) ? 'selected' : '';
            $opt .= "<option $terpilih value='$l'>$l</option>";
        }
        return $opt;
    }

}

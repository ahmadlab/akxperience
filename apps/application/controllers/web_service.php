<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/******************************************/

/**
 * Global Web Service Management Class
 * @author    : Latada
 * @email    : mac_ [at] gxrg [dot] org
 * @type    : Controller
 * @desc    : Web Service Management
 *********************************************/
class Web_service extends CI_Controller
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    }

    public function news()
    {
        $html = false;
        $type = 'class';
        $val = 'article';
        $path = 'http://www.ford.co.id/about/newsroom';
        $target = array('http://social.ford.com/syndication/?c=&np=&t=1');
        $rss = false;//get_rss($target);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        );

        $resources = cURL($path, $options);

        $doc = new domDocument;

        @$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $resources);

        $finder = new DomXPath($doc);

        $nodes = $finder->query("//*[contains(@$type, '$val')]");

        if ($rss) {
            foreach ($rss as $k => $v) {
                $c = get_content($v['link'], 'class', 'article_copy');
                if ($c) {
                    $content = $c;
                } else {
                    $content = $v['content'];
                }

                $data = array(
                    'title' => $v['title'],
                    'content' => $content,
                    'thumb' => $v['thumb'],
                    'published_date' => $v['publish_date'],
                    'link' => $v['link'],
                    'node' => $v['node']
                );

                $dup = $this->db->where("LCASE(title) LIKE '%" . utf8_strtolower($v['title']) . "%'")->get('news_tmp');
                if ($dup->num_rows() <= '0') {
                    $this->db->insert('news_tmp', $data);

                }
            }
        }

        if (!is_null($nodes)) {

            foreach ($nodes as $element) {

                foreach ($finder->query('h3/a', $element) as $child) {

                    $buf['link'] = $child->getAttribute('href');
                    $buf['title'] = trim($child->nodeValue);
                    $val = 'cms';
                    $resources = cURL($buf['link'], $options);
                    $doc = new domDocument;
                    @$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $resources);

                    $finders = new DomXPath($doc);
                    $nodes = $finders->query("//*[contains(@$type, '$val')]");

                    if (!is_null($nodes)) {

                        foreach ($nodes as $element) {

                            if (substr($element->nodeValue, 0, 6) != 'jQuery') {

                                $t = $element->getElementsByTagName('h3')->item(0);
                                $p = $element->getElementsByTagName('p')->item(0);

                                $t->parentNode->removeChild($t);
                                $p->parentNode->removeChild($p);


                                $tmp_doc = new DOMDocument();
                                $tmp_doc->resolveExternals = true;

                                $tmp_doc->appendChild($tmp_doc->importNode($element, true));

                                $buf['content'] = $tmp_doc->saveHTML();

                            }
                        }

                    }
                    $dup = $this->db->where("LCASE(title) LIKE '%" . utf8_strtolower($buf['title']) . "%'")->get('news_tmp');
                    if ($dup->num_rows() <= '0') {
                        $this->db->insert('news_tmp', $buf);

                    }

                }
            }
        }
    }

    public function repair_service()
    {
        // $where = array('status_book !=' => 'finished', 'status_book !=' => 'cancel','datetime_book >' => date('Y-m-d H:i:s',strtotime('-1 hour' . 'NOW')),'datetime_book <' => date('Y-m-d H:i:s',strtotime('+1 hour' . 'NOW')) );
        $where = array(
            'status_book !=' => 'finished',
            'status_book !=' => 'cancel',
            'datetime_book >=' => date('Y-m-d H:i:s', strtotime('+1 hour' . 'NOW')),
            'datetime_book <' => date('Y-m-d H:i:s', strtotime('+4 hour' . 'NOW'))
        );
        $sbooks = $this->db->get_where('view_service_booking', $where);

        if ($sbooks->num_rows() > 0) {
            $template = fetch_template('service_booking');
            $sbooks = $sbooks->result_array();
            foreach ($sbooks as $sbook) {
                $location = $sbook['location'];
                $mail = array($sbook['email']);
                $gcm = array($sbook['gcm_id']);
                $apn = $sbook['apn_id'];
                $time = date('D, d M H:i', strtotime($sbook['datetime_book']));
                $ckey = 'Reminder';
                eval("\$payloads = \"" . $template['message'] . "\";");
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );

                $log = array(
                    'mail' => $sbook['email'],
                    'location' => $sbook['location'],
                    'gcm' => $sbook['gcm_id'],
                    'msg' => $payloads
                );

                cutomlogs('./' . APPPATH . 'logs/repair.log', $log);

                if ($sbook['gcm_id'] != '') {
                    broadcast_gcm($mail, $gcm, $msg, $ckey);
                }
                if ($sbook['apn_id'] != '') {
                    broadcast_apn($apn, $payloads);
                }

            }
        }
    }

    public function tdrive()
    {
        // $where = array('status_book !=' => 'finished', 'status_book !=' => 'cancel','datetime_book >' => date('Y-m-d H:i:s',strtotime('-1 hour' . 'NOW')),'datetime_book <' => date('Y-m-d H:i:s',strtotime('+1 hour' . 'NOW')) );
        $where = array(
            'status_book !=' => 'finished',
            'status_book !=' => 'cancel',
            'datetime_book >=' => date('Y-m-d H:i:s', strtotime('+1 hour' . 'NOW')),
            'datetime_book <' => date('Y-m-d H:i:s', strtotime('+4 hour' . 'NOW'))
        );
        $sbooks = $this->db->get_where('view_test_drive_booking', $where);

        if ($sbooks->num_rows() > 0) {
            $sbooks = $sbooks->result_array();
            $template = fetch_template('test_drive_booking');
            foreach ($sbooks as $sbook) {
                $location = $sbook['location'];
                $mail = array($sbook['email']);
                $gcm = array($sbook['gcm_id']);
                $apn = $sbook['apn_id'];
                $time = date('D, d M H:i', $sbook['datetime_book']);
                $ckey = 'Reminder';
                eval("\$payloads = \"" . $template['message'] . "\";");
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );

                $log = array(
                    'mail' => $sbook['email'],
                    'location' => $sbook['location'],
                    'gcm' => $sbook['gcm_id'],
                    'msg' => $payloads
                );

                cutomlogs('./' . APPPATH . 'logs/tdrive.log', $log);

                if ($sbook['gcm_id'] != '') {
                    broadcast_gcm($mail, $gcm, $msg, $ckey);
                }
                if ($sbook['apn_id'] != '') {
                    broadcast_apn($apn, $payloads);
                }
            }
        }
    }

    public function mileage_info()
    {
        $userc = $this->db->group_by('id_user')->get('user_cars');
        if ($userc->num_rows() > 0) {
            $template = fetch_template('mileage_info');
            $userc = $userc->result_array();
            $amsg = '';
            foreach ($userc as $k => $v) {
                $detail = $this->db->get_where('view_user_cars', array('id_user' => $v['id_user']));
                $payloads = '';
                if ($detail->num_rows() > 0) {
                    foreach ($detail->result_array() as $q => $n) {
                        // $car = $n['brands'] .' '.$n['types'] .' '.$n['series'] .' '.$n['model'] .' '.$n['transmisi'] .' '.$n['car_cc'] .' '.$n['engine'];
                        $gcm = $n['gcm_id'];
                        $apn = $n['apn_id'];
                        $car = $n['police_number'];
                        $usr = $n['email'];

                        // eval("\$payloads .= \"".$template['message']."\n\n\n\";");
                        eval("\$payloads .= \"" . $template['message'] . " <br>\";");
                        $msg = array(
                            'message' => $payloads,
                            'description' => 'testing description'
                        );
                    }
                    if ($gcm != '') {
                        broadcast_gcm($usr, $gcm, $msg, $ckey);
                    }
                    if ($apn != '') {
                        broadcast_apn($apn, $payloads);
                    }

                }

            }

        }
    }

    public function birthday()
    {
        $where = array('birthday' => date('Y-m-d'));
        $dday = $this->db->get_where('user', $where);

        if ($dday->num_rows() > 0) {
            $template = fetch_template('birthday');
            foreach ($dday->result_array() as $day) {
                $mail = $day['email'];
                $gcm = array($day['gcm_id']);
                $apn = $day['apn_id'];
                $ckey = 'Reminder';
                eval("\$payloads = \"" . $template['message'] . "\";");
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );

                if ($gcm != '') {
                    broadcast_gcm($mail, $gcm, $msg, $ckey);
                }
                if ($apn != '') {
                    broadcast_apn($apn, $payloads);
                }
            }
        }
    }

    public function stnk_date()
    {
        $where = array('stnk_date' => date('Y-m-d', strtotime('+1month')));
        $dday = $this->db->get_where('view_user_cars', $where);

        if ($dday->num_rows() > 0) {
            $template = fetch_template('stnk_date');
            foreach ($dday->result_array() as $day) {
                $mail = $day['email'];
                $stnkdate = $day['stnk_date'];
                $gcm = array($day['gcm_id']);
                $apn = $day['apn_id'];
                $ckey = 'Reminder';
                eval("\$payloads = \"" . $template['message'] . "\";");
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );

                if ($gcm != '') {
                    broadcast_gcm($mail, $gcm, $msg, $ckey);
                }
                if ($apn != '') {
                    broadcast_apn($apn, $payloads);
                }
            }
        }
    }

    public function insurance_date()
    {
        $where = array('insurance_date' => date('Y-m-d', strtotime('+1month')));
        $dday = $this->db->get_where('view_user_cars', $where);

        if ($dday->num_rows() > 0) {
            $template = fetch_template('insurance_date');
            foreach ($dday->result_array() as $day) {
                $mail = $day['email'];
                $insurance_date = $day['insurance_date'];
                $gcm = array($day['gcm_id']);
                $apn = $day['apn_id'];
                $ckey = 'Reminder';
                eval("\$payloads = \"" . $template['message'] . "\";");
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );

                if ($gcm != '') {
                    broadcast_gcm($mail, $gcm, $msg, $ckey);
                }
                if ($apn != '') {
                    broadcast_apn($apn, $payloads);
                }
            }
        }
    }

    public function vip_date()
    {
        $dday = $this->db->get_where('user', array('expire_date !=' => ''));
        if ($dday->num_rows() > 0) {
            $template = fetch_template('vip_date_renewal');
            foreach ($dday->result_array() as $day) {
                if ($day['expire_date'] == strtotime(date('Y-m-d') . '+1month')) {

                    $mail = $day['email'];
                    $date = date('D,d M Y', $day['expire_date']);
                    $gcm = array($day['gcm_id']);
                    $apn = $day['apn_id'];
                    $ckey = 'Reminder';
                    eval("\$payloads = \"" . $template['message'] . "\";");
                    $msg = array(
                        'message' => $payloads,
                        'description' => 'testing description'
                    );

                    $slug = ($day['username'] != '' && $day['username'] != '-') ? $day['username'] : $day['email'];
                    $conf['from_name'] = $slug;
                    $conf['subject'] = 'Validity Period of Your AK Experience Membership';
                    $conf['to'] = $day['email'];
                    $conf['content'] = "Dear Mr./Mrs. " . $slug . "<br>";
                    $conf['content'] .= "Thank you for your trust to PT. Kreasi Auto Kencana as your After Sales Service partner.<br>";
                    $conf['content'] .= "We would like to inform that your AK Experience Membership will be ";
                    $conf['content'] .= "expired in <strong>$date</strong>, we suggest you to continuing your membership by ";
                    $conf['content'] .= "visiting our DCRC Staff at any Auto Kencana Workshop or you can directly call our hotline at +622199252525.<br><br>";
                    $conf['content'] .= "Thank you for your attention,<br>";
                    $conf['content'] .= "have a good day.<br><br>";
                    $conf['content'] .= "Regards,<br>";
                    $conf['content'] .= "AK Experience Team<br>";
                    $conf['content'] .= "<small><i>Feel the Experience with AK Experience!</i></small><br>";

                    if ($gcm != '') {
                        broadcast_gcm($mail, $gcm, $msg, $ckey);
                    }
                    if ($apn != '') {
                        broadcast_apn($apn, $payloads);
                    }
                    sent_mail($conf);
                }
            }
        }
    }

    public function purge_technical_consultation()
    {
        $this->db->select('a.consult_id AS id, max(a.create_date) as date');
        $this->db->from('chat a');
        $this->db->where('a.consult_id !=', '');
        $this->db->where('stat', 'open');
        $this->db->where('a.create_date <', date('Y-m-d', strtotime('-1 week')));
        $this->db->join('tech_consult b', 'b.id_tech_consult = a.consult_id');
        $this->db->group_by('a.consult_id');
        $resp = $this->db->get()->result_array();

        if (count($resp) > 0) {
            foreach ($resp as $v) {
                $id_array[] = $v['id'];
            }

            $data = array(
                'stat' => 'closed',
            );

            $this->db->where_in('id_tech_consult', $id_array);
            $this->db->update('tech_consult', $data);
        }

        echo count($resp);
    }

    public function purge_complaint_handling()
    {
        $this->db->select('a.complain_id AS id, max(a.create_date) as date');
        $this->db->from('chat a');
        $this->db->where('a.complain_id !=', '');
        $this->db->where('stat', 'open');
        $this->db->where('a.create_date <', date('Y-m-d', strtotime('-1 week')));
        $this->db->join('tech_complain b', 'b.id_tech_complain = a.complain_id');
        $this->db->group_by('a.complain_id');
        $resp = $this->db->get()->result_array();

        if (count($resp) > 0) {
            foreach ($resp as $v) {
                $id_array[] = $v['id'];
            }

            $data = array(
                'stat' => 'closed',
            );

            $this->db->where_in('id_tech_complain', $id_array);
            $this->db->update('tech_complain', $data);
        }

        echo count($resp);
    }

    public function vip_expired()
    {
        $this->db->select('id_user AS id');
        $this->db->where('user_type', 'vip');
        $this->db->where('expire_date <', time());
        $resp = $this->db->get('user')->result_array();

        if (count($resp) > 0) {
            foreach ($resp as $v) {
                $id_array[] = $v['id'];
            }

            $data = array(
                'user_type' => 'reguler',
            );

            $this->db->where_in('id_user', $id_array);
            $this->db->update('user', $data);
        }

        echo count($resp);
    }

    public function purge_book()
    {
        $data = array(
            'status_book' => 'canceled',
        );

        $this->db->where('datetime_book <', date('Y-m-d'));
        $this->db->where_in('status_book', array('booked', 'confirmed'));
        $this->db->update('car_service_booking', $data);

        $this->db->where('datetime_book <', date('Y-m-d'));
        $this->db->where_in('status_book', array('booked', 'confirmed'));
        $this->db->update('test_drive_booking', $data);
    }
}
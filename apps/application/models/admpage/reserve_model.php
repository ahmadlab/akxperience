<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Reserve Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Reserve model
 ***********************************/
class Reserve_model extends CI_Model
{
    /**
     * count total Test Drive Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @return int count total rows
     */
    function TotalTestDriveReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $location
    ) {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null && $s_search2 != 'all') {
            $this->db->where("status_book", $s_search2);
        }
        if ($s_search3 != null) {
            $this->db->where("datetime_book >=", iso_date($s_search3) . ' 08:00:00');
        }
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }

        if ($s_search3 == null && $s_search4 == null) {
            $where = array(
                'datetime_book >' => date('Y-m-d H:i:s'),
                'datetime_book <=' => date('Y-m-d H:i:s', strtotime('+8days'))
            );
            $this->db->where($where);
        }
        $this->db->where_in("ref_location", $location);
        $query = $this->db->get('view_test_drive_booking');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Service Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @return int count total rows
     */
    function TotalServiceReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $location,
        $s_stype
    ) {
        if ($s_search3 == null) {
            $s_search3 = date("Y-m-d");
        } else {
            $s_search3 = iso_date($s_search3);
        }

        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->where("status_book", $s_search2);
        }
        $this->db->where("datetime_book >=", $s_search3 . ' 08:00:00');
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }
        if ($s_stype != null) {
            $this->db->where("service_type <=", $s_stype);
        }

        $this->db->where_in("id_ref_location", $location);
        $query = $this->db->get('view_service_booking');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Service Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @return int count total rows
     */
    function TotalServiceHomeReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $location
    ) {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->where("status_book", $s_search2);
        }
        if ($s_search3 != null) {
            $this->db->where("datetime_book >=", iso_date($s_search3) . ' 08:00:00');
        }
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }

        // $where = array('datetime_book >' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')));
        // $where = array('datetime_book >=' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')), 'status_book !=' => 'finished', 'status_book !=' => 'canceled' );

        // $where = "status_book != 'finished' AND datetime_book >= '".date('Y-m-d 06:00:00')."' and datetime_book <= '".date('Y-m-d 06:00:00',strtotime('+8days'))."' and status_book != 'canceled'";

        // $this->db->where($where);
        $this->db->where('ref_service', '2')->where_in("id_ref_location", $location);
        $query = $this->db->get('view_service_booking');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Service Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @return int count total rows
     */
    function TotalServiceWorkshopReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $location
    ) {
        $this->db->select('count(*) as total');
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->where("status_book", $s_search2);
        }

        if ($s_search3 != null) {
            $this->db->where("datetime_book >=", iso_date($s_search3) . ' 08:00:00');
        }
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }

        // $where = array('datetime_book >' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')));
        // $where = array('datetime_book >=' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')), 'status_book !=' => 'finished', 'status_book !=' => 'canceled' );


        // $where = "status_book != 'finished' AND datetime_book >= '".date('Y-m-d 06:00:00')."' and datetime_book <= '".date('Y-m-d 06:00:00',strtotime('+8days'))."' and status_book != 'canceled'";

        // $this->db->where($where);
        $this->db->order_by('create_date', 'desc');
        $this->db->where('ref_service', '1')->where_in("id_ref_location", $location);
        $query = $this->db->get('view_service_booking');

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Test Drive Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTestDriveReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $limit = 0,
        $per_pg = 0,
        $location
    ) {
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null && $s_search2 != 'all') {
            $this->db->where("status_book", $s_search2);
        }
        if ($s_search3 != null) {
            $this->db->where("datetime_book >=", iso_date($s_search3) . ' 08:00:00');
        }
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }

        if ($s_search3 == null && $s_search4 == null) {
            $where = array(
                'datetime_book >' => date('Y-m-d H:i:s'),
                'datetime_book <=' => date('Y-m-d H:i:s', strtotime('+8days'))
            );
            $this->db->where($where);
        }
        $this->db->where_in("ref_location", $location);
        $this->db->order_by('create_date', 'desc')->limit($per_pg, $limit);
        $query = $this->db->get('view_test_drive_booking');
        return $query;
    }

    /**
     * retrieve all Service Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllServiceReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $limit = 0,
        $per_pg = 0,
        $location,
        $s_stype
    ) {
        if ($s_search3 == null) {
            $s_search3 = date("Y-m-d");
        } else {
            $s_search3 = iso_date($s_search3);
        }

        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->where("status_book", $s_search2);
        }
        $this->db->where("datetime_book >=", $s_search3 . ' 08:00:00');
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }
        if ($s_stype != null) {
            $this->db->where("service_type <=", $s_stype);
        }

        $this->db->where_in("id_ref_location", $location);
        $this->db->order_by('datetime_book', 'asc')->limit($per_pg, $limit);
        $query = $this->db->get('view_service_booking');
        return $query;
    }

    /**
     * retrieve all Service Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllServiceHomeReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $limit = 0,
        $per_pg = 0,
        $location
    ) {
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->where("status_book", $s_search2);
        }
        if ($s_search3 != null) {
            $this->db->where("datetime_book >=", iso_date($s_search3) . ' 08:00:00');
        }
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }


        // $where = array('datetime_book >' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')));
        // $where = array('datetime_book >=' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')), 'status_book !=' => 'finished', 'status_book !=' => 'canceled' );

        // $where = "status_book != 'finished' AND datetime_book >= '".date('Y-m-d 06:00:00')."' and datetime_book <= '".date('Y-m-d 06:00:00',strtotime('+8days'))."' and status_book != 'canceled'";

        // $this->db->where($where);
        $this->db->order_by('create_date', 'desc');
        $this->db->where('ref_service', '2')->where_in("id_ref_location", $location);
        $this->db->order_by('create_date', 'desc');
        $this->db->limit($per_pg, $limit);

        $query = $this->db->get('view_service_booking');
        return $query;
    }

    /**
     * retrieve all Service Reserve
     * @param mixed $s_search1
     * @param mixed $s_search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllServiceWorkshopReserve(
        $s_search1 = null,
        $s_search2 = null,
        $s_search3 = null,
        $s_search4 = null,
        $limit = 0,
        $per_pg = 0,
        $location
    ) {
        if ($s_search1 != null) {
            $this->db->like("username", $s_search1);
        }
        if ($s_search2 != null) {
            $this->db->where("status_book", $s_search2);
        }

        if ($s_search3 != null) {
            $this->db->where("datetime_book >=", iso_date($s_search3) . ' 08:00:00');
        }
        if ($s_search4 != null) {
            $this->db->where("datetime_book <=", iso_date($s_search4) . ' 20:00:00');
        }

        // $where = array('datetime_book >' => date('Y-m-d H:i:s'), 'datetime_book <=' => date('Y-m-d H:i:s',strtotime('+8days')));

        // $where = "status_book != 'finished' AND datetime_book >= '".date('Y-m-d 06:00:00')."' and datetime_book <= '".date('Y-m-d 06:00:00',strtotime('+8days'))."' and status_book != 'canceled'";

        // $this->db->where($where);
        $this->db->order_by('create_date', 'desc');
        $this->db->where('ref_service', '1')->where_in("id_ref_location", $location);
        $this->db->order_by('create_date', 'desc')->limit($per_pg, $limit);
        $query = $this->db->get('view_service_booking');

        return $query;
    }

    /**
     * get Test Drive Reserve by id
     * @param type $Id
     * @return object $query
     */
    function GetTestDriveReserveById($Id)
    {
        $this->db->where('id_test_drive_booking', $Id);
        $this->db->limit(1);
        $query = $this->db->get('view_test_drive_booking');
        return $query;
    }

    /**
     * get Service Reserve by id
     * @param type $Id
     * @return object $query
     */
    function GetServiceReserveById($Id)
    {
        $this->db->where('id_service_booking', $Id);
        $this->db->limit(1);
        $query = $this->db->get('view_service_booking');
        return $query;
    }

    /**
     * get Service Reserve by id
     * @param type $Id
     * @return object $query
     */
    function GetServiceHomeReserveById($Id)
    {
        $this->db->where('ref_service', '2')->where('id_service_booking', $Id);
        $this->db->limit(1);
        $query = $this->db->get('view_service_booking');
        return $query;
    }

    /**
     * get Service Reserve by id
     * @param type $Id
     * @return object $query
     */
    function GetServiceWorkshopReserveById($Id)
    {
        $this->db->where('ref_service', '1')->where('id_service_booking', $Id);
        $this->db->limit(1);
        $query = $this->db->get('view_service_booking');
        return $query;
    }

    /**
     * Add Service Reserve
     * @param type $Id
     * @return object $query
     */
    function AddServiceReserve($data)
    {
        $this->db->insert('car_service_booking', $data);
        $er = $this->db->_error_message();
        $return = ($er) ? $er : $this->db->insert_id();
        return $return;
    }

    /**
     * Update Service Reserve
     * @param type $Id
     * @return object $query
     */
    function UpdateServiceReserve($data, $Id)
    {
        $stat = db_get_one('car_service_booking', 'status_book', "id_service_booking = '$Id'");
        $service = $this->db->get_where('view_service_booking', array('id_service_booking' => $Id))->row_array();

        if (isset($data['status_book'])) {
            switch ($stat) {
                case 'booked':
                    if ($data['status_book'] == 'confirmed') {
                        $data['confirmed_at'] = date('Y-m-d H:i:s');
                        $data['confirmed_by'] = adm_sess_userid();

                        $ckey = 'Reminder';
                        $payloads = ucfirst($service['service_type']) . ' Service Confirmed for ' . $service['police_number'] . ' on ' . $service['datetime_book'] . ', check menu Service Status';
                        $msg = array(
                            'message' => $payloads,
                            'description' => 'testing description'
                        );
                        if ($service['gcm_id'] != '') {
                            send_gcm_async($service['email'], $service['gcm_id'], $msg, $ckey);
                        }
                        if ($service['apn_id'] != '') {
                            send_apn_async($service['apn_id'], $payloads);
                        }
                    }
                    break;
                case 'confirmed':
                    if ($data['status_book'] == 'service in progress') {
                        $data['progress_at'] = date('Y-m-d H:i:s');
                        $data['progress_by'] = adm_sess_userid();

                        $eta = explode(' ', $data['eta']);
                        if ($eta[1] == 'Hour') {
                            $eta = date('Y-m-d H:i',
                                strtotime('+' . $eta[0] . ' hour', strtotime($service['datetime_book'])));
                        } else {
                            if ($eta[1] == 'Day') {
                                $eta = date('Y-m-d H:i',
                                    strtotime('+' . $eta[0] . ' day', strtotime($service['datetime_book'])));
                            } else {
                                $eta = 'unknown';
                            }
                        }

                        $ckey = 'Reminder';
                        $payloads = ucfirst($service['service_type']) . ' Service is in progress for ' . $service['police_number'] . ' ETA: ' . $eta . ', check menu Service Status';
                        $msg = array(
                            'message' => $payloads,
                            'description' => 'testing description'
                        );
                        if ($service['gcm_id'] != '') {
                            send_gcm_async($service['email'], $service['gcm_id'], $msg, $ckey);
                        }
                        if ($service['apn_id'] != '') {
                            send_apn_async($service['apn_id'], $payloads);
                        }
                    }
                    break;
                case 'service in progress':
                    if ($data['status_book'] == 'service is done') {
                        $data['done_at'] = date('Y-m-d H:i:s');
                        $data['done_by'] = adm_sess_userid();

                        $usr = $this->db->get_where('view_service_booking', array('id_service_booking' => $Id));
                        if ($usr->num_rows() > 0) {

                            $ckey = 'Reminder';
                            $payloads = ucfirst($service['service_type']) . ' Service is done for ' . $service['police_number'] . ', check menu Service Status';
                            $msg = array(
                                'message' => $payloads,
                                'description' => 'testing description'
                            );
                            if ($service['gcm_id'] != '') {
                                send_gcm_async($service['email'], $service['gcm_id'], $msg, $ckey);
                            }
                            if ($service['apn_id'] != '') {
                                send_apn_async($service['apn_id'], $payloads);
                            }


                        }
                    }
                    break;
            }

            if ($data['status_book'] == 'canceled') {
                $data['canceled_at'] = date('Y-m-d H:i:s');
                $data['canceled_by'] = adm_sess_userid();

                $ckey = 'Reminder';
                $payloads = ucfirst($service['service_type']) . ' Service Canceled for ' . $service['police_number'] . ' on ' . $service['datetime_book'] . ', check menu Service Status';
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );
                if ($service['gcm_id'] != '') {
                    send_gcm_async($service['email'], $service['gcm_id'], $msg, $ckey);
                }
                if ($service['apn_id'] != '') {
                    send_apn_async($service['apn_id'], $payloads);
                }
            }

            if ($data['status_book'] == 'finished') {
                $data['finished_at'] = date('Y-m-d H:i:s');
                $data['finished_by'] = adm_sess_userid();

                $ckey = 'Reminder';
                $payloads = 'You received ' . floor($data['total_price'] / 500000) . ' Reward Points from your last transaction, check your dashboard.';
                $msg = array(
                    'message' => $payloads,
                    'description' => 'testing description'
                );
                if ($service['gcm_id'] != '') {
                    send_gcm_async($service['email'], $service['gcm_id'], $msg, $ckey);
                }
                if ($service['apn_id'] != '') {
                    send_apn_async($service['apn_id'], $payloads);
                }
            }
        }
        $this->db->where('id_service_booking', $Id)->update('car_service_booking', $data);
        if (isset($data['status_book']) && $data['status_book'] == 'finished') {
            update_point($Id);
        }
    }

    /**
     * Update Service Reserve
     * @param type $Id
     * @return object $query
     */
    function UpdateTestDriveReserve($data, $Id)
    {
        $this->db->where('id_test_drive_booking', $Id)->update('test_drive_booking', $data);
        echo $this->db->_error_message();
    }

    function fetching_point($usercar)
    {
        $point = 0;
        $hist = $this->db->from('car_service_booking a')
            ->join('user_cars b', 'b.id_user_cars = a.ref_user_cars', 'inner')
            ->where("a.status_book = 'confirmed' and a.ref_user_cars = '$usercar'")->get();
        if ($hist->num_rows() > 0) {
            foreach ($hist->result_array() as $k => $v) {
                $p = converter_point($v['total_price']);
                if ($p) {
                    $buf[] = $p;
                }
            }
            $point = (isset($buf)) ? array_sum($buf) : 0;
        }
        return $point;
    }
}
<?php

class Executive_summary_model extends CI_Model
{

    public function totalUser($year, $gender = null)
    {
        if ($gender != null) {
            $this->db->where('sex', $gender);
        }
        $this->db->where('user_type !=', 'sales');
        $this->db->where('create_date <=', $year . '-12-31 23:59:59');
        $this->db->where('create_date >=', $year . '-01-01 00:00:00');
        $this->db->from('user');
        return $this->db->count_all_results();
    }

    public function totalUserMonth($year, $month, $type = null)
    {
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        if ($type != null) {
            $this->db->where('user_type', $type);
        }
        $this->db->where('user_type !=', 'sales');
        $this->db->where('create_date <=', $year . '-' . $month . '-31 23:59:59');
        $this->db->where('create_date >=', $year . '-' . $month . '-01 00:00:00');
        $this->db->from('user');
        return $this->db->count_all_results();
    }

    public function totalUserInAge($year, $ageMin, $ageMax)
    {
        $ageMin = strtotime(date('Y-m-d') . ' -' . $ageMin . ' year');
        $ageMax = strtotime(date('Y-m-d') . ' -' . $ageMax . ' year');

        $this->db->where('user_type !=', 'sales');
        $this->db->where('birthday <=', date('Y-m-d 23:59:59', $ageMin));
        $this->db->where('birthday >=', date('Y-m-d 00:00:00', $ageMax));
        $this->db->where('create_date <=', $year . '-12-31 23:59:59');
        $this->db->where('create_date >=', $year . '-01-01 00:00:00');
        $this->db->from('user');
        return $this->db->count_all_results();
    }

    public function userSummary($year)
    {
        $this->db->where('user_type !=', 'sales');
        $this->db->where('create_date <=', $year . '-12-31 23:59:59');
        $this->db->where('create_date >=', $year . '-01-01 00:00:00');
        return $this->db->get('user');
    }

    public function userTopFive()
    {
        $this->db->where('user_type !=', 'sales');
        $this->db->select('name, COUNT(*) AS total');
        $this->db->join('ref_city', 'id = city');
        $this->db->group_by('city');
        $this->db->order_by('total', 'DESC');
        return $this->db->get('user', 5)->result_array();
    }

    public function totalTestDrive($year)
    {
        $this->db->where('create_date <=', $year . '-12-31 23:59:59');
        $this->db->where('create_date >=', $year . '-01-01 00:00:00');
        $this->db->from('test_drive_booking');
        return $this->db->count_all_results();
    }

    public function totalTestDriveMonth($year, $month, $status = null)
    {
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        if ($status != null) {
            $this->db->where('status_book', $status);
        }
        $this->db->where('create_date <=', $year . '-' . $month . '-31 23:59:59');
        $this->db->where('create_date >=', $year . '-' . $month . '-01 00:00:00');
        $this->db->from('test_drive_booking');
        return $this->db->count_all_results();
    }

    public function totalService($type, $year)
    {
        $this->db->where('service_type', $type);
        $this->db->where('create_date <=', $year . '-12-31 23:59:59');
        $this->db->where('create_date >=', $year . '-01-01 00:00:00');
        $this->db->from('view_service_booking');
        return $this->db->count_all_results();
    }

    public function totalServiceMonth($type, $year, $month, $status = null)
    {
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        if ($status != null) {
            $this->db->where('status_book', $status);
        }
        $this->db->where('service_type', $type);
        $this->db->where('create_date <=', $year . '-' . $month . '-31 23:59:59');
        $this->db->where('create_date >=', $year . '-' . $month . '-01 00:00:00');
        $this->db->from('view_service_booking');
        return $this->db->count_all_results();
    }

    public function totalServicePriceMonth($type, $year, $month)
    {
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $this->db->select_sum('total_price');
        $this->db->where('status_book', 'finished');
        $this->db->where('service_type', $type);
        $this->db->where('create_date <=', $year . '-' . $month . '-31 23:59:59');
        $this->db->where('create_date >=', $year . '-' . $month . '-01 00:00:00');
        $row = $this->db->get('view_service_booking')->row();
        if (empty($row->total_price)) {
            return 0;
        }
        return number_format($row->total_price, 0, '', '.');
    }

}
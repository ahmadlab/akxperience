<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Push Notification Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : News model
 ***********************************/
class Push_model extends CI_Model
{
    /**
     * count total
     * @param mixed $s_search
     * @return int count total rows
     */
    function TotalPush($s_search = null)
    {
        $this->db->select('count(*) as total');
        // if ($s_search != null) $this->db->where("title",$s_search);

        $query = $this->db->get('push_notification_history');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllPush($s_search1 = null, $limit = 0, $per_pg = 0)
    {
        // if ($s_search1 != null) $this->db->where("title",$s_search1);

        $this->db->order_by('create_date', 'desc')->limit($per_pg, $limit);
        $query = $this->db->get('push_notification_history');
        return $query;
    }

    /**
     * get by id
     * @param type $Id
     * @return object $query
     */
    function GetPushById($Id)
    {
        $this->db->where('id', $Id);
        $this->db->limit(1);
        $query = $this->db->get('push_notification_history');
        return $query;
    }

    /**
     * Insert
     * @param array $data
     * @return last id inserted
     */
    function InsertPush($data)
    {
        $this->db->insert('push_notification_history', $data);
        $id = $this->db->insert_id();

        $this->broadcast($data['payload']);

        return $id;
    }

    /**
     * Update
     * @param array $data
     * @param int $id
     * @return void
     */
    function UpdatePush($data, $Id)
    {
        $this->db->where('id', $Id);
        $this->db->update('push_notification_history', $data);
    }

    /**
     * Update
     * @param array $data
     * @param int $id
     * @return void
     */
    function broadcast($data)
    {
        // SELECT * FROM (`jdi_user`) WHERE `status` = '1' AND user_type != 'sales' AND (gcm_id != '' OR apn_id != '')
        $where = "status = '1' AND user_type != 'sales' AND (gcm_id != '' OR apn_id != '')";
        $usr = $this->db->where($where)->get('user');

        if ($usr->num_rows() > 0) {
            $usr = result_array();
            foreach ($usr as $k => $o) {
                $apn = $o['apn_id'];
                $gcm = $o['gcm_id'];
                $mail = $o['email'];
                $ckey = 'Broadcast event';
                if ($gcm != '') {
                    $msg = array(
                        'message' => $data,
                        'description' => 'testing description'
                    );
                    broadcast_gcm($mail, $gcm, $msg, $ckey, false, $lname = 'broadcast_event.log');

                }
                if ($apn != '') {
                    broadcast_apn($apn, $data);

                }
            }
        }
    }


}
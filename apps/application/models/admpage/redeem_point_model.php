<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Redeem Point Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Redeem Point model
 ***********************************/
class Redeem_point_model extends CI_Model
{

    /**
     * retrieve all Location
     * @param mixed $s_search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllRedeem($s_search1 = '', $s_search2 = '', $s_search3 = '', $s_search4 = '', $limit = 0, $per_pg = 0)
    {
        if ($s_search1 != '') {
            $this->db->where_in("card_id", $s_search1);
        }
        // if ($s_search2 != false) $this->db->where("username",$s_search2);
        if ($s_search2 != '') {
            $this->db->where("(LCASE(username) LIKE '%" . utf8_strtolower($s_search2) . "%')");
        }
        if ($s_search3 != '') {
            $this->db->where("police_number", $s_search3);
        }
        if ($s_search4 != '') {
            $this->db->where("vin_number", $s_search4);
        }

        // $this->db->limit($per_pg,$limit);

        if ($s_search1 == '' && $s_search2 == '' && $s_search3 == '' && $s_search4 == '') {
            $this->db->where('id', '0');
        }
        $query = $this->db->get('view_user_cars');

        return $query;
    }

    /**
     * get Location by id
     * @param type $Id
     * @return object $query
     */
    function GetRedeemById($Id)
    {
        $this->db->where('id', $Id);
        $this->db->limit(1);
        $query = $this->db->get('view_user_cars');
        return $query;
    }

    function insertHistory($data)
    {
        $this->db->insert('redeem_point', $data);
        return $this->db->insert_id();
    }

    function update_point($id, $currentp, $type = 'renewal')
    {
        $ucar = $this->db->get_where('view_user_cars', array('id' => $id));
        if ($ucar->num_rows() > 0) {
            $ucar = $ucar->row_array();
            if ($type == 'renewal') {
                $dexpire = strtotime('+ 1 years');
                if ($ucar['expire_date'] != '') {
                    $dexpire = strtotime(date('Y-m-d', $ucar['expire_date']) . "+ 365 day");
                }
                $this->db->where('id_user', $ucar['id_user'])->update('user', array('expire_date' => $dexpire));
            }
            $this->db->where('id_user_cars', $ucar['id'])->update('user_cars', array('point_reward' => $currentp));
        }
    }

    function GetAllRedeemHist($Id)
    {
        $hist = $this->db->from('redeem_point a')->join('view_user_cars b', 'a.ref_user_car = b.id', 'inner')
            ->where_in('a.ref_user_car', array($Id))->get();
        return $hist;
    }

    function TotalRefRedeem($reward)
    {
        $this->db->select('count(*) as total');
        if ($reward != null) {
            $this->db->like("reward", $reward);
        }

        $query = $this->db->get('ref_redeem_point');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    function GetAllRefRedeem($s_search1 = '', $limit, $per_pg)
    {
        if ($s_search1 != '') {
            $this->db->where("(LCASE(reward) LIKE '%" . utf8_strtolower($s_search1) . "%')");
        }

        $this->db->limit($per_pg, $limit);

        $query = $this->db->get('ref_redeem_point');

        return $query;
    }

    function GetRefRedeemById($Id)
    {
        $this->db->where('id_ref_redeem_point', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_redeem_point');
        return $query;
    }

    function CheckExistsRefRedeem($data, $Id)
    {
        if ($Id > 0) {
            $this->db->where('id_ref_redeem_point != ', $Id);
        }
        $this->db->where('reward', $data);
        $query = $this->db->get('ref_redeem_point');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    function ChangePublishRedeem($id)
    {
        $this->db->where('id_ref_redeem_point', $id);
        $query = $this->db->get('ref_redeem_point');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_ref_redeem_point', $id);
            $this->db->update('ref_redeem_point', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    function DeleteRefRedeem($Id)
    {
        $this->db->where_in('id_ref_redeem_point', $Id);
        $this->db->delete('ref_redeem_point');
    }

    function InsertRefRedeem($data)
    {
        $this->db->insert('ref_redeem_point', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    function UpdateRefRedeem($data, $Id)
    {
        $this->db->where('id_ref_redeem_point', $Id);
        $this->db->update('ref_redeem_point', $data);
    }

}
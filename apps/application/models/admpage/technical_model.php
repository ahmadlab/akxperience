<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Technical Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Technical model
 ***********************************/
class Technical_model extends CI_Model
{
    /**
     * count total Complain
     * @param mixed $s_about
     * @return int count total rows
     */
    function TotalTechComplain($s_about = null, $s_status, $s_sdate = null, $s_edate = null)
    {
        if ($s_status == '') {
            $s_status = 'open';
        }
        $this->db->select('count(*) as total');
        $this->db->from('chat a')
            ->join('tech_complain b', 'a.complain_id = b.id_tech_complain', 'inner');
        if ($s_status != 'all') {
            $this->db->where('b.stat', $s_status);
        }

        if ($s_about != null) {
            $this->db->where("b.about", $s_about);
        }

        if ($s_sdate != null) {
            $this->db->where("b.create_date >=", iso_date($s_sdate) . ' 00:00:00');
        }
        if ($s_edate != null) {
            $this->db->where("b.create_date <=", iso_date($s_edate) . '23:59:59');
        }

        $query = $this->db->where('a.parent_id', 0)->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Consult
     * @param mixed $s_about
     * @return int count total rows
     */
    function TotalTechConsult($s_about = null, $s_status, $s_sdate = null, $s_edate = null)
    {
        if ($s_status == '') {
            $s_status = 'open';
        }
        $this->db->select('count(*) as total');
        $this->db->from('chat a')
            ->join('tech_consult b', 'a.consult_id = b.id_tech_consult', 'inner');
        if ($s_status != 'all') {
            $this->db->where('b.stat', $s_status);
        }

        if ($s_about != null) {
            $this->db->where("b.about", $s_about);
        }
        if ($s_sdate != null) {
            $this->db->where("b.create_date >=", iso_date($s_sdate) . ' 00:00:00');
        }
        if ($s_edate != null) {
            $this->db->where("b.create_date <=", iso_date($s_edate) . '23:59:59');
        }

        $query = $this->db->where('a.parent_id', 0)->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Tech Complain
     * @param mixed $s_search
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTechComplain($s_search = null, $s_status, $s_sdate = null, $s_edate = null, $limit = 0, $per_pg = 0)
    {
        if ($s_status == '') {
            $s_status = 'open';
        }
        get_adm_location();
        $locate = get_adm_location();
        $locate = implode(', ', $locate);

        $where = array('a.parent_id' => 0, 'complain_id' => 1);

        // $this->db->select('c.username as maker,a.from_id as makerid, a.id_chat,b.id_tech_complain,b.about,b.ref_user_car,c.username as froms,a.text,a.status,d.location')
        // ->from('chat a')
        // ->join('tech_complain b','a.complain_id = b.id_tech_complain','inner')
        // ->join('user c','a.from_id = c.id_user','inner')
        // ->join('ref_location d','d.id_ref_location = b.ref_location','inner');


        $this->db->select('a.about,a.id_tech_complain,a.ref_user_car,a.ref_location,d.location,a.stat')
            ->from('tech_complain a')->join('ref_location d', 'd.id_ref_location = a.ref_location', 'inner')
            ->where("a.ref_location in($locate)");
        if ($s_status != 'all') {
            $this->db->where('a.stat', $s_status);
        }

        if ($s_sdate != null) {
            $this->db->where("a.create_date >=", iso_date($s_sdate) . ' 00:00:00');
        }
        if ($s_edate != null) {
            $this->db->where("a.create_date <=", iso_date($s_edate) . '23:59:59');
        }

        if ($s_search != null) {
            $this->db->like("a.about", $s_search);
        }
        $complain = $this->db->get();
        if ($complain->num_rows() > 0) {
            $complain = $complain->result_array();
            foreach ($complain as $k => $v) {
                $where = array('a.complain_id' => $v['id_tech_complain'], 'a.parent_id' => 0);
                $cht = $this->db->select('c.username as maker,a.from_id as makerid, a.id_chat,c.username as froms,a.text,a.status,a.create_date')
                    ->from('chat a')
                    ->join('user c', 'a.from_id = c.id_user', 'inner')
                    ->where($where)->get();

                if ($cht->num_rows() > 0) {
                    $chts = $cht->row_array();
                    $chts['stamp'] = $chts['create_date'];
                    $where = array('parent_id' => $chts['id_chat'], 'complain_id' => $v['id_tech_complain']);
                    $q1 = $this->db->order_by('create_date', 'desc')
                        ->where($where)->get('chat', 1);
                    if ($q1->num_rows() > 0) {
                        $q1 = $q1->row_array();
                        if ($q1['id_chat'] != $chts['id_chat']) {
                            if ($q1['type'] == 'msg') {
                                $tbl = 'user';
                                $id = 'id_user';
                            } else {
                                $tbl = 'auth_user';
                                $id = 'id_auth_user';
                            }
                            $chts['text'] = $q1['text'];
                            $chts['status'] = $q1['status'];
                            $chts['froms'] = db_get_one($tbl, 'username', "$id = '" . $q1['from_id'] . "'");
                            $chts['stamp'] = $q1['create_date'];
                        }
                    }
                }
                $complain[$k] = array_merge($complain[$k], $chts);
            }
        } else {
            $complain = array();
        }

        return $complain;

        // if ($s_search != null) $this->db->where("a.about",$s_search);
        // $this->db->where($where)
        // ->limit($per_pg,$limit);
        // $query = $this->db->get();
        // if($query->num_rows()>0) {
        // $query = $query->result_array();
        // foreach($query as $h => $n) {
        // $where = array('parent_id' => $n['id_chat'],'complain_id'=>1);
        // $q1 = $this->db->order_by('create_date','desc')
        // ->where($where)->get('chat',1);
        // if($q1->num_rows()>0) {
        // $q1 = $q1->row_array();
        // if($q1['id_chat'] != $n['id_chat']) {
        // if($q1['type'] == 'msg') {
        // $tbl = 'user';
        // $id  = 'id_user';
        // }else {
        // $tbl = 'auth_user';
        // $id  = 'id_auth_user';
        // }
        // $query[$h]['text']   	= $q1['text'];
        // $query[$h]['status'] 	= $q1['status'];
        // $query[$h]['froms'] 	= db_get_one($tbl,'username',"$id = '".$q1['from_id']."'");
        // }
        // }
        // }
        // return $query;
        // }
    }

    /**
     * retrieve all Tech Consult
     * @param mixed $s_search
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTechConsult($s_search = null, $s_status, $s_sdate = null, $s_edate = null, $limit = 0, $per_pg = 0)
    {
        if ($s_status == '') {
            $s_status = 'open';
        }
        get_adm_location();
        $locate = get_adm_location();
        $locate = implode(', ', $locate);

        $where = array('a.parent_id' => 0, 'consult_id' => 1);

        $this->db->select('a.about,a.id_tech_consult,a.ref_location,d.location,a.stat')
            ->from('tech_consult a')
            ->join('ref_location d', 'd.id_ref_location = a.ref_location', 'left')
            ->where("a.ref_location in($locate)");
        if ($s_status != 'all') {
            $this->db->where('a.stat', $s_status);
        }

        if ($s_sdate != null) {
            $this->db->where("a.create_date >=", iso_date($s_sdate) . ' 00:00:00');
        }
        if ($s_edate != null) {
            $this->db->where("a.create_date <=", iso_date($s_edate) . '23:59:59');
        }

        if ($s_search != null) {
            $this->db->like("a.about", $s_search);
        }

        $consult = $this->db->get();

        if ($consult->num_rows() > 0) {
            $consult = $consult->result_array();
            foreach ($consult as $k => $v) {
                $where = array('a.consult_id' => $v['id_tech_consult'], 'a.parent_id' => 0);
                $cht = $this->db->select('c.username as maker,a.from_id as makerid, a.id_chat,c.username as froms,a.text,a.status,a.create_date')
                    ->from('chat a')
                    ->join('user c', 'a.from_id = c.id_user', 'inner')
                    ->where($where)->get();

                if ($cht->num_rows() > 0) {
                    $chts = $cht->row_array();

                    $chts['stamp'] = $chts['create_date'];

                    $where = array('parent_id' => $chts['id_chat'], 'consult_id' => $v['id_tech_consult']);
                    $q1 = $this->db->order_by('create_date', 'desc')
                        ->where($where)->get('chat', 1);
                    if ($q1->num_rows() > 0) {
                        $q1 = $q1->row_array();
                        if ($q1['id_chat'] != $chts['id_chat']) {
                            if ($q1['type'] == 'msg') {
                                $tbl = 'user';
                                $id = 'id_user';
                            } else {
                                $tbl = 'auth_user';
                                $id = 'id_auth_user';
                            }
                            $chts['text'] = $q1['text'];
                            $chts['status'] = $q1['status'];
                            $chts['froms'] = db_get_one($tbl, 'username', "$id = '" . $q1['from_id'] . "'");
                            $chts['stamp'] = $q1['create_date'];
                        }
                    }
                }
                $consult[$k] = array_merge($consult[$k], $chts);
            }
        } else {
            $consult = array();
        }
        return $consult;
    }

    function getAllComplainHistory($Id)
    {
        $where = array('a.complain_id' => $Id);

        $this->db->select('a.from_id, a.id_chat,b.about,b.ref_user_car,a.text,a.type,a.status,d.location')
            ->from('chat a')
            ->join('tech_complain b', 'a.complain_id = b.id_tech_complain', 'inner')
            ->join('ref_location d', 'd.id_ref_location = b.ref_location', 'left');

        $query = $this->db->order_by('a.create_date', 'asc')
            ->where($where)->get();

        if ($query->num_rows() > 0) {
            $query = $query->result_array();
            foreach ($query as $h => $n) {

                if ($n['type'] == 'msg') {
                    $tbl = 'user';
                    $id = 'id_user';
                } else {
                    $tbl = 'auth_user';
                    $id = 'id_auth_user';
                }
                $query[$h]['froms'] = db_get_one($tbl, 'username', "$id = '" . $n['from_id'] . "'");

            }
            return $query;
        }
        return false;
    }

    function getAllConsultHistory($Id)
    {
        $where = array('a.consult_id' => $Id);

        $this->db->select('a.from_id, a.id_chat,b.about,a.text,a.type,a.status,d.location')
            ->from('chat a')
            ->join('tech_consult b', 'a.consult_id = b.id_tech_consult', 'inner')
            ->join('ref_location d', 'd.id_ref_location = b.ref_location', 'left');

        $query = $this->db->order_by('a.create_date', 'asc')
            ->where($where)->get();

        echo $this->db->_error_message();

        if ($query->num_rows() > 0) {
            $query = $query->result_array();
            foreach ($query as $h => $n) {

                if ($n['type'] == 'msg') {
                    $tbl = 'user';
                    $id = 'id_user';
                } else {
                    $tbl = 'auth_user';
                    $id = 'id_auth_user';
                }
                $query[$h]['froms'] = db_get_one($tbl, 'username', "$id = '" . $n['from_id'] . "'");

            }
            return $query;
        }
        return false;
    }

    /**
     * Insert Tech Complain
     * @param array data insert
     * @return type string $query
     */
    function InsertTechComplain($data)
    {
        $this->db->insert('chat', $data);
        return $this->db->insert_id();
    }

    /**
     * Insert Tech Consult
     * @param array data insert
     * @return type string $query
     */
    function InsertTechConsult($data)
    {
        $this->db->insert('chat', $data);
        return $this->db->insert_id();
    }

    /**
     * change Complain publish status
     * @param type $Id
     * @return string publish status
     */
    function ChangePublishComplain($Id)
    {
        $this->db->where('id_tech_complain', $Id);
        $query = $this->db->get('tech_complain');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['stat'] == 'open') {
                $val = 'closed';
            } else {
                $val = 'open';
            }

            $this->db->where('id_tech_complain', $row['id_tech_complain']);
            $this->db->update('tech_complain', array('stat' => $val));

            // if ($val == 1) return 'Publish';
            // else return 'Not Publish';
            return $val;
        }
    }

    /**
     * change Complain publish status
     * @param type $Id
     * @return string publish status
     */
    function ChangePublishConsult($Id)
    {
        $this->db->where('id_tech_consult', $Id);
        $query = $this->db->get('tech_consult');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['stat'] == 'open') {
                $val = 'closed';
            } else {
                $val = 'open';
            }

            $this->db->where('id_tech_consult', $row['id_tech_consult']);
            $this->db->update('tech_consult', array('stat' => $val));

            // if ($val == 1) return 'Publish';
            // else return 'Not Publish';
            return $val;
        }
    }

}
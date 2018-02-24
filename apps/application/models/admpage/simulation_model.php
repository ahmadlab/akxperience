<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Credit Simulation Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Credit Simulation model
 ***********************************/
class Simulation_model extends CI_Model
{
    /**
     * count total Credit Simulation
     * @param type $search1
     * @param type $search2
     * @param type $search3
     * @return int count total rows
     */
    function TotalCreditSimulation($search1 = null, $search2 = null, $search3)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("ref_bank = '$search1'");
        }
        if ($search2 != null) {
            $this->db->where("ref_tenor = '$search2'");
        }
        if ($search3 != null) {
            $this->db->where("ref_down_payment = '$search3'");
        }

        $query = $this->db->get('credit_simulation');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Tenor
     * @param type $search1
     * @return int count total rows
     */
    function TotalTenor($search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("ref_bank = '$search1'");
        }

        $query = $this->db->get('ref_tenor');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total Down Payment
     * @param type $search1
     * @return int count total rows
     */
    function TotalDownPayment($search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("down_payment = '$search1'");
        }

        $query = $this->db->get('ref_down_payment');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }


    /**
     * count total Bank Account
     * @param type $search1
     * @return int count total rows
     */
    function TotalBankAccount($search1 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->like("bank_name", $search1);
        }

        $query = $this->db->get('ref_bank');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all Credit Simulation
     * @param type $search1
     * @param type $search2
     * @param type $search3
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCreditSimulation($search1 = null, $search2 = null, $search3 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("ref_bank = '$search1'");
        }
        if ($search2 != null) {
            $this->db->where("ref_tenor = '$search2'");
        }
        if ($search3 != null) {
            $this->db->where("ref_down_payment = '$search3'");
        }

        $this->db->order_by('ref_bank,ref_tenor', 'asc');
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('credit_simulation');
        return $query;
    }

    /**
     * retrieve all Tenor
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllTenor($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("ref_bank = '$search1'");
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('ref_tenor');
        return $query;
    }

    /**
     * retrieve all Down Payment
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllDownPayment($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("down_payment = '$search1'");
        }

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('ref_down_payment');
        return $query;
    }

    /**
     * retrieve all Bank Account
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllBankAccount($search1 = null, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->like("bank_name", $search1);
        }

        $this->db->order_by('sort', 'asc')->limit($per_pg, $limit);
        $query = $this->db->get('ref_bank');
        return $query;
    }

    /**
     * get Credit Simulation by id
     * @param type $Id
     * @return object $query
     */
    function GetCreditSimulationById($Id)
    {
        $this->db->where('id_credit_simulation', $Id);
        $this->db->limit(1);
        $query = $this->db->get('credit_simulation');
        return $query;
    }

    /**
     * get Tenor by  id
     * @param type $Id
     * @return object $query
     */
    function GetTenorById($Id)
    {
        $this->db->where('id_ref_tenor', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_tenor');
        return $query;
    }

    /**
     * get Down Payment by  id
     * @param type $Id
     * @return object $query
     */
    function GetDownPaymentById($Id)
    {
        $this->db->where('id_ref_down_payment', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_down_payment');
        return $query;
    }

    /**
     * get Bank Account by  id
     * @param type $Id
     * @return object $query
     */
    function GetBankAccountById($Id)
    {
        $this->db->where('id_ref_bank', $Id);
        $this->db->limit(1);
        $query = $this->db->get('ref_bank');
        return $query;
    }

    /**
     * get reference urut for sort
     * @param type $id_parent
     * @return type string maximum field urut
     */
    function GetRefSortMax($id_parent = null)
    {
        $this->db->select('max(sort) as sort');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('ref_bank');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['sort'];
        } else {
            return '0';
        }
    }

    /**
     * get reference minimum urut for sort
     * @param type $id_parent
     * @return type string minimum field urut
     */
    function GetRefSortMin($id_parent = null)
    {
        $this->db->select('min(sort) as sort');
        if ($id_parent) {
            $this->db->where('id_parents_menu_admin', $id_parent);
        }
        $query = $this->db->get('ref_bank');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['sort'];
        } else {
            return '0';
        }
    }

    /**
     * change Bank account sort
     * @param type $id_page
     * @param type $urut
     * @param type $direction
     */
    function ChangeSort($id_page, $sort, $direction)
    {
        $this->db->where('id_ref_bank', $id_page);
        // $this->db->where('id_parents_menu_admin',$parent_id);
        $this->db->where('sort', $sort);
        $this->db->order_by('sort', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('ref_bank');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($direction == "down") {
                $this->db->where('sort > ', $sort);
                $this->db->order_by('sort', 'asc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_bank');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateBankAccount(array('sort' => $sort), $row_1['id_ref_bank']);
                    $this->UpdateBankAccount(array('sort' => $row_1['sort']), $id_page);
                }
            } elseif ($direction == "up") {
                $this->db->where('sort < ', $sort);
                $this->db->order_by('sort', 'desc');
                $this->db->limit(1);
                $query_1 = $this->db->get('ref_bank');
                if ($query_1->num_rows() > 0) {
                    $row_1 = $query_1->row_array();

                    $this->UpdateBankAccount(array('sort' => $sort), $row_1['id_ref_bank']);
                    $this->UpdateBankAccount(array('sort' => $row_1['sort']), $id_page);
                }
            }
        }
    }


    /**
     * insert Credit Simulation
     * @param array $data
     * @return int $id last inserted
     */
    function InsertCreditSimulation($data)
    {
        $this->db->insert('credit_simulation', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * insert Tenor
     * @param array $data
     * @return int $id last inserted
     */
    function InsertTenor($data)
    {
        $this->db->insert('ref_tenor', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * insert Down Payment
     * @param array $data
     * @return int $id last inserted
     */
    function InsertDownPayment($data)
    {
        $this->db->insert('ref_down_payment', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * insert Bank Account
     * @param array $data
     * @return int $id last inserted
     */
    function InsertBankAccount($data)
    {
        $this->db->insert('ref_bank', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    /**
     * change cars brands publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishBank($id)
    {
        $this->db->where('id_ref_bank', $id);
        $query = $this->db->get('ref_bank');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_ref_bank', $row['id_ref_bank']);
            $this->db->update('ref_bank', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Published';
            } else {
                return 'Not Published';
            }
        }
    }

    /**
     * update Credit Simulation
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCreditSimulation($data, $Id)
    {
        $this->db->where('id_credit_simulation', $Id);
        $this->db->update('credit_simulation', $data);
    }

    /**
     * update Tenor
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateTenor($data, $Id)
    {
        $this->db->where('id_ref_tenor', $Id);
        $this->db->update('ref_tenor', $data);
    }

    /**
     * update Down Payment
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateDownPayment($data, $Id)
    {
        $this->db->where('id_ref_down_payment', $Id);
        $this->db->update('ref_down_payment', $data);
    }

    /**
     * update Bank Account
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateBankAccount($data, $Id)
    {
        $this->db->where('id_ref_bank', $Id);
        $this->db->update('ref_bank', $data);
    }

    /**
     * delete Credit Simulation by id
     * @param type $Id
     */
    function DeleteCreditSimulation($Id)
    {
        $this->db->where_in('id_credit_simulation', $Id);
        $this->db->delete('credit_simulation');
    }

    /**
     * delete Tenor by id
     * @param type $Id
     */
    function DeleteTenor($Id)
    {
        $this->db->where_in('id_ref_tenor', $Id);
        $this->db->delete('ref_tenor');
    }

    /**
     * delete Down Payment by id
     * @param type $Id
     */
    function DeleteDownPayment($Id)
    {
        $this->db->where_in('id_ref_down_payment', $Id);
        $this->db->delete('ref_down_payment');
    }

    /**
     * delete Bank Account by id
     * @param type $Id
     */
    function DeleteBankAccount($Id)
    {
        $this->db->where_in('id_ref_bank', $Id);
        $this->db->delete('ref_bank');
    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteBankThumbByID($Id)
    {
        $data = $this->db->get_where('ref_bank', array('id_ref_bank' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/bank_account/' . $data['bank_thumb'];
            $xfile = substr($file, 0, -4);
            if (file_exists($file)) {
                @unlink($file);
                @unlink($xfile . '_HDPI.png');
                @unlink($xfile . '_XHDPI.png');
            }
            $this->db->where('id_ref_bank', $Id)->update('ref_bank', array('bank_thumb' => ''));
        }
    }


    /* Check whether Credit Simulation already exists or not
     * @param int $ref_bank
     * @param int $ref_tenor
     * @param int $ref_dp
     * return true on already in or false on not yet
     */
    function already_in($id, $ref_bank = null, $ref_tenor = null, $ref_dp = null)
    {
        $where = array('ref_bank' => $ref_bank, 'ref_tenor' => $ref_tenor, 'ref_down_payment' => $ref_dp);
        if ($ref_bank != null && $ref_tenor != null && $ref_dp != null) {
            if ($id) {
                $this->db->where_not_in('id_credit_simulation', array($id));
            }
            $this->db->where($where);
            $exist = $this->db->get('credit_simulation');
            if ($exist->num_rows() > 0) {
                return true;
            }
        }
        return false;
    }

    function check_exist($table, $field, $var)
    {
        if ($this->db->get_where($table, "$field = '$var'")->num_rows() > 0) {
            return true;
        }

        return false;
    }

    function check_exist_bank($table, $field, $val, $var)
    {
        // if($this->db->get_where($table,"$field = '$var'")->num_rows()>0)
        if ($var) {
            $this->db->where_not_in('id_ref_bank', array($var));
        }
        $chk = $this->db->where("$field = '$val'")->get($table)->num_rows();
        if ($chk > 0) {
            echo last_query();
            return false;
        }
        return true;
    }
}
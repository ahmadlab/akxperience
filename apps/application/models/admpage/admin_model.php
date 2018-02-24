<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Admin_menu Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Adminmenu model
 ***********************************/
class Admin_model extends CI_Model
{

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * retrieve user all user admin
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllUsersAdmin($search1 = null, $search2 = null, $search3 = 0, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("LCASE(name) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email) LIKE '%" . utf8_strtolower($search2) . "%'");
        }
        if ($search3 != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('auth_user');
        return $query;
    }

    /**
     *
     * @param int $id
     * @return bool
     */
    function check_is_superadmin($id, $is_super_admin)
    {
        $this->db->where('id_auth_user', $id);
        $this->db->where('status', 1);
        $this->db->limit(1);
        $query = $this->db->get('auth_user');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($is_super_admin == 1) {
                return true;
            } else {
                if ($row['is_superadmin'] == 0 && $is_super_admin == 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * count total user admin
     * @param type $search1
     * @param type $search2
     * @return type integer count total rows
     */
    function TotalUserAdmin($search1 = null, $search2 = null, $search3 = 0)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("LCASE(name) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email) LIKE '%" . utf8_strtolower($search2) . "%'");
        }
        if ($search3 != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $query = $this->db->get('auth_user');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * get group name by id group
     * @param type $id_group
     * @return type string group name
     */
    function GetGroupNameById($id_group)
    {
        $this->db->where('id_auth_user_group', $id_group);
        $this->db->limit(1);
        $query = $this->db->get('auth_user_group');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['auth_user_group'];
        } else {
            return '--';
        }
    }

    /**
     * get site name for user admin
     * @param type $id_site
     * @return type site name
     */
    function getUserAdminSiteName($id_site)
    {
        // $this->db->where('id_site',$id_site);
        // $this->db->where('is_delete',0);
        // $this->db->limit(1);
        // $query = $this->db->get('sites');
        // if ($query->num_rows()>0) {
        // $row = $query->row_array();
        // return $row['site_name'];
        // } else {
        // return '--';
        // }
    }

    /**
     * list of user admin group
     * @return type string $query
     */
    function ListUsersGroup($is_superadmin = 0)
    {
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $this->db->order_by('auth_user_group', 'asc');
        $query = $this->db->get('auth_user_group');
        return $query;
    }

    /**
     * get site list
     * @return type $query
     */
    function getSites()
    {
        // $this->db->order_by('site_name','asc');
        // $this->db->where('is_delete',0);
        // $query = $this->db->get('sites');
        // return $query;
    }

    /**
     * get user admin by user admin id
     * @param type $Id
     * @return type string $query
     */
    function GetUsersAdminById($Id)
    {
        $this->db->where('id_auth_user', $Id);
        $this->db->order_by('create_date', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('auth_user');
        return $query;
    }

    /**
     * check existing username
     * @param type $username
     * @param type $Id
     * @return type boolean (true or false)
     */
    function CheckExistsUsername($username, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_auth_user != ', $Id);
        }
        $this->db->where('username', $username);
        $query = $this->db->get('auth_user');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing email
     * @param type $email
     * @param type $Id
     * @return type boolean (true or false)
     */
    function CheckExistsEmail($email, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_auth_user != ', $Id);
        }
        $this->db->where('email', $email);
        $query = $this->db->get('auth_user');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * insert user admin
     * @param type $data
     * @return type string $id_auth_user last inserted id user admin
     */
    function InsertAdminUser($data)
    {
        $this->db->insert('auth_user', $data);
        $id_auth_user = $this->db->insert_id();
        return $id_auth_user;
    }

    /**
     * update user admin by id user
     * @param type $Id
     * @param type $data
     */
    function UpdateAdminUser($Id, $data)
    {
        $this->db->where('id_auth_user', $Id);
        $this->db->update('auth_user', $data);
    }

    /**
     * delete user admin by id user
     * @param type $Id
     */
    function DeleteAdminUser($Id)
    {
        $this->db->where_in('id_auth_user', $Id);
        $this->db->delete('auth_user');
    }

}


/* End of file admin_model.php */
/* Location: ./application/model/webcontrol/admin_model.php */

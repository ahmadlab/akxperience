<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Auth_group Model Class
 * @Author  : Latada
 * @Email   : mac_ [at] gxrg [dot] org
 * @Type     : Model
 * @Desc     : auth group model
 *************************************/
class Authgroup_model extends CI_Model
{

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * list group
     * @param int $is_superadmin
     * @param int $perpage
     * @param int $limit
     * @return string $query
     */
    function ListGroup($is_superadmin = null, $perpage = null, $limit = null)
    {
        if ($limit && $perpage) {
            $this->db->limit($perpage, $limit);
        }
        $this->db->order_by('id_auth_user_group', 'asc');
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $query = $this->db->get('auth_user_group');
        return $query;
    }

    /**
     * get total group
     * @param int $is_superadmin
     * @return string $total
     */
    function GetTotalGroup($is_superadmin = null)
    {
        $this->db->select('count(*) as total');
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $query = $this->db->get('auth_user_group');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     *
     * @param int $Id
     * @return string $query
     */
    function GetAdminGroupById($Id)
    {
        $this->db->where('id_auth_user_group', $Id);
        $this->db->order_by('id_auth_user_group', 'asc');
        $this->db->limit(1);
        $query = $this->db->get('auth_user_group');
        return $query;
    }

    /**
     *
     * @param array $data
     * @return int $id_auth_user_group
     */
    function InsertAdminGroup($data)
    {
        $this->db->insert('auth_user_group', $data);
        $id_auth_user_group = $this->db->insert_id();
        return $id_auth_user_group;
    }

    /**
     *
     * @param int $Id
     * @param array $data
     */
    function UpdateAdminGroup($Id, $data)
    {
        $this->db->where('id_auth_user_group', $Id);
        $this->db->update('auth_user_group', $data);
    }

    /**
     *
     * @param int $Id
     */
    function DeleteAdminGroup($Id)
    {
        $this->db->where('id_auth_user_group', $Id);
        $this->db->delete('auth_user_group');
    }

    /**
     * delete auth group
     * @param int $id_group
     */
    function DeleteAuthGroup($id_group)
    {
        $this->db->where('id_auth_user_group', $id_group);
        $this->db->delete('auth_pages');

    }

    /**
     *
     * @param array $data
     */
    function InsertAuthGroup($data)
    {
        $this->db->insert('auth_pages', $data);
    }

    /**
     *
     * @param string $group_name
     * @param int $id
     * @return bool true/false
     */
    function CheckExistsAdminGroup($group_name, $id = 0)
    {
        if ($id) {
            $this->db->where('id_auth_user_group != ', $id);
        }
        $this->db->where('auth_user_group', $group_name);
        $query = $this->db->get('auth_user_group');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param int $id_parent
     * @param int $is_superadmin
     * @return string $query
     */
    function getAllAdminMenu($id_parent, $is_superadmin = null)
    {
        $this->db->where('id_parents_menu_admin', $id_parent);
        if ($is_superadmin != 1) {
            $this->db->where('is_superadmin', 0);
        }
        $this->db->order_by('id_menu_admin', 'asc');
        $query = $this->db->get('menu_admin');
        return $query;
    }

    /**
     *
     * @param int $id_group
     * @param int $id_admin_menu
     * @return bool $id_auth_pages
     */
    function getAuthPages($id_group, $id_admin_menu)
    {
        $this->db->where('id_auth_user_group', $id_group);
        $this->db->where('id_menu_admin', $id_admin_menu);
        $this->db->limit(1);
        $query = $this->db->get('auth_pages');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['id_auth_pages'];
        } else {
            return false;
        }
    }

    /**
     *
     * @param int $parent_id
     * @return string $query
     */
    function GetMenuByParent($parent_id)
    {
        $this->db->where('id_parents_menu_admin', $parent_id);
        $query = $this->db->get('ref_menu_admin');
        return $query;
    }

    /**
     *
     * @param int $id_ref_menu
     * @param int $id_group
     * @return string $query
     */
    function GetMenuByRef($id_ref_menu, $id_group)
    {
        $this->db->where('id_menu_admin', $id_ref_menu);
        $this->db->where('id_auth_user_group', $id_group);
        $query = $this->db->get('auth_pages');
        return $query;
    }

}


/* End of file authgroup_model.php */
/* Location: ./application/model/webcontrol/authgroup_model.php */
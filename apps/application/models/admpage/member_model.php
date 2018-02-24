<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * Member Model Class
 * @Author : Latada
 * @Email  : mac_ [at] gxrg [dot] org
 * @Type    : Model
 * @Desc    : Member model
 ***********************************/
class Member_model extends CI_Model
{
    /**
     * count total user MEMBER
     * @param type $search1
     * @param type $search2
     * @return type integer count total rows
     */
    function TotalUserMember($search1 = null, $search2 = null, $search3 = null, $search4 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("LCASE(username) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email) LIKE '%" . utf8_strtolower($search2) . "%'");
        }

        // if ($search3 != 1) $this->db->where('is_superadmin',0);

        if ($search3 != null) {
            $search3 = $search3 == '2' ? '0' : '1';
            $this->db->where("status", $search3);
        }

        if ($search4 != null) {
            $this->db->where("LCASE(user_type) LIKE '%" . utf8_strtolower($search4) . "%'");
        }

        // $where = array('user_type !=' => 'sales','status' => '1');

        // $this->db->where($where);


        $this->db->where('user_type !=', 'sales');


        $query = $this->db->get('user');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total user Sales
     * @param type $search1
     * @param type $search2
     * @return type integer count total rows
     */
    function TotalUserSales($search1 = null, $search2 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("LCASE(username) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email) LIKE '%" . utf8_strtolower($search2) . "%'");
        }
        // if ($search3 != 1) $this->db->where('is_superadmin',0);

        // $where = array('user_type' => 'sales','status' => '1');

        // $this->db->where($where);

        $this->db->where('user_type', 'sales');


        $query = $this->db->get('user');

        echo $this->db->_error_message();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all user member
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllUsersMember(
        $search1 = null,
        $search2 = null,
        $search3 = null,
        $search4 = null,
        $search5 = 0,
        $limit = 0,
        $per_pg = 0
    ) {
        if ($search1 != null) {
            $this->db->where("LCASE(username) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email) LIKE '%" . utf8_strtolower($search2) . "%'");
        }

        if ($search3 != null) {
            $search3 = $search3 == '2' ? '0' : '1';
            $this->db->where("status", $search3);
        }

        if ($search4 != null) {
            $this->db->where("LCASE(user_type) LIKE '%" . utf8_strtolower($search4) . "%'");
        }

        $this->db->where('user_type !=', 'sales');
        $this->db->join('ref_location', 'user.nearest_workshop = ref_location.id_ref_location', 'left');

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('user');

        return $query;
    }

    /**
     * retrieve all user sales
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllUsersSales($search1 = null, $search2 = null, $search3 = 0, $limit = 0, $per_pg = 0)
    {
        if ($search1 != null) {
            $this->db->where("LCASE(username) LIKE '%" . utf8_strtolower($search1) . "%'");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email) LIKE '%" . utf8_strtolower($search2) . "%'");
        }
        // if ($search3 != 1) $this->db->where('is_superadmin',0);

        // $where = array('user_type' => 'sales','status' => '1');

        // $this->db->where($where);

        $this->db->where('user_type', 'sales');

        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('user');
        return $query;
    }

    /**
     * change News publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishMember($Id)
    {
        $this->db->where('id_user', $Id);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['status'] == '1') {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_user', $row['id_user']);
            $this->db->update('user', array('status' => $val));

            if ($val == 1) {
                return 'Active';
            } else {
                return 'Not Active';
            }
        }
    }


    /**
     * get user member by user member id
     * @param type $Id
     * @return type string $query
     */
    function GetUsersMemberById($Id)
    {
        $this->db->where('id_user', $Id);
        // $this->db->where('user_type !=','sales');
        $this->db->order_by('create_date', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('user');
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
            $this->db->where('id_user != ', $Id);
        }
        $this->db->where('username', $username);
        $query = $this->db->get('user');
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
            $this->db->where('id_user != ', $Id);
        }
        $this->db->where('email', $email);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Card Id
     * @param type $carid
     * @param type $Id
     * @return type boolean (true or false)
     */
    function CheckExistsCardId($carid, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_user != ', $Id);
        }
        $this->db->where('card_id', $carid);
        $query = $this->db->get('user');

        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Delete Avatar By Id
     * @param string $id avatar
     * @return string $id_user last inserted id user member
     */
    function DeleteAvatarByID($Id)
    {
        $usr = $this->db->get_where('user', array('id_user' => $Id));
        if ($usr->num_rows() > 0) {
            $usr = $usr->row_array();
            $file = './uploads/ava/' . $usr['avatar'];
            if ($usr['avatar'] !== '' && file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id_user', $Id)->update('user', array('avatar' => ''));
        }
    }

    /**
     * Delete Avatar By Id
     * @param string $id avatar
     * @return string $id_user last inserted id user member
     */
    function DeleteUserCarByID($Id)
    {
        $usr = $this->db->get_where('user_cars', array('id_user_cars' => $Id));
        if ($usr->num_rows() > 0) {
            $usr = $usr->row_array();
            $this->db->where('id_user_cars', $Id)->delete('user_cars');
        }
    }

    /**
     * insert user member
     * @param type $data
     * @return type string $id_user last inserted id user member
     */
    function InsertMemberUser($data)
    {
        $this->db->insert('user', $data);
        $id_user = $this->db->insert_id();
        return $id_user;
    }

    /**
     * update user member by id user
     * @param type $Id
     * @param type $data
     * @return Void
     */
    function UpdateMemberUser($data, $Id)
    {
        $this->db->where('id_user', $Id);
        $this->db->update('user', $data);
    }

    /**
     * delete user member by id user
     * @param type $Id
     */
    function DeleteMemberUser($Id)
    {

        if (is_array($Id)) {
            foreach ($Id as $v) {
                $this->DeleteAvatarByID($v);
            }
            $this->db->where_in('id_user', $Id);

        } else {
            $this->DeleteAvatarByID($Id);
            $this->db->where('id_user', $Id);
        }

        $this->db->delete('user');
    }

    /**
     * insert into user car
     * @param array $data
     */
    function updateMemberUserCars($data)
    {
        $this->db->insert('user_cars', $data);
    }

    /**
     * delete member cars by id user
     * @param type $Id
     */
    function deleteMemberUserCars($Id)
    {
        $this->db->where_in('id_user', array($Id))
            ->delete('user_cars');
    }
}


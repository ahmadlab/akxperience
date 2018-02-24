<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*************************************
 * User_model Class
 * @Author  : Latada
 * @Email   : mac_ [at] gxrg [dot] org
 * @Type     : Model
 * @Desc     : Module model
 *************************************/
class User_model extends CI_Model
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * count all user
     * @param type $search1
     * @param type $search2
     * @param type $search3
     * @return type integer number of user
     */
    function getTotalUser($search1 = null, $search2 = null, $search3 = null)
    {
        $this->db->select('count(*) as total');
        if ($search1 != null) {
            $this->db->where("(LCASE(name) LIKE '%" . utf8_strtolower($search1) . "%' OR LCASE(display_name) LIKE '%" . utf8_strtolower($search1) . "%')");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email ) LIKE '%" . utf8_strtolower($search2) . "%'");
        }
        if ($search3 != null) {
            $this->db->where('activation_status', $search3);
        }
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * get all user
     * @param type $search1
     * @param type $search2
     * @param type $search3
     * @param type $limit
     * @param type $perpage
     * @return type string $query
     */
    function GetAllUser($search1 = null, $search2 = null, $search3 = null, $limit = 0, $perpage = 0)
    {
        if ($search1 != null) {
            $this->db->where("(LCASE(name) LIKE '%" . utf8_strtolower($search1) . "%' OR LCASE(display_name) LIKE '%" . utf8_strtolower($search1) . "%')");
        }
        if ($search2 != null) {
            $this->db->where("LCASE(email ) LIKE '%" . utf8_strtolower($search2) . "%'");
        }
        if ($search3 != null) {
            $this->db->where('activation_status', $search3);
        }
        if ($perpage > 0) {
            $this->db->limit($perpage, $limit);
        }
        $this->db->order_by('id_user', 'desc');
        $query = $this->db->get('users');

        return $query;
    }

    /**
     * get user by user id
     * @param type $Id
     * @return type string $query
     */
    function GetUserByID($Id = 0)
    {
        $this->db->where('id_user', $Id);
        $this->db->limit(1);
        $this->db->order_by('id_user', 'desc');
        $query = $this->db->get('users');

        return $query;
    }

    /**
     *update user by user id
     * @param type $Id
     * @param type $data
     */
    function UpdateUser($Id, $data)
    {
        $this->db->where('id_user', $Id);
        $this->db->update('users', $data);
    }

    /**
     * insert user and returning last inserted id
     * @param type $data
     * @return type integer $id_user last inserted id
     */
    function InsertUser($data)
    {
        $this->db->insert('users', $data);
        $id_user = $this->db->insert_id();
        return $id_user;
    }

    /**
     * delete user by user id
     * //(set status to deleted, but not delete data just incase)
     * @param type $Id
     */
    function DeleteUser($Id)
    {
        $query = $this->GetUserByID($Id);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            /*
            $data = array('is_delete'=>1);
            $this->db->update('users',$data);
             * *
             */

            // delete store
            $this->db->where('id_user', $row['id_user']);
            $stores = $this->db->get('stores');
            // if user have store
            if ($stores->num_rows() > 0) {
                foreach ($stores->result_array() as $store) {
                    $this->db->where('id_store', $store['id_store']);
                    $items = $this->db->get('items');
                    // if user have items
                    if ($items->num_rows() > 0) {
                        foreach ($items->result_array() as $item) {
                            $this->db->where('id_item', $item['id_item']);
                            $galleries = $this->db->get('items_galleries');
                            if ($galleries->num_rows() > 0) {
                                foreach ($galleries->result_array() as $gallery) {
                                    // delete gallery picture
                                    if ($gallery['image'] != '' && file_exists('./uploads/item/' . $gallery['image'])) {
                                        @unlink('./uploads/item/' . $gallery['image']);
                                    }
                                    if ($gallery['thumb'] != '' && file_exists('./uploads/item/' . $gallery['thumb'])) {
                                        @unlink('./uploads/item/' . $gallery['thumb']);
                                    }
                                }
                                // delete item gallery db user
                                $this->db->where('id_item', $item['id_item']);
                                $this->db->delete('items_galleries');
                            }

                            // delete item picture
                            if ($item['primary_image'] != '' && file_exists('./uploads/item/' . $item['primary_image'])) {
                                @unlink('./uploads/item/' . $item['primary_image']);
                            }
                            if ($item['primary_thumb'] != '' && file_exists('./uploads/item/' . $item['primary_thumb'])) {
                                @unlink('./uploads/item/' . $item['primary_thumb']);
                            }
                            // delete item comment db
                            $this->db->where('id_item', $item['id_item']);
                            $this->db->delete('items_comments');
                        }
                        // delete item db user
                        $this->db->where('id_store', $store['id_store']);
                        $this->db->delete('items');

                        // delete item comment by id user
                        $this->db->where('id_user', $row['id_user']);
                        $this->db->delete('items_comments');

                    }

                    // delete store picture
                    if ($store['logo'] != '' && file_exists('./uploads/store/' . $store['logo'])) {
                        @unlink('./uploads/store/' . $store['logo']);
                    }
                    if ($store['logo_thumbnail'] != '' && file_exists('./uploads/store/' . $store['logo_thumbnail'])) {
                        @unlink('./uploads/store/' . $store['logo_thumbnail']);
                    }
                    // delete store feedback
                    $this->db->where('id_store', $store['id_store']);
                    $this->db->delete('stores_feedbacks');

                    // delete store promote
                    $this->db->where('id_store', $store['id_store']);
                    $this->db->delete('stores_promotes');
                }

                // delete feedback by user id
                $this->db->where('id_user', $row['id_user']);
                $this->db->delete('stores_feedbacks');

                // delete store db user
                $this->db->where('id_user', $row['id_user']);
                $this->db->delete('stores');
            }

            // delete fashion tag
            $this->db->where('id_user', $row['id_user']);
            $fashion_tags = $this->db->get('fashion_tags');
            if ($fashion_tags->num_rows() > 0) {
                foreach ($fashion_tags->result_array() as $fashion_tag) {
                    if ($fashion_tag['picture_content'] != '' && file_exists('./uploads/fashion_tag/' . $fashion_tag['picture_content'])) {
                        @unlink('./uploads/fashion_tag/' . $fashion_tag['picture_content']);
                    }
                    if ($fashion_tag['picture_thumbnail'] != '' && file_exists('./uploads/fashion_tag/' . $fashion_tag['picture_thumbnail'])) {
                        @unlink('./uploads/fashion_tag/' . $fashion_tag['picture_thumbnail']);
                    }
                    if (file_exists('./uploads/fashion_tag/ori_' . $fashion_tag['picture_thumbnail'])) {
                        @unlink('./uploads/fashion_tag/' . $fashion_tag['picture_thumbnail']);
                    }
                    // delete fashion tag comment
                    $this->db->where('id_fashion_tag', $fashion_tag['id_fashion_tag']);
                    $this->db->delete('fashion_tags_comments');

                    // delete fashion tag coordinate
                    $this->db->where('id_fashion_tag', $fashion_tag['id_fashion_tag']);
                    $this->db->delete('fashion_tags_coordinates');
                }
                // delete fashion tag comment
                $this->db->where('id_user', $row['id_user']);
                $this->db->delete('fashion_tags_comments');

                // delete fashion tag db user
                $this->db->where('id_user', $row['id_user']);
                $this->db->delete('fashion_tags');
            }

            // delete user comment article
            $this->db->where('id_user', $row['id_user']);
            $this->db->delete('blogs_comments');
            $this->db->where('id_user', $row['id_user']);
            $this->db->delete('fashions_comments');
            $this->db->where('id_user', $row['id_user']);
            $this->db->delete('places_comments');
            $this->db->where('id_user', $row['id_user']);
            $this->db->delete('issues_comments');

            // delete user pic
            $username = strtolower($row['username']);
            $this->DeletePictureByID($row['id_user']);
            $path = './uploads/user/';
            if (is_dir($path . $username)) {
                remove_module_directory($path . $username);
            }

            $this->db->where('id_user', $row['id_user']);
            $this->db->delete('users');
        }
    }

    /**
     * delete picture by user id
     * @param type $id
     * @param type $type
     */
    function DeletePictureByID($id)
    {
        $data = array();
        $query = $this->GetUserByID($id);

        $path = './uploads/user/';

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $username = strtolower($row['username']);
            if ($row['image'] != '' && file_exists($path . $username . '/' . $row['image'])) {
                @unlink($path . $username . '/' . $row['image']);
                @unlink($path . $username . '/' . $row['image'] . '-thumb2');
            }
            if ($row['thumbnail'] != '' && file_exists($path . $username . '/' . $row['thumbnail'])) {
                @unlink($path . $username . '/' . $row['thumbnail']);
            }

            $data = array('image' => '', 'thumbnail' => '');


            $this->UpdateUser($row['id_user'], $data);
        }
    }

    /**
     * change blog publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangeStatus($Id)
    {
        $this->db->where('id_user', $Id);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['activation_status'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_user', $row['id_user']);
            $this->db->update('users', array('activation_status' => $val));

            if ($val == 1) {
                return 'Active';
            } else {
                return 'Not Active';
            }
        }
    }

    /**
     * this function is use for installation module
     * @return type boolean return true
     */
    function install()
    {
        /*
        $create_tbl_user = "
            CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('users')." (
                `id_user` int(11) NOT NULL AUTO_INCREMENT,
                `id_ref_publish` int(11) NOT NULL,
                `id_template` int(11) NOT NULL,
                `publish_date` date NOT NULL,
                `title` varchar(255) COLLATE utf8_bin NOT NULL,
                `intro` text COLLATE utf8_bin NOT NULL,
                `content` text COLLATE utf8_bin NOT NULL,
                `title_in` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                `intro_in` text COLLATE utf8_bin,
                `content_in` text COLLATE utf8_bin,
                `picture_thumbnail` varchar(255) COLLATE utf8_bin NOT NULL,
                `picture_content` varchar(255) COLLATE utf8_bin NOT NULL,
                `picture_landing` varchar(255) COLLATE utf8_bin NOT NULL,
                `urut` int(11) NOT NULL,
                `menu_path` varchar(255) COLLATE utf8_bin NOT NULL,
                `is_delete` tinyint(4) NOT NULL DEFAULT '0',
                `is_landing` tinyint(4) NOT NULL DEFAULT '0',
                `is_must_read` tinyint(4) NOT NULL DEFAULT '0',
                `created_by` int(11) NOT NULL DEFAULT '0',
                `edited_by` int(11) NOT NULL DEFAULT '0',
                `tags` varchar(255) COLLATE utf8_bin NOT NULL,
                `viewed` int(11) NOT NULL,
                `modify_date` datetime NOT NULL,
                `create_date` datetime NOT NULL,
                PRIMARY KEY (`id_user`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
            ";
        $this->db->query($create_tbl_user);
        
        $create_tbl_user_comments = "
            CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('users_comments')." (
                `id_user_comment` int(11) NOT NULL AUTO_INCREMENT,
                `id_user` int(11) NOT NULL,
                `id_user` int(11) NOT NULL,
                `id_ref_publish` int(11) NOT NULL,
                `ip_address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                `comment` text COLLATE utf8_bin NOT NULL,
                `is_delete` tinyint(4) NOT NULL DEFAULT '0',
                `create_date` datetime NOT NULL,
                PRIMARY KEY (`id_user_comment`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
            ";
        $this->db->query($create_tbl_user_comments);
        
        $create_tbl_user_galleries = "
            CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('users_galleries')." (
                `id_user_gallery` int(11) NOT NULL AUTO_INCREMENT,
                `id_user` int(11) NOT NULL,
                `file_gallery` varchar(255) COLLATE utf8_bin NOT NULL,
                `caption_gallery` text COLLATE utf8_bin NOT NULL,
                `create_date_gallery` datetime NOT NULL,
                PRIMARY KEY (`id_user_gallery`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
            ";
        $this->db->query($create_tbl_user_galleries);
        
        $create_tbl_user_sites = "
            CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('users_sites')."` (
              `id_user` int(11) NOT NULL,
              `id_site` int(11) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id_user`,`id_site`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
            ";
        $this->db->query($create_tbl_user_sites);
        
        $create_tbl_user_widget = "
            CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('users_widget')."` (
                `id_user_widget` int(11) NOT NULL AUTO_INCREMENT,
                `id_user` int(11) NOT NULL,
                `id_widget` int(11) NOT NULL,
                PRIMARY KEY (`id_user_widget`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;
            ";
        $this->db->query($create_tbl_user_widget);
        */
        // get module
        $this->db->where('module', 'user');
        $this->db->where('module_link', 'user');
        $this->db->limit(1);
        $this->db->order_by('id_module', 'desc');
        $query = $this->db->get('modules');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $module = $row['module'];
            $module_name = $row['module_title'];
        } else {
            $module = 'user';
            $module_name = 'User Management';
        }

        // get last urut
        $this->db->select('max(urut) as urut');
        $query2 = $this->db->get('menu_admin');
        $row2 = $query2->row_array();
        $last_urut = $row2['urut'] + 1;

        // add menu admin
        $this->db->insert('menu_admin',
            array('id_parents_menu_admin' => 0, 'urut' => $last_urut, 'menu' => $module_name, 'file' => $module));
        $id_menu_admin = $this->db->insert_id();

        // add auth
        $this->db->insert('auth_pages',
            array('id_auth_user_grup' => adm_sess_usergroupid(), 'id_menu_admin' => $id_menu_admin));

        //mkdir("./uploads/user", 0755);

        return true;
    }

    /**
     * this function is use for un-installation or delete module user
     * @return type boolean true
     */
    function uninstall()
    {
        /*
        // delete auth
        $this->db->where('file','user');
        $this->db->limit(1);
        $query = $this->db->get('menu_admin');
        if ($query->num_rows()>0) {
            $row = $query->row_array();
            
            // delete auth
            $this->db->where('id_menu_admin',$row['id_menu_admin']);
            $this->db->delete('auth_pages');
            
            // delete menu admin
            $this->db->where('id_menu_admin',$row['id_menu_admin']);
            $this->db->delete('menu_admin');
        }
        
        $this->db->query("DROP TABLE 
            `".$this->db->dbprefix('users_widget')."`,
            `".$this->db->dbprefix('users_sites')."`,
            `".$this->db->dbprefix('users_galleries')."`,
            `".$this->db->dbprefix('users_comments')."`,
            `".$this->db->dbprefix('users')."`
        ");
        */

        //rmdir("./uploads/user");

        return true;
    }





    /***************************************/
    /**
     * used for front end
     ******************************************/

    /**
     *
     * @param int $id_user
     * @return string $query
     */
    function getUserActiveById($id_user)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('activation_status', 1);
        $this->db->limit(1);
        $this->db->order_by('id_user', 'desc');
        $query = $this->db->get('users');
        return $query;
    }

    /**
     *
     * @param string $username
     * @param int $id
     * @return bool true/false
     */
    function CheckExistsUsername($username, $id = 0)
    {
        if ($id != '' && $id != 0) {
            $this->db->where('id_user !=', $id);
        }
        $this->db->where('username', $username);
        $this->db->where('blocked', 0);
        $this->db->where('activation_status', 1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param string $username
     * @param int $id
     * @return bool true/false
     */
    function CheckExistsEmail($email, $id = 0)
    {
        if ($id != '' && $id != 0) {
            $this->db->where('id_user !=', $id);
        }
        $this->db->where('email', $email);
        $this->db->where('blocked', 0);
        $this->db->where('activation_status', 1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param string $plurk
     * @param int $id
     * @return bool true/false
     */
    function CheckExistsPlurk($plurk, $id = null)
    {
        if ($id != '' && $id != 0) {
            $this->db->where('id_user !=', $id);
        }
        $this->db->where('plurk', $plurk);
        $this->db->where('blocked', 0);
        $this->db->where('activation_status', 1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param string $token
     * @return bool true/false
     */
    function check_user_token($token)
    {
        $this->db->where('token', $token);
        $query = $this->db->get('tokens');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param array $data
     * @return int $id_user
     */
    function RegisterUser($data)
    {
        $this->db->insert('users', $data);
        $id_user = $this->db->insert_id();
        return $id_user;
    }

    /**
     *
     * @param int $id_user
     */
    function SendEmailActivation($id_user)
    {
        $this->db->where('id_user', $id_user);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $this->load->library('email');

            $config['protocol'] = 'sendmail';
            $config['useragent'] = 'none';
            $config['mailpath'] = '/usr/sbin/sendmail';
            $config['charset'] = 'iso-8859-1';
            $config['mailtype'] = 'html';
            $config['wordwrap'] = true;

            $this->email->initialize($config);

            $id = $this->encrypt->encode($row['id_user']);
            $hash = $this->encrypt->encode($row['activation_code']);

            $body = 'Please click this link to activate your account.<br/>';
            $body .= '<a href="' . base_url('registration/activation') . '?r=' . myUrlEncode($hash) . '&u=' . myUrlEncode($id) . '">' . base_url('registration/activation') . '?r=' . urlencode($hash) . '&u=' . urlencode($id) . '</a>';
            $body .= '<br/>';
            $body .= 'Confirmation code : ' . $row['activation_code'] . '<br/><br/>';
            $body .= 'Please ignore this message if you did not recently create an account with GOGIRL! MAGAZINE.<br/><br/>';
            $body .= 'Regards,<br/><br/>GOGIRL! MAGAZINE\'';

            $this->email->from('no-reply@gogirlmagazine.com', 'GOGIRL! MAGAZINE');
            $this->email->to($row['email'], $row['name']);
            $this->email->bcc('mac_@gxrg.org');

            $this->email->subject('GOGIRL! MAGAZINE : Account Activation');
            $this->email->message($body);

            $this->email->send();
        }
    }

    function AddTokenToDB($token)
    {
        $this->db->insert('tokens', array('token' => $token));
    }

    function check_auth_login($username, $password)
    {
        //$this->db->select('id_user,username,name,email');
        $this->db->where('username', $username);
        $this->db->where('activation_status', 1);
        $this->db->limit(1);
        $this->db->order_by('id_user', 'desc');
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            //$pass = $this->encrypt->decode($row['password']);
            $pass = $row['password'];
            $password = encrypt_user_password($password);
            if ($pass == $password) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getUserPlurkById($id_user)
    {
        $return = '';
        $this->db->where('id_user', $id_user);
        $this->db->where('activation_status', 1);
        $this->db->order_by('id_user', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $return = strtolower($row['plurk']);
        }
        return $return;
    }

    /**
     * get user id by plurk
     * @param type $plurk
     * @return string $id_user / false
     */
    function getUserIdByPlurk($plurk)
    {
        $this->db->where('LCASE(plurk)', strtolower($plurk));
        $this->db->where('activation_status', 1);
        $this->db->order_by('id_user', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $this->load->library("encrypt");
            $row = $query->row_array();
            return $this->encrypt->encode($row['id_user']);
        } else {
            return false;
        }
    }

    /**
     * check if facebook id is registered
     * @param type $fb_id
     */
    function CheckFacebookRegistered($fb_id)
    {
        $this->db->where('fb_id', $fb_id);
        $this->db->where('activation_status', 1);
        $this->db->order_by('id_user', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $this->load->library("session");
            $this->load->library("encrypt");

            $session_M = array(
                'M_NAME' => $row['name'],
                'M_UNAME' => $row['username'],
                'M_ID' => $this->encrypt->encode($row['id_user']),
                'M_EMAIL' => $row['email'],
                'M_TOKEN' => $row['token'],
            );

            $this->session->set_userdata('M_SESS', $session_M);

            # update last login date
            $last_login = time();
            $last_login = date('Y-m-d H:i:s');

            # insert to log
            $data_log = array(
                'id_user' => 0,
                'id_group' => 0,
                'action' => 'Member Login With Facebook',
                'desc' => 'Login:succeed; IP:' . $_SERVER['SERVER_ADDR'] . '; ID:' . $row['id_user'] . ' ; FB ID : ' . $fb_id . '; Username:' . $row['username'] . '; Last Login:' . $user['last_login'] . '',
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_log);

            # update last login member
            $this->UpdateUser($row['id_user'], array('last_login' => $last_login));

            redirect('user/index/' . $row['plurk']);
        }
    }

    /**
     * check if twitter id is registered
     * @param type $fb $tw_id_id
     */
    function CheckTwitterRegistered($tw_id)
    {
        $this->db->where('tw_id', $tw_id);
        $this->db->where('activation_status', 1);
        $this->db->order_by('id_user', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $this->load->library("session");
            $this->load->library("encrypt");

            $session_M = array(
                'M_NAME' => $row['name'],
                'M_UNAME' => $row['username'],
                'M_ID' => $this->encrypt->encode($row['id_user']),
                'M_EMAIL' => $row['email'],
                'M_TOKEN' => $row['token'],
            );

            $this->session->set_userdata('M_SESS', $session_M);

            # update last login date
            $last_login = time();
            $last_login = date('Y-m-d H:i:s');

            # insert to log
            $data_log = array(
                'id_user' => 0,
                'id_group' => 0,
                'action' => 'Member Login With Twitter',
                'desc' => 'Login:succeed; IP:' . $_SERVER['SERVER_ADDR'] . '; ID:' . $row['id_user'] . ' ; Twitter ID : ' . $tw_id . '; Username:' . $row['username'] . '; Last Login:' . $user['last_login'] . '',
                'create_date' => date('Y-m-d H:i:s'),
            );
            insert_to_log($data_log);

            # update last login member
            $this->UpdateUser($row['id_user'], array('last_login' => $last_login));

            redirect('user/index/' . $row['plurk']);
        }
    }

    function AddViewCount($id_user)
    {
        $this->db->query("UPDATE " . $this->db->dbprefix('users') . " SET view_count = view_count + 1 WHERE id_user ='" . $id_user . "' ");
    }

    function count_user_msg($id_user)
    {
        $this->db->select('count(*) as total');
        $this->db->where('id_user_to', $id_user);
        $this->db->where('is_delete', 0);
        $query = $this->db->get('users_messages');
        $row = $query->row_array();
        return $row['total'];
    }

    function getUserGalleries($id_user)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('is_delete', 0);
        $this->db->where('id_ref_publish', 1);
        $this->db->order_by('create_date', 'desc');
        $query = $this->db->get('fashion_tags');
        return $query;
    }

    function getUserStore($id_user)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('is_delete', 0);
        $this->db->where('id_ref_publish', 1);
        $this->db->order_by('created_at', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('stores');
        return $query;
    }

    function check_user_has_store($id_user)
    {
        $this->db->where('id_user', $id_user);
        $this->db->order_by('id_store', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('stores');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['id_store'];
            //return true;
        } else {
            return false;
        }
    }

    function CheckExistsStoreUri($store_uri, $id = 0)
    {
        if ($id) {
            $this->db->where('id_store !=', $id);
        }
        $this->db->where('LCASE(uri)', strtolower($store_uri));
        $query = $this->db->get('stores');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param string $email
     * @param int $id
     * @return bool true/false
     */
    function CheckExistsStoreEmail($email, $id = 0)
    {
        if ($id) {
            $this->db->where('id_store !=', $id);
        }
        $this->db->where('email', $email);
        $query = $this->db->get('stores');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param int $id_user
     * @param int $limit
     * @param int $perpage
     * @return string $query
     */
    function getUserInbox($id_user, $limit = 0, $perpage = 0)
    {
        $this->db->select('users_messages.*,users.name');
        $this->db->where('users_messages.id_user_to', $id_user);
        $this->db->where('users_messages.is_delete', 0);
        $this->db->join('users', 'users.id_user=users_messages.id_user_from', 'left');
        $this->db->order_by('users_messages.create_date', 'desc');
        if ($perpage > 0) {
            $this->db->limit($perpage, $limit);
        }
        $query = $this->db->get('users_messages');
        return $query;
    }

    /**
     *
     * @param int $id_inbox
     * @return string $query
     */
    function getUserInboxDetail($id_inbox)
    {
        $this->db->select('users_messages.*,users.name,users.display_name,users.username,users.image,users.thumbnail,users.plurk');
        $this->db->where('users_messages.id_user_message', $id_inbox);
        $this->db->where('users_messages.is_delete', 0);
        $this->db->join('users', 'users.id_user=users_messages.id_user_from', 'left');
        $this->db->order_by('users_messages.create_date', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('users_messages');
        return $query;
    }

    /**
     *
     * @param int $id_inbox
     */
    function setInboxMsgRead($id_inbox)
    {
        $query = $this->getUserInboxDetail($id_inbox);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $date_now = date('Y-m-d H:i:s');
            if ($row['read_date'] != '') {
                $date_now = $row['read_date'];
            }

            $this->db->where('id_user_message', $id_inbox);
            $this->db->update('users_messages', array('is_status' => 1, 'read_date' => $date_now));
        }
    }

    /**
     *
     * @param array $data
     * @return int last insert id
     */
    function InsertUserMessage($data)
    {
        $this->db->insert('users_messages', $data);
        $last_id = $this->db->insert_id();
        return $last_id;
    }


}

/* End of file user_model.php */
/* Location: ./application/model/user_model.php */


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
class Cars_model extends CI_Model
{
    /**
     * count total cars
     * @param mixed $msc_code search by car name
     * @param mixed $s_brand search by car brand
     * @param mixed $s_type search by car type
     * @param mixed $s_series search by car series
     * @return int count total rows
     */
    function TotalCars($msc_code = null, $s_brand = null, $s_type = null, $s_series = null, $s_model = null)
    {
        $this->db->select('count(*) as total');
        if ($msc_code != null) {
            $this->db->where("LCASE(msc_code) LIKE '%" . utf8_strtolower($msc_code) . "%'");
        }
        if ($s_brand != null) {
            $this->db->where("LCASE(brands) LIKE '%" . utf8_strtolower($s_brand) . "%'");
        }
        if ($s_type != null) {
            $this->db->where("LCASE(types) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        if ($s_series != null) {
            $this->db->where("LCASE(series) LIKE '%" . utf8_strtolower($s_series) . "%'");
        }
        if ($s_model != null) {
            $this->db->where("LCASE(model) LIKE '%" . utf8_strtolower($s_model) . "%'");
        }

        $query = $this->db->get('view_cars');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total car brands
     * @param mixed $s_brand search by car brands
     * @return int count total rows
     */
    function TotalCarsBrand($s_brand = null)
    {
        $this->db->select('count(*) as total');
        // if ($s_car != null) $this->db->where("LCASE(cars) LIKE '%".utf8_strtolower($s_car) . "%'");
        if ($s_brand != null) {
            $this->db->where("LCASE(brands) LIKE '%" . utf8_strtolower($s_brand) . "%'");
        }
        // if ($s_type != null) $this->db->where("LCASE(types) LIKE '%".utf8_strtolower($s_type) . "%'");
        // if ($s_series != null) $this->db->where("LCASE(series) LIKE '%".utf8_strtolower($s_series) . "%'");

        $query = $this->db->get('car_brands');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total car types
     * @param mixed $s_type search by car type
     * @param mixed $s_brand search by car brand
     * @return int count total rows
     */
    function TotalCarsType($s_brand = null, $s_type = null)
    {
        $this->db->select('count(*) as total');
        if ($s_brand != null) {
            $this->db->where("LCASE(id_brands) LIKE '%" . utf8_strtolower($s_brand) . "%'");
        }
        if ($s_type != null) {
            $this->db->where("LCASE(types) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        $query = $this->db->get('car_types');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * count total car series
     * @param mixed $s_brand search by car brands
     * @param mixed $s_type search by car type
     * @param mixed $s_series search by car series
     * @return int count total rows
     */
    function TotalCarsSeries(/* $s_brand=null, */
        $s_type = null,
        $s_series = null
    ) {
        $this->db->select('count(*) as total');
        // if ($s_brand != null) $this->db->where("LCASE(brands) LIKE '%".utf8_strtolower($s_brand) . "%'");
        if ($s_type != null) {
            $this->db->where("LCASE(types) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        if ($s_series != null) {
            $this->db->where("LCASE(series) LIKE '%" . utf8_strtolower($s_series) . "%'");
        }
        $query = $this->db->get('view_cars_series');
        echo $this->db->_error_message();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['total'];
        } else {
            return '0';
        }
    }

    /**
     * retrieve all cars
     * @param type $search1
     * @param type $search2
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCars(
        $msc_code = null,
        $s_brand = null,
        $s_type = null,
        $s_series = null,
        $search4 = null,
        $search3 = 0,
        $limit = 0,
        $per_pg = 0
    ) {
        if ($msc_code != null) {
            $this->db->where("LCASE(msc_code) LIKE '%" . utf8_strtolower($msc_code) . "%'");
        }
        if ($s_brand != null) {
            $this->db->where("LCASE(brands) LIKE '%" . utf8_strtolower($s_brand) . "%'");
        }
        if ($s_type != null) {
            $this->db->where("LCASE(types) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        if ($s_series != null) {
            $this->db->where("LCASE(series) LIKE '%" . utf8_strtolower($s_series) . "%'");
        }
        if ($search4 != null) {
            $this->db->where("LCASE(model) LIKE '%" . utf8_strtolower($search4) . "%'");
        }
        $this->db->limit($per_pg, $limit)
            ->order_by('msc_code asc,brands asc,types asc, series asc');

        $query = $this->db->get('view_cars');
        return $query;
    }

    /**
     * retrieve all car brands
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCarsBrand($s_brand = null, $limit = 0, $per_pg = 0)
    {
        if ($s_brand != null) {
            $this->db->where("LCASE(brands) LIKE '%" . utf8_strtolower($s_brand) . "%'");
        }
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_brands');
        return $query;
    }

    /**
     * retrieve all car brands
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCarsType($s_brand = null, $s_type = null, $limit = 0, $per_pg = 0)
    {
        if ($s_brand != null) {
            $this->db->where("LCASE(id_brands) LIKE '%" . utf8_strtolower($s_brand) . "%'");
        }
        if ($s_type != null) {
            $this->db->where("LCASE(types) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('car_types');
        return $query;
    }

    /**
     * retrieve all car series
     * @param type $search1
     * @param type $limit
     * @param type $per_pg
     * @return type string $query
     */
    function GetAllCarsSeries($s_series = null, $s_type = null, $limit = 0, $per_pg = 0)
    {
        if ($s_type != null) {
            $this->db->where("LCASE(types) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        if ($s_series != null) {
            $this->db->where("LCASE(series) LIKE '%" . utf8_strtolower($s_type) . "%'");
        }
        $this->db->limit($per_pg, $limit);
        $query = $this->db->get('view_cars_series');
        return $query;
    }

    /**
     * get car by car id
     * @param type $Id
     * @return object $query
     */
    function GetCarById($Id)
    {
        $this->db->where('id_cars', $Id);
        $this->db->order_by('create_date', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('car');
        return $query;
    }

    /**
     * get car brand by car brand id
     * @param type $Id
     * @return object $query
     */
    function GetCarBrandById($Id)
    {
        $this->db->where('id_brands', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('car_brands');
        return $query;
    }

    /**
     * get car type by car type id
     * @param type $Id
     * @return object $query
     */
    function GetCarTypeById($Id)
    {
        $this->db->where('id_car_types', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('car_types');
        return $query;
    }

    /**
     * get car series by car series id
     * @param type $Id
     * @return object $query
     */
    function GetCarSeriesById($Id)
    {
        $this->db->where('id', $Id);
        // $this->db->order_by('create_date','desc');
        $this->db->limit(1);
        $query = $this->db->get('view_cars_series');
        return $query;
    }

    /**
     * change cars publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublish($Id)
    {
        $this->db->where('id_cars', $Id);
        // $this->db->where('is_delete',0);
        $query = $this->db->get('car');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['car_ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_cars', $row['id_cars']);
            $this->db->update('car', array('car_ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * change cars brands publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangePublishBrand($Id)
    {
        $this->db->where('id_brands', $Id);
        $query = $this->db->get('car_brands');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_brands', $row['id_brands']);
            $this->db->update('car_brands', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * change cars type publish status
     * @param type $Id
     * @return string publish status
     */
    function ChangePublishType($Id)
    {
        $this->db->where('id_car_types', $Id);
        $query = $this->db->get('car_types');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_car_types', $row['id_car_types']);
            $this->db->update('car_types', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * change cars series publish status
     * @param series $Id
     * @return string publish status
     */
    function ChangePublishSeries($Id)
    {
        $this->db->where('id_car_series', $Id);
        $query = $this->db->get('car_series');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if ($row['ref_publish'] == 1) {
                $val = 0;
            } else {
                $val = 1;
            }

            $this->db->where('id_car_series', $row['id_car_series']);
            $this->db->update('car_series', array('ref_publish' => $val));

            if ($val == 1) {
                return 'Publish';
            } else {
                return 'Not Publish';
            }
        }
    }

    /**
     * check existing Car name
     * @param string $carname
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarName($carname, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_cars != ', $Id);
        }
        $this->db->where('msc_code', $carname);
        $query = $this->db->get('car');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Car Brand
     * @param string $carbrand
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarBrand($carbrand, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_brands != ', $Id);
        }
        $this->db->where('brands', $carbrand);
        $query = $this->db->get('car_brands');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Car Type
     * @param string $cartype
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarType($cartype, $brand, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_car_types != ', $Id);
        }
        $this->db->where('types', $cartype);
        $this->db->where('id_brands', $brand);
        $query = $this->db->get('car_types');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check existing Car Series
     * @param string $carseries
     * @param int $Id
     * @return boolean (true or false)
     */
    function CheckExistsCarSeries($cartype, $carseries, $Id = 0)
    {
        if ($Id > 0) {
            $this->db->where('id_car_series != ', $Id);
        }
        $where = array('id_type' => $cartype, 'series' => $carseries);
        // $this->db->where('series',$carseries);
        $this->db->where($where);
        $query = $this->db->get('car_series');
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * insert Car
     * @param array $data
     * @return int $id_car last inserted
     */
    function InsertCar($data)
    {
        $this->db->insert('car', $data);
        $id_car = $this->db->insert_id();
        return $id_car;
    }

    /**
     * insert Car Brand
     * @param array $data
     * @return int $id_car last inserted
     */
    function InsertCarBrand($data)
    {
        $this->db->insert('car_brands', $data);
        $id_car = $this->db->insert_id();
        return $id_car;
    }

    /**
     * insert Car Type
     * @param array $data
     * @return int $id_car last inserted
     */
    function InsertCarType($data)
    {
        $this->db->insert('car_types', $data);
        $id_car = $this->db->insert_id();
        return $id_car;
    }

    /**
     * insert Car Series
     * @param array $data
     * @return int $id_car last inserted
     */
    function InsertCarSeries($data)
    {
        $this->db->insert('car_series', $data);
        $id_car = $this->db->insert_id();
        return $id_car;
    }

    /**
     * update Car
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCar($data, $Id)
    {
        $this->db->where('id_cars', $Id);
        $this->db->update('car', $data);
    }

    /**
     * update Car Brand
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarBrand($data, $Id)
    {
        $this->db->where('id_brands', $Id);
        $this->db->update('car_brands', $data);
    }

    /**
     * update Car Type
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarType($data, $Id)
    {
        $this->db->where('id_car_types', $Id);
        $this->db->update('car_types', $data);
    }

    /**
     * update Car Series
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarSeries($data, $Id)
    {
        $this->db->where('id_car_series', $Id);
        $this->db->update('car_series', $data);
    }

    /**
     * update Car Color
     * @param int $Id
     * @param array $data
     * @return Void
     */
    function UpdateCarColor($data, $Id)
    {
        $this->db->where('id_car_colors', $Id);
        $this->db->update('car_colors', $data);
    }

    /**
     * delete car by id car
     * @param car $Id
     */
    function DeleteCar($Id)
    {
        foreach ($Id as $ida) {
            $this->DeleteCarThumbByID($ida);
        }
        $this->db->where_in('id_cars', $Id);
        $this->db->delete('car');
    }

    /**
     * delete car brand by id car brand
     * @param brand $Id
     */
    function DeleteCarBrand($Id)
    {
        $this->db->where_in('id_brands', $Id);
        $this->db->delete('car_brands');
    }

    /**
     * delete car type by id car type
     * @param type $Id
     */
    function DeleteCarType($Id)
    {
        $this->db->where_in('id_car_types', $Id);
        $this->db->delete('car_types');
    }

    /**
     * delete car series by id car series
     * @param series $Id
     */
    function DeleteCarSeries($Id)
    {
        $this->db->where_in('id_car_series', $Id);
        $this->db->delete('car_series');

    }

    /**
     * Delete Thumb By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteCarThumbByID($Id)
    {
        $data = $this->db->get_where('car', array('id_cars' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/cars/' . $data['car_thumb'];
            if (file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id_cars', $Id)->update('car', array('car_thumb' => ''));
        }
    }

    /**
     * Delete Color By Id
     * @param int $id
     * @return int $id last inserted id
     */
    function DeleteCarColorByID($Id)
    {
        $data = $this->db->get_where('car_colors', array('id_car_colors' => $Id));
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $file = './uploads/cars/colors/' . $data['car_color_thumb'];
            $xfile = substr($file, 0, -4);

            if (file_exists($file)) {
                unlink($file);
                @unlink($xfile . '_HDPI.png');
                @unlink($xfile . '_XHDPI.png');
            }
            $this->db->where('id_car_colors', $Id)->delete('car_colors');
        }
    }


}
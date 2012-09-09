<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Images Module
 *
 * @author 		Eko Muhammad Isa
 * @website		http://www.enotes.web.id
 * @package 	PyroCMS
 * @subpackage 	Images Module
 */
class Images_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'simpleshop_images';
    }

    public function get_all() {
        $this->db
                ->select('i.*')
                ->from('simpleshop_images as i')
                ->order_by('filename', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get($id) {
        $this->db
                ->select('i.*')
                ->from('simpleshop_images as i')
                ->where('i.id', $id);

        $query = $this->db->get();
        $product = $query->row();

        return $product;
    }

    public function get_by_product($id) {
        $this->db
                ->select('i.*')
                ->from('simpleshop_images as i')
                ->where('i.product_id', $id)
                ->order_by('filename', 'asc');

        $query = $this->db->get();
        $products = $query->result();

        return $products;
    }

    public function save_image($prm = array()) {

        $result = $this->db->insert($this->_table, $prm);

        if($result){
			$result = $this->db->insert_id();
		}
        return $result;
    }

    public function set_default_image($id = 0) {

		if(empty($id)){
			return false;
		}
		
		$row = $this->get($id);
		if($row){
			$this->db->where('product_id', $row->product_id);
			$this->db->update($this->_table, array('isdefault'=>0));

			$this->db->where('id', $id);
			$result = $this->db->update($this->_table, array('isdefault'=>1));

			return $result;
		}else{
			return false;
		}
    }


}

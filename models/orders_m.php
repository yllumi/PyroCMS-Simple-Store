<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Specials Module
 *
 * @author 		Patrick Kivits - Woodits Webbureau
 * @website		http://woodits.nl
 * @package 	PyroCMS
 * @subpackage 	Specials Module
 */
class Orders_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = 'simpleshop_orders';
    }

    public function get_all() {
        $this->db->from('simpleshop_orders as o')
                ->join('simpleshop_customers as c', 'o.customer_id = c.id')
                ->join('simpleshop_order_status as s', 'o.status = s.id')
                ->order_by('s.id', 'asc')
                ->order_by('o.ordered_on', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    public function get($id) {
        $this->db->from('simpleshop_orders as o')
                ->join('simpleshop_customers as c', 'o.customer_id = c.id')
                ->join('simpleshop_order_status as s', 'o.status = s.id')
                ->where('o.id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_order_products($order_id){
        $this->db->from('simpleshop_order_items as i')
                ->join('simpleshop_products as p', 'i.product_id = p.id')
                ->where('i.order_id', $order_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function get_order_status() {
        return $this->db->get('simpleshop_order_status')->result();
    }

}

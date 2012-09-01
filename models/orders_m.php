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
    
    public function get_for_cart($slug) {
        $this->db->from('simpleshop_products')->where('slug', $slug);

        $query = $this->db->get();
        $product = $query->row_array();

        $this->db->from('simpleshop_products_x_fields as x')
                ->join('simpleshop_fields as f', 'f.id = x.field', 'left')
                ->where('x.product', $product['id']);

        $query = $this->db->get();
        $fields = $query->result_array();

        foreach ($fields as $field) {
            // Put the custom fields in there own array
            $custom_field[$field['slug']] = $field['value'];

            // And also in the main array for easy refference
            if (!isset($product[$field['slug']])) {
                // Only if the array not already contains the key
                $product[$field['slug']] = $field['value'];
            }
        }
        if (isset($custom_field)) {
            $product['custom_fields'] = $custom_field;
        }

        return $product;
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
    
    function save_customer_info($data) {
        $this->db->insert('simpleshop_customers', $data);
        return $this->db->insert_id();
    }

    function save_order($total, $orders_data, $customer) {
        $order = array(
            'customer_id' => $customer['customer_id'],
            'ordered_on' => date('Y-m-d H:i:s'),
            'shipped_on' => date('Y-m-d H:i:s'),
            'subtotal' => $total,
            'shipping' => 0,
            'tax' => 0,
            'total' => $total,
            'status' => 1,
            'order_code' => random_string('numeric', 8)
        );
        $this->db->insert('simpleshop_orders', $order);
        $order_id = $this->db->insert_id();
        
        foreach ($orders_data as $order_data) {
            $order_items = array(
                'order_id' => $order_id,
                'product_id' => $order_data['id'],
                'quantity' => $order_data['qty'],
                'price' => $order_data['price'],
                'total' => $order_data['subtotal']
            );
            $this->db->insert('simpleshop_order_items', $order_items);
        };
        return $order['order_code'];
    }

}

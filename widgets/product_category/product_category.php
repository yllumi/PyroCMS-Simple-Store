<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package 		PyroCMS
 * @subpackage 		Cart Widget
 * @author		Toni Haryanto
 *
 * Display a shopping cart
 *
 */
class Widget_Product_Category extends Widgets {

    public $title = array(
        'en' => 'Product Category',
        'id' => 'Kategori Produk'
    );
    public $description = array(
        'en' => 'Display a product category.',
        'id' => 'Menampilkan daftar kategori produk.'
    );
    public $author = 'Toni Haryanto';
    public $website = 'http://toniharyanto.com';
    public $version = '1.0';

    public function form($options) {
		return true;
    }

    public function run($options) {
        $this->load->model('products/categories_m');
        return array('categories' => $this->categories_m->order_by('name', 'asc')->get_all());
    }

}

<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Products Module
 *
 * @author 		Patrick Kivits - Woodits Webbureau
 * @website		http://woodits.nl
 * @package 	PyroCMS
 * @subpackage 	Products Module
 */
class Products extends Public_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('cart');

        $this->load->model('products_m');
        $this->load->model('custom_fields_m');
        $this->lang->load('general');
        $this->lang->load('products');

        $this->template->set('currency', $this->settings->currency);
    }

    public function index($offset = 0) {
        $limit = $this->settings->item_per_page;

        $data->items = $this->products_m->limit($limit)
                ->offset($offset)
                ->get_all();

        if (count($data->items)) {
            $data->items_exist = TRUE;
        } else {
            $data->items_exist = FALSE;
        }
        
        // Params: (module/method, total count, limit, uri segment)
        $data->pagination = create_pagination('products/index', $this->products_m->count_all(), $limit, 3);

        $this->template->title($this->module_details['name'], lang('products:label'))
				->append_css('module::public.css')
                ->build('index', $data);
    }

    public function product($slug = null) {

        if ($slug)
            $data->items = $this->products_m->get_by_slug($slug);
        else
            redirect('products');

        if (count($data->items)) {
            $data->items_exist = TRUE;
            print_r($data->items);
            $this->load->model('images_m');
			$data->items_images = $this->images_m->get_by_product($data->items->id);
        } else {
            $data->items_exist = FALSE;
            $data->items_images = false;
        }

		
//		echo "<pre>";
//		print_r($data);
//		die();

        $data->fields = $this->custom_fields_m->get_all();

        $this->template->title($this->module_details['name'], '')
				->append_css('module::public.css')
                ->build('product', $data);
    }

    public function category() {
        $data->items = $this->products_m->get_by_category($this->uri->segment(3));

        if (count($data->items)) {
            $data->items_exist = TRUE;
        } else {
            $data->items_exist = FALSE;
        }

        $this->template->title($this->module_details['name'], lang('products:category'))
                ->build('category', $data);
    }

}

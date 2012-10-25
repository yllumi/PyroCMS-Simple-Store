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
class Admin extends Admin_Controller {

    protected $section = 'products';

    public function __construct() {
        parent::__construct();

        $this->load->model('products_m');
        $this->load->library('form_validation');
        $this->lang->load('general');
        $this->lang->load('products');
        $this->load->library('files/files');
        $this->load->model('images_m');

        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => lang('products:name'),
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'slug',
                'label' => lang('products:slug'),
                'rules' => 'trim|max_length[80]|required'
            ),
            array(
                'field' => 'category',
                'label' => lang('products:category'),
                'rules' => 'trim|max_length[100]'
            ),
            array(
                'field' => 'price',
                'label' => lang('products:price'),
                'rules' => 'trim|max_length[12]'
            ),
            array(
                'field' => 'description',
                'label' => lang('products:description'),
                'rules' => 'trim'
            )
        );

        $this->template
                ->append_js('module::jquery.cookie.js')
                ->append_js('module::admin.js')
                ->append_css('module::admin.css');
    }

    public function index() {
        $items = $this->products_m->get_all();

        $this->template
                ->title($this->module_details['name'])
                ->set('items', $items)
                ->build('admin/' . $this->module . '/items');
    }

    public function create() {
        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {
			$rslt = $this->products_m->create($this->input->post());
            if ($rslt) {
                $this->session->set_flashdata('success', lang('general:success'));
                redirect('admin/' . $this->module . '/edit/' . $rslt);
            } else {
                $this->session->set_flashdata('error', lang('general:error'));
                redirect('admin/' . $this->module . '/create');
            }
        }

        foreach ($this->item_validation_rules AS $rule) {
            $products->{$rule['field']} = $this->input->post($rule['field']);
        }

        $categories = $this->products_m->get_categories();
        $fields = $this->products_m->get_fields();

        $this->template
                ->title($this->module_details['name'], lang('products:create'))
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_js('module::form.js')
                ->append_js('module::jquery.form.js')
                ->append_js('module::image.js')
                ->set('products', $products)
                ->set('fields', $fields)
                ->set('categories', $categories)
                ->build('admin/' . $this->module . '/form');
    }

    public function edit($id = 0) {
        $id = $this->uri->segment(4);
        $id or redirect('admin/' . $this->module);

        $products = $this->products_m->get($id);

        $this->form_validation->set_rules($this->item_validation_rules);

        if ($this->form_validation->run()) {
            unset($_POST['btnAction']);

            if ($this->products_m->update($id, $this->input->post())) {
                $this->session->set_flashdata('success', lang('general:success'));
                redirect('admin/' . $this->module);
            } else {
                $this->session->set_flashdata('error', lang('general:error'));
                redirect('admin/' . $this->module . '/create');
            }
        }

        $categories = $this->products_m->get_categories();
        $fields = $this->products_m->get_fields();
        
        $images = $this->images_m->get_by_product($id);

        $this->template->title($this->module_details['name'], lang('products:edit'))
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_js('module::form.js')
                ->append_js('module::jquery.form.js')
                ->append_js('module::image.js')
                ->set('products', $products)
                ->set('fields', $fields)
                ->set('images', $images)
                ->set('categories', $categories)
                ->build('admin/' . $this->module . '/form');
    }

    public function delete($id = 0) {
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) {
            foreach ($this->input->post('action_to') as $key => $id) {
                $products = $this->products_m->get($id);

                if ($products->thumbnail) {
                    unlink(UPLOAD_PATH . $this->module . '/' . $products->thumbnail);
                }
                if ($products->image) {
                    unlink(UPLOAD_PATH . $this->module . '/' . $products->image);
                }
            }

            $this->products_m->delete_many($this->input->post('action_to'));
        } elseif (is_numeric($id)) {
            $products = $this->products_m->get($id);

            if ($products->thumbnail) {
                unlink(UPLOAD_PATH . $this->module . '/' . $products->thumbnail);
            }
            if ($products->image) {
                unlink(UPLOAD_PATH . $this->module . '/' . $products->image);
            }

            $this->products_m->delete($id);
        }
        redirect('admin/' . $this->module);
    }

    public function ajax_upload_image($id = '') {

		if(empty($id)){
			echo json_encode(array('status' => 0));
		}
		
    	// Get product
		$row    = $this->products_m->get_simple($id);
		$folder = $this->images_m->get_file_folder_by_slug($row->slug);
		$allow  = array('jpeg', 'jpg', 'png', 'gif', 'bmp');

		// Create folder?
		if( !$folder )
		{
			$parent = $this->images_m->get_file_folder_by_slug('product-images');
			$folder = $this->images_m->create_file_folder($parent->id, $row->name, $row->slug);
			$folder = (object)$folder;
		}

		// Check for folder
		if( is_object($folder) AND ! empty($folder) )
		{
			// Upload it
			$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';
			$thefile = Files::upload($folder->id);
		}

		if ($thefile['status']) {

			$json_data = array(
				'status' => 1,
				'raw_name' => $thefile['data']['name'],
				'real_name' => $thefile['data']['filename'],
				'thumbnail' => site_url("files/thumb/".$thefile['data']['filename']."/0/120"),
				'image' => $thefile['data']['path'],
				'ext' => $thefile['data']['extension'],
				'products_image' => lang("products:image"),
				'delete_image' => lang("products:delete_x"),
				'delete_title' => lang("products:delete_image"),
				'set_default' => lang("products:set_default"),
				'current_default' => lang("products:current_default"),
				'image_dt' => $thefile['data']['id']
			);
			
		} else {
			$json_data = array(
				'status' => 0
			);
		}

		echo json_encode($json_data);
    }

    public function ajax_delete_image($id) {
		
		if(empty($id)){
			echo json_encode(array('status'=>0));
			return;
		}

		// unset this image as default if it is
		$this->images_m->unset_if_default($id);

		// delete image
		$row = Files::delete_file($id);

        echo json_encode($row);
    }

    public function ajax_images($id = 0) {
		if(empty($id)){
			echo json_encode(array('status'=>0));
			return;
		}

		// Get product
		$product= $this->products_m->get_simple($id);
		$folder = $this->images_m->get_file_folder_by_slug($product->slug);
		$rslt = Files::folder_contents($folder->id);

		//print_r($rslt);

		if($rslt){
			$newrslt = array();
			foreach($rslt['data']['file'] as $row){
				$newrslt[] = array(
					'image_dt' => $row->id,
					'image_pdt' => $id,
					'image_file' => site_url("files/large/".$row->filename),
					'image_thumbnail' => site_url("files/thumb/".$row->filename."/0/120"),
					'image_default' => ($product->default_image_id == $row->id)? 1 : 0,
					'products_image' => lang("products:image"),
					'delete_image' => lang("products:delete_x"),
					'delete_title' => lang("products:delete_image"),
					'set_default' => lang("products:set_default"),
					'current_default' => lang("products:current_default")
				);
			}
			$result = array('status'=>1, 'result'=>$newrslt);
		}else{
			$result = array('status'=>0, 'result'=>'');
		}
		echo json_encode($result);
    }

    public function ajax_set_default($id = '') {
		if(empty($id)){
			echo json_encode(array('status'=>0));
			return;
		}

		$rslt = $this->images_m->set_default_image($id);
		if($rslt){
			$result = array('status'=>1);
		}else{
			$result = array('status'=>0);
		}
		echo json_encode($result);
    }

}

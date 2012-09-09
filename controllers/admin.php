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
            ),
            array(
                'field' => 'thumbnail',
                'label' => lang('products:thumbnail'),
                'rules' => 'trim'
            ),
            array(
                'field' => 'image',
                'label' => lang('products:image'),
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
        $this->load->model('images_m');
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
		
		$this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';
		$this->load->library('upload');

		$upload_path = UPLOAD_PATH . $this->module . '/';

		if (!is_dir($upload_path)) {
			@mkdir($upload_path, 0777, TRUE);
		}

		if (is_dir($upload_path))
			$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'gif|jpg|jpeg|png|GIF|JPG|JPEG|PNG';
		$config['remove_spaces'] = TRUE;
		$config['overwrite'] = TRUE;
		$config['max_size'] = 0;
		$config['encrypt_name'] = TRUE;
		$uploadedname = $_FILES['file']['name'];
		$config['file_name'] = 'P'.date("YmdHis").rand(10,99).'_'. substr($uploadedname, 0, strrpos($uploadedname, '.'));

		$this->upload->initialize($config);

		if ($this->upload->do_upload('file')) {

			$upload_data = $this->upload->data();

			$this->load->library('image_lib');

			unset($config);
			$config['source_image'] = $upload_path . $upload_data['file_name'];
			$config['maintain_ratio'] = TRUE;
			$config['quality'] = '100%';
			$config['width'] = $this->settings->thumbnail_width;
			$config['height'] = $this->settings->thumbnail_height;
			$config['new_image'] = $upload_path . $upload_data['raw_name'] . '_thumb' . $upload_data['file_ext'];

			if ($upload_data['image_width'] > $upload_data['image_height']) {
				$config['master_dim'] = 'width';
			} elseif ($upload_data['image_height'] > $upload_data['image_width']) {
				$config['master_dim'] = 'height';
			} else {
				$config['maintain_ratio'] = TRUE;
			}

			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();

			unset($config);
			$config['image_library'] = 'gd2';

			$image_size = getimagesize($upload_path . $upload_data['raw_name'] . '_thumb' . $upload_data['file_ext']);
			$config['source_image'] = $upload_path . $upload_data['raw_name'] . '_thumb' . $upload_data['file_ext'];

			$config['x_axis'] = (($image_size[0] - $this->settings->thumbnail_width) / 2);
			$config['y_axis'] = (($image_size[1] - $this->settings->thumbnail_height) / 2);
			$config['width'] = $this->settings->thumbnail_width;
			$config['height'] = $this->settings->thumbnail_height;
			$config['quality'] = '100%';
			$config['maintain_ratio'] = FALSE;

			$this->image_lib->initialize($config);
			$this->image_lib->crop();
			$this->image_lib->clear();

			$this->load->model("images_m");
			
			$rslt = $this->images_m->save_image(array(
				'product_id'=>$id,
				'filename'=>$upload_data['raw_name'].$upload_data['file_ext'],
				'thumbnailname'=>$upload_data['raw_name'] . '_thumb' . $upload_data['file_ext'],
				'uploadedname' => $uploadedname,
				'isdefault'=>0
				));
			
			if($rslt){
				$json_data = array(
					'status' => 1,
					'raw_name' => $upload_data['raw_name'],
					'real_name' => $uploadedname,
					'thumbnail' => $upload_data['raw_name'] . '_thumb' . $upload_data['file_ext'],
					'image' => $upload_data['raw_name'] . $upload_data['file_ext'],
					'ext' => $upload_data['file_ext'],
					'upload_path' => base_url() . 'uploads/' . SITE_REF . '/' . $this->module . '/',
					'products_image' => lang("products:image"),
					'delete_image' => lang("products:delete_x"),
					'delete_title' => lang("products:delete_image"),
					'set_default' => lang("products:set_default"),
					'current_default' => lang("products:current_default"),
					'image_dt' => $rslt,
					'thumb_width' => $this->settings->thumbnail_width
				);
			}else{
				$json_data = array(
					'status' => 0
				);
			}

			echo json_encode($json_data);
		}
    }

    public function ajax_delete_image($id) {
		
		if(empty($id)){
			echo json_encode(array('status'=>0));
			return;
		}
        if ($this->input->is_ajax_request()) {
			
			$this->load->model("images_m");
			$row = $this->images_m->get($id);
			if($row){
				
				$rslt = $this->images_m->delete_by(array('id' => $id));
				if($rslt){
					@unlink(UPLOAD_PATH . $this->module . '/' . $row->filename);
					@unlink(UPLOAD_PATH . $this->module . '/' . $row->thumbnailname);
					$result = array('status'=>1);
				}else{
					$result = array('status'=>0);
				}
					
			}else{
				$result = array('status'=>0);
			}
			
            echo json_encode($result);
        } else echo json_encode(array('status'=>0));
    }

    public function ajax_images($id = 0) {
        
			if(empty($id)){
				echo json_encode(array('status'=>0));
				return;
			}
			$this->load->model("images_m");
			$rslt = $this->images_m->get_by_product($id);
			if($rslt){
				$newrslt = array();
				foreach($rslt as $row){
					$newrslt[] = array(
					'image_dt' => $row->id,
					'image_pdt' => $row->product_id,
					'image_file' => $row->filename,
					'image_thumbnail' => $row->thumbnailname,
					'image_uploaded' => $row->uploadedname,
					'image_default' => $row->isdefault,
					'products_image' => lang("products:image"),
					'delete_image' => lang("products:delete_x"),
					'delete_title' => lang("products:delete_image"),
					'set_default' => lang("products:set_default"),
					'current_default' => lang("products:current_default"),
					'thumb_width' => $this->settings->thumbnail_width);
				}
				$result = array('status'=>1, 'result'=>$newrslt, 'ipath'=> base_url() . 'uploads/' . SITE_REF . '/' . $this->module . '/');
			}else{
				$result = array('status'=>0, 'result'=>'');
			}
			echo json_encode($result);
    }

    public function ajax_set_default($id = 0) {
        
			if(empty($id)){
				echo json_encode(array('status'=>0));
				return;
			}
			$this->load->model("images_m");
			$rslt = $this->images_m->set_default_image($id);
			if($rslt){
				$result = array('status'=>1);
			}else{
				$result = array('status'=>0);
			}
			echo json_encode($result);
    }

}

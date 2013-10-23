<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Module_Products extends Module {

    public $version = '1.0';

    public function info() {
        $info = array(
            'name' => array(
                'en' => 'Products',
                'id' => 'Produk'
            ),
            'description' => array(
                'en' => 'Manage products, categories and specials, and products order.',
                'id' => 'Mengatur produk, kategori produk dan produk spesial serta pemesanan produk.'
            ),
            'frontend' => TRUE,
            'backend' => TRUE,
            'menu' => 'content',
            'roles' => array('manage_orders', 'manage_custom_fields', 'manage_specials'),
            'sections' => array(
                'products' => array(
                    'name' => 'products:label',
                    'uri' => 'admin/products',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'products:create',
                            'uri' => 'admin/products/create',
                            'class' => 'add'
                        )
                    )
                ),
                'categories' => array(
                    'name' => 'categories:label',
                    'uri' => 'admin/products/categories',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'categories:create',
                            'uri' => 'admin/products/categories/create',
                            'class' => 'add'
                        )
                    )
                )
            )
        );
        
        if (group_has_role('products', 'manage_orders'))
		{
			$info['sections']['orders'] = array(
                    'name' => 'orders:label',
                    'uri' => 'admin/products/orders'
				);
		};
		
		if (group_has_role('products', 'manage_custom_fields'))
		{
			$info['sections']['fields'] = array(
                    'name' => 'fields:label',
                    'uri' => 'admin/products/fields',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'fields:create',
                            'uri' => 'admin/products/fields/create',
                            'class' => 'add'
                        )
                    )
                );
		};
		
		if (group_has_role('products', 'manage_specials'))
		{
			$info['sections']['specials'] = array(
                    'name' => 'specials:label',
                    'uri' => 'admin/products/specials',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'specials:create',
                            'uri' => 'admin/products/specials/create',
                            'class' => 'add'
                        )
                    )
                );
		};
		
		$info['sections']['settings'] = array(
                    'name' => 'settings:label',
                    'uri' => 'admin/settings#products'
                );

		return $info;
    }

    public function install() {
        $this->dbforge->drop_table('simpleshop_specials');
        $this->dbforge->drop_table('simpleshop_products');
        $this->dbforge->drop_table('simpleshop_categories');
        $this->dbforge->drop_table('simpleshop_specials_x_products');
        $this->dbforge->drop_table('simpleshop_fields');
        $this->dbforge->drop_table('simpleshop_products_x_fields');
        $this->dbforge->drop_table('simpleshop_orders');
        $this->dbforge->drop_table('simpleshop_order_items');
        $this->dbforge->drop_table('simpleshop_order_status');
        $this->dbforge->drop_table('simpleshop_customers');
        $this->db->delete('settings', array('module' => 'products'));

        $tables = array(
            'simpleshop_specials' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'slug' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => '', 'unique' => true),
                'start' => array('type' => 'DATETIME'),
                'end' => array('type' => 'DATETIME'),
                'description' => array('type' => 'TEXT'),
            ),
            'simpleshop_products' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'slug' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => '', 'unique' => true),
                'category' => array('type' => 'INT', 'constraint' => 11),
                'description' => array('type' => 'TEXT'),
                'price' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'min_buy' => array('type' => 'INT', 'constraint' => 11, 'default' => 1),
                'default_image_id' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 0)
            ),
            'simpleshop_categories' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'slug' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => '', 'unique' => true),
                'description' => array('type' => 'TEXT'),
            ),
            'simpleshop_fields' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'slug' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => '', 'unique' => true),
                'type' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'order' => array('type' => 'INT', 'constraint' => 11),
            ),
            'simpleshop_specials_x_products' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'special' => array('type' => 'INT', 'constraint' => 11),
                'product' => array('type' => 'INT', 'constraint' => 11),
                'old_price' => array('type' => 'FLOAT(8,2)'),
                'new_price' => array('type' => 'FLOAT(8,2)'),
            ),
            'simpleshop_products_x_fields' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'product' => array('type' => 'INT', 'constraint' => 11),
                'field' => array('type' => 'INT', 'constraint' => 11),
                'value' => array('type' => 'TEXT'),
            ),
            'simpleshop_orders' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'customer_id' => array('type' => 'INT', 'constraint' => 11),
                'ordered_on' => array('type' => 'datetime'),
                'shipped_on' => array('type' => 'datetime'),
                'subtotal' => array('type' => 'INT', 'constraint' => 11),
                'shipping' => array('type' => 'INT', 'constraint' => 11),
                'tax' => array('type' => 'INT', 'constraint' => 11),
                'total' => array('type' => 'INT', 'constraint' => 11),
                'status' => array('type' => 'TINYINT', 'constraint' => 4),
                'order_code' => array('type' => 'VARCHAR', 'constraint' => 8)
            ),
            'simpleshop_order_items' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'order_id' => array('type' => 'INT', 'constraint' => 11),
                'product_id' => array('type' => 'INT', 'constraint' => 11),
                'quantity' => array('type' => 'INT', 'constraint' => 11),
                'price' => array('type' => 'INT', 'constraint' => 11),
                'total' => array('type' => 'INT', 'constraint' => 11),
                'additional' => array('type' => 'TEXT', 'null' => true)
            ),
            'simpleshop_order_status' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'status' => array('type' => 'VARCHAR', 'constraint' => 20),
                'slug' => array('type' => 'VARCHAR', 'constraint' => 25)
            ),
            'simpleshop_customers' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'firstname' => array('type' => 'VARCHAR', 'constraint' => 32),
                'lastname' => array('type' => 'VARCHAR', 'constraint' => 32),
                'email' => array('type' => 'VARCHAR', 'constraint' => 128),
                'address' => array('type' => 'TEXT'),
                'city' => array('type' => 'VARCHAR', 'constraint' => 40),
                'postalcode' => array('type' => 'VARCHAR', 'constraint' => 5, 'null' => true),
                'phone' => array('type' => 'VARCHAR', 'constraint' => 32),
                'password' => array('type' => 'VARCHAR', 'constraint' => 40, 'null' => true),
                'active' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
                'confirmed' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0)  
            )      
        );

        if (!$this->install_tables($tables)) {
            return false;
        }

        // Create file folder for images
        $this->load->library('files/files');
        Files::create_folder(0, 'Product Images');

        $order_status = "INSERT INTO `".$this->db->dbprefix('simpleshop_order_status')."` (`id`, `status`, `slug`) VALUES
            (1, 'Pending', 'pending'),
            (2, 'Processing', 'processing'),
            (3, 'Shipped', 'shipped'),
            (4, 'On Hold', 'onhold'),
            (5, 'Cancelled', 'cancelled'),
            (6, 'Delivered', 'delivered');";
        $this->db->query($order_status);

        // setting items
        $thumbnail_width = array(
            'slug' => 'thumbnail_width',
            'title' => 'Thumbnail width',
            'description' => 'Specify the width for the product thumbnail',
            '`default`' => '160',
            '`value`' => '',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 0,
            'is_gui' => 1,
            'module' => 'products'
        );

        $thumbnail_height = array(
            'slug' => 'thumbnail_height',
            'title' => 'Thumbnail height',
            'description' => 'Specify the height for the product thumbnail',
            '`default`' => '160',
            '`value`' => '',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 0,
            'is_gui' => 1,
            'module' => 'products'
        );

        $item_per_page = array(
            'slug' => 'item_per_page',
            'title' => 'Item per Page',
            'description' => 'Specify the amount of item should be shown in a page',
            '`default`' => '10',
            '`value`' => '',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );

        $thousand_separator = array(
            'slug' => 'thousand_separator',
            'title' => 'Thousand Separator',
            'description' => 'Specify the thousand separator for price',
            '`default`' => '.',
            '`value`' => '',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );

        $decimal_separator = array(
            'slug' => 'decimal_separator',
            'title' => 'Decimal Separator',
            'description' => 'Specify the decimal separator for price',
            '`default`' => ',',
            '`value`' => '',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );

        $decimal_point = array(
            'slug' => 'decimal_point',
            'title' => 'Decimal Point',
            'description' => 'Sets the number of decimal points for price',
            '`default`' => '2',
            '`value`' => '',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );
        $bank_transfer = array(
            'slug' => 'bank_transfer',
            'title' => 'Bank',
            'description' => 'Set your bank for transfer payment',
            '`default`' => '-',
            '`value`' => '-',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );
        $rekening = array(
            'slug' => 'rekening',
            'title' => 'No. Rekening',
            'description' => 'Set your rekening number for transfer payment',
            '`default`' => '-',
            '`value`' => '-',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );
        $rekening_owner = array(
            'slug' => 'rekening_owner',
            'title' => 'Rekening Owner',
            'description' => 'Set the name  of the rekening owner',
            '`default`' => '-',
            '`value`' => '-',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );
        $shop_phone = array(
            'slug' => 'shop_phone',
            'title' => 'Shop Phone Number',
            'description' => 'Set your phone number for customer to send confirmation SMS',
            '`default`' => '-',
            '`value`' => '-',
            'type' => 'text',
            '`options`' => '',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'products'
        );

        if (!$this->db->insert('settings', $thumbnail_width)) {
            return FALSE;
        }

        if (!$this->db->insert('settings', $thumbnail_height)) {
            return FALSE;
        }

        if (!$this->db->insert('settings', $item_per_page)) {
            return FALSE;
        }

        if (!$this->db->insert('settings', $decimal_separator)) {
            return FALSE;
        }

        if (!$this->db->insert('settings', $thousand_separator)) {
            return FALSE;
        }

        if (!$this->db->insert('settings', $decimal_point)) {
            return FALSE;
        }
        if (!$this->db->insert('settings', $bank_transfer)) {
            return FALSE;
        }
        if (!$this->db->insert('settings', $rekening)) {
            return FALSE;
        }
        if (!$this->db->insert('settings', $rekening_owner)) {
            return FALSE;
        }
        if (!$this->db->insert('settings', $shop_phone)) {
            return FALSE;
        }

        //if (!is_dir($this->upload_path . 'products') AND !@mkdir($this->upload_path . 'products', 0777, TRUE)) {
            //return FALSE;
        //}

        $email = array(
            array(
                'slug' => 'order-invoice',
                'name' => 'Order Invoice',
                'description' => 'Pesan invoice pemesanan yang dikirimkan kepada pelanggan',
                'subject' => 'Order Invoice',
                'body' => "<p>Terima kasih telah memesan produk kami! :D</p>\n\n<p>Produk yang Anda pesan diantaranya adalah:</p>\n{{ order_items }} {{ /order_items }}\n\n<table border=\"1\" cellpadding=\"6\" cellspacing=\"1\" style=\"width:100%\">\n <tbody>\n       <tr>\n          <th>Prod. ID</th>\n         <th>QTY</th>\n          <th>Item Description</th>\n         <th style=\"text-align:right\">Item Price</th>\n          <th style=\"text-align:right\">Sub-Total</th>\n       </tr>\n     <tr>\n          <td>{{ id }}</td>\n         <td>{{ qty }}</td>\n            <td>{{ name }}</td>\n           <td>{{ settings:currency }} {{ price }}</td>\n          <td>{{ settings:currency }} {{ subtotal }}</td>\n       </tr>\n </tbody>\n</table>\n\n<p>Untuk selanjutnya silakan Anda melakukan pembayaran sebesar <strong>{{ settings:currency }} {{ total }}</strong> ke nomor rekening berikut:</p>\n\n<blockquote><strong>Bank: </strong>{{ settings:bank_transfer }}<br />\n<strong>No. Rekening: </strong>{{ settings:rekening }}<br />\n<strong>a.n. </strong>{{ settings:rekening_owner }}</blockquote>\nSetelah Anda mengirimkan pembayaran, segera lakukan konfirmasi via SMS ke nomor <strong>{{ settings:shop_phone }}</strong> dengan isi nama dan kode order, yakni sebagai berikut:\n\n<blockquote style=\"background: lightsteelblue\"><strong>{{ customer:firstname }} {{ customer:lastname }} {{ ordercode }}</strong></blockquote>\nKami akan mengirimkan produk pesanan Anda ke alamat yang tertera di atas segera setelah kami menerima pembayaran dari Anda.\n\n<p>&nbsp;</p>\n\n<p style=\"margin-bottom: 80px;\">Terima kasih.</p>",
                'lang' => 'en',
                'is_default' => 0,
                'module' => 'products'
            ),
            array(
                'slug' => 'order-notification',
                'name' => 'Order Notification',
                'description' => 'Notifikasi pembelian ke admin toko',
                'subject' => '{{ settings:site_name }} - Notifikasi Pemesanan',
                'body' => "<p>Selamat, produk ada ada yang pesan!</p>\n\n<p>Produk dipesan oleh&nbsp;{{ customer:firstname }} {{ customer:lastname }} dengan kode pesan <strong>{{ ordercode }}.</strong></p>\n\n<p>Produk dipesan diantaranya adalah:</p>\n{{ order_items }} {{ /order_items }}\n\n<table border=\"1\" cellpadding=\"6\" cellspacing=\"1\" style=\"width:100%\">\n  <tbody>\n       <tr>\n          <th>Prod. ID</th>\n         <th>QTY</th>\n          <th>Item Description</th>\n         <th style=\"text-align:right\">Item Price</th>\n          <th style=\"text-align:right\">Sub-Total</th>\n       </tr>\n     <tr>\n          <td>{{ id }}</td>\n         <td>{{ qty }}</td>\n            <td>{{ name }}</td>\n           <td>{{ settings:currency }} {{ price }}</td>\n          <td>{{ settings:currency }} {{ subtotal }}</td>\n       </tr>\n </tbody>\n</table>\n\n<p>Total pembelian:&nbsp;<strong>{{ settings:currency }} {{ total }}</strong>.</p>\n\n<p>Silakan ditindaklanjuti dengan menunggu konfirmasi pembayaran ke nomor {{ settings:shop_phone }}, kemudian periksa biaya transfer di rekening:</p>\n\n<blockquote><strong>Bank: </strong>{{ settings:bank_transfer }}<br />\n<strong>No. Rekening: </strong>{{ settings:rekening }}<br />\n<strong>a.n. </strong>{{ settings:rekening_owner }}</blockquote>\n\n<p>Dan segera kirimkan paket produk yang dipesan ke alamat tujuan.</p>\n\n<p>Detail pemesanan selanjutnya dapat dilihat di halaman admin situs Anda <a href=\"{{ url:site }}admin/products/orders/view/{{ ordercode }}\">{{ url:site }}admin/products/orders/view/{{ ordercode }}</a>.</p>\n\n<p style=\"margin-bottom: 80px;\">Terima kasih.</p>",
                'lang' => 'en',
                'is_default' => 0,
                'module' => 'products'
            )
        );
        
        $this->db->insert_batch('email_templates', $email);

        return TRUE;
    }

    public function uninstall() {
        $this->dbforge->drop_table('simpleshop_specials');
        $this->dbforge->drop_table('simpleshop_products');
        $this->dbforge->drop_table('simpleshop_categories');
        $this->dbforge->drop_table('simpleshop_specials_x_products');
        $this->dbforge->drop_table('simpleshop_fields');
        $this->dbforge->drop_table('simpleshop_products_x_fields');
        $this->dbforge->drop_table('simpleshop_orders');
        $this->dbforge->drop_table('simpleshop_order_items');
        $this->dbforge->drop_table('simpleshop_order_status');
        $this->dbforge->drop_table('simpleshop_customers');
        $this->dbforge->drop_table('simpleshop_images');

        $this->db->delete('settings', array('module' => 'products'));
        $this->db->delete('email_templates', array('module'=>'products'));

        return TRUE;
    }

    public function upgrade($old_version) {
        return TRUE;
    }

    public function help() {
        return "Help is not available for this module";
    }

}

/* End of file details.php */

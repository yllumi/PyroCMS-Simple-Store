<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Module_Products extends Module {

    public $version = '1.0';

    public function info() {
        return array(
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
            'menu' => 'store',
            'sections' => array(
                'orders' => array(
                    'name' => 'orders:label',
                    'uri' => 'admin/products/orders'
                ),
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
                ),
                'fields' => array(
                    'name' => 'fields:label',
                    'uri' => 'admin/products/fields',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'fields:create',
                            'uri' => 'admin/products/fields/create',
                            'class' => 'add'
                        )
                    )
                ),
                'specials' => array(
                    'name' => 'specials:label',
                    'uri' => 'admin/products/specials',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'specials:create',
                            'uri' => 'admin/products/specials/create',
                            'class' => 'add'
                        )
                    )
                ),
                'settings' => array(
                    'name' => 'settings:label',
                    'uri' => 'admin/settings#products'
                )
            )
        );
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
                'thumbnail' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'image' => array('type' => 'VARCHAR', 'constraint' => 255, 'default' => ''),
                'description' => array('type' => 'TEXT'),
                'price' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'min_buy' => array('type' => 'INT', 'constraint' => 11, 'default' => 1)
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

        $order_status = "INSERT INTO `default_simpleshop_order_status` (`id`, `status`, `slug`) VALUES
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

        if (!$this->install_tables($tables)) {
            return false;
        }

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

        if (!is_dir($this->upload_path . 'products') AND !@mkdir($this->upload_path . 'products', 0777, TRUE)) {
            return FALSE;
        }

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
        

        $this->db->delete('settings', array('module' => 'products'));

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

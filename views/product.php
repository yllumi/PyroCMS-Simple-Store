<div class="products-container">

    <?php if (!$items_exist) : ?>
        <p><?php echo lang('products:no_items') ?></p>
    <?php else : ?>

        <div class="products-data">

            <h2><a href="<?php echo base_url() . $this->module ?>"><?php echo lang('products:label') ?></a> &raquo; <?php echo $items->name ?></h2>

            <table cellpadding="0" cellspacing="0" style="width:100%">
                <tr>
                    <td width="210px">
                        <img src="<?= base_url() . 'uploads/default/products/' . $items->filename ?>" alt="<?php echo $items->name ?>" />
                    </td>
                    <td style="vertical-align: top; padding: 0 10px;">
                        <table>
                            <tr>
                                <td width="100px"><label><?php echo lang('products:category') ?></label></td>
                                <td><?php echo $items->category_name ? $items->category_name : lang('products:no_category') ?></td>
                            </tr>
                            <tr>
                                <td><label><?php echo lang('products:price') ?></label></td>
                                <td><?php echo $currency . ' ' . number_format($items->price, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></td>
                            </tr>
                            <?php foreach ($fields as $field) : ?>
                                <tr>
                                    <td><label><?php echo $field->name ?></label></td>
                                    <td><?php echo $items->custom_fields[$field->id]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td><label><?php echo lang('products:description') ?></label></td>
                                <td><?php echo $items->description ?></td>
                            </tr>
                        </table>
                        <table style="float:right;margin-top:20px;">
                            <tr><td>
                                    <form action="<?php echo site_url('products/checkout/add_to_cart/' . $items->slug) ?>" method="POST">
                                        Kuantitas
                                        <input type="text" name="qty" size="5" maxlength="2" placeholder="qty" value="1" class="verysmall" />
                                        <button class="button">Masukkan ke Keranjang</button>
                                    </form>
                                </td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    <?php endif; ?>

</div>

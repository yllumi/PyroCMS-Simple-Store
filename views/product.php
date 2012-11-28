<div class="products-container">

    <?php if (!$items) : ?>
        <p><?php echo lang('products:no_items') ?></p>
    <?php else : ?>

        <div class="products-data">

            <h2><a href="<?php echo base_url() . $this->module ?>"><?php echo lang('products:label') ?></a> &raquo; <?php echo $items->name ?></h2>

			<div class="product-images">
			<?php
				foreach($items->images as $item):
				$imgurl = site_url('files/large/'.$item->filename.'/250/250');
				echo '<img src="'.$imgurl.'" />';
				endforeach;
			?>
			</div>
			<div class="product-details">
				<label><?php echo lang('products:category'); ?></label>
				<span><?php echo $items->category_name ? $items->category_name : lang('products:no_category'); ?></span><br class="cboth"/>
				
				<label><?php echo lang('products:price'); ?></label>
				<span><?php echo $currency . ' ' . number_format($items->price, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></span><br class="cboth"/>
				
				<?php foreach ($fields as $field) : ?>
				<label><?php echo $field->name; ?></label>
				<span><?php echo $items->custom_fields[$field->id]; ?></span><br class="cboth"/>
				<?php endforeach; ?>
				
				<label><?php echo lang('products:description') ?></label>
				<span><?php echo $items->description; ?></span><br class="cboth"/>
				
				
				<label><?php echo lang('products:quantity') ?>Kuantitas</label>
				<span>
					<form action="<?php echo site_url('products/checkout/add_to_cart/' . $items->slug) ?>" method="POST">
						<input type="text" name="qty" size="5" maxlength="2" placeholder="qty" value="1" class="verysmall" />
						<button class="bbtn green">Masukkan ke Keranjang</button>
					</form>
				</span><br class="cboth"/>
		
			</div>
			
        </div>

    <?php endif; ?>

</div>

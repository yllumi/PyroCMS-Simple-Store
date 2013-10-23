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
				<dt><?php echo lang('products:category'); ?></dt>
				<dd><?php echo $items->category_name ? $items->category_name : lang('products:no_category'); ?></dd>
				
				<dt><?php echo lang('products:price'); ?></dt>
				<dd><?php echo $currency . ' ' . number_format($items->price, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?></dd>
				
				<?php foreach ($fields as $field) : ?>
				<dt><?php echo $field->name; ?></dt>
				<dd><?php echo $items->custom_fields[$field->id]; ?></dd>
				<?php endforeach; ?>
				
				<dt><?php echo lang('products:description') ?></dt>
				<dd><?php echo $items->description; ?></dd>

				<form action="<?php echo site_url('products/checkout/add_to_cart/' . $items->slug) ?>" method="POST" class="addtocart-form">
					<dt><?php echo lang('products:quantity') ?>Kuantitas</dt>
					<dd>
						<input type="text" name="qty" maxlength="2" placeholder="qty" value="1" />
						<button class="button">Add to Cart</button>
					</dd>
				</form>
		
			</div>
			
        </div>

    <?php endif; ?>

</div>
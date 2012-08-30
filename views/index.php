<div class="products-container">
	
	<h2><?php echo lang('products:all_products') ?></h2>
	
	<?php if(!$items_exist) : ?>
		<p><?php echo lang('products:no_items') ?></p>
	<?php else : ?>
    
		<div class="products-data">
			
			<?php foreach($items as $item) : ?>
			<div class="product-item">
				<a href="<? echo base_url().$this->module?>/product/<?php echo $item->slug?>">
					<img src="<?=base_url().'uploads/default/products/'.$item->thumbnail ?>" alt="<?php echo $item->name ?>" />
				</a>
				<div class="product-desc">
					<div class="product-name">
						<a href="<? echo base_url().$this->module?>/product/<?php echo $item->slug ?>"><?php echo $item->name ?></a>
					</div>
					<div class="product-price">
						<?php echo $currency . ' ' . number_format($item->price, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?> 
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			
		</div>
	
		{{ pagination:links }}
	
	<?php endif; ?>
	
</div>
<style>
	.product-item {
		display: inline-block;
		vertical-align: top;
		padding: 5px;
	}
	.product-item:hover {
		-webkit-box-shadow:rgba(0, 0, 0, 0.296875) 0px 0px 10px 0px;
		-moz-box-shadow:rgba(0, 0, 0, 0.296875) 0px 0px 10px 0px;
		box-shadow:rgba(0, 0, 0, 0.296875) 0px 0px 10px 0px;
	}
	.product-name, .product-price {
		text-align: center;
	}
	.product-price {
		font-size: 12px;
		color: green;
	}
</style>

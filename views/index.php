<div class="products-container">
	
	<h2><?php echo lang('products:all_products') ?></h2>
	
	<?php if(!$items_exist) : ?>
		<p><?php echo lang('products:no_items') ?></p>
	<?php else : ?>
    
		<div class="products-data">
			
			<?php foreach($items as $item) : ?>
			<div class="product-item bx green">
				<?php
					$base_path = FCPATH.UPLOAD_PATH.'products/'.$item->thumbnailname;
					if(file_exists($base_path) && is_file($base_path)){
						$imgurl = base_url().UPLOAD_PATH.'products/'.$item->thumbnailname;
						//list($width, $height, $type, $attr) = getimagesize($base_path);
						//$marleft = 0;
					}else{
						$imgurl = base_url().$this->module_details['path'].'/img/noimage/no_photo_trans_small.gif';
						//$marleft = 5;
					}
				?>
				<a href="<?php echo base_url().$this->module?>/product/<?php echo $item->slug;?>">
					<div class="imgbox">
						<img src="<?php echo $imgurl; ?>" alt="<?php echo $item->name; ?>" />
					</div>
					<div class="product-desc">
						<div class="product-name">
							<?php echo $item->name; ?>
						</div>
						<div class="product-price">
							<?php echo $currency . ' ' . number_format($item->price, $this->settings->decimal_point, $this->settings->decimal_separator, $this->settings->thousand_separator); ?> 
						</div>
					</div>
				</a>
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

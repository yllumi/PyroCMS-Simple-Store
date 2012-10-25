<section class="title">
    <h4><?php echo lang('products:' . $this->method); ?></h4>
</section>

<section class="item">

    <?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

    <div class="tabs">

        <ul class="tab-menu">
            <li><a href="#products-content"><span><?php echo lang('products:product_label'); ?></span></a></li>
            <li><a href="#products-image"><span><?php echo lang('products:image_label'); ?></span></a></li>
        </ul>

        <!-- Content tab -->
        <div class="form_inputs" id="products-content">
            <fieldset>
                <ul>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="name"><?php echo lang('products:name'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('name', set_value('name', $products->name), 'class="width-15"'); ?></div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="slug"><?php echo lang('products:slug'); ?> <span>*</span></label>
                        <div class="input"><?php echo form_input('slug', set_value('slug', $products->slug), 'class="width-15"'); ?></div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="category"><?php echo lang('products:category'); ?></label>
                        <div class="input">
                            <?php if (isset($categories)) : ?>
                                <?php $select_categories[0] = lang('products:no_category'); ?>
                                <?php foreach ($categories as $category) : ?>
                                    <?php $select_categories[$category->id] = $category->name; ?>
                                <?php endforeach; ?>
                                <?php if (isset($select_categories)) : ?>
                                    <?php echo form_dropdown('category', $select_categories, $products->category); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php echo anchor('admin/products/categories/create', lang('products:add_category'), 'style="padding: 8px; position:absolute;"'); ?>
                        </div>
                    </li>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="price"><?php echo lang('products:price'); ?></label>
                        <div class="input"><?php echo $this->settings->currency . ' ' . form_input('price', set_value('price', $products->price), 'class="width-15"'); ?></div>
                    </li>
                    <?php foreach ($fields as $field) : ?>
                        <?php if ($field->type == 'text') : ?>
                            <li class="<?php echo alternator('', 'even'); ?>">
                                <label for="<?php echo $field->slug ?>"><?php echo $field->name ?></label>
                                <div class="input"><?php echo form_input('custom_field[' . $field->id . ']', set_value($field->slug, isset($products->custom_fields[$field->id]) ? $products->custom_fields[$field->id] : ''), 'class="width-15"'); ?></div>
                            </li>
                        <?php endif; ?>
                        <?php if ($field->type == 'textarea') : ?>
                            <li class="<?php echo alternator('', 'even'); ?>">
                                <label for="<?php echo $field->slug ?>"><?php echo $field->name ?></label><br /><br />
                                <div>
                                    <?php echo form_textarea('custom_field[' . $field->id . ']', set_value($field->slug, isset($products->custom_fields[$field->id]) ? $products->custom_fields[$field->id] : ''), 'class="wysiwyg-simple"'); ?>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <label for="description"><?php echo lang('products:description'); ?></label><br /><br />
                        <div>
                            <?php echo form_textarea('description', $products->description, 'class="wysiwyg-simple"'); ?>
                        </div>
                    </li>
                </ul>
            </fieldset>
            <div class="buttons">
                <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
            </div>

            <?php echo form_close(); ?>
        </div>

        <!-- Content tab -->
        <div class="form_inputs" id="products-image">
            <fieldset>
                <ul class="info_img">
					<li>
						<?php echo lang('products:image_save_first'); ?>
					</li>
                </ul>
                <ul class="form_img">
					<?php 
					if(empty($products->id) or empty($products->thumbnail)){
						$imgsrc = site_url($this->module_details['path'].'/img/noimage/no_photo_trans_small.gif');
					}else{
						if(empty($images)){
							$imgsrc = site_url($this->module_details['path'].'/img/noimage/no_photo_trans_small.gif');
						}else{
							$imgsrc = ''; //site_url(UPLOAD_PATH.$this->module. '/' . $products->thumbnail).'#/'.$this->settings->thumbnail_width . 'x' . $this->settings->thumbnail_height;
						}
					}
					?>
                    <li class="<?php echo alternator('', 'even'); ?>">
                        <div>
                            <?php 
                            $upload_id = empty($products->id) ? 0 : $products->id;
                            $action_path = site_url('admin/' . $this->module . '/ajax_upload_image/'. $upload_id);
                            echo form_open_multipart($action_path, 'id="ajax-form-upload"'); ?>
                            <input type="file" name="userfile">&nbsp;
                            <input type="submit" value="<?php echo lang('products:upload_image'); ?>">
                            <?php echo form_close(); ?>

                            <div class="progress">
                                <div class="bar"></div >
                                <div class="percent">0%</div >
                            </div>
                        </div>
                        <hr />
                        <input type="hidden" id="prm_post_dt" value="<?php echo empty($products->id) ? 0 : $products->id; ?>" />
                        <div id="img_list" style="clear: both; height: <?php echo $this->settings->thumbnail_height ?>;">
                            <div class="imgBox" id="img_no_img"></div>
                        </div>
                    </li>
                </ul>
            </fieldset>
        </div>
    </div>

</section>

<script type="text/javascript">
    jQuery(function($) {
        $('form input[name="name"]').blur($.debounce(300, function(){
            var slug = $('input[name="slug"]');
            if(slug.val() == ""){
                $.post(SITE_URL + 'ajax/url_title', { title : $(this).val() }, function(new_slug){
                    slug.val( new_slug );
                });
            };
        }));
        var idp = <?php echo empty($products->id) ? 0 : $products->id; ?>;
        if(idp > 0){
			$('#products-image .info_img').css('display', 'none');
		}else{
			$('#products-image .form_img').html('<li>&nbsp;</li>');
		}
    });
</script>

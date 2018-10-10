<?php

			if (have_rows('reviews_product')) {
				// set the id of the element to something unique
				// this id will be needed by JS to append more content
				$total = count(get_field('reviews_product'));
				?>
				<div id="my-repeater-list-id">
					<?php 
					$number = 2; // the number of rows to show
					$count = 0; // a counter
					while (have_rows('reviews_product')) {
						the_row();
						?>

						<div class="comment">
							<div class="comment_header">
								<div class="info">
									<div class="line">
										<span class="label">Имя:</span>
										<span class="text"><?php the_sub_field('review_name'); ?></span>
									</div>
									<div class="line">
										<span class="label">Дата отзыва:</span>
										<span class="text"><?php the_sub_field('review_date'); ?></span>
									</div>
								</div>
								<span data-rate="<?php the_sub_field('review_rating'); ?>" class="rating rating-rev rate-<?php the_sub_field('review_rating'); ?>">
									<span class="star"></span>
									<span class="star"></span>
									<span class="star"></span>
									<span class="star"></span>
									<span class="star"></span>
								</span>
							</div>
							<div class="comment_content"><?php the_sub_field('review_text'); ?></div>
						</div>

						<?php 
						$count++;
						if ($count == $number) {
							// we've shown the number, break out of loop
							break;
						}
					} // end while have rows
					?>
				</div>
			<!-- 
				add a link to call the JS function to show more
				you will need to format this link using
				CSS if you want it to look like a button
				this button needs to be outside the container holding the
				items in the repeater field
			-->
			<button id="my-repeater-show-more-link" onclick="my_repeater_show_more();" <?php	if ($total < $count) { ?>style="display: none;"<?php } ?>>Загрузить еще</button>
			
			<!-- 
				The JS that will do the AJAX request
			-->
			<script type="text/javascript">
				var my_repeater_field_post_id = <?php echo $post->ID; ?>;
				var my_repeater_field_offset = <?php echo $number; ?>;
				var my_repeater_field_nonce = '<?php echo wp_create_nonce('my_repeater_field_nonce'); ?>';
				var my_repeater_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
				var my_repeater_more = true;
				
				function my_repeater_show_more() {
					
					// make ajax request
					jQuery.post(
						my_repeater_ajax_url, {
							// this is the AJAX action we set up in PHP
							'action': 'my_repeater_show_more',
							'post_id': my_repeater_field_post_id,
							'offset': my_repeater_field_offset,
							'nonce': my_repeater_field_nonce
						},
						function (json) {
							// add content to container
							// this ID must match the containter
							// you want to append content to
							jQuery('#my-repeater-list-id').append(json['content']);
							// update offset
							my_repeater_field_offset = json['offset'];
							// see if there is more, if not then hide the more link
							if (!json['more']) {
								// this ID must match the id of the show more link
								jQuery('#my-repeater-show-more-link').css('display', 'none');
							}
						},
						'json'
						);
				}
				
			</script>

		<?php } ?>

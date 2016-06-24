<?php

function rcp_member_levels_page()
{
	global $rcp_options, $rcp_db_name, $wpdb;
	$page = admin_url( '/admin.php?page=rcp-member-levels' );
	?>
	<div class="wrap">
		<?php if(isset($_GET['edit_subscription'])) :
			include('edit-subscription.php');
		else : ?>
			<h2><?php _e('Subscription Levels', 'rcp'); ?></h2>
			<table class="wp-list-table widefat fixed posts rcp-subscriptions">
				<thead>
					<tr>
						<th scope="col" class="rcp-sub-name-col column-primary"><?php _e('Name', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-desc-col"><?php _e('Description', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-level-col"><?php _e('Access Level', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-duration-col"><?php _e('Duration', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-price-col"><?php _e('Price', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-subs-col"><?php _e('Subscribers', 'rcp'); ?></th>
						<?php do_action('rcp_levels_page_table_header'); ?>
						<th scope="col" class="rcp-sub-order-col"><?php _e('Order', 'rcp'); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
				<?php $levels = rcp_get_subscription_levels( 'all' ); ?>
				<?php
				if($levels) :
					$i = 1;
					foreach( $levels as $key => $level) : ?>
						<tr id="recordsArray_<?php echo $level->id; ?>" class="rcp-subscription rcp_row <?php if(rcp_is_odd($i)) { echo 'alternate'; } ?>">
							<td class="rcp-sub-name-col column-primary has-row-actions" data-colname="<?php _e( 'Name', 'rcp' ); ?>">
								<strong><a href="<?php echo esc_url( add_query_arg( 'edit_subscription', $level->id, $page ) ); ?>"><?php echo stripslashes( $level->name ); ?></a></strong>
								<?php if( current_user_can( 'rcp_manage_levels' ) ) : ?>
									<div class="row-actions">
										<span class="rcp-sub-id-col" data-colname="<?php _e( 'ID:', 'rcp' ); ?>"> <?php echo __( 'ID:', 'rcp' ) . ' ' . $level->id; ?> | </span>
										<a href="<?php echo esc_url( add_query_arg('edit_subscription', $level->id, $page) ); ?>"><?php _e('Edit', 'rcp'); ?></a> |
										<?php if($level->status != 'inactive') { ?>
											<a href="<?php echo esc_url( add_query_arg('deactivate_subscription', $level->id, $page) ); ?>"><?php _e('Deactivate', 'rcp'); ?></a> |
										<?php } else { ?>
											<a href="<?php echo esc_url( add_query_arg('activate_subscription', $level->id, $page) ); ?>"><?php _e('Activate', 'rcp'); ?></a> |
										<?php } ?>
										<a href="<?php echo esc_url( add_query_arg('delete_subscription', $level->id, $page) ); ?>" class="rcp_delete_subscription"><?php _e('Delete', 'rcp'); ?></a>
									</div>
								<?php endif; ?>
								<button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e( 'Show more details', 'rcp' ); ?></span></button>
							</td>
							<td class="rcp-sub-desc-col" data-colname="<?php _e( 'Description', 'rcp' ); ?>"><?php echo stripslashes( $level->description ); ?></td>
							<td class="rcp-sub-level-col" data-colname="<?php _e( 'Access Level', 'rcp' ); ?>"><?php echo $level->level != '' ? $level->level : __('none', 'rcp'); ?></td>
							<td class="rcp-sub-duration-col" data-colname="<?php _e( 'Duration', 'rcp' ); ?>">
								<?php
									if($level->duration > 0) {
										echo $level->duration . ' ' . rcp_filter_duration_unit($level->duration_unit, $level->duration);
									} else {
										echo __('unlimited', 'rcp');
									}
								?>
							</td>
							<td class="rcp-sub-price-col" data-colname="<?php _e( 'Price', 'rcp' ); ?>">
								<?php
								$price = rcp_get_subscription_price( $level->id );
								if( ! $price ) {
									echo __( 'Free', 'rcp' );
								} else {
									echo rcp_currency_filter( $price );
								}
								?>
							</td>
							<td class="rcp-sub-subs-col" data-colname="<?php _e( 'Subscribers', 'rcp' ); ?>">
								<?php
								if( $price || $level->duration > 0 ) {
									echo rcp_get_subscription_member_count( $level->id, 'active' );
								} else {
									echo rcp_get_subscription_member_count( $level->id, 'free' );
								}
								?>
							</td>
							<?php do_action('rcp_levels_page_table_column', $level->id); ?>
							<td class="rcp-sub-order-col"><a href="#" class="dragHandle"></a></td>
						</tr>
					<?php $i++;
					endforeach;
				else : ?>
					<tr><td colspan="9"><?php _e('No subscription levels added yet.', 'rcp'); ?></td></tr>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col" class="rcp-sub-name-col column-primary"><?php _e('Name', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-desc-col"><?php _e('Description', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-level-col"><?php _e('Access Level', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-duration-col"><?php _e('Duration', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-price-col"><?php _e('Price', 'rcp'); ?></th>
						<th scope="col" class="rcp-sub-subs-col"><?php _e('Subscribers', 'rcp'); ?></th>
						<?php do_action('rcp_levels_page_table_footer'); ?>
						<th scope="col" class="rcp-sub-order-col"><?php _e('Order', 'rcp'); ?></th>
					</tr>
				</tfoot>
			</table>
			<?php do_action('rcp_levels_below_table'); ?>
			<?php if( current_user_can( 'rcp_manage_levels' ) ) : ?>
				<h3><?php _e('Add New Level', 'rcp'); ?></h3>
				<form id="rcp-member-levels" action="" method="post">
					<table class="form-table">
						<tbody>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-name"><?php _e('Name', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-name" name="name" value="" style="width: 300px;"/>
									<p class="description"><?php _e('The name of the membership level.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-description"><?php _e('Description', 'rcp'); ?></label>
								</th>
								<td>
									<textarea id="rcp-description" name="description" style="width: 300px;"></textarea>
									<p class="description"><?php _e('Membership level description. This is shown on the registration form.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-level"><?php _e('Access Level', 'rcp'); ?></label>
								</th>
								<td>
									<select id="rcp-level" name="level">
										<?php
										$access_levels = rcp_get_access_levels();
										foreach( $access_levels as $access ) {
											echo '<option value="' . $access . '">' . $access . '</option>';
										}
										?>
									</select>
									<p class="description"><?php _e('Level of access this subscription gives. Leave None for default or you are unsure what this is.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-duration"><?php _e('Duration', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-duration" style="width: 40px;" name="duration" value=""/>
									<select name="duration_unit" id="rcp-duration-unit">
										<option value="day"><?php _e('Day(s)', 'rcp'); ?></option>
										<option value="month"><?php _e('Month(s)', 'rcp'); ?></option>
										<option value="year"><?php _e('Year(s)', 'rcp'); ?></option>
									</select>
									<p class="description"><?php _e('Length of time for this membership level. Enter 0 for unlimited.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-price"><?php _e('Price', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-price" name="price" value="" style="width: 40px;"/>
									<select name="rcp-price-select" id="rcp-price-select">
										<option value="normal"><?php echo isset( $rcp_options['currency'] ) ? $rcp_options['currency'] : 'USD'; ?></option>
										<option value="free"><?php _e('Free', 'rcp'); ?></option>
									</select>
									<p class="description"><?php _e('The price of this membership level. Enter 0 for free.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-fee"><?php _e('Signup Fee', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-fee" name="fee" value="" style="width: 40px;"/>
									<p class="description"><?php _e('Optional signup fee to charge subscribers for the first billing cycle. Enter a negative number to give a discount on the first payment.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-status"><?php _e('Status', 'rcp'); ?></label>
								</th>
								<td>
									<select name="status" id="rcp-status">
										<option value="active"><?php _e('Active', 'rcp'); ?></option>
										<option value="inactive"><?php _e('Inactive', 'rcp'); ?></option>
									</select>
									<p class="description"><?php _e('Members may only sign up for active subscription levels.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-role"><?php _e( 'User Role', 'rcp' ); ?></label>
								</th>
								<td>
									<select name="role" id="rcp-role">
										<?php wp_dropdown_roles( 'subscriber' ); ?>
									</select>
									<p class="description"><?php _e( 'The user role given to the member after signing up.', 'rcp' ); ?></p>
								</td>
							</tr>
							<?php do_action( 'rcp_add_subscription_form' ); ?>
						</tbody>
					</table>
					<p class="submit">
						<input type="hidden" name="rcp-action" value="add-level"/>
						<input type="submit" value="<?php _e('Add Membership Level', 'rcp'); ?>" class="button-primary"/>
					</p>
					<?php wp_nonce_field( 'rcp_add_level_nonce', 'rcp_add_level_nonce' ); ?>
				</form>
			<?php endif; ?>
		<?php endif; ?>
	</div><!--end wrap-->

	<?php
}

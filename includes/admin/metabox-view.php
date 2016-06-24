<?php
$is_paid           = get_post_meta( get_the_ID(), '_is_paid', true ); // This is purely for backwards compatibility and is no longer used
$sub_levels        = get_post_meta( get_the_ID(), 'rcp_subscription_level', true );
$set_level         = is_array( $sub_levels ) ? '' : $sub_levels;
$access_level      = get_post_meta( get_the_ID(), 'rcp_access_level', true );
$access_level      = is_numeric( $access_level ) ? absint( $access_level ) : '';
$show_excerpt      = get_post_meta( get_the_ID(), 'rcp_show_excerpt', true );
$hide_in_feed      = get_post_meta( get_the_ID(), 'rcp_hide_from_feed', true );
$user_role         = get_post_meta( get_the_ID(), 'rcp_user_level', true );
$access_display    = is_numeric( $access_level ) ? '' : ' style="display:none;"';
$level_set_display = ! empty( $sub_levels ) || ! empty( $is_paid ) ? '' : ' style="display:none;"';
$levels_display    = is_array( $sub_levels ) ? '' : ' style="display:none;"';
$role_set_display  = '' != $user_role ? '' : ' style="display:none;"';
?>
<div id="rcp-metabox-field-restrict-by" class="rcp-metabox-field">
	<p><strong><?php _e( 'Member access options', 'rcp' ); ?></strong></p>
	<p>
		<?php _e( 'Select who should have access to this content.', 'rcp' ); ?>
		<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php _e( '<strong>Subscription level</strong>: a subscription level refers to a membership option. For example, you might have a Gold, Silver, and Bronze membership level. <br/><br/><strong>Access Level</strong>: refers to a tiered system where a member\'s ability to view content is determined by the access level assigned to their account. A member with an access level of 5 can view content assigned to access levels of 5 and lower.', 'rcp' ); ?>"></span>
	</p>
	<p>
		<select id="rcp-restrict-by" name="rcp_restrict_by">
			<option value="unrestricted" <?php selected( true, empty( $sub_levels ) && empty( $access_level) ); ?>><?php _e( 'Everyone', 'rcp' ); ?></option>
			<option value="subscription-level"<?php selected( true, ! empty( $sub_levels ) || ! empty( $is_paid ) ); ?>><?php _e( 'Members of subscription level(s)', 'rcp' ); ?></option>
			<option value="access-level"<?php selected( true, is_numeric( $access_level ) && empty( $is_paid ) ); ?>><?php _e( 'Members with an access level', 'rcp' ); ?></option>
			<option value="registered-users"<?php selected( true, empty( $sub_levels ) && ! is_numeric( $access_level ) && empty( $is_paid ) ); ?>><?php _e( 'Members with a certain role', 'rcp' ); ?></option>
		</select>
	</p>
</div>
<div id="rcp-metabox-field-levels" class="rcp-metabox-field"<?php echo $level_set_display; ?>>
	<label for="rcp_subscription_level_any">
		<input type="radio" name="rcp_subscription_level_any_set" id="rcp_subscription_level_any" value="any"<?php checked( 'any', $set_level ); ?>/>
		&nbsp;<?php _e( 'Members of any subscription level(s)', 'rcp' ); ?><br/>
	</label>
	<label for="rcp_subscription_level_any_paid">
		<input type="radio" name="rcp_subscription_level_any_set" id="rcp_subscription_level_any_paid" value="any-paid"<?php checked( true, $set_level == 'any-paid' || ! empty( $is_paid ) ); ?>/>
		&nbsp;<?php _e( 'Members of any non-free subscription level(s)', 'rcp' ); ?><br/>
	</label>
	<label for="rcp_subscription_level_specific">
		<input type="radio" name="rcp_subscription_level_any_set" id="rcp_subscription_level_specific" value="specific"<?php checked( true, is_array( $sub_levels ) ); ?>/>
		&nbsp;<?php _e( 'Members of specific subscription levels', 'rcp' ); ?><br/>
	</label>
	<p class="rcp-subscription-levels"<?php echo $levels_display; ?>>
		<?php foreach( rcp_get_subscription_levels() as $level ) : ?>
			<label for="rcp_subscription_level_<?php echo $level->id; ?>">
				<input type="checkbox" name="rcp_subscription_level[]"<?php checked( true, in_array( $level->id, (array) $sub_levels ) ); ?> class="rcp_subscription_level" id="rcp_subscription_level_<?php echo $level->id; ?>" value="<?php echo esc_attr( $level->id ); ?>" data-price="<?php echo esc_attr( $level->price ); ?>"/>
				&nbsp;<?php echo $level->name; ?><br/>
			</label>
		<?php endforeach; ?>
	</p>
</div>
<div id="rcp-metabox-field-access-levels" class="rcp-metabox-field"<?php echo $access_display; ?>>
	<p>
		<select name="rcp_access_level" id="rcp-access-level-field">
			<?php foreach( rcp_get_access_levels() as $key => $access_level_label ) : ?>
				<option id="rcp_access_level<?php echo $key; ?>" value="<?php echo esc_attr( $key ); ?>"<?php selected( $key, $access_level ); ?>><?php printf( __( '%s and higher', 'rcp' ), $key ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
</div>
<div id="rcp-metabox-field-role" class="rcp-metabox-field"<?php echo $role_set_display; ?>>
	<p>
		<span><?php _e( 'Require member to have capabilities from this user role or higher.', 'rcp' ); ?></span>
	</p>
	<p>
		<select name="rcp_user_level" id="rcp-user-level-field">
			<?php foreach( array( 'All', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber' ) as $role ) : ?>
				<option value="<?php echo esc_attr( $role ); ?>"<?php selected( $role, $user_role ); ?>><?php echo $role; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
</div>
<div id="rcp-metabox-field-options" class="rcp-metabox-field">

	<p><strong><?php _e( 'Additional options', 'rcp' ); ?></strong></p>
	<p>
		<label for="rcp-show-excerpt">
			<input type="checkbox" name="rcp_show_excerpt" id="rcp-show-excerpt" value="1"<?php checked( true, $show_excerpt ); ?>/>
			<?php _e( 'Show excerpt to members without access to this content.', 'rcp' ); ?>
		</label>
	</p>
	<p>
		<label for="rcp-hide-in-feed">
			<input type="checkbox" name="rcp_hide_from_feed" id="rcp-hide-in-feed" value="1"<?php checked( true, $hide_in_feed ); ?>/>
			<?php _e( 'Hide this content and excerpt from RSS feeds.', 'rcp' ); ?>
		</label>
	</p>
	<p>
		<?php printf(
			__( 'Optionally use [restrict paid="true"] ... [/restrict] shortcode to restrict partial content. %sView documentation for additional options%s.', 'rcp' ),
			'<a href="' . esc_url( 'http://docs.pippinsplugins.com/article/36-restricting-post-and-page-content' ) . '" target="_blank">',
			'</a>'
		); ?>
	</p>
</div>
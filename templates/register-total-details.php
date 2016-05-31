<?php

if ( ! rcp_is_registration() ) {
	return;
}

?>

<table class="rcp_registration_total_details rcp-table">

	<tbody style="vertical-align: top;">

		<tr>
			<th><?php _e( 'Subscription', 'rcp' ); ?></th>
			<th><?php _e( 'Amount', 'rcp' ); ?></th>
		</tr>

		<tr>
			<td><?php echo rcp_get_subscription_name( rcp_get_registration()->get_subscription() ); ?></td>
			<td><?php echo ( rcp_get_subscription_price( rcp_get_registration()->get_subscription() ) > 0 ) ? rcp_currency_filter( number_format( rcp_get_subscription_price( rcp_get_registration()->get_subscription() ), rcp_currency_decimal_filter() ) ) : __( 'free', 'rcp' ); ?></td>
		</tr>

		<?php if ( rcp_get_subscription_price( rcp_get_registration()->get_subscription() ) ) : ?>
			<?php if ( rcp_get_registration()->get_fees() || rcp_get_registration()->get_discounts() ) : ?>
				<tr>
					<th colspan="2"><?php _e( 'Discounts and Fees', 'rcp' ); ?></th>
				</tr>

				<?php // Discounts ?>
				<?php if ( rcp_get_registration()->get_discounts() ) : foreach( rcp_get_registration()->get_discounts() as $code => $recuring ) : if ( ! $discount = rcp_get_discount_details_by_code( $code ) ) continue; ?>
					<tr class="rcp-discount">
						<td><?php echo esc_html( $discount->name ); ?></td>
						<td><?php echo esc_html( rcp_discount_sign_filter( $discount->amount, $discount->unit ) ); ?></td>
					</tr>
				<?php endforeach; endif; ?>

				<?php // Fees ?>
				<?php if ( rcp_get_registration()->get_fees() ) : foreach( rcp_get_registration()->get_fees() as $fee ) :

					$sign          = ( $fee['amount'] < 0 ) ? '-' : '';
					$fee['amount'] = abs( $fee['amount'] );
					$fee['amount'] = number_format( $fee['amount'], rcp_currency_decimal_filter() );
				?>
					<tr class="rcp-fee">
						<td><?php echo esc_html( $fee['description'] ); ?></td>
						<td><?php echo esc_html( $sign . rcp_currency_filter( $fee['amount'] ) ); ?></td>
					</tr>
				<?php endforeach; endif; ?>

			<?php endif; ?>
		<?php endif; ?>

		<tr class="rcp-total">
			<th><?php _e( 'Total Today', 'rcp' ); ?></th>
			<th><?php rcp_registration_total(); ?></th>
		</tr>

		<?php if ( rcp_registration_is_recurring() ) : ?>
			<?php
			$subscription = rcp_get_subscription_details( rcp_get_registration()->get_subscription() );

			if ( $subscription->duration == 1 ) {
				$label = sprintf( __( 'Total Recurring Per %s', 'rcp' ), rcp_filter_duration_unit( $subscription->duration_unit, 1 ) );
			} else {
				$label = sprintf( __( 'Total Recurring Every %s %s', 'rcp' ), $subscription->duration, rcp_filter_duration_unit( $subscription->duration_unit, $subscription->duration ) );
			}
			?>
			<tr class="rcp-recurring-total">
				<th><?php echo $label; ?></th>
				<th><?php rcp_registration_recurring_total(); ?></th>
			</tr>
		<?php endif; ?>

	</tbody>
</table>
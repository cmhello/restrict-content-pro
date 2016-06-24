<?php

/**
 * Generate URL to view / download an invoice
 *
 * @since 2.6
 * @return string
*/
function rcp_get_invoice_url( $payment_id = 0 ) {

	if ( empty( $payment_id ) ) {
		return false;
	}

	$base = is_admin() ? admin_url( 'index.php' ) : home_url();

	return wp_nonce_url( add_query_arg( array( 'payment_id' => $payment_id, 'rcp-action' => 'download_invoice' ), $base ), 'rcp_download_invoice_nonce' );
}

function rcp_trigger_invoice_download() {

	if( ! isset( $_GET['rcp-action'] ) || 'download_invoice' != $_GET['rcp-action'] ) {
		return;
	}

	if( ! wp_verify_nonce( $_GET['_wpnonce'], 'rcp_download_invoice_nonce' ) ) {
		return;
	}

	$payment_id = absint( $_GET['payment_id'] );

	rcp_generate_invoice( $payment_id );

}
add_action( 'init', 'rcp_trigger_invoice_download' );

/**
 * Generate Invoice
 *
 * @since 2.6
*/
function rcp_generate_invoice( $payment_id = 0 ) {

	global $rcp_options, $rcp_payment, $rcp_member;

	if ( empty( $payment_id ) ) {
		return;
	}

	$payments_db  = new RCP_Payments;
	$payment      = $payments_db->get_payment( $payment_id );

	if( ! $payment ) {
		wp_die( __( 'This payment record does not exist', 'rcp' ) );
	}

	if( $payment->user_id != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to download this invoice', 'rcp' ) );
	}

	$rcp_payment = $payment;
	$rcp_member = new RCP_Member( $payment->user_id );

	rcp_get_template_part( 'invoice' );

	die(); // Stop the rest of the page from processsing and being sent to the browser
}
<?php
/**
 * Upgrade class
 *
 * This class handles database upgrade routines between versions
 *
 * @package     Restrict Content Pro
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.6
 */
class RCP_Upgrades {

	private $upgraded = false;

	public function __construct() {

		add_action( 'admin_init', array( $this, 'init' ), -9999 );

	}

	public function init() {

		$version = get_option( 'rcp_version' );

		$this->v26_upgrades();

		// If upgrades have occurred
		if ( $this->upgraded ) {
			update_option( 'rcp_version_upgraded_from', $version );
			update_option( 'rcp_version', RCP_PLUGIN_VERSION );
		}

	}

	private function v26_upgrades() {

		$version = get_option( 'rcp_version' );

		if( version_compare( $version, '2.6', '<' ) ) {
			@rcp_options_install();
		}
	}

}
new RCP_Upgrades;
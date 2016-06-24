<?php

/**
 * RCP Subscription Levels class
 *
 * This class handles querying, inserting, updating, and removing subscription levels
 * Also includes other discount helper functions
 *
 * @since 1.5
*/

class RCP_Levels {

	/**
	 * Holds the name of our levels database table
	 *
	 * @access  public
	 * @since   1.5
	*/
	public $db_name;

	/**
	 * Holds the name of our level meta database table
	 *
	 * @access  public
	 * @since   2.6
	*/
	public $meta_db_name;


	/**
	 * Holds the version number of our levels database table
	 *
	 * @access  public
	 * @since   1.5
	*/
	public $db_version;


	/**
	 * Get things started
	 *
	 * @since   1.5
	*/
	function __construct() {

		$this->db_name      = rcp_get_levels_db_name();
		$this->meta_db_name = rcp_get_level_meta_db_name();
		$this->db_version   = '1.6';

	}


	/**
	 * Retrieve a specific subscription level from the database
	 *
	 * @access  public
	 * @since   1.5
	*/

	public function get_level( $level_id = 0 ) {
		global $wpdb;

		$level = wp_cache_get( 'level_' . $level_id, 'rcp' );

		if( false === $level ) {

			$level = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->db_name} WHERE id='%d';", $level_id ) );

			wp_cache_set( 'level_' . $level_id, $level, 'rcp' );

		}

		return apply_filters( 'rcp_get_level', $level );

	}

	/**
	 * Retrieve a specific subscription level from the database
	 *
	 * @access  public
	 * @since   1.8.2
	*/

	public function get_level_by( $field = 'name', $value = '' ) {
		global $wpdb;


		$level = wp_cache_get( 'level_' . $field . '_' . $value, 'rcp' );

		if( false === $level ) {

			$level = $wpdb->get_row( "SELECT * FROM {$this->db_name} WHERE {$field}='{$value}';" );

			wp_cache_set( 'level_' . $field . '_' . $value, $level, 'rcp' );

		}

		return apply_filters( 'rcp_get_level', $level );

	}


	/**
	 * Retrieve all subscription levels from the database
	 *
	 * @access  public
	 * @since   1.5
	*/

	public function get_levels( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'status'  => 'all',
			'limit'   => null,
			'orderby' => 'list_order'
		);

		$args = wp_parse_args( $args, $defaults );

		if( $args['status'] == 'active' ) {
			$where = "WHERE `status` !='inactive'";
		} elseif( $args['status'] == 'inactive' ) {
			$where = "WHERE `status` ='{$status}'";
		} else {
			$where = "";
		}

		if( ! empty( $args['limit'] ) )
			$limit = " LIMIT={$args['limit']}";
		else
			$limit = '';

		$cache_key = md5( implode( '|', $args ) . $where );

		$levels = wp_cache_get( $cache_key, 'rcp' );

		if( false === $levels ) {

			$levels = $wpdb->get_results( "SELECT * FROM {$this->db_name} {$where} ORDER BY {$args['orderby']}{$limit};" );

			wp_cache_set( $cache_key, $levels, 'rcp' );

		}

		$levels = apply_filters( 'rcp_get_levels', $levels );

		if( ! empty( $levels ) ) {
			return $levels;
		}

		return false;
	}


	/**
	 * Retrieve a field for a subscription level
	 *
	 * @access  public
	 * @since   1.5
	*/

	public function get_level_field( $level_id = 0, $field = '' ) {

		global $wpdb;


		$value = wp_cache_get( 'level_' . $level_id . '_' . $field, 'rcp' );

		if( false === $value ) {

			$value = $wpdb->get_col( $wpdb->prepare( "SELECT {$field} FROM {$this->db_name} WHERE id='%d';", $level_id ) );

			wp_cache_set( 'level_' . $level_id . '_' . $field, $value, 'rcp', 3600 );

		}

		$value = ( $value ) ? $value[0] : false;

		return apply_filters( 'rcp_get_level_field', $value, $level_id, $field );

	}


	/**
	 * Insert a subscription level into the database
	 *
	 * @access  public
	 * @since   1.5
	*/

	public function insert( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'name'          => '',
			'description'   => '',
			'duration'      => 'unlimited',
			'duration_unit' => 'month',
			'price'         => '0',
			'fee'           => '0',
			'list_order'    => '0',
			'level' 	    => '0',
			'status'        => 'inactive',
			'role'          => 'subscriber'
		);

		$args = wp_parse_args( $args, $defaults );

		do_action( 'rcp_pre_add_subscription', $args );

		$args = apply_filters( 'rcp_add_subscription_args', $args );

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->db_name} SET
					`name`          = '%s',
					`description`   = '%s',
					`duration`      = '%d',
					`duration_unit` = '%s',
					`price`         = '%s',
					`fee`           = '%s',
					`list_order`    = '0',
					`level`         = '%d',
					`status`        = '%s',
					`role`          = '%s'
				;",
				sanitize_text_field( $args['name'] ),
				sanitize_text_field( $args['description'] ),
				sanitize_text_field( $args['duration'] ),
				sanitize_text_field( $args['duration_unit'] ),
				sanitize_text_field( $args['price'] ),
				sanitize_text_field( $args['fee'] ),
				absint( $args['level'] ),
				sanitize_text_field( $args['status'] ),
				sanitize_text_field( $args['role'] )
			 )
		);

		if( $add ) {

			$args = array(
				'status'  => 'all',
				'limit'   => null,
				'orderby' => 'list_order'
			);

			$cache_key = md5( implode( '|', $args ) );

			wp_cache_delete( $cache_key, 'rcp' );

			do_action( 'rcp_add_subscription', $wpdb->insert_id, $args );

			return $wpdb->insert_id;
		}

		return false;

	}


	/**
	 * Update an existing subscription level
	 *
	 * @access  public
	 * @since   1.5
	*/

	public function update( $level_id = 0, $args = array() ) {

		global $wpdb;

		$level = $this->get_level( $level_id );
		$level = get_object_vars( $level );

		$args     = array_merge( $level, $args );

		do_action( 'rcp_pre_edit_subscription_level', absint( $args['id'] ), $args );

		$update = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$this->db_name} SET
					`name`          = '%s',
					`description`   = '%s',
					`duration`      = '%d',
					`duration_unit` = '%s',
					`price`         = '%s',
					`fee`           = '%s',
					`level`         = '%d',
					`status`        = '%s',
					`role`          = '%s'
					WHERE `id`      = '%d'
				;",
				sanitize_text_field( $args['name'] ),
				wp_kses( $args['description'], rcp_allowed_html_tags() ),
				sanitize_text_field( $args['duration'] ),
				sanitize_text_field( $args['duration_unit'] ),
				sanitize_text_field( $args['price'] ),
				sanitize_text_field( $args['fee'] ),
				absint( $args['level'] ),
				sanitize_text_field( $args['status'] ),
				sanitize_text_field( $args['role'] ),
				absint( $args['id'] )
			)
		);

		$cache_args = array(
			'status'  => 'all',
			'limit'   => null,
			'orderby' => 'list_order'
		);

		$cache_key = md5( implode( '|', $cache_args ) );

		wp_cache_delete( $cache_key, 'rcp' );

		do_action( 'rcp_edit_subscription_level', absint( $args['id'] ), $args );

		if( $update !== false )
			return true;
		return false;

	}


	/**
	 * Delete a subscription level
	 *
	 * @access  public
	 * @since   1.5
	*/

	public function remove( $level_id = 0 ) {

		global $wpdb;

		$remove = $wpdb->query( $wpdb->prepare( "DELETE FROM " . $this->db_name . " WHERE `id`='%d';", absint( $level_id ) ) );

		$args = array(
			'status'  => 'all',
			'limit'   => null,
			'orderby' => 'list_order'
		);

		$cache_key = md5( implode( '|', $args ) );

		wp_cache_delete( $cache_key, 'rcp' );

		do_action( 'rcp_remove_level', absint( $level_id ) );

	}

	/**
	 * Retrieve level meta field for a subscription level.
	 *
	 * @param   int    $level_id      Subscription level ID.
	 * @param   string $meta_key      The meta key to retrieve.
	 * @param   bool   $single        Whether to return a single value.
	 * @return  mixed                 Will be an array if $single is false. Will be value of meta data field if $single is true.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function get_meta( $level_id = 0, $meta_key = '', $single = false ) {
		return get_metadata( 'level', $level_id, $meta_key, $single );
	}

	/**
	 * Add meta data field to a subscription level.
	 *
	 * @param   int    $level_id      Subscription level ID.
	 * @param   string $meta_key      Metadata name.
	 * @param   mixed  $meta_value    Metadata value.
	 * @param   bool   $unique        Optional, default is false. Whether the same key should not be added.
	 * @return  bool                  False for failure. True for success.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function add_meta( $level_id = 0, $meta_key = '', $meta_value, $unique = false ) {
		return add_metadata( 'level', $level_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Update level meta field based on Subscription level ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and Subscription level ID.
	 *
	 * If the meta field for the subscription level does not exist, it will be added.
	 *
	 * @param   int    $level_id      Subscription level ID.
	 * @param   string $meta_key      Metadata key.
	 * @param   mixed  $meta_value    Metadata value.
	 * @param   mixed  $prev_value    Optional. Previous value to check before removing.
	 * @return  bool                  False on failure, true if success.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function update_meta( $level_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
		return update_metadata( 'level', $level_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Remove metadata matching criteria from a subscription level.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @param   int    $level_id      Subscription level ID.
	 * @param   string $meta_key      Metadata name.
	 * @param   mixed  $meta_value    Optional. Metadata value.
	 * @return  bool                  False for failure. True for success.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function delete_meta( $level_id = 0, $meta_key = '', $meta_value = '' ) {
		return delete_metadata( 'level', $level_id, $meta_key, $meta_value );
	}

}
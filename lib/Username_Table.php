<?php
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Description of Username_Table
 *
 * @author gyates
 */
class Username_Table extends WP_List_Table {
	public function __construct() {
		parent::__construct(array(
			'singular' => 'username_table',
			'plural' => 'username_tables',
			'ajax' => true
			)
		);
	}

	function get_columns() {
		return $columns = array(
			'cb' => '<input type="checkbox">',
			'username' => __('Username'),
			'last_tweet' => __('Last Tweet')
			);
	}

	function get_sortable_columns() {
		return $sortable = array(
			'col_username' => 'username',
			'col_last_tweet' => 'last_tweet'
			);
	}

	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = aray();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $this->get_users();
	}

	function column_default($item, $column_name) {
		switch($column_name) {
			case 'cb':
				break;
			case 'username':
				break;
			case 'last_tweet':
				break;
		}
	}

	function get_users() {
		$args = array(
			'numberposts' => 0,
			'post_type' => 'twits'
			);
		return get_posts($args);
	}
}

?>
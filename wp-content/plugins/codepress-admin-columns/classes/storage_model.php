<?php

/**
 * Storage Model
 *
 * @since 2.0.0
 */
abstract class CPAC_Storage_Model {

	/**
	 * @since 2.0.0
	 */
	public $label;

	/**
	 * Identifier for Storage Model; Posttype etc.
	 *
	 * @since 2.0.0
	 */
	public $key;

	/**
	 * Type of storage model; Post, Media, User or Comments
	 *
	 * @since 2.0.0
	 */
	public $type;

	/**
	 * Groups the storage model in the menu.
	 *
	 * @since 2.0.0
	 */
	public $menu_type;

	/**
	 * @since 2.0.0
	 * @var string
	 */
	public $page;

	/**
	 * @since 2.0.1
	 * @var array
	 */
	protected $columns_filepath;

	/**
	 * @since 2.0.1
	 * @var array
	 */
	public $columns = array();

	/**
	 * @since 2.1.0
	 * @var array
	 */
	public $custom_columns = array();

	/**
	 * @since 2.1.0
	 * @var array
	 */
	public $default_columns = array();

	/**
	 * @since 2.2
	 * @var array
	 */
	public $stored_columns = NULL;

	/**
	 * @since 2.2
	 * @var array
	 */
	public $column_types = array();

	/**
	 * @since 2.0.0
	 * @return array Column Name | Column Label
	 */
	abstract function get_default_columns();

	/**
	 * @since 2.2
	 */
	function __construct() {

		// set columns paths
		$this->set_columns_filepath();

		// Populate columns variable.
		// This is used for manage_value. By storing these columns we greatly improve performance.
		add_action( 'admin_init', array( $this, 'set_columns' ) );
	}

	/**
	 * Checks if menu type is currently viewed
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @return bool
	 */
	public function is_menu_type_current( $first_posttpe ) {

		// display the page that was being viewed before saving
		if ( ! empty( $_REQUEST['cpac_key'] ) ) {
			if ( $_REQUEST['cpac_key'] == $this->key ) {
				return true;
			}

		// settings page has not yet been saved
		} elseif ( $first_posttpe == $this->key ) {
			return true;
		}

		return false;
	}

	/**
	 * @since 2.0.0
	 * @return array
     */
    public function get_meta_keys( $add_hidden_meta = false ) {
        global $wpdb;

        $keys = array();

		$fields = $this->get_meta();

		if ( is_wp_error( $fields ) || empty( $fields ) )
			$keys = false;

		if ( $fields ) {
			foreach ( $fields as $field ) {

				// give hidden fields a prefix for identifaction
				if ( $add_hidden_meta && "_" == substr( $field[0], 0, 1 ) ) {
					$keys[] = 'cpachidden' . $field[0];
				}

				// non hidden fields are saved as is
				elseif ( "_" != substr( $field[0], 0, 1 ) ) {
					$keys[] = $field[0];
				}
			}
		}

		/**
		 * Filter the available custom field meta keys
		 * If showing hidden fields is enabled, they are prefixed with "cpachidden" in the list
		 *
		 * @since 2.0.0
		 *
		 * @param array $keys Available custom field keys
		 * @param CPAC_Storage_Model $storage_model Storage model class instance
		 */
		$keys = apply_filters( 'cac/storage_model/meta_keys', $keys, $this );

		/**
		 * Filter the available custom field meta keys for this storage model type
		 *
		 * @since 2.0.0
		 * @see Filter cac/storage_model/meta_keys
		 */
		return apply_filters( "cac/storage_model/meta_keys/storage_key={$this->key}", $keys, $this );
    }

	/**
	 * @since 2.0.0
	 * @param array $fields Custom fields.
	 * @return array Custom fields.
	 */
	protected function add_hidden_meta( $fields ) {
		if ( ! $fields )
			return false;

		$combined_fields = array();

		// filter out hidden meta fields
		foreach ( $fields as $field ) {

			// give hidden fields a prefix for identifaction
			if ( "_" == substr( $field[0], 0, 1 ) ) {
				$combined_fields[] = 'cpachidden'.$field[0];
			}

			// non hidden fields are saved as is
			elseif ( "_" != substr( $field[0], 0, 1 ) ) {
				$combined_fields[] = $field[0];
			}
		}

		if ( empty( $combined_fields ) )
			return false;

		return $combined_fields;
	}

	/**
	 * @since 2.0.0
	 */
	function restore() {

		delete_option( "cpac_options_{$this->key}" );

		cpac_admin_message( "<strong>{$this->label}</strong> " . __( 'settings succesfully restored.',  'cpac' ), 'updated' );

		// refresh columns otherwise the removed columns will still display
		$this->set_columns();
	}

	/**
	 * @since 2.0.0
	 */
	function store( $columns = '' ) {

		if ( ! empty( $_POST[ $this->key ] ) )
			$columns = array_filter( $_POST[ $this->key ] );

		if( ! $columns ) {
			cpac_admin_message( __( 'No columns settings available.',  'cpac' ), 'error' );
			return false;
		}

		// sanitize user inputs
		foreach ( $columns as $name => $options ) {
			if ( $_column = $this->get_column_by_name( $name ) ) {
				$columns[ $name ] = $_column->sanitize_storage( $options );
			}

			// Santize Label: Need to replace the url for images etc, so we do not have url problem on exports
			// this can not be done by CPAC_Column::sanitize_storage() because 3rd party plugins are not available there
			$columns[ $name ]['label'] = stripslashes( str_replace( site_url(), '[cpac_site_url]', trim( $columns[ $name ]['label'] ) ) );
		}

		// store columns
		$result = update_option( "cpac_options_{$this->key}", $columns );
		$result_default = update_option( "cpac_options_{$this->key}_default", array_keys( $this->get_default_columns() ) );

		// error
		if( ! $result && ! $result_default ) {
			cpac_admin_message( sprintf( __( 'You are trying to store the same settings for %s.', 'cpac' ), "<strong>{$this->label}</strong>" ), 'error' );
			return false;
		}

		cpac_admin_message( sprintf( __( 'Settings for %s updated succesfully.',  'cpac' ), "<strong>{$this->label}</strong>" ), 'updated' );

		// refresh columns otherwise the newly added columns will not be displayed
		$this->set_columns();

		return true;
	}

	/**
	 * Goes through all files in 'classes/column' and includes each file.
	 *
	 * @since 2.0.1
	 * @return array Column Classnames | Filepaths
	 */
	public function set_columns_filepath() {

		$columns  = array(
			'CPAC_Column_Custom_Field' 		=> CPAC_DIR . 'classes/column/custom-field.php',
			'CPAC_Column_ACF_Placeholder' 	=> CPAC_DIR . 'classes/column/acf-placeholder.php',
			'CPAC_Column_Taxonomy' 			=> CPAC_DIR . 'classes/column/taxonomy.php'
		);

		// Directory to iterate
		$columns_dir = CPAC_DIR . 'classes/column/' . $this->type;
		if ( is_dir( $columns_dir ) ) {
			$iterator = new DirectoryIterator( $columns_dir );
			foreach( $iterator as $leaf ) {

				if ( $leaf->isDot() || $leaf->isDir() )
					continue;

				// only allow php files, exclude .SVN .DS_STORE and such
				if ( substr( $leaf->getFilename(), -4 ) !== '.php' ) {
	    			continue;
	    		}

				// build classname from filename
				$class_name = 'CPAC_Column_' . ucfirst( $this->type ) . '_'  . implode( '_', array_map( 'ucfirst', explode( '-', basename( $leaf->getFilename(), '.php' ) ) ) );

				// classname | filepath
				$columns[ $class_name ] = $leaf->getPathname();
			}
		}

		/**
		 * Filter the available custom column types
		 * Use this to register a custom column type
		 *
		 * @since 2.0.0
		 * @param array $columns Available custom columns ([class_name] => [class file path])
		 * @param CPAC_Storage_Model $storage_model Storage model class instance
		 */
		$columns = apply_filters( 'cac/columns/custom', $columns, $this );

		/**
		 * Filter the available custom column types for a specific type
		 *
		 * @since 2.0.0
		 * @see Filter cac/columns/custom
		 */
		$columns = apply_filters( 'cac/columns/custom/type=' . $this->type, $columns, $this );

		/**
		 * Filter the available custom column types for a specific type
		 *
		 * @since 2.0.0
		 * @see Filter cac/columns/custom
		 */
		$columns = apply_filters( 'cac/columns/custom/post_type=' . $this->key, $columns, $this );

		$this->columns_filepath = $columns;
	}

	/**
	 * @since 2.0.0
	 * @param $column_name
	 * @param $label
	 * @return object CPAC_Column
	 */
	public function create_column_instance( $column_name, $label ) {

		// create column instance
		$column = new CPAC_Column( $this );

		$column
			->set_properties( 'type', $column_name )
			->set_properties( 'name', $column_name )
			->set_properties( 'label', $label )
			->set_properties( 'is_cloneable', false )
			->set_properties( 'default', true )
			->set_properties( 'group', 'default' )
			->set_options( 'label', $label )
			->set_options( 'state', 'on' );

		// Hide Label when it contains HTML elements
		if( strlen( $label ) != strlen( strip_tags( $label ) ) ) {
			$column->set_properties( 'hide_label', true );
		}

		// Label empty? Use it's column_name
		if ( ! $label ) {
			$column->set_properties( 'label', ucfirst( $column_name ) );
		}

		return $column;
	}

	/**
	 * @since 2.0.0
	 * @return array Column Type | Column Instance
	 */
	public function get_default_registered_columns() {

		$columns = array();

		// Default columns
		foreach ( $this->get_default_columns() as $column_name => $label ) {

			// checkboxes are mandatory
			if ( 'cb' == $column_name )
				continue;

			$column = $this->create_column_instance( $column_name, $label );

			$columns[ $column->properties->name ] = $column;
		}

		do_action( "cac/columns/registered/default", $columns, $this );
		do_action( "cac/columns/registered/default/storage_key={$this->key}", $columns, $this );

		return $columns;
	}

	/**
	 * @since 2.0.0
	 * @return array Column Type | Column Instance
	 */
	function get_custom_registered_columns() {

		$columns = array();

		foreach ( $this->columns_filepath as $classname => $path ) {
			include_once $path;

			if ( ! class_exists( $classname ) )
				continue;

			$column = new $classname( $this );

			// exlude columns that are not registered based on conditional logic within the child column
			if ( ! $column->properties->is_registered ) {
				continue;
			}

			$columns[ $column->properties->type ] = $column;
		}

		do_action( "cac/columns/registered/custom", $columns, $this );
		do_action( "cac/columns/registered/custom/storage_key={$this->key}", $columns, $this );

		return $columns;
	}

	/**
	 * @since 1.0.0
	 * @param string $key
	 * @return array Column options
	 */
	public function get_default_stored_columns() {

		if ( ! $columns = get_option( "cpac_options_{$this->key}_default" ) )
			return array();

		return $columns;
	}

	/**
	 * @since 1.0.0
	 * @return array Column options
	 */
	public function get_stored_columns() {

		if ( $this->stored_columns !== NULL ) {
			$columns = $this->stored_columns;
		}
		else {
			$columns = $this->get_database_columns();
		}

		$columns = apply_filters( 'cpac/storage_model/stored_columns', $columns, $this );
		$columns = apply_filters( 'cpac/storage_model/stored_columns/storage_key={$this->key}', $columns, $this );

		if ( ! $columns ) {
			return array();
		}

		return $columns;
	}

	public function get_database_columns() {

		return get_option( "cpac_options_{$this->key}" );
	}

	public function set_stored_columns( $columns ) {
		$this->stored_columns = $columns;
	}

	/**
	 * @since 2.0.2
	 * @param bool $ignore_check This will allow (3rd party plugins) to populate columns outside the approved screens.
	 */
	public function set_columns( $ignore_screen_check = false ) {

		// Only set columns on allowed screens
		if ( ! $ignore_screen_check && ! $this->is_doing_ajax() && ! $this->is_columns_screen() && ! $this->is_settings_page() ) {
			return;
		}

		$this->custom_columns = $this->get_custom_registered_columns();
		$this->default_columns = $this->get_default_registered_columns();

		$this->column_types = $this->get_grouped_column_types();

		$this->columns = $this->get_columns();
	}

	public function get_grouped_column_types() {

		$types = array();
		$groups = array_keys( $this->get_column_type_groups() );

		$columns = array_merge( $this->default_columns, $this->custom_columns );

		foreach ( $groups as $group ) {
			$grouptypes = array();

			foreach ( $columns as $index => $column ) {
				if ( $column->properties->group == $group ) {
					$grouptypes[ $index ] = $column;
					unset( $columns[ $index ] );
				}
			}

			$types[ $group ] = $grouptypes;
		}

		return $types;
	}

	public function get_column_type_groups() {

		$groups = array(
			'custom' => __( 'Custom', 'cpac' ),
			'default' => __( 'Default', 'cpac' )
		);

		/**
		 * Filter the available column type groups
		 *
		 * @since 2.3
		 *
		 * @param array $groups Available groups ([groupid] => [label])
		 * @param CPAC_Storage_Model $storage_model_instance Storage model class instance
		 */
		$groups = apply_filters( "cac/storage_model/column_type_groups", $groups, $this );
		$groups = apply_filters( "cac/storage_model/column_type_groups/storage_key={$this->key}", $groups, $this );

		return $groups;
	}

	/**
	 * @since 2.0.2
	 */
	function get_registered_columns() {

		$types = array();

		foreach ( $this->column_types as $grouptypes ) {
			$types = array_merge( $types, $grouptypes );
		}

		return $types;
	}

	/**
	 * @since 2.0.0
	 */
	function get_columns() {

		do_action( 'cac/get_columns', $this );

		$columns = array();

		// get columns
		$default_columns = $this->get_default_columns();

		// @todo check if this solves the issue with not displaying value when using "manage_{$post_type}_posts_columns" at CPAC_Storage_Model_Post
		$registered_columns = $this->get_registered_columns();

		if ( $stored_columns = $this->get_stored_columns() ) {
			$stored_names = array();

			foreach ( $stored_columns as $name => $options ) {
				if ( ! isset( $options['type'] ) ) {
					continue;
				}

				$stored_names[] = $name;

				// In case of a disabled plugin, we will skip column.
				// This means the stored column type is not available anymore.
				if ( ! in_array( $options['type'], array_keys( $registered_columns ) ) ) {
					continue;
				}

				// add an clone number which defines the instance
				$column = clone $registered_columns[ $options['type'] ];
				$column->set_clone( $options['clone'] );

				// repopulate the options, so they contains the right stored options
				$column->populate_options();
				$column->sanitize_label();

				$columns[ $name ] = $column;
			}

			// In case of an enabled plugin, we will add that column.
			// When $diff contains items, it means a default column has not been stored.
			if ( $diff = array_diff( array_keys( $default_columns ), $this->get_default_stored_columns() ) ) {
				foreach ( $diff as $name ) {
					// because of the filter "manage_{$post_type}_posts_columns" the columns
					// that are being added by CPAC will also appear in the $default_columns.
					// this will filter out those columns.
					if ( isset( $columns[ $name ] ) ) {
						continue;
					}

					// is the column registered?
					if ( ! isset( $registered_columns[ $name ] ) ) {
						continue;
					}

					$columns[ $name ] = clone $registered_columns[ $name ];
				}
			}
		}
		// When nothing has been saved yet, we return the default WP columns.
		else {
			foreach ( array_keys( $default_columns ) as $name ) {
				if ( isset( $registered_columns[ $name ] ) ) {
					$columns[ $name ] = clone $registered_columns[ $name ];
				}
			}
		}

		do_action( "cac/columns", $columns );
		do_action( "cac/columns/storage_key={$this->key}", $columns );

		return $columns;
	}

	/**
	 * @since 2.0.0
	 */
	function get_column_by_name( $name ) {

		if ( ! isset( $this->columns[ $name ] ) ) {
			return false;
		}

		return $this->columns[ $name ];
	}

	/**
	 * @since 2.0.0
	 */
	public function add_headings( $columns ) {

		// only add headings on overview screens, to prevent deactivating columns in the Storage Model.
		if ( ! $this->is_columns_screen() ) {
			return $columns;
		}

		if ( ! ( $stored_columns = $this->get_stored_columns() ) ) {
			return $columns;
		}

		$column_headings = array();

		// add mandatory checkbox
		if ( isset( $columns['cb'] ) ) {
			$column_headings['cb'] = $columns['cb'];
		}

		// add active stored headings
		foreach ( $stored_columns as $column_name => $options ) {

			/**
			 * Filter the column headers label for use in a WP_List_Table
			 * Label needs stripslashes() for HTML tagged labels, like icons and checkboxes
			 *
			 * @since 2.0.0
			 * @param string $label Label
			 * @param string $column_name Column name
			 * @param array $options Column options
			 * @param CPAC_Storage_Model $storage_model Storage model class instance
			 */
			$label = apply_filters( 'cac/headings/label', stripslashes( $options['label'] ), $column_name, $options, $this );
			$label = str_replace( '[cpac_site_url]', site_url(), $label );

			$column_headings[ $column_name ] = $label;
		}

		// Add 3rd party columns that have ( or could ) not been stored.
		// For example when a plugin has been activated after storing column settings.
		// When $diff contains items, it means an available column has not been stored.
		if ( $diff = array_diff( array_keys( $columns ), $this->get_default_stored_columns() ) ) {
			foreach ( $diff as $column_name ) {
				$column_headings[ $column_name ] = $columns[ $column_name ];
			}
		}

		// Remove 3rd party columns that have been deactivated.
		// While the column settings have not been stored yet.
		// When $diff contains items, it means the default stored columns are not available anymore.
		// @todo: check if working properly. cuurently issues with woocommerce columns
		/*
		if ( $diff = array_diff( $this->get_default_stored_columns(), array_keys( $columns ) ) ) {
			foreach ( $diff as $column_name ) {
				if( isset( $column_headings[ $column_name ] ) )
					unset( $column_headings[ $column_name ] );
			}
		}*/

		return $column_headings;
	}

	/**
	 * @since 2.0.0
	 * @return string Link
	 */
	protected function get_screen_link() {

		return admin_url( $this->page . '.php' );
	}

	/**
	 * @since 2.0.0
	 */
	function screen_link() {

		echo '<a href="' . $this->get_screen_link() . '" class="add-new-h2">' . __('View', 'cpac') . '</a>';
	}

	/**
	 * @since 2.0.0
	 */
	function get_edit_link() {

		return add_query_arg( array( 'page' => 'codepress-admin-columns', 'cpac_key' => $this->key ), admin_url( 'options-general.php' ) );
	}

	/**
	 * @since 2.0.5
     * @return boolean
	 */
	function is_doing_ajax() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		return false;
	}

	/**
	 * @since 2.0.5
     * @return boolean
	 */
	function is_doing_quick_edit() {
		return $this->is_doing_ajax() && isset( $_REQUEST['action'] ) && 'inline-save' == $_REQUEST['action'];
	}

	/**
	 * @since 2.0.3
	 * @global string $pagenow
     * @global object $current_screen
     * @return boolean
	 */
	function is_columns_screen() {

		global $pagenow;

		if ( $this->page . '.php' != $pagenow )
			return false;

		// posttypes
		if ( 'post' == $this->type ) {
			$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : $this->type;

			if ( $this->key != $post_type )
				return false;
		}

		// taxonomy
		if ( 'taxonomy' == $this->type ) {
			$taxonomy = isset( $_GET['taxonomy'] ) ? $_GET['taxonomy'] : '';

			if ( $this->taxonomy != $taxonomy )
				return false;
		}

		return true;
	}

    /**
     * Checks if the current page is the settings page
     *
     * @since 2.0.2
     * @global string $pagenow
     * @global string $plugin_page
     * @return boolean
     */
    public function is_settings_page() {
        global $pagenow, $plugin_page;

        return 'options-general.php' == $pagenow && ! empty( $plugin_page ) && 'codepress-admin-columns' == $plugin_page;
    }

    /**
     * @since 2.1.1
     */
    public function get_general_option( $option ) {
    	$options = get_option( 'cpac_general_options' );

    	if ( ! isset( $options[ $option ] ) )
    		return false;

    	return $options[ $option ];
    }
}
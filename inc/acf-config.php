<?php
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
		'key' => 'group_5ad89ce9b0218',
		'title' => 'UUID Project sheet',
		'fields' => array (
			array (
				'key' => 'field_5ad89d0630681',
				'label' => 'Identifiant de la fiche projet',
				'name' => 'uuid_project_sheet',
				'type' => 'text',
				'instructions' => 'Insérer l\'identifiant (UUID) de la fiche projet',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'aeris-project-sheets',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => array (
			0 => 'the_content',
			1 => 'excerpt',
			2 => 'custom_fields',
			3 => 'discussion',
			4 => 'comments',
			5 => 'slug',
			6 => 'author',
			7 => 'format',
			8 => 'page_attributes',
			9 => 'featured_image',
			10 => 'categories',
			11 => 'tags',
			12 => 'send-trackbacks',
		),
		'active' => 1,
		'description' => '',
	));
	

	/**
	 * Add column custom
	 * Render the custom form fields for the ACF fields to the "Quick Edit" menu
	 * source : https://sites.google.com/site/tessaleetutorials/home/add-acf-fields-to-quick-edit
	 */

	// Add the columns for the "aeris-project-sheets" post type
	add_filter('manage_edit-aeris-project-sheets_columns', 'aeris_wppl_project_sheets_add_acf_columns');
	function aeris_wppl_project_sheets_add_acf_columns($columns) {
	$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'uuid_project_sheet' => 'UUID',
			'date' => 'Date',
		);
	return $columns;
	}
	
	// Render the custom columns for the "aeris-project-sheets" post type
	add_action('manage_aeris-project-sheets_posts_custom_column', 'aeris_wppl_project_sheets_render_acf_columns', 10, 2);
	function aeris_wppl_project_sheets_render_acf_columns($column_name) {
		global $post;
		switch ($column_name) {
			case 'uuid_project_sheet':
			$uuidProjectSheet = get_field($column_name, $post->ID);
			if(!empty($uuidProjectSheet)) {
				echo(sprintf( '<span class="acf-field %s">%s</span>', $column_name, $uuidProjectSheet ) );
			}
			break;
		}
	}

	/**
	 * Render the custom form fields for the ACF fields to the "Quick Edit" menu
	 * official source : https://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box
	 * 
	 * source : https://wpdreamer.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/#add_to_bulk_quick_edit
	 */

	add_action( 'quick_edit_custom_box', 'aeris_wppl_project_sheets_add_to_bulk_quick_edit_custom_box', 10, 2 );
	function aeris_wppl_project_sheets_add_to_bulk_quick_edit_custom_box( $column_name, $post_type ) {
		switch ( $post_type ) {
			case 'aeris-project-sheets':

				switch( $column_name ) {
					case 'uuid_project_sheet':
					?><fieldset class="inline-edit-col-right">
						<div class="inline-edit-group">
							<label>
								<span class="title">UUID</span>
								<input type="text" name="uuid_project_sheet" value="" />
							</label>
						</div>
					</fieldset><?php
					break;
				}
				break;

		}
	}

	// Saving Data of "quick edit" field
	add_action( 'save_post', 'aeris_wppl_project_sheets_save_uuid' );

	function aeris_wppl_project_sheets_save_uuid( $post_id ) {
		/* in production code, $slug should be set only once in the plugin,
		preferably as a class property, rather than in each function that needs it.
		*/
		$slug = 'aeris-project-sheets';
		if ( $slug !== $_POST['post_type'] ) {
			return;
		}
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$_POST += array("{$slug}_edit_nonce" => '');
		if ( !wp_verify_nonce( $_POST["{$slug}_edit_nonce"],
							plugin_basename( __FILE__ ) ) )
		{
			return;
		}

		if ( isset( $_REQUEST['uuid_project_sheet'] ) ) {
			update_post_meta( $post_id, 'uuid_project_sheet', $_REQUEST['uuid_project_sheet'] );
		}
	}

	// Settings existing values

	/* load script in the footer */
		
	add_action( 'admin_print_scripts-edit.php', 'aeris_wppl_project_sheets_enqueue_edit_scripts' );
	function aeris_wppl_project_sheets_enqueue_edit_scripts() {
		wp_enqueue_script( 'aeris_wppl_project_sheets-admin-edit', plugins_url('js/aeris-project-sheets.js', __FILE__), array( 'jquery', 'inline-edit-post' ), '', true );
	}
		
		
	// Save "quick Edit" data
	add_action( 'save_post','aeris_wppl_project_sheets_save_post', 10, 2 );
	function aeris_wppl_project_sheets_save_post( $post_id, $post ) {

		// don't save for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// dont save for revisions
		if ( isset( $post->post_type ) && $post->post_type == 'revision' )
			return $post_id;

		switch( $post->post_type ) {

			case 'aeris-project-sheets':

				// release date
			// Because this action is run in several places, checking for the array key keeps WordPress from editing
				// data that wasn't in the form, i.e. if you had this post meta on your "Quick Edit" but didn't have it
				// on the "Edit Post" screen.
			if ( array_key_exists( 'uuid_project_sheet', $_POST ) )
				update_post_meta( $post_id, 'uuid_project_sheet', $_POST[ 'uuid_project_sheet' ] );

			break;

		}

	}


endif;


?>
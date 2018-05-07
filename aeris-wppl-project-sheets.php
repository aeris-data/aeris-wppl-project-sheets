<?php 
/**
* Plugin Name: Aeris Project Sheets
* Plugin URI : https://github.com/aeris-data/aeris-wppl-project-sheets
* Text Domain: aeris-wppl-project-sheets
* Domain Path: /languages
* Description: Manage AERIS projects sheets
* Author: Pierre VERT
* Version: 1.1.1
* GitHub Plugin URI: aeris-data/aeris-wppl-project-sheets
* GitHub Branch:     master
*/

// ====================================================================================
/** 
* REGISTER Custom Post Type (cpt)
*/
function aeris_wppl_project_sheets_cpt() {
    register_post_type( 
        'aeris-project-sheets', 							
        array(
            'label' => __('Project sheet', 'aeris-wppl-project-sheets'),			
            'labels' => array(    			
                'name' => __('Projects sheets', 'aeris-wppl-project-sheets'),
                'singular-name' => __('project sheet', 'aeris-wppl-project-sheets'),
                'all_items' => __('All projects', 'aeris-wppl-project-sheets'),
                'add_new_item' => __('Add new project', 'aeris-wppl-project-sheets'),
                'edit_item' => __('Edit project', 'aeris-wppl-project-sheets'),
                'new_item' => __('New project', 'aeris-wppl-project-sheets'),
                'view_item' => __('View project', 'aeris-wppl-project-sheets'),
                'search_item' => __('Search project', 'aeris-wppl-project-sheets'),
                'not_found' => __('No project found', 'aeris-wppl-project-sheets'),
                'not_found_in_trash' => __('No project found in trash', 'aeris-wppl-project-sheets')
            ),
            'public' => true, 				
            'show_in_rest' => true,         
            'capability_type' => 'post',
            // rewrite URL 
            'rewrite' => array( 'slug' => __('project', 'aeris-wppl-project-sheets')),
            'supports' => array(			
                'title',
            ),
            'has_archive' => true, 
            // Url vers une icone ou identifiant à choisir parmi celles de WP : https://developer.wordpress.org/resource/dashicons/.
            'menu_icon'   => 'dashicons-media-document'
        ) 
    );
}
add_action('init', 'aeris_wppl_project_sheets_cpt');

// ====================================================================================
/*
* PLUGIN ACTIVATION / DESACTIVATION
* WARNING !! Flush rewrite rules function is an expensive operation ! use only on activation/desactivation plugin 
* source : https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
*/

function aeris_wppl_project_sheets_flush_rewrites() {
    // call your CPT registration function here (it should also be hooked into 'init')
    aeris_wppl_project_sheets_cpt();
    // clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'aeris_wppl_project_sheets_flush_rewrites' );

function aeris_wppl_project_sheets_deactivation() {
    // unregister the post type, so the rules are no longer in memory
    unregister_post_type( 'aeris-project-sheets' );
    // clear the permalinks to remove our post type's rules from the database
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'aeris_wppl_project_sheets_deactivation' );

// ====================================================================================
/* 
* LOAD TEXT DOMAIN FOR TEXT TRANSLATIONS
*/

function aeris_wppl_project_sheets_load_plugin_textdomain() {
    $domain = 'aeris-wppl-project-sheets';
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    // wp-content/languages/plugin-name/plugin-name-fr_FR.mo
    load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
    // wp-content/plugins/plugin-name/languages/plugin-name-fr_FR.mo
    load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'aeris_wppl_project_sheets_load_plugin_textdomain' );

// ====================================================================================
/*
* REGISTER TPL SINGLE
*/
add_filter ( 'single_template', 'aeris_wppl_project_sheets_single' );
function aeris_wppl_project_sheets_single($single_template) {
    global $post;
    
    if ($post->post_type == 'aeris-project-sheets') {
        $single_template = plugin_dir_path ( __FILE__ ) . 'single-aeris-project-sheets.php';
    }
    return $single_template;
}
// ====================================================================================

?>
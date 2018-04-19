<?php 
/**
* Plugin Name: Aeris Project Sheets
* Plugin URI : https://github.com/aeris-data/aeris-wppl-project-sheets
* Description: Plugin pour gérer les fiches projets AERIS
* Author: Pierre VERT
* Version: 0.1.0
* GitHub Plugin URI: aeris-data/aeris-wppl-project-sheets
* GitHub Branch:     master
*/

// include 'inc/commons.php';

function aeris_wppl_project_sheets_plugin_init(){

    /* Gestion de la dépendance de ACF */
	if ( ! function_exists('get_field') && current_user_can( 'activate_plugins' ) ) {
        
        add_action( 'admin_init', 'sb_plugin_deactivate');
        add_action( 'admin_notices', 'sb_plugin_admin_notice');

        //Désactiver le plugin
        function sb_plugin_deactivate () {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
        
        // Alerter pour expliquer pourquoi il ne s'est pas activé
        function sb_plugin_admin_notice () {
            
            echo '<div class="error">Le plugin "Aeris Project Sheets" requiert ACF pour fonctionner <br><strong>Activez ACF ci-dessous</strong> ou <a href=https://wordpress.org/plugins/advanced-custom-fields/> Téléchargez ACF &raquo;</a><br></div>';

            if ( isset( $_GET['activate'] ) ) 
                unset( $_GET['activate'] );	
        }

    } else {
    // Le plugin est activé 

    //require_once 'inc/acf-config.php';
    
        /** 
        * Création du custom post type (cpt)
        */
        add_action('init', 'aeris_wppl_project_sheets_cpt');
        function aeris_wppl_project_sheets_cpt() {
            register_post_type( 
                'aeris-project-sheets', 							
                array(
                    'label' => 'Fiches projet', 			
                    'labels' => array(    			
                        'name' => 'Fiches projet',
                        'singular-name' => 'fiche projet',
                        'all_items' => 'Toutes les fiches projet',
                        'add_new_item' => 'Ajouter une fiche projet',
                        'edit_item' => 'Editer la fiche projet',
                        'new_item' => 'Nouveau fiche projet',
                        'view_item' => 'Voir la fiche projet',
                        'search_item' => 'Rechercher parmis les Fiches projet',
                        'not_found' => 'Pas de fiche projet trouvée',
                        'not_found_in_trash' => 'Pas de fiche projet dans la corbeille'
                    ),
                    'public' => true, 				
                    'show_in_rest' => true,         
                    'capability_type' => 'post',	
                    'supports' => array(			
                        'title',
                        // 'author',
                        //'editor'	
                    ),
                    'has_archive' => true, 
                    // Url vers une icone ou à choisir parmi celles de WP : https://developer.wordpress.org/resource/dashicons/.
                    'menu_icon'   => 'dashicons-media-document'
                ) 
            );
        }

        
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
        
    
    } // end test plugin ACF

}
add_action('plugins_loaded', 'aeris_wppl_project_sheets_plugin_init');
?>
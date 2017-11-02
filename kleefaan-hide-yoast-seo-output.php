<?php

/*
Plugin Name: Hide Yoast SEO admin output for non-admins
Plugin URI: https://kleefaan.nl
Description: Hides stuff from the Yoast SEO plugin for other users then administrators. Extremely useful for clients that do not need to have SEO meta fields/columns visible on their admin.
Author: Sander Langendoen
Version: 0.1
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class kleefaan_wp_hide_yoast_stuff {
    public function __construct(){
        add_action( 'plugins_loaded', array( $this, 'kleefaan_check_if_user_logged_in' ) );
    }

    public function kleefaan_check_if_user_logged_in(){
        
        // disable SEO meta box for others then specific capabilities
        if (!current_user_can('activate_plugins')) {

            function kleefaan_yoast_metabox_hide() {
                    // other CPT then the defaults 
                    remove_meta_box('wpseo_meta', 'page', 'normal');
                    remove_meta_box('wpseo_meta', 'post', 'normal');
            }

            add_action('add_meta_boxes', 'kleefaan_yoast_metabox_hide', 99);
        }

        // hide the YOAST Elements publish meta box
        function kleefaan_hide_yoast_seo_elements_publish_meta_box() {
            if (!current_user_can('activate_plugins')) { ?>
                <style type="text/css">
                    .misc-pub-section.yoast-seo-score { display: none; }
                </style>
            <?php }
        }
        if (!current_user_can('activate_plugins')) {
            add_action( 'admin_enqueue_scripts', 'kleefaan_hide_yoast_seo_elements_publish_meta_box' );
        }

        // hide the YOAST Admin Bar Tool (on top of CMS admin)
        function kleefaan_hide_yoast_admin_bar() {
            if (!current_user_can('activate_plugins')) { ?>
                <style type="text/css">
                    #wp-admin-bar-root-default #wp-admin-bar-wpseo-menu { display: none; }
                </style>
            <?php }
        }
        if (!current_user_can('activate_plugins')) {
            add_action( 'admin_enqueue_scripts', 'kleefaan_hide_yoast_admin_bar' );
        }

        // Remove SEO Yoast Columns from admin
        if (!current_user_can('activate_plugins')) {
            function kleefaan_remove_yoast_columns( $columns ) {
                // remove the Yoast SEO columns
                unset( $columns['wpseo-score'] );
                unset( $columns['wpseo-title'] );
                unset( $columns['wpseo-metadesc'] );
                unset( $columns['wpseo-focuskw'] );
                unset( $columns['wpseo-score-readability'] );
                unset( $columns['wpseo-links']);
                return $columns;
            }
            add_filter ( 'manage_edit-post_columns', 'kleefaan_remove_yoast_columns' );
            add_filter ( 'manage_edit-page_columns', 'kleefaan_remove_yoast_columns' );
            // add_filter ( 'manage_edit-product_columns', 'kleefaan_remove_yoast_columns' ); // if Woo enabled
            // add_filter ( 'manage_edit-cpt_columns', 'kleefaan_remove_yoast_columns' ); // other custom post types

        }

        // disable the Yoast notifications after each upgrade
        function kleefaan_disable_yoast_notifications() {
          if (!current_user_can('activate_plugins')) {
            remove_action('admin_notices', array(Yoast_Notification_Center::get(), 'display_notifications'));
            remove_action('all_admin_notices', array(Yoast_Notification_Center::get(), 'display_notifications'));
          }
        }

        add_action('admin_init', 'kleefaan_disable_yoast_notifications');


        // hide the Yoast SEO dashboard widget
        function remove_wpseo_dashboard_overview() {
          // In some cases, you may need to replace 'side' with 'normal' or 'advanced'.
          remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );

        }
        add_action('wp_dashboard_setup', 'remove_wpseo_dashboard_overview' );
    }
}


$kleefaan_wp_hide_yoast_stuff = new kleefaan_wp_hide_yoast_stuff();


// end plugin ?>
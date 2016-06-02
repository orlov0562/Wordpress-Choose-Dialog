<?php defined('ABSPATH') or die('Direct access forbidden');

    add_action('admin_menu', function(){
        if (is_super_admin()) {

            add_menu_page( 'PostChooseDialog',
                           'Post Choose Dialog',
                           'delete_users',
                           'post-choose-dialog'
            );

            add_submenu_page(
                'post-choose-dialog',
                'Post Choose Dialog',
                'Post Choose Dialog',
                'delete_users',
                'post-choose-dialog',
                function() {
                    include dirname(__FILE__).'/sections/index.php';
                }
            );
        }
    });

    include dirname(__FILE__).'/ajax.php';
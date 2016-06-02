<?php defined('ESC_PLUGIN_PATH') or die('Direct access forbidden');

    // -----------------------------------------------------------------------------------------------
    // Post choose dialog

    add_action( 'wp_ajax_post_choose_dialog', function(){
        global $wpdb;

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "post_choose_dialog")) {
            exit("No naughty business please");
        }

        $items = $wpdb->get_results($wpdb->prepare(
            'SELECT * FROM '.$wpdb->posts.' WHERE
                `post_status`="publish" AND (`post_type`="post" OR `post_type`="page")
                 AND (
                    `ID` = %d
                    OR
                    `post_title` LIKE "%%%s%%"
                )
             ORDER BY `id` DESC

             LIMIT 10
            ',
            trim($_POST['query']),
            trim($_POST['query'])
        ));

        $ret = [];

        if ($items) {
            foreach($items as $item) {
                $ret[] = [
                    'id' => $item->ID,
                    'title' => $item->post_title,
                ];
            }
        }
        wp_send_json($ret);

        wp_die();
    });

    // -----------------------------------------------------------------------------------------------
    // Category choose dialog

    add_action( 'wp_ajax_cat_choose_dialog', function(){
        global $wpdb;

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "cat_choose_dialog")) {
            exit("No naughty business please");
        }

        $items = $wpdb->get_results($wpdb->prepare(
            'SELECT
                `terms`.`name` as `title`,
                `terms`.`term_id` as `id`,
                `tax`.`parent` as `parent`
            FROM '.$wpdb->terms.' AS `terms`
            LEFT JOIN '.$wpdb->term_taxonomy.' AS `tax`
                ON `tax`.`term_id` = `terms`.`term_id`
            WHERE
                `tax`.`taxonomy` = "category"
                 AND (
                    `terms`.`term_id` = %d
                    OR
                    `terms`.`name` LIKE "%%%s%%"
                    OR
                    `terms`.`slug` LIKE "%%%s%%"
                )
             ORDER BY `tax`.`parent` DESC

             LIMIT 10
            ',
            trim($_POST['query']),
            trim($_POST['query']),
            trim($_POST['query'])
        ));

        $ret = [];

        if ($items) {
            foreach($items as $item) {
                $title = [$item->title];

                if ($item->parent) {
                    $parentId = $item->parent;
                    do {
                        $parent = $wpdb->get_row($wpdb->prepare(
                            'SELECT
                                `terms`.`name` as `title`,
                                `terms`.`term_id` as `id`,
                                `tax`.`parent` as `parent`
                            FROM '.$wpdb->term_taxonomy.' AS `tax`
                            LEFT JOIN '.$wpdb->terms.' AS `terms`
                                ON `tax`.`term_id` = `terms`.`term_id`
                            WHERE
                                `term_taxonomy_id` = %d
                            LIMIT 1
                            ', $parentId
                        ));

                        if ($parent) {
                            $title[] = $parent->title;
                            $parentId = $parent->parent;
                        }

                    } while($parent);
                }

                $ret[] = [
                    'id' => $item->id,
                    'title' => implode(' &gt; ', array_reverse($title)),
                ];
            }
        }

        wp_send_json($ret);

        wp_die();
    });

    // -----------------------------------------------------------------------------------------------
<?php
    defined('ESC_PLUGIN_PATH') or die('Direct access forbidden');

    class esc {
        function admin_url($sectionId, $params=[]) {
            $query = array_merge(['page'=>'esc-'.$sectionId], $params);
            return esc_url(add_query_arg($query, admin_url('admin.php')));
        }

        /**
         * Allowed types: error / info / success
         */
        function print_messages(array $messages, $type) {
            $output = '';
            if (!$messages) return $output;
            $output .= '<div id="message" class="notice notice-'.esc_attr($type).' is-dismissible">';
            if (count($messages)>1) {
                $output .= '<ul><li>'.implode('</li><li>',$messages).'</li></ul>';
            } else {
                $output .= '<p>'.$messages[0].'</p>';
            }
            $output .= '<button class="notice-dismiss" type="button"></button></div>';
            return $output;
        }

        function tr_max_result($val) {
            $ret = 'n/a';
            switch($val) {
                case 'winner': $ret = 'Победитель'; break;
                case 'second': $ret = 'Второе место'; break;
                case 'third': $ret = 'Третье место'; break;
                case 'final': $ret = 'Финал'; break;
                case 'semifinal': $ret = 'Полуфинал'; break;
            }
            return $ret;
        }

        function post($var, $default='') {
            return isset($_POST[$var]) ? trim($_POST[$var]) : $default;
        }

        function get($var, $default='') {
            return isset($_GET[$var]) ? trim($_GET[$var]) : $default;
        }

        function request($var, $default='') {
            return isset($_REQUEST[$var]) ? trim($_REQUEST[$var]) : $default;
        }

        function html_post($var, $default='') {
            return esc_html(self::post($var, $default));
        }

        function html_get($var, $default='') {
            return esc_html(self::get($var, $default));
        }

        function html_request($var, $default='') {
            return esc_html(self::request($var, $default));
        }

        function post_title($postId, $default=''){
            $ret = $default;
            if (intval($postId)) {
                if ($res = $wpdb->get_var($wpdb->prepare('SELECT `post_title` FROM '.$wpdb->posts.' WHERE `ID` = %d LIMIT 1', $postId))) {
                    $ret = $res;
                }
            }
            return $ret;
        }

        function cat_title($catId, $default=''){
            $ret = $default;
            if (intval($catId)) {
                if ($res = get_the_category_by_ID($catId) && !is_wp_error($res)) {
                    $ret = $res;
                }
            }
            return $ret;
        }

    }

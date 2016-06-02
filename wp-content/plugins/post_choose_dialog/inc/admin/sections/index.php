<?php defined('ESC_PLUGIN_PATH') or die('Direct access forbidden'); ?>

<?php


    function getPostVar($var, $default=null) { return isset($_POST[$var]) ? $_POST[$var] : $default;}

    function getPostTitleByVar($var) {
        global $wpdb;
        $res = $wpdb->get_var($wpdb->prepare('SELECT `post_title` FROM '.$wpdb->posts.' WHERE `ID`=%d LIMIT 1', getPostVar($var, 0)));
        return $res ? $res : '';
    }

    function getCatByVar($var) {
        $ret = '';
        if ($catId = getPostVar($var)) {
            if (($res = get_the_category_by_ID($catId)) && !is_wp_error($res)) {
                $ret = $res;
            }
        }
        return $ret;
    }

?>

<div class="wrap">
    <h1>Post Choose Dialog Example</h1>

        <div class="form-wrap" style="width: 400px;">
            <?php if (!empty($_POST['submit'])):?>
                <pre><?php print_r($_POST); ?></pre>
            <?php endif; ?>
            <h2>Example form</h2>
                <form method="post">
                <div class="form-field form-required term-name-wrap">
                    <label for="example1_post_id_title">Выбор поста 1</label>
                    <input id="example1_post_id" type="hidden" name="example1_post_id" value="<?=esc_attr(getPostVar('example1_post_id'))?>" >
                    <input id="example1_post_id_title" type="text" readonly value="<?=esc_attr(getPostTitleByVar('example1_post_id'))?>" onclick="PostChooserDialog.show('example1_post_id', 'example1_post_id_title');">
                </div>

                <div class="form-field form-required term-name-wrap">
                    <label for="example2_post_id_title">Выбор поста 2</label>
                    <input id="example2_post_id" type="hidden" name="example2_post_id" value="<?=esc_attr(getPostVar('example2_post_id'))?>" >
                    <input id="example2_post_id_title" type="text" readonly value="<?=esc_attr(getPostTitleByVar('example2_post_id'))?>" onclick="PostChooserDialog.show('example2_post_id', 'example2_post_id_title');">
                </div>

                <div class="form-field form-required term-name-wrap">
                    <label for="example_cat_id_title">Выбор категории</label>
                    <input id="example_cat_id" type="hidden" name="example_cat_id" value="<?=esc_attr(getPostVar('example_cat_id'))?>" >
                    <input id="example_cat_id_title" type="text" readonly value="<?=esc_attr(getCatByVar('example_cat_id'))?>" onclick="CatChooserDialog.show('example_cat_id', 'example_cat_id_title'); ">
                </div>

                <p class="submit">
                    <input id="submit" class="button button-primary" type="submit" value="Проверка" name="submit"></button>
                </p>


                </form>
        </div>

</div>

<div class="clear"></div>

<?php include(ESC_PLUGIN_PATH.'/inc/admin/post_choose_dialog.php'); ?>
<?php include(ESC_PLUGIN_PATH.'/inc/admin/cat_choose_dialog.php'); ?>
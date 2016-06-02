<?php defined('ESC_PLUGIN_PATH') or die('Direct access forbidden'); ?>

<script>

    var PostChooserDialog = {};

    PostChooserDialog.esc_attr = function(s, preserveCR) {
    preserveCR = preserveCR ? '&#13;' : '\n';
    return ('' + s) /* Forces the conversion to string. */
        .replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
        .replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        /*
        You may add other replacements here for HTML only
        (but it's not necessary).
        Or for XML, only if the named entities are defined in its DTD.
        */
        .replace(/\r\n/g, preserveCR) /* Must be before the next replacement. */
        .replace(/[\r\n]/g, preserveCR);
        ;
}

    PostChooserDialog.show = function(retToId, retToTitle) {
        jQuery('#post-choose-dialog-background').show();
        jQuery('#post-choose-panel').attr('retToId', retToId);
        jQuery('#post-choose-panel').attr('retToTitle', retToTitle);
        jQuery('#post-choose-panel').show();

        if(jQuery('#'+retToId).val()) jQuery('#post-choose-dialog-filter').val(jQuery('#'+retToTitle).val());

        PostChooserDialog.findPosts(
            jQuery('#post-choose-dialog-filter').val()
        );
    };

    PostChooserDialog.returnPostId = function(postId, postTitle){
        var retToId = jQuery('#'+jQuery('#post-choose-panel').attr('retToId'));
        if (retToId.length) retToId.val(postId);

        var retToTitle = jQuery('#'+jQuery('#post-choose-panel').attr('retToTitle'));
        if (retToTitle.length) retToTitle.val(postTitle);

        jQuery('#post-choose-dialog-background').hide();
        jQuery('#post-choose-panel').hide();
    };

    PostChooserDialog.lazyTimer = null;

    PostChooserDialog.findPostsLazy = function(query) {

        if (PostChooserDialog.waitForResponse) return;

        if (PostChooserDialog.lazyTimer) clearTimeout(PostChooserDialog.lazyTimer);

        PostChooserDialog.lazyTimer = setTimeout(function(){
            PostChooserDialog.findPosts(query);
        }, 1000);
    };

    PostChooserDialog.waitForResponse = null;

    PostChooserDialog.findPosts = function(query){
        if (PostChooserDialog.waitForResponse) return;

        PostChooserDialog.waitForResponse = true;
        jQuery('#post-choose-dialog-results').html('Поиск..');

        var data = {
                'action': 'post_choose_dialog',
                'query': query,
                'nonce': '<?=wp_create_nonce('post_choose_dialog')?>'
        };

        jQuery.post(ajaxurl, data, function(response) {
            if (response.length<1) {
                jQuery('#post-choose-dialog-results').html('Нет подходящих результатов');
            } else {
                var html = '';
                for (var i=0; i<response.length; i++) {
                    html += '<div style="padding:10px 5px; border-bottom: 1px dotted silver;">'
                    html += '<span style="display:inline-block; width:30px; margin-right:10px;">'+response[i].id+'</span>';
                    html += '<a href="#" onclick="PostChooserDialog.returnPostId('+response[i].id+', \''+PostChooserDialog.esc_attr(response[i].title)+'\'); return false;">'+response[i].title+'</a>';
                    html += '</div>';
                }
                jQuery('#post-choose-dialog-results').html(html);
            }
        }).fail(function() {
            jQuery('#post-choose-dialog-results').html(
                '<div style="padding:5px 10px;">' +
                    '<div style="color:red; margin-bottom:5px;">Во время запроса произошла ошибка.</div>' +
                    '<button class="button" onclick="PostChooserDialog.findPosts(\''+query+'\'); return false;">Повторить запрос</button>' +
                '</div>'
            );
        }).always(function() {
            PostChooserDialog.waitForResponse = null;
        });
    };

    PostChooserDialog.init = function(){
        PostChooserDialog.initWrapper();
        PostChooserDialog.initDialogPanel();
    };

    PostChooserDialog.initWrapper = function(){
        jQuery("body").append('<div id="post-choose-dialog-background"></div>');

        var wrapper = jQuery('#post-choose-dialog-background');

        wrapper.css('display', 'none');
        wrapper.css('position', 'fixed');
        wrapper.css('z-index', '1000');
        wrapper.css('top', '0');
        wrapper.css('left', '0');
        wrapper.css('width', '100%');
        wrapper.css('height', '100%');
        wrapper.css('min-height', '500px');
        wrapper.css('background-color', '#000');
        wrapper.css('opacity', '0.85');
        wrapper.click(function(){
            jQuery('#post-choose-panel').hide();
            jQuery(this).hide();

        });
    };

    PostChooserDialog.initDialogPanel = function(){
        var html = '<div id="post-choose-panel">';
            html += '<div style="height:85px;">';
                html += '<h2 style="margin:0px 0px 10px 0px;">Выберите пост</h2>';
                html += '<div>Фильтр<br><input type="text" value="" id="post-choose-dialog-filter" style="width:100%;"></div>';
            html += '</div>';
            html += '<div id="post-choose-dialog-results" style="height: calc(100% - 95px); overflow-x:hidden; overflow-y:auto; background-color: white; border:1px solid silver; padding:5px;"></div>';
        html += '</div>';

        jQuery("body").append(html);

        var panel = jQuery('#post-choose-panel');

        panel.css('display', 'none');
        panel.css('position', 'fixed');
        panel.css('z-index', '1500');
        panel.css('top', '20%');
        panel.css('left', '25%');
        panel.css('width', '50%');
        panel.css('height', '50%');
        panel.css('min-height', '450px');
        panel.css('background-color', '#F1F1F1');
        panel.css('border', '1px solid gray;');
        panel.css('padding', '20px');

        jQuery('#post-choose-dialog-filter').keyup(function(){
            PostChooserDialog.findPostsLazy(
                jQuery('#post-choose-dialog-filter').val()
            );
        });
    };

    jQuery(function() {
       PostChooserDialog.init();
    });
</script>
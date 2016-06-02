<?php defined('ESC_PLUGIN_PATH') or die('Direct access forbidden'); ?>

<script>

    var CatChooserDialog = {};

    CatChooserDialog.esc_attr = function(s, preserveCR) {
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


    CatChooserDialog.show = function(retToId, retToTitle) {
        jQuery('#cat-choose-dialog-background').show();
        jQuery('#cat-choose-panel').attr('retToId', retToId);
        jQuery('#cat-choose-panel').attr('retToTitle', retToTitle);
        jQuery('#cat-choose-panel').show();

        if(jQuery('#'+retToTitle).length && jQuery('#'+retToTitle).val()) {
            var cat = jQuery('#'+retToTitle).val();
            console.log(cat);
            if(cat.lastIndexOf('>')>-1) {
                cat = cat.substring(cat.lastIndexOf('>')+1).trim();
            }
            jQuery('#cat-choose-dialog-filter').val(cat);
        }

        CatChooserDialog.findCats(
            jQuery('#cat-choose-dialog-filter').val()
        );
    };

    CatChooserDialog.returnCatId = function(postId, postTitle){
        var retToId = jQuery('#'+jQuery('#cat-choose-panel').attr('retToId'));
        if (retToId.length) retToId.val(postId);

        var retToTitle = jQuery('#'+jQuery('#cat-choose-panel').attr('retToTitle'));
        if (retToTitle.length) retToTitle.val(postTitle);

        jQuery('#cat-choose-dialog-background').hide();
        jQuery('#cat-choose-panel').hide();
    };

    CatChooserDialog.lazyTimer = null;

    CatChooserDialog.findCatsLazy = function(query) {

        if (CatChooserDialog.waitForResponse) return;

        if (CatChooserDialog.lazyTimer) clearTimeout(CatChooserDialog.lazyTimer);

        CatChooserDialog.lazyTimer = setTimeout(function(){
            CatChooserDialog.findCats(query);
        }, 1000);
    };

    CatChooserDialog.waitForResponse = null;

    CatChooserDialog.findCats = function(query){
        if (CatChooserDialog.waitForResponse) return;

        CatChooserDialog.waitForResponse = true;
        jQuery('#cat-choose-dialog-results').html('<p>Поиск..</p>');

        var data = {
                'action': 'cat_choose_dialog',
                'query': query,
                'nonce': '<?=wp_create_nonce('cat_choose_dialog')?>'
        };

        jQuery.post(ajaxurl, data, function(response) {
            if (response.length<1) {
                jQuery('#cat-choose-dialog-results').html('<p>Нет подходящих результатов</p>');
            } else {
                var html = '';
                for (var i=0; i<response.length; i++) {
                    html += '<div style="padding:10px 5px; border-bottom: 1px dotted silver;">'
                    html += '<span style="display:inline-block; width:30px; margin-right:10px;">'+response[i].id+'</span>';
                    html += '<a href="#" onclick="CatChooserDialog.returnCatId('+response[i].id+', \''+CatChooserDialog.esc_attr(response[i].title)+'\'); return false;">'+response[i].title+'</a>';
                    html += '</div>';
                }
                jQuery('#cat-choose-dialog-results').html(html);
            }
        }).fail(function() {
            jQuery('#cat-choose-dialog-results').html(
                '<div style="padding:5px 10px;">' +
                    '<div style="color:red; margin-bottom:5px;">Во время запроса произошла ошибка.</div>' +
                    '<button class="button" onclick="CatChooserDialog.findCats(\''+query+'\'); return false;">Повторить запрос</button>' +
                '</div>'
            );
        }).always(function() {
            CatChooserDialog.waitForResponse = null;
        });
    };

    CatChooserDialog.init = function(){
        CatChooserDialog.initWrapper();
        CatChooserDialog.initDialogPanel();
    };

    CatChooserDialog.initWrapper = function(){
        jQuery("body").append('<div id="cat-choose-dialog-background"></div>');

        var wrapper = jQuery('#cat-choose-dialog-background');

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
            jQuery('#cat-choose-panel').hide();
            jQuery(this).hide();

        });
    };

    CatChooserDialog.initDialogPanel = function(){
        var html = '<div id="cat-choose-panel">';
            html += '<div style="height:85px;">';
                html += '<h2 style="margin:0px 0px 10px 0px;">Выберите категорию</h2>';
                html += '<div>Фильтр<br><input type="text" value="" id="cat-choose-dialog-filter" style="width:100%;"></div>';
            html += '</div>';
            html += '<div id="cat-choose-dialog-results" style="height: calc(100% - 95px); overflow-x:hidden; overflow-y:auto; background-color: white; border:1px solid silver; padding:5px;"></div>';
        html += '</div>';

        jQuery("body").append(html);

        var panel = jQuery('#cat-choose-panel');

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

        jQuery('#cat-choose-dialog-filter').keyup(function(){
            CatChooserDialog.findCatsLazy(
                jQuery('#cat-choose-dialog-filter').val()
            );
        });
    };

    jQuery(function() {
       CatChooserDialog.init();
    });
</script>
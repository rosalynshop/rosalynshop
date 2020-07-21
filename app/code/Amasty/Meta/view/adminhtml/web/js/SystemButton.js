define([
    "jquery",
    "jquery/ui"
], function ($) {

    $.widget('mage.amMeta', {
        options: {},

        _create: function () {
            this.init_url = this.options.urls.init_url;
            this.process_url = this.options.urls.process_url;
            this.conclude_url = this.options.urls.conclude_url;
            this.template = $('#ammeta_product_url_template').val();
            this.indicator = $('.am_processer_container')[0];

            $('#ammeta_apply_templates').click(function(){

                var template = $('#ammeta_product_url_template').val();

                if (template == '') {
                    alert('Please specify url template');
                    return;
                }
                this.initIndicator();

                $.ajax({
                    url     : this.init_url,
                    type    : 'POST',
                    dataType: 'json',
                    data: {form_key: FORM_KEY, template: template}
                }).done($.proxy(function(response) {
                    this.page_size = response.page_size;
                    this.total = response.total;

                    this.pages = Math.ceil(this.total / this.page_size);

                    this.process(1);
                }, this));

            }.bind(this));
        },

        process: function(page){
            var self = this;
            $.ajax({
                url     : this.process_url,
                type    : 'POST',
                dataType: 'json',
                data: {form_key: FORM_KEY, template: $('#ammeta_product_url_template').val(), page: page}
            }).done($.proxy(function(response) {

                if (page < self.pages) {
                    self.updateIndicator(page);
                    self.process(page + 1)
                }
                else {
                    self.conclude();
                }
            }));
        },

        conclude: function () {
            this.concludeIndicator();

            new Ajax.Request(
                this.options['conclude_url'],
                {
                    method: 'post',
                    onSuccess: function(){
                        this.indicator.hide();
                    }.bind(this)
                }
            );
        },

        initIndicator: function(){
            this.indicator.show();
            this.indicator.down('.am_processer').setStyle({width: 0});
            this.indicator.down('.end')
                .removeClassName('end_imported')
                .addClassName('end_not_imported')
            ;
            $('.am_meta_success_msg')[0].hide();
        },

        concludeIndicator: function(){
            this.indicator.down('.end')
                .addClassName('end_imported')
                .removeClassName('end_not_imported')
            ;
            this.indicator.down('.am_processer').setStyle({width: '100%'});
            $('.am_meta_success_msg')[0].appear();
        },

        updateIndicator: function(page){
            var percent = (page * this.page_size / this.total) * 100;
            this.indicator.down('.am_processer').setStyle({width: percent + '%'});
        }


    });
    return $.mage.amMeta;
});

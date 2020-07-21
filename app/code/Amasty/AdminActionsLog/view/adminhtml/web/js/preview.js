require([
    "jquery",
    "Magento_Ui/js/modal/modal",
    "mage/translate",
    "prototype",
    "mage/adminhtml/events"
], function(jQuery, modal, $t){
    Window.keepMultiModalWindow = true;
    var previewChanges = {
        overlayShowEffectOptions : null,
        overlayHideEffectOptions : null,
        modal: null,
        open : function(editorUrl, elementId) {
            if (editorUrl && elementId) {
                jQuery.ajax({
                    url: editorUrl,
                    data: {
                        element_id: elementId
                    },
                    showLoader: true,
                    dataType: 'html',
                    success: function(data, textStatus, transport) {
                        this.openDialogWindow(data, elementId);
                    }.bind(this)
                });
            }
        },
        openDialogWindow : function(data, elementId) {
            if (this.modal) {
                this.modal.html(jQuery(data).html());
            } else {
                this.modal = jQuery(data).modal({
                    title: $t('Action Log Details'),
                    modalClass: 'magento',
                    type: 'slide',
                    firedElementId: elementId
                });
                this.modal.html(jQuery(data).html());
            }
            this.modal.modal('openModal');
        }
    };
    window.previewChanges = previewChanges;
});
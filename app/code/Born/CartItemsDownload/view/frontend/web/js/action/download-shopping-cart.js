/**
 * Copyright 2020 © Born, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/modal/alert',
    'jquery',
    'jquery-ui-modules/widget',
    'mage/validation'
], function (alert, $) {
    'use strict';

    $.widget('mage.downloadShoppingCart', {
        options: {
            downloadURL: '',
            eventName: 'downloadItems'
        },

        /** @inheritdoc */
        _create: function () {
            this._on(this.element, {
                'click': this.onClick
            });
        },

        /**
         * Calls CSV creator and downloader.
         *
         * @param {Event} event
         * @return {Boolean}
         */
        onClick: function (event) {
	    $('body').trigger('processStart');
	    $.ajax({
                url: this.options.downloadURL,
                data: '',
                type: 'post',
                dataType: 'json',
                context: this
            })
            .done(function (response) {
                if (response.success) {
                    this.onSuccess();
                } else {
                    this.onError(response);
                }
                $('body').trigger('processStop');
            })
            .fail(function () {
                console.log('fail1:');
                $('body').trigger('processStop');
            });

            return;
        },

        /**
         * CSV download succeeded.
         */
        onSuccess: function () {
            $(document).trigger('ajax:' + this.options.eventName);
            console.log('success:');
        },

        /**
         * CSV download failed.
         */
        onError: function (response) {
            if (response['error_message']) {
                alert({
                    content: response['error_message']
                });
            } else {
                console.log('fail2:');
            }
        }
    });

    return $.mage.downloadShoppingCart;
});


define([
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'mage/translate'
    ],
    function (
        $,
        ko,
        Component,
        _,
        stepNavigator,
        $t
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Antonio88_ConditionalCheckoutStep/customer-message'
            },
            cartUrl: window.checkoutConfig.cartUrl,
            customerMessage: window.checkoutConfig.customerMessage,
            anyItemWIthProp65Attribute: window.checkoutConfig.anyItemWIthProp65Attribute,
            isVisible: ko.observable(false),
            stepCode: 'customer_message',
            stepTitle: $t('Customer Message'),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();

                if (this.hasAnyItemWIthProp65Attribute()) {
                    stepNavigator.registerStep(
                        this.stepCode,
                        null,
                        this.stepTitle,
                        this.isVisible,
                        _.bind(this.navigate, this),
                        15
                    );
                }
                return this;
            },

            /**
             * @return {String}
             */
            getCustomerMessage: function () {
                return this.customerMessage;
            },

            /**
             * @returns void
             */
            navigate: function () {
                this.isVisible(true);
            },

            /**
             * @return {Boolean}
             */
            hasAnyItemWIthProp65Attribute: function () {

                return this.anyItemWIthProp65Attribute;
            },

            /**
             * @returns void
             */
            navigateToCartPage: function () {
                window.location.href = this.cartUrl;
            },

            /**
             * @returns void
             */
            navigateToNextStep: function () {
                stepNavigator.next();
            }
        });
    }
);

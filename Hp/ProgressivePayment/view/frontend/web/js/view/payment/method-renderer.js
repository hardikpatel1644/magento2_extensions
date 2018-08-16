define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'progressivepayment',
                component: 'Hp_ProgressivePayment/js/view/payment/method-renderer/progressivepayment'
            }
        );
        return Component.extend({});
    }
);
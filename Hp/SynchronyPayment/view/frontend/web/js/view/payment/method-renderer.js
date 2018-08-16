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
                type: 'synchronypayment',
                component: 'Hp_SynchronyPayment/js/view/payment/method-renderer/synchronypayment'
            }
        );
        return Component.extend({});
    }
);
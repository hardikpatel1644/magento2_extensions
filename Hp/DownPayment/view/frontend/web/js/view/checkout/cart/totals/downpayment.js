define(
        [
            'Hp_DownPayment/js/view/checkout/summary/downpayment'
        ],
        function (Component) {
            'use strict';

            return Component.extend({
                /**
                 * @override
                 * use to define amount is display setting
                 */
                isDisplayed: function () {
                    return true;
                }
            });
        }
);
define(
        [
            'Hp_Downpayment/js/view/checkout/summary/customfee'
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
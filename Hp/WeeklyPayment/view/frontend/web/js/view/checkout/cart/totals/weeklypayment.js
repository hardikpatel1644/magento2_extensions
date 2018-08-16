define(
        [
            'Hp_WeeklyPayment/js/view/checkout/summary/weeklypayment'
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
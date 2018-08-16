<?php

namespace Hp\DownPayment\Model\Total;

class Downpayment extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator) {
        $this->quoteValidator = $quoteValidator;
    }

    public function collect(
    \Magento\Quote\Model\Quote $quote, \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $snProductPrice = $total->getGrandTotal();
        $snOrgPrice = $snProductPrice * 52;
        $snDownPayment = round(($snOrgPrice * 0.15), 2);

        $total->setTotalAmount('downpayment', $snDownPayment);
        $total->setBaseTotalAmount('downpayment', $snDownPayment);



        $total->setDownpayment($snDownPayment);
        $total->setBaseDownpayment($snDownPayment);

        $total->setGrandTotal($total->getGrandTotal());
        $total->setBaseGrandTotal($total->getBaseGrandTotal());





        return $this;
    }

    protected function clearValues(Address\Total $total) {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
        $snProductPrice = $total->getGrandTotal();

        $snOrgPrice = $snProductPrice * 52;
        $snDownPayment = round(($snOrgPrice * 0.15), 2);

        return [
            'code' => 'downpayment',
            'title' => '* Approx Down Payment',
            'value' => $snDownPayment
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel() {
        return __('Down Payment');
    }

}

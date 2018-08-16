<?php

namespace Hp\Downpayment\Block;

/**
 * Description of Downpayment
 *
 * @author Hardik Patel<hpca1644@gmail.com>
 */
class Downpayment extends \Magento\Framework\View\Element\Template {

    protected $_cart;
    protected $_checkoutSession;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Checkout\Model\Cart $cart, \Magento\Checkout\Model\Session $checkoutSession, array $data = []
    ) {
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;

        parent::__construct($context, $data);
    }

    public function getCart() {
        return $this->_cart;
    }

    public function getCheckoutSession() {
        return $this->_checkoutSession;
    }

    public function calculateDownpayment() {
        $quote = $this->_checkoutSession->getQuote();
        $items = $quote->getAllItems();
        $subTotal = $quote->getSubtotal();
        $grandTotal = $quote->getGrandTotal();
        $snOrgPrice = $grandTotal * 52;
        return $snDownPayment = round(($snOrgPrice * 0.15), 2);
    }

}

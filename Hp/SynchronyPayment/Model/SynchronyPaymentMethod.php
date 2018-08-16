<?php
 
namespace Hp\SynchronyPayment\Model;
 
/**
 * Pay In Store payment method model
 */
class SynchronyPaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
 
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'synchronypayment';
}
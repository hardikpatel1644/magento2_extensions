<?php
 
namespace Hp\ProgressivePayment\Model;
 
/**
 * Pay In Store payment method model
 */
class ProgressivePaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
 
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'progressivepayment';
}
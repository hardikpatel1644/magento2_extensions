<?php
 
namespace Hp\CustomPayment\Model;
 
/**
 * Pay In Store payment method model
 */
class CustomPaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
 
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'custompayment';
}
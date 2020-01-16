<?php

/**
 * Hp
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Hp ShipmentTracker
 *
 * @category Hp
 * @package Hp_ShipmentTracker
 * 
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hardik Patel <hpca1644@gmail.com>
 *
 */
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hp\ShipmentTracker\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class AbstractCarrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier
{

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    private $trackStatusFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory
     */
    private $trackFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->rateResultFactory = $rateResultFactory;
        $this->trackStatusFactory = $trackStatusFactory;
        $this->trackFactory = $trackFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Determins if tracking is set in the admin panel
     **/
    public function isTrackingAvailable()
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        return true;
    }

    /**
     * Dummy method - need to make this carrier work.
     * But tracker is only used for tracking - not for sending!
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {

        return false;
    }

    public function getTrackingInfo($tracking, $postcode = null)
    {
        $result = $this->getTracking($tracking, $postcode);

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    public function getTracking($trackings, $postcode = null)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }
        $this->_getCgiTracking($trackings, $postcode);

        return $this->_result;
    }

    protected function setTrackingReqeust()
    {
        $r = $this->dataObjectFactory->create();

        $userId = $this->getConfigData('userid');
        $r->setUserId($userId);

        $this->_rawTrackRequest = $r;
    }

    /** Popup window to tracker **/
    protected function _getCgiTracking($trackings, $postcode = null)
    {

        $this->_result = $this->trackFactory->create();

        $defaults = $this->getDefaults();
        foreach ($trackings as $tracking) {
            $status = $this->trackStatusFactory->create();

            $status->setCarrier('Tracker');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);
            $manualUrl = $this->getConfigData('url');
            $preUrl = $this->getConfigData('preurl');
            if ($preUrl != 'none') {
                $taggedUrl = $this->getCode('tracking_url', $preUrl);
            } else {
                $taggedUrl = $manualUrl;
            }
            if (strpos($taggedUrl, '#SPECIAL#')!== false) {
                $taggedUrl = str_replace("#SPECIAL#", "", $taggedUrl);
                $fullUrl = str_replace("#TRACKNUM#", "", $taggedUrl);
            } else {
                $fullUrl = str_replace("#TRACKNUM#", $tracking, $taggedUrl);
                if ($postcode && strpos($taggedUrl, '#POSTCODE#')!== false) {
                    $fullUrl = str_replace("#POSTCODE#", $postcode, $fullUrl);
                }
            }
            $status->setUrl($fullUrl);
            $this->_result->append($status);
        }
    }

    public function getCode($type, $code = '')
    {
        $codes = [

            'preurl' => [
                'none' => __('Use Manual Url'),
                
                
                'canadapost_live' => __('Canada Post Live URL'),
                'sandbox_tracking_summary' => __('Sandbox Tracking Summary'),
                'sandbox_tracking_details' => __('Sandbox Tracking Details'),                
                'production_tracking_summary' => __('Sandbox Tracking Details'),
                'production_tracking_details' => __('Sandbox Tracking Details'),
                
            ],

            'tracking_url' => [
                'canadapost_live' => 'https://www.canadapost.ca/trackweb/en#/details/#TRACKNUM#',
                'sandbox_tracking_summary' => 'https://ct.soa-gw.canadapost.ca/vis/track/pin/#TRACKNUM#/summary',
                'sandbox_tracking_details' => 'https://ct.soa-gw.canadapost.ca/vis/track/pin/#TRACKNUM#/details',
                'production_tracking_summary' => 'https://soa-gw.canadapost.ca/vis/track/pin/#TRACKNUM#/summary',
                'production_tracking_details' => 'https://soa-gw.canadapost.ca/vis/track/pin/#TRACKNUM#/details',
                
            ],

        ];

        if (!isset($codes[$type])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid Tracking code type: %1.', $type)
            );
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid Tracking code for type %1: %2.', $type, $code)
            );
        }

        return $codes[$type][$code];
    }
}

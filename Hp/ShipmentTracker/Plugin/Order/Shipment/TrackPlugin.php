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


namespace Hp\ShipmentTracker\Plugin\Order\Shipment;

class TrackPlugin
{

    public function afterGetNumberDetail(\Magento\Shipping\Model\Order\Track $trackObj, $result)
    {

        // check its the tracker carrier
        if (strpos($this->getCarrierCode(), 'tracker')!== false &&
            strpos($result, 'No detail for number') !== false) {
            // didn't find tracking details, lets see if we can get from our extn
            $carrierInstance = $this->_carrierFactory->create($this->getCarrierCode());
            if (!$carrierInstance) {
                $custom = [];
                $custom['title'] = $this->getTitle();
                $custom['number'] = $this->getTrackNumber();
                return $custom;
            } else {
                $carrierInstance->setStore($this->getStore());
            }

            $rplChars = [" " => ''];
            $string = $this->getShipment()->getShippingAddress()->getPostcode();
            $postcode = strtr($string, $rplChars);
            
            $trackingInfo = $carrierInstance->getTrackingInfo($this->getNumber(), $postcode);
            if (!$trackingInfo) {
                return __('No detail for number "%s"', $this->getNumber());
            }

            return $trackingInfo;
        }
        return $result;
    }
}

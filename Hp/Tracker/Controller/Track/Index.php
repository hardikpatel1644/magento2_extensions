<?php

namespace Hp\Tracker\Controller\Track;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	public function execute()
	{
		// 1. POST request : Get booking data
		$post = (array) $this->getRequest()->getPost();

		if (!empty($post)) {
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$snTrackingNo   = trim(str_replace(" ","",$post['TRACKNUM']));
			if($post['shipper'] == "canada_post")
			{
				// Retrieve your form data


				if($this->validateTrackingNo($snTrackingNo))
				{
					// Doing-something with...

					// Display the succes form validation message
					$this->messageManager->addSuccessMessage('Your tracking details!');
					// Redirect to your form page (or anywhere you want...)
					$resultRedirect->setUrl('/track-details?TRACKNUM='.$snTrackingNo);

				}
				else {
					// Display the succes form validation message
					$this->messageManager->addErrorMessage('Tracking information for ('.$snTrackingNo.') not found. Please contact our customer service department');
					// Redirect to your form page (or anywhere you want...)
					$resultRedirect->setUrl('/track-details/track');
				}
			}
			elseif($post['shipper'] == "canpar"){
				$resultRedirect->setUrl('http://www.canpar.ca/en/track/TrackingAction.do?locale=en&type=2&reference='.$snTrackingNo.'&shipper_num=43300354');
			}
			else {
				// Display the succes form validation message
				$this->messageManager->addErrorMessage('Tracking information for ('.$snTrackingNo.') not found. Please contact our customer service department');
				// Redirect to your form page (or anywhere you want...)
				$resultRedirect->setUrl('/track-details/track');
			}
			return $resultRedirect;
		}


		// 2. GET request : Render the booking page
		$this->_view->loadLayout();
		$this->_view->renderLayout();
	}


	/**
	* Function to validate tracking no
	* @params $snTrackingNo
	* return boolean
	*/
	protected function validateTrackingNo($snTrackingNo)
	{
		$ssFlag = FALSE;
		if($snTrackingNo != '' && strlen($snTrackingNo) === 16)
		{
			if ( preg_match ( '/([0-9]+)/', $snTrackingNo, $matches ) )
			{
				$ssFlag = TRUE;
			}
			$ssFlag = TRUE;
		}
		return $ssFlag;
	}
}

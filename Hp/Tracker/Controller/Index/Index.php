<?php



namespace Hp\Tracker\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action

{

	public function execute()

    {

		$snTrackingNo = $this->getRequest()->getParam('TRACKNUM');

		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

		if($this->validateTrackingNo($snTrackingNo) == false)

		{

			$this->messageManager->addErrorMessage('Tracking information for ('.$snTrackingNo.') not found. Please contact our customer service department');

			$resultRedirect->setUrl('/track-details/track');

			return $resultRedirect;

		}



        $this->_view->loadLayout();

        $this->_view->getLayout()->initMessages();

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
		$snTrackingNo = str_replace(" ","",$snTrackingNo);

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


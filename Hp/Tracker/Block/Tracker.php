<?php



namespace Hp\Tracker\Block;



/**

* Details content block

*/

class Tracker extends \Magento\Framework\View\Element\Template

{



	/**

	* @var \Magento\Shipping\Model\Config

	*/

	private $shippingConfig;

	public function __construct(

		\Magento\Framework\View\Element\Template\Context $context,

		\Magento\Shipping\Model\Config $shippingConfig

	) {

		$this->shippingConfig = $shippingConfig;

		parent::__construct($context);

	}



	public function _prepareLayout()

	{



		$snTrackingNo = $this->getRequest()->getParam('TRACKNUM');

		$this->pageConfig->getTitle()->set(__('Tracking Details')) ;

		return parent::_prepareLayout();

	}


	public function getTrackingInfoData()
		{

		$snTrackingNo = $this->getRequest()->getParam('TRACKNUM');
		$snTrackingNo = str_replace(" ","",$snTrackingNo);

		$asTrackingInfo = array();

		if($this->validateTrackingNo($snTrackingNo))

		{

			$asTrackingInfo['tracking_details'] = $this->getTrackingDetails($snTrackingNo);

			$asTrackingInfo['tracking_summary']  = $this->getTrackingSummary($snTrackingNo);

		}

		return $asTrackingInfo;

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



	/**

	* Function to get canada post certificate path

	* return string

	*/

	protected function getCanadaPostCertPath()

	{

		$certPath = dirname($_SERVER['SCRIPT_FILENAME'])."/cp-cert/cacert.pem";

		return $certPath;

	}





	/**

	* Function to get all config values

	* return array

	*/

	protected function getAllCpConfig()

	{

		$carrierInstances = $this->shippingConfig->getAllCarriers();

		$asConfig  = array();

		foreach ($carrierInstances as $code => $carrier) {



			if($code === "hptracker1")

			{

				if ($carrier->isTrackingAvailable()) {

					$asConfig['sandbox'] = $carrier->getConfigData('sandbox');

					$asConfig['title'] = $carrier->getConfigData('title');

					$asConfig['username'] = $carrier->getConfigData('username');

					$asConfig['password'] = $carrier->getConfigData('password');

					$asConfig['customer_no'] = $carrier->getConfigData('customer_no');

					if( $carrier->getConfigData('sandbox') != NULL && $carrier->getConfigData('sandbox') == 1 )

					{

						$asConfig['canadapost_summary_api_url'] = $carrier->getConfigData('canadapost_sandbox_summary_url');

						$asConfig['canadapost_details_api_url'] = $carrier->getConfigData('canadapost_sandbox_details_url');

					}

					else {

						$asConfig['canadapost_summary_api_url'] = $carrier->getConfigData('canadapost_production_summary_url');

						$asConfig['canadapost_details_api_url'] = $carrier->getConfigData('canadapost_production_details_url');

					}

					$manualUrl = $carrier->getConfigData('url');

					$preUrl = $carrier->getConfigData('preurl');

					if ($preUrl != 'none') {

						$taggedUrl = $carrier->getCode('tracking_url', $preUrl);

					} else {

						$taggedUrl = $manualUrl;

					}

					$asConfig['from_page_url'] =$taggedUrl;

					$asConfig['cert_path'] = $this->getCanadaPostCertPath();

				}

			}

		}

		return $asConfig;

	}





	/**

	* Function to get details of tracking from canada post

	* @params $snTrackingNo string

	* return array

	*/

	protected function getTrackingDetails($snTrackingNo)

	{

		$asConfig = $this->getAllCpConfig();

		$ssResponse = array();

		if($asConfig != '' && is_array($asConfig) && $snTrackingNo != '')

		{

			$ssTrackingUrl = str_replace('#TRACKNUM#',$snTrackingNo,$asConfig['canadapost_details_api_url']);

			$curl = curl_init($ssTrackingUrl); // Create REST Request

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

			curl_setopt($curl, CURLOPT_CAINFO, $asConfig['cert_path']); // Mozilla cacerts

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

			curl_setopt($curl, CURLOPT_USERPWD, $asConfig['username'] . ':' . $asConfig['password']);

			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.track+xml', 'Accept-Language:en-CA'));

			$curl_response = curl_exec($curl); // Execute REST Request

			if(curl_errno($curl)){

				echo 'Curl error: ' . curl_error($curl) . "\n";

			}

			$asResponse['code'] = curl_getinfo($curl,CURLINFO_HTTP_CODE);

			curl_close($curl);

			// Example of using SimpleXML to parse xml response

			libxml_use_internal_errors(true);

			$xml = simplexml_load_string($curl_response);



			$ssResponse = json_encode($xml);

			$asResponse = json_decode($ssResponse, true);

		}

		return $asResponse;

	}



	/**

	* Function to get details of tracking from canada post

	* @params $snTrackingNo string

	* return array

	*/

	protected function getTrackingSummary($snTrackingNo)

	{

		$asConfig = $this->getAllCpConfig();

		$ssResponse = array();

		if($asConfig != '' && is_array($asConfig) && $snTrackingNo != '')

		{

			$ssTrackingUrl = str_replace('#TRACKNUM#',$snTrackingNo,$asConfig['canadapost_summary_api_url']);

			$curl = curl_init($ssTrackingUrl); // Create REST Request

			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

			curl_setopt($curl, CURLOPT_CAINFO, $asConfig['cert_path']); // Mozilla cacerts

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

			curl_setopt($curl, CURLOPT_USERPWD, $asConfig['username'] . ':' . $asConfig['password']);

			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.track+xml', 'Accept-Language:en-CA'));

			$curl_response = curl_exec($curl); // Execute REST Request

			if(curl_errno($curl)){

				echo 'Curl error: ' . curl_error($curl) . "\n";

			}

			$asResponse['code'] = curl_getinfo($curl,CURLINFO_HTTP_CODE);

			curl_close($curl);

			// Example of using SimpleXML to parse xml response

			libxml_use_internal_errors(true);

			$xml = simplexml_load_string($curl_response);



			$ssResponse = json_encode($xml);

			$asResponse = json_decode($ssResponse, true);

		}

		return $asResponse;

	}





}


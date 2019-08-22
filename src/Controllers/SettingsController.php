<?php

namespace Skrill\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Application;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Templates\Twig;
use Plenty\Modules\Frontend\Services\SystemService;
use Skrill\Services\Database\SettingsService;

/**
* Class SettingsController
* @package Skrill\Controllers
*/
class SettingsController extends Controller
{
	use Loggable;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 *
	 * @var systemService
	 */
	private $systemService;

	/**
	 * @var settingsService
	 */
	private $settingsService;

	/**
	 * SettingsController constructor.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param SystemService $systemService
	 * @param SettingsService $settingsService
	 */
	public function __construct(
					Request $request,
					Response $response,
					SystemService $systemService,
					SettingsService $settingsService
	) {
		$this->request = $request;
		$this->response = $response;
		$this->systemService = $systemService;
		$this->settingsService = $settingsService;
	}

	/**
	 * save the settings
	 *
	 * @param Request $request
	 */
	public function saveSettings(Request $request)
	{
		$this->getLogger(__METHOD__)->error('Skrill:request', $request);
		return $this->settingsService->saveSettings($request->get('settingType'), $request->get('settings'));
	}

	/**
	 * load the settings
	 *
	 * @param string $settingType
	 * @return array
	 */
	public function loadSettings($settingType)
	{
		return $this->settingsService->loadSettings($settingType);
	}

	/**
	 * Load the settings for one webshop
	 *
	 * @param string $plentyId
	 * @param string $settingType
	 * @return null|mixed
	 */
	public function loadSetting($plentyId, $settingType)
	{
		return $this->settingsService->loadSetting($plentyId, $settingType);
	}

	/**
	 * Display Skrill backend configuration
	 *
	 * @param Twig $twig
	 * @param string $settingType
	 * @return void
	 */
	public function loadConfiguration(Twig $twig, $settingType)
	{
		$plentyId = $this->systemService->getPlentyId();

		try {
			$configuration = $this->settingsService->loadSetting($plentyId, $settingType);
		}
		catch (\Exception $e)
		{
			die('something wrong, please try again...');
		}
		if ($configuration['error']['code'] == '401')
		{
			die('access denied...');
		}

		return $twig->render(
						'Skrill::Configuration.Settings',
						array(
							'status' => $this->request->get('status'),
							'locale' => substr($_COOKIE['plentymarkets_lang_'], 0, 2),
							'plentyId' => $plentyId,
							'settingType' => $settingType,
							'setting' => $configuration
						)
		);
	}

	/**
	 * Save Skrill backend configuration
	 *
	 */
	public function saveConfiguration()
	{

		$settingType = $this->request->get('settingType');
		$plentyId = $this->request->get('plentyId');
		$apiPassword = $this->request->get('apiPassword');
		$secretWord = $this->request->get('secretWord');

		$oldConfiguration = $this->loadSetting($plentyId, $settingType);

		if ($apiPassword == '*****')
		{
			$apiPassword = $oldConfiguration['apiPassword'];
		}
		if ($secretWord == '*****')
		{
			$secretWord = $oldConfiguration['secretWord'];
		}

		if ($settingType == 'skrill_general')
		{
			$settings['settings'][0]['PID_'.$plentyId] = array(
				'merchantId' => $this->request->get('merchantId'),
				'merchantAccount' => $this->request->get('merchantAccount'),
				'recipient' => $this->request->get('recipient'),
				'logoUrl' => $this->request->get('logoUrl'),
				'shopUrl' => $this->request->get('shopUrl'),
				'apiPassword' => $apiPassword,
				'secretWord' => $secretWord,
				'display' => $this->request->get('display'),
				'merchantEmail' => $this->request->get('merchantEmail'),
			);
		}
		else
		{
			$settings['settings'][0]['PID_'.$plentyId] = array(
				'language' => array(
					'en' => array(
						'paymentName' => $this->request->get('languageEnPaymentName')
					),
					'de' => array(
						'paymentName' => $this->request->get('languageDePaymentName')
					)
				),
				'enabled' => $this->request->get('enabled'),
				'showSeparately' => $this->request->get('showSeparately')
			);
		};

		$result = $this->settingsService->saveSettings($settingType, $settings);

		if ($result == 1)
		{
			$status = 'success';
		}
		else
		{
			$status = 'failed';
		}

		return $status;
	}
}
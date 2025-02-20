<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage  System.rkonikgtm
 *
 * @copyright   rKonik Rafał Kobyliński
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

/**
 * Plugin integrujący Google Tag Manager z Joomla.
 */
class PlgSystemrkonikgtm extends CMSPlugin
{

	/**
	 * Application object.
	 *
	 * @var    CMSApplication
	 * @since  4.0.0
	 */
	protected $app;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * @var
	 * @since 0.1.0
	 */
	protected $idGTM;

	/**
	 * Affects the name of the GTM data layer
	 *
	 * @var
	 * @since 1.0.3
	 */
	protected string $dataLayerName;

	/**
	 * Event handler for the "onBeforeRender" event.
	 *
	 * This method is triggered before the document is rendered. It checks if the current client is the
	 * site (not administrator) and if the document type is HTML. It then retrieves the GTM ID and sets
	 * the data layer name before adding the GTM script to the document using the WebAssetManager.
	 *
	 * @return void
	 */
	function onBeforeRender()
	{

		$app = $this->app;

		// Joomla4 version only Site Work
		if ( $app->isClient('administrator') )
		{
			return;
		}

		$document = $app->getDocument();

		// Only work with HTML documents
		if ( $document->getType() != 'html' )
		{
			return;
		}

		$this->getIdGoogleTagManager();
		$this->setDataLayerName();

		$jsheader = <<<GTM
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','{$this->dataLayerName}','{$this->idGTM}');
GTM;

		$document->getWebAssetManager()->addInlineScript($jsheader);

	}


	/**
	 * Event handler for the "onAfterRender" event.
	 *
	 * This method is triggered after the document has been rendered. It checks if the current client is the
	 * site (not administrator) and if the document type is HTML. It then injects the noscript version of the
	 * GTM code immediately after the opening <body> tag.
	 *
	 * @return bool Returns true if the operation completed successfully.
	 */
	function onAfterRender()
	{
		$app = $this->app;

		// Joomla4 version only Site Work
		if ( $app->isClient('administrator') )
		{
			return;
		}

		$document = $app->getDocument();

		// Only work with HTML documents
		if ( $document->getType() != 'html' )
		{
			return;
		}

		$body = $app->getBody();

		$noscript = <<<GTM
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$this->idGTM}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

GTM;

		$body = preg_replace ("/(<body.*?>)/is", "$1".$noscript, $body, 1);

		$this->app->setBody($body);

		return true;
	}

	/**
	 * Retrieves and validates the Google Tag Manager ID from the plugin parameters.
	 *
	 * This method fetches the GTM ID parameter and assigns it to the class property.
	 *
	 * TODO: Add proper validation to ensure the GTM ID format is correct (e.g., using regex).
	 *
	 * @return void
	 */
	protected function getIdGoogleTagManager() :void
	{
		//todo wykonaj walidację danych
		$this->idGTM = $this->params->get('idGTM','GTM-XXXXXX');
	}

	/**
	 * Sets the name of the data layer for Google Tag Manager.
	 *
	 * If the plugin parameter 'renameDataLayer' is not enabled, it defaults the data layer name to 'dataLayer'.
	 * Otherwise, it retrieves the custom name from the plugin parameters.
	 *
	 * @since 1.0.3
	 */
	protected function setDataLayerName() :void
	{

		if (!$this->params->get('renameDataLayer'))
		{
			$this->dataLayerName = 'dataLayer';
			return;
		}

		$this->dataLayerName=$this->params->get('dataLayerName','dataLayer');

	}
}
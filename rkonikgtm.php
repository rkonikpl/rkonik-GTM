<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

// no direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

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

		$jsheader = <<<GTM
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$this->idGTM}');</script>
<!-- End Google Tag Manager -->
GTM;

		$document->addScriptDeclaration($jsheader);

	}


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

	protected function getIdGoogleTagManager() :void
	{
		//todo wykonaj walidację danych
		$this->idGTM = $this->params->get('idGTM','GTM-XXXXXX');
	}
}
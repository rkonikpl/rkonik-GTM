<?php
/**
 * @package    Joomla.Administrator
 *
 * @author     rKonik Rafał Kobyliński
 * @copyright  rKonik Rafał Kobyliński
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;

class plgSystemrkonikgtmInstallerScript extends InstallerScript
{
	/**
	 * Extension script constructor.
	 *
	 * @return  void
	 */
	public function __construct() {
		$this->minimumJoomla = '4.0';
		$this->minimumPhp = '7.4';

	}

}
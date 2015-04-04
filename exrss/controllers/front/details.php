<?php

if (!defined('_PS_VERSION_')) exit;

class ExRSSDetailsModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign(array(
			'exrss_title' => Configuration::get('MOD_EXRSS_TITLE'),
			'exrss_link' => Configuration::get('MOD_EXRSS_LINK'),
			'exrss_uptime' => Configuration::get('MOD_EXRSS_UPTIME'),
			'exrss_limit' => Configuration::get('MOD_EXRSS_LIMIT')
		));

		$this->setTemplate('details.tpl');
	}
}

?>

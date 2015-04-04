<?php
if (!defined('_PS_VERSION_')) exit;

class ExRSS extends Module {
	public function __construct() {
		$this->name = 'exrss';
		$this->tab = 'front_office_features';
		$this->version = '1.5.4';
		$this->author = 'Aki4';
		parent::__construct();
		$this->displayName = $this->l('ExRSS Module');
		$this->description = $this->l('RSS block with images from any website.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
		//$this->_checkContent();
		$this->context->smarty->assign('module_name', $this->name);
	}

	public function regHook() {
		foreach (func_get_args() as $p) if (!$this->registerHook($p)) return false;
		return true;
	}
	
	public function install() {
		return parent::install() && 
			$this->regHook('displayHeader', 'displayLeftColumn', 'displayRightColumn', 'displayFooter', 'displayHomeTab', 'displayHomeTabContent') &&
			Configuration::deleteByName('MOD_EXRSS_TITLE') &&
			Configuration::deleteByName('MOD_EXRSS_LINK') &&
			Configuration::deleteByName('MOD_EXRSS_UPTIME') &&
			Configuration::deleteByName('MOD_EXRSS_LIMIT');
	}

	public function uninstall() {
		$title = Configuration::get('MOD_EXRSS_TITLE');
		unlink("config/xml/exrss_".$title.".xml");
		return parent::uninstall() && 
		Configuration::deleteByName('MOD_EXRSS_TITLE') && 
		Configuration::deleteByName('MOD_EXRSS_LINK') && 
		Configuration::deleteByName('MOD_EXRSS_UPTIME') && 
		Configuration::deleteByName('MOD_EXRSS_LIMIT');
	}
	
	public function getRSS() {
		$lnk = Configuration::get('MOD_EXRSS_LINK');
		$limit = Configuration::get('MOD_EXRSS_LIMIT');
		$upftime = (int)Configuration::get('MOD_EXRSS_UPTIME');
		$path = sprintf("config/xml/exrss_%s.xml", Configuration::get('MOD_EXRSS_TITLE'));
		$currentTime = time();
		$rss = new DOMDocument();
		if(file_exists($path)) $lastTime = filemtime($path); else $lastTime = null;
		if ($lastTime != null && $currentTime - $lastTime > $upftime*60) {
			$ld = $rss->load($lnk);
			if (is_writable($path)) $rss->save($path);
		} else $ld = $rss->load(file_exists($path) ? $path : $lnk);
		if($lnk && $ld) {
			$f = array();
			foreach ($rss->getElementsByTagName('item') as $node) {
				$item = array ( 
					'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
					'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
					'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
					'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
					'image' => $node->getElementsByTagName('enclosure')->item(0)->getAttribute('url'),
				);
				array_push($f, $item);
			}
			if (!isset($limit) || $limit == null || (int)$limit == 0 || $limit == 0 || $limit>count($f)) $limit = count($f);
			
			$str = '<div class="rsswp-feed">';
			for($x = 0; $x < $limit; $x++) {
				$title = str_replace(' & ', ' &amp; ', $f[$x]['title']);
				$str.= '<a href="'.$f[$x]['link'].'" title="'.$title.'" target="_blank"><img style="width:150px;display:inline-block;" src="'.$f[$x]['image'].'">';
				$str.= sprintf('<div style="display:inline-block;"><p><strong><span class="rp-title">%s</span></strong><br />', $title);
				$str.= sprintf('<small><em>Добавлено %s</em></small></p>', date('d.m.Y', strtotime($f[$x]['date'])));
				$str.= sprintf('<p class="rp-description">%s</p></div></a>', $f[$x]['desc']);
			}
			return $str.'</div>';
		} else return sprintf('<div style="background-color:#f00 !important;color:#000 !important;">RSS PATH "%s" WAS NOT SET UP</div>', $lnk);
	}
	
	public function hookDisplayHeader() {
		$this->context->controller->addCSS($this->_path.'css/style.css', 'all');
	}
	
	public function hookDisplayLeftColumn() {
		$this->context->smarty->assign(array(
			'placement' => 'homeTab',
			'txt' => $this->getRSS(),
			'title' => Configuration::get('MOD_EXRSS_TITLE')
		));
		return $this->display(__FILE__, 'homeTab.tpl');
	}
	
	public function hookDisplayHomeTabContent($params) {
		$this->context->smarty->assign(array(
			'placement' => 'left',
			'txt' => $this->getRSS(),
			'title' => Configuration::get('MOD_EXRSS_TITLE')
		));    
		return $this->display(__FILE__, 'tab.tpl');
	}
	
	public function hookDisplayHomeTab($params) {
		$this->context->smarty->assign(array('title' => Configuration::get('MOD_EXRSS_TITLE')));    
		return $this->display(__FILE__, 'tab_content.tpl');
	}
	
	public function hookDisplayRightColumn() {
		$this->context->smarty->assign(array(
			'placement' => 'right',
			'txt' => $this->getRSS(),
			'title' => Configuration::get('MOD_EXRSS_TITLE')
		));
		return $this->display(__FILE__, 'left.tpl');
	}
	
	public function hookDisplayFooter() {
		$this->context->smarty->assign(array(
			'placement' => 'right',
			'txt' => $this->getRSS(),
			'title' => Configuration::get('MOD_EXRSS_TITLE')
		));
		return $this->display(__FILE__, 'left.tpl');
	}

	public function getContent() {
		$this->_displayContent(Tools::isSubmit('submit_'.$this->name) ? $this->_saveContent() : '');
		return $this->display(__FILE__, 'settings.tpl');
	}

	private function _saveContent() {
		$message = '';
		if (Configuration::updateValue('MOD_EXRSS_TITLE', Tools::getValue('MOD_EXRSS_TITLE')) &&
				Configuration::updateValue('MOD_EXRSS_LINK', Tools::getValue('MOD_EXRSS_LINK')) &&
				Configuration::updateValue('MOD_EXRSS_UPTIME', Tools::getValue('MOD_EXRSS_UPTIME')) &&
				Configuration::updateValue('MOD_EXRSS_LIMIT', Tools::getValue('MOD_EXRSS_LIMIT'))
			)
			$message = $this->displayConfirmation($this->l('Your settings have been saved'));
		else
			$message = $this->displayError($this->l('There was an error while saving your settings'));
		return $message;
	}

	private function _displayContent($message) {
		$this->context->smarty->assign(array(
			'message' => $message,
			'MOD_EXRSS_TITLE' => Configuration::get('MOD_EXRSS_TITLE'),
			'MOD_EXRSS_LINK' => Configuration::get('MOD_EXRSS_LINK'),
			'MOD_EXRSS_UPTIME' => Configuration::get('MOD_EXRSS_UPTIME'),
			'MOD_EXRSS_LIMIT' => Configuration::get('MOD_EXRSS_LIMIT')
		));
	}
}

?>

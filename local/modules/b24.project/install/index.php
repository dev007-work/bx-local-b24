<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;

class b24_project extends CModule
{
	/**
	 * @return string
	 */
	public static function getModuleId(): string
	{
		return basename(dirname(__DIR__));
	}

	public function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . '/version.php');
		$this->MODULE_ID = self::getModuleId();
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = 'Инвестиционный проект Битрикс24';
		$this->MODULE_DESCRIPTION = 'Модуль инвестиционного проекта Битрикс24';

		$this->PARTNER_NAME = '1С-Битрикс';
		$this->PARTNER_URI = '';
	}

	public function doInstall(): void
	{
		Main\ModuleManager::registerModule($this->MODULE_ID);
	}

	public function doUninstall(): void
	{
		Main\ModuleManager::unRegisterModule($this->MODULE_ID);
	}
}
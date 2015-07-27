<?php
class VracConfiguration
{
	private static $_instance = null;
	protected $configuration;
	
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new VracConfiguration();
		}
		return self::$_instance;
	}
	
	public function __construct() 
	{
		$this->configuration = sfConfig::get('app_configuration_vrac', array());
	}
	
	public function getTransactions()
	{
		return $this->configuration['transactions'];
	}
	
	public function getContenances()
	{
		return $this->configuration['contenances'];
	}
	
	public function getDelaisPaiement()
	{
		return $this->configuration['delais_paiement'];
	}
	
	public function getMoyensPaiement()
	{
		return $this->configuration['moyens_paiement'];
	}
	
	public function getRepartitionCvo()
	{
		return $this->configuration['repartition_cvo'];
	}
	
	public function getTva()
	{
		return $this->configuration['tva'];
	}
	
	public function getCategories()
	{
		return $this->configuration['categories'];
	}
	
	public function getEtapes()
	{
		return $this->configuration['etapes'];
	}
	
	public function getChamps($etape)
	{
		return $this->configuration['champs'][$etape];
	}
	public function getUnites()
	{
		return $this->configuration['unites'];
	}
}
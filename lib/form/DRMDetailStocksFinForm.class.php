<?php

class DRMDetailStocksFinForm  extends acCouchdbObjectForm {

    public function configure() {
    	foreach (Configuration::getStocksFin() as $key => $item) {
	    	$this->setWidget($key, new sfWidgetFormInputFloat());
	    	$this->setValidator($key, new sfValidatorNumber(array('required' => false)));
    	}        
        $this->widgetSchema->setNameFormat('drm_detail_stocks_fin[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

}
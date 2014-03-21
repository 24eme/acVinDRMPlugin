<?php

class DRMDetailSortiesForm  extends acCouchdbObjectForm {

    public function configure() {
    	foreach (Configuration::getStocksSortie() as $key => $item) {
	    	$this->setWidget($key, new sfWidgetFormInputFloat(array('float_format' => "%01.04f")));
	    	$this->setValidator($key, new sfValidatorNumber(array('required' => false)));
    	}        
        $this->widgetSchema->setNameFormat('drm_detail_sorties[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

}
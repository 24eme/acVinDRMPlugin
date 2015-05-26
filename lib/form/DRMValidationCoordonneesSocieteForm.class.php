<?php

class DRMValidationCoordonneesSocieteForm extends acCouchdbObjectForm {

    protected $coordonneesSociete = null;
    protected $drm = null;

    public function __construct(DRM $drm, $options = array(), $CSRFSecret = null) {
        $this->drm = $drm;
        parent::__construct($drm, $options, $CSRFSecret);
    }

    public function configure() {
        parent::configure();

        $this->setWidget('siret', new sfWidgetFormInput());
        $this->setValidator('siret', new sfValidatorString(array('required' => true)));
        $this->widgetSchema->setLabel('siret', 'SIRET :');

        $this->setWidget('adresse', new sfWidgetFormInput());
        $this->setValidator('adresse', new sfValidatorString(array('required' => true)));
        $this->widgetSchema->setLabel('adresse', 'Adresse :');

        $this->setWidget('code_postal', new sfWidgetFormInput());
        $this->setValidator('code_postal', new sfValidatorString(array('required' => true)));
        $this->widgetSchema->setLabel('code_postal', 'Code postal :');

        $this->setWidget('commune', new sfWidgetFormInput());
        $this->setValidator('commune', new sfValidatorString(array('required' => true)));
        $this->widgetSchema->setLabel('commune', 'Commune :');

        $this->setWidget('email', new sfWidgetFormInput());
        $this->setValidator('email', new sfValidatorString(array('required' => true)));
        $this->widgetSchema->setLabel('email', 'E-mail :');

        $this->setWidget('telephone', new sfWidgetFormInput());
        $this->setValidator('telephone', new sfValidatorString(array('required' => false)));
        $this->widgetSchema->setLabel('telephone', 'Téléphone :');

        $this->setWidget('fax', new sfWidgetFormInput());
        $this->setValidator('fax', new sfValidatorString(array('required' => false)));
        $this->widgetSchema->setLabel('fax', 'Fax :');

        $this->widgetSchema->setNameFormat('drm_validation_coordonnees_societe[%s]');
    }

    private function getCoordonneesSociete() {
        if (!$this->coordonneesSociete) {
            $this->coordonneesSociete = $this->drm->getCoordonneesSociete();
        }
        return $this->coordonneesSociete;
    }
    
    public function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $this->getCoordonneesSociete();
        $this->setDefault('siret', $this->coordonneesSociete->siret);
        $this->setDefault('adresse', $this->coordonneesSociete->adresse);
        $this->setDefault('code_postal', $this->coordonneesSociete->code_postal);
        $this->setDefault('commune', $this->coordonneesSociete->commune);
        $this->setDefault('email', $this->coordonneesSociete->email);       
        $this->setDefault('telephone', $this->coordonneesSociete->telephone);
        $this->setDefault('fax', $this->coordonneesSociete->fax);
    }
    
    public function getDiff() {  
        $diff = array();
        $this->getCoordonneesSociete();
        foreach ($this->getValues() as $key => $new_value) {
            if(!preg_match('/^_revision$/', $key)){
                if($this->coordonneesSociete->$key != $new_value){
                    $diff[$key] = $new_value;
                    }
            }
        }
        return $diff;
    }

}
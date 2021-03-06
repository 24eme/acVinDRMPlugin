<?php

class DRMValidationCommentaireForm extends acCouchdbObjectForm {

    public function configure() {
      parent::configure();
      $this->setWidget('commentaire', new bsWidgetFormTextarea(array(), array('style' => 'width: 100%;resize:none;')));
      $this->setValidator('commentaire', new sfValidatorString(array('required' => false)));
      $this->widgetSchema->setLabel('commentaire', 'Commentaire interne :');
      
      $this->setWidget('email_transmission', new sfWidgetFormInputHidden());
      $this->setValidator('email_transmission', new sfValidatorString(array('required' => false)));
      $this->widgetSchema->setLabel('email_transmission', 'Email de transmission :');
      
      $this->widgetSchema->setNameFormat('drm[%s]');
    }    
  
}

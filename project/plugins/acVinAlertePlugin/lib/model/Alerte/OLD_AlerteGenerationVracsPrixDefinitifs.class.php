
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class AlerteGenerationVracsPrixDefinitifs
 * @author mathurin
 */
class AlerteGenerationVracsPrixDefinitifs extends AlerteGenerationVrac {

    public function getTypeAlerte() {
        return AlerteClient::VRAC_PRIX_DEFINITIFS;
    }
    
    public function execute(){
        $this->updates();
        $this->creations();
    }
    
    public function updates() {
        $this->updatesByDocumentsIds($this->getChanges(),self::TYPE_DOCUMENT);
    }
    
    public function creations() {
        $this->creationsByDocumentsIds($this->getChanges(),self::TYPE_DOCUMENT);
    }
    
    public function creation($document) {
       return $this->creationByDocumentId($document,self::TYPE_DOCUMENT,  AlerteClient::STATUT_A_RELANCER);
    }
    
    public function update($document) {        
        return $this->updateByDocumentId($document,self::TYPE_DOCUMENT);        
    }

    public function isInAlerte($document) {
         return $document->hasPrixVariable() && !$document->hasPrixDefinitif();
    }
    
    public function getTypeRelance() {
        return RelanceClient::TYPE_RELANCE_DECLARATIVE;
    }
}
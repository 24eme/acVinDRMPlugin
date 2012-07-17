<?php
/**
 * BaseDRMDetailVrac
 * 
 * Base model for DRMDetailVrac

 * @property string $numero_contrat
 * @property float $volume
 * @property string $date_enlevement

 * @method string getNumeroContrat()
 * @method string setNumeroContrat()
 * @method float getVolume()
 * @method float setVolume()
 * @method string getDateEnlevement()
 * @method string setDateEnlevement()
 
 */

abstract class BaseDRMDetailVrac extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDetailVrac';
    }
                
}
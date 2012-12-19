<?php
/**
 * BaseDRMESDetailVrac
 * 
 * Base model for DRMESDetailVrac

 * @property string $identifiant
 * @property float $volume
 * @property string $date_enlevement

 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method float getVolume()
 * @method float setVolume()
 * @method string getDateEnlevement()
 * @method string setDateEnlevement()
 
 */

abstract class BaseDRMESDetailVrac extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMESDetailVrac';
    }
                
}
<?php
/**
 * BaseDRMDetailExport
 * 
 * Base model for DRMDetailExport

 * @property string $destination
 * @property float $volume
 * @property string $date_enlevement

 * @method string getDestination()
 * @method string setDestination()
 * @method float getVolume()
 * @method float setVolume()
 * @method string getDateEnlevement()
 * @method string setDateEnlevement()
 
 */

abstract class BaseDRMDetailExport extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DRM';
       $this->_tree_class_name = 'DRMDetailExport';
    }
                
}
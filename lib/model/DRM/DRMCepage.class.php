<?php

/**
 * Model for DRMCepage
 *
 */
class DRMCepage extends BaseDRMCepage {

    public function getChildrenNode() {

        return $this->filter('details*');
    }

    public function getDetails() {

        return $this->getChildrenNode();
    }

    public function getCouleur() {

        return $this->getParentNode();
    }

    public function getProduits() {

        return array($this->getHash() => $this);
    }

    public function addDetails($detailsKey) {

        $detailsKey = str_replace('details_', '', $detailsKey);

        if($detailsKey != DRM::DETAILS_KEY_ACQUITTE && $detailsKey != DRM::DETAILS_KEY_SUSPENDU) {

            throw new sfException(sprintf("La clé détail %s n'est pas autorisé", $detailsKey));
        }

        return $this->add('details_'.str_replace('details_', '', $detailsKey));
    }

    public function getProduitsDetails($teledeclarationMode = false, $detailsKey = null) {
        $details = array();
        foreach ($this->getChildrenNode() as $key => $items) {
            if(!is_null($detailsKey) && $detailsKey != str_replace('details_', '', $key)) {
                continue;
            }

            foreach($items as $item) {
                $details[$item->getHash()] = $item;
            }
        }

        return $details;
    }

    public function hasProduitDetailsWithStockNegatif() {
        foreach ($this->getProduitsDetails() as $detail) {
            if ($detail->total < 0) {
                return true;
            }
        }

        return false;
    }

    public function getLieuxArray() {

        throw new sfException('this function need to call before lieu tree');
    }

    public function hasMovements(){
        return !$this->exist('no_movements') || !$this->no_movements;
    }
}

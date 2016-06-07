<?php

/**
 * Model for DRMDeclaration
 *
 */
class DRMDeclaration extends BaseDRMDeclaration {

    public function getChildrenNode() {

        return $this->certifications;
    }

    public function getMouvements($isTeledeclaration = false) {
        $produits = $this->getProduitsDetails();
        $mouvements = array();
        foreach ($produits as $produit) {
            $mouvements = array_replace_recursive($mouvements, $produit->getMouvements());
        }

        return $mouvements;
    }

    public function cleanDetails() {
        $delete = false;
        foreach ($this->getProduitsDetails() as $detail) {
            if ($detail->isSupprimable()) {
                $detail->delete();
                $delete = true;
            }
        }

        if ($delete) {
            $this->cleanNoeuds();
        }
    }

    public function cleanNoeuds() {
        $this->_cleanNoeuds();
    }

    public function hasProduitDetailsWithStockNegatif() {
        foreach ($this->getProduitsDetails() as $prod) {
            if ($prod->hasProduitDetailsWithStockNegatif()) {
                return true;
            }
        }
        return false;
    }

    public function getProduitsDetailsSorted($teledeclarationMode = false, $detailsKey = null) {
        $produits = array();

        foreach ($this->certifications as $certification) {

            $produits = array_merge($produits, $certification->getProduitsDetailsSorted($teledeclarationMode, $detailsKey));
        }

        return $produits;
    }

    public function getProduitsDetailsByCertifications($isTeledeclarationMode = false, $detailsKey = null) {
        foreach ($this->getConfig()->getCertifications() as $certification) {
            if (!isset($produitsDetailsByCertifications[$certification->getHashWithoutInterpro()])) {
                $produitsDetailsByCertifications[$certification->getHashWithoutInterpro()] = new stdClass();
                $produitsDetailsByCertifications[$certification->getHashWithoutInterpro()]->certification_libelle = $certification->getLibelle();
                $produitsDetailsByCertifications[$certification->getHashWithoutInterpro()]->produits = array();
                $produitsDetailsByCertifications[$certification->getHashWithoutInterpro()]->certification_keys = $certification->getKey();
            } else {
                $produitsDetailsByCertifications[$certification->getHashWithoutInterpro()]->certification_keys .= ','.$certification->getKey();
            }
           if ($this->getDocument()->exist($certification->getHash())) {
                $produitsDetailsByCertifications[$certification->getHashWithoutInterpro()]->produits = array_merge($produitsDetailsByCertifications[$certification->getHashWithoutInterpro()]->produits, $this->getDocument()->get($certification->getHash())->getProduitsDetailsSorted($isTeledeclarationMode, $detailsKey));
            }
        }

        return $produitsDetailsByCertifications;
    }

    public function getProduitsDetailsByCertificationsForPdf($isTeledeclarationMode = false, $detailsKey = null){

      $produitsDetailsByCertifications = $this->getProduitsDetailsByCertifications($isTeledeclarationMode, $detailsKey);

      $produitsDetailsByCertificationsForPdf = array();
      foreach ($produitsDetailsByCertifications as $keyCertif => $produitsByCertif) {
        if(!count($produitsByCertif->produits)){
          continue;
        }
        if(!array_key_exists($detailsKey,$produitsDetailsByCertificationsForPdf)){
          $produitsDetailsByCertificationsForPdf[$detailsKey] = array();
        }
        $produitsDetailsByCertificationsForPdf[$detailsKey][$keyCertif] = $produitsByCertif;
        $produits = array();
        foreach ($produitsByCertif->produits as $hash => $produit) {
          if(!$produit->hasStockEpuise()){
            $produits[$hash] = $produit;
          }
        }
        $produitsDetailsByCertificationsForPdf[$detailsKey][$keyCertif]->produits = $produits;
      }
      return $produitsDetailsByCertificationsForPdf;
    }

}

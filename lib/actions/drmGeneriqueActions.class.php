<?php

/**
 * Description of class drmGeneriqueActions
 * @author mathurin
 */
class drmGeneriqueActions extends sfActions {

    protected function initSocieteAndEtablissementPrincipal() {
        $this->compte = $this->getUser()->getCompte();
        if ($this->isTeledeclarationDrm()) {

            /*if (!$this->compte) {
                new sfException("Le compte $compte n'existe pas");
            }*/

            $this->etablissementPrincipal =  $this->getRoute()->getEtablissement();
            $this->societe = $this->etablissementPrincipal->getSociete();
        }
    }

    protected function redirect403IfIsNotTeledeclaration() {
        if (!$this->isTeledeclarationDrm()) {
            $this->redirect403();
        }
    }
    
     protected function redirect403IfIsTeledeclaration() {
        if ($this->isTeledeclarationDrm()) {
            $this->redirect403();
        }
    }

    protected function redirect403IfIsNotTeledeclarationAndNotMe() {
        $this->redirect403IfIsNotTeledeclaration();
        if ($this->getUser()->getCompte()->identifiant != $this->identifiant) {
            $this->redirect403();
        }
    }

    private function redirect403() {
        $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    protected function isTeledeclarationDrm() {
       return true;
    }
    
    protected function initDeleteForm() {
        $this->deleteForm = new DRMDeleteForm($this->drm);
    }
    
    protected function isUsurpationMode() {
        return $this->getUser()->isUsurpationCompte();
    }
    
    protected function createMouvementsByProduits($mouvements) {
        $this->mouvementsByProduit = array();
        foreach ($mouvements as $mouvement) {
        	if (!isset($this->mouvementsByProduit[$mouvement->type_drm_libelle])) {
        		$this->mouvementsByProduit[$mouvement->type_drm_libelle] = array();
        	}
            if (!array_key_exists($mouvement->produit_hash, $this->mouvementsByProduit)) {
                $this->mouvementsByProduit[$mouvement->type_drm_libelle][$mouvement->produit_hash] = array();
            }
            $this->mouvementsByProduit[$mouvement->type_drm_libelle][$mouvement->produit_hash][] = $mouvement;
        }
        return $this->mouvementsByProduit;
    }

}

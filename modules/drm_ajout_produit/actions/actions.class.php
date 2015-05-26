<?php

class drm_ajout_produitActions extends drmGeneriqueActions {

    public function executeChoixPoduits(sfWebRequest $request) {
        $this->initSocieteAndEtablissementPrincipal();
        $this->drm = $this->getRoute()->getDRM();
        $this->certificationsProduits = $this->drm->declaration->getProduitsDetailsByCertifications();
        $this->form = new DRMProduitsChoiceForm($this->drm);

        $this->formAddProduitsByCertifications = array();
        foreach ($this->certificationsProduits as $certificationProduits) {
            $this->formAddProduitsByCertifications[$certificationProduits->certification->getHashForKey()] = new DRMAddProduitByCertificationForm($this->drm, array('configurationCertification' => $certificationProduits->certification));
        }

        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        if ($request->isMethod(sfRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirect('drm_edition', $this->form->getObject());
            }
        }
    }

    public function executeChoixAjoutPoduits(sfWebRequest $request) {
        $this->initSocieteAndEtablissementPrincipal();
        $this->drm = $this->getRoute()->getDRM();
        $cerfificationParam = $request['certification_hash'];
        if (!$cerfificationParam || !preg_match('/^\-declaration\-certifications\-([a-zA-Z]*)/', $cerfificationParam)) {
            throw new sfException("le format de la certification n'est pas correct : $cerfificationParam");
        }
        $cerfificationHash = str_replace('-', '/', $cerfificationParam);
        $certificationDrm = $this->drm->get($cerfificationHash);
        if (!$certificationDrm) {
            throw new sfException("La certification n'existe pas dans la DRM : $cerfificationHash");
        }
        $this->form = new DRMAddProduitByCertificationForm($this->drm, array('configurationCertification' => $certificationDrm->getConfig()));
        if ($request->isMethod(sfRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirect('drm_choix_produit', $this->form->getDrm());
            }
        }
    }

}
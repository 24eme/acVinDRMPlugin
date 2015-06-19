<?php

/**
 * drm actions.
 *
 * @package    vinsi
 * @subpackage drm
 * @author     Mathurin
 */
class drmActions extends drmGeneriqueActions {

    public function executeRedirect(sfWebRequest $request) {
        $drm = DRMClient::getInstance()->find($request->getParameter('identifiant_drm'));
        $this->forward404Unless($drm);
        return $this->redirect('drm_visualisation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
    }

    public function executeChooseEtablissement(sfWebRequest $request) {
        $this->form = new DRMEtablissementChoiceForm('INTERPRO-inter-loire');
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                return $this->redirect('drm_etablissement', $this->form->getEtablissement());
            }
        }
    }

    public function executeRedirectEtape(sfWebRequest $request) {
        $isTeledeclarationMode = $this->isTeledeclarationDrm();
        $drm = $this->getRoute()->getDRM();
        switch ($drm->etape) {
            case DRMClient::ETAPE_CHOIX_PRODUITS:
                if ($isTeledeclarationMode) {
                    return $this->redirect('drm_choix_produit', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                } else {
                    return $this->redirect('drm_edition', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                }
                break;

            case DRMClient::ETAPE_SAISIE:
                return $this->redirect('drm_edition', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                break;

            case DRMClient::ETAPE_CRD:
                if ($isTeledeclarationMode) {
                    return $this->redirect('drm_crd', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                } else {
                    return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                }
                break;

            case DRMClient::ETAPE_ADMINISTRATION:
                if ($isTeledeclarationMode) {
                    return $this->redirect('drm_annexes', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                } else {
                    return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                }
                break;

            case DRMClient::ETAPE_VALIDATION:
                return $this->redirect('drm_validation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
                break;
        }
        return $this->redirect('drm_visualisation', array('identifiant' => $drm->identifiant, 'periode_version' => $drm->getPeriodeAndVersion()));
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeNouvelle(sfWebRequest $request) {
        $isTeledeclarationMode = $this->isTeledeclarationDrm();
        $identifiant = $request->getParameter('identifiant');
        $periode = $request->getParameter('periode');
        $drm = DRMClient::getInstance()->createDoc($identifiant, $periode, $isTeledeclarationMode);
        $drm->save();
        if ($isTeledeclarationMode) {
            $this->redirect('drm_choix_produit', $drm);
        } else {
            $this->redirect('drm_edition', $drm);
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeInProcess(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->campagne = $request->getParameter('campagne');
        $this->historique = new DRMHistorique($request->getParameter('identifiant'), $this->campagne);
        if (!$this->campagne) {
            $this->campagne = '2012-2013';
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeDelete(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        if ($request->isMethod(sfRequest::POST)) {
            if ($request->getParameter('confirm')) {
                $this->drm->delete();
            }

            $this->redirect('drm_etablissement', $this->drm);
        }
    }

    private function formCampagne(sfWebRequest $request, $route) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->societe = $this->etablissement->getSociete();
        if ($this->etablissement->famille != EtablissementFamilles::FAMILLE_PRODUCTEUR)
            throw new sfException("L'établissement sélectionné ne déclare pas de DRM");

        $this->campagne = $request->getParameter('campagne');
        if (!$this->campagne) {
            $this->campagne = -1;
        }

        $this->formCampagne = new DRMEtablissementCampagneForm($this->etablissement->identifiant, $this->campagne);
        if ($request->isMethod(sfWebRequest::POST)) {
            $param = $request->getParameter($this->formCampagne->getName());
            if ($param) {
                $this->formCampagne->bind($param);
                return $this->redirect($route, array('identifiant' => $this->etablissement->getIdentifiant(), 'campagne' => $this->formCampagne->getValue('campagne')));
            }
        }
    }

    /**
     * Executes mon espace action
     *
     * @param sfRequest $request A request object
     */
    public function executeMonEspace(sfWebRequest $request) {
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        return $this->formCampagne($request, 'drm_etablissement');
    }

    public function executeStocks(sfWebRequest $request) {
        return $this->formCampagne($request, 'drm_etablissement_stocks');
    }

    /**
     * Executes historique action
     *
     * @param sfRequest $request A request object
     */
    public function executeHistorique(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->campagne = $request->getParameter('campagne');
    }

    /**
     * Executes informations action
     *
     * @param sfRequest $request A request object
     */
    public function executeInformations(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        $this->tiers = $this->getUser()->getTiers();
        $isAdmin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
        $this->form = new DRMInformationsForm(array(), array('is_admin' => $isAdmin));

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $values = $this->form->getValues();
                if ($values['confirmation'] == "modification") {
                    $this->redirect('drm_modif_infos', $this->drm);
                } elseif ($values['confirmation']) {
                    $this->drm->declarant->nom = $this->tiers->nom;
                    $this->drm->declarant->raison_sociale = $this->tiers->raison_sociale;
                    $this->drm->declarant->siret = $this->tiers->siret;
                    $this->drm->declarant->cni = $this->tiers->cni;
                    $this->drm->declarant->cvi = $this->tiers->cvi;
                    $this->drm->declarant->siege->adresse = $this->tiers->siege->adresse;
                    $this->drm->declarant->siege->code_postal = $this->tiers->siege->code_postal;
                    $this->drm->declarant->siege->commune = $this->tiers->siege->commune;
                    $this->drm->declarant->comptabilite->adresse = $this->tiers->comptabilite->adresse;
                    $this->drm->declarant->comptabilite->code_postal = $this->tiers->comptabilite->code_postal;
                    $this->drm->declarant->comptabilite->commune = $this->tiers->comptabilite->commune;
                    $this->drm->declarant->no_accises = $this->tiers->no_accises;
                    $this->drm->declarant->no_tva_intracommunautaire = $this->tiers->no_tva_intracommunautaire;
                    $this->drm->declarant->service_douane = $this->tiers->service_douane;
                    $this->drm->save();
                }
                $this->drm->setCurrentEtapeRouting('ajouts_liquidations');
                $this->redirect('drm_mouvements_generaux', $this->drm);
            }
        }
    }

    public function executeModificationInfos(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
    }

    public function executeDeclaratif(sfWebRequest $request) {
        $this->drm = $this->getRoute()->getDRM();
        $this->drm->setCurrentEtapeRouting('declaratif');
        $this->form = new DRMDeclaratifForm($this->drm);
        $this->hasFrequencePaiement = ($this->drm->declaratif->paiement->douane->frequence) ? true : false;
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->drm->setCurrentEtapeRouting('validation');
                $this->redirect('drm_validation', $this->drm);
            }
        }
    }

    public function executePaiementFrequenceFormAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $drm = $this->getRoute()->getDRM();
        return $this->renderText($this->getPartial('popupFrequence', array('drm' => $drm)));
    }

    public function executeShowError(sfWebRequest $request) {
        $drm = $this->getRoute()->getDRM();
        $drmValidation = new DRMValidation($drm);
        $controle = $drmValidation->find($request->getParameter('type'), $request->getParameter('identifiant'));
        $this->forward404Unless($controle);
        $this->getUser()->setFlash('control_message', $controle->getMessage());
        $this->getUser()->setFlash('control_css', "flash_" . $controle->getType());
        $this->redirect($controle->getLien());
    }

    public function executeRectificative(sfWebRequest $request) {
        $drm = $this->getRoute()->getDRM();

        $drm_rectificative = $drm->generateRectificative();
        $drm_rectificative->save();

        return $this->redirect('drm_redirect_etape', array('identifiant' => $drm_rectificative->identifiant, 'periode_version' => $drm_rectificative->getPeriodeAndVersion()));
    }

    public function executeModificative(sfWebRequest $request) {
        $drm = $this->getRoute()->getDRM();

        $drm_rectificative = $drm->generateModificative();
        $drm_rectificative->save();

        return $this->redirect('drm_redirect_etape', array('identifiant' => $drm_rectificative->identifiant, 'periode_version' => $drm_rectificative->getPeriodeAndVersion()));
    }

    /**
     * Executes mouvements generaux action
     *
     * @param sfRequest $request A request object
     */
    public function executePdf(sfWebRequest $request) {

        ini_set('memory_limit', '512M');
        $this->drm = $this->getRoute()->getDRM();
        $pdf = new ExportDRMPdf($this->drm);

        return $this->renderText($pdf->render($this->getResponse(), false, $request->getParameter('format')));
    }

    public function executeSociete(sfWebRequest $request) {

        $this->identifiant = $request['identifiant'];

        $this->initSocieteAndEtablissementPrincipal();

        $this->redirect403IfIsNotTeledeclarationAndNotMe();

        $this->redirect('drm_etablissement', $this->etablissementPrincipal);
    }

}

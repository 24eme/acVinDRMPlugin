<?php

/**
 * Model for DRM
 *
 */
class DRM extends BaseDRM implements InterfaceMouvementDocument, InterfaceVersionDocument, InterfaceArchivageDocument {

    const NOEUD_TEMPORAIRE = 'TMP';
    const DEFAULT_KEY = 'DEFAUT';

    protected $mouvement_document = null;
    protected $version_document = null;
    protected $archivage_document = null;
    protected $suivante = null;

    public function  __construct() {
        parent::__construct();   
        $this->initDocuments();
    }

    public function __clone() {
        parent::__clone();
        $this->initDocuments();
    }   

    protected function initDocuments() {
        $this->mouvement_document = new MouvementDocument($this);
        $this->version_document = new VersionDocument($this);
        $this->archivage_document = new ArchivageDocument($this);
    }

    public function constructId() {

        $this->set('_id', DRMClient::getInstance()->buildId($this->identifiant, 
                                                            $this->periode, 
                                                            $this->version));
    }

    public function getCampagne() {

        return $this->_get('campagne');
    }

    public function getPeriodeAndVersion() {

        return DRMClient::getInstance()->buildPeriodeAndVersion($this->periode, $this->version);
    }

    public function getMois() {
        
        return DRMClient::getInstance()->getMois($this->periode);
    }

    public function getAnnee() {
        
        return DRMClient::getInstance()->getAnnee($this->periode);
    }

    public function getDate() {
        
        return DRMClient::getInstance()->buildDate($this->periode);
    }

    public function setPeriode($periode) {
        $this->campagne = DRMClient::getInstance()->buildCampagne($periode);

        return $this->_set('periode', $periode);
    }

    public function getProduit($hash, $labels = array()) {
        if (!$this->exist($hash)) {

            return false;
        }

        return $this->get($hash)->details->getProduit($labels);
    }

    public function addProduit($hash, $labels = array()) {
      if ($p = $this->getProduit($hash, $labels)) {
        return $p;
      }
      
      $detail = $this->getOrAdd($hash)->details->addProduit($labels);
      $detail->produit_libelle = $detail->getLibelle($format = "%g% %a% %m% %l% %co% %ce% %la%");
     
      return $detail;
    }

    public function getDepartement() {
        if($this->declarant->siege->code_postal )  {
          return substr($this->declarant->siege->code_postal, 0, 2);
        }

        return null;
    }

    public function getDetails() {
        
        return $this->declaration->getProduits();
    }

    public function getDetailsAvecVrac() {
        $details = array();
        foreach ($this->getDetails() as $d) {
        if ($d->sorties->vrac)
            $details[] = $d;
        }
        
        return $details;
    }
    
   public function getVracs() {
        $vracs = array();
        foreach ($this->getDetails() as $d) {
        if ($vrac = $d->sorties->vrac_details)
            $vracs[] = $vrac;
        }
        
        return $vracs;
    }

    public function generateSuivante() 
    {

        return $this->generateSuivanteByPeriode(DRMClient::getInstance()->getPeriodeSuivante($this->periode));
    }

    public function generateSuivanteByPeriode($periode) 
    {
        $is_just_the_next_periode = (DRMClient::getInstance()->getPeriodeSuivante($this->periode) == $periode);
        $keepStock = ($periode > $this->periode);

        $drm_suivante = clone $this;
        $drm_suivante->init(array('keepStock' => $keepStock));
        $drm_suivante->update();
        $drm_suivante->periode = $periode;

        if ($is_just_the_next_periode) {
            $drm_suivante->precedente = $this->_id;
        }

        return $drm_suivante;
    }

    public function init($params = array()) {
      	parent::init($params);

        $this->remove('douane');
        $this->add('douane');
        $this->remove('declarant');
        $this->add('declarant');
        $this->remove('editeurs'); 
        $this->add('editeurs'); 

        $this->version = null;
        $this->raison_rectificative = null;
        $this->etape = null;
        $this->precedente = null;
        $this->remove('editeurs'); 
        $this->add('editeurs');

        $this->archivage_document->reset();
       
        $this->devalide();
    }

    public function setDroits() {
        $this->remove('droits');
        $this->add('droits');
        foreach ($this->declaration->certifications as $certification) {
            foreach ($certification->genres as $genre) {
    	        foreach ($genre->appellations as $appellation) {
                    $appellation->updateDroits($this->droits);
    	        }
            }
        }
    }

    public function getEtablissement() {
    	
        return EtablissementClient::getInstance()->find($this->identifiant);
    }
    
    public function getInterpro() {
      	if ($this->getEtablissement()) {

         	return $this->getEtablissement()->getInterproObject();
     	}
    }
    
    public function getDRMHistorique() {

        return $this->store('drm_historique', array($this, 'getDRMHistoriqueAbstract'));
    }

    public function getPrecedente() {
        if ($this->exist('precedente') && $this->_get('precedente')) {
	        
            return DRMClient::getInstance()->find($this->_get('precedente'));
        } else {
            
            return new DRM();
        }
    }

    public function getSuivante() {
        if(is_null($this->suivante)) {
            $periode = DRMClient::getInstance()->getPeriodeSuivante($this->periode);
            $this->suivante = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($this->identifiant, $periode);
        }
      
       return $this->suivante;
    }

    public function isSuivanteCoherente() {
        $drm_suivante = $this->getSuivante();

        if(!$drm_suivante) {

            return true;
        }

        if ($this->declaration->total != $drm_suivante->declaration->total_debut_mois) {

           return false;
        }

        if (count($this->getDetails()) != count($drm_suivante->getDetails())) {

           return false;
        }

        if ($this->droits->douane->getCumul() != $drm_suivante->droits->douane->getCumul()) {

           return false;
        }

        return false;        
    }

    public function devalide() {
      $this->etape = null;
      $this->clearMouvements();
      $this->valide->identifiant = '';
      $this->valide->date_saisie = '';
      $this->valide->date_signee = '';
    }

    public function isValidee() {

        return ($this->valide->date_saisie);
    }

    public function validate($options = null) {
        $this->update();
        $this->storeIdentifiant($options);
        $this->storeDates();
        $this->storeDeclarant();

        if (!isset($options['no_droits']) || !$options['no_droits']) {
           //$this->setDroits();
        }
        
        $this->setInterpros();        
        $this->generateMouvements();

        if(!isset($options['no_vracs']) || !$options['no_vracs']) {
            $this->updateVracs();
        }

        if($this->getSuivante() && $this->isSuivanteCoherente()) {
            $this->getSuivante()->precedente = $this->get('_id');
            $this->getSuivante()->save();
        }
    }

    public function storeIdentifiant($options) {
        $identifiant = $this->identifiant;

        if ($options && is_array($options)) {
            if (isset($options['identifiant']))
                $identifiant = $options['identifiant'];
        }

        $this->valide->identifiant = $identifiant;
    }

    public function storeDeclarant() {
        $this->declarant->nom = $this->getEtablissement()->nom;
    }

    public function storeDates() {
        if (!$this->valide->date_saisie) {
           $this->valide->add('date_saisie', date('c'));
        }

        if (!$this->valide->date_signee) {
           $this->valide->add('date_signee', date('c'));
        }
    }

    public function updateVracs() {        
        foreach ($this->getDetails() as $d) {            
            foreach ($d->sorties->vrac_details as $vrac_detail) {                
                $vrac = VracClient::getInstance()->find($vrac_detail->identifiant);
                $vrac->enleverVolume($vrac_detail->volume);
                $vrac->save();
            }          
        }     
    }


    public function setInterpros() {
      $i = $this->getInterpro();
      if ($i)
       $this->interpros->add(0,$i->getKey());
    }

    protected function getDRMHistoriqueAbstract() {
        
        return new DRMHistorique($this->identifiant, $this->periode);
    }

    private function getTotalDroit($type) {
        $total = 0;
        foreach ($this->declaration->certifications as $certification) {
            foreach ($certification->appellations as $appellation) {
                $total += $appellation->get('total_'.$type);
            }
        }
        return $total;  
    }

    private function interpretHash($hash) {
      if (!preg_match('|declaration/certifications/([^/]*)/appellations/([^/]*)/|', $hash, $match)) {
        
        throw new sfException($hash." invalid");
      }
      
      return array('certification' => $match[1], 'appellation' => $match[2]);
    }

    private function setDroit($type, $appellation) {
        $configurationDroits = $appellation->getConfig()->interpro->get($this->getInterpro()->get('_id'))->droits->get($type)->getCurrentDroit($this->periode);
        $droit = $appellation->droits->get($type);
        $droit->ratio = $configurationDroits->ratio;
        $droit->code = $configurationDroits->code;
        $droit->libelle = $configurationDroits->libelle;
    }
    
    public function isPaiementAnnualise() {
    	return $this->declaratif->paiement->douane->isAnnuelle();
    }

    public function getHumanDate() {
	   setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
	   
       return strftime('%B %Y', strtotime($this->periode.'-01'));
    }

    public function getEuValideDate() {
	   return strftime('%d/%m/%Y', strtotime($this->valide->date_signee));
    }
    
    public function isDebutCampagne() {
    	
        return $this->getMois() == 8;
    }

    public function getCurrentEtapeRouting() {
    	$etape = sfConfig::get('app_drm_etapes_'.$this->etape);
    	
        return $etape['url'];
    }

    public function setCurrentEtapeRouting($etape) {
    	if (!$this->isValidee()) {
    		$this->etape = $etape;
    		$this->getDocument()->save();
    	}
    }

    public function hasApurementPossible() {
    	
        return $this->declaratif->hasApurementPossible();
    }
    public function hasVrac() {
    	$detailsVrac = $this->getDetailsAvecVrac();
    	
        return (count($detailsVrac) > 0) ;
    }
    
    public function hasConditionneExport() {
        
        return ($this->declaration->getTotalByKey('sorties/export') > 0);
    }

    public function hasMouvementAuCoursDuMois() {
        
        return $this->hasVrac() || $this->hasConditionneExport();
    }

    public function isEnvoyee() {
    	if (!$this->exist('valide')) {
    		
            return false;
        }

    	if (!$this->valide->exist('status')) {
    		
            return false;
        }

    	if ($this->valide->status != DRMClient::VALIDE_STATUS_VALIDEE_ENVOYEE && $this->valide->status != DRMClient::VALIDE_STATUS_VALIDEE_RECUE) {
    		
            return false;
    	}
    		
        return true;
    }

    public function canSetStockDebutMois() {
        if (!$this->getPrecedente()) {
            
            return true;
        } elseif ($this->getPrecedente() && $this->getPrecedente()->isNew()) {

            return true;
        } elseif ($this->isDebutCampagne()) {

            return true;
        }
            
        return false;
    }

    public function hasProduits() {
    	return (count($this->declaration->getProduits()) > 0)? true : false;
    }
    
    public function hasEditeurs() {
    	return (count($this->editeurs) > 0);
    }
    
    public function getLastEditeur() {
    	if ($this->hasEditeurs()) {
    		$editeurs = $this->editeurs->toArray();

    		return array_pop($editeurs);
    	} else {

    		return null;
    	}
    }
    
    public function getUser() {
	try {
	    	return sfContext::getInstance()->getUser();
        }catch(Exception $e) {
		return null;
        }
    }
    
    public function addEditeur($compte) {
    	$editeur = $this->editeurs->add();
    	$editeur->compte = $compte->_id;
    	$editeur->nom = $compte->nom;
    	$editeur->prenom = $compte->prenom;
    	$editeur->date_modification = date('c');
    }

    protected function preSave() {
        $this->preSaveEditeur();
        
    }

    public function save() {
        $this->archivage_document->preSave();
        parent::save();
    }

    protected function preSaveEditeur() {
        if ($user = $this->getUser()) {
            if ($user->hasCredential(myUser::CREDENTIAL_ADMIN)) {
                $compte = $user->getCompte();
                $canInsertEditeur = true;
                if ($lastEditeur = $this->getLastEditeur()) {
                    $diff = Date::diff($lastEditeur->date_modification, date('c'), 'i');
                    if ($diff < 25) {
                        $canInsertEditeur = false;
                    }
                }
                if ($canInsertEditeur) {
                    $this->addEditeur($compte);
                }
            }
        }
    }

    public function __toString()
    {
        return DRMClient::getInstance()->getLibelleFromId($this->_id);
    }

    /**** VERSION ****/

    public static function buildVersion($rectificative, $modificative) {

        return VersionDocument::buildVersion($rectificative, $modificative);
    }

    public static function buildRectificative($version) {

        return VersionDocument::buildRectificative($version);
    }

    public static function buildModificative($version) {

        return VersionDocument::buildModificative($version);
    }

    public function getVersion() {

        return $this->_get('version');
    }

    public function hasVersion() {

        return $this->version_document->hasVersion();
    }

    public function isVersionnable() {
        if (!$this->isValidee()) {
           
           return false;
        }

        return $this->version_document->isVersionnable();
    }

    public function getRectificative() {

        return $this->version_document->getRectificative();
    }

    public function isRectificative() {

        return $this->version_document->isRectificative();
    }

    public function isRectifiable() {
        
        return false;
    }

    public function getModificative() {

        return $this->version_document->getModificative();
    }

    public function isModificative() {

        return $this->version_document->isModificative();
    }

    public function isModifiable() {

        return $this->version_document->isModifiable();
    }

    public function getPreviousVersion() {

       return $this->version_document->getPreviousVersion();
    }

    public function getMasterVersionOfRectificative() {
        return DRMClient::getInstance()->getMasterVersionOfRectificative($this->identifiant, 
                                                                 $this->periode, 
                                                                 self::buildVersion($this->getRectificative() - 1, 0));
    }

    public function needNextVersion() {

       return $this->version_document->needNextVersion() || !$this->isSuivanteCoherente();
    }

    public function getMaster() {

        return $this->version_document->getMaster();
    }

    public function isMaster() {

        return $this->version_document->isMaster();
    }

    public function findMaster() {

        return DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($this->identifiant, $this->periode);
    }

    public function findDocumentByVersion($version) {

        return DRMClient::getInstance()->find(DRMClient::getInstance()->buildId($this->identifiant, $this->periode, $version));
    }

    public function getMother() {

        return $this->version_document->getMother();   
    }

    public function motherGet($hash) {

        return $this->version_document->motherGet($hash);
    }

    public function motherExist($hash) {

        return $this->version_document->motherExist($hash);
    }

    public function motherHasChanged() {
        if ($this->declaration->total != $this->getMother()->declaration->total) {
           
           return true;
        }

        if (count($this->getDetails()) != count($this->getMother()->getDetails())) {
           
           return true;
        }

        if ($this->droits->douane->getCumul() != $this->getMother()->droits->douane->getCumul()) {
           
           return true;
        }

        return false;
    }

    public function getDiffWithMother() {

        return $this->version_document->getDiffWithMother();
    }

    public function isModifiedMother($hash_or_object, $key = null) {
        
        return $this->version_document->isModifiedMother($hash_or_object, $key);
    }

    public function generateRectificative() {

        return $this->version_document->generateRectificative();
    }

    public function generateModificative() {

        return $this->version_document->generateModificative();
    }

    public function generateNextVersion() {
        if (!$this->hasVersion()) {

            return $this->version_document->generateModificativeSuivante();
        }

        return $this->version_document->generateNextVersion();
    }

    public function listenerGenerateVersion($document) {
        $document->devalide();
    }

    public function listenerGenerateNextVersion($document) {
        $this->replicate($document);
        $document->precedente = $this->get('_id');
        $document->validate();
    }

    protected function replicate($drm) {
        foreach($this->getDiffWithMother() as $key => $value) {
            $this->replicateDetail($drm, $key, $value, 'total', 'total_debut_mois');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/revendique', 'stocks_debut/revendique');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/instance', 'stocks_debut/instance');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/bloque', 'stocks_debut/bloque');
        }
    }

    protected function replicateDetail(&$drm, $key, $value, $hash_match, $hash_replication) {
        if (preg_match('|^(/declaration/certifications/.+/appellations/.+/mentions/.+/lieux/.+/couleurs/.+/cepages/.+/details/.+)/'.$hash_match.'$|', $key, $match)) {
            $detail = $this->get($match[1]);
            if (!$drm->exist($detail->getHash())) {
                $drm->addProduit($detail->getCepage()->getHash(), $detail->labels->toArray());
            }
            $drm->get($detail->getHash())->set($hash_replication, $value);
        }
    }
    /**** FIN DE VERSION ****/

    /**** MOUVEMENTS ****/

    public function getMouvements() {

        return $this->_get('mouvements');
    }

    public function getMouvementsCalcule() {

        return $this->declaration->getMouvements();
    }

    public function getMouvementsCalculeByIdentifiant($identifiant) {

       return $this->mouvement_document->getMouvementsCalculeByIdentifiant($identifiant);
    }
    
    public function generateMouvements() {

        return $this->mouvement_document->generateMouvements();
    }
    
    public function findMouvement($cle){
        
        return $this->mouvement_document->findMouvement($cle);
    }

    public function clearMouvements(){
        $this->remove('mouvements');
        $this->add('mouvements');
    }

    /**** FIN DES MOUVEMENTS ****/

    /*** ARCHIVAGE ***/

    public function getNumeroArchive() {

        return $this->_get('numero_archive');
    }

    public function isArchivageCanBeSet() {

        return $this->isValidee();
    }
    
    /*** FIN ARCHIVAGE ***/
}

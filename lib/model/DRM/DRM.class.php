<?php

/**
 * Model for DRM
 *
 */
class DRM extends BaseDRM implements InterfaceVersionDocument {

    const NOEUD_TEMPORAIRE = 'TMP';
    const DEFAULT_KEY = 'DEFAUT';

    protected $version_document = null;

    public function  __construct() {
        parent::__construct();   
        $this->version_document = new VersionDocument($this);
    }

    public function constructId() {

        $this->set('_id', DRMClient::getInstance()->buildId($this->identifiant, 
                                                            $this->periode, 
                                                            $this->version));
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
      
      return $detail;
    }

    public function getDepartement() {
        if($this->declarant->siege->code_postal) {
          return substr($this->declarant->siege->code_postal, 0, 2);
        }

        return null;
    }

    public function getModeDeSaisieLibelle()
    {
        
        return DRMClient::getInstance()->getModeDeSaisieLibelle($this->mode_de_saisie);
    }

    public function getDetails() {
        
        return $this->declaration->getProduits();
    }

    public function getDetailsAvecVrac() {
        $details = array();
        foreach ($this->getDetails() as $d) {
	        if ($d->sorties->vrac && $d->hasCvo()) {
	           $details[] = $d;
            }
        }
        
        return $details;
    }

    public function generateSuivante($periode, $keepStock = true) 
    {
        $drm_suivante = clone $this;
    	$drm_suivante->init(array('keepStock' => $keepStock));
        $drm_suivante->update();
        $drm_suivante->periode = $periode;
        $drm_suivante->precedente = $this->_id;
        $drm_suivante->devalide();
	    $drm_suivante->remove('editeurs'); 
	    $drm_suivante->add('editeurs'); 
       
	    foreach ($drm_suivante->getDetails() as $detail) {
	       $drm_suivante->get($detail->getHash())->remove('vrac');
	    }

        return $drm_suivante;
    }
    
    public function init($params = array()) {
      	parent::init($params);
      	$keepStock = isset($params['keepStock']) ? $params['keepStock'] : true;
        $this->remove('douane');
        $this->add('douane');
        $this->remove('declarant');
        $this->add('declarant');
        $this->version = null;
        $this->raison_rectificative = null;
        $this->etape = null;

        if (!$keepStock) {
        	$this->declaratif->adhesion_emcs_gamma = null;
        	$this->declaratif->paiement->douane->frequence = null;
        	$this->declaratif->paiement->douane->moyen = null;
        	$this->declaratif->paiement->cvo->frequence = null;
        	$this->declaratif->paiement->cvo->moyen = null;
        	$this->declaratif->caution->dispense = null;
        	$this->declaratif->caution->organisme = null;
        }
        $this->declaratif->defaut_apurement = null;
        $this->declaratif->daa->debut = null;
        $this->declaratif->daa->fin = null;
        $this->declaratif->dsa->debut = null;
        $this->declaratif->dsa->fin = null;
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
    	if (!$this->identifiant) {
		    throw new Exception('pas d\'établissement saisi pour '.$this->_id);
        }
        
        $e = EtablissementClient::getInstance()->retrieveById($this->identifiant);
	    
        if (!$e) {
	       throw new Exception('pas d\'établissement correspondant à '.$this->identifiant);
	    }
	   
        return $e;
    }
    
    public function getInterpro() {
    	
        if ($this->getEtablissement())
            return $this->getEtablissement()->getInterproObject();
    }
    
    public function getHistorique() {

        return $this->store('historique', array($this, 'getHistoriqueAbstract'));
    }

    public function getPrecedente() {
        if ($this->exist('precedente') && $this->_get('precedente')) {
            
            return DRMClient::getInstance()->find($this->_get('precedente'));
        } else {
            
            return new DRM();
        }
    }

    public function getSuivante() {
       $periode = DRMClient::getInstance()->getPeriodeSuivante($this->periode);

       $next_drm = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($this->identifiant, $periode);
       if (!$next_drm) {

           return null;
       }
       
       return $next_drm;
    }

    public function devalide() {
        $this->etape = null;
        $this->valide->identifiant = '';
        $this->valide->date_saisie = '';
        $this->valide->date_signee = '';
    }

    public function isValidee() {

        return ($this->valide->date_saisie);
    }

    public function validate($options = null) {
        if ($this->hasApurementPossible()) {
            $this->apurement_possible = 1;
        }

        if ($next_drm = $this->getSuivante()) {
            $next_drm->precedente = $this->_id;
            $next_drm->save();
        }
    
        $this->storeIdentifiant($options);
        $this->storeDates();
        $this->storeDroits($options);
        $this->setInterpros();
        $this->updateVrac();
    }

    public function storeDroits($options) {
        if (!isset($options['no_droits']) || !$options['no_droits']) {
           $this->setDroits();
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

    public function storeDates() {
        if (!$this->valide->date_saisie) {
           $this->valide->add('date_saisie', date('c'));
        }

        if (!$this->valide->date_signee) {
           $this->valide->add('date_signee', date('c'));
        }

    }

    public function updateVrac() {
    	foreach ($this->getDetails() as $detail) {
			foreach ($detail->vrac as $numero => $vrac) {
				$volume = $vrac->volume;
				if ($this->isRectificative() && !$this->isModifiedMasterDRM($vrac, 'volume')) {
					continue;
					
				}
				
				if ($this->isRectificative() && $this->getDRMMaster()->exist($vrac->getHash())) {
					$volume = $volume - $this->getDRMMaster()->get($vrac->getHash())->volume;
				}
				
				if ($volume == 0) {
					continue;
				}
				$contrat = VracClient::getInstance()->findByNumContrat($numero);
				$contrat->integreVolumeEnleve($volume);
				$contrat->save();
			}
      	}
    }

    public function setInterpros() {
        $i = $this->getInterpro();
        if ($i) {
	        $this->interpros->add(0,$i->getKey());
        }
    }

    public function save() {
        if (!preg_match('/^2\d{3}-[01][0-9]$/', $this->periode)) {
            throw new sfException('Wrong format for periode ('.$this->periode.')');
        }
        if ($user = $this->getUser()) {
        	if ($user->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {
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
        if ($this->isNew()) {
        	$etablissement = $this->getEtablissement();
        	$this->etablissement_num_interne = $etablissement->num_interne;
        }

        return parent::save();
    }

    protected function getHistoriqueAbstract() {
        
        return DRMClient::getInstance()->getDRMHistorique($this->identifiant);
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
    	
        return DRMPaiement::isDebutCampagne((int)$this->getMois());
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
    	if (
    		$this->declaratif->daa->debut ||
    		$this->declaratif->daa->fin ||
    		$this->declaratif->dsa->debut ||
    		$this->declaratif->dsa->debut ||
    		$this->declaratif->adhesion_emcs_gamma
    	) {
    		
            return true;
    	} else {
    		
            return false;
    	}
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
    	if (!$this->exist('valide'))
    		return false;
    	if (!$this->valide->exist('status'))
    		return false;
    	if ($this->valide->status != DRMClient::VALIDE_STATUS_VALIDEE_ENVOYEE && $this->valide->status != DRMClient::VALIDE_STATUS_VALIDEE_RECUE) {
    		return false;
    	} else {
    		return true;
    	}
    }
    /*
     * Pour les users administrateur
     */
    public function canSetStockDebutMois() {
    	if (!$this->getPrecedente()) {
    		return true;
    	} elseif ($this->getPrecedente() && $this->getPrecedente()->isNew()) {
    		return true;
    	} elseif ($this->isDebutCampagne()) {
    		return true;
    	} else {
    		return false;
    	}
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
    	return sfContext::getInstance()->getUser();
    }
    
    public function addEditeur($compte) {
    	$editeur = $this->editeurs->add();
    	$editeur->compte = $compte->_id;
    	$editeur->nom = $compte->nom;
    	$editeur->prenom = $compte->prenom;
    	$editeur->date_modification = date('c');
    }
    
    public function isRectificativeEnCascade() {
    	if (!$this->isRectificative()) {
    		return false;
    	}
    	if ($mother = $this->getMother()) {
    		return ($mother->getPrecedente()->_id != $this->getPrecedente()->_id)? true : false;
    	} else {
    		return false;
    	}
    }

    public function isSupprimable() {

        return !$this->isValidee() && !$this->isRectificativeEnCascade();
    }

    public function isSupprimableOperateur() {

        return !$this->isEnvoyee() && !$this->isRectificativeEnCascade();
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
        
        return $this->version_document->isRectifiable();
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
                                                                 $this->getRectificative() - 1);
    }

    public function needNextVersion() {

       return $this->version_document->needNextVersion();      
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

        return $this->version_document->generateNextVersion();
    }

    public function listenerGenerateVersion($document) {
        $document->devalide();
    }

    public function listenerGenerateNextVersion($document) {
        $this->replicate($document);
        $document->update();
        $document->devalide();
    }

    protected function replicate($drm) {
        foreach($this->getDiffWithMother() as $key => $value) {
            $this->replicateDetail($drm, $key, $value, 'total', 'total_debut_mois');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/bloque', 'stocks_debut/bloque');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/warrante', 'stocks_debut/warrante');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/instance', 'stocks_debut/instance');
            $this->replicateDetail($drm, $key, $value, 'stocks_fin/commercialisable', 'stocks_debut/commercialisable');
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
    
}

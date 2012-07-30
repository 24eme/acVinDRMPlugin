<?php

class DRMClient extends acCouchdbClient {
   
    /**
     *
     * @return DRMClient
     */
    public static function getInstance() {
      return acCouchdbManager::getClient("DRM");
    }

    public function getId($identifiant, $campagne, $rectificative = null) {
      return 'DRM-'.$identifiant.'-'.$this->getCampagneAndRectificative($campagne, $rectificative);
    }

    public function getCampagne($annee, $mois) {

      return sprintf("%04d-%02d", $annee, $mois);
    }

    public function getAnnee($campagne) {

      return preg_replace('/([0-9]{4})-([0-9]{2})/', '$1', $campagne);
    }

    public function getMois($campagne) {

      return preg_replace('/([0-9]{4})-([0-9]{2})/', '$2', $campagne);
    }

    public function getCampagneAndRectificative($campagne, $rectificative = null) {
      if($rectificative  && $rectificative > 0) {
        return $campagne.'-R'.sprintf("%02d", $rectificative);
      } 
      return $campagne;
    }

    public function findLastByIdentifiantAndCampagne($identifiant, $campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
      $drms = $this->viewByIdentifiantCampagne($identifiant, $campagne);

      foreach($drms as $id => $drm) {

        return $this->find($id, $hydrate);
      }

      return null;
    }

    public function findByIdentifiantCampagneAndRectificative($identifiant, $campagne, $rectificative = null, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

      return $this->find($this->getId($identifiant, $campagne, $rectificative, $hydrate));
    }
    
    public function retrieveOrCreateByIdentifiantAndCampagne($identifiant, $annee, $mois, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
      if ($obj = $this->findLastByIdentifiantAndCampagne($identifiant, $this->getCampagne($annee, $mois), $hydrate)) {
        return $obj;
      }

      $obj = new DRM();
      $obj->identifiant = $identifiant;
      $obj->campagne = $this->getCampagne($annee, $mois);
      
      return $obj;
    }

    public function findByInterproDate($interpro, $date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
      $drm = array();
      foreach ($this->viewByInterproDate($interpro, $date) as $id => $key) {
	$drm[] = $this->find($id);
      }
      return $drm;
    }

    protected function viewByInterproDate($interpro, $date) {
      $rows = acCouchdbManager::getClient()
	->startkey(array($interpro, $date))
	->endkey(array($interpro, array()))
	->getView("drm", "date")
	->rows;

      $drms = array();

      foreach($rows as $row) {
        $drms[$row->id] = $row->key;
      }
      
      return $drms;
    }

    protected function viewByIdentifiantCampagne($identifiant, $campagne) {
      $annee = $this->getAnnee($campagne);
      $mois = $this->getMois($campagne);

      $rows = acCouchdbManager::getClient()
            ->startkey(array($identifiant, $annee, $mois))
              ->endkey(array($identifiant, $annee, $mois, array()))
              ->reduce(false)
              ->getView("drm", "all")
              ->rows;
      
      $drms = array();

      foreach($rows as $row) {
        $drms[$row->id] = $row->key;
      }
      
      krsort($drms);
      
      return $drms;
    }
  
  public function findProduits() {
    return $this->startkey(array("produit"))
              ->endkey(array("produit", array()))->getView('drm', 'produits');
  }
  
  public function getAllProduits() {
    $produits = $this->findProduits()->rows;
    $result = array();
    foreach ($produits as $produit) {
    	$result[] = $produit->key[1];
    }
    return $result;
  }
  
	public function createDoc($identifiant, $campagne = null) {
		$historique = new DRMHistorique($identifiant);
    	if (!$campagne) {
    		$drm = $this->createNewDoc($historique);
    	} else {
    		$drm = $this->createDocByCampagne($historique, $campagne);
    	}
        return $drm;
    }
    
    private function createNewDoc($historique)
    {
    	$lastDRM = $historique->getLastDRM();
    	if ($lastDRM && $drm = DRMClient::getInstance()->find(key($lastDRM))) {
    		if ($drm->isValidee()) {
    			$drm = $drm->generateSuivante($this->getCurrentCampagne());
    		}
    	} else {
    		$drm = new DRM();
    		$drm->identifiant = $historique->etablissement;
    		$drm->campagne = $this->getCurrentCampagne();
    	}
    	return $drm;
    }
    
    private function createDocByCampagne($historique, $campagne)
    {
       	$prev_drm = $historique->getPrevByCampagne($campagne);
       	$next_drm = $historique->getNextByCampagne($campagne);
       	if ($prev_drm) {
       	   $prev_drm = DRMClient::getInstance()->find($prev_drm[DRMHistorique::VIEW_INDEX_ID]);
           $drm = $prev_drm->generateSuivante($campagne);
       	} elseif ($next_drm) {
       	   $next_drm = DRMClient::getInstance()->find($next_drm[DRMHistorique::VIEW_INDEX_ID]);
           $drm = $next_drm->generateSuivante($campagne, false);
       	} else {
       		$drm = $this->createNewDoc($historique);
       		$drm->campagne = $campagne;
       	}
       	return $drm;
    }
    
    public function getCurrentCampagne() {
      return CurrentClient::getCurrent()->campagne;
    }

}

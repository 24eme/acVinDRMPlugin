<?php

class drmComponents extends sfComponents {

  public function executeChooseEtablissement() {
    if (!$this->form) {
      $this->form = new DRMEtablissementChoiceForm('INTERPRO-inter-loire',
             array('identifiant' => $this->identifiant));
    }
  }

    public function executeEtapes() {
        $this->config_certifications = ConfigurationClient::getCurrent()->declaration->certifications;
        $this->certifications = array();
        
        $i = 3;
        foreach ($this->config_certifications as $certification_config) {
            if ($this->drm->exist($certification_config->getHash())) {
            	$certif = $this->drm->get($certification_config->getHash());
            	if ($certif->hasMouvementCheck()) {
	                $this->certifications[$i] = $this->drm->get($certification_config->getHash());
	                $i++;
            	}
            }
        }
        $nbCertifs = count($this->certifications);
        if (count($this->drm->getDetailsAvecVrac()) > 0) {
	        $this->numeros = array(
	            'informations' => 1,
	            'ajouts_liquidations' => 2,
	            'recapitulatif' => 3,
	            'vrac' => 3 + $nbCertifs,
	            'declaratif' => 4 + $nbCertifs,
	            'validation' => 5 + $nbCertifs,
	        );
        } else {
	        $this->numeros = array(
	            'informations' => 1,
	            'ajouts_liquidations' => 2,
	            'recapitulatif' => 3,
	            'declaratif' => 3 + $nbCertifs,
	            'validation' => 4 + $nbCertifs,
	        );        	
        }
        
        $this->numero = $this->numeros[$this->etape];
        if(isset($this->numeros[$this->drm->etape])) 
            $this->numero_autorise = $this->numeros[$this->drm->etape];
        else 
            $this->numero_autorise = '';
        $this->numero_vrac = (isset($this->numeros['vrac']))? $this->numeros['vrac'] : null;
        $this->numero_declaratif = $this->numeros['declaratif'];
        $this->numero_validation = $this->numeros['validation'];

        if ($this->etape == 'recapitulatif') {
            foreach ($this->config_certifications as $certification_config) {
                if ($this->drm->exist($certification_config->getHash())) {
                    if ($this->certification == $certification_config->getKey()) {
                        break;
                    }
                    $this->numero++;
                }
            }
        }
    }

    public function executeHistoriqueItem() {
        $this->periode_version = DRMClient::getInstance()->buildPeriodeAndVersion($this->drm[DRMHistorique::VIEW_PERIODE], $this->drm[DRMHistorique::VIEW_INDEX_VERSION]);
    	$this->etablissement_identifiant = $this->drm[DRMHistorique::VIEW_INDEX_ETABLISSEMENT];
        $this->valide = $this->drm[DRMHistorique::VIEW_INDEX_STATUS] && $this->drm[DRMHistorique::VIEW_INDEX_STATUS] > 0;
        $this->titre = $this->drm[DRMHistorique::VIEW_PERIODE];
        if($this->drm[DRMHistorique::VIEW_INDEX_VERSION]) {
            $this->titre .= ' '.$this->drm[DRMHistorique::VIEW_INDEX_VERSION];
        }
        $this->derniere = $this->drm[DRMHistorique::DERNIERE];
		$this->drm = DRMClient::getInstance()->find($this->drm[7]);
    }

    public function executeHistoriqueList() {
        if (isset($this->limit)) {
            $this->list = $this->historique->getSliceDRMs($this->limit);
        } else {
            $this->list = $this->historique->getDRMsParCampagneCourante();   
        }
        $this->futurDRM = current($this->historique->getFutureDRM());
        $this->hasNewDRM = false;
        if (DRMClient::getInstance()->getCurrentPeriode() >= ($this->futurDRM[DRMHistorique::VIEW_PERIODE]) && !$this->historique->hasDRMInProcess()) {
            $this->hasNewDRM = true;
            if (isset($this->limit)) {
                $this->limit--;
            }
        }
    }

    public function executeCalendrier() {
        $this->calendrier = new DRMCalendrier($this->etablissement->identifiant, '2012-2013');
    }

    public function executeStocks() {
        $this->calendrier = new DRMCalendrier($this->etablissement->identifiant, '2012-2013');
        $this->details = array();
        foreach($this->calendrier->getPeriodes() as $periode) {
            $drm = $this->calendrier->getDRM($periode);
            if($drm && $drm->isValidee()) {
                foreach($drm->getDetails() as $detail) {
                    $d = new stdClass();
                    $d->mois = ucfirst($this->calendrier->getPeriodeLibelle($periode));
                    $d->libelle = $detail->getLibelle();
                    $d->total_debut_mois = $detail->total_debut_mois;
                    $d->total_entrees = $detail->total_entrees;
                    $d->total_sorties = $detail->total_sorties;
                    $d->total = $detail->total;
                    $this->details[] = $d;
                }
            }      
        }
    }

}

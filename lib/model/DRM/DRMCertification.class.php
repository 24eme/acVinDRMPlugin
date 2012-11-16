<?php
/**
 * Model for DRMCertification
 *
 */

class DRMCertification extends BaseDRMCertification {

	public function getPreviousSisterWithMouvementCheck() {
        $item = $this->getPreviousSister();
        $sister = null;

        if ($item) {
            $sister = $item;
        }

        if ($sister && !$sister->hasMouvementCheck()) {

            return $sister->getPreviousSisterWithMouvementCheck();
        }

        return $sister;
	}

	public function getNextSisterWithMouvementCheck() {
		$item = $this->getNextSister();
        $sister = null;

        if ($item) {
            $sister = $item;
        }

        if ($sister && !$sister->hasMouvementCheck()) {

            return $sister->getNextSisterWithMouvementCheck();
        }

        return $sister;
	}

	public function getChildrenNode() {

		return $this->genres;
	}
    
    public function hasDetailLigne($ligne)
    {
    	if ($configurationDetail = $this->getConfig()->exist('detail')) {
    		$line = $configurationDetail->get($ligne);
    		if (!is_null($line->readable)) {
    			return $line->readable;
    		}
    	}
    	return false;
    }
    
    public function getDroit($type) {
      return $this->getConfig()->getDroits($this->getInterproKey())->get($type)->getCurrentDroit($this->getPeriode());
    }

    public function getDroits() {
      $conf = $this->getConfig();
      $droits = array();
      foreach ($conf->getDroits($this->getInterproKey()) as $key => $droit) {
	$droits[$key] = $droit->getCurrentDroit($this->getPeriode());
      }
      return $droits;
    }
    public function getInterproKey() {
      if (!$this->getDocument()->getInterpro())
	return array();
      return $this->getDocument()->getInterpro()->get('_id');
    }
    public function getPeriode() {
      return $this->getDocument()->getPeriode();
    }

}
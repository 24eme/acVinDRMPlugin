<?php
/**
 * Model for DRMESDetailVrac
 *
 */

class DRMESDetailVrac extends BaseDRMESDetailVrac {

    const IDENTIFIANT_SANS_CONTRAT = "SANSCONTRAT";

    protected $vrac = null;

    public function getProduitDetail() {

        return $this->getParent()->getProduitDetail();
    }

    public function getVrac() {
        if (is_null($this->vrac)) {
            $this->vrac = VracClient::getInstance()->find($this->identifiant);
        }

        return $this->vrac;
    }

    public function isSansContrat() {

        return $this->identifiant == self::IDENTIFIANT_SANS_CONTRAT;
    }

    public function getIdentifiantLibelle() {
        if($this->isSansContrat()) {

            return null;
        }

        return $this->getVrac()->getNumeroArchive();
    }
}

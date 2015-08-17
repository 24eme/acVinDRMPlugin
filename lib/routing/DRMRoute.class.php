<?php

class DRMRoute extends sfObjectRoute implements InterfaceEtablissementRoute {

    protected $drm = null;
    
    protected function getObjectForParameters($parameters) {

        $this->drm = DRMClient::getInstance()->find('DRM-'.$parameters['identifiant'].'-'.$parameters['periode_version']);

        if (!$this->drm) {
            throw new sfError404Exception(sprintf("La DRM n'a pas été trouvée"));
        }

	$myUser = sfContext::getInstance()->getUser();

        if (!$myUser->hasCredential('transactions') && 
	    $myUser->getCompte()->getSociete()->identifiant != $this->drm->getEtablissement()->getSociete()->identifiant) {
		throw new sfError404Exception("Vous n'avez pas le droit d'accéder à cette DRM");
	}

        $control = isset($this->options['control']) ? $this->options['control'] : array();

		if (in_array('valid', $control) && !$this->drm->isValidee()) {
			
            throw new sfException('La DRM doit être validée');
		}

		if (in_array('edition', $control) && $this->drm->isValidee()) {

			throw new sfException('La DRM ne peut pas être éditée car elle est validé');
		}

        return $this->drm;
    }

    protected function doConvertObjectToArray($object) {  
        $parameters = array("identifiant" => $object->getIdentifiant(), "periode_version" => $object->getPeriodeAndVersion());
        return $parameters;
    }
    
    public function getDRMConfiguration() {
        
        return $this->getDRM()->getConfig();
    }

    public function getDRM() {
        if (!$this->drm) {
            $this->getObject();
        }

        return $this->drm;
    }

    public function getEtablissement() {

        return $this->getDRM()->getEtablissement();
    }
}

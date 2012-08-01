<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneForm
 * @author mathurin
 */
class DRMEtablissementForm extends baseForm {

  private $drm_etablissements = null;

    public function configure()
    {
        $this->setWidget('etablissement_identifiant', new sfWidgetFormChoice(array('choices' =>  $this->getDRMEtablissements()), array('class' => 'autocomplete', 'data-ajax' => $this->getUrlAutocomplete())));

        $this->widgetSchema->setLabels(array(
            'vendeur_identifiant' => 'Sélectionner un etablissement&nbsp;:',
        ));
        
        $this->setValidators(array(
				   'etablissement_identifiant' => new sfValidatorString(array('required' => true)),
				   ));
        
        
        $this->validatorSchema['etablissement_identifiant']->setMessage('required', 'Le choix d\'un etablissement est obligatoire');        
        
        $this->widgetSchema->setNameFormat('etablissement[%s]');
    }
    
    public function getDRMEtablissements()
    {
        return array();
    }

    public function getEtablissements($famille) {
        $etablissements = array('' => '');
        $datas = EtablissementClient::getInstance()->findByFamille($famille)->rows;
        foreach($datas as $data) {
            $labels = array($data->key[4], $data->key[3], $data->key[1]);
            $etablissements[str_replace('ETABLISSEMENT-', '', $data->id)] = implode(', ', array_filter($labels));
        }
        return $etablissements;
    }

    public function getUrlAutocomplete() {

        return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_byfamilles', array('familles' => EtablissementFamilles::FAMILLE_PRODUCTEUR));
    }
    
}


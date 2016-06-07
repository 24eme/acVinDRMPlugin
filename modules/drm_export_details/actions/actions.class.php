<?php

class drm_export_detailsActions extends drmGeneriqueActions
{

    public function executeProduit(sfWebRequest $request) {
        $this->detail = $this->getRoute()->getDRMDetail();
        $this->drm = $this->detail->getDocument();
        $this->isTeledeclarationMode = $this->isTeledeclarationDrm();
        $this->catKey = $request->getParameter('cat_key');
        $this->key = $request->getParameter('key');
        $this->form = new DRMDetailExportForm($this->detail->get($this->catKey)->get($this->key."_details"), array(),  array('isTeledeclarationMode' => $this->isTeledeclarationMode && false));

        if ($request->isMethod(sfRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {
                $this->form->update();
                $this->drm->update();
                $this->drm->save();
                if($request->isXmlHttpRequest())
                {
                    $this->getUser()->setFlash("notice", 'Le détail des exports a été mis à jour avec success.');
                    return $this->renderText(json_encode(array("success" => true, "type" => $this->catKey."_".$this->key, "volume" => $this->detail->get($this->catKey)->get($this->key), "document" => array("id" => $this->drm->get('_id'),"revision" => $this->drm->get('_rev')))));
                }

                return $this->redirect('drm_edition_detail', $this->detail);
            }
            if($request->isXmlHttpRequest())
            {
                return $this->renderText(json_encode(array('success' => false ,'content' => $this->getPartial('formContent', array('form' => $this->form, 'detail' => $this->detail,'isTeledeclarationMode' => $this->isTeledeclarationMode, 'catKey' => $catKey, 'key' => $key)))));
            }
        }
    }
}

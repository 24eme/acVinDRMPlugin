<?php use_helper('DRM'); ?>
<?php $etablissements = $etablissement->getSociete()->getEtablissementsObj(false); ?>
<?php $multiEtablissement = (count($etablissements) > 1); ?>
<div class="section_label_maj <?php echo ($isTeledeclarationMode) ? 'section_label_maj_teledeclaration_drm' : '' ?>" id="calendrier_drm">
    <form method="POST">
        <?php echo $formCampagne->renderGlobalErrors() ?>
        <?php echo $formCampagne->renderHiddenFields() ?>
        <?php echo $formCampagne; ?> <input class="btn_majeur btn_vert" type="submit" value="changer"/>
    </form>
    <div class="bloc_form">
        <div class="ligne_form ligne_compose">
            <ul class="liste_mois">
                <?php foreach ($calendrier->getPeriodes() as $periode): ?>
                    <?php include_partial('drm/calendrierItem', array('calendrier' => $calendrier, 'periode' => $periode, 'etablissement' => $etablissement, 'isTeledeclarationMode' => $isTeledeclarationMode, 'etablissements' => $etablissements, 'multiEtablissement' => $multiEtablissement)); ?>
                <?php endforeach; ?>

            </ul>
        </div>
    </div>
</div>
<?php
if ($isTeledeclarationMode):
    foreach ($calendrier->getPeriodes() as $periode):
        if ($multiEtablissement):
            foreach ($etablissements as $etb):
                $statut = $calendrier->getStatut($periode, $etb->etablissement, true);
                if ($statut == DRMCalendrier::STATUT_NOUVELLE):
                    include_partial('drm/creationDrmPopup', array('periode' => $periode, 'identifiant' => $etb->etablissement->identifiant)); 
                endif;
            endforeach;
        else:
            $statut = $calendrier->getStatut($periode, $etablissement, true);
            if ($statut == DRMCalendrier::STATUT_NOUVELLE):
               include_partial('drm/creationDrmPopup', array('periode' => $periode, 'identifiant' => $etablissement->identifiant)); 
            endif;
        endif;
    endforeach;
endif;
?>

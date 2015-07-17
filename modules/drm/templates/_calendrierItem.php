<?php use_helper('DRM'); ?>
<?php $etablissements = $etablissement->getSociete()->getEtablissementsObj(false); ?>
<?php $multiEtablissement = (count($etablissements) > 1); ?>


<li class="bloc_mois <?php echo getClassEtatDRMCalendrier($isTeledeclarationMode, $calendrier, $periode); ?>">
    <p class="mois"><?php echo $calendrier->getPeriodeLibelle($periode) ?></p>

    <div class="mois_infos">
        <?php if ($isTeledeclarationMode && $multiEtablissement): ?>
            <ul class="liste_etablissements clearfix">
                <?php foreach ($etablissements as $etb): ?>
                    <li class="<?php echo getEtatDRMPictoCalendrier($calendrier, $periode, $etb->etablissement); ?>">
                        <button class="btn_etablissement" type="button"><?php echo $etb->etablissement->nom; ?></button>
                        <div class="etablissement_tooltip">
                            <p class="etablissement_nom"><?php echo $etb->etablissement->nom; ?></p>
                            <p>Etat : <span class="statut"><?php echo getEtatDRMCalendrier($calendrier, $periode, $etb->etablissement); ?></span>&nbsp;<?php echo getTeledeclareeLabelCalendrier($isTeledeclarationMode, $calendrier, $periode, $etb->etablissement) ?></p>
                              <?php if (hasALink($isTeledeclarationMode, $calendrier, $periode, $etb->etablissement)) : ?> 
                                <a href="<?php echo getEtatDRMHrefCalendrier($calendrier, $periode, $etb->etablissement); ?>" class="action"><?php echo getEtatDRMLibelleCalendrier($calendrier, $periode, $etb->etablissement); ?></a>
                            <?php endif; ?> 
                        </div>
                    </li>

                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="<?php echo getEtatDRMPictoCalendrier($calendrier, $periode); ?>">
                <p class="etablissement_nom"><?php echo $etablissement->nom; ?></p>
                <p>Etat : <span class="statut"><?php echo getEtatDRMCalendrier($calendrier, $periode); ?></span>&nbsp;<?php echo getTeledeclareeLabelCalendrier($isTeledeclarationMode, $calendrier, $periode) ?></p>
       
                    <?php if (hasALink($isTeledeclarationMode, $calendrier, $periode)) : ?> 
                    <a href="<?php echo getEtatDRMHrefCalendrier($calendrier, $periode); ?>" class="action"><?php echo getEtatDRMLibelleCalendrier($calendrier, $periode); ?></a>
                <?php endif; ?> 
            </div>
        <?php endif; ?>
    </div>
</li>

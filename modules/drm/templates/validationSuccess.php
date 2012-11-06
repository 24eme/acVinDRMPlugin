<?php include_component('global', 'navTop', array('active' => 'drm')); ?>

<section id="contenu">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php include_component('drm', 'etapes', array('drm' => $drm, 'etape' => 'validation', 'pourcentage' => '100')); ?>

    <!-- #principal -->
    <section id="principal">
        <form id="formValidation" action="<?php echo url_for('drm_validation', $drm) ?>" method="post">
            <?php echo $form->renderGlobalErrors() ?>
            <?php echo $form->renderHiddenFields() ?>
            <div id="application_dr">
               
                <div id="validation_intro">
                    <h2>Validation</h2>
                    <p>Vous êtes sur le point de valider votre DRM. Merci de vérifier vos données.</p>
                </div>
                
                <?php if ($drmValidation->hasErrors() || $drmValidation->hasWarnings()) { ?>
                <div id="contenu_onglet">
               
                    <div id="retours">
                        <?php
                        if ($drmValidation->hasErrors()) {
                            include_partial('erreurs', array('drm' => $drm, 'drmValidation' => $drmValidation));
                        }
                        if ($drmValidation->hasWarnings()) {
                            include_partial('vigilances', array('drm' => $drm, 'drmValidation' => $drmValidation));
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
                
                <?php if ($drmValidation->hasEngagements()) { ?>
                <div id="contenu_onglet" class="tableau_ajouts_liquidations">
                    <?php include_partial('engagements', array('drm' => $drm, 'drmValidation' => $drmValidation, 'form' => $form)); ?>                    
                </div>
                <?php } ?>
                
                <div id="contenu_onglet">
                    <?php if($drm->declaration->hasMouvement() && !$drm->declaration->hasStockEpuise()):  ?>
                        <?php include_partial('drm/recap', array('drm' => $drm)) ?>
                    <?php else: ?>
                        <?php include_partial('drm/pasDeMouvement', array('drm' => $drm)) ?>
                    <?php endif; ?>
                </div>
            </div>
            <a id="telecharger_pdf" href="<?php echo url_for('drm_pdf', $drm) ?>">Visualisez le brouille de DRM en PDF</a>
            
            <div id="btn_etape_dr">
                <?php if ($drm->mode_de_saisie == DRMClient::MODE_DE_SAISIE_PAPIER): ?>
                <a href="<?php echo url_for('drm_vrac', array("sf_subject" => $drm, "precedent" => true))?>" class="btn_prec">
                    <span>Précédent</span>
                </a>
                <?php else: ?>
                <a href="<?php echo url_for('drm_declaratif', $drm) ?>" class="btn_prec">
                    <span>Précédent</span>
                </a>
                <?php endif; ?>
                <?php if ($drmValidation->hasError('stock') && $sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR)): ?>
                <button type="submit" class="btn_suiv"><span>Générer rectificative suivante</span></button>
                <?php else: ?>
                <button type="submit" class="btn_suiv"<?php if ($drmValidation->hasErrors()): ?> disabled="disabled"<?php endif; ?>><span>Valider</span></button>
                <?php endif; ?>
            </div>

        </form>
    </section>
</section>
<script type="text/javascript">
	$(document).ready(function () {
		$("#formValidation").submit(function(){
			return confirm("Une fois votre déclaration validée, vous ne pourrez plus la modifier.\n\nConfirmez vous la validation de votre DRM ?");
		});
	});
</script>

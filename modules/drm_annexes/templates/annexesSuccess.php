<?php use_helper("Date"); ?>
<?php use_helper('DRM'); ?>

<!-- #principal -->
<section id="principal" class="drm">

    <?php include_partial('drm/etapes', array('drm' => $drm, 'isTeledeclarationMode' => $isTeledeclarationMode, 'etape_courante' => DRMClient::ETAPE_ADMINISTRATION)); ?>
    <?php include_partial('drm/controlMessage'); ?>
    <div id="application_drm">
        <form action="<?php echo url_for('drm_annexes', $annexesForm->getObject()); ?>" class="form-horizontal" method="post">
            <div class="row">
                <?php echo $annexesForm->renderGlobalErrors(); ?>
                <?php echo $annexesForm->renderHiddenFields(); ?>
                <div class="col-xs-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">Déclaration des documents d'accompagnement</h3>
                        </div>
                        <table id="table_drm_adminitration" class="table table-bordered table-striped">
                            <thead >
                                <tr>
                                    <th class="col-xs-4" >Type de document</th>
                                    <th class="col-xs-4">Numéro de début</th>
                                    <th class="col-xs-4">Numéro de fin</th>
                                </tr>
                            </thead>
                            <tbody class="drm_adminitration">
                                <?php foreach ($annexesForm->getDocTypes() as $typeDoc): ?>
                                    <tr>
                                        <td style="vertical-align: middle;" class="drm_annexes_type"><?php echo DRMClient::$drm_documents_daccompagnement[$typeDoc]; ?></td>
                                        <td class="drm_annexes_doc_debut"><?php echo $annexesForm[$typeDoc . '_debut']->render(); ?></td>
                                        <td class="drm_annexes_doc_fin"><?php echo $annexesForm[$typeDoc . '_fin']->render(); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">Relevé de non apurement</h3>
                        </div>
                        <table id="table_drm_non_apurement" class="table table-bordered table-striped">
                            <thead >
                                <tr>
                                    <th class="col-xs-4">Numéro de document</th>
                                    <th class="drm_non_apurement_date_emission col-xs-4">Date d'expédition</th>
                                    <th class="col-xs-4">Numéro d'accise</th>
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody class="drm_non_apurement" id="nonapurement_list">

                                <?php
                                foreach ($annexesForm['releve_non_apurement'] as $nonApurementForm) :
                                    include_partial('itemNonApurement', array('form' => $nonApurementForm));
                                endforeach;
                                ?>
                                <?php include_partial('templateNonApurementItem', array('form' => $annexesForm->getFormTemplate())); ?>
                            </tbody>

                        </table>
                    </div>
                    <a class="btn_ajouter_ligne_template btn btn-link" data-container="#nonapurement_list" data-template="#template_nonapurement" href="#"><span class="glyphicon glyphicon-plus-sign"></span> Ajouter une ligne non apurement</a>
                </div>
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">Compléments d'information</h3>
                        </div>
                        <div class="panel-body row">
                            <div class="col-sm-5">
                                <?php echo $annexesForm['quantite_sucre']->renderError(); ?>
                                <div class="form-group <?php if($annexesForm['quantite_sucre']->hasError()): ?>has-error<?php endif; ?>">
                                    <?php echo $annexesForm['quantite_sucre']->renderLabel("Quantité de sucre :", array('class' => 'col-sm-5 control-label')); ?>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                        <?php echo $annexesForm['quantite_sucre']->render(array('class' => 'form-control text-right', 'autocomplete' => 'off')); ?>
                                        <span class="input-group-addon">quintal</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <?php echo $annexesForm['observations']->renderError(); ?>
                                <div class="form-group <?php if($annexesForm['observations']->hasError()): ?>has-error<?php endif; ?>">
                                    <div class="col-sm-12">
                                    <?php echo $annexesForm['observations']->render(array('class' => 'form-control', 'placeholder' => 'Observations', 'rows' => "2")); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4 text-left">
                    <a tabindex="-1" href="<?php echo url_for('drm_crd', $drm); ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Etape précédente</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a class="btn btn-default" href="#drm_delete_popup">
                        <span>Supprimer la DRM</span>
                    </a> 
                </div>
                <div class="col-xs-4 text-right">
                    <button type="submit" class="btn btn-success">Étape suivante <span class="glyphicon glyphicon-chevron-right"></span></button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php
include_partial('drm/deleteDrmPopup', array('drm' => $drm, 'deleteForm' => $deleteForm));
?>

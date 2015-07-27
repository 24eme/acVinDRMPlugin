<?php use_helper('Float'); ?>
<?php $contratNonSolde = ((!is_null($form->getObject()->valide->statut)) && ($form->getObject()->valide->statut != VracClient::STATUS_CONTRAT_SOLDE)); ?>

<?php include_component('vrac', 'etapes', array('vrac' => $form->getObject(), 'compte' => $compte, 'actif' => 2, 'urlsoussigne' => null, 'isTeledeclarationMode' => $isTeledeclarationMode)); ?>

<form action="" method="post" class="form-horizontal" id="contrat_marche" >
    <?php echo $form->renderHiddenFields() ?>
    <?php echo $form->renderGlobalErrors() ?>
    <div class="row">
        <div class="col-sm-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Produit</h3>
                </div>
                <div class="panel-body">
                <?php if (in_array($form->getObject()->type_transaction, array(VracClient::TYPE_TRANSACTION_VIN_VRAC, VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE))): ?>
                	<?php if (isset($form['produit'])): ?><?php echo $form['produit']->renderError(); ?><?php endif; ?>
                    <?php if (isset($form['millesime'])): ?><?php echo $form['millesime']->renderError(); ?><?php endif; ?>
                    <div class="form-group">
                    	<?php if (isset($form['produit'])): ?>
                        <div class="col-xs-8 <?php if($form['produit']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['produit']->render(array('class' => 'form-control select2', 'placeholder' => 'Selectionner un produit', 'tabindex'=> '0')); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($form['millesime'])): ?>
                        <div class="col-xs-4 <?php if($form['millesime']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['millesime']->render(array('class' => 'form-control select2')); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($form['selection'])): ?>
                    <div class="form-group <?php if($form['selection']->hasError()): ?>has-error<?php endif; ?>">
                		<div class="col-sm-12">
							<?php echo $form['selection']->renderError(); ?>
							<div class="checkbox bloc_condition" data-condition-cible="#bloc_cepage">
								<label for="<?php echo $form['selection']->renderId(); ?>">
									<?php echo $form['selection']->render(); ?>
									Déclarer un cépage
								</label>
							</div>
						</div>
					</div>
					<?php endif; ?>
                    <?php if (isset($form['cepage'])): ?><?php echo $form['cepage']->renderError(); ?><?php endif; ?>
                    <div class="form-group" id="bloc_cepage" data-condition-value="1" >
                        <?php if (isset($form['cepage'])): ?>
                        <div class="col-xs-8 <?php if($form['cepage']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['cepage']->render(array('class' => 'form-control select2', 'placeholder' => 'Selectionner un cépage', 'tabindex'=> '0')); ?>
                        </div>
                        <?php endif; ?>
                    </div>
				<?php else: ?>
                	<?php if (isset($form['cepage'])): ?><?php echo $form['cepage']->renderError(); ?><?php endif; ?>
                    <?php if (isset($form['millesime'])): ?><?php echo $form['millesime']->renderError(); ?><?php endif; ?>
                    <div class="form-group">
                    	<?php if (isset($form['cepage'])): ?>
                        <div class="col-xs-8 <?php if($form['cepage']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['cepage']->render(array('class' => 'form-control select2', 'placeholder' => 'Selectionner un cépage', 'tabindex'=> '0')); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($form['millesime'])): ?>
                        <div class="col-xs-4 <?php if($form['millesime']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['millesime']->render(array('class' => 'form-control select2')); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($form['selection'])): ?>
                    <div class="form-group <?php if($form['selection']->hasError()): ?>has-error<?php endif; ?>">
                		<div class="col-sm-12">
							<?php echo $form['selection']->renderError(); ?>
							<div class="checkbox bloc_condition" data-condition-cible="#bloc_produit">
								<label for="<?php echo $form['selection']->renderId(); ?>">
									<?php echo $form['selection']->render(); ?>
									Revendiquer le cépage
								</label>
							</div>
						</div>
					</div>
					<?php endif; ?>
                    <?php if (isset($form['produit'])): ?><?php echo $form['produit']->renderError(); ?><?php endif; ?>
                    <div class="form-group" id="bloc_produit" data-condition-value="1" >
                        <?php if (isset($form['produit'])): ?>
                        <div class="col-xs-8 <?php if($form['produit']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['produit']->render(array('class' => 'form-control select2', 'placeholder' => 'Selectionner un produit', 'tabindex'=> '0')); ?>
                        </div>
                        <?php endif; ?>
                    </div>
				<?php endif; ?>
                    
                    
                    <?php if (isset($form['categorie_vin'])): ?><?php echo $form['categorie_vin']->renderError(); ?><?php endif; ?>
                    <?php if (isset($form['domaine'])): ?><?php echo $form['domaine']->renderError(); ?><?php endif; ?>
                    <?php if (isset($form['label'])): ?><?php echo $form['label']->renderError(); ?><?php endif; ?>
                    <div class="form-group">
                        <div class=" bloc_condition" data-condition-cible="#bloc_domaine_notworking">
                        	<?php if (isset($form['categorie_vin'])): ?>
                            <div class="col-xs-8 <?php if($form['categorie_vin']->hasError()): ?>has-error<?php endif; ?>">
                                
                                    <?php echo $form['categorie_vin']->render(); ?>
                                
                            </div>
                            <?php endif; ?>
                            <?php if (isset($form['domaine'])): ?>
                            <div id="bloc_domaine" data-condition-data="DOMAINE" class="bloc_conditionner col-xs-4 <?php if($form['domaine']->hasError()): ?>has-error<?php endif; ?>">
                                
                                    <?php echo $form['domaine']->render(array('class' => 'form-control select2', 'placeholder' => 'Déclarer un domaine')); ?>
                                
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (isset($form['label'])): ?>
                    <?php echo $form['label']->renderError(); ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 <?php if($form['label']->hasError()): ?>has-error<?php endif; ?>">
                            <div class="col-sm-8">
                            <?php echo $form['label']->render(); ?>
                            </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Informations</h3>
                    </div>
                    <div class="panel-body">
						
						<?php if(isset($form['volume_initial'])): ?>
                        <?php echo $form['volume_initial']->renderError(); ?>
                        <div class="form-group <?php if($form['volume_initial']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['volume_initial']->renderLabel("Volume initial :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['volume_initial']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon">&nbsp;<?php echo $configuration->getUnites()[$form->getObject()->type_transaction]['volume_initial']['libelle'] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
						
						<?php if(isset($form['volume_vigueur'])): ?>
                        <?php echo $form['volume_vigueur']->renderError(); ?>
                        <div class="form-group <?php if($form['volume_initial']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['volume_vigueur']->renderLabel("Volume en vigueur :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['volume_vigueur']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon">&nbsp;<?php echo $configuration->getUnites()[$form->getObject()->type_transaction]['volume_vigueur']['libelle'] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
						
						<?php if(isset($form['degre'])): ?>
                        <?php echo $form['degre']->renderError(); ?>
                        <div class="form-group <?php if($form['degre']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['degre']->renderLabel("Degré :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['degre']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon">&nbsp;°&nbsp;&nbsp;</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
						
						<?php if(isset($form['surface'])): ?>
                        <?php echo $form['surface']->renderError(); ?>
                        <div class="form-group <?php if($form['surface']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['surface']->renderLabel("Surface :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['surface']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon">&nbsp;<?php echo $configuration->getUnites()[$form->getObject()->type_transaction]['surface']['libelle'] ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Commande</h3>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($form['bouteilles_contenance_libelle'])): ?>
                        <script type="text/javascript">
						var contenances = new Array();
						<?php foreach (VracConfiguration::getInstance()->getContenances() as $l => $hl): ?>
						contenances["<?php echo $l ?>"] = <?php echo $hl ?>;
						<?php endforeach; ?>
						</script>
                        <?php echo $form['bouteilles_contenance_libelle']->renderError(); ?>
                        <div class="form-group <?php if($form['bouteilles_contenance_libelle']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['bouteilles_contenance_libelle']->renderLabel("Contenance :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-8">
                                <?php echo $form['bouteilles_contenance_libelle']->render(array('class' => 'form-control')); ?>
                            </div>
                        </div>
                        <?php endif; ?>
						
						<?php if(isset($form['jus_quantite'])): ?>
                        <?php echo $form['jus_quantite']->renderError(); ?>
                        <div class="form-group <?php if($form['jus_quantite']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['jus_quantite']->renderLabel("Volume proposé :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['jus_quantite']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon">&nbsp;<?php echo $configuration->getUnites()[$form->getObject()->type_transaction]['jus_quantite']['libelle'] ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <?php if(isset($form['bouteilles_contenance_libelle'])): ?>
                                <p class="control-label" id="correspondance_bouteille"></p>
                                <?php endif; ?>
                           </div>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($form['raisin_quantite'])): ?>
                        <?php echo $form['raisin_quantite']->renderError(); ?>
                        <div class="form-group <?php if($form['raisin_quantite']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['raisin_quantite']->renderLabel("Quantité :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['raisin_quantite']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon"><?php echo $configuration->getUnites()[$form->getObject()->type_transaction]['raisin_quantite']['libelle'] ?>&nbsp;&nbsp;&nbsp;</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
            			<?php if(isset($form['prix_initial_unitaire'])): ?>
                        <?php echo $form['prix_initial_unitaire']->renderError(); ?>
                        <div class="form-group <?php if($form['prix_initial_unitaire']->hasError()): ?>has-error<?php endif; ?>">
                            <?php echo $form['prix_initial_unitaire']->renderLabel("Prix :", array('class' => 'col-sm-4 control-label')); ?>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?php echo $form['prix_initial_unitaire']->render(array("class" => "form-control text-right", 'autocomplete' => 'off')); ?>
                                    <span class="input-group-addon"><?php echo $configuration->getUnites()[$form->getObject()->type_transaction]['prix_initial_unitaire']['libelle'] ?></span>
                                </div>
                            </div>
                        </div>
           				<?php endif; ?>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4 text-left">
            <a tabindex="-1" href="<?php echo url_for('vrac_soussigne', $vrac); ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Etape précédente</a>
        </div>
        <div class="col-xs-4 text-center">
            <?php if ($isTeledeclarationMode && $vrac->isBrouillon()) : ?>
                <a class="btn btn-default" href="<?php echo url_for('vrac_supprimer_brouillon', $vrac); ?>">Supprimer le brouillon</a>
            <?php endif; ?>  
        </div>
        <div class="col-xs-4 text-right">
            <button type="submit" class="btn btn-success">Étape suivante <span class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
    </div>
</form>

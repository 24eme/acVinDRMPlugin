<?php foreach ($certificationsProduits as $certificationHash => $certificationProduits): ?>
    <?php $certifKey = $certificationProduits->certification_libelle; ?>
        <div class="col-xs-12">
            <h3><?php echo $certificationProduits->certification_libelle; ?></h3>
            <table id="table_drm_choix_produit" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="col-xs-6 text-left">Produits
                        </th>
                        <th class="col-xs-3 text-center">Déclarer des mouvements en droit suspendu</th>
                        <th class="col-xs-3 text-center">Déclarer des mouvements en droit acquitté</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($certificationProduits->produits)): ?>
                        <?php foreach ($certificationProduits->produits as $produit):
                            ?>
                            <tr>
                                <td class="text-left"><?php echo $produit->getLibelle("%format_libelle%"); ?></td>
                                <td class="text-center"><?php echo $form['produit' . $produit->getHashForKey()]->render(); ?></td>
                                <td class="text-center"><?php echo $form['acquitte' . $produit->getHashForKey()]->render(); ?></td>
                            </tr>
                            <?php ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="text-center" colspan="3"><em>Vous n'avez pas de produit déclaré en catégorie <?php echo $certificationProduits->certification_libelle; ?></em></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
<?php endforeach; ?>

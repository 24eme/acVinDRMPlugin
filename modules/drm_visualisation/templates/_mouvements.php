<?php use_helper('Float'); ?>
<?php use_helper('Date'); ?>
<?php use_helper('Mouvement') ?>

<?php if(count($mouvements) > 0): ?>
<?php if(isset($hamza_style)) : ?>
    <?php include_partial('global/hamzaStyle', array('table_selector' => '#table_mouvements', 
                                                     'mots' => mouvement_get_words($mouvements),
                                                     'consigne' => "Saisissez un produit, un type de mouvement, un numéro de contrat, un pays d'export, etc. :")) ?>
<?php endif; ?>

<h2>RECAPITULATIF DES MOUVEMENTS</h2>
<table id="table_mouvements" class="table_recap">
    <thead>
        <tr>
            <th style="width: 170px;">Date de modification</th>
            <th style="width: 280px;">Produits</th>
            <th>Type</th>
            <th>Volume</th>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php foreach($mouvements as $mouvement): ?>
    <?php $i++; ?>
        <tr id="<?php echo mouvement_get_id($mouvement) ?>" class="<?php if($i%2!=0) echo "alt"; if (!$isTeledeclarationMode && $mouvement->facturable) {echo " facturable";}  ?>">
            <td>
                Saisi le <?php echo format_date($mouvement->date_version, 'D') ?>
            </td>
            </td>
            <td><?php echo $mouvement->produit_libelle ?> </td>
            <td><?php
   if ($mouvement->vrac_numero) {
     echo (!isset($no_link) || !$no_link)? '<a href="'.url_for("vrac_visualisation", array("numero_contrat" => $mouvement->vrac_numero)).'">' : '';
     echo $mouvement->type_libelle.' '.$mouvement->numero_archive;
     echo (!isset($no_link) || !$no_link)? '</a>' : '';
   }else{
     echo $mouvement->type_libelle.' '.$mouvement->detail_libelle;
   }
?></td>
            <td <?php echo ($mouvement->volume > 0)? ' class="positif"' : 'class="negatif"';?> >
                <?php  echoSignedFloat($mouvement->volume); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<h2>AUCUN MOUVEMENTS</h2>
<?php endif; ?>
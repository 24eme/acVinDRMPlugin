<?php use_helper('Version'); ?>
<?php use_helper('Float'); ?>
<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th class="col-xs-1">Type</th>
			<th class="col-xs-3">Produits</th>
			<th class="col-xs-1 text-right">Stock initial</th>
			<th class="col-xs-1">(revendiqué)</th>
			<th class="col-xs-1 text-right">Entrées</th>
			<th class="col-xs-1">&nbsp;(Rev.)</th>
			<th class="col-xs-1 text-right">Sorties</th>
			<th class="col-xs-1"><?php if(!$isTeledeclarationMode): ?>&nbsp;(Rev.)<?php endif; ?></th>
			<th class="col-xs-1 text-right"><strong>Stock final</th>
			<th class="col-xs-1">(revendiqué)</strong></th>
		</tr>
	</thead>
	<tbody>
		<?php $details = $drm->getProduitsDetails($isTeledeclarationMode, DRM::DETAILS_KEY_SUSPENDU); ?>
		<?php foreach($details as $detail): ?>
			<tr>
				<td><?php echo $detail->getTypeDRMLibelle() ?></td>
				<td><a href="#tab=mouvements&filtre=<?php echo strtolower($detail->getLibelle(ESC_RAW)); ?>"><?php echo $detail->getLibelle(ESC_RAW) ?></a></td>
                <td class="text-right <?php echo isVersionnerCssClass($detail, 'total_debut_mois') ?>"><strong><?php echoFloat($detail->total_debut_mois) ?></strong>&nbsp;<span class="unite">hl</span>&nbsp;</td><td class="<?php echo isVersionnerCssClass($detail, 'total_debut_mois') ?>">(<?php echoFloat($detail->stocks_debut->dont_revendique) ?>&nbsp;<span class="unite">hl</span>)</td>
				<td class="text-right <?php echo isVersionnerCssClass($detail, 'total_entrees') ?>"><?php echoFloat($detail->total_entrees) ?>&nbsp;<span class="unite">hl</span></td>
                                <td class="<?php echo isVersionnerCssClass($detail, 'total_entrees_revendique') ?>">&nbsp;(<?php echoFloat($detail->total_entrees_revendique) ?>&nbsp;<span class="unite">hl</span>)</td>
				<td class="text-right <?php echo isVersionnerCssClass($detail, 'total_sorties') ?>"><?php echoFloat($detail->total_sorties) ?>&nbsp;<span class="unite">hl</span></td>
                                <td><?php if(!$isTeledeclarationMode): ?>&nbsp;(<?php echoFloat($detail->total_sorties_revendique) ?>&nbsp;<span class="unite">hl</span>)<?php endif; ?></td>
				<td class="text-right <?php echo isVersionnerCssClass($detail, 'total') ?>"><strong><?php echoFloat($detail->total) ?></strong>&nbsp;<span class="unite">hl</span>&nbsp;</td><td class="<?php echo isVersionnerCssClass($detail, 'total') ?>">(<?php echoFloat($detail->stocks_fin->dont_revendique) ?>&nbsp;<span class="unite">hl</span>)</td>
			</tr>
		<?php endforeach; ?>
		<?php $details = $drm->getProduitsDetails($isTeledeclarationMode, DRM::DETAILS_KEY_ACQUITTE); ?>
		<?php foreach($details as $detail): ?>
			<tr>
				<td><?php echo $detail->getTypeDRMLibelle() ?></td>
				<td><a href="#tab=mouvements&filtre=<?php echo strtolower($detail->getLibelle(ESC_RAW)); ?>"><?php echo $detail->getLibelle(ESC_RAW) ?></a></td>
                <td class="text-right <?php echo isVersionnerCssClass($detail, 'total_debut_mois') ?>"><strong><?php echoFloat($detail->total_debut_mois) ?></strong>&nbsp;<span class="unite">hl</span>&nbsp;</td><td class="<?php echo isVersionnerCssClass($detail, 'total_debut_mois') ?>">(<?php echoFloat($detail->stocks_debut->dont_revendique) ?>&nbsp;<span class="unite">hl</span>)</td>
				<td class="text-right <?php echo isVersionnerCssClass($detail, 'total_entrees') ?>"><?php echoFloat($detail->total_entrees) ?>&nbsp;<span class="unite">hl</span></td>
                                <td class="<?php echo isVersionnerCssClass($detail, 'total_entrees_revendique') ?>">&nbsp;(<?php echoFloat($detail->total_entrees_revendique) ?>&nbsp;<span class="unite">hl</span>)</td>
				<td class="text-right <?php echo isVersionnerCssClass($detail, 'total_sorties') ?>"><?php echoFloat($detail->total_sorties) ?>&nbsp;<span class="unite">hl</span></td>
                                <td><?php if(!$isTeledeclarationMode): ?>&nbsp;(<?php echoFloat($detail->total_sorties_revendique) ?>&nbsp;<span class="unite">hl</span>)<?php endif; ?></td>
				<td class="text-right <?php echo isVersionnerCssClass($detail, 'total') ?>"><strong><?php echoFloat($detail->total) ?></strong>&nbsp;<span class="unite">hl</span>&nbsp;</td><td class="<?php echo isVersionnerCssClass($detail, 'total') ?>">(<?php echoFloat($detail->stocks_fin->dont_revendique) ?>&nbsp;<span class="unite">hl</span>)</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

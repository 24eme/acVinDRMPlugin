<tr>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getCertification(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getGenre(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getAppellation(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getMention(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getLieu(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getCouleur(), 'cvo' => $cvo)) ?>
	</td>
	<td>
		<?php include_partial('itemNoeud', array('noeud' => $produit->getCepage(), 'cvo' => $cvo)) ?>
	</td>
	<td class="center">
		<strong><?php echo (!is_null($cvo)) ? $cvo->taux : null ?></strong>
	</td>
	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo sprintf("%04d", $produit->getCodeProduit()) ?>
		</a>
	</td>

	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo $produit->getCodeDouane() ?>
		</a>
	</td>
	<td class="center">
		<a href="<?php echo url_for('produit_modification', array('noeud' => $produit->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
			<?php echo $produit->getCodeComptable() ?>
		</a>
	</td>
</tr>
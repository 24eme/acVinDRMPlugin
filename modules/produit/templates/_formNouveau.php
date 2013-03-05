<form class="popup_form form_delay" id="form_ajout" action="<?php echo url_for('produit_nouveau') ?>" method="post">
	<?php echo $form->renderGlobalErrors() ?>
	<?php echo $form->renderHiddenFields() ?>
	<div class="ligne_form">
		<span class="error"><?php echo $form['certifications']->renderError() ?></span>
		<?php echo $form['certifications']->renderLabel() ?>
		<?php echo $form['certifications']->render() ?>
	</div>
	<div class="ligne_form">
		<span class="error"><?php echo $form['genres']->renderError() ?></span>
		<?php echo $form['genres']->renderLabel() ?>
		<?php echo $form['genres']->render() ?>
	</div>
	<div class="ligne_form">
		<span class="error"><?php echo $form['appellations']->renderError() ?></span>
		<?php echo $form['appellations']->renderLabel() ?>
		<?php echo $form['appellations']->render(array('class' => 'permissif')) ?>
	</div>
	<div class="ligne_form">
		<span class="error"><?php echo $form['mentions']->renderError() ?></span>
		<?php echo $form['mentions']->renderLabel() ?>
		<?php echo $form['mentions']->render() ?>
	</div>
	<div class="ligne_form">
		<span class="error"><?php echo $form['lieux']->renderError() ?></span>
		<?php echo $form['lieux']->renderLabel() ?>
		<?php echo $form['lieux']->render(array('class' => 'permissif')) ?>
	</div>
	<div class="ligne_form">
		<span class="error"><?php echo $form['couleurs']->renderError() ?></span>
		<?php echo $form['couleurs']->renderLabel() ?>
		<?php echo $form['couleurs']->render() ?>
	</div>
	<div class="ligne_form">
		<span class="error"><?php echo $form['cepages']->renderError() ?></span>
		<?php echo $form['cepages']->renderLabel() ?>
		<?php echo $form['cepages']->render(array('class' => 'permissif')) ?>
	</div>
	<div class="form_ligne_btn">
		<a class="btn_majeur btn_annuler" href="<?php echo url_for('produits') ?>">Annuler</a>
		<button name="valider" class="btn_majeur btn_valider" type="submit">Valider</button>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function () {
	$( "#<?php echo $form['appellations']->renderId() ?>" ).combobox();
	$( "#<?php echo $form['lieux']->renderId() ?>" ).combobox();
	$( "#<?php echo $form['cepages']->renderId() ?>" ).combobox();
});
</script>
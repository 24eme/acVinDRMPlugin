<form method="post" action="<?php echo url_for('drm_cooperative_details', $detail) ?>">
<div id="drm_cooperative_details_form">
    <?php
    echo $form->renderHiddenFields();
    echo $form->renderGlobalErrors();
    ?>
    <table id="drm_cooperative_details_table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Numéro contrat</th>
                <th>Nom de la coopérative</th>
                <th>Volumes</th>
                <th>Dates</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="drm_cooperative_details_tableBody">
        <?php
        foreach ($form as $itemForm){
            if($itemForm instanceof sfFormFieldSchema) {
                include_partial('item',array('form' => $itemForm,'detail' => $detail));
            } else {
                $itemForm->renderRow();
            }
        }
        ?>
        </tbody>
    </table>
</div>
<input type="submit" value="Valider" />
<a href="<?php echo url_for('drm_edition', $drm); ?>" id="drm_cooperative_details_annuler" class="btn_majeur btn_rouge">Annuler</a>
<a href="#" id="drm_cooperative_details_addTemplate" class="btn_majeur btn_orange">Ajouter</a>
</form>

<script type="text/javascript">
    $(document).ready( function()
    {
            $('#drm_cooperative_details_addTemplate').click(function()
            {
                $($('#template_cooperative').html().replace(/var---nbItem---/g, UUID.generate())).appendTo($('#drm_cooperative_details_tableBody'));
                $('.autocomplete').combobox();
                
            });
            
            $('.drm_cooperative_details_remove').live('click',function()
            {
                $(this).parent().parent().remove();
            });
            
            
     });
        
</script>

<?php include_partial('templateItem', array('form' => $form->getFormTemplate(), 'detail' => $detail)); ?>

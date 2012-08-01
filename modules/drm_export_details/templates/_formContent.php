<?php    
    echo $form->renderHiddenFields();
    echo $form->renderGlobalErrors();
?>
<table id="drm_export_details_table">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Destination</th>
            <th>Volumes</th>
            <th>Dates</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="drm_export_details_tableBody" class="drm_details_tableBody">
    <?php
    foreach ($form as $itemForm){
        if($itemForm instanceof sfFormFieldSchema) {
            include_partial('item',array('form' => $itemForm,'detail' => $detail));
        } else {
            $itemForm->renderRow();
        }
    }
    ?>
        <tr id="drm_details_lastRow">
            <td class="export_detail_produit"></td>
            <td class="export_detail_destination">
                <a href="#" id="drm_export_details_addTemplate" class="drm_details_addTemplate">Ajouter un export</a>
            </td>
            <td class="export_detail_volume">
                <div id="drm_details_export_volume_total">
                    <strong>
                        <span class="drm_details_volume_somme">&Sigma;</span>
                        <span class="drm_details_volume_total"><?php echo $detail->sorties->export > 0 ? $detail->sorties->export : "0.00" ?></span>
                        <span class="drm_details_volume_unite unite">hl</span>
                    </strong>
                </div>      
            </td>
            <td class="export_detail_date_enlevement"></td>   
            <td class="export_detail_remove"></td>
        </tr>
    </tbody>
</table>
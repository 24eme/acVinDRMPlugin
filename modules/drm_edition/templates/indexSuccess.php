<?php //include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu" style="background: #fff; padding: 0 10px;">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php /*include_partial('etapes', array('drm' => $drm, 
                                                   'etape' => 'mouvements', 
                                                   'pourcentage' => '10'));*/ ?>
    <?php include_partial('drm/controlMessage'); ?>

    
    <!-- #principal -->
    <section id="principal" style="width: auto;">
    	<a href="" data-popup="#raccourci_clavier" class="btn_popup" data-popup-config="configDefaut">Raccourcis clavier</a>
    		<?php include_partial('shortcutKeys') ?>
        <div id="application_dr">
        	<?php include_component('drm_edition', 'produitForm', array('drm' => $drm,
        															  'config' => $config)) ?>
            
            <div id="contenu_onglet">

                <?php include_partial('drm_edition/list', array('drm_noeud' => $drm->declaration, 
                                                                   'config' => $config,
   'detail' => $detail,
                                                                   'produits' => $produits,
                                                                   'form' => $form,
                												   'detail' => $detail)); ?>

            </div>
            <div id="btn_etape_dr">
            	
            </div>
            
            <a href="<?php echo url_for('drm_pdf_facture', $drm); ?>" id="facture">Facture</a>   
            <a href="<?php echo url_for('drm_validation', $drm); ?>" id="facture">Suite</a> 
        </div>
    </section>
</section>



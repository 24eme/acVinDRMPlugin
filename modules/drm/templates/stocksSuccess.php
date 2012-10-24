<div id="contenu" class="drm">
    
    <!-- #principal -->
    <section id="principal">
        <p id="fil_ariane"><a href="<?php echo url_for('drm') ?>">Page d'accueil</a> &gt; <strong><?php echo $etablissement->nom ?></strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <?php include_component('drm', 'chooseEtablissement', array('identifiant' => $etablissement->identifiant)); ?>
            
            <fieldset id="historique_drm">
                <legend>Historique des DRMs de l'opérateur</legend>
                <nav>
                    <ul>
                        <li><a href="<?php echo url_for('drm_etablissement', $etablissement) ?>">Vue calendaire</a></li>
                        <li class="actif"><span>Vue stock</span></li>
                    </ul>
                </nav>
                <?php include_component('drm', 'stocks', array('etablissement' => $etablissement)) ?>
            </fieldset>

            <?php //include_partial('drm/calendrier', array('calendrier' => $calendrier)); ?>
        </section>
        <!-- fin #contenu_etape -->
        
    </section>
    <!-- fin #principal -->
    
    <!-- #colonne -->
    <aside id="colonne">
        
        <div class="bloc_col" id="contrat_aide">
            <h2>Aide</h2>
            
            <div class="contenu">
                
            </div>
        </div>
        
        <div class="bloc_col" id="infos_contact">
            <h2>Infos contact</h2>
            
            <div class="contenu">
                
            </div>
        </div>
    
    </aside>
    <!-- fin #colonne -->
</div>
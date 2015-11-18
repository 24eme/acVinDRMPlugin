    <ul class="breadcrumb breadcrumb-etape">
        <?php $cpt_etape = 1; ?>
        <?php if (isset($isTeledeclarationMode) && $isTeledeclarationMode) : ?> 
            <?php $actif = ($etape_courante == DRMClient::ETAPE_CHOIX_PRODUITS); ?>
            <?php $past = ((!$actif) && (array_search($drm->etape, DRMClient::$drm_etapes) >= array_search(DRMClient::ETAPE_CHOIX_PRODUITS, DRMClient::$drm_etapes))); ?>
            <li class="<?php if($actif): ?>active<?php endif; ?> <?php if (!$past && !$actif): ?>disabled<?php endif; ?> <?php if ($past && !$actif): ?>visited<?php endif; ?>">
                <a  href="<?php echo url_for('drm_choix_produit', $drm); ?>">
                    <span>Produits</span>
                    <small>Etape <?php echo $cpt_etape++; ?></small>
                </a>
            </li>
        <?php endif; ?>
        <?php $actif = ($etape_courante == DRMClient::ETAPE_SAISIE); ?>
        <?php $past = ((!$actif) && (array_search($drm->etape, DRMClient::$drm_etapes) >= array_search(DRMClient::ETAPE_SAISIE, DRMClient::$drm_etapes))); ?>
        <li class="<?php if($actif): ?>active<?php endif; ?> <?php if (!$past && !$actif): ?>disabled<?php endif; ?> <?php if ($past && !$actif): ?>visited<?php endif; ?>">
            <a href="<?php echo url_for('drm_edition', $drm); ?>">
                <span>Mouvements</span>
                <small>Etape <?php echo $cpt_etape++; ?></small>
            </a>
        </li>
        <?php if (isset($isTeledeclarationMode) && $isTeledeclarationMode) : ?> 
            <?php $actif = ($etape_courante == DRMClient::ETAPE_CRD); ?>
            <?php $past = ((!$actif) && (array_search($drm->etape, DRMClient::$drm_etapes) >= array_search(DRMClient::ETAPE_CRD, DRMClient::$drm_etapes))); ?>
            <li class="<?php if($actif): ?>active<?php endif; ?> <?php if (!$past && !$actif): ?>disabled<?php endif; ?> <?php if ($past && !$actif): ?>visited<?php endif; ?>"> 
               <a href="<?php echo url_for('drm_crd', $drm); ?>">
                    <span>CRD</span>
                    <small>Etape <?php echo $cpt_etape++; ?></small>
                </a>
            </li>
        <?php endif; ?>
        <?php if (isset($isTeledeclarationMode) && $isTeledeclarationMode) : ?> 
            <?php $actif = ($etape_courante == DRMClient::ETAPE_ADMINISTRATION); ?>
            <?php $past = ((!$actif) && (array_search($drm->etape, DRMClient::$drm_etapes) >= array_search(DRMClient::ETAPE_ADMINISTRATION, DRMClient::$drm_etapes))); ?>
            <li class="<?php if($actif): ?>active<?php endif; ?> <?php if (!$past && !$actif): ?>disabled<?php endif; ?> <?php if ($past && !$actif): ?>visited<?php endif; ?>"> 
                <a href="<?php echo url_for('drm_annexes', $drm); ?>">
                    <span>Annexes</span>
                    <small>Etape <?php echo $cpt_etape++; ?></small>
                </a>
            </li>
        <?php endif; ?>
        <?php $actif = ($etape_courante == DRMClient::ETAPE_VALIDATION); ?>
        <?php $past = ((!$actif) && (array_search($drm->etape, DRMClient::$drm_etapes) >= array_search(DRMClient::ETAPE_VALIDATION, DRMClient::$drm_etapes))); ?>
        <li class="<?php if($actif): ?>active<?php endif; ?> <?php if (!$past && !$actif): ?>disabled<?php endif; ?> <?php if ($past && !$actif): ?>visited<?php endif; ?>">
            <a href="<?php echo url_for('drm_validation', $drm); ?>">
               <span>Validation</span>
               <small>Etape <?php echo $cpt_etape++; ?></small>
            </a>
        </li>
    </ul>

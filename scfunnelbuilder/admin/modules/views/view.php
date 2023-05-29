<?php
/**
 * View create funnel 
 * 
 * @package
 */
    // $is_pro_active = false;
    dd('yes');
?>
<div class="scfunnel">
    <div id="templates-library"></div>
    <div class="scfunnel-dashboard">
        <nav class="scfunnel-dashboard__nav">
            <?php require_once SCFUNNEL_DIR . '/admin/partials/dashboard-nav.php'; ?>
        </nav>

        <div class="dashboard-nav__content">
            <div class="scfunnel-dashboard__header create-funnel__header">
                <div class="title">
                    <h1><?php echo __('Create A Funnel', 'scfunnel'); ?></h1>
                    <span class="subtitle"><?php echo __('Choose a template to start funnel', 'scfunnel'); ?></span>
                </div>

                <a href="#" class="btn-default">
                    <?php
                        require SCFUNNEL_DIR . '/admin/partials/icons/play-icon.php';
                        echo __('How to use this funnel', 'scfunnel');
                    ?>
                </a>
            </div>
            <!-- /create-funnel__header -->

            <div id="templates-library">

            </div>
            <!-- /scfunnel-create-funnel__inner-content -->

        </div>
    </div>
</div>
<!-- /.scfunnel -->

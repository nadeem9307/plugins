<?php
/**
 * View builder not active
 * 
 * @package
 */
//$is_pro_active = false;
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

            <div class="scfunnel-create-funnel__inner-content">
                <div class="scfunnel-create-funnel__template-wrapper">

                    <div class="create-funnel__single-template create-funnel__from-scratch">
                        <p>Please activate <strong><?php echo ucfirst($this->builder); ?></strong> to see the templates. </p>
                    </div>

                    <div class="create-funnel__single-template create-funnel__from-scratch">
                        <a href="#" id="scfunnel-create-funnel" class="btn-default">
                            <?php echo __('Start From scratch', 'scfunnel'); ?>
                        </a>
                        <span class="helper-txt"><?php echo __('User step by step to create you own funnel', 'scfunnel'); ?></span>
                    </div>
                    <!-- /create-funnel__single-template -->
                </div>
                <!-- /scfunnel-create-funnel__template-wrapper -->

            </div>
            <!-- /scfunnel-create-funnel__inner-content -->

        </div>
    </div>

</div>
<!-- /.scfunnel -->

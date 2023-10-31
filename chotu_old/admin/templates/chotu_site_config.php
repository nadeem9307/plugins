<h1>Site Config Page</h1>
   <?php settings_errors(); ?> 
<form method="post" action="options.php" enctype="multipart/form-data">
    <div class="site_config_page">
    <?php do_settings_sections('theme_options_page')?>
    <?php settings_fields("chotu_theme_options");?>
    <!-- <?php settings_fields("chotu_theme_defaults");?> -->
    <?php submit_button();?>
    </div>
</form>

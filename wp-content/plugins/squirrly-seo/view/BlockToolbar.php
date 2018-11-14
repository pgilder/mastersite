<?php if (current_user_can('manage_options')) {?>
<div>
    <h2 class="nav-tab-wrapper sq_toolbar">
        <a href="?page=sq_seo#seo" id="seo_tab" class="nav-tab nav-tab-active"><?php echo __('SEO Settings', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_seo#jsonld" id="jsonld_tab" class="nav-tab"><?php echo __('Structured Data', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_seo#socials" id="socials_tab" class="nav-tab"><?php echo __('Social Media', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_seo#siteicon" id="siteicon_tab" class="nav-tab"><?php echo __('Site Icon', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_seo#tracking" id="tracking_tab" class="nav-tab"><?php echo __('Tracking Tools', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_seo#connections" id="connections_tab" class="nav-tab"><?php echo __('Connections', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_seo#sitemap" id="sitemap_tab" class="nav-tab"><?php echo __('Sitemap XML', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_settings#ranking" id="ranking_tab" class="nav-tab"><?php echo __('Ranking Options', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_settings#advanced" id="advanced_tab" class="nav-tab"><?php echo __('Advanced', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_settings#robots" id="robots_tab" class="nav-tab"><?php echo __('Robots.txt', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_patterns#patterns" id="patterns_tab" class="nav-tab"><?php echo __('Patterns', _SQ_PLUGIN_NAME_) ?></a>
        <a href="?page=sq_import#import" id="import_tab" class="nav-tab"><?php echo __('Import', _SQ_PLUGIN_NAME_) ?></a>
    </h2>
</div>
<?php } ?>
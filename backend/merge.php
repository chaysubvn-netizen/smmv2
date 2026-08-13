<?php
$top1 = file_get_contents('d:/top1smm.vn/app/Http/Controllers/Admin/ChildPanelController.php');
// Replace namespace
$top1 = str_replace('namespace App\Http\Controllers\Admin;', 'namespace App\Http\Controllers\Admin\Website;', $top1);
// Replace Class name
$top1 = str_replace('class ChildPanelController extends Controller', 'class ChildpanelAdminController extends Controller', $top1);

// Replace Models
$top1 = str_replace('use App\Models\Site;', 'use App\Models\Config;', $top1);
$top1 = str_replace('Site::getCurrentSite()', 'Config::where(\'domain\', request()->getHost())->first()', $top1);
$top1 = preg_replace('/Site::find\((.*?)\)/', 'Config::where(\'domain\', $1)->first()', $top1); // SMMV2 config find

// Replace main_site logic
$top1 = str_replace('->main_site', '->id === 1', $top1); // SMMV2 might not have main_site, assume id 1 is main

// Replace hierarchy
$top1 = preg_replace('/\$viewingSiteId = \$site->id;.*\$allRelevantSiteIds = collect\(\[\$viewingSiteId\]\)->merge\(\$allDescendantSiteIds\);/s', '$viewingSiteId = $site->domain;', $top1);
$top1 = str_replace('child_panels.site_id', 'child_panels.main_domain', $top1);
$top1 = preg_replace('/\$statsBase\(\)->where\(\'site_id\', \$viewingSiteId\)/', '\$statsBase()->where(\'main_domain\', \$viewingSiteId)', $top1);
$top1 = preg_replace('/\$statsBase\(\)->whereIn\(\'site_id\', \$allDescendantSiteIds\)/', '\$statsBase()->where(\'main_domain\', \'!=\', \$viewingSiteId)', $top1);
$top1 = str_replace('whereIn(\'child_panels.site_id\', $allRelevantSiteIds)', 'where(\'child_panels.main_domain\', $viewingSiteId)', $top1);
$top1 = str_replace('whereIn(\'site_id\', $allRelevantSiteIds)', 'where(\'main_domain\', $viewingSiteId)', $top1);


// created_site_id -> main_domain
$top1 = str_replace('$panel->created_site_id', '$panel->main_domain', $top1);

// view paths
$top1 = str_replace('\'admin.child-panel.index\'', '\'admin.website.childpanel.index\'', $top1);

// Add missing use clauses
$top1 = str_replace('use App\Models\ChildPanel;', "use App\Models\ChildPanel;\nuse App\Library\CloudflareCustom;\nuse App\Library\CpanelCustom;", $top1);

file_put_contents('d:/smmv2/backend/app/Http/Controllers/Admin/Website/ChildpanelAdminController.php', $top1);
echo "Merged successfully.";

<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * This is the API for handling exif data.
 */
class fillpathcache_Core {

  static function stats() {
    $missing_pathcaches = db::build()
      ->select("id")
      ->from("items")
      ->where("relative_path_cache", "IS", null)
      ->where("id", "<>", 1)
      ->limit(10000)
      ->execute()
      ->count();
    
    //Das root-Album wird nicht mitgezählt
    //$missing_pathcaches = $missing_pathcaches -1;
    $total_items = ORM::factory("item")->count_all();
    if (!$total_items) {
      return array(0, 0, 0);
    }
    return array($missing_pathcaches, $total_items,
                 round(100 * (($total_items - $missing_pathcaches) / $total_items)));
  }

  static function check_index() {
    list ($remaining) = fillpathcache::stats();
    if ($remaining) {
      site_status::warning(
        t('Some PathCaches are empty and need to be regenerated.  <a href="%url" class="g-dialog-link">Fix this now</a>',
          array("remaining" => $remaining, "url" => html::mark_clean(url::site("admin/maintenance/start/fillpathcache_task::update_index?csrf=__CSRF__")))),
        "path_caches_empty");
    }
  }
}

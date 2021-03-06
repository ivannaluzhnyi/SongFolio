<?php

declare(strict_types=1);

namespace Songfolio\Models;

use Songfolio\Core\BaseSQL;
use Songfolio\Core\Routing;

class Slug
{
    /**
     * check slug function
     *
     * @param string $slug
     * @return boolean
     */
    public static function checkIfExist(string $slug): bool
    {

        if (Routing::isSlugExist($slug)) return true;

        $base = new BaseSQL();
        $tables = ['Albums', 'Contents', 'Events', 'Categories', 'Songs'];

        foreach ($tables as $tab) {
            $ch = $base->getCustomSlug('SELECT 1 FROM ' . $tab, ['slug' => $slug]);
            if (is_array($ch)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function rewrite(string $slug): string
    {
        $array = require 'app/config/rewriteSlug.php';
        $rewriteSlug = trim($slug);
        $rewriteSlug = str_replace([' ', '\''], '_', $rewriteSlug);
        $rewriteSlug = strtolower($rewriteSlug);
        $rewriteSlug = strtr($rewriteSlug, $array );
        return $rewriteSlug;
    }
}

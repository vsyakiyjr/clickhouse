<?php

namespace App\Http\ViewComposers;

use App\Models\Cms\Page;
use Illuminate\View\View;

class LatestCmsPagesComposer
{
    public function compose(View $view)
    {
        $alias = explode('/', request()->route('slug'));
        if (isset($alias[1])) {
            $alias = $alias[1];
        } else {
            $alias = $alias[0];
        }
        $pages = Page::where('parent_directory', '/blog')->where('enabled', 1)->where('alias', '!=', $alias)->latest()->limit(3)->get();
        return $view->with('latestPages', $pages);
    }
}

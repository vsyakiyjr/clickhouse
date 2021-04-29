<?php

use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subrategories=[
            '7' => '/ru/ru/catalog/categories/departments/eating/20538/',
            '14' => '/ru/ru/catalog/categories/departments/bedroom/24828/',
            '60' => '/ru/ru/catalog/categories/departments/living_room/16239/',
            '38' => '/ru/ru/catalog/categories/departments/bedroom/10651/',
            '40' => '/ru/ru/catalog/categories/departments/kitchen/22957/',
            '43' => '/ru/ru/catalog/categories/departments/cooking/20633/',
            '90' => '/ru/ru/catalog/categories/departments/dining/19145/',
            '94' => '/ru/ru/catalog/categories/departments/kitchen/20819/',
            '118' => '/ru/ru/catalog/categories/departments/childrens_ikea/18690/',
            '119' => '/ru/ru/catalog/categories/departments/bathroom/20520/',
            '132' => '/ru/ru/catalog/categories/departments/childrens_ikea/18734/',
            '139' => '/ru/ru/catalog/categories/departments/childrens_ikea/24711/',
            '149' => '/ru/ru/catalog/categories/departments/outdoor/21964/',
            '157' => '/ru/ru/catalog/categories/seasonal/winter_holidays/holiday_decoration/',
            '169' => '/ru/ru/catalog/categories/departments/ikea_family_products/16248/',
            '186' => '/ru/ru/catalog/categories/departments/home_improvement/16292/',
            '189' => '/ru/ru/catalog/categories/departments/lighting/18750/',
            '199' => '/ru/ru/catalog/categories/departments/decoration/10768/',
            '216' => '/ru/ru/catalog/categories/departments/Textiles/10651/',
        ];
        foreach ($subrategories as $key => $item) {
            $subcat = Subcategory::query()->find($key);
            if ($subcat) {
                $subcat->link = $item;
                $subcat->timestamps = false;
                $subcat->save();
            }
        }
    }
}

<?php
namespace ZentoSupport\M2Data\Seeders;

use Illuminate\Support\Facades\DB;
use Zento\Catalog\Model\ORM\Category;
use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeInSet;

class CategorySeeder extends \Illuminate\Database\Seeder {
    use TraitEavHelper;

    protected $m2AttrsMappingToZentoTableFields = [
        'default_sort_by' => 'sort_by',
        'is_active' => 'active',
        'name' => 'name',
        'description' => 'description',
    ];

    protected $m2AttrHandlers = [
    ];

    protected $ignoreAttrs = [
        'url_path',
        'is_anchor',
        'custom_layout_update',
    ];

    protected $attributeChangeNames = [
        // 'url_key' => 'url'
    ];

    public function run()
    {
        $total = \ZentoSupport\M2Data\Model\ORM\Catalog\Category::count();
        $this->command->getOutput()->progressStart($total);

        $collection = \ZentoSupport\M2Data\Model\ORM\Catalog\Category::with(['integerattrs.codedesc',
            'varcharattrs.codedesc', 
            'textattrs.codedesc',
            'datetimeattrs.codedesc',
            'decimalattrs.codedesc'])
            ->get();
        foreach($collection as $item) {
            $this->command->getOutput()->progressAdvance();
            $category = Category::find($item->entity_id);
            if (!$category) {
                $category = new Category();
            }
            $category->id = $item->entity_id;
            $category->attribute_set_id = $item->attribute_set_id;
            $category->parent_id = $item->parent_id;
            $category->position = $item->position;
            $category->path = $item->path;
            $category->name = $category->id;
            $category->level = $item->level;
            $category->children_count = $item->children_count;
            $category->active = true;
            $category->sort_by = 0;
            $category->save();

            foreach(['integer','text', 'varchar', 'datetime', 'decimal'] as $ftype) {
                $this->saveEavs($item, $ftype, $category);
            }

            $category->push();
        }
        $this->command->getOutput()->progressFinish();
    }
}
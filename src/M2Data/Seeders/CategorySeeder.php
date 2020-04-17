<?php
namespace ZentoSupport\M2Data\Seeders;

use Illuminate\Support\Facades\DB;
use Zento\Catalog\Model\ORM\Category;
use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeInSet;

class CategorySeeder extends \Illuminate\Database\Seeder {
    use TraitEavHelper;

    protected $attrsInMainTable = [
        'default_sort_by' => 'sort_by',         //m2 attr => zento
        'active' => 'active',
    // ];

    // protected $attrsInDescriptionTable = [
        'name' => 'name',         //m2 attr => zento
        'description' => 'description',
        'meta_description' => 'meta_description',         //m2 attr => zento
        'meta_keyword' => 'meta_keyword',
        'meta_title' => 'meta_title',
    ];

    protected $attrsIgnore = [
        'thumbnail',
        'small_image',
        'small_image_label',
    ];


    public function run()
    {
        $collection = \ZentoSupport\M2Data\Model\ORM\Catalog\Category::with(['integerattrs.codedesc',
            'varcharattrs.codedesc', 
            'textattrs.codedesc',
            'datetimeattrs.codedesc',
            'decimalattrs.codedesc'])
            ->get();
        foreach($collection as $item) {
            $category = Category::find($item->entity_id);
            if (!$category) {
                $category = new Category();
            }
            $category->id = $item->entity_id;
            $category->attribute_set_id = $item->attribute_set_id;
            $category->parent_id = $item->parent_id;
            $category->position = $item->position;
            $category->level = $item->level;
            $category->children_count = $item->children_count;
            $category->active = true;
            $category->sort_by = 0;
            // $category->name = 'category' . $category->id;
            // $category->image = '';
            $category->save();


            foreach(['integer','text', 'varchar', 'datetime', 'decimal'] as $ftype) {
                $relation = $ftype .'attrs';
                foreach($item->{$relation} ?? [] as $eavItem) {
                    if (!$eavItem->codedesc) {
                        continue;
                    }
                    
                    $attrKeysInMainTable = array_keys($this->attrsInMainTable);
                    if (in_array($eavItem->codedesc->attribute_code, $attrKeysInMainTable)) {
                        $zentoKey = $this->attrsInMainTable[$eavItem->codedesc->attribute_code];
                        $category->{$zentoKey} = $eavItem->value;
                        continue;
                    }

                    list($attrId, $tableName) = DanamicAttributeFactory::createRelationShipORM($category,
                        $eavItem->codedesc->attribute_code, [$ftype], 
                        $this->isSingleEav($eavItem->codedesc->frontend_input),
                        !empty($eavItem->codedesc->options)
                    );
                    
                    $attrInSet = DynamicAttributeInSet::where('attribute_set_id', $category->attribute_set_id)
                        ->where('attribute_id', $attrId)
                        ->first();
                    if (!$attrInSet) {
                        $attrInSet = new DynamicAttributeInSet();
                    }
                    $attrInSet->attribute_set_id = $category->attribute_set_id;
                    $attrInSet->attribute_id = $attrId;
                    $attrInSet->save();

                    //migrate option value
                    $this->migrateOptionValue($eavItem->codedesc, 'categories', $attrId);
                    
                    if ($eavItem->value) {
                        DanamicAttributeFactory::single($category, $eavItem->codedesc->attribute_code)->newValue($eavItem->value);
                    }
                }
            }

            $category->push();
        }
    }
}
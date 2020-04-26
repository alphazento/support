<?php
namespace ZentoSupport\M2Data\Seeders;

use Illuminate\Support\Facades\DB;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeSet;

use Zento\Kernel\Facades\DanamicAttributeFactory;

class AttributeSetSeeder extends \Illuminate\Database\Seeder {

    // Magento2 entity type id list
    // customer – Entity Id = 1
    // customer_address – Entity Id = 2
    // catalog_category – Entity Id = 3
    // catalog – Entity Id = 4
    // order – Entity Id = 5
    // invoice – Entity Id = 6
    // creditmemo – Entity Id = 7
    // shipment – Entity Id = 8

    protected $entityTypeToModelMappings = [
        '1' => 'customers', 
        '2' => 'customer_addresses', 
        '3' => 'categories', 
        '4' => 'products', 
        '5' => 'orders', 
        '6' => 'invoices',
        '7' => 'creditmemos', 
        '8' => 'shipments'
    ];
    
    public function run()
    {
        $collection = \ZentoSupport\M2Data\Model\ORM\EavAttributeSet::get();
        $this->command->getOutput()->progressStart(count($collection));
        foreach($collection as $srcItem) {
            $tgtAttrSet = DynamicAttributeSet::find($srcItem->attribute_set_id);
            if (!$tgtAttrSet) {
                $tgtAttrSet = new DynamicAttributeSet();
            }
            $tgtAttrSet->id = $srcItem->attribute_set_id;
            $tgtAttrSet->model = $this->entityTypeToModelMappings[$srcItem->entity_type_id];
            $tgtAttrSet->name = $srcItem->attribute_set_name;
            $tgtAttrSet->description = sprintf('%s for %s', $tgtAttrSet->name, $tgtAttrSet->model);
            $tgtAttrSet->active = 1;
            $tgtAttrSet->save();
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();
    }
}
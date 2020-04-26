<?php
namespace ZentoSupport\M2Data\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Macroable;
use Zento\Catalog\Model\ORM\Product;

use Zento\Catalog\Model\ORM\ProductPrice;
use Zento\Catalog\Model\ORM\ProductRrp;
use Zento\Catalog\Model\ORM\ProductSpecialPrice;

class ProductSeeder extends \Illuminate\Database\Seeder {
    use TraitEavHelper, Macroable;

    protected $m2AttrsMappingToZentoTableFields = [
        'sku' => 'sku',
        'name' => 'name',
        'status' => 'active',
    ];

    protected $m2AttrHandlers = [
        //price group
        'cost' => 'saveCost',
        'price' => 'savePrice',
        'special_price' => 'saveSpecialPrice',
        'special_from_date' => 'saveSpecialFromDate',
        'special_to_date' => 'saveSpecialToDate',
    ];

    protected $attributeChangeNames = [
        // 'url_key' => 'url'
    ];

    protected $ignoreAttrs = [
        'small_image', 
        'thumbnail',
        'erin_recommends',
        'sale' => 'sale',
        'new' => 'new',
    ];

    public function run()
    {
        $total = \ZentoSupport\M2Data\Model\ORM\Catalog\Product::count();
        $this->command->getOutput()->progressStart($total);

        $collection = \ZentoSupport\M2Data\Model\ORM\Catalog\Product::with(
            [
                'integerattrs.codedesc.options.value',
                'varcharattrs.codedesc.options.value', 
                'textattrs.codedesc.options.value',
                'datetimeattrs.codedesc.options.value',
                'decimalattrs.codedesc.options.value',
                'galleryattrs.codedesc.options.value',
                'galleryattrs.galleryvalue',
                'galleryattrs.video',
            ])
            ->get();
        foreach($collection as $item) {
            $this->command->getOutput()->progressAdvance();
            $product = Product::find($item->entity_id) ?? new Product();

            $product->id = $item->entity_id;
            $product->attribute_set_id = $item->attribute_set_id;
            $product->model_type = $item->type_id;
            $product->has_options = $item->has_options;
            $product->required_options = $item->required_options;
            $product->name = '';
            $product->sku = $item->sku;
            $product->active = true;
            $product->save();

            foreach(['integer', 'text', 'varchar', 'datetime', 'decimal', 'gallery'] as $ftype) {
                $this->saveEavs($item, $ftype, $product);
            }
            $product->unsetRelation('configurables');
            $product->push();
        }
        $this->command->getOutput()->progressFinish();
    }

    protected function saveCost($product, $value) {
        $rrpItem = ProductRrp::where('product_id', $product->id)->first() ?? new ProductRrp;
        $rrpItem->product_id = $product->id;
        $rrpItem->cost = $value;
        $rrpItem->save();
    }

    protected function savePrice($product, $value) {
        $priceItem = ProductPrice::where('product_id', $product->id)
            ->where('customer_group_id', 0)
            ->first() ?? new ProductPrice;
        $priceItem->product_id = $product->id;
        $priceItem->price = $value;
        $priceItem->save();
    }

    protected function saveSpecialPrice($product, $value) {
        $priceItem = ProductSpecialPrice::where('product_id', $product->id)
            ->where('customer_group_id', 0)
            ->first() ?? new ProductSpecialPrice;
        $priceItem->special_price = $value;
        $priceItem->save();
    }

    protected function saveSpecialFromDate($product, $value) {
        $priceItem = ProductSpecialPrice::where('product_id', $product->id)
            ->where('customer_group_id', 0)
            ->first() ?? new ProductSpecialPrice;
        $priceItem->product_id = $product->id;
        $priceItem->special_from = $value;
        $priceItem->save();
    }

    protected function saveSpecialToDate($product, $value) {
        $priceItem = ProductSpecialPrice::where('product_id', $product->id)
            ->where('customer_group_id', 0)
            ->first() ?? new ProductSpecialPrice;
        $priceItem->product_id = $product->id;
        $priceItem->special_to = $value;
        $priceItem->save();
    }
}
<?php

namespace ZentoSupport\M2Data\Model\ORM\Catalog;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\ProductIntAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\ProductTextAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\ProductVarcharAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\ProductDatetimeAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\ProductDecimalAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\ProductMediaGalleryAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Product\MediaGallery\ValueToEntity;

class Product extends \ZentoSupport\M2Data\Model\ORM\Magento2Model
{
    protected $table = 'catalog_product_entity';
    protected $primaryKey = 'entity_id';

    public function integerattrs() {
        return $this->hasMany(ProductIntAttribute::class, 'entity_id');
    }

    public function textattrs() {
        return $this->hasMany(ProductTextAttribute::class, 'entity_id');
    }

    public function varcharattrs() {
        return $this->hasMany(ProductVarcharAttribute::class, 'entity_id');
    }

    public function datetimeattrs() {
        return $this->hasMany(ProductDatetimeAttribute::class, 'entity_id');
    }

    public function decimalattrs() {
        return $this->hasMany(ProductDecimalAttribute::class, 'entity_id');
    }

    public function galleryattrs() {
        return $this->hasManyThrough(ProductMediaGalleryAttribute::class, ValueToEntity::class, 'entity_id', 'value_id', 'entity_id', 'value_id');
    }
}
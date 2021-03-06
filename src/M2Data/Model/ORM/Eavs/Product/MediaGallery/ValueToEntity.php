<?php

namespace ZentoSupport\M2Data\Model\ORM\Eavs\Product\MediaGallery;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;

class ValueToEntity extends \ZentoSupport\M2Data\Model\ORM\Magento2Model
{
    protected $primaryKey = 'value_id';
    protected $table = 'catalog_product_entity_media_gallery_value_to_entity';
}
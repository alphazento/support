<?php

namespace ZentoSupport\M2Data\Model\ORM\Eavs\Category;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;

class CategoryDecimalAttribute extends \ZentoSupport\M2Data\Model\ORM\Eavs\AttrBase
{
    protected $table = 'catalog_category_entity_decimal';
}
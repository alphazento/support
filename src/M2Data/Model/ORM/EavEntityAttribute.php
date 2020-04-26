<?php

namespace ZentoSupport\M2Data\Model\ORM;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;

class EavEntityAttribute extends Magento2Model
{
    protected $table = 'eav_entity_attribute';
    protected $primaryKey = 'entity_attribute_id';

    public function attribute() {
        return $this->hasOne(EavAttribute::class, 'attribute_id', 'attribute_id');
    }

    public function attributeSet() {
        return $this->hasOne(EavAttributeSet::class, 'attribute_set_id', 'attribute_set_id');
    }

    // public function attributeGroup() {
    //     return $this->hasOne(EavAttributeSet::class, 'attribute_set_id', 'attribute_set_id');
    // }
}
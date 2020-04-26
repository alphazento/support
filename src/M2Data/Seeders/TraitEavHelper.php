<?php
namespace ZentoSupport\M2Data\Seeders;

use ZentoSupport\M2Data\Model\ORM\EavAttribute;

use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeValueMap;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute;
use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeInSet;

trait TraitEavHelper {
    protected $front_input_mappings = [
        'select' => 'config-options-item',
        'multiselect' => 'config-multi-options-item',
        'price' => 'config-text-item',
        'weight' => 'config-text-item',
        'text' => 'config-text-item',
        'date' => 'config-date-item',
        'hidden' => 'config-text-item',
        'boolean' => 'config-boolean-item',
        'multiline' => 'config-longtext-item',
        'textarea' => 'config-longtext-item',
        'image' => 'z-file-picker',
        'media_image' => 'z-file-picker',
        'gallery' => 'z-file-picker',
    ];

    protected $dynamicAttributeCache;

    protected function isSingleEav($frontend_input) {
        switch($frontend_input) {
            case 'text':
            case 'hidden':
            case 'select':
            case 'multiline':
            case 'textarea':
            case 'price':
            case 'weight':
            case 'media_image':
            case 'date':
            case 'boolean':
                return true;
            case 'multiselect':
            case 'gallery':
                return false;
        }
        return true;
    }

    protected function migrateOptionValue(EavAttribute $codedesc, $modelName, $attrId = 0) {
        if (!$codedesc->options) return;

        if (!$codedesc->options || count($codedesc->options) == 0) {
            $this->command->error(sprintf('%s has no options', $codedesc->attribute_code));
            return;
        }

        $dynAttribute = null;
        if (!$attrId) {
            if ($dynAttribute = DynamicAttribute::where('parent_table', $model)
                    ->where('name', $codedesc->attribute_code)
                    ->first()) 
            {
                $attrId = $dynAttribute->id;
            }
        } else {
            $dynAttribute = DynamicAttribute::find($attrId);
        }
        if ($attrId) {
            foreach($codedesc->options as $option) {
                if ($option->value) {
                    // $attrValue = DynamicAttributeValueMap::where('attribute_id', $attrId)
                    //     ->where('value', $option->value->value)
                    //     ->first();
                    $attrValue = DynamicAttributeValueMap::find($option->option_id);
                    if (!$attrValue) {
                        if ($option->swatch) {
                            $dynAttribute->swatch = 1 + $option->swatch->type;
                            $dynAttribute->update();
                        }
                        $attrValue = DynamicAttributeValueMap::create([
                            'id' => $option->option_id, 
                            'attribute_id' => $attrId, 
                            'value' => $option->value->value,
                            'swatch_value' => ($option->swatch ? $option->swatch->value : null)
                            ]);
                    }
                }
            }
        }
    }
   
    protected function saveEavs($m2Instance, $ftype, $zentoInstance) {
        $relation = $ftype .'attrs';
        foreach($m2Instance->{$relation} ?? [] as $eavItem) {
            if (!$eavItem->codedesc) {
                continue;
            }
            $attributeCode = $eavItem->codedesc->attribute_code;
            $value = $eavItem->value;
            if ($value == null) {
                continue;
            }

            if ($valueHandler = $this->m2AttrHandlers[$attributeCode] ?? false) {
                $result = $this->{$valueHandler}($zentoInstance, $value);
                if ($result == null) {
                    $this->command->error('continue');
                    continue;
                }
                $value = $result;
            }
            if (in_array($attributeCode, $this->ignoreAttrs)) {
                // $this->command->info(sprintf('skip attribute code [%s] value=[%s] frontend=[%s]', 
                //     $attributeCode, 
                //     $value,
                //     $eavItem->codedesc->frontend_input));
                continue;
            }

            //not eav attribute in alphazento
            if ($zentoField = $this->m2AttrsMappingToZentoTableFields[$attributeCode] ?? false) {
                $zentoInstance->{$zentoField} = $value;
                continue;
            }

            // $this->command->info(sprintf('[%s] = [%s]', 
            //     $attributeCode, 
            //     $eavItem->value));

            $zentoAttribute = $this->attributeChangeNames[$attributeCode] ?? $attributeCode;
            //not eav attribute in alphazento
            list($attrId, $attrTableName, $isSingleDyn) = 
                $this->hitDynamicAttribueFromCache($zentoInstance, $zentoAttribute, $ftype, $eavItem);
            //save item value to dynamic attribute value
            if ($value) {                
                if ($isSingleDyn) {
                    DanamicAttributeFactory::single($zentoInstance, $zentoAttribute)
                        ->newValue($value);
                } else {
                    if ($options = DanamicAttributeFactory::option($zentoInstance, $zentoAttribute)) {
                        if ($eavItem->codedesc->frontend_input == 'multiselect') {
                            $values = explode(',', $value);
                        } else {
                            $values = [$eavItem->value];
                        }
                        foreach($values as $value) {
                            $options->newValue($value);
                        }
                    }
                }
            }
        }
    }

    protected function hitDynamicAttribueFromCache($instance, $attributeCode, $ftype, $eavItem) {
        $isSingleDyn = $this->isSingleEav($eavItem->codedesc->frontend_input);
        $tableName = $instance->getTable();
        $cacheKey = sprintf('%s.%s.%s', $tableName, $isSingleDyn ? 1 : 0, $attributeCode);

        $this->loadAlphazentoDynamicAttributes($tableName);
        if (isset($this->dynamicAttributeCache[$cacheKey])) {
            $results = $this->dynamicAttributeCache[$cacheKey];
        } else {
            $results = DanamicAttributeFactory::createRelationShipORM(
                $instance,
                $attributeCode, 
                [$ftype === 'gallery' ? 'varchar' : $ftype], 
                '', //$front_component
                $eavItem->codedesc->frontend_label ? $eavItem->codedesc->frontend_label : '',
                $this->front_input_mappings[$eavItem->codedesc->frontend_input] ?? 'z-label', //admin_component,
                $isSingleDyn,
                !empty($eavItem->codedesc->options)
            );
            $results[] = $isSingleDyn;

            //migrate option value
            $this->dynamicAttributeCache[$cacheKey] = $results;
            $this->migrateOptionValue($eavItem->codedesc, $tableName, $results[0]);
        }
        list($attrId, $attrTableName, $isSingleDyn) = $results;
        $attrInSet = DynamicAttributeInSet::where('attribute_set_id', 
                $instance->attribute_set_id)
                ->where('attribute_id', $attrId)
                ->first();
        if (!$attrInSet) {
            $attrInSet = new DynamicAttributeInSet();
        }
        $attrInSet->attribute_set_id = $instance->attribute_set_id;
        $attrInSet->attribute_id = $attrId;
        $attrInSet->save();
        return $results;
    }

    protected function loadAlphazentoDynamicAttributes($table) {
        if (!$this->dynamicAttributeCache) {
            $collection = DynamicAttribute::where('parent_table', $table)->get();
            foreach($collection as $item) {
                $key = sprintf('%s.%s.%s', $item->parent_table, $item->single, $item->name);
                $this->dynamicAttributeCache[$key] = [$item->id, $item->attribute_table, $item->single];
            }
        }
    }
}
    
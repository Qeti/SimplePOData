<?php

namespace qeti\SimplePOData;

trait EntityTrait {

    /**
     * Default mapping of fields
     * @param array|null $record Record with data
     */
    public static function fromRecord($record)
    {
        if (!$record) {
            return null;
        }
        $className = get_called_class();
        $entity = new $className;
        foreach ($record as $key => $value) {
            $entity->$key = $value;
        }
        return $entity;
    }

}

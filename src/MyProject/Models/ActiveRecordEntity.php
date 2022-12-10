<?php

namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity
{
    /** @var int */
    protected $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    private function mappedProperties(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $mappedProperties[$propertyName] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    public function save(): void
    {
        $properties = $this->mappedProperties();
        
        
        // if ($this->id !== null) {
        //     $this->update($properties);
        //   } else {
              $this->insert($properties);
          //}
    }

    private function update(array $mappedProperties): void
    {

    }


    private function insert(array $properties): void
    {
        $db = Db::getInstance();
        $db->insertToDb($properties);
    }


    // public function refresh(): void
    // {
    //     $db = Db::getInstance();
    //     $db->updateDb(array($this));
    // }


    /**
     * @param string $id
     * @return static|null
     */
    public static function getById(string $id): ?self
    {
        $db = Db::getInstance();
        $entities  = $db->getUserId($id);
        return $entities ? $entities[0] : null;
    }
}
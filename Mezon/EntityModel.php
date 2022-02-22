<?php
namespace Mezon;

use Mezon\Functional\Fetcher;
use Mezon\Service\DbServiceModelBase;

class EntityModel extends DbServiceModelBase
{

    // TODO move entity model to the separate package
    // TODO make something in common with DBServiceModel????

    /**
     * Loaded entity data
     *
     * @var ?object
     */
    private $data = null;

    /**
     * Entity name
     *
     * @var string
     */
    protected static $entity = '';

    /**
     * Fields
     *
     * @var array
     */
    protected static $fields = [];

    /**
     * Fields set
     *
     * @var FieldsSet
     */
    private $fieldsSet;

    /**
     * Constructor
     *
     * @param object $data
     *            entity data
     */
    public function __construct(object $data = null)
    {
        $this->data = $data;

        $this->setTableName(static::$entity);

        // TODO create class FieldSetSimple without custom fields and
        // FieldSetComplex with custom fields and use here FieldSetSimple

        $this->fieldsSet = new FieldsSet(static::$fields);
    }

    /**
     * Method returns entity's raw data
     *
     * @return object entity's raw data
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * Method sets entity's data
     *
     * @param ?object $data
     *            setting data
     */
    protected function setData(?object $data): void
    {
        $this->data = $data;
    }

    /**
     * Method asserts that data was loaded
     */
    protected function assertDataWasLoaded(): void
    {
        if ($this->data === null) {
            throw (new \Exception('Data was not loaded', - 1));
        }
    }

    /**
     * Method deletes entity
     *
     * @return int id of the deleted entity
     */
    public function delete(): int
    {
        $this->assertDataWasLoaded();

        $entityId = Fetcher::getField($this->data, 'id');

        static::deleteById($entityId);

        $this->data = null;

        return $entityId;
    }

    /**
     * Method deletes entity
     *
     * @param int $id
     *            entity's id
     */
    public static function deleteById(int $id): void
    {
        static::getConnection()->prepare('DELETE FROM ' . static::$entity . ' WHERE id = :id');
        static::getConnection()->bindParameter(':id', $id, \PDO::PARAM_INT);
        static::getConnection()->execute();
    }

    /**
     * Method sets field vaue
     *
     * @param string $fieldName
     *            field name
     * @param string $fieldValue
     *            field value
     */
    public function setField(string $fieldName, string $fieldValue): void
    {
        $this->assertDataWasLoaded();

        if (isset($this->data->$fieldName)) {
            $this->data->$fieldName = $fieldValue;
        } else {
            throw (new \Exception('Field "' . $fieldName . '" was not found', - 1));
        }
    }

    /**
     * Method sets fields
     *
     * @param array $fields
     *            fields
     */
    public function setFields(array $fields): void
    {
        foreach ($fields as $fieldName => $fieldValue) {
            $this->setField($fieldName, $fieldValue);
        }
    }

    /**
     * Method returns field's value
     *
     * @param string $fieldName
     *            field's name
     * @return mixed field's value
     */
    protected function getField(string $fieldName)
    {
        $this->assertDataWasLoaded();

        if (isset($this->data->$fieldName)) {
            return $this->data->$fieldName;
        } else {
            throw (new \Exception('Field "' . $fieldName . '" was not found', - 1));
        }
    }

    /**
     * Method returns entity id
     *
     * @return int id of the entity
     */
    public function getId(): int
    {
        return (int) $this->getField('id');
    }

    /**
     * Method loads entity by it's id
     *
     * @param int $id
     *            id of the record
     */
    public function loadById(int $id): void
    {
        static::getConnection()->prepare('SELECT * FROM ' . static::$entity . ' WHERE id = :id');
        static::getConnection()->bindParameter(':id', $id, \PDO::PARAM_INT);
        $data = static::getConnection()->executeSelect();

        if (empty($data)) {
            throw (new \Exception('Record with the id ' . $id . ' was not found', - 1));
        }

        $this->setData($data[0]);
    }

    /**
     * Compiling SET query
     *
     * @param array $record
     *            record
     * @return string SET query
     */
    private function compileSetQuery(array $record): string
    {
        $return = [];

        foreach (array_keys($record) as $key) {
            $return[] = $key . ' = :' . $key;
        }

        return implode(', ', $return);
    }

    /**
     * Setting value for parameter
     *
     * @param string $field
     *            field name
     * @param string $value
     *            field value
     */
    private function bindParameter(string $field, string $value): void
    {
        switch ($this->fieldsSet->getFieldType($field)) {
            case ('int'):
                static::getConnection()->bindParameter(':' . $field, $value, \PDO::PARAM_INT);
                break;
            case ('string'):
                static::getConnection()->bindParameter(':' . $field, $value, \PDO::PARAM_STR);
                break;
            default:
                throw (new \Exception('Undefined type : "' . $this->fieldsSet->getFieldType($field) . '"', - 1));
        }
    }

    /**
     * Binding parameters
     *
     * @param string[] $record
     *            record
     */
    private function bindParameters(array $record): void
    {
        foreach ($record as $field => $value) {
            $this->bindParameter($field, $value);
        }
    }

    /**
     * Method updates record
     *
     * @param string[] $record
     *            record with the updating data
     */
    protected function updateRecord(array $record): void
    {
        $setQuery = $this->compileSetQuery($record);

        static::getConnection()->prepare('UPDATE ' . static::$entity . ' SET ' . $setQuery . ' WHERE id = :id');

        $this->bindParameters($record);

        static::getConnection()->bindParameter(':id', $this->getId(), \PDO::PARAM_INT);
        static::getConnection()->execute();

        $this->setFields($record);
    }

    /**
     * Compiling VALUES query
     *
     * @param string[] $record
     *            record
     * @return string VALUES query
     */
    private function compileValuesQuery(array $record): string
    {
        $return = [];

        foreach (array_keys($record) as $key) {
            $return[] = ':' . $key;
        }

        return implode(', ', $return);
    }

    /**
     * Creating record
     *
     * @param string[] $record
     *            record to be created
     * @return int id of the created record
     */
    protected function createRecord(array $record): int
    {
        static::getConnection()->prepare(
            'INSERT INTO ' . static::$entity . ' (' . implode(', ', array_keys($record)) . ') VALUES (' .
            $this->compileValuesQuery($record) . ')');

        $this->bindParameters($record);

        static::getConnection()->execute();

        $this->loadById(static::getConnection()->lastInsertId());

        return $this->getId();
    }
}

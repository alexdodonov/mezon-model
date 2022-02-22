<?php
namespace Mezon\Tests;

use Mezon\EntityModel;

class TestEntityModel extends EntityModel
{

    /**
     * Entity name
     *
     * @var string
     */
    protected static $entity = 'test';

    /**
     * Fields
     *
     * @var array
     */
    protected static $fields = [
        'id' => [
            'type' => 'int'
        ],
        'title' => [
            'type' => 'string'
        ],
        'domain_id' => [
            'type' => 'int'
        ],
        'invalid' => [
            'type' => 'invalid type'
        ]
    ];

    /**
     * Updating loaded record
     *
     * @param string $title
     *            new title
     */
    public function update(string $title): void
    {
        $this->updateRecord([
            'title' => $title
        ]);
    }

    /**
     * Creating new record
     *
     * @param string $title
     *            title
     * @param int $domainId
     *            domain id
     * @return int id of the created record
     */
    public function create(string $title, int $domainId): int
    {
        return $this->createRecord([
            'title' => $title,
            'domain_id' => $domainId
        ]);
    }

    /**
     * Checking invalid type processing
     */
    public function createInvalid(): void
    {
        $this->createRecord([
            'invalid' => 1
        ]);
    }
}

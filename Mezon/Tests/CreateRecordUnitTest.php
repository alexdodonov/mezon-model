<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Functional\Fetcher;
use Mezon\PdoCrud\Tests\PdoCrudMock;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class CreateRecordUnitTest extends TestCase
{

    /**
     * Testing createRecord method
     */
    public function testCreateRecord(): void
    {
        // setup
        $connection = new PdoCrudMock();
        $connection->lastInsertIdResut = 2;

        $obj = new \stdClass();
        $obj->id = 2;
        $obj->title = 'new title';
        $connection->selectResults[] = [
            $obj
        ];

        $model = new TestEntityModel();
        $model->setConnection($connection);

        // test body
        $id = $model->create('new title', 1);

        // assertions
        $this->assertEquals(2, $id);
        $this->assertEquals(2, $model->getId());
        $this->assertEquals('new title', Fetcher::getField($model->getData(), 'title'));
        $this->assertEquals(
            'INSERT INTO test (title, domain_id) VALUES (:title, :domain_id)',
            $connection->prepareStatements[0]);
        $this->assertEquals(1, $connection->executeWasCalledCounter);
        // asserting title
        $this->assertEquals(':title', $connection->bindedParameters[0][0]);
        $this->assertEquals('new title', $connection->bindedParameters[0][1]);
        // asserting id
        $this->assertEquals(':domain_id', $connection->bindedParameters[1][0]);
        $this->assertEquals('1', $connection->bindedParameters[1][1]);
        $this->assertEquals(1, $connection->executeSelectWasCalledCounter);
    }

    /**
     * Testing exception while invalid type handling
     */
    public function testInvalidTypeException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Undefined type : "invalid type"');

        // setup
        $model = new TestEntityModel();

        // test body
        $model->createInvalid();
    }
}

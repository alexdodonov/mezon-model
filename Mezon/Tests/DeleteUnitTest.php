<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\PdoCrud\Tests\PdoCrudMock;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class DeleteUnitTest extends TestCase
{

    /**
     * Testing delete method
     */
    public function testDelete(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 1;
        $obj->title = 'title';

        $connection = new PdoCrudMock();

        $model = new TestEntityModel($obj);
        $model->setConnection($connection);

        // test body
        $model->delete();

        // assertions
        $this->assertEquals(1, $connection->executeWasCalledCounter);
        $this->assertEquals('DELETE FROM test WHERE id = :id', $connection->prepareStatements[0]);
        $this->assertEquals(1, $connection->bindedParameters[0][1]);
    }

    /**
     * Testing exception while deleting record
     */
    public function testDeleteException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Data was not loaded');

        // setup
        $model = new TestEntityModel();

        // test body
        $model->delete();
    }
}

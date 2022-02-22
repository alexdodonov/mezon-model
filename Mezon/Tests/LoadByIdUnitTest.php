<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Functional\Fetcher;
use Mezon\PdoCrud\Tests\PdoCrudMock;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class LoadByIdUnitTest extends TestCase
{

    /**
     * Testing loadById method
     */
    public function testLoadById(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 1;
        $obj->title = 'title';

        $connection = new PdoCrudMock();
        $connection->selectResults[] = [
            $obj
        ];

        $model = new TestEntityModel();
        $model->setConnection($connection);

        // test body
        $model->loadById(1);

        // assertions
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('title', Fetcher::getField($model->getData(), 'title'));
        $this->assertEquals('SELECT * FROM test WHERE id = :id', $connection->prepareStatements[0]);
        $this->assertEquals(1, $connection->bindedParameters[0][1]);
    }

    /**
     * Testing exception in loadById method
     */
    public function testLoadByIdException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Record with the id 1 was not found');

        // setup
        $connection = new PdoCrudMock();
        $connection->selectResults[] = [];

        $model = new TestEntityModel();
        $model->setConnection($connection);

        // test body
        $model->loadById(1);
    }
}

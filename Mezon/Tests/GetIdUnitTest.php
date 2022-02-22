<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\PdoCrud\Tests\PdoCrudMock;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class GetIdUnitTest extends TestCase
{

    /**
     * Testing exception while getting id of the record wich was not loaded
     */
    public function testGetIdException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Data was not loaded');

        // setup
        $model = new TestEntityModel();

        // test body
        $model->getId();
    }

    /**
     * Testing getId method
     */
    public function testGetId(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 1;

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
    }

    /**
     * Testing exception while getting invalid id
     */
    public function testGetInvalidId(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 'abc';
        $model = new TestEntityModel($obj);

        // test body and assertions
        $this->assertEquals(0, $model->getId());
    }
}

<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Functional\Fetcher;
use Mezon\PdoCrud\Tests\PdoCrudMock;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class UpdateRecordUnitTest extends TestCase
{

    /**
     * Testing updateRecord method
     */
    public function testUpdateRecord(): void
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
        $model->loadById(1);

        // test body
        $model->update('new title');

        // assertions
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('new title', Fetcher::getField($model->getData(), 'title'));
        $this->assertEquals('UPDATE test SET title = :title WHERE id = :id', $connection->prepareStatements[1]);
        $this->assertEquals(1, $connection->executeWasCalledCounter);
        // asserting title param
        $this->assertEquals(':title', $connection->bindedParameters[1][0]);
        $this->assertEquals('new title', $connection->bindedParameters[1][1]);
        // asserting id param
        $this->assertEquals(':id', $connection->bindedParameters[2][0]);
        $this->assertEquals('1', $connection->bindedParameters[2][1]);
    }

    /**
     * Testing exception while updating not loaded record
     */
    public function testUpdateRecordException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Data was not loaded');

        // setup
        $connection = new PdoCrudMock();
        $model = new TestEntityModel();
        $model->setConnection($connection);

        // test body
        $model->update('new title');
    }
}

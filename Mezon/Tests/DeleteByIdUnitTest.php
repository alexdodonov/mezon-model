<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\PdoCrud\Tests\PdoCrudMock;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class DeleteByIdUnitTest extends TestCase
{

    /**
     * Testing deleteById method
     */
    public function testDeleteById(): void
    {
        // setup
        $connection = new PdoCrudMock();
        TestEntityModel::setConnection($connection);

        // test body
        TestEntityModel::deleteById(1);

        // assertions
        $this->assertEquals(1, $connection->executeWasCalledCounter);
        $this->assertEquals('DELETE FROM test WHERE id = :id', $connection->prepareStatements[0]);
        $this->assertEquals(1, $connection->bindedParameters[0][1]);
    }
}

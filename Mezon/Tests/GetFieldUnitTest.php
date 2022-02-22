<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class GetFieldUnitTest extends TestCase
{

    /**
     * Testing exception in getField method
     */
    public function testGeFieldException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Field "id" was not found');

        // setup
        $obj = new \stdClass();
        $model = new TestEntityModel($obj);

        // test body
        $model->getId();
    }
}

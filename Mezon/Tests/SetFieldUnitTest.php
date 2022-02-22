<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SetFieldUnitTest extends TestCase
{

    /**
     * Testing setField method
     */
    public function testSetField(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 1;
        $obj->title = 'title';

        $model = new TestEntityModel($obj);

        // test body
        $model->setField('id', 2);

        // assertions
        $this->assertEquals(2, $model->getId());
    }

    /**
     * Testing exception when the data was not loaded
     */
    public function testSetFieldExceptionForNotLoadedData(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Data was not loaded');

        // setup
        $model = new TestEntityModel();

        // test body
        $model->setField('id', 2);
    }

    /**
     * Testing exception when setting unexisting field
     */
    public function testSetFieldExceptionForUnexistingField(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Field "title" was not found');

        // setup
        $obj = new \stdClass();
        $obj->id = 1;
        $model = new TestEntityModel($obj);

        // test body
        $model->setField('title', 't');
    }
}

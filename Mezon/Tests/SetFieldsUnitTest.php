<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Functional\Fetcher;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SetFieldsUnitTest extends TestCase
{

    /**
     * Testing setField method
     */
    public function testSetFields(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 1;
        $obj->title = 'title';

        $model = new TestEntityModel($obj);

        // test body
        $model->setFields([
            'id' => 2,
            'title' => 't'
        ]);

        // assertions
        $this->assertEquals(2, $model->getId());
        $this->assertEquals('t', Fetcher::getField($model->getData(), 'title'));
    }
}

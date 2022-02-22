<?php
namespace Mezon\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Functional\Fetcher;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConstructorUnitTest extends TestCase
{

    /**
     * Testing constructor
     */
    public function testConstruct(): void
    {
        // setup
        $obj = new \stdClass();
        $obj->id = 1;
        $obj->title = 'title';

        // test body
        $model = new TestEntityModel($obj);

        // assertions
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('title', Fetcher::getField($model->getData(), 'title'));
        $this->assertEquals('test', $model->getTableName());
    }
}

<?php

namespace Phabric;

use Mockery as m;

/**
 * This file is part of the Phabric.
 * (c) Ben Waine <ben@ben-waine.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Test class for Bus.
 * Generated by PHPUnit on 2011-07-31 at 12:12:00.
 */
class PhabricTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Phabric
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $db = m::mock('\Doctrine\DBAL\Connection');
        $this->object = new Phabric($db);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        \Mockery::close();
    }

    public function testCreateEntity() {
        $entity = $this->object->createEntity('Event');

        $this->assertType('\Phabric\Entity', $entity);
    }

    public function testCreateEntityWithConfig() {
        $entity = $this->object->createEntity('Event', array('tableName' => 'event'));

        $this->assertType('\Phabric\Entity', $entity);
    }

    public function testCreateEntityHasRegisteredEntity() {
        $this->object->createEntity('Event');

        $entity = $this->object->getEntity('Event');

        $this->assertType('\Phabric\Entity', $entity);
    }

    public function testAddDataTransformation() {
        $fn = function($a, $b) {
                    $c = $a + $b;
                    return $c;
                };

        $this->object->addDataTransformation('test', $fn);

        $this->assertEquals($fn, $this->object->getDataTransformation('test'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddDataTransformationNonCallableArgument() {
        $fn = array();

        $this->object->addDataTransformation('test', $fn);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetDataTransformationNotFound() {
        $this->object->getDataTransformation('test');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetEntityNotFound() {
        $this->object->getEntity('anything');
    }

    public function testInsertFromTable() {
        $tableData = array(
            array('name', 'datetime', 'venue', 'description'),
            array('PHPNW', '2011-10-08 09:00:00', 'Ramada Hotel', 'A Great Conf!')
        );


        $tableNode = m::mock('\Behat\Gherkin\Node\TableNode');

        $mockEntity = m::mock('\Phabric\Entity');

        $mockEntity->shouldReceive('insertFromTable')
                ->with($tableNode, null)
                ->once();

        $this->object->addEntity('TEST', $mockEntity);

        $this->object->insertFromTable('TEST', $tableNode);
    }

    public function testCreateEntitiesFromConfig() {
        
        
        $config = array(
            'event' => array(
                'tableName' => 'event'
            ),
            'vote' => array(
                'tableName' => 'vote'
            ),
            'session' => array(
                'tableName' => 'session'
            ),
        );
        
        $this->object->createEntitiesFromConfig($config);
        
        $this->assertType('Phabric\Entity', $this->object->getEntity('event'));
        $this->assertType('Phabric\Entity', $this->object->getEntity('vote'));
        $this->assertType('Phabric\Entity', $this->object->getEntity('session'));
        
    }

}


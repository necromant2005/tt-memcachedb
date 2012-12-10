<?php
namespace TweeMemcacheDb\Cache\Storage\Adapter;
use PHPUnit_Framework_TestCase;

class MemcacheDbOptionsTest extends PHPUnit_Framework_TestCase
{
    public function testAddServer()
    {
        $options = new MemcacheDbOptions;
        $options->addServer('localhost');
        $this->assertAttributeEquals(
            array(
                array('host' => '127.0.0.1', 'port' => 21201, 'weight' => 0),
                array('host' => 'localhost', 'port' => 21201, 'weight' => 0),
            ),
            'servers',
            $options
        );
    }

    public function testSetServers()
    {
        $options = new MemcacheDbOptions;
        $options->setServers('localhost');
        $this->assertAttributeEquals(
            array(
                array('host' => 'localhost', 'port' => 21201, 'weight' => 1),
            ),
            'servers',
            $options
        );
    }
}

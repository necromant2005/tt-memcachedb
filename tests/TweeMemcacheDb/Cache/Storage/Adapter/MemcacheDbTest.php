<?php
namespace TweeMemcacheDb\Cache\Storage\Adapter;
use PHPUnit_Framework_TestCase;


class MemcacheDbTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped("MemcacheDb extension is not loaded");
        }

        $this->_options = new MemcacheDbOptions();
        //if (defined('TESTS_ZEND_CACHE_MEMCACHED_HOST') && defined('TESTS_ZEND_CACHE_MEMCACHED_PORT')) {
        //    $this->_options->addServer(TESTS_ZEND_CACHE_MEMCACHED_HOST, TESTS_ZEND_CACHE_MEMCACHED_PORT);
        //} elseif (defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) {
        //    $this->_options->addServer(TESTS_ZEND_CACHE_MEMCACHED_HOST);
        //}

        $this->_storage = new MemcacheDb();
        $this->_storage->setOptions($this->_options);
        $this->_storage->flush();

        parent::setUp();
    }

    public function testOptionsAddServer()
    {
        $options = new MemcacheDbOptions();
        $options->addServer('127.0.0.1', 21201);
        $options->addServer('localhost');
        $options->addServer('domain.com', 11215);

        $servers = array(
            array('host' => '127.0.0.1', 'port' => 21201, 'weight' => 0),
            array('host' => 'localhost', 'port' => 21201, 'weight' => 0),
            array('host' => 'domain.com', 'port' => 11215, 'weight' => 0),
        );

        $this->assertEquals($options->getServers(), $servers);
        $memcached = new MemcacheDb($options);
        $this->assertEquals($memcached->getOptions()->getServers(), $servers);
    }

    public function getServersDefinitions()
    {
        $expectedServers = array(
            array('host' => '127.0.0.1', 'port' => 12345, 'weight' => 1),
            array('host' => 'localhost', 'port' => 54321, 'weight' => 2),
            array('host' => 'examp.com', 'port' => 21201, 'weight' => 1),
        );

        return array(
            // servers as array list
            array(
                array(
                    array('127.0.0.1', 12345, 1),
                    array('localhost', '54321', '2'),
                    array('examp.com'),
                ),
                $expectedServers,
            ),

            // servers as array assoc
            array(
                array(
                    array('127.0.0.1', 12345, 1),
                    array('localhost', '54321', '2'),
                    array('examp.com'),
                ),
                $expectedServers,
            ),

            // servers as string list
            array(
                array(
                    '127.0.0.1:12345?weight=1',
                    'localhost:54321?weight=2',
                    'examp.com',
                ),
                $expectedServers,
            ),

            // servers as string
            array(
                '127.0.0.1:12345?weight=1, localhost:54321?weight=2,tcp://examp.com',
                $expectedServers,
            ),
        );
    }

    /**
     *
     * @dataProvider getServersDefinitions
     */
    public function testOptionSetServers($servers, $expectedServers)
    {
        $options = new MemcacheDbOptions();
        $options->setServers($servers);
        $this->assertEquals($expectedServers, $options->getServers());
    }

    public function testLibOptionsSet()
    {
        $options = new MemcacheDbOptions();

        $options->setLibOptions(array(
            'COMPRESSION' => false
        ));

        $this->assertEquals($options->getLibOption(\Memcached::OPT_COMPRESSION), false);

        $memcached = new MemcacheDb($options);
        $this->assertEquals($memcached->getOptions()->getLibOptions(), array(
            \Memcached::OPT_COMPRESSION => false
        ));
    }

    public function testNoOptionsSetsDefaultServer()
    {
        $memcached = new MemcacheDb();

        $expected = array(array(
            'host'   => '127.0.0.1',
            'port'   => 21201,
            'weight' => 0,
        ));

        $this->assertEquals($expected, $memcached->getOptions()->getServers());
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }
}

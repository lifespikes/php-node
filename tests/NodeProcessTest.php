<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use LifeSpikes\PHPNode\Instance;
use LifeSpikes\PHPNode\Engine;
use LifeSpikes\PHPNode\Exceptions\NodeExitCodeException;

final class NodeProcessTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $script = <<<JAVASCRIPT
        import fs from 'fs';
        const data = JSON.parse(fs.readFileSync(process.stdin.fd, 'utf-8'));
        
        if (data.throw) {
            throw 'Blah';
        }
        
        console.log(JSON.stringify({
            key: 'value',
            data
        }));
        JAVASCRIPT;

        file_put_contents('index.js', $script);
    }

    /**
     * @covers \LifeSpikes\PHPNode\Engine
     */
    public function test_node_binary_can_be_found(): void
    {
       $nodeScript = Engine::instanceParams(
           'index.js',
           ['--test']
       );

       $this->assertStringContainsString('bin/node', $nodeScript['node']);
       $this->assertEquals(['index.js', '--test'], $nodeScript['args']);
    }

    /**
     * @covers \LifeSpikes\PHPNode\Instance
     */
    public function test_instance_gets_proc_params()
    {
        $bin = Engine::spawn('index.js');
        $classArgsProp = (new ReflectionObject($bin))
            ->getProperty('args')
            ->getValue($bin);

        $this->assertInstanceOf(Instance::class, $bin);
        $this->assertCount(2, $classArgsProp);
    }

    /**
     * @covers \LifeSpikes\PHPNode\Instance, \LifeSpikes\PHPNode\FinishedProcess
     */
    public function test_writes_payload_to_stdin()
    {
        $bin = Engine::spawn('index.js');
        $i = $bin->with([
            'ping'  =>  'pong'
        ])->output;

        $this->assertIsArray($i);
        $this->assertEquals('value', $i['key']);

        $this->assertIsArray($i['data']);
        $this->assertEquals('pong', $i['data']['ping']);
    }

    /**
     * @covers \LifeSpikes\PHPNode\FinishedProcess
     */
    public function test_fail_throws_exception()
    {
        $this->expectException(NodeExitCodeException::class);

        $bin = Engine::spawn('index.js');
        $result = $bin->with([
            'throw'  =>  true
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists('index.js')) {
            unlink('index.js');
        }
    }
}

<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;

class ParamAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ParamAssembler */
    private $fixture;

    /** @var m\MockInterface|Analyzer */
    private $analyzer;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->analyzer = m::mock('phpDocumentor\Descriptor\Analyzer');
        $this->fixture = new ParamAssembler();
        $this->fixture->setAnalyzer($this->analyzer);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\Tags\ParamAssembler::create
     */
    public function testCreatingParamDescriptorFromReflector()
    {
        $types = new Collection();
        $this->analyzer->shouldReceive('analyze')
            ->with(
                m::on(
                    function ($value) {
                        return $value instanceof TypeCollection && $value[0] == 'string';
                    }
                )
            )
            ->andReturn($types);
        $reflector = new ParamTag('param', 'string $myParameter This is a description');

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('param', $descriptor->getName());
        $this->assertSame('This is a description', $descriptor->getDescription());
        $this->assertSame('$myParameter', $descriptor->getVariableName());
        $this->assertSame($types, $descriptor->getTypes());
    }
}

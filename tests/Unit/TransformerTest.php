<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTransformer\Tests\Unit;

use LongitudeOne\SpatialTransformer\Strategy\TransformStrategyInterface;
use LongitudeOne\SpatialTransformer\Transformer;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests orchestration independently of concrete spatial types.
 */
final class TransformerTest extends TestCase
{
    /**
     * An immutable custom strategy needs only the spatial abstraction.
     */
    public function testCustomStrategyPreservesInput(): void
    {
        $input = self::createStub(SpatialInterface::class);
        $strategy = new readonly class implements TransformStrategyInterface {
            /**
             * Return a distinct result without changing the source.
             *
             * @param SpatialInterface $spatial the source spatial object
             */
            public function execute(SpatialInterface $spatial): SpatialInterface
            {
                return clone $spatial;
            }
        };

        $result = new Transformer($strategy)->transform($input);

        self::assertNotSame($input, $result);
        self::assertEquals($input, $result);
    }

    /**
     * Pass the exact input once and return the exact strategy result.
     */
    public function testDelegatesWithoutInspectingOrMutatingInput(): void
    {
        $input = $this->createMock(SpatialInterface::class);
        $input->expects($this->never())->method(self::anything());
        $result = self::createStub(SpatialInterface::class);
        $strategy = $this->createMock(TransformStrategyInterface::class);
        $strategy->expects($this->once())->method('execute')->with(self::identicalTo($input))->willReturn($result);

        self::assertSame($result, new Transformer($strategy)->transform($input));
    }

    /**
     * Strategy failures propagate unchanged.
     */
    public function testPropagatesUnsupportedTransformation(): void
    {
        $input = self::createStub(SpatialInterface::class);
        $strategy = $this->createMock(TransformStrategyInterface::class);
        $failure = new \DomainException('Unsupported transformation.');
        $strategy->expects($this->once())->method('execute')->with($input)->willThrowException($failure);

        $this->expectExceptionObject($failure);
        new Transformer($strategy)->transform($input);
    }

    /**
     * The strategy contract exposes exactly one typed operation.
     */
    public function testStrategyContractContainsOnlyExecute(): void
    {
        $reflection = new \ReflectionClass(TransformStrategyInterface::class);
        $method = $reflection->getMethod('execute');

        self::assertCount(1, $reflection->getMethods());
        self::assertCount(1, $method->getParameters());
        self::assertSame(SpatialInterface::class, (string) $method->getParameters()[0]->getType());
        self::assertSame(SpatialInterface::class, (string) $method->getReturnType());
    }

    /**
     * The strategy dependency cannot be replaced after construction.
     */
    public function testTransformerIsFinalAndReadonly(): void
    {
        $reflection = new \ReflectionClass(Transformer::class);

        self::assertTrue($reflection->isFinal());
        self::assertTrue($reflection->isReadOnly());
        self::assertTrue($reflection->getProperty('strategy')->isPrivate());
        self::assertTrue($reflection->getProperty('strategy')->isReadOnly());
        self::assertCount(2, $reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
    }
}

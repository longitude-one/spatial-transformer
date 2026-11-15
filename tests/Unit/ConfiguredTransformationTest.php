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

use LongitudeOne\SpatialTransformer\Tests\Fixture\OffsetStrategy;
use LongitudeOne\SpatialTransformer\Transformer;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/** External strategies validate their options before the orchestrator executes them. */
final class ConfiguredTransformationTest extends TestCase
{
    /** Custom options affect only the requested ordinate. */
    public function testConfiguredOffset(): void
    {
        $point = new PointImmutable(1, 2, 4326);
        $result = new Transformer(new OffsetStrategy(['offset' => 3]))->transform($point);
        self::assertSame([4.0, 2], $result->toArray());
        self::assertSame(4326, $result->getSrid());
        self::assertSame($point->getFamily(), $result->getFamily());
        self::assertSame([1, 2], $point->toArray());
    }

    /** Default options leave the source value intact while creating a new object. */
    public function testDefaults(): void
    {
        $point = new PointImmutable(1, 2, 4326);
        $result = new Transformer(new OffsetStrategy())->transform($point);
        self::assertNotSame($point, $result);
        self::assertSame([1.0, 2], $result->toArray());
        self::assertSame(4326, $result->getSrid());
    }

    /** Application preconditions still govern unsupported inputs. */
    public function testEmptyInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Transformer(new OffsetStrategy())->transform(new PointImmutable());
    }

    /** Invalid option types fail before transformation. */
    public function testInvalidType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new OffsetStrategy(['offset' => '3']);
    }

    /** Nonfinite offsets are rejected by the application strategy. */
    public function testInvalidValue(): void
    {
        $this->expectException(InvalidOptionsException::class);
        new OffsetStrategy(['offset' => \INF]);
    }

    /** Undefined options are rejected before transformation. */
    public function testUnknownOption(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        new OffsetStrategy(['precision' => 2]);
    }
}

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

namespace LongitudeOne\SpatialTransformer;

use LongitudeOne\SpatialTransformer\Strategy\TransformStrategyInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Delegates transformations to a strategy fixed at construction.
 */
final readonly class Transformer
{
    /**
     * Bind the transformation strategy.
     *
     * @param TransformStrategyInterface $strategy the transformation strategy
     */
    public function __construct(private TransformStrategyInterface $strategy)
    {
    }

    /**
     * Return the strategy result without modifying the input.
     *
     * @param SpatialInterface $spatial the source spatial object
     */
    public function transform(SpatialInterface $spatial): SpatialInterface
    {
        return $this->strategy->execute($spatial);
    }
}

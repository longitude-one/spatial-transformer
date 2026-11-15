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

namespace LongitudeOne\SpatialTransformer\Strategy;

use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Transforms spatial objects without modifying the input.
 */
interface TransformStrategyInterface
{
    /**
     * Create the result, preserving properties not intentionally changed.
     *
     * Implementations must explicitly reject unsupported transformations.
     * Stateful strategies should be readonly with constructor configuration.
     *
     * @param SpatialInterface $spatial the unchanged source object
     */
    public function execute(SpatialInterface $spatial): SpatialInterface;
}

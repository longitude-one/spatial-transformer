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

namespace LongitudeOne\SpatialTransformer\Tests\Fixture;

use LongitudeOne\SpatialTransformer\Strategy\ConfigurableStrategyInterface;
use LongitudeOne\SpatialTransformer\Strategy\TransformStrategyInterface;
use LongitudeOne\SpatialTypes\Builder\SpatialBuilder;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** An application-owned point transformation with its own resolved configuration. */
final class OffsetStrategy implements ConfigurableStrategyInterface, TransformStrategyInterface
{
    /** @var array<string, mixed> Resolved options. */
    private readonly array $options;

    /** @param array<string, mixed> $options application-specific options */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /** @param OptionsResolver $resolver application configuration resolver */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('offset', 0.0);
        $resolver->setAllowedTypes('offset', ['int', 'float']);
        $resolver->setAllowedValues('offset', static fn (float|int $value): bool => is_finite((float) $value));
        $resolver->setNormalizer('offset', static function (Options $options, float|int $value): float {
            unset($options); // The normalizer only needs the supplied offset.

            return (float) $value;
        });
    }

    /** @param SpatialInterface $spatial unchanged source point */
    public function execute(SpatialInterface $spatial): SpatialInterface
    {
        if (!$spatial instanceof PointInterface || null === $spatial->getX()) {
            throw new \InvalidArgumentException('A nonempty point is required.');
        }

        return SpatialBuilder::create()->dimension($spatial->getDimension())->family($spatial->getFamily())
            ->srid($spatial->getSpatialReference())->point(
                $spatial->getX() + $this->options['offset'],
                $spatial->getY(),
                $spatial->hasZ() ? $spatial->getZ() : null,
                $spatial->hasM() ? $spatial->getM() : null,
            );
    }
}

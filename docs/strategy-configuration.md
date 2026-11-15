# Strategy configuration

Options are resolved when the strategy is constructed. Invalid types, unsupported values and unknown options fail before execution with Symfony OptionsResolver exceptions.
The resolver is local to construction; only the resolved options are retained.

## Built-in behavior

The library supplies an execution contract and an orchestrator, without imposing algorithm-specific options.
A transformation declares only the parameters it actually uses. Existing `execute(SpatialInterface): SpatialInterface` implementations continue to work.

## Optional base class

Implement `Strategy\ConfigurableStrategyInterface::configureOptions(OptionsResolver $resolver): void` to declare options.
Extend `Strategy\AbstractConfigurableStrategy` for an optional constructor accepting `array $options = []` and a protected readonly `$options` array.
The abstract class does not implement the execution interface: each strategy still implements its existing execution contract.
Use `setDefaults`, `setAllowedTypes`, `setAllowedValues` and `setNormalizer` to declare strategy-owned configuration.
Allowed values are checked before normalization.

## External strategy without inheritance

The orchestrator does not require the configuration interface or base class. This complete example owns its configuration lifecycle:

```php
use LongitudeOne\SpatialTransformer\Strategy\ConfigurableStrategyInterface;
use LongitudeOne\SpatialTransformer\Strategy\TransformStrategyInterface;
use LongitudeOne\SpatialTypes\Builder\SpatialBuilder;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


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
            unset($options);

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
```

```php
$transformer = new \LongitudeOne\SpatialTransformer\Transformer(new OffsetStrategy(['offset' => 3]));
$result = $transformer->transform(new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(1, 2, 4326));
// X = 4, Y = 2; source, coordinate dimension, family and spatial reference are preserved.
```

This application-specific example offsets a coordinate in its existing units; it is not a CRS conversion.
It rejects EMPTY points and non-point geometries explicitly.

## Symfony compatibility

The dependency supports Symfony OptionsResolver 5.4, 6, 7 and 8. Configuration uses the shared APIs available since 5.4.
No Symfony dependency or configuration abstraction is added to spatial-core.

# Transformation strategies

Transformations operate on spatial objects, including structural changes. The API
assumes neither a CRS transformation nor a serialization format.

```text
SpatialInterface -> Transformer::transform() -> strategy::execute() -> SpatialInterface
```

| Component | Responsibility |
| --- | --- |
| `Transformer` | Delegate to its injected strategy and return its exact result. |
| `Strategy\TransformStrategyInterface` | Define `execute(SpatialInterface $spatial): SpatialInterface` as its sole operation. |
| Concrete strategy | Determine support, create the result, preserve unaffected properties and explicitly reject unsupported transformations. |

## Implementing a strategy

```php
use LongitudeOne\SpatialTransformer\Strategy\TransformStrategyInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

// Minimal copy example; no concrete strategy is shipped by this package.
final readonly class CopyStrategy implements TransformStrategyInterface
{
    public function execute(SpatialInterface $spatial): SpatialInterface
    {
        return clone $spatial;
    }
}
```

A shallow clone can share child objects. Strategies that modify children must
create independent affected objects instead of mutating the input graph.

## Immutability and failures

`Transformer` is `final readonly`: its strategy reference cannot be replaced.
Readonly properties do not freeze the referenced strategy object. Strategies should
also be readonly, with configuration supplied through constructor injection.

The transformer does not inspect, mutate or validate the supplied object.
Strategies must preserve the input, including mutable implementations of
`SpatialInterface`, and return a spatial object representing the result.

Unsupported transformations must fail explicitly. Strategy exceptions propagate
unchanged. No package-specific exception hierarchy is introduced.
Strategies need no Doctrine, parser, serializer, database or CRS engine dependency.

See [Strategy configuration](strategy-configuration.md) for validated options and custom strategies.

<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

<<<<<<< HEAD
class <?= $class_name ?> implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private ObjectNormalizer $normalizer)
    {
=======
class <?= $class_name ?> implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        // TODO: add, edit, or delete some data

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
<<<<<<< HEAD
        return $data instanceof \App\Entity\<?= str_replace('Normalizer', '', $class_name) ?>;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
=======
<?php if ($entity_exists): ?>
        return $data instanceof <?= $entity_name ?>;
<?php else: ?>
        // TODO: return $data instanceof Object
<?php endif ?>
    }

    public function getSupportedTypes(?string $format): array
    {
<?php if ($entity_exists): ?>
        return [<?= $entity_name ?>::class => true];
<?php else: ?>
        // TODO: return [Object::class => true];
<?php endif ?>
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    }
}

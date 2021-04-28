<?php

namespace Drupal\ivw_integration\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;

/**
 * Extension to add the IVW field.
 *
 * @SchemaExtension(
 *   id = "ivw",
 *   name = "Ivw extension",
 *   description = "Adds the IVW query field.",
 *   schema = "composable"
 * )
 */
class IvwSchemaExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry) {
    $builder = new ResolverBuilder();

    $registry->addFieldResolver('Query', 'ivw', $builder->compose(
      $builder->produce('route_load')
        ->map('path', $builder->fromArgument('path')),
      $builder->produce('route_entity')
        ->map('url', $builder->fromParent()),
      $builder->produce('ivw_call')
        ->map('entity', $builder->fromParent())
    ));
  }

}

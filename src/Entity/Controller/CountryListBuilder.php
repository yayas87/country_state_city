<?php

namespace Drupal\country_state_city\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for countrylist entity.
 *
 * @ingroup country_state_city
 */
class CountryListBuilder extends EntityListBuilder {

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type, $container->get('entity_type.manager')->getStorage($entity_type->id()), $container->get('url_generator')
    );
  }

  /**
   * Constructs a new SliderListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the
   * table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('Country Entity implements a Country model.'),
    ];
    $build['table'] = parent::render();
    \Drupal::service('cache.render')->invalidateAll();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the slider list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity
   * type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('Country ID');
    $header['name'] = $this->t('Name');
    $header['iso3'] = $this->t('ISO3');
    $header['iso2'] = $this->t('ISO2');
    $header['currency'] = $this->t('Currency');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->id();
    $row['name'] = $entity->label();
    $row['iso3'] = $entity->iso3->value;
    $row['iso2'] = $entity->iso2->value;
    $row['currency'] = $entity->currency->value;
    return $row + parent::buildRow($entity);
  }

}

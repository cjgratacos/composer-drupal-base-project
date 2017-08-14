<?php

namespace Drupal\upc_marketplace\Entity;


interface MarketplaceElementEntityTypeInterface {

  /**
   * @return string
   */
  function getId():?string;

  /**
   * @param string $id
   *
   * @return $this
   */
  function setId(string $id): MarketplaceElementEntityTypeInterface;

  /**
   * @return string
   */
  function getLabel(): ?string ;

  /**
   * @param string $label
   *
   * @return $this
   */
  function setLabel(string $label): MarketplaceElementEntityTypeInterface ;

  /**
   * @return string
   */
  function getDescription(): ?string;

  /**
   * @param string $description
   *
   * @return $this
   */
  function setDescription(string $description): MarketplaceElementEntityTypeInterface;
}
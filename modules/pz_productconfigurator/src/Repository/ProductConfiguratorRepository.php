<?php

namespace PrestaZone\ProductConfigurator\Repository;

use Doctrine\DBAL\Connection;

class ProductConfiguratorRepository
{
  /** @var Connection */
  private $connection;

  /** @var string */
  private $dbPrefix;

  public function __construct(Connection $connection, $dbPrefix)
  {
    $this->connection = $connection;
    $this->dbPrefix = $dbPrefix;
  }

  public function createTables()
  {
    $queries = [];

    $queries[] = 'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . 'pz_productconfigurator_product_shop` (
      `id_product` INT UNSIGNED NOT NULL,
      `id_shop` INT UNSIGNED NOT NULL,
      `active` TINYINT(1) NOT NULL DEFAULT 0,
      `is_valid` TINYINT(1) NOT NULL DEFAULT 0,
      `config_json` LONGTEXT NULL,
      `normalized_json` LONGTEXT NULL,
      `last_error` VARCHAR(255) DEFAULT NULL,
      `date_add` DATETIME NOT NULL,
      `date_upd` DATETIME NOT NULL,
      PRIMARY KEY (`id_product`, `id_shop`),
      KEY `idx_shop_active` (`id_shop`, `active`),
      KEY `idx_shop_valid` (`id_shop`, `is_valid`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    $queries[] = 'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . 'pz_productconfigurator_cart_snapshot` (
      `id_snapshot` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_cart` INT UNSIGNED NOT NULL,
      `id_product` INT UNSIGNED NOT NULL,
      `id_product_attribute` INT UNSIGNED NOT NULL DEFAULT 0,
      `id_customization` INT UNSIGNED NOT NULL DEFAULT 0,
      `configuration_hash` CHAR(40) NOT NULL,
      `configuration_json` LONGTEXT NULL,
      `price_impact_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT 0,
      `date_add` DATETIME NOT NULL,
      `date_upd` DATETIME NOT NULL,
      PRIMARY KEY (`id_snapshot`),
      UNIQUE KEY `uniq_cart_configuration` (`id_cart`, `id_product`, `id_product_attribute`, `configuration_hash`),
      KEY `idx_cart` (`id_cart`),
      KEY `idx_cart_customization` (`id_cart`, `id_customization`),
      KEY `idx_product` (`id_product`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    $queries[] = 'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . 'pz_productconfigurator_order_snapshot` (
      `id_snapshot` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_order` INT UNSIGNED NOT NULL,
      `id_order_detail` INT UNSIGNED NOT NULL DEFAULT 0,
      `id_product` INT UNSIGNED NOT NULL,
      `id_product_attribute` INT UNSIGNED NOT NULL DEFAULT 0,
      `configuration_hash` CHAR(40) NOT NULL,
      `configuration_json` LONGTEXT NULL,
      `price_impact_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT 0,
      `date_add` DATETIME NOT NULL,
      PRIMARY KEY (`id_snapshot`),
      KEY `idx_order` (`id_order`),
      KEY `idx_order_detail` (`id_order_detail`),
      KEY `idx_product` (`id_product`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    foreach ($queries as $query) {
      $this->connection->executeStatement($query);
    }

    $this->ensureSchema();

    return true;
  }

  public function dropTables()
  {
    $queries = [];

    $queries[] = 'DROP TABLE IF EXISTS `' . $this->dbPrefix . 'pz_productconfigurator_order_snapshot`';
    $queries[] = 'DROP TABLE IF EXISTS `' . $this->dbPrefix . 'pz_productconfigurator_cart_snapshot`';
    $queries[] = 'DROP TABLE IF EXISTS `' . $this->dbPrefix . 'pz_productconfigurator_product_shop`';

    foreach ($queries as $query) {
      $this->connection->executeStatement($query);
    }

    return true;
  }

  public function getProductConfiguration($idProduct, $idShop)
  {
    return $this->connection->fetchAssociative(
      'SELECT id_product, id_shop, active, is_valid, config_json, normalized_json, last_error, date_add, date_upd
      FROM `' . $this->dbPrefix . 'pz_productconfigurator_product_shop`
      WHERE id_product = :id_product AND id_shop = :id_shop',
      [
        'id_product' => (int) $idProduct,
        'id_shop' => (int) $idShop,
      ]
    ) ?: [];
  }

  public function saveProductConfiguration($idProduct, array $shopIds, $active, $configJson, $isValid, $normalizedJson, $lastError)
  {
    $idProduct = (int) $idProduct;
    $active = (int) (bool) $active;
    $isValid = (int) (bool) $isValid;
    $configJson = $configJson !== '' ? (string) $configJson : null;
    $normalizedJson = $normalizedJson !== null && $normalizedJson !== '' ? (string) $normalizedJson : null;
    $lastError = $lastError !== null && trim((string) $lastError) !== '' ? trim((string) $lastError) : null;

    foreach ($shopIds as $shopId) {
      $shopId = (int) $shopId;
      if ($shopId <= 0) {
        continue;
      }

      $exists = (int) $this->connection->fetchOne(
        'SELECT COUNT(*)
        FROM `' . $this->dbPrefix . 'pz_productconfigurator_product_shop`
        WHERE id_product = :id_product AND id_shop = :id_shop',
        [
          'id_product' => $idProduct,
          'id_shop' => $shopId,
        ]
      );

      if ($exists > 0) {
        $this->connection->executeStatement(
          'UPDATE `' . $this->dbPrefix . 'pz_productconfigurator_product_shop`
          SET active = :active,
              is_valid = :is_valid,
              config_json = :config_json,
              normalized_json = :normalized_json,
              last_error = :last_error,
              date_upd = NOW()
          WHERE id_product = :id_product AND id_shop = :id_shop',
          [
            'id_product' => $idProduct,
            'id_shop' => $shopId,
            'active' => $active,
            'is_valid' => $isValid,
            'config_json' => $configJson,
            'normalized_json' => $normalizedJson,
            'last_error' => $lastError,
          ]
        );

        continue;
      }

      $this->connection->executeStatement(
        'INSERT INTO `' . $this->dbPrefix . 'pz_productconfigurator_product_shop`
          (id_product, id_shop, active, is_valid, config_json, normalized_json, last_error, date_add, date_upd)
        VALUES
          (:id_product, :id_shop, :active, :is_valid, :config_json, :normalized_json, :last_error, NOW(), NOW())',
        [
          'id_product' => $idProduct,
          'id_shop' => $shopId,
          'active' => $active,
          'is_valid' => $isValid,
          'config_json' => $configJson,
          'normalized_json' => $normalizedJson,
          'last_error' => $lastError,
        ]
      );
    }

    return true;
  }

  public function getNativeAttributesByProduct($idProduct, $idLang)
  {
    $sql = 'SELECT DISTINCT
        a.id_attribute,
        a.id_attribute_group,
        COALESCE(agl.name, "") AS group_name,
        COALESCE(al.name, "") AS attribute_name
      FROM `' . $this->dbPrefix . 'product_attribute` pa
      INNER JOIN `' . $this->dbPrefix . 'product_attribute_combination` pac
        ON (pac.id_product_attribute = pa.id_product_attribute)
      INNER JOIN `' . $this->dbPrefix . 'attribute` a
        ON (a.id_attribute = pac.id_attribute)
      LEFT JOIN `' . $this->dbPrefix . 'attribute_lang` al
        ON (al.id_attribute = a.id_attribute AND al.id_lang = :id_lang)
      LEFT JOIN `' . $this->dbPrefix . 'attribute_group_lang` agl
        ON (agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = :id_lang)
      WHERE pa.id_product = :id_product
      ORDER BY agl.name ASC, a.position ASC, al.name ASC, a.id_attribute ASC';

    return $this->connection->fetchAllAssociative($sql, [
      'id_product' => (int) $idProduct,
      'id_lang' => (int) $idLang,
    ]);
  }

  public function ensureSchema()
  {
    $cartSnapshotTable = $this->dbPrefix . 'pz_productconfigurator_cart_snapshot';
    if (!$this->tableExists($cartSnapshotTable)) {
      return true;
    }

    if (!$this->columnExists($cartSnapshotTable, 'id_customization')) {
      $this->connection->executeStatement(
        'ALTER TABLE `' . $cartSnapshotTable . '`
        ADD COLUMN `id_customization` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `id_product_attribute`'
      );
    }

    if (!$this->indexExists($cartSnapshotTable, 'idx_cart_customization')) {
      $this->connection->executeStatement(
        'ALTER TABLE `' . $cartSnapshotTable . '`
        ADD INDEX `idx_cart_customization` (`id_cart`, `id_customization`)'
      );
    }

    return true;
  }

  public function saveCartSnapshot($idCart, $idProduct, $idProductAttribute, $idCustomization, $configurationHash, $configurationJson, $priceImpactTaxExcl)
  {
    $idCart = (int) $idCart;
    $idProduct = (int) $idProduct;
    $idProductAttribute = (int) $idProductAttribute;
    $idCustomization = max(0, (int) $idCustomization);
    $configurationHash = trim((string) $configurationHash);
    $configurationJson = trim((string) $configurationJson);
    $priceImpactTaxExcl = round((float) $priceImpactTaxExcl, 6);

    if ($idCart <= 0 || $idProduct <= 0 || $configurationHash === '') {
      return false;
    }

    $this->connection->executeStatement(
      'INSERT INTO `' . $this->dbPrefix . 'pz_productconfigurator_cart_snapshot`
        (id_cart, id_product, id_product_attribute, id_customization, configuration_hash, configuration_json, price_impact_tax_excl, date_add, date_upd)
      VALUES
        (:id_cart, :id_product, :id_product_attribute, :id_customization, :configuration_hash, :configuration_json, :price_impact_tax_excl, NOW(), NOW())
      ON DUPLICATE KEY UPDATE
        id_customization = VALUES(id_customization),
        configuration_json = VALUES(configuration_json),
        price_impact_tax_excl = VALUES(price_impact_tax_excl),
        date_upd = NOW()',
      [
        'id_cart' => $idCart,
        'id_product' => $idProduct,
        'id_product_attribute' => $idProductAttribute,
        'id_customization' => $idCustomization,
        'configuration_hash' => $configurationHash,
        'configuration_json' => $configurationJson !== '' ? $configurationJson : null,
        'price_impact_tax_excl' => $priceImpactTaxExcl,
      ]
    );

    return true;
  }

  public function getCartSnapshotsByCartId($idCart)
  {
    return $this->connection->fetchAllAssociative(
      'SELECT id_snapshot, id_cart, id_product, id_product_attribute, id_customization, configuration_hash, configuration_json, price_impact_tax_excl, date_add, date_upd
      FROM `' . $this->dbPrefix . 'pz_productconfigurator_cart_snapshot`
      WHERE id_cart = :id_cart
      ORDER BY id_snapshot ASC',
      [
        'id_cart' => (int) $idCart,
      ]
    );
  }

  public function getCartSnapshotByCustomization($idCart, $idCustomization)
  {
    $idCart = (int) $idCart;
    $idCustomization = (int) $idCustomization;

    if ($idCart <= 0 || $idCustomization <= 0) {
      return [];
    }

    return $this->connection->fetchAssociative(
      'SELECT id_snapshot, id_cart, id_product, id_product_attribute, id_customization, configuration_hash, configuration_json, price_impact_tax_excl, date_add, date_upd
      FROM `' . $this->dbPrefix . 'pz_productconfigurator_cart_snapshot`
      WHERE id_cart = :id_cart AND id_customization = :id_customization
      ORDER BY id_snapshot DESC',
      [
        'id_cart' => $idCart,
        'id_customization' => $idCustomization,
      ]
    ) ?: [];
  }

  public function saveOrderSnapshot($idOrder, $idOrderDetail, $idProduct, $idProductAttribute, $configurationHash, $configurationJson, $priceImpactTaxExcl)
  {
    $idOrder = (int) $idOrder;
    $idOrderDetail = max(0, (int) $idOrderDetail);
    $idProduct = (int) $idProduct;
    $idProductAttribute = (int) $idProductAttribute;
    $configurationHash = trim((string) $configurationHash);
    $configurationJson = trim((string) $configurationJson);
    $priceImpactTaxExcl = round((float) $priceImpactTaxExcl, 6);

    if ($idOrder <= 0 || $idProduct <= 0 || $configurationHash === '') {
      return false;
    }

    $exists = (int) $this->connection->fetchOne(
      'SELECT COUNT(*)
      FROM `' . $this->dbPrefix . 'pz_productconfigurator_order_snapshot`
      WHERE id_order = :id_order
        AND id_order_detail = :id_order_detail
        AND id_product = :id_product
        AND id_product_attribute = :id_product_attribute
        AND configuration_hash = :configuration_hash',
      [
        'id_order' => $idOrder,
        'id_order_detail' => $idOrderDetail,
        'id_product' => $idProduct,
        'id_product_attribute' => $idProductAttribute,
        'configuration_hash' => $configurationHash,
      ]
    );

    if ($exists > 0) {
      return true;
    }

    $this->connection->executeStatement(
      'INSERT INTO `' . $this->dbPrefix . 'pz_productconfigurator_order_snapshot`
        (id_order, id_order_detail, id_product, id_product_attribute, configuration_hash, configuration_json, price_impact_tax_excl, date_add)
      VALUES
        (:id_order, :id_order_detail, :id_product, :id_product_attribute, :configuration_hash, :configuration_json, :price_impact_tax_excl, NOW())',
      [
        'id_order' => $idOrder,
        'id_order_detail' => $idOrderDetail,
        'id_product' => $idProduct,
        'id_product_attribute' => $idProductAttribute,
        'configuration_hash' => $configurationHash,
        'configuration_json' => $configurationJson !== '' ? $configurationJson : null,
        'price_impact_tax_excl' => $priceImpactTaxExcl,
      ]
    );

    return true;
  }

  private function tableExists($tableName)
  {
    $count = (int) $this->connection->fetchOne(
      'SELECT COUNT(*)
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :table_name',
      [
        'table_name' => (string) $tableName,
      ]
    );

    return $count > 0;
  }

  private function columnExists($tableName, $columnName)
  {
    $count = (int) $this->connection->fetchOne(
      'SELECT COUNT(*)
      FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :table_name
        AND COLUMN_NAME = :column_name',
      [
        'table_name' => (string) $tableName,
        'column_name' => (string) $columnName,
      ]
    );

    return $count > 0;
  }

  private function indexExists($tableName, $indexName)
  {
    $count = (int) $this->connection->fetchOne(
      'SELECT COUNT(*)
      FROM information_schema.STATISTICS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :table_name
        AND INDEX_NAME = :index_name',
      [
        'table_name' => (string) $tableName,
        'index_name' => (string) $indexName,
      ]
    );

    return $count > 0;
  }
}

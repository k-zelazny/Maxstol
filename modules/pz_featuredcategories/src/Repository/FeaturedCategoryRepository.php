<?php

namespace PrestaZone\FeaturedCategories\Repository;

use Doctrine\DBAL\Connection;

class FeaturedCategoryRepository
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

    $queries[] = 'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . 'pz_featuredcategories_item` (
      `id_item` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `date_add` DATETIME NOT NULL,
      `date_upd` DATETIME NOT NULL,
      PRIMARY KEY (`id_item`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    $queries[] = 'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . 'pz_featuredcategories_item_shop` (
      `id_item` INT UNSIGNED NOT NULL,
      `id_shop` INT UNSIGNED NOT NULL,
      `id_category` INT UNSIGNED NOT NULL,
      `media_type` VARCHAR(16) DEFAULT NULL,
      `media_path` VARCHAR(255) DEFAULT NULL,
      `active` TINYINT(1) NOT NULL DEFAULT 1,
      `position` INT UNSIGNED NOT NULL DEFAULT 0,
      PRIMARY KEY (`id_item`, `id_shop`),
      KEY `idx_shop_active_position` (`id_shop`, `active`, `position`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    $queries[] = 'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . 'pz_featuredcategories_item_lang` (
      `id_item` INT UNSIGNED NOT NULL,
      `id_shop` INT UNSIGNED NOT NULL,
      `id_lang` INT UNSIGNED NOT NULL,
      `title` VARCHAR(255) DEFAULT NULL,
      `short_description` TEXT DEFAULT NULL,
      PRIMARY KEY (`id_item`, `id_shop`, `id_lang`),
      KEY `idx_lang_shop` (`id_lang`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    foreach ($queries as $query) {
      $this->connection->executeStatement($query);
    }

    return true;
  }

  public function dropTables()
  {
    $queries = [];

    $queries[] = 'DROP TABLE IF EXISTS `' . $this->dbPrefix . 'pz_featuredcategories_item_lang`';
    $queries[] = 'DROP TABLE IF EXISTS `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`';
    $queries[] = 'DROP TABLE IF EXISTS `' . $this->dbPrefix . 'pz_featuredcategories_item`';

    foreach ($queries as $query) {
      $this->connection->executeStatement($query);
    }

    return true;
  }

  public function getCategories($idLang, $idShop)
  {
    $sql = 'SELECT c.id_category, cl.name
      FROM `' . $this->dbPrefix . 'category` c
      INNER JOIN `' . $this->dbPrefix . 'category_shop` cs
        ON (cs.id_category = c.id_category AND cs.id_shop = :id_shop)
      INNER JOIN `' . $this->dbPrefix . 'category_lang` cl
        ON (cl.id_category = c.id_category AND cl.id_lang = :id_lang AND cl.id_shop = :id_shop)
      WHERE c.id_category > 1
      ORDER BY cl.name ASC';

    return $this->connection->fetchAllAssociative($sql, [
      'id_shop' => (int) $idShop,
      'id_lang' => (int) $idLang,
    ]);
  }

  public function getItems($idShop, $idLang)
  {
    $sql = 'SELECT i.id_item, s.id_category, s.media_type, s.media_path, s.active, s.position,
        l.title, l.short_description, cl.name AS category_name
      FROM `' . $this->dbPrefix . 'pz_featuredcategories_item` i
      INNER JOIN `' . $this->dbPrefix . 'pz_featuredcategories_item_shop` s
        ON (s.id_item = i.id_item AND s.id_shop = :id_shop)
      LEFT JOIN `' . $this->dbPrefix . 'pz_featuredcategories_item_lang` l
        ON (l.id_item = i.id_item AND l.id_shop = s.id_shop AND l.id_lang = :id_lang)
      LEFT JOIN `' . $this->dbPrefix . 'category_lang` cl
        ON (cl.id_category = s.id_category AND cl.id_lang = :id_lang AND cl.id_shop = s.id_shop)
      ORDER BY s.position ASC, i.id_item ASC';

    return $this->connection->fetchAllAssociative($sql, [
      'id_shop' => (int) $idShop,
      'id_lang' => (int) $idLang,
    ]);
  }

  public function getItemForEdit($itemId, $idShop)
  {
    $itemId = (int) $itemId;
    $idShop = (int) $idShop;

    $sql = 'SELECT i.id_item, s.id_category, s.media_type, s.media_path, s.active, s.position
      FROM `' . $this->dbPrefix . 'pz_featuredcategories_item` i
      INNER JOIN `' . $this->dbPrefix . 'pz_featuredcategories_item_shop` s
        ON (s.id_item = i.id_item AND s.id_shop = :id_shop)
      WHERE i.id_item = :id_item';

    $item = $this->connection->fetchAssociative($sql, [
      'id_shop' => $idShop,
      'id_item' => $itemId,
    ]);

    if (!$item) {
      return null;
    }

    $translationsSql = 'SELECT id_lang, title, short_description
      FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_lang`
      WHERE id_item = :id_item AND id_shop = :id_shop';

    $translationsRows = $this->connection->fetchAllAssociative($translationsSql, [
      'id_item' => $itemId,
      'id_shop' => $idShop,
    ]);

    $translations = [];
    foreach ($translationsRows as $row) {
      $translations[(int) $row['id_lang']] = [
        'title' => $row['title'],
        'short_description' => $row['short_description'],
      ];
    }

    $item['translations'] = $translations;

    return $item;
  }

  public function saveItem($itemId, array $shopIds, $idCategory, $mediaType, $mediaPath, array $translations, $active)
  {
    $idCategory = (int) $idCategory;
    $active = (int) (bool) $active;

    if ($itemId) {
      $itemId = (int) $itemId;
      $this->connection->executeStatement(
        'UPDATE `' . $this->dbPrefix . 'pz_featuredcategories_item` SET date_upd = NOW() WHERE id_item = :id_item',
        ['id_item' => $itemId]
      );
    } else {
      $this->connection->executeStatement(
        'INSERT INTO `' . $this->dbPrefix . 'pz_featuredcategories_item` (date_add, date_upd) VALUES (NOW(), NOW())'
      );
      $itemId = (int) $this->connection->lastInsertId();
    }

    foreach ($shopIds as $shopId) {
      $shopId = (int) $shopId;

      $exists = (int) $this->connection->fetchOne(
        'SELECT COUNT(*)
        FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
        WHERE id_item = :id_item AND id_shop = :id_shop',
        [
          'id_item' => $itemId,
          'id_shop' => $shopId,
        ]
      );

      if ($exists > 0) {
        $this->connection->executeStatement(
          'UPDATE `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
          SET id_category = :id_category,
            media_type = :media_type,
            media_path = :media_path,
            active = :active
          WHERE id_item = :id_item AND id_shop = :id_shop',
          [
            'id_category' => $idCategory,
            'media_type' => $mediaType,
            'media_path' => $mediaPath,
            'active' => $active,
            'id_item' => $itemId,
            'id_shop' => $shopId,
          ]
        );
      } else {
        $position = (int) $this->connection->fetchOne(
          'SELECT COALESCE(MAX(position), 0) + 1
          FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
          WHERE id_shop = :id_shop',
          ['id_shop' => $shopId]
        );

        $this->connection->executeStatement(
          'INSERT INTO `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
          (id_item, id_shop, id_category, media_type, media_path, active, position)
          VALUES (:id_item, :id_shop, :id_category, :media_type, :media_path, :active, :position)',
          [
            'id_item' => $itemId,
            'id_shop' => $shopId,
            'id_category' => $idCategory,
            'media_type' => $mediaType,
            'media_path' => $mediaPath,
            'active' => $active,
            'position' => $position,
          ]
        );
      }

      $this->connection->executeStatement(
        'DELETE FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_lang`
        WHERE id_item = :id_item AND id_shop = :id_shop',
        [
          'id_item' => $itemId,
          'id_shop' => $shopId,
        ]
      );

      foreach ($translations as $idLang => $translation) {
        $this->connection->executeStatement(
          'INSERT INTO `' . $this->dbPrefix . 'pz_featuredcategories_item_lang`
          (id_item, id_shop, id_lang, title, short_description)
          VALUES (:id_item, :id_shop, :id_lang, :title, :short_description)',
          [
            'id_item' => $itemId,
            'id_shop' => $shopId,
            'id_lang' => (int) $idLang,
            'title' => $translation['title'],
            'short_description' => $translation['short_description'],
          ]
        );
      }
    }

    return $itemId;
  }

  public function deleteItem($itemId, array $shopIds)
  {
    $itemId = (int) $itemId;
    if ($itemId <= 0 || empty($shopIds)) {
      return;
    }

    $shopIdsSql = implode(',', array_map('intval', $shopIds));

    $this->connection->executeStatement(
      'DELETE FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_lang`
      WHERE id_item = ' . $itemId . ' AND id_shop IN (' . $shopIdsSql . ')'
    );

    $this->connection->executeStatement(
      'DELETE FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
      WHERE id_item = ' . $itemId . ' AND id_shop IN (' . $shopIdsSql . ')'
    );

    $remaining = (int) $this->connection->fetchOne(
      'SELECT COUNT(*) FROM `' . $this->dbPrefix . 'pz_featuredcategories_item_shop` WHERE id_item = :id_item',
      ['id_item' => $itemId]
    );

    if ($remaining === 0) {
      $this->connection->executeStatement(
        'DELETE FROM `' . $this->dbPrefix . 'pz_featuredcategories_item` WHERE id_item = :id_item',
        ['id_item' => $itemId]
      );
    }
  }

  public function setActive($itemId, array $shopIds, $active)
  {
    $itemId = (int) $itemId;
    $active = (int) (bool) $active;

    if ($itemId <= 0 || empty($shopIds)) {
      return;
    }

    $shopIdsSql = implode(',', array_map('intval', $shopIds));

    $this->connection->executeStatement(
      'UPDATE `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
      SET active = ' . $active . '
      WHERE id_item = ' . $itemId . ' AND id_shop IN (' . $shopIdsSql . ')'
    );
  }

  public function updatePositions(array $orderedItemIds, array $shopIds)
  {
    if (empty($orderedItemIds) || empty($shopIds)) {
      return;
    }

    foreach ($shopIds as $shopId) {
      $shopId = (int) $shopId;
      $position = 1;

      foreach ($orderedItemIds as $itemId) {
        $this->connection->executeStatement(
          'UPDATE `' . $this->dbPrefix . 'pz_featuredcategories_item_shop`
          SET position = :position
          WHERE id_item = :id_item AND id_shop = :id_shop',
          [
            'position' => $position,
            'id_item' => (int) $itemId,
            'id_shop' => $shopId,
          ]
        );

        ++$position;
      }
    }
  }
}

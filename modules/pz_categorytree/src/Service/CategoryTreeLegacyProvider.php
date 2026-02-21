<?php
/**
 * 2026 PrestaZone
 *
 * NOTICE OF LICENSE
 *
 * This code is proprietary and paid. Distribution, copying, or modification
 * of the code without prior written consent from PrestaZone is strictly prohibited.
 *
 * LICENSE
 *
 * This module is licensed for use on a single domain only. Using the module on
 * more than one domain requires purchasing additional licenses. Details are
 * available in the module documentation or by contacting PrestaZone.
 *
 * DISCLAIMER
 *
 * PrestaZone is not liable for any damages resulting from the improper use
 * of this module.
 *
 * @author    PrestaZone <modules@prestazone.pl>
 * @copyright 2026 PrestaZone
 * @license   Paid license for a single domain, contact: <modules@prestazone.pl>
 */

namespace PrestaZone\CategoryTree\Service;

use Context;

final class CategoryTreeLegacyProvider
{
  /**
   * @return array<int, array<string, mixed>>
   */
  public function getMainCategoriesWithSubcategories(Context $context): array
  {
    $langId = (int) $context->language->id;
    $homeCategoryId = (int) \Configuration::get('PS_HOME_CATEGORY');
    
    if ($langId <= 0 || $homeCategoryId <= 0) {
      return [];
    }
    
    $tree = \Category::getNestedCategories($homeCategoryId, $langId, true, null, true);
    $mainCategoryNodes = $this->extractMainCategoryNodes($tree);
    
    if (empty($mainCategoryNodes) && $homeCategoryId !== 1) {
      $rootTree = \Category::getNestedCategories(1, $langId, true, null, true);
      $rootChildren = $this->extractMainCategoryNodes($rootTree);
      
      foreach ($rootChildren as $rootChild) {
        if ((int) ($rootChild['id_category'] ?? 0) !== $homeCategoryId) {
          continue;
        }
        
        $mainCategoryNodes = isset($rootChild['children']) && is_array($rootChild['children'])
          ? $rootChild['children']
          : [];
        break;
      }
    }
    
    if (empty($mainCategoryNodes)) {
      return [];
    }
    
    $subCategoryIds = [];
    foreach ($mainCategoryNodes as $mainCategoryNode) {
      $subCategoryNodes = isset($mainCategoryNode['children']) && is_array($mainCategoryNode['children'])
        ? $mainCategoryNode['children']
        : [];
      
      foreach ($subCategoryNodes as $subCategoryNode) {
        $subCategoryId = (int) ($subCategoryNode['id_category'] ?? 0);
        if ($subCategoryId > 0) {
          $subCategoryIds[] = $subCategoryId;
        }
      }
    }
    
    $productCounts = $this->fetchProductCountsByCategory($subCategoryIds);
    
    $mainCategories = [];
    foreach ($mainCategoryNodes as $mainCategoryNode) {
      $mainCategoryId = (int) ($mainCategoryNode['id_category'] ?? 0);
      if ($mainCategoryId <= 0) {
        continue;
      }
      
      $subcategories = [];
      $subCategoryNodes = isset($mainCategoryNode['children']) && is_array($mainCategoryNode['children'])
        ? $mainCategoryNode['children']
        : [];
      
      foreach ($subCategoryNodes as $subCategoryNode) {
        $subCategoryId = (int) ($subCategoryNode['id_category'] ?? 0);
        if ($subCategoryId <= 0) {
          continue;
        }
        
        $subcategories[] = [
          'id_category' => $subCategoryId,
          'name' => (string) ($subCategoryNode['name'] ?? ''),
          'link' => $context->link->getCategoryLink(
            $subCategoryId,
            (string) ($subCategoryNode['link_rewrite'] ?? '')
          ),
          'product_count' => (int) ($productCounts[$subCategoryId] ?? 0),
        ];
      }
      
      $mainCategories[] = [
        'id_category' => $mainCategoryId,
        'name' => (string) ($mainCategoryNode['name'] ?? ''),
        'subcategories' => $subcategories,
      ];
    }
    
    return $mainCategories;
  }

  /**
   * @param array<int> $categoryIds
   *
   * @return array<int, int>
   */
  private function fetchProductCountsByCategory(array $categoryIds): array
  {
    $categoryIdsSql = $this->buildIntList($categoryIds);
    if ('0' === $categoryIdsSql) {
      return [];
    }
    
    $sql = sprintf(
      'SELECT c_root.id_category, COUNT(DISTINCT cp.id_product) AS product_count
       FROM %1$scategory c_root
       LEFT JOIN %1$scategory c_leaf
         ON (c_leaf.nleft >= c_root.nleft AND c_leaf.nright <= c_root.nright)
       LEFT JOIN %1$scategory_product cp
         ON (cp.id_category = c_leaf.id_category)
       WHERE c_root.id_category IN (%2$s)
       GROUP BY c_root.id_category',
      _DB_PREFIX_,
      $categoryIdsSql
    );
    
    $rows = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    if (!is_array($rows)) {
      return [];
    }
    
    $counts = [];
    foreach ($rows as $row) {
      $counts[(int) $row['id_category']] = (int) $row['product_count'];
    }
    
    return $counts;
  }

  /**
   * @param mixed $tree
   *
   * @return array<int, array<string, mixed>>
   */
  private function extractMainCategoryNodes($tree): array
  {
    if (!is_array($tree) || empty($tree)) {
      return [];
    }
    
    $homeNode = reset($tree);
    if (!is_array($homeNode)) {
      return [];
    }
    
    return isset($homeNode['children']) && is_array($homeNode['children'])
      ? $homeNode['children']
      : [];
  }

  /**
   * @param array<int> $values
   */
  private function buildIntList(array $values): string
  {
    $sanitized = array_values(array_unique(array_map('intval', $values)));
    $sanitized = array_filter($sanitized, static function ($value) {
      return $value > 0;
    });
    
    if (empty($sanitized)) {
      return '0';
    }
    
    return implode(',', $sanitized);
  }
}
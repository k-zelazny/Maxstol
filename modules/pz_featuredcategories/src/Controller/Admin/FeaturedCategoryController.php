<?php

namespace PrestaZone\FeaturedCategories\Controller\Admin;

use PrestaZone\FeaturedCategories\Repository\FeaturedCategoryRepository;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeaturedCategoryController extends PrestaShopAdminController
{
  #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function indexAction(FeaturedCategoryRepository $repository): Response
  {
    $shopIds = $this->getContextShopIds();
    $contextShopId = (int) reset($shopIds);
    $context = \Context::getContext();
    $idLang = (int) $context->language->id;

    return $this->render('@Modules/pz_featuredcategories/views/templates/admin/featured_categories/list.html.twig', [
      'items' => $repository->getItems($contextShopId, $idLang),
      'shopIds' => $shopIds,
      'contextShopId' => $contextShopId,
      'deleteRoute' => 'admin_pz_featured_categories_delete',
      'toggleRoute' => 'admin_pz_featured_categories_toggle',
      'reorderUrl' => $this->generateUrl('admin_pz_featured_categories_reorder'),
      'createUrl' => $this->generateUrl('admin_pz_featured_categories_create'),
      'enableSidebar' => true,
      'showContentHeader' => true,
    ]);
  }

  #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) or is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function createAction(FeaturedCategoryRepository $repository): Response
  {
    return $this->renderFormPage($repository, null);
  }

  #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function editAction(int $itemId, FeaturedCategoryRepository $repository): Response
  {
    $item = $repository->getItemForEdit($itemId, (int) reset($this->getContextShopIds()));

    if (!$item) {
      $this->addFlash('error', $this->trans('Item not found.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return $this->redirectToRoute('admin_pz_featured_categories_index');
    }

    return $this->renderFormPage($repository, $item);
  }

  #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) or is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function saveAction(Request $request, FeaturedCategoryRepository $repository): RedirectResponse
  {
    $shopIds = $this->getContextShopIds();

    $itemId = (int) $request->request->get('item_id', 0);
    $idCategory = (int) $request->request->get('id_category', 0);
    $mediaType = $this->normalizeMediaType((string) $request->request->get('media_type', 'image'));
    $active = (bool) $request->request->get('active', false);
    $existingMediaPath = trim((string) $request->request->get('existing_media_path', ''));

    if ($idCategory <= 0) {
      $this->addFlash('error', $this->trans('Category is required.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return $this->redirectToFormRoute($itemId);
    }

    $mediaPath = $existingMediaPath !== '' ? $existingMediaPath : null;
    $uploadedFile = $request->files->get('media_file');

    if ($uploadedFile instanceof UploadedFile) {
      $mediaPath = $this->handleMediaUpload($uploadedFile, $mediaType);
      if ($mediaPath === null) {
        return $this->redirectToFormRoute($itemId);
      }
    }

    $translations = $this->buildTranslations($request);

    try {
      $savedItemId = $repository->saveItem(
        $itemId > 0 ? $itemId : null,
        $shopIds,
        $idCategory,
        $mediaType,
        $mediaPath,
        $translations,
        $active
      );

      $this->addFlash('success', $this->trans('Item saved successfully.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return $this->redirectToRoute('admin_pz_featured_categories_edit', [
        'itemId' => (int) $savedItemId,
      ]);
    } catch (\Throwable $e) {
      $this->addFlash('error', $this->trans('Failed to save item.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return $this->redirectToFormRoute($itemId);
    }
  }

  #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function deleteAction($itemId, FeaturedCategoryRepository $repository): RedirectResponse
  {
    try {
      $repository->deleteItem((int) $itemId, $this->getContextShopIds());
      $this->addFlash('success', $this->trans('Item deleted successfully.', [], 'Modules.Pzfeaturedcategories.Admin'));
    } catch (\Throwable $e) {
      $this->addFlash('error', $this->trans('Failed to delete item.', [], 'Modules.Pzfeaturedcategories.Admin'));
    }

    return $this->redirectToRoute('admin_pz_featured_categories_index');
  }

  #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function toggleAction(Request $request, $itemId, FeaturedCategoryRepository $repository): JsonResponse
  {
    $payload = json_decode((string) $request->getContent(), true);
    $active = isset($payload['active']) ? (bool) $payload['active'] : false;

    try {
      $repository->setActive((int) $itemId, $this->getContextShopIds(), $active);

      return new JsonResponse([
        'success' => true,
        'active' => $active,
      ]);
    } catch (\Throwable $e) {
      return new JsonResponse([
        'success' => false,
        'message' => $this->trans('Failed to update status.', [], 'Modules.Pzfeaturedcategories.Admin'),
      ], 400);
    }
  }

  #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_homepage')]
  public function reorderAction(Request $request, FeaturedCategoryRepository $repository): JsonResponse
  {
    $payload = json_decode((string) $request->getContent(), true);
    $orderedIds = isset($payload['orderedIds']) && is_array($payload['orderedIds']) ? $payload['orderedIds'] : [];

    $orderedIds = array_values(array_filter(array_map('intval', $orderedIds)));

    if (empty($orderedIds)) {
      return new JsonResponse([
        'success' => false,
        'message' => $this->trans('No order data provided.', [], 'Modules.Pzfeaturedcategories.Admin'),
      ], 400);
    }

    try {
      $repository->updatePositions($orderedIds, $this->getContextShopIds());

      return new JsonResponse(['success' => true]);
    } catch (\Throwable $e) {
      return new JsonResponse([
        'success' => false,
        'message' => $this->trans('Failed to reorder items.', [], 'Modules.Pzfeaturedcategories.Admin'),
      ], 400);
    }
  }

  private function renderFormPage(FeaturedCategoryRepository $repository, ?array $editItem): Response
  {
    $shopIds = $this->getContextShopIds();
    $contextShopId = (int) reset($shopIds);
    $context = \Context::getContext();
    $idLang = (int) $context->language->id;

    return $this->render('@Modules/pz_featuredcategories/views/templates/admin/featured_categories/form.html.twig', [
      'categories' => $repository->getCategories($idLang, $contextShopId),
      'languages' => \Language::getLanguages(true, $contextShopId),
      'defaultLangId' => (int) \Configuration::get('PS_LANG_DEFAULT'),
      'shopIds' => $shopIds,
      'contextShopId' => $contextShopId,
      'editItem' => $editItem,
      'saveUrl' => $this->generateUrl('admin_pz_featured_categories_save'),
      'listUrl' => $this->generateUrl('admin_pz_featured_categories_index'),
      'enableSidebar' => true,
      'showContentHeader' => true,
    ]);
  }

  private function redirectToFormRoute(int $itemId): RedirectResponse
  {
    if ($itemId > 0) {
      return $this->redirectToRoute('admin_pz_featured_categories_edit', ['itemId' => $itemId]);
    }

    return $this->redirectToRoute('admin_pz_featured_categories_create');
  }

  private function getContextShopIds()
  {
    $shopIds = \Shop::getContextListShopID();

    if (!is_array($shopIds) || empty($shopIds)) {
      $context = \Context::getContext();
      $shopIds = [(int) $context->shop->id];
    }

    return array_values(array_unique(array_map('intval', $shopIds)));
  }

  private function buildTranslations(Request $request)
  {
    $translations = [];

    foreach (\Language::getLanguages(true) as $language) {
      $idLang = (int) $language['id_lang'];
      $title = trim((string) $request->request->get('title_' . $idLang, ''));
      $shortDescription = trim((string) $request->request->get('short_description_' . $idLang, ''));

      if (mb_strlen($title) > 255) {
        $title = mb_substr($title, 0, 255);
      }

      $translations[$idLang] = [
        'title' => $title !== '' ? $title : null,
        'short_description' => $shortDescription !== '' ? $shortDescription : null,
      ];
    }

    return $translations;
  }

  private function normalizeMediaType($mediaType)
  {
    return in_array($mediaType, ['image', 'video'], true) ? $mediaType : 'image';
  }

  private function handleMediaUpload(UploadedFile $uploadedFile, $mediaType)
  {
    $allowedMimeTypes = [
      'image' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
      'video' => ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'],
    ];

    $mimeType = (string) $uploadedFile->getMimeType();
    if (!in_array($mimeType, $allowedMimeTypes[$mediaType], true)) {
      $this->addFlash('error', $this->trans('Invalid media type for selected format.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return null;
    }

    $targetDir = _PS_MODULE_DIR_ . 'pz_featuredcategories/views/media';
    if (!is_dir($targetDir) && !@mkdir($targetDir, 0755, true)) {
      $this->addFlash('error', $this->trans('Cannot create media directory.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return null;
    }

    $extension = strtolower((string) $uploadedFile->guessExtension());
    if ($extension === '') {
      $extension = strtolower(pathinfo((string) $uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION));
    }

    if ($extension === '') {
      $extension = $mediaType === 'video' ? 'mp4' : 'jpg';
    }

    try {
      $fileName = sprintf('%s_%s.%s', $mediaType, bin2hex(random_bytes(10)), $extension);
      $uploadedFile->move($targetDir, $fileName);

      return 'modules/pz_featuredcategories/views/media/' . $fileName;
    } catch (\Throwable $e) {
      $this->addFlash('error', $this->trans('Failed to upload file.', [], 'Modules.Pzfeaturedcategories.Admin'));

      return null;
    }
  }
}

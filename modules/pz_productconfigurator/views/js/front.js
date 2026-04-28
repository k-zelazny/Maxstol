(function () {
  if (typeof window !== 'undefined' && window.__pzProductConfiguratorFrontLoaded === true) {
    return;
  }

  if (typeof window !== 'undefined') {
    window.__pzProductConfiguratorFrontLoaded = true;
  }

  let cachedSelections = {};
  let submitterTrackingBound = false;
  let floatingTooltipElement = null;
  let floatingTooltipAnchor = null;
  let nativeImpactMapCache = null;
  const DEPENDENCY_NONE_OPTION = '__none__';

  const destroyFloatingTooltip = () => {
    if (floatingTooltipElement && floatingTooltipElement.parentNode) {
      floatingTooltipElement.parentNode.removeChild(floatingTooltipElement);
    }

    floatingTooltipElement = null;
    floatingTooltipAnchor = null;
  };

  const positionFloatingTooltip = () => {
    if (!(floatingTooltipElement instanceof HTMLElement) || !(floatingTooltipAnchor instanceof HTMLElement)) {
      return;
    }

    const rect = floatingTooltipAnchor.getBoundingClientRect();
    const spacing = 10;
    const maxWidth = Math.min(320, window.innerWidth - 24);
    floatingTooltipElement.style.maxWidth = `${Math.max(180, maxWidth)}px`;

    const tooltipRect = floatingTooltipElement.getBoundingClientRect();
    let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
    left = Math.max(8, Math.min(left, window.innerWidth - tooltipRect.width - 8));

    let top = rect.top - tooltipRect.height - spacing;
    if (top < 8) {
      top = rect.bottom + spacing;
      floatingTooltipElement.classList.add('pzpc-floating-tooltip--bottom');
    } else {
      floatingTooltipElement.classList.remove('pzpc-floating-tooltip--bottom');
    }

    floatingTooltipElement.style.left = `${Math.round(left)}px`;
    floatingTooltipElement.style.top = `${Math.round(top)}px`;
  };

  const showFloatingTooltip = (anchor) => {
    if (!(anchor instanceof HTMLElement)) {
      return;
    }

    const message = String(anchor.getAttribute('data-tooltip') || anchor.getAttribute('title') || '').trim();
    if (message === '') {
      destroyFloatingTooltip();
      return;
    }

    if (!floatingTooltipElement) {
      floatingTooltipElement = document.createElement('div');
      floatingTooltipElement.className = 'pzpc-floating-tooltip';
      document.body.appendChild(floatingTooltipElement);
    }

    floatingTooltipAnchor = anchor;
    floatingTooltipElement.textContent = message;
    floatingTooltipElement.classList.add('is-visible');
    positionFloatingTooltip();
  };

  const bindInfoTooltip = (info) => {
    if (!(info instanceof HTMLElement) || info.dataset.pzInfoBound === '1') {
      return;
    }

    const openTooltip = () => {
      showFloatingTooltip(info);
    };

    const closeTooltip = () => {
      if (floatingTooltipAnchor === info) {
        destroyFloatingTooltip();
      }
    };

    info.addEventListener('mouseenter', openTooltip);
    info.addEventListener('mouseleave', closeTooltip);
    info.addEventListener('focus', openTooltip);
    info.addEventListener('blur', closeTooltip);
    info.addEventListener('click', (event) => {
      event.stopPropagation();
      if (floatingTooltipAnchor === info) {
        destroyFloatingTooltip();
      } else {
        openTooltip();
      }
    });

    info.dataset.pzInfoBound = '1';
  };

  const formatMoney = (value) => {
    const parsed = Number(value);
    if (!Number.isFinite(parsed) || Math.abs(parsed) < 0.000001) {
      return '0 zł';
    }

    return new Intl.NumberFormat('pl-PL', {
      minimumFractionDigits: Number.isInteger(parsed) ? 0 : 2,
      maximumFractionDigits: 2,
    }).format(parsed) + ' zł';
  };

  const resolveSyncErrorMessage = (root) => {
    if (!root) {
      return '';
    }

    return String(root.dataset.syncErrorMessage || '').trim();
  };

  const resolveChooseOptionLabel = (group) => {
    if (!group) {
      return '';
    }

    const root = group.closest('.js-pz-productconfigurator');
    if (!root) {
      return '';
    }

    return String(root.dataset.chooseOptionLabel || '').trim();
  };

  const getAllConfigGroups = (root) => {
    if (!root) {
      return [];
    }

    return Array.from(root.querySelectorAll('.config-group[data-group-code]'));
  };

  const getGroups = (root) => getAllConfigGroups(root).filter((group) => !group.hidden);

  const getGroupOptions = (group) => {
    if (!group) {
      return [];
    }

    return Array.from(group.querySelectorAll('.js-pz-config-option'));
  };

  const getOptionInput = (option) => {
    if (!option) {
      return null;
    }

    return option.querySelector('.js-pz-config-input');
  };

  const isOptionSelected = (option) => {
    if (!option) {
      return false;
    }

    const input = getOptionInput(option);
    if (input) {
      return !!input.checked;
    }

    return option.dataset.selected === '1';
  };

  const getSelectedOptions = (group) => getGroupOptions(group).filter((option) => isOptionSelected(option));

  const setOptionSelected = (option, selected) => {
    if (!option) {
      return;
    }

    const isSelected = !!selected;
    const input = getOptionInput(option);
    if (input && input.checked !== isSelected) {
      input.checked = isSelected;
    }

    option.dataset.selected = isSelected ? '1' : '0';
    option.classList.toggle('is-selected', isSelected);
    option.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
  };

  const clearGroupSelection = (group) => {
    if (!(group instanceof HTMLElement)) {
      return;
    }

    const options = getGroupOptions(group);
    const selectionMode = group.dataset.selection === 'multiple' ? 'multiple' : 'single';

    if (selectionMode === 'multiple') {
      options.forEach((option) => {
        setOptionSelected(option, false);
      });

      return;
    }

    const noneOption = options.find((option) => String(option.dataset.optionCode || '') === '') || null;
    options.forEach((option) => {
      setOptionSelected(option, option === noneOption);
    });
  };

  const getSelectedOptionCodes = (group) => getSelectedOptions(group)
    .map((option) => String(option.dataset.optionCode || '').trim())
    .filter((code) => code !== '');

  const isGroupDependencyMatched = (group, allGroups) => {
    if (!(group instanceof HTMLElement)) {
      return true;
    }

    const sourceGroupCode = String(group.dataset.dependsOnGroup || '').trim();
    if (sourceGroupCode === '') {
      return true;
    }

    const sourceGroup = allGroups.find((item) => String(item.dataset.groupCode || '') === sourceGroupCode) || null;
    if (!(sourceGroup instanceof HTMLElement) || sourceGroup.hidden) {
      return false;
    }

    const requiredOption = String(group.dataset.dependsOnOption || '').trim();
    const sourceSelectedCodes = getSelectedOptionCodes(sourceGroup);

    if (requiredOption === DEPENDENCY_NONE_OPTION) {
      return sourceSelectedCodes.length === 0;
    }

    if (requiredOption === '') {
      return sourceSelectedCodes.length > 0;
    }

    return sourceSelectedCodes.includes(requiredOption);
  };

  const applyGroupDependencies = (root) => {
    const allGroups = getAllConfigGroups(root);
    if (allGroups.length === 0) {
      return;
    }

    let hasChanges = true;
    let guard = 0;
    const maxIterations = Math.max(2, allGroups.length + 1);

    while (hasChanges && guard < maxIterations) {
      hasChanges = false;
      guard += 1;

      allGroups.forEach((group) => {
        const shouldShow = isGroupDependencyMatched(group, allGroups);

        if (shouldShow) {
          if (group.hidden) {
            hasChanges = true;
            group.hidden = false;
          }

          group.dataset.pzDependencyActive = '1';
          return;
        }

        if (!group.hidden) {
          hasChanges = true;
        }

        clearGroupSelection(group);
        updateGroupState(group);
        group.hidden = true;
        group.dataset.pzDependencyActive = '0';
      });
    }

    getGroups(root).forEach((group) => {
      updateGroupState(group);
    });
  };

  const normalizeImageUrlFromStyle = (styleValue) => {
    const source = String(styleValue || '').trim();
    if (source === '') {
      return '';
    }

    const match = source.match(/^url\((['"]?)(.*)\1\)$/i);
    if (!match || !match[2]) {
      return '';
    }

    return String(match[2]).trim();
  };

  const getOptionSummaryThumbnail = (option) => {
    if (!(option instanceof HTMLElement)) {
      return null;
    }

    const previewImage = option.querySelector('.config-option__preview-image');
    if (previewImage instanceof HTMLImageElement) {
      const imageSrc = String(previewImage.getAttribute('src') || '').trim();
      if (imageSrc !== '') {
        return { type: 'image', value: imageSrc };
      }
    }

    const swatchTexture = option.querySelector('.config-option__swatch--texture');
    if (swatchTexture instanceof HTMLElement) {
      const texture = normalizeImageUrlFromStyle(swatchTexture.style.backgroundImage);
      if (texture !== '') {
        return { type: 'image', value: texture };
      }
    }

    const swatchColor = option.querySelector('.config-option__swatch');
    if (swatchColor instanceof HTMLElement) {
      const color = String(swatchColor.style.backgroundColor || '').trim();
      if (color !== '') {
        return { type: 'color', value: color };
      }
    }

    return null;
  };

  const setGroupSummary = (holder, label, impact, thumbnail = null) => {
    if (!(holder instanceof HTMLElement)) {
      return;
    }

    const safeLabel = String(label || '').trim();
    const safeImpact = String(impact || '').trim();

    if (safeLabel === '' && safeImpact === '') {
      holder.textContent = '';
      return;
    }

    holder.innerHTML = '';

    if (thumbnail && thumbnail.type && thumbnail.value) {
      const thumbNode = document.createElement('span');
      thumbNode.className = 'config-group__value-thumb';

      if (thumbnail.type === 'color') {
        thumbNode.style.backgroundColor = thumbnail.value;
      } else {
        thumbNode.style.backgroundImage = `url("${thumbnail.value}")`;
      }

      holder.appendChild(thumbNode);
    }

    const labelNode = document.createElement('span');
    labelNode.className = 'config-group__value-label';
    labelNode.textContent = safeLabel;
    holder.appendChild(labelNode);

    if (safeImpact !== '') {
      const impactNode = document.createElement('span');
      impactNode.className = 'config-group__value-impact';
      impactNode.textContent = safeImpact;
      holder.appendChild(impactNode);
    }
  };

  const initializeGroupFlow = (root) => {
    void root;
  };

  const moveToNextGroup = (root, currentGroup) => {
    void root;
    void currentGroup;
  };

  const applyCachedSelections = (root) => {
    if (!root || !cachedSelections || typeof cachedSelections !== 'object') {
      return;
    }

    Object.keys(cachedSelections).forEach((groupCode) => {
      const value = cachedSelections[groupCode];
      const group = root.querySelector(`.config-group[data-group-code="${groupCode}"]`);
      if (!group) {
        return;
      }

      getGroupOptions(group).forEach((option) => {
        const optionCode = option.dataset.optionCode || '';

        if (group.dataset.selection === 'multiple') {
          const values = Array.isArray(value) ? value : [];
          setOptionSelected(option, values.includes(optionCode));
          return;
        }

        setOptionSelected(option, String(value) === String(optionCode));
      });
    });
  };

  const collectSelections = (root) => {
    const result = {};

    getGroups(root).forEach((group) => {
      const groupCode = group.dataset.groupCode || '';
      if (groupCode === '') {
        return;
      }

      const selectedOptions = getSelectedOptions(group);

      if (group.dataset.selection === 'multiple') {
        result[groupCode] = selectedOptions
          .map((option) => option.dataset.optionCode || '')
          .filter((value) => value !== '');
        return;
      }

      result[groupCode] = selectedOptions.length > 0
        ? (selectedOptions[0].dataset.optionCode || '')
        : '';
    });

    return result;
  };

  const updateGroupState = (group) => {
    if (!group) {
      return;
    }

    const summary = group.querySelector('.js-config-selected');
    const selectedOptions = getSelectedOptions(group);

    getGroupOptions(group).forEach((option) => {
      const isSelected = isOptionSelected(option);
      option.classList.toggle('is-selected', isSelected);
      option.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
      option.dataset.selected = isSelected ? '1' : '0';
    });

    if (!summary) {
      return;
    }

    const labels = selectedOptions
      .map((option) => option.dataset.optionLabel || '')
      .map((label) => label.trim())
      .filter((label) => label !== '');

    if (labels.length > 0) {
      if (selectedOptions.length === 1) {
        const selectedOption = selectedOptions[0];
        const impactNode = selectedOption.querySelector('.js-option-impact');
        const impactText = impactNode ? String(impactNode.textContent || '').trim() : '';
        const thumbnail = getOptionSummaryThumbnail(selectedOption);
        setGroupSummary(summary, labels[0], impactText, thumbnail);
      } else {
        setGroupSummary(summary, labels.join(', '), '');
      }
      return;
    }

    const noneLabel = String(group.dataset.noneLabel || '').trim();
    const chooseOptionLabel = resolveChooseOptionLabel(group);
    setGroupSummary(summary, noneLabel !== '' ? noneLabel : chooseOptionLabel, '');
  };

  const updatePayload = (root) => {
    if (!root) {
      return;
    }

    const payloadInput = root.querySelector('.js-pz-productconfigurator-payload');
    const payloadTotalInput = root.querySelector('.js-pz-productconfigurator-total');
    const summaryValue = root.querySelector('.js-pz-productconfigurator-total-label');
    const selections = collectSelections(root);
    cachedSelections = selections;

    let total = 0;
    const items = [];

    getGroups(root).forEach((group) => {
      const groupCode = group.dataset.groupCode || '';
      const groupLabel = group.dataset.groupLabel || groupCode;

      getSelectedOptions(group).forEach((option) => {
        const optionCode = option.dataset.optionCode || '';
        const price = Number(option.dataset.optionPrice || 0);
        total += Number.isFinite(price) ? price : 0;

        if (optionCode === '') {
          return;
        }

        items.push({
          group: groupCode,
          group_label: groupLabel,
          option: optionCode,
          option_label: option.dataset.optionLabel || optionCode,
          price: Number.isFinite(price) ? price : 0,
          product_id: Number(option.dataset.productId || 0) || 0,
          attribute_id: Number(option.dataset.attributeId || 0) || 0,
        });
      });
    });

    const payload = {
      selections,
      items,
      total_price_impact: total,
    };

    if (payloadInput) {
      payloadInput.value = JSON.stringify(payload);
    }

    if (payloadTotalInput) {
      payloadTotalInput.value = String(total);
    }

    if (summaryValue) {
      summaryValue.textContent = total > 0 ? `+${formatMoney(total)}` : formatMoney(0);
    }
  };

  const resetSelections = (root) => {
    if (!root) {
      return;
    }

    getAllConfigGroups(root).forEach((group) => {
      const options = getGroupOptions(group);
      const defaults = options.filter((option) => option.dataset.isDefault === '1');
      const selectionMode = group.dataset.selection === 'multiple' ? 'multiple' : 'single';

      if (selectionMode === 'multiple') {
        options.forEach((option) => {
          setOptionSelected(option, defaults.includes(option));
        });
      } else {
        let selectedOption = defaults.length > 0 ? defaults[0] : null;

        if (!selectedOption) {
          selectedOption = options.find((option) => (option.dataset.optionCode || '') === '') || null;
        }

        if (!selectedOption) {
          selectedOption = options[0] || null;
        }

        options.forEach((option) => {
          setOptionSelected(option, option === selectedOption);
        });
      }

      updateGroupState(group);
    });

    applyGroupDependencies(root);
    updatePayload(root);
    initializeGroupFlow(root);
  };

  const handleOptionClick = (group, option, root) => {
    if (!group || !option || !root) {
      return;
    }

    const selection = group.dataset.selection === 'multiple' ? 'multiple' : 'single';
    const optionCode = option.dataset.optionCode || '';
    const isSelected = option.dataset.selected === '1';

    if (selection === 'multiple') {
      setOptionSelected(option, !isSelected);
    } else {
      getGroupOptions(group).forEach((item) => {
        setOptionSelected(item, item === option);
      });

      if (optionCode === '' && isSelected) {
        setOptionSelected(option, true);
      }
    }

    updateGroupState(group);
    applyGroupDependencies(root);
    updatePayload(root);

    if (selection === 'single') {
      moveToNextGroup(root, group);
    }
  };

  const bindOptionInteraction = (group, option, root) => {
    if (!group || !option || !root || option.dataset.pzConfigClickable === '1') {
      return;
    }

    if (getOptionInput(option)) {
      option.dataset.pzConfigClickable = '1';
      return;
    }

    option.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      handleOptionClick(group, option, root);
    });

    option.addEventListener('keydown', (event) => {
      if (event.key !== 'Enter' && event.key !== ' ') {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      handleOptionClick(group, option, root);
    });

    option.dataset.pzConfigClickable = '1';
  };

  const parsePayload = (payloadRaw) => {
    const source = String(payloadRaw || '').trim();
    if (source === '') {
      return null;
    }

    try {
      const parsed = JSON.parse(source);
      return parsed && typeof parsed === 'object' ? parsed : null;
    } catch (error) {
      return null;
    }
  };

  const hasMeaningfulPayload = (payloadRaw) => {
    const payload = parsePayload(payloadRaw);
    if (!payload) {
      return false;
    }

    if (Array.isArray(payload.items) && payload.items.length > 0) {
      return true;
    }

    if (payload.selections && typeof payload.selections === 'object') {
      return Object.keys(payload.selections).some((groupCode) => {
        const value = payload.selections[groupCode];

        if (Array.isArray(value)) {
          return value.some((item) => String(item || '').trim() !== '');
        }

        return String(value || '').trim() !== '';
      });
    }

    return false;
  };

  const ADD_TO_CART_SELECTOR = '[data-button-action="add-to-cart"]';

  const getNativeImpactMap = () => {
    if (nativeImpactMapCache !== null) {
      return nativeImpactMapCache;
    }

    nativeImpactMapCache = {};
    const script = document.getElementById('pz-productconfigurator-data');
    if (!script) {
      return nativeImpactMapCache;
    }

    try {
      const parsed = JSON.parse(script.textContent || '{}');
      if (parsed && typeof parsed === 'object') {
        nativeImpactMapCache = parsed;
      }
    } catch (error) {
      nativeImpactMapCache = {};
    }

    return nativeImpactMapCache;
  };

  const parseNativeImpactFromLabel = (rawLabel) => {
    const source = String(rawLabel || '').trim();
    if (source === '') {
      return { name: '', impact: '' };
    }

    const patterns = [
      /\s*\|\s*([+-]?\d+(?:[.,]\d{1,2})?)\s*(?:zł|pln)?\s*$/i,
      /\s*\(\s*([+-]?\d+(?:[.,]\d{1,2})?)\s*(?:zł|pln)?\s*\)\s*$/i,
      /\s*\[\s*([+-]?\d+(?:[.,]\d{1,2})?)\s*(?:zł|pln)?\s*\]\s*$/i,
      /\s+([+-]\d+(?:[.,]\d{1,2})?)\s*(?:zł|pln)\s*$/i,
    ];

    for (let index = 0; index < patterns.length; index += 1) {
      const match = source.match(patterns[index]);
      if (!match) {
        continue;
      }

      return {
        name: source.replace(match[0], '').trim(),
        impact: String(match[1] || '').trim(),
      };
    }

    return { name: source, impact: '' };
  };

  const formatNativeImpact = (rawImpact) => {
    const value = String(rawImpact || '').trim();
    if (value === '') {
      return '';
    }

    if (/dopł|zł|pln/i.test(value) && /[0-9]/.test(value)) {
      return value.replace(/^\+/, '');
    }

    const normalized = value.replace(',', '.').replace(/[^0-9+-.]/g, '');
    const parsed = Number(normalized);

    if (!Number.isFinite(parsed)) {
      return value;
    }

    const amount = new Intl.NumberFormat('pl-PL', {
      minimumFractionDigits: Number.isInteger(parsed) ? 0 : 2,
      maximumFractionDigits: 2,
    }).format(Math.abs(parsed));

    if (parsed > 0) {
      return `${amount} zł`;
    }

    if (parsed < 0) {
      return `-${amount} zł`;
    }

    return '0 zł';
  };

  const prepareNativeVariantOption = (option) => {
    if (!(option instanceof HTMLElement) || option.dataset.pzNativePrepared === '1') {
      return;
    }

    const card = option.querySelector('.config-option__card');
    const nameNode = option.querySelector('.js-option-name');
    const impactNode = option.querySelector('.js-option-impact');
    if (!(card instanceof HTMLElement) || !(nameNode instanceof HTMLElement) || !(impactNode instanceof HTMLElement)) {
      return;
    }

    const rawName = String(card.dataset.rawName || nameNode.textContent || '').trim();
    const parsed = parseNativeImpactFromLabel(rawName);
    const optionInput = option.querySelector('input[name^="group["]');
    const attributeId = optionInput ? String(optionInput.value || '') : '';
    const impactMap = getNativeImpactMap();
    const boImpact = attributeId !== '' && Object.prototype.hasOwnProperty.call(impactMap, attributeId)
      ? impactMap[attributeId]
      : undefined;
    const impactCandidate = typeof boImpact !== 'undefined' ? boImpact : (card.dataset.impact || parsed.impact);
    const impact = formatNativeImpact(impactCandidate);

    nameNode.textContent = parsed.name || rawName;

    if (impact !== '') {
      impactNode.textContent = impact;
      impactNode.classList.add('is-visible');
    } else {
      impactNode.textContent = '';
      impactNode.classList.remove('is-visible');
    }

    option.dataset.pzNativePrepared = '1';
  };

  const syncNativeVariantGroupState = (groupElement) => {
    if (!(groupElement instanceof HTMLElement)) {
      return;
    }

    const selectedInput = groupElement.querySelector('input[name^="group["]:checked');
    const selectedHolder = groupElement.querySelector('.js-config-selected');

    groupElement.querySelectorAll('.config-option').forEach((option) => {
      const input = option.querySelector('input[name^="group["]');
      option.classList.toggle('is-selected', !!(input && input.checked));
    });

    if (!(selectedHolder instanceof HTMLElement)) {
      return;
    }

    if (!(selectedInput instanceof HTMLElement)) {
      setGroupSummary(selectedHolder, 'Wybierz opcję', '');
      return;
    }

    const option = selectedInput.closest('.config-option');
    const selectedName = option ? option.querySelector('.js-option-name') : null;
    const selectedImpact = option ? option.querySelector('.js-option-impact') : null;
    const thumbnail = getOptionSummaryThumbnail(option);
    setGroupSummary(
      selectedHolder,
      selectedName ? String(selectedName.textContent || '').trim() : String(selectedInput.title || ''),
      selectedImpact ? String(selectedImpact.textContent || '').trim() : '',
      thumbnail
    );
  };

  const initNativeVariantConfigurator = () => {
    nativeImpactMapCache = null;
    const root = document.querySelector('.product__configurator.js-product-variants');
    if (!(root instanceof HTMLElement)) {
      return;
    }

    root.querySelectorAll('.config-option').forEach((option) => {
      prepareNativeVariantOption(option);
    });

    root.querySelectorAll('.config-group').forEach((groupElement) => {
      if (groupElement.dataset.pzNativeBound !== '1') {
        groupElement.addEventListener('change', (event) => {
          const target = event.target;
          if (!(target instanceof HTMLElement) || !target.matches('input[name^="group["]')) {
            return;
          }

          syncNativeVariantGroupState(groupElement);
        });

        groupElement.dataset.pzNativeBound = '1';
      }

      syncNativeVariantGroupState(groupElement);
    });
  };

  const ensureSubmitterTracking = () => {
    if (submitterTrackingBound) {
      return;
    }

    submitterTrackingBound = true;

    document.addEventListener('click', (event) => {
      const submitter = event.target.closest('button[type="submit"], input[type="submit"]');
      if (submitter && submitter.form) {
        submitter.form.__pzpcLastSubmitter = submitter;
      }
    }, true);
  };

  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', () => {
      if (floatingTooltipElement) {
        positionFloatingTooltip();
      }
    }, true);

    window.addEventListener('resize', () => {
      if (floatingTooltipElement) {
        positionFloatingTooltip();
      }
    });
  }

  document.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target : null;
    if (target && target.closest('.config-option__info')) {
      return;
    }

    destroyFloatingTooltip();
  });

  const ensureCustomizationInput = (form) => {
    if (!form) {
      return null;
    }

    let input = form.querySelector('input[name="id_customization"]');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'id_customization';
      input.value = '0';
      form.appendChild(input);
    }

    return input;
  };


  const submitFormWithBypass = (form) => {
    if (!form) {
      return;
    }

    form.dataset.pzpcSyncBypass = '1';

    if (typeof form.requestSubmit === 'function') {
      const submitter = form.__pzpcLastSubmitter;
      if (submitter && submitter.form === form) {
        form.requestSubmit(submitter);
      } else {
        form.requestSubmit();
      }
    } else {
      form.submit();
    }
  };

  const replayAddToCartClick = (submitter) => {
    if (!(submitter instanceof HTMLElement)) {
      return;
    }

    submitter.dataset.pzpcSyncBypassClick = '1';
    submitter.dispatchEvent(new MouseEvent('click', {
      bubbles: true,
      cancelable: true,
      view: window,
    }));
  };

  const syncCustomization = async (form, root) => {
    if (!form || !root) {
      return { success: false, message: resolveSyncErrorMessage(root) };
    }

    const syncUrl = (root.dataset.customizationSyncUrl || '').trim();
    if (syncUrl === '') {
      return {
        success: false,
        message: resolveSyncErrorMessage(root),
      };
    }

    const payloadInput = root.querySelector('.js-pz-productconfigurator-payload');
    const payloadTotalInput = root.querySelector('.js-pz-productconfigurator-total');
    const productInput = form.querySelector('input[name="id_product"]');
    const combinationInput = form.querySelector('input[name="id_product_attribute"]');
    const tokenInput = form.querySelector('input[name="token"]');

    const requestBody = new URLSearchParams();
    requestBody.set('ajax', '1');
    requestBody.set('id_product', productInput ? String(productInput.value || '') : '0');
    requestBody.set('id_product_attribute', combinationInput ? String(combinationInput.value || '') : '0');
    requestBody.set('pz_productconfigurator_payload', payloadInput ? String(payloadInput.value || '') : '');
    requestBody.set('pz_productconfigurator_total', payloadTotalInput ? String(payloadTotalInput.value || '') : '0');

    const token = (root.dataset.cartToken || (tokenInput ? tokenInput.value : '') || '').trim();
    if (token !== '') {
      requestBody.set('token', token);
    }

    try {
      const response = await fetch(syncUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          Accept: 'application/json',
        },
        credentials: 'same-origin',
        body: requestBody.toString(),
      });

      if (!response.ok) {
        return {
          success: false,
          message: resolveSyncErrorMessage(root),
        };
      }

      const data = await response.json();
      if (!data || data.success !== true) {
        return {
          success: false,
          message: data && data.message ? String(data.message) : resolveSyncErrorMessage(root),
        };
      }

      return {
        success: true,
        idCustomization: Number(data.id_customization || 0) || 0,
      };
    } catch (error) {
      return {
        success: false,
        message: resolveSyncErrorMessage(root),
      };
    }
  };

  const bindCartSync = (root) => {
    if (!root) {
      return;
    }

    const form = root.closest('form#add-to-cart-or-refresh') || document.getElementById('add-to-cart-or-refresh');
    if (!form || form.dataset.pzpcSyncBound === '1') {
      return;
    }

    ensureSubmitterTracking();
    form.dataset.pzpcSyncBound = '1';

    form.addEventListener('click', async (event) => {
      const target = event.target instanceof Element ? event.target : null;
      if (!target) {
        return;
      }

      const submitter = target.closest(ADD_TO_CART_SELECTOR);
      if (!(submitter instanceof HTMLElement) || submitter.form !== form) {
        return;
      }

      form.__pzpcLastSubmitter = submitter;

      if (submitter.dataset.pzpcSyncBypassClick === '1') {
        submitter.dataset.pzpcSyncBypassClick = '0';
        return;
      }

      if (form.dataset.pzpcSyncPending === '1') {
        event.preventDefault();
        if (typeof event.stopImmediatePropagation === 'function') {
          event.stopImmediatePropagation();
        }
        return;
      }

      const payloadInput = root.querySelector('.js-pz-productconfigurator-payload');
      const payloadRaw = payloadInput ? String(payloadInput.value || '') : '';
      if (!hasMeaningfulPayload(payloadRaw)) {
        const customizationInput = ensureCustomizationInput(form);
        if (customizationInput) {
          customizationInput.value = '0';
        }
        return;
      }

      event.preventDefault();
      if (typeof event.stopImmediatePropagation === 'function') {
        event.stopImmediatePropagation();
      }

      form.dataset.pzpcSyncPending = '1';
      const syncResult = await syncCustomization(form, root);
      form.dataset.pzpcSyncPending = '0';

      if (!syncResult.success) {
        if (syncResult.message) {
          window.alert(syncResult.message);
        }
        return;
      }

      const customizationInput = ensureCustomizationInput(form);
      if (customizationInput) {
        customizationInput.value = String(syncResult.idCustomization || 0);
      }

      replayAddToCartClick(submitter);
    }, true);

    form.addEventListener('submit', async (event) => {
      if (form.dataset.pzpcSyncBypass === '1') {
        form.dataset.pzpcSyncBypass = '0';
        return;
      }

      if (form.dataset.pzpcSyncPending === '1') {
        event.preventDefault();
        return;
      }

      const payloadInput = root.querySelector('.js-pz-productconfigurator-payload');
      const payloadRaw = payloadInput ? String(payloadInput.value || '') : '';

      if (!hasMeaningfulPayload(payloadRaw)) {
        const customizationInput = ensureCustomizationInput(form);
        if (customizationInput) {
          customizationInput.value = '0';
        }

        return;
      }

      event.preventDefault();
      if (typeof event.stopImmediatePropagation === 'function') {
        event.stopImmediatePropagation();
      }

      form.dataset.pzpcSyncPending = '1';

      const syncResult = await syncCustomization(form, root);
      form.dataset.pzpcSyncPending = '0';

      if (!syncResult.success) {
        if (syncResult.message) {
          window.alert(syncResult.message);
        }

        return;
      }

      const customizationInput = ensureCustomizationInput(form);
      if (customizationInput) {
        customizationInput.value = String(syncResult.idCustomization || 0);
      }

      submitFormWithBypass(form);
    }, true);
  };

  const initConfigurator = (root) => {
    if (!root || root.dataset.pzConfigInitialized === '1') {
      return;
    }

    destroyFloatingTooltip();
    root.dataset.pzConfigInitialized = '1';
    applyCachedSelections(root);

    if (root.dataset.pzConfigInputsBound !== '1') {
      root.addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLInputElement) || !target.classList.contains('js-pz-config-input')) {
          return;
        }

        const group = target.closest('.config-group');
        if (!group) {
          return;
        }

        getGroupOptions(group).forEach((option) => {
          setOptionSelected(option, isOptionSelected(option));
        });

        updateGroupState(group);
        applyGroupDependencies(root);
        updatePayload(root);

        if (group.dataset.selection !== 'multiple' && target.checked) {
          moveToNextGroup(root, group);
        }
      });

      root.dataset.pzConfigInputsBound = '1';
    }

    if (root.dataset.pzConfigResetBound !== '1') {
      const resetButton = root.querySelector('.js-pz-productconfigurator-reset');
      if (resetButton) {
        resetButton.addEventListener('click', () => {
          resetSelections(root);
        });
      }

      root.dataset.pzConfigResetBound = '1';
    }

    getAllConfigGroups(root).forEach((group) => {
      getGroupOptions(group).forEach((option) => {
        bindOptionInteraction(group, option, root);
        setOptionSelected(option, isOptionSelected(option));
      });

      updateGroupState(group);
    });

    root.querySelectorAll('.config-option__info').forEach((info) => {
      bindInfoTooltip(info);
    });

    applyGroupDependencies(root);
    updatePayload(root);
    initializeGroupFlow(root);
    bindCartSync(root);
  };

  const initConfigurators = () => {
    document.querySelectorAll('.js-pz-productconfigurator').forEach((root) => {
      initConfigurator(root);
    });

    initNativeVariantConfigurator();
  };

  const onProductUpdated = () => {
    window.requestAnimationFrame(() => {
      document.querySelectorAll('.js-pz-productconfigurator').forEach((root) => {
        root.dataset.pzConfigInitialized = '0';
      });

      initConfigurators();
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initConfigurators);
  } else {
    initConfigurators();
  }

  if (typeof prestashop !== 'undefined' && prestashop && typeof prestashop.on === 'function') {
    prestashop.on('updatedProduct', onProductUpdated);
  }
}());

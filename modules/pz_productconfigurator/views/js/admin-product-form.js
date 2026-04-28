(function () {
  const EDITABLE_GROUP_TYPES = ['virtual_option', 'addon_product'];
  const DEPENDENCY_NONE_OPTION = '__none__';

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const slugify = (value, fallback) => {
    const normalized = String(value ?? '')
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');

    if (normalized !== '') {
      return normalized;
    }

    return String(fallback || 'item');
  };

  const deepClone = (value) => {
    if (value === null || typeof value === 'undefined') {
      return value;
    }

    try {
      return JSON.parse(JSON.stringify(value));
    } catch (error) {
      return value;
    }
  };

  const formatIndexedLabel = (template, index, fallbackPrefix) => {
    const safeIndex = Number(index) > 0 ? Math.trunc(Number(index)) : 1;
    const safeTemplate = String(template || '').trim();

    if (safeTemplate.includes('%d')) {
      return safeTemplate.replace('%d', String(safeIndex));
    }

    if (safeTemplate.includes('{index}')) {
      return safeTemplate.replace('{index}', String(safeIndex));
    }

    if (safeTemplate !== '') {
      return `${safeTemplate} ${safeIndex}`;
    }

    const safeFallbackPrefix = String(fallbackPrefix || '').trim();
    if (safeFallbackPrefix !== '') {
      return `${safeFallbackPrefix} ${safeIndex}`;
    }

    return String(safeIndex);
  };

  const replaceSinglePlaceholder = (template, value) => {
    const source = String(template || '');
    const safeValue = String(value || '');

    if (source.includes('%s')) {
      return source.replace('%s', safeValue);
    }

    if (source.includes('{action}')) {
      return source.replace('{action}', safeValue);
    }

    return source;
  };

  const normalizeHexColor = (value) => {
    const source = String(value || '').trim();
    const shortMatch = /^#([0-9a-fA-F]{3})$/.exec(source);
    if (shortMatch) {
      const expanded = shortMatch[1].split('').map((char) => char + char).join('');
      return `#${expanded}`.toLowerCase();
    }

    if (/^#[0-9a-fA-F]{6}$/.test(source)) {
      return source.toLowerCase();
    }

    return '';
  };

  const colorPickerValue = (value) => normalizeHexColor(value) || '#ffffff';

  const normalizeImageColumns = (value) => {
    const parsed = Number.parseInt(String(value ?? '').trim(), 10);
    if (!Number.isInteger(parsed) || parsed < 1 || parsed > 8) {
      return '';
    }

    return String(parsed);
  };

  const createEmptyOption = (groupIndex, optionIndex, labels) => ({
    code: `option-${groupIndex + 1}-${optionIndex + 1}`,
    label: formatIndexedLabel(labels.optionTemplate, optionIndex + 1, ''),
    description: '',
    price: '',
    color: '',
    texture: '',
    image: '',
    product_id: '',
    default: false,
    enabled: true,
  });

  const createEmptyGroup = (groupIndex, labels) => ({
    code: `group-${groupIndex + 1}`,
    label: formatIndexedLabel(labels.groupTemplate, groupIndex + 1, ''),
    type: 'virtual_option',
    selection: 'single',
    presentation: 'text',
    image_columns: '',
    required: false,
    none_label: String(labels.noneOptionDefaultLabel || ''),
    depends_on_group: '',
    depends_on_option: '',
    options: [createEmptyOption(groupIndex, 0, labels)],
  });

  const createExampleSchema = () => ({
    version: 1,
    native_attribute_impacts: {},
    groups: [
      {
        code: 'group-1',
        label: 'Rozmiar łóżka',
        type: 'virtual_option',
        selection: 'single',
        presentation: 'text',
        required: true,
        none_label: 'Nie chce tej opcji',
        options: [
          {
            code: 'option-1-1',
            label: '70x140',
            price: 1120,
            enabled: true,
          },
          {
            code: 'option-1-2',
            label: '70x150',
            price: 1120,
            enabled: true,
          },
          {
            code: 'option-1-3',
            label: '70x160',
            price: 1120,
            enabled: true,
          },
          {
            code: 'option-1-4',
            label: '80x150',
            price: 1130,
            enabled: true,
          },
          {
            code: 'option-1-5',
            label: '80x160',
            price: 1130,
            enabled: true,
          },
          {
            code: 'option-1-6',
            label: '80x170',
            price: 1130,
            enabled: true,
          },
        ],
      },
      {
        code: 'group-2',
        label: 'Materac',
        type: 'virtual_option',
        selection: 'single',
        presentation: 'text',
        required: false,
        none_label: 'Nie chcę materaca',
        options: [
          {
            code: 'option-2-1',
            label: '70x140',
            price: 1120,
            enabled: true,
          },
          {
            code: 'option-2-2',
            label: '70x150',
            price: 1120,
            enabled: true,
          },
          {
            code: 'option-2-3',
            label: '70x160',
            price: 1120,
            enabled: true,
          },
          {
            code: 'option-2-4',
            label: '80x150',
            price: 1130,
            enabled: true,
          },
          {
            code: 'option-2-5',
            label: '80x160',
            price: 1130,
            enabled: true,
          },
          {
            code: 'option-2-6',
            label: '80x170',
            price: 1130,
            enabled: true,
          },
        ],
      },
      {
        code: 'group-6',
        label: 'Kolory',
        type: 'virtual_option',
        selection: 'single',
        presentation: 'image',
        image_columns: 6,
        required: true,
        none_label: 'Nie chce tej opcji',
        options: [
          {
            code: 'option-6-1',
            label: 'Orzech 22-62',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-2',
            label: 'Orzech 22-74',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-3',
            label: 'Orzech 22-68',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-4',
            label: 'Oliwka 27-50',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-5',
            label: 'Orzech 22-66',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-6',
            label: 'Oranż 23-05',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-7',
            label: 'Brąz 22-45',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-8',
            label: 'Teak 23-49',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-9',
            label: 'Brąz 22-40',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-10',
            label: 'Oranż 23-50',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-11',
            label: 'Wenge',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-12',
            label: 'Oranż 23-25',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-13',
            label: 'Palisander 26-32',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-14',
            label: 'Granat 28-60',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-15',
            label: 'Olcha/Olcha miodowa',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-16',
            label: 'Biel transparentna',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-17',
            label: 'Dąb bielony',
            price: 0,
            enabled: true,
          },
        ],
      },
      {
        code: 'group-3',
        label: 'Barierka ochronna',
        type: 'virtual_option',
        selection: 'single',
        presentation: 'text',
        required: false,
        none_label: 'Nie chcę barierki ochronnej',
        options: [
          {
            code: 'option-3-1',
            label: 'Barierka A',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-3-2',
            label: 'Barierka B',
            price: 0,
            enabled: true,
          },
        ],
      },
      {
        code: 'group-4',
        label: 'Szuflady',
        type: 'virtual_option',
        selection: 'single',
        presentation: 'text',
        required: false,
        none_label: 'Nie chce szuflad',
        options: [
          {
            code: 'option-4-1',
            label: 'Szuflada jednoczęściowa',
            price: 420,
            enabled: true,
          },
          {
            code: 'option-4-2',
            label: 'Szuflada dwuczęściowa',
            price: 420,
            enabled: true,
          },
        ],
      },
      {
        code: 'group-5',
        label: 'Stelaż',
        type: 'virtual_option',
        selection: 'single',
        presentation: 'text',
        required: false,
        none_label: 'Nie chcę stelaża',
        options: [
          {
            code: 'option-5-1',
            label: 'Stelaż Standardowy A',
            price: 0,
            enabled: true,
          },
          {
            code: 'option-5-2',
            label: 'Stelaż B z ręczną regulacją przy głowie (6 stopni  podnoszenia)',
            price: 200,
            enabled: true,
          },
        ],
      },
    ],
    rules: [],
  });

  const hasMeaningfulConfiguration = (state) => {
    return Array.isArray(state?.groups) && state.groups.length > 0;
  };

  const normalizeOption = (option, groupIndex, optionIndex, labels) => ({
    code: String(option?.code || `option-${groupIndex + 1}-${optionIndex + 1}`),
    label: String(option?.label || option?.name || formatIndexedLabel(labels.optionTemplate, optionIndex + 1, '')),
    description: String(option?.description || ''),
    price: typeof option?.price === 'number' || option?.price === 0 ? String(option.price) : '',
    color: normalizeHexColor(option?.color),
    texture: String(option?.texture || ''),
    image: String(option?.image || ''),
    product_id: option?.product_id ? String(option.product_id) : '',
    default: !!option?.default,
    enabled: !Object.prototype.hasOwnProperty.call(option || {}, 'enabled') || !!option?.enabled,
  });

  const normalizeGroup = (group, groupIndex, labels) => {
    const selection = group?.selection === 'multiple' ? 'multiple' : 'single';
    const presentation = ['text', 'swatch', 'image'].includes(group?.presentation) ? group.presentation : 'text';
    const type = EDITABLE_GROUP_TYPES.includes(group?.type) ? group.type : 'virtual_option';
    const rawOptions = Array.isArray(group?.options) ? group.options : [];
    const options = rawOptions.length > 0
      ? rawOptions.map((option, optionIndex) => normalizeOption(option, groupIndex, optionIndex, labels))
      : [createEmptyOption(groupIndex, 0, labels)];

    return {
      code: String(group?.code || `group-${groupIndex + 1}`),
      label: String(group?.label || group?.name || formatIndexedLabel(labels.groupTemplate, groupIndex + 1, '')),
      type,
      selection,
      presentation,
      image_columns: normalizeImageColumns(group?.image_columns),
      required: !!group?.required,
      none_label: String(group?.none_label || labels.noneOptionDefaultLabel || ''),
      depends_on_group: String(group?.depends_on_group || '').trim(),
      depends_on_option: String(group?.depends_on_option || '').trim(),
      options,
    };
  };

  const reconcileGroupDependencies = (groups) => {
    if (!Array.isArray(groups) || groups.length === 0) {
      return;
    }

    groups.forEach((group, groupIndex) => {
      const sourceCode = String(group?.depends_on_group || '').trim();
      if (sourceCode === '') {
        group.depends_on_group = '';
        group.depends_on_option = '';
        return;
      }

      const sourceGroup = groups.find((candidate, candidateIndex) => (
        candidateIndex !== groupIndex
        && String(candidate?.code || '').trim() === sourceCode
      )) || null;

      if (!sourceGroup) {
        group.depends_on_group = '';
        group.depends_on_option = '';
        return;
      }

      group.depends_on_group = sourceCode;
      const dependsOnOption = String(group?.depends_on_option || '').trim();
      if (dependsOnOption === '' || dependsOnOption === DEPENDENCY_NONE_OPTION) {
        group.depends_on_option = dependsOnOption;
        return;
      }

      const sourceOptionCodes = Array.isArray(sourceGroup.options)
        ? sourceGroup.options
          .map((option) => String(option?.code || '').trim())
          .filter((code) => code !== '')
        : [];

      if (!sourceOptionCodes.includes(dependsOnOption)) {
        group.depends_on_option = '';
      }
    });
  };

  const parseEditorState = (rawJson, labels) => {
    const state = {
      version: 1,
      native_attribute_impacts: {},
      groups: [],
      preserved_groups: [],
      base_object: {},
      invalid_source: false,
    };

    const source = String(rawJson || '').trim();
    if (source === '') {
      return state;
    }

    try {
      const parsed = JSON.parse(source);
      if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
        state.invalid_source = true;
        return state;
      }

      state.base_object = deepClone(parsed) || {};
      state.version = Math.max(1, Number(parsed.version || 1) || 1);
      state.native_attribute_impacts = {};

      const rawGroups = Array.isArray(parsed.groups) ? parsed.groups : [];
      rawGroups.forEach((group, groupIndex) => {
        if (EDITABLE_GROUP_TYPES.includes(group?.type || 'virtual_option')) {
          state.groups.push(normalizeGroup(group, groupIndex, labels));
          return;
        }

        state.preserved_groups.push(deepClone(group));
      });

      reconcileGroupDependencies(state.groups);

      return state;
    } catch (error) {
      state.invalid_source = true;
      return state;
    }
  };

  const serializeState = (state, labels) => {
    reconcileGroupDependencies(state.groups);

    const output = deepClone(state.base_object) || {};
    output.version = Math.max(1, Number(state.version || 1) || 1);
    output.native_attribute_impacts = {};

    const serializedGroups = state.groups.map((group, groupIndex) => {
      const serializedGroup = {
        code: String(group.code || slugify(group.label, `group-${groupIndex + 1}`)),
        label: String(group.label || formatIndexedLabel(labels.groupTemplate, groupIndex + 1, '')),
        type: EDITABLE_GROUP_TYPES.includes(group.type) ? group.type : 'virtual_option',
        selection: group.selection === 'multiple' ? 'multiple' : 'single',
        presentation: ['text', 'swatch', 'image'].includes(group.presentation) ? group.presentation : 'text',
        required: !!group.required,
        none_label: String(group.none_label || labels.noneOptionDefaultLabel || ''),
        options: [],
      };

      const dependsOnGroup = String(group.depends_on_group || '').trim();
      const dependsOnOption = String(group.depends_on_option || '').trim();
      if (dependsOnGroup !== '') {
        serializedGroup.depends_on_group = dependsOnGroup;
        serializedGroup.depends_on_option = dependsOnOption;
      }

      const imageColumns = normalizeImageColumns(group.image_columns);
      if (serializedGroup.presentation === 'image' && imageColumns !== '') {
        serializedGroup.image_columns = Number(imageColumns);
      }

      const defaultHandled = new Set();

      group.options.forEach((option, optionIndex) => {
        const label = String(option.label || formatIndexedLabel(labels.optionTemplate, optionIndex + 1, '')).trim();
        if (label === '') {
          return;
        }

        const serializedOption = {
          code: String(option.code || slugify(label, `option-${groupIndex + 1}-${optionIndex + 1}`)),
          label,
          price: 0,
          enabled: true,
        };

        const description = String(option.description || '').trim();
        if (description !== '') {
          serializedOption.description = description;
        }

        const rawPrice = String(option.price ?? '').trim().replace(',', '.');
        if (rawPrice !== '' && Number.isFinite(Number(rawPrice))) {
          serializedOption.price = Number(rawPrice);
        }

        const color = normalizeHexColor(option.color);
        const texture = String(option.texture || '').trim();
        const image = String(option.image || '').trim();
        const productId = String(option.product_id || '').trim();

        if (color !== '') {
          serializedOption.color = color;
        }
        if (texture !== '') {
          serializedOption.texture = texture;
        }
        if (image !== '') {
          serializedOption.image = image;
        }
        if (productId !== '' && Number.isFinite(Number(productId))) {
          serializedOption.product_id = Number(productId);
        }

        if (option.default) {
          if (serializedGroup.selection === 'multiple') {
            serializedOption.default = true;
          } else if (!defaultHandled.has(serializedGroup.code)) {
            serializedOption.default = true;
            defaultHandled.add(serializedGroup.code);
          }
        }

        serializedGroup.options.push(serializedOption);
      });

      if (serializedGroup.options.length === 0) {
        serializedGroup.options.push({
          code: `option-${groupIndex + 1}-1`,
          label: formatIndexedLabel(labels.optionTemplate, 1, ''),
          price: 0,
          enabled: true,
        });
      }

      return serializedGroup;
    });

    output.groups = serializedGroups.concat(state.preserved_groups || []);

    if (!Array.isArray(output.rules)) {
      output.rules = [];
    }

    return JSON.stringify(output, null, 2);
  };

  const renderMediaField = ({ label, mediaField, mediaValue, groupIndex, optionIndex, labels }) => {
    const hasMedia = String(mediaValue || '').trim() !== '';
    const uploadLabel = hasMedia
      ? String(labels.changeImage || labels.uploadImage || '')
      : String(labels.uploadImage || '');

    return `
      <div class="pzpc-editor__option-field pzpc-editor__option-field--full">
        <label>${escapeHtml(label)}</label>
        <div class="pzpc-editor__media-actions">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-action="pick-media" data-media-field="${escapeHtml(mediaField)}" data-group-index="${groupIndex}" data-option-index="${optionIndex}">${escapeHtml(uploadLabel)}</button>
          <button type="button" class="btn btn-outline-danger btn-sm" data-action="clear-media" data-media-field="${escapeHtml(mediaField)}" data-group-index="${groupIndex}" data-option-index="${optionIndex}" ${hasMedia ? '' : 'disabled'}>${escapeHtml(labels.removeImage)}</button>
          <input type="file" class="d-none" accept="image/*" data-action="upload-media" data-media-field="${escapeHtml(mediaField)}" data-group-index="${groupIndex}" data-option-index="${optionIndex}">
        </div>
        ${hasMedia ? `
          <div class="pzpc-editor__media-preview">
            <img src="${escapeHtml(mediaValue)}" alt="${escapeHtml(labels.imagePreviewAlt)}">
          </div>
        ` : ''}
      </div>
    `;
  };

  const renderOptionFields = (group, option, groupIndex, optionIndex, labels) => {
    const isImage = group.presentation === 'image';
    const isSwatch = group.presentation === 'swatch';
    const isText = group.presentation === 'text';
    const normalizedColor = colorPickerValue(option.color);

    return `
      <div class="pzpc-editor__option" data-group-index="${groupIndex}" data-option-index="${optionIndex}">
        <div class="pzpc-editor__option-header">
          <span class="pzpc-editor__option-title">${escapeHtml(option.label || formatIndexedLabel(labels.optionTemplate, optionIndex + 1, ''))}</span>
          <div class="pzpc-editor__toolbar">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="move-option-up" data-group-index="${groupIndex}" data-option-index="${optionIndex}">${escapeHtml(labels.moveOptionUp)}</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="move-option-down" data-group-index="${groupIndex}" data-option-index="${optionIndex}">${escapeHtml(labels.moveOptionDown)}</button>
            <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-option" data-group-index="${groupIndex}" data-option-index="${optionIndex}">${escapeHtml(labels.removeOption)}</button>
          </div>
        </div>
        <div class="pzpc-editor__option-fields">
          <div class="pzpc-editor__option-field">
            <label>${escapeHtml(labels.optionName)}</label>
            <input type="text" class="form-control" value="${escapeHtml(option.label)}" data-group-index="${groupIndex}" data-option-index="${optionIndex}" data-option-field="label">
          </div>
          ${isText ? `
            <div class="pzpc-editor__option-field pzpc-editor__option-field--full">
              <label>${escapeHtml(labels.optionShortDescription)}</label>
              <input type="text" class="form-control" value="${escapeHtml(option.description)}" data-group-index="${groupIndex}" data-option-index="${optionIndex}" data-option-field="description">
            </div>
          ` : ''}
          <div class="pzpc-editor__option-field">
            <label>${escapeHtml(labels.optionSurcharge)}</label>
            <input type="number" step="0.01" class="form-control" value="${escapeHtml(option.price)}" placeholder="0" data-group-index="${groupIndex}" data-option-index="${optionIndex}" data-option-field="price">
          </div>
          ${isSwatch ? `
            <div class="pzpc-editor__option-field">
              <label>${escapeHtml(labels.optionColorHex)}</label>
              <div class="pzpc-editor__color-row">
                <input type="color" class="form-control form-control-color pzpc-editor__color-picker" value="${escapeHtml(normalizedColor)}" data-color-role="picker" data-group-index="${groupIndex}" data-option-index="${optionIndex}" data-option-field="color">
                <input type="text" class="form-control" value="${escapeHtml(option.color)}" placeholder="${escapeHtml(normalizedColor)}" data-color-role="text" data-group-index="${groupIndex}" data-option-index="${optionIndex}" data-option-field="color">
              </div>
            </div>
            ${renderMediaField({
              label: labels.optionTextureUrl,
              mediaField: 'texture',
              mediaValue: option.texture,
              groupIndex,
              optionIndex,
              labels,
            })}
          ` : ''}
          ${isImage ? `
            ${renderMediaField({
              label: labels.optionImageUrl,
              mediaField: 'image',
              mediaValue: option.image,
              groupIndex,
              optionIndex,
              labels,
            })}
          ` : ''}
          <div class="pzpc-editor__option-field">
            <label class="pzpc-editor__checkbox">
              <input type="${group.selection === 'multiple' ? 'checkbox' : 'radio'}" ${option.default ? 'checked' : ''} data-group-index="${groupIndex}" data-option-index="${optionIndex}" data-option-field="default">
              <span>${escapeHtml(labels.optionDefaultSelected)}</span>
            </label>
          </div>
        </div>
      </div>
    `;
  };

  const renderGroups = (state, labels, groupOpenStates) => {
    if (!Array.isArray(state.groups) || state.groups.length === 0) {
      return `
        <div class="pzpc-editor__empty">
          ${escapeHtml(replaceSinglePlaceholder(labels.emptyGroups, labels.addGroup))}
        </div>
      `;
    }

    return state.groups.map((group, groupIndex) => {
      const isOpen = !Array.isArray(groupOpenStates) || typeof groupOpenStates[groupIndex] === 'undefined'
        ? false
        : !!groupOpenStates[groupIndex];
      const sourceGroupCode = String(group.depends_on_group || '').trim();
      const sourceGroups = state.groups.filter((candidate, candidateIndex) => (
        candidateIndex !== groupIndex && String(candidate.code || '').trim() !== ''
      ));
      const sourceGroup = sourceGroups.find((candidate) => String(candidate.code || '').trim() === sourceGroupCode) || null;

      const dependencySourceOptions = [
        `<option value="">${escapeHtml(labels.groupDependencyDisabled)}</option>`,
        ...sourceGroups.map((candidate) => {
          const candidateCode = String(candidate.code || '').trim();
          const candidateLabel = String(candidate.label || candidateCode || '');
          const isSelected = sourceGroupCode !== '' && candidateCode === sourceGroupCode;
          return `<option value="${escapeHtml(candidateCode)}" ${isSelected ? 'selected' : ''}>${escapeHtml(candidateLabel)}</option>`;
        }),
      ].join('');

      const dependencyConditionValue = String(group.depends_on_option || '').trim();
      const dependencyConditionDisabled = !sourceGroup;
      const dependencyConditionOptions = sourceGroup
        ? [
          `<option value="">${escapeHtml(labels.groupDependencyAnyOption)}</option>`,
          `<option value="${escapeHtml(DEPENDENCY_NONE_OPTION)}" ${dependencyConditionValue === DEPENDENCY_NONE_OPTION ? 'selected' : ''}>${escapeHtml(labels.groupDependencyNoneOption)}</option>`,
          ...sourceGroup.options
            .map((option) => {
              const optionCode = String(option.code || '').trim();
              const optionLabel = String(option.label || optionCode || '');
              if (optionCode === '') {
                return '';
              }

              const isSelected = dependencyConditionValue !== '' && dependencyConditionValue === optionCode;
              return `<option value="${escapeHtml(optionCode)}" ${isSelected ? 'selected' : ''}>${escapeHtml(optionLabel)}</option>`;
            })
            .filter((row) => row !== ''),
        ].join('')
        : `<option value="">${escapeHtml(labels.groupDependencyDisabled)}</option>`;

      return `
      <details class="pzpc-editor__group" ${isOpen ? 'open' : ''}>
        <summary class="pzpc-editor__group-summary">
          <h4 class="pzpc-editor__group-title">${escapeHtml(group.label || formatIndexedLabel(labels.groupTemplate, groupIndex + 1, ''))}</h4>
        </summary>
        <div class="pzpc-editor__group-body">
          <div class="pzpc-editor__group-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="move-group-up" data-group-index="${groupIndex}">${escapeHtml(labels.moveGroupUp)}</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="move-group-down" data-group-index="${groupIndex}">${escapeHtml(labels.moveGroupDown)}</button>
            <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-group" data-group-index="${groupIndex}">${escapeHtml(labels.removeGroup)}</button>
          </div>
          <div class="pzpc-editor__fields">
            <div class="pzpc-editor__field">
              <label>${escapeHtml(labels.groupName)}</label>
              <input type="text" class="form-control" value="${escapeHtml(group.label)}" data-group-index="${groupIndex}" data-group-field="label">
            </div>
            <div class="pzpc-editor__field">
              <label>${escapeHtml(labels.groupPresentation)}</label>
              <select class="form-select" data-group-index="${groupIndex}" data-group-field="presentation">
                <option value="text" ${group.presentation === 'text' ? 'selected' : ''}>${escapeHtml(labels.groupPresentationText)}</option>
                <option value="swatch" ${group.presentation === 'swatch' ? 'selected' : ''}>${escapeHtml(labels.groupPresentationSwatch)}</option>
                <option value="image" ${group.presentation === 'image' ? 'selected' : ''}>${escapeHtml(labels.groupPresentationImage)}</option>
              </select>
            </div>
            <div class="pzpc-editor__field ${group.presentation !== 'image' ? 'd-none' : ''}">
              <label>${escapeHtml(labels.groupImageColumns)}</label>
              <input type="number" min="1" max="8" step="1" class="form-control" value="${escapeHtml(normalizeImageColumns(group.image_columns))}" placeholder="6" data-group-index="${groupIndex}" data-group-field="image_columns">
            </div>
            <div class="pzpc-editor__field">
              <label class="pzpc-editor__checkbox">
                <input type="checkbox" ${group.required ? 'checked' : ''} data-group-index="${groupIndex}" data-group-field="required">
                <span>${escapeHtml(labels.groupRequiredSelection)}</span>
              </label>
            </div>
            <div class="pzpc-editor__field ${group.selection !== 'single' || group.required ? 'd-none' : ''}">
              <label>${escapeHtml(labels.groupNoneOptionLabel)}</label>
              <input type="text" class="form-control" value="${escapeHtml(group.none_label)}" data-group-index="${groupIndex}" data-group-field="none_label">
            </div>
            <div class="pzpc-editor__field">
              <label>${escapeHtml(labels.groupDependencySource)}</label>
              <select class="form-select" data-group-index="${groupIndex}" data-group-field="depends_on_group">
                ${dependencySourceOptions}
              </select>
            </div>
            <div class="pzpc-editor__field">
              <label>${escapeHtml(labels.groupDependencyOption)}</label>
              <select class="form-select" data-group-index="${groupIndex}" data-group-field="depends_on_option" ${dependencyConditionDisabled ? 'disabled' : ''}>
                ${dependencyConditionOptions}
              </select>
            </div>
          </div>
          <div class="pzpc-editor__section">
            <div class="pzpc-editor__section-header">
              <h5 class="pzpc-editor__section-title">${escapeHtml(labels.groupOptionsTitle)}</h5>
              <button type="button" class="btn btn-primary btn-sm" data-action="add-option" data-group-index="${groupIndex}">${escapeHtml(labels.addOption)}</button>
            </div>
            <div class="pzpc-editor__section-body">
              <div class="pzpc-editor__options">
                ${group.options.map((option, optionIndex) => renderOptionFields(group, option, groupIndex, optionIndex, labels)).join('')}
              </div>
            </div>
          </div>
        </div>
      </details>
    `;
    }).join('');
  };

  const renderEditor = (host, state, textarea, activeToggle, labels, groupOpenStates) => {
    const configuratorDisabledWarning = hasMeaningfulConfiguration(state) && activeToggle && !activeToggle.checked
      ? `
        <div class="pzpc-editor__warning">
          ${escapeHtml(labels.warningConfiguratorDisabled)}
          <div class="mt-2">
            <button type="button" class="btn btn-warning btn-sm" data-action="enable-configurator">${escapeHtml(labels.enableConfigurator)}</button>
          </div>
        </div>
      `
      : '';

    const warning = state.invalid_source
      ? `
        <div class="pzpc-editor__warning">
          ${escapeHtml(labels.warningInvalidJson)}
        </div>
      `
      : '';

    host.innerHTML = `
      ${warning}
      ${configuratorDisabledWarning}
      <div class="pzpc-editor__actions">
        <div class="pzpc-editor__actions-left">
          <button type="button" class="btn btn-primary" data-action="add-group">${escapeHtml(labels.addGroup)}</button>
          <button type="button" class="btn btn-outline-danger" data-action="clear-groups">${escapeHtml(labels.clearConfiguration)}</button>
        </div>
        <div class="pzpc-editor__actions-right">
          <button type="button" class="btn btn-outline-secondary" data-action="load-example">${escapeHtml(labels.loadSampleData)}</button>
        </div>
      </div>
      <section class="pzpc-editor__section">
        <div class="pzpc-editor__section-body">
          ${renderGroups(state, labels, groupOpenStates)}
        </div>
      </section>
    `;

    const advanced = host.nextElementSibling;
    if (advanced && advanced.classList.contains('pzpc-editor__advanced')) {
      const advancedBody = advanced.querySelector('.pzpc-editor__advanced-body');
      if (advancedBody && !advancedBody.contains(textarea)) {
        advancedBody.appendChild(textarea);
      }
    }
  };

  const moveArrayItem = (items, fromIndex, toIndex) => {
    if (!Array.isArray(items)) {
      return;
    }

    if (fromIndex < 0 || toIndex < 0 || fromIndex >= items.length || toIndex >= items.length) {
      return;
    }

    const [item] = items.splice(fromIndex, 1);
    items.splice(toIndex, 0, item);
  };

  const initializeEditor = (textarea) => {
    if (!textarea || textarea.dataset.pzpcInitialized === '1') {
      return;
    }

    textarea.dataset.pzpcInitialized = '1';
    textarea.setAttribute('wrap', 'off');

    const labels = {
      addGroup: String(textarea.dataset.labelAddGroup || ''),
      loadSampleData: String(textarea.dataset.labelLoadSample || ''),
      clearConfiguration: String(textarea.dataset.labelClearConfiguration || ''),
      moveGroupUp: String(textarea.dataset.labelMoveGroupUp || ''),
      moveGroupDown: String(textarea.dataset.labelMoveGroupDown || ''),
      removeGroup: String(textarea.dataset.labelRemoveGroup || ''),
      moveOptionUp: String(textarea.dataset.labelMoveOptionUp || textarea.dataset.labelMoveGroupUp || ''),
      moveOptionDown: String(textarea.dataset.labelMoveOptionDown || textarea.dataset.labelMoveGroupDown || ''),
      removeOption: String(textarea.dataset.labelRemoveOption || textarea.dataset.labelRemoveGroup || ''),
      addOption: String(textarea.dataset.labelAddOption || textarea.dataset.labelAddGroup || ''),
      emptyGroups: String(textarea.dataset.labelEmptyGroups || ''),
      optionName: String(textarea.dataset.labelOptionName || ''),
      optionShortDescription: String(textarea.dataset.labelOptionShortDescription || ''),
      optionSurcharge: String(textarea.dataset.labelOptionSurcharge || ''),
      optionColorHex: String(textarea.dataset.labelOptionColorHex || ''),
      optionTextureUrl: String(textarea.dataset.labelOptionTextureUrl || ''),
      optionImageUrl: String(textarea.dataset.labelOptionImageUrl || ''),
      optionDefaultSelected: String(textarea.dataset.labelOptionDefaultSelected || ''),
      groupName: String(textarea.dataset.labelGroupName || ''),
      groupPresentation: String(textarea.dataset.labelGroupPresentation || ''),
      groupPresentationText: String(textarea.dataset.labelGroupPresentationText || ''),
      groupPresentationSwatch: String(textarea.dataset.labelGroupPresentationSwatch || ''),
      groupPresentationImage: String(textarea.dataset.labelGroupPresentationImage || ''),
      groupImageColumns: String(textarea.dataset.labelGroupImageColumns || ''),
      groupRequiredSelection: String(textarea.dataset.labelGroupRequiredSelection || ''),
      groupNoneOptionLabel: String(textarea.dataset.labelGroupNoneOptionLabel || ''),
      groupDependencySource: String(textarea.dataset.labelGroupDependencySource || ''),
      groupDependencyOption: String(textarea.dataset.labelGroupDependencyOption || ''),
      groupDependencyNoneOption: String(textarea.dataset.labelGroupDependencyNoneOption || ''),
      groupDependencyDisabled: String(textarea.dataset.labelGroupDependencyDisabled || ''),
      groupDependencyAnyOption: String(textarea.dataset.labelGroupDependencyAnyOption || textarea.dataset.labelChooseOption || ''),
      groupOptionsTitle: String(textarea.dataset.labelGroupOptionsTitle || ''),
      warningConfiguratorDisabled: String(textarea.dataset.labelWarningConfiguratorDisabled || ''),
      enableConfigurator: String(textarea.dataset.labelEnableConfigurator || ''),
      warningInvalidJson: String(textarea.dataset.labelWarningInvalidJson || ''),
      showAdvancedJson: String(textarea.dataset.labelShowAdvancedJson || ''),
      groupTemplate: String(textarea.dataset.labelGroupTemplate || '%d'),
      optionTemplate: String(textarea.dataset.labelOptionTemplate || '%d'),
      noneOptionDefaultLabel: String(textarea.dataset.labelNoneOptionDefault || ''),
      uploadImage: String(textarea.dataset.labelUploadImage || ''),
      changeImage: String(textarea.dataset.labelChangeImage || ''),
      removeImage: String(textarea.dataset.labelRemoveImage || ''),
      imagePreviewAlt: String(textarea.dataset.labelImagePreviewAlt || ''),
    };

    const state = parseEditorState(textarea.value, labels);
    const activeToggle = document.querySelector('input[type="checkbox"][name*="[pz_productconfigurator][active]"]');
    const host = document.createElement('div');
    host.className = 'pzpc-editor';

    const advanced = document.createElement('details');
    advanced.className = 'pzpc-editor__advanced';
    advanced.innerHTML = `
      <summary>${escapeHtml(labels.showAdvancedJson)}</summary>
      <div class="pzpc-editor__advanced-body"></div>
    `;

    textarea.parentNode.insertBefore(host, textarea);
    textarea.parentNode.insertBefore(advanced, textarea.nextSibling);
    advanced.querySelector('.pzpc-editor__advanced-body').appendChild(textarea);

    const syncTextarea = () => {
      textarea.value = serializeState(state, labels);
    };

    let groupOpenStates = [];

    const rerender = () => {
      if (host.childElementCount > 0) {
        groupOpenStates = Array.from(host.querySelectorAll('.pzpc-editor__group')).map((groupElement) => !!groupElement.open);
      }

      renderEditor(host, state, textarea, activeToggle, labels, groupOpenStates);
    };

    const markConfiguratorAsActive = () => {
      if (!activeToggle) {
        return;
      }

      if (hasMeaningfulConfiguration(state)) {
        activeToggle.checked = true;
      }
    };

    const touchGroupDefaults = (group) => {
      if (!group) {
        return;
      }

      if (group.selection === 'single') {
        let defaultFound = false;
        group.options.forEach((option) => {
          if (!option.default || defaultFound) {
            option.default = false;
            return;
          }

          defaultFound = true;
        });
      }
    };

    const normalizeStateDependencies = () => {
      reconcileGroupDependencies(state.groups);
    };

    const applyOptionMediaValue = (groupIndex, optionIndex, mediaField, mediaValue) => {
      if (
        !Number.isInteger(groupIndex)
        || !Number.isInteger(optionIndex)
        || !['image', 'texture'].includes(String(mediaField || ''))
        || groupIndex < 0
        || optionIndex < 0
        || groupIndex >= state.groups.length
        || optionIndex >= state.groups[groupIndex].options.length
      ) {
        return false;
      }

      state.groups[groupIndex].options[optionIndex][mediaField] = String(mediaValue || '');

      return true;
    };

    const syncColorTwinInput = (target) => {
      if (!(target instanceof HTMLInputElement)) {
        return;
      }

      if ((target.getAttribute('data-option-field') || '') !== 'color') {
        return;
      }

      const groupIndex = target.getAttribute('data-group-index');
      const optionIndex = target.getAttribute('data-option-index');
      const role = target.getAttribute('data-color-role');

      if (role === 'picker') {
        const textInput = host.querySelector(`input[data-color-role="text"][data-option-field="color"][data-group-index="${groupIndex}"][data-option-index="${optionIndex}"]`);
        if (textInput instanceof HTMLInputElement) {
          textInput.value = String(target.value || '').toLowerCase();
        }
        return;
      }

      if (role === 'text') {
        const pickerInput = host.querySelector(`input[data-color-role="picker"][data-option-field="color"][data-group-index="${groupIndex}"][data-option-index="${optionIndex}"]`);
        if (!(pickerInput instanceof HTMLInputElement)) {
          return;
        }

        const normalized = normalizeHexColor(target.value);
        if (normalized !== '') {
          pickerInput.value = normalized;
        }
      }
    };

    const handleMediaUpload = (target) => {
      if (!(target instanceof HTMLInputElement) || target.type !== 'file') {
        return;
      }

      const file = target.files && target.files[0] ? target.files[0] : null;
      if (!file || String(file.type || '').indexOf('image/') !== 0) {
        target.value = '';
        return;
      }

      const groupIndex = Number(target.getAttribute('data-group-index'));
      const optionIndex = Number(target.getAttribute('data-option-index'));
      const mediaField = String(target.getAttribute('data-media-field') || '').trim();

      const reader = new FileReader();
      reader.onload = () => {
        const mediaValue = typeof reader.result === 'string' ? reader.result : '';
        if (!applyOptionMediaValue(groupIndex, optionIndex, mediaField, mediaValue)) {
          target.value = '';
          return;
        }

        markConfiguratorAsActive();
        syncTextarea();
        rerender();
        target.value = '';
      };

      reader.onerror = () => {
        target.value = '';
      };

      reader.readAsDataURL(file);
    };

    const applyControlValue = (target) => {
      if (!(target instanceof HTMLInputElement) && !(target instanceof HTMLSelectElement)) {
        return false;
      }

      const groupIndex = Number(target.getAttribute('data-group-index'));
      const optionIndex = Number(target.getAttribute('data-option-index'));
      const groupField = target.getAttribute('data-group-field');
      const optionField = target.getAttribute('data-option-field');

      if (Number.isInteger(groupIndex) && groupIndex >= 0 && groupIndex < state.groups.length && groupField) {
        const group = state.groups[groupIndex];
      if (groupField === 'image_columns') {
        group.image_columns = normalizeImageColumns(target.value);
      } else {
        group[groupField] = target.type === 'checkbox' ? target.checked : target.value;
      }

      if (groupField === 'depends_on_group' && String(group.depends_on_group || '').trim() === '') {
        group.depends_on_option = '';
      }

      if (groupField === 'selection') {
        touchGroupDefaults(group);
      }

      return true;
      }

      if (
        Number.isInteger(groupIndex)
        && groupIndex >= 0
        && groupIndex < state.groups.length
        && Number.isInteger(optionIndex)
        && optionIndex >= 0
        && optionIndex < state.groups[groupIndex].options.length
        && optionField
      ) {
        const group = state.groups[groupIndex];
        const option = group.options[optionIndex];

        if (optionField === 'default') {
          option.default = target.checked;
          if (group.selection === 'single' && target.checked) {
            group.options.forEach((item, index) => {
              if (index !== optionIndex) {
                item.default = false;
              }
            });
          }
        } else {
          option[optionField] = target.value;
        }

        return true;
      }

      return false;
    };

    host.addEventListener('click', (event) => {
      const button = event.target.closest('[data-action]');
      if (!button) {
        return;
      }

      const action = button.getAttribute('data-action');
      const groupIndex = Number(button.getAttribute('data-group-index'));
      const optionIndex = Number(button.getAttribute('data-option-index'));

      if (action === 'add-group') {
        state.groups.push(createEmptyGroup(state.groups.length, labels));
        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'load-example') {
        const example = parseEditorState(JSON.stringify(createExampleSchema()), labels);
        state.version = example.version;
        state.groups = example.groups;
        state.preserved_groups = example.preserved_groups;
        state.base_object = example.base_object;
        state.invalid_source = false;
        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'clear-groups') {
        state.groups = [];
        normalizeStateDependencies();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'enable-configurator') {
        if (activeToggle) {
          activeToggle.checked = true;
        }
        rerender();
        return;
      }

      if (!Number.isInteger(groupIndex) || groupIndex < 0 || groupIndex >= state.groups.length) {
        return;
      }

      const group = state.groups[groupIndex];

      if (action === 'remove-group') {
        state.groups.splice(groupIndex, 1);
        normalizeStateDependencies();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'move-group-up') {
        moveArrayItem(state.groups, groupIndex, groupIndex - 1);
        normalizeStateDependencies();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'move-group-down') {
        moveArrayItem(state.groups, groupIndex, groupIndex + 1);
        normalizeStateDependencies();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'add-option') {
        group.options.push(createEmptyOption(groupIndex, group.options.length, labels));
        touchGroupDefaults(group);
        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
        rerender();
        return;
      }

      if (!Number.isInteger(optionIndex) || optionIndex < 0 || optionIndex >= group.options.length) {
        return;
      }

      if (action === 'pick-media') {
        const mediaField = String(button.getAttribute('data-media-field') || '').trim();
        const fileInput = host.querySelector(`input[data-action="upload-media"][data-media-field="${mediaField}"][data-group-index="${groupIndex}"][data-option-index="${optionIndex}"]`);
        if (fileInput instanceof HTMLInputElement) {
          fileInput.click();
        }
        return;
      }

      if (action === 'clear-media') {
        const mediaField = String(button.getAttribute('data-media-field') || '').trim();
        if (!applyOptionMediaValue(groupIndex, optionIndex, mediaField, '')) {
          return;
        }

        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'remove-option') {
        group.options.splice(optionIndex, 1);
        if (group.options.length === 0) {
          group.options.push(createEmptyOption(groupIndex, 0, labels));
        }
        touchGroupDefaults(group);
        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'move-option-up') {
        moveArrayItem(group.options, optionIndex, optionIndex - 1);
        normalizeStateDependencies();
        syncTextarea();
        rerender();
        return;
      }

      if (action === 'move-option-down') {
        moveArrayItem(group.options, optionIndex, optionIndex + 1);
        normalizeStateDependencies();
        syncTextarea();
        rerender();
      }
    });

    host.addEventListener('input', (event) => {
      const target = event.target;
      syncColorTwinInput(target);
      if (applyControlValue(target)) {
        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
      }
    });

    host.addEventListener('change', (event) => {
      const target = event.target;
      if (!(target instanceof HTMLInputElement) && !(target instanceof HTMLSelectElement)) {
        return;
      }

      if (target instanceof HTMLInputElement && target.getAttribute('data-action') === 'upload-media') {
        handleMediaUpload(target);
        return;
      }

      syncColorTwinInput(target);

      if (applyControlValue(target)) {
        normalizeStateDependencies();
        markConfiguratorAsActive();
        syncTextarea();
      }

      const groupField = target.getAttribute('data-group-field');
      const optionField = target.getAttribute('data-option-field');

      if (
        groupField === 'selection'
        || groupField === 'presentation'
        || groupField === 'image_columns'
        || groupField === 'type'
        || groupField === 'required'
        || groupField === 'depends_on_group'
        || groupField === 'depends_on_option'
        || groupField === 'label'
        || optionField === 'default'
        || optionField === 'label'
        || optionField === 'code'
      ) {
        rerender();
      }
    });

    textarea.addEventListener('change', () => {
      const parsedState = parseEditorState(textarea.value, labels);
      state.version = parsedState.version;
      state.groups = parsedState.groups;
      state.preserved_groups = parsedState.preserved_groups;
      state.base_object = parsedState.base_object;
      state.invalid_source = parsedState.invalid_source;
      normalizeStateDependencies();
      rerender();
    });

    rerender();
  };

  const boot = () => {
    const textarea = document.querySelector('.js-pz-productconfigurator-json');
    if (!textarea) {
      return;
    }

    initializeEditor(textarea);
  };

  document.addEventListener('DOMContentLoaded', boot);
}());

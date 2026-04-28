# PZ Product Configurator

Hybrydowy konfigurator produktu dla PrestaShop 9.

Cel modułu:
- zostawić natywne kombinacje tylko dla osi wymagających stocku, ceny bazowej, EAN lub zdjęć,
- przenieść kolory, dodatki i reguły kompatybilności do osobnej konfiguracji JSON,
- uniknąć eksplozji liczby kombinacji.

## Co już robi moduł

- dodaje wizualny edytor `Product configurator` na karcie produktu w Back Office,
- zapisuje konfigurację per produkt / sklep,
- waliduje i normalizuje JSON,
- pozwala zarządzać grupami i opcjami bez ręcznej edycji JSON,
- pilnuje aktywacji modułu przy realnej edycji konfiguratora w BO,
- renderuje dodatkowe grupy przy wariantach produktu przez hook `displayPZProductConfigurator`,
- ma fallback na `displayProductAdditionalInfo`, gdy aktywny motyw nie ma jeszcze własnego wpięcia konfiguratora przy wariantach,
- wystawia mapę dopłat dla natywnych atrybutów w skrypcie `#pz-productconfigurator-data`,
- przygotowuje tabele pod przyszły snapshot koszyka / zamówienia.

## Zasada modelowania

Używaj kombinacji tylko dla:
- rozmiaru,
- innych atrybutów, które naprawdę mają osobny stock lub logistykę.

Używaj modułu dla:
- kolorów,
- barierek,
- szuflad,
- stelaży,
- reguł kompatybilności,
- dopłat, które nie wymagają osobnej kombinacji.

## Schemat JSON

```json
{
  "version": 1,
  "native_attribute_impacts": {},
  "groups": [
    {
      "code": "group-1",
      "label": "Rozmiar łóżka",
      "type": "virtual_option",
      "selection": "single",
      "presentation": "text",
      "required": true,
      "none_label": "Nie chce tej opcji",
      "options": [
        {
          "code": "option-1-1",
          "label": "70x140",
          "price": 1120,
          "enabled": true
        },
        {
          "code": "option-1-2",
          "label": "70x150",
          "price": 1120,
          "enabled": true
        },
        {
          "code": "option-1-3",
          "label": "70x160",
          "price": 1120,
          "enabled": true
        },
        {
          "code": "option-1-4",
          "label": "80x150",
          "price": 1130,
          "enabled": true
        },
        {
          "code": "option-1-5",
          "label": "80x160",
          "price": 1130,
          "enabled": true
        },
        {
          "code": "option-1-6",
          "label": "80x170",
          "price": 1130,
          "enabled": true
        }
      ]
    },
    {
      "code": "group-2",
      "label": "Materac",
      "type": "virtual_option",
      "selection": "single",
      "presentation": "text",
      "required": false,
      "none_label": "Nie chcę materaca",
      "options": [
        {
          "code": "option-2-1",
          "label": "70x140",
          "price": 1120,
          "enabled": true
        },
        {
          "code": "option-2-2",
          "label": "70x150",
          "price": 1120,
          "enabled": true
        },
        {
          "code": "option-2-3",
          "label": "70x160",
          "price": 1120,
          "enabled": true
        },
        {
          "code": "option-2-4",
          "label": "80x150",
          "price": 1130,
          "enabled": true
        },
        {
          "code": "option-2-5",
          "label": "80x160",
          "price": 1130,
          "enabled": true
        },
        {
          "code": "option-2-6",
          "label": "80x170",
          "price": 1130,
          "enabled": true
        }
      ]
    },
    {
      "code": "group-6",
      "label": "Kolory",
      "type": "virtual_option",
      "selection": "single",
      "presentation": "image",
      "required": true,
      "none_label": "Nie chce tej opcji",
      "options": [
        {
          "code": "option-6-1",
          "label": "Orzech 22-62",
          "price": 0,
        },
        {
          "code": "option-3-2",
          "label": "Orzech 22-74",
          "price": 0
        }
      ]
    },
    {
      "code": "group-3",
      "label": "Barierka ochronna",
      "type": "virtual_option",
      "selection": "single",
      "presentation": "text",
      "required": false,
      "none_label": "Nie chcę barierki ochronnej",
      "options": [
        {
          "code": "option-3-1",
          "label": "Barierka A",
          "price": 0
        },
        {
          "code": "option-3-2",
          "label": "Barierka B",
          "price": 0
        }
      ]
    },
    {
      "code": "group-4",
      "label": "Szuflady",
      "type": "virtual_option",
      "selection": "single",
      "presentation": "text",
      "required": false,
      "none_label": "Nie chce szuflad",
      "options": [
        {
          "code": "option-4-1",
          "label": "Szuflada jednoczęściowa",
          "price": 420
        },
        {
          "code": "option-4-2",
          "label": "Szuflada dwuczęściowa",
          "price": 420
        }
      ]
    },
    {
      "code": "group-5",
      "label": "Stelaż",
      "type": "virtual_option",
      "selection": "single",
      "presentation": "text",
      "required": false,
      "none_label": "Nie chcę stelaża",
      "options": [
        {
          "code": "option-5-1",
          "label": "Stelaż Standardowy A",
          "price": 0
        },
        {
          "code": "option-5-2",
          "label": "Stelaż B z ręczną regulacją przy głowie (6 stopni  podnoszenia)",
          "price": 200
        }
      ]
    }
  ],
  "rules": []
}
```

## Typy grup

- `native_combination`: dane pomocnicze dla natywnych atrybutów Presty, bez renderowania dodatkowej grupy.
- `virtual_option`: opcja w konfiguratorze bez osobnego produktu.
- `addon_product`: opcja, która docelowo może zostać zmapowana na osobny produkt dodawany do koszyka.

## Prezentacje

- `text`
- `swatch`
- `image`

## Selekcja

- `single`
- `multiple`

## Następny krok

Ten commit przygotowuje fundament pod:
- silnik reguł `allow/deny`,
- serializację do customizacji koszyka, żeby różne konfiguracje nie scalały się w jedną linię,
- snapshot konfiguracji do zamówienia i stabilne liczenie ceny po stronie backendu.

JSON nadal istnieje jako tryb zaawansowany i format zapisu, ale domyślna obsługa ma odbywać się przez edytor wizualny.

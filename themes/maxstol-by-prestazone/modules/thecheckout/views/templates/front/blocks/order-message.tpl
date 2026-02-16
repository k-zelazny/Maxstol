{**
* NOTICE OF LICENSE
*
* This source file is subject to the Software License Agreement
* that is bundled with this package in the file LICENSE.txt.
*
*  @author    Peter Sliacky (Prestasmart)
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<div id="delivery">
  <label for="delivery_message">{l s='Additional informations' d='Maxstol.Theme.Checkout'}</label>
  {* <textarea rows="2" id="delivery_message" name="delivery_message" placeholder="{l s='Before entering the property, please have the courier call me. I have a dangerous dog.' d='Maxstol.Theme.Checkout'}">{$delivery_message|replace:'&#039;':'\''|replace:'&quot;':'"'}</textarea> *}
  <textarea rows="2" id="delivery_message" name="delivery_message">{$delivery_message|replace:'&#039;':'\''|replace:'&quot;':'"'}</textarea>
</div>

<a href="{$urls.pages.addresses}" class="btn-tertiary">
  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M11.9777 6.37768H7.62212V2.02212C7.62212 1.8571 7.55657 1.69884 7.43988 1.58215C7.32319 1.46546 7.16493 1.3999 6.9999 1.3999C6.83488 1.3999 6.67661 1.46546 6.55992 1.58215C6.44324 1.69884 6.37768 1.8571 6.37768 2.02212V6.37768H2.02212C1.8571 6.37768 1.69884 6.44324 1.58215 6.55992C1.46546 6.67661 1.3999 6.83488 1.3999 6.9999C1.3999 7.16493 1.46546 7.32319 1.58215 7.43988C1.69884 7.55657 1.8571 7.62212 2.02212 7.62212H6.37768V11.9777C6.37768 12.1427 6.44324 12.301 6.55992 12.4177C6.67661 12.5343 6.83488 12.5999 6.9999 12.5999C7.16493 12.5999 7.32319 12.5343 7.43988 12.4177C7.55657 12.301 7.62212 12.1427 7.62212 11.9777V7.62212H11.9777C12.1427 7.62212 12.301 7.55657 12.4177 7.43988C12.5343 7.32319 12.5999 7.16493 12.5999 6.9999C12.5999 6.83488 12.5343 6.67661 12.4177 6.55992C12.301 6.44324 12.1427 6.37768 11.9777 6.37768Z" fill="#111928"/>
  </svg>
  {l s='Add new delivery address' d='Maxstol.Theme.Checkout'}
</a>
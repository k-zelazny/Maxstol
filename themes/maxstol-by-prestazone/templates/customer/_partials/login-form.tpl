{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{block name='login_form'}

  {block name='login_form_errors'}
    {include file='_partials/form-errors.tpl' errors=$errors['']}
  {/block}

  <form id="login-form" class="form-validation" action="{block name='login_form_actionurl'}{$action}{/block}" method="post" novalidate>

    {block name='login_form_fields'}
      {foreach from=$formFields item="field"}
        {block name='form_field'}
          {form_field field=$field}
        {/block}
      {/foreach}
    {/block}
    
    <div class="auth-page__actions mb-3">
      <label for="remember-me">
        <input class="form-check-input" type="checkbox" id="remember-me" name="remember-me">
        <span></span>
        {l s='Remember me' d='Shop.Theme.Actions'}
      </label>
      <div class="login__forgot-password text-end">
        <a href="{$urls.pages.password}" rel="nofollow">
          {l s='Forgot your password?' d='Shop.Theme.Customeraccount'}
        </a>
      </div>
    </div>

    {block name='login_form_footer'}
      <input type="hidden" name="submitLogin" value="1">
      {block name='form_buttons'}
        <div class="d-grid">
          <button id="submit-login" class="btn btn-primary" data-link-action="sign-in" type="submit" class="form-control-submit">
            {l s='Sign in' d='Shop.Theme.Actions'}
          </button>
        </div>
      {/block}
    {/block}

  </form>
{/block}

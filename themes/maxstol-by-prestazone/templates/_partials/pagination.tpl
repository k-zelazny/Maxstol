{$componentName = 'pagination'}

<nav class="{$componentName}-container row">
  <div class="{$componentName}-list-container d-flex justify-content-center col-lg-12">
    {block name='pagination_page_list'}
      <nav aria-label="{l s='Products pagination' d='Shop.Theme.Catalog'}">
        {if $pagination.should_be_displayed}
          <ul class="pagination pagination--custom">
            {foreach from=$pagination.pages item="page" name="paginationLoop"}
              {if $page@iteration === 1}
                <li class="page-item">
                  <a rel="prev" href="{$page.url}"
                    class="page-link btn-with-icon previous {['disabled' => !$page.clickable, 'js-pager-link' => true]|classnames}">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M7.98974 11.3996C7.72508 11.3995 7.47128 11.2859 7.28416 11.0836L3.2922 6.76886C3.1051 6.56658 3 6.29226 3 6.00624C3 5.72021 3.1051 5.4459 3.2922 5.24361L7.28416 0.928916C7.37623 0.825892 7.48635 0.743716 7.60811 0.687184C7.72987 0.630651 7.86082 0.600895 7.99334 0.59965C8.12585 0.598406 8.25726 0.625698 8.37991 0.679934C8.50256 0.734171 8.61399 0.814266 8.70769 0.915546C8.8014 1.01683 8.8755 1.13726 8.92568 1.26983C8.97586 1.40239 9.00111 1.54443 8.99996 1.68766C8.99881 1.83088 8.97128 1.97243 8.91898 2.10403C8.86667 2.23563 8.79064 2.35466 8.69532 2.45416L5.40894 6.00624L8.69532 9.55831C8.83485 9.70917 8.92987 9.90135 8.96836 10.1106C9.00685 10.3198 8.98709 10.5366 8.91157 10.7337C8.83605 10.9308 8.70817 11.0992 8.54409 11.2177C8.38001 11.3363 8.1871 11.3996 7.98974 11.3996Z" fill="#6B7280"/>
                    </svg>
                    <span class="d-none d-xl-flex"></span>
                  </a>
                </li>
                
                {if $page.type === 'previous'}
                  {continue}
                {/if}
              {/if}

              {if $page.type === 'spacer'}
                <li class="page-item disabled">
                  <span class="page-link">&hellip;</span>
                </li>
              {else if $page.type != "prev" && $page.type != "next"}
                <li class="page-item{if $page.current} active{/if}" {if $page.current}aria-current="page" {/if}>
                  <a rel="nofollow" href="{$page.url}"
                    class="page-link btn-with-icon {['disabled' => !$page.clickable, 'js-pager-link' => true]|classnames}">
                    {$page.page}
                  </a>
                </li>
              {/if}

              {if $smarty.foreach.paginationLoop.last}
                <li class="page-item">
                  <a rel="next" href="{$page.url}"
                    class="page-link btn-with-icon next {['disabled' => !$page.clickable, 'js-pager-link' => true]|classnames}">
                    <span class="d-none d-xl-flex"></span>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4.01026 11.3996C3.8129 11.3996 3.61999 11.3363 3.45591 11.2177C3.29183 11.0992 3.16395 10.9308 3.08843 10.7337C3.01291 10.5366 2.99315 10.3198 3.03164 10.1106C3.07013 9.90135 3.16515 9.70917 3.30468 9.55831L6.59106 6.00624L3.30468 2.45416C3.20936 2.35466 3.13333 2.23563 3.08102 2.10403C3.02872 1.97243 3.00119 1.83088 3.00004 1.68766C2.99889 1.54443 3.02414 1.40239 3.07432 1.26983C3.1245 1.13726 3.1986 1.01683 3.29231 0.915546C3.38601 0.814266 3.49744 0.734171 3.62009 0.679934C3.74274 0.625698 3.87415 0.598406 4.00666 0.59965C4.13918 0.600895 4.27013 0.630651 4.39189 0.687184C4.51365 0.743716 4.62377 0.825892 4.71584 0.928916L8.7078 5.24361C8.8949 5.4459 9 5.72021 9 6.00624C9 6.29226 8.8949 6.56658 8.7078 6.76886L4.71584 11.0836C4.52872 11.2859 4.27492 11.3995 4.01026 11.3996Z" fill="#6B7280"/>
                    </svg>
                  </a>
                </li>
              {/if}
            {/foreach}
          </ul>
        {/if}
      </nav>
    {/block}
  </div>
</nav>

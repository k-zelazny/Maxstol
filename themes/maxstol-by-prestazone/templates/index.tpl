{extends file=$layout}

{block name='breadcrumb'}{/block}

{block name='content_columns'}
  {block name='left_column'}{/block}

  {block name='content_wrapper'}
    <div id="content-wrapper" class="wrapper__content">
      {hook h="displayContentWrapperTop"}

      {block name='content'}
          {block name='page_header_container'}
            {block name='page_title' hide}
              <header class="page-header">
                <h1 class="h1">{$smarty.block.child}</h1>
              </header>
            {/block}
          {/block}

          {block name='page_content_container'}
            <section id="content" class="page-content page-home">
              {block name='page_content_top'}{/block}

              {block name='page_content'}
                {block name='hook_home'}
                  {$HOOK_HOME nofilter}
                {/block}
              {/block}
            </section>
          {/block}
      {/block}

      {hook h="displayContentWrapperBottom"}
    </div>
  {/block}

  {block name='right_column'}{/block}
{/block}

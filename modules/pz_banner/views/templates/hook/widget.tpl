<div class="container">
  <section class="pz-banner">    
    <img width="" height="" src="{$urls.base_url}img/cms/pz-banner.jpg" alt="">
    
    <div class="pz-banner__meta">
      <h3 class="pz-banner__meta-title h3">
        Posiadamy<br> własną stolarnię
      </h3>
      <div class="pz-banner__meta-desc">
        <p>
          <strong>Jakość, nad którą mamy pełną kontrolę.</strong>
          </p>
        <p>
          Dzięki własnej stolarni mamy pełną kontrolę nad każdym etapem produkcji – od wyboru drewna po finalne wykończenie. Oznacza to, że nasze łóżka powstają z dbałością o każdy detal, zapewniając trwałość i estetykę, której możesz zaufać.
        </p>
      </div>
      <a href="#" class="pz-banner__meta-link btn-primary">
        Sprawdź nasze możliwości
      </a>
    </div>
  </section>
</div>

<style>
  .pz-banner {
    border: 1px solid #D3D3D3;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
    min-height: 539px;
    display: flex;
    align-items: center;
    padding-inline: 113px;
    padding-block: 32px;
  }

  .pz-banner::before {
    content: '';
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='1320' height='539' viewBox='0 0 1320 539' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23clip0_905_2)'%3E%3Cmask id='mask0_905_2' style='mask-type:luminance' maskUnits='userSpaceOnUse' x='0' y='0' width='1320' height='539'%3E%3Cpath d='M1320 0H0V539H1320V0Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask0_905_2)'%3E%3Cmask id='mask1_905_2' style='mask-type:luminance' maskUnits='userSpaceOnUse' x='-772' y='-810' width='1885' height='1858'%3E%3Cpath d='M-280.051 1047.37L1112.57 539.227L620.627 -809.007L-771.994 -300.867L-280.051 1047.37Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask1_905_2)'%3E%3Cpath d='M701.55 18.9519C537.336 64.6944 549 191.483 656.489 335.931C785.229 508.952 663.295 660.197 445.541 608.456C136.549 535.034 178.165 765.627 264.184 848.79L-280.048 1047.37L-771.999 -300.853L-169.265 -520.783C-31.8667 -398.313 215.297 -618.693 237.755 -307.562C253.996 -82.468 420.192 -209.872 462.897 -245.526C551.173 -319.234 720.573 -408.174 810.227 -273.204C888.629 -155.147 811.067 -11.5631 701.55 18.9519Z' fill='white'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_905_2'%3E%3Crect width='1320' height='539' fill='white'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E%0A");
    background-size: auto 100%;
    z-index: 2;
    display: block;
  }

  .pz-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    pointer-events: none;
  }

  .pz-banner .pz-banner__meta {
    position: relative;
    z-index: 3;
    max-width: 473px;
  }

  .pz-banner .pz-banner__meta-title {
    font-weight: 700;
    margin-block-end: 8px;
  }

  .pz-banner .pz-banner__meta-desc {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 360px;
  }

  .pz-banner .pz-banner__meta-desc > * {
    margin: 0;
  }

  .pz-banner .pz-banner__meta-desc :is(p, li) {
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 150%;
    color: #7D7D7D;
  }

  .pz-banner .pz-banner__meta-desc :is(b, strong) {
    font-weight: 700;
  }

  .pz-banner .pz-banner__meta-link {
    display: block;
    width: fit-content;
    margin-top: 32px;
  }

  @media (max-width: 991.98px) {
    .pz-banner {
      padding-inline: 60px;
    }
    
    .pz-banner::before {
      background-position: 20%;
    }
  }

  @media (max-width: 767.98px) {
    .pz-banner {
      align-items: flex-end;
      padding-inline: 24px;
    }
    
    .pz-banner::before {
      background-image: url("data:image/svg+xml,%3Csvg width='1320' height='539' viewBox='0 0 1320 539' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg clip-path='url(%23clip0_905_12)'%3E%3Cpath d='M-549.43 -141.726C1691 1122.47 2919.49 -354.942 3313.87 -3043.34C3786.39 -6263.41 6701.47 -6576.19 8472.98 -3700.43C10986.7 380.285 13003.3 -2524.44 12972.8 -4329.77L20899.3 -641.339L11762.1 18995L2983.5 14910.2C2824.2 12137.1 -2164.92 11851.3 915.487 8291.6C3144.14 5716.34 11.1586 5302.39 -825.005 5227.15C-2553.52 5071.69 -5310.25 4213.11 -4826.69 1816.16C-4403.53 -280.236 -2043.7 -984.751 -549.43 -141.726Z' fill='white'/%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_905_12'%3E%3Crect width='1320' height='539' fill='white'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E%0A");
      background-position: unset;
    }
    
    .pz-banner .pz-banner__meta, 
    .pz-banner .pz-banner__meta-desc {
      max-width: 100%;
    }
  }
</style>
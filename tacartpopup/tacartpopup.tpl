<!-- block cartpopup -->
<div style="display: none;">
    {if isset($products) AND $products}
    <div id="blocktacartpopup_footer_hook" class="block products_block clearfix">
        <h2 class="title_block">{l s='Related products' mod='cartpopup'}</h2>
            <div class="block_content">
                    {assign var='liHeight' value=250}
                    {assign var='nbItemsPerLine' value=6}
                    {assign var='nbLi' value=$products|@count}
                    {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
                    {math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
                    <ul style="height:{$ulHeight}px;">
                    {foreach from=$products item=product name=relatedProducts}
                            {math equation="(total%perLine)" total=$smarty.foreach.relatedProducts.total perLine=$nbItemsPerLine assign=totModulo}
                            {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                            <li class="ajax_block_product {if $smarty.foreach.relatedProducts.first}first_item{elseif $smarty.foreach.relatedProducts.last}last_item{else}item{/if} {if $smarty.foreach.relatedProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.relatedProducts.iteration%$nbItemsPerLine == 1} {/if} {if $smarty.foreach.relatedProducts.iteration > ($smarty.foreach.relatedProducts.total - $totModulo)}last_line{/if}">
                                    <a href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}" />{if isset($product.new) && $product.new == 1}<span class="new">{l s='New' mod='cartpopup'}</span>{/if}</a>
                                    <h5 class="s_title_block"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h5>
                                    <div class="product_desc"><a href="{$product.link|escape:'html'}" title="{l s='More' mod='cartpopup'}">{$product.description_short|strip_tags|truncate:65:'...'}</a></div>
                                    <div>
                                            <a class="lnk_more" href="{$product.link|escape:'html'}" title="{l s='View' mod='cartpopup'}">{l s='View' mod='cartpopup'}</a>
                                            {if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<p class="price_container"><span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></p>{else}<div style="height:21px;"></div>{/if}
                                            <div style="height:23px;"></div>
                                    </div>
                            </li>
                    {/foreach}
                    </ul>
            </div>
        <div class="buttons">
            <a href="#" class="exclusive closefancybox" title="{l s='Back' mod='cartpopup'}" rel="nofollow"><span></span>{l s='Back' mod='cartpopup'}</a>
            <a href="{$link->getPageLink('order', true)|escape:'html'}" class="exclusive" title="{l s='Go to cart' mod='cartpopup'}" rel="nofollow"><span></span>{l s='Go to cart' mod='cartpopup'}</a>
        </div>
    </div>
    {/if}
</div>
<!-- /block cartpopup -->

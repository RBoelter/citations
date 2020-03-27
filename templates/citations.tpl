<div class="item citations">
	<div class="citations-count">
        {if $citationsProvider == 'crossref' || $citationsProvider == 'all'}
			<div>
				<img class="img-fluid" src="{$citationsImagePath}crossref.png" alt="Crossref"/><br/>
                {$citationsList['crossref_count']}
			</div>
        {/if}
        {if $citationsProvider == 'scopus' || $citationsProvider == 'all'}
			<div>
                {if $citationsList['scopus_url']}
					<a href="{$citationsList['scopus_url']}" target="_blank" rel="noreferrer">
						<img src="{$citationsImagePath}scopus.png" alt="Scopus"/>
					</a>
                {else}
					<img src="{$citationsImagePath}scopus.png" alt="Scopus"/>
                {/if}
				<br/>
                {$citationsList['scopus_count']}
			</div>
        {/if}
	</div>
    {assign var="citationsProviderList" value="{$citationsProvider}_list"}
    {if $citationsList[$citationsProviderList]}
		<div class="citations-list">
            {foreach from=$citationsList[$citationsProviderList] item="itm"}
				<div>
					<img src="{$citationsImagePath}{$itm["type"]}.png" alt="Crossref" style="max-width: 60px;"/>
					<div>
                        {$itm["authors"]}&nbsp;({$itm["year"]})
					</div>
					<div>
						<span class="font-weight-bold">{$itm["article_title"]}</span>
                        {$itm["journal_title"]}{if $itm["volume"]!=''}&nbsp;{$itm["volume"]}:{/if}{if $itm["first_page"]!=''}&nbsp;{$itm["first_page"]}{/if}.
					</div>
					<div>
                        {if $itm["doi"]!=''}
							<a href="https://doi.org/{$itm["doi"]}" target="_blank" rel="noreferrer">
                                {$itm["doi"]}
							</a>
                        {/if}
					</div>
				</div>
            {/foreach}
		</div>
    {/if}

	<style>
		.citations-count {
			display: grid;
			grid-template-columns: 1fr 1fr;
			text-align: center;
			font-weight: bold;
		}

		.citations-count div:last-child {
			position: relative;
			top: 17px;
		}

		.citations-count img {
			max-width: 90%;
		}

		.citations {
			max-height: 500px;
			overflow-y: auto;
			overflow-x: hidden;
		}

		.citations-list > div {
			margin: 15px 5px;
		}

		.citations-list a {
			overflow: hidden;
			white-space: nowrap;
		}

		.font-weight-bold {
			font-weight: bold;
		}

		/*.doi-txt {
			position: relative;
			bottom: 10px;
			left: 2px;
		}*/

		.citations-table {
			text-align: center;
			font-weight: bold;
		}

		.citations-table tr {
			margin: 0;
		}
	</style>
</div>

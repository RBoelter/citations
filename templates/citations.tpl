<div class="item citations-container" data-image-path="{$citationsImagePath}" data-citations-url="{url page="citations" op="get" params=$citationsArgsList}"
     data-citations-provider="{$citationsProvider}" data-citations-total="{$citationsShowTotal}" data-citations-list="{$citationsShowList}">
	<div id="citations-loader"></div>
	<div class="citations-count">
		<div class="citations-count-crossref">
			<img class="img-fluid" src="{$citationsImagePath}crossref.png" alt="Crossref"/>
			<br/>
			<span class="badge"> {$citationsList['crossref_count']}</span>
		</div>
		<div class="citations-count-scopus">
			<img src="{$citationsImagePath}scopus.png" alt="Scopus"/>
			<br/>
			<span class="badge">{$citationsList['scopus_count']}</span>
		</div>
	</div>
	<div class="citations-list"></div>
	<style>
		{if $citationsMaxHeight && intval($citationsMaxHeight)>0}
			.citations-container {
				overflow-y: auto;
				overflow-x: hidden;
				max-height: {intval($citationsMaxHeight)}px;
			}
		{/if}
		{if $citationsProvider!='all'}
			.citations-count {
				grid-template-columns: 1fr;
			}
			.citations-count img {
				max-width: 50%;
			}
		{/if}
	</style>
</div>

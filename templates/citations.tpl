<div class="item citations-container" data-image-path="{$citationsImagePath}" data-citations-url="{url page="citations" op="get" params=$citationsArgsList}"
     data-citations-provider="{$citationsProvider}" data-citations-total="{$citationsShowTotal}" data-citations-list="{$citationsShowList}">
	<div class="citations-count">
		<div class="citations-count-crossref">
			<img class="img-fluid" src="{$citationsImagePath}crossref.png" alt="Crossref"/>
			<br/>
			<span class="badge"> {$citationsList['crossref_count']}</span>
		</div>
		<div class="citations-count-scopus">
			<a href="#" target="_blank" rel="noreferrer">
				<img src="{$citationsImagePath}scopus.png" alt="Scopus"/>
			</a>
			<br/>
			<span class="badge">{$citationsList['scopus_count']}</span>
		</div>
	</div>
	<div class="citations-list"></div>
</div>

<div class="item citations-container" data-image-path="{$citationsImagePath}" data-citations-url="{url page="citations" op="get" params=$citationsArgsList}"
     data-citations-provider="{$citationsProvider}" data-citations-total="{$citationsShowTotal}" data-citations-list="{$citationsShowList}"
     data-show-google="{$citationsShowGoogle}" data-show-pmc="{$citationsShowPmc}">
	<div id="citations-loader"></div>
	<div class="citations-count">
		<div class="citations-count-crossref">
			<img class="img-fluid" src="{$citationsImagePath}crossref.png" alt="Crossref"/>
			<div class="badge_total"></div>
		</div>
		<div class="citations-count-scopus">
			<img src="{$citationsImagePath}scopus.png" alt="Scopus"/>
			<br/>
			<span class="badge_total"></span>
		</div>
		<div class="citations-count-google">
			<a href="https://scholar.google.com/scholar?q={$citationsId}" target="_blank" rel="noreferrer">
				<img src="{$citationsImagePath}scholar.png" alt="Google Scholar"/>
			</a>
		</div>
		<div class="citations-count-pmc">
			<a href="http://europepmc.org/search?scope=fulltext&query=(REF:{$citationsId})" target="_blank" rel="noreferrer">
				<img src="{$citationsImagePath}pmc.png" alt="Europe PMC"/>
				<br/>
				<span class="badge_total"></span>
			</a>
		</div>
	</div>
	<div class="citations-list"></div>
    {if $citationsMaxHeight && intval($citationsMaxHeight)>0}
		<style>
			.citations-container {
				overflow-y: auto;
				overflow-x: hidden;
				max-height: {intval($citationsMaxHeight)}px;
			}
		</style>
    {/if}
</div>

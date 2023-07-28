<div id="citation-plugin" class="item citations-container" data-citations-url="{url page="citations" op="get" params=$urlArgs}" data-img-url="{$imagePath}">
    <div id="citations-loader"></div>
    <div class="citations-count">
        <div class="citations-count-crossref">
            <img class="img-fluid" src="{$imagePath}crossref.png" alt="Crossref"/>
            <div class="badge_total"></div>
        </div>
        <div class="citations-count-scopus">
            <img src="{$imagePath}scopus.png" alt="Scopus"/>
            <br/>
            <span class="badge_total"></span>
        </div>
        <div class="citations-count-google">
            <a href="https://scholar.google.com/scholar?q={$urlArgs['doi']}" target="_blank" rel="noreferrer">
                <img src="{$imagePath}scholar.png" alt="Google Scholar"/>
            </a>
        </div>
        <div class="citations-count-europepmc">
            <a href="https://europepmc.org/search?scope=fulltext&query={$urlArgs['doi']}" target="_blank" rel="noreferrer">
                <img src="{$imagePath}pmc.png" alt="Europe PMC"/>
                <br/>
                <span class="badge_total"></span>
            </a>
        </div>
    </div>
    <div class="citations-list">
        <div class="cite-itm cite-prototype" style="display: none">
            <img class="cite-img img-fluid" src="" alt="">
            <div>
                <span class="cite-author"></span>
                <span class="cite-date"></span>
            </div>
            <div>
                <span class="cite-title"></span>
                <span class="cite-info"></span>
            </div>
            <div class="cite-doi"></div>
        </div>
    </div>
    {if $maxHeight && intval($maxHeight)>0}
        <style>
            .citations-container {
                overflow-y: auto;
                overflow-x: hidden;
                max-height: {intval($maxHeight)}px;
            }
        </style>
    {/if}
</div>

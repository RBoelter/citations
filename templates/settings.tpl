<script>
	$(function () {ldelim}
		$('#citationsSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});

	document.querySelectorAll('.checkNum').forEach(function (el) {ldelim}
		el.addEventListener("input", elem => el.value = (isNaN(el.value)) ? el.value.replace(elem.data, '') : el.value);
        {rdelim})
</script>
<style>
	.key-saved{
		border: 1px solid deepskyblue !important;
	}
</style>
<form
		class="pkp_form"
		id="citationsSettings"
		method="POST"
		action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
    {csrf}
    {fbvFormArea id="citationsSettings"}
	    {fbvFormSection list=true label="plugins.generic.citations.provider" description="plugins.generic.citations.provider.desc"}
	        {fbvElement type="select" id="citationsProvider" from=$citationsProviderOptions selected=$citationsProvider size=$fbvStyles.size.SMALL}
	    {/fbvFormSection}
	    <div id="api_description">{translate key="plugins.generic.citations.api.desc"}</div>
	    <br/>
	    {fbvFormSection label="plugins.generic.citations.scopus" description="plugins.generic.citations.scopus.desc"}
	        {fbvElement type="text" id="citationsScopusKey" class=$citationsScopusSaved value=$citationsScopusKey label="plugins.generic.citations.scopus.key"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.crossref" description="plugins.generic.citations.crossref.desc"}
	        {fbvElement type="text" id="citationsCrossrefUser" class=$citationsCrossrefUserSaved value=$citationsCrossrefUser label="plugins.generic.citations.crossref.name" inline=true}
            {fbvElement type="text" id="citationsCrossrefPwd" class=$citationsCrossrefPwdSaved value=$citationsCrossrefPwd label="plugins.generic.citations.crossref.pwd" inline=true}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.total" list=true description="plugins.generic.citations.show.total.desc"}
	        {fbvElement type="checkbox" id="citationsShowTotal" value="1" checked=$citationsShowTotal label="plugins.generic.citations.show.total.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.list" list=true description="plugins.generic.citations.show.list.desc"}
            {fbvElement type="checkbox" id="citationsShowList" value="1" checked=$citationsShowList label="plugins.generic.citations.show.list.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.google" list=true description="plugins.generic.citations.show.google.desc"}
	        {fbvElement type="checkbox" id="citationsShowGoogle" value="1" checked=$citationsShowGoogle label="plugins.generic.citations.show.google.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.pmc" list=true description="plugins.generic.citations.show.pmc.desc"}
	        {fbvElement type="checkbox" id="citationsShowPmc" value="1" checked=$citationsShowPmc label="plugins.generic.citations.show.pmc.check"}
	    {/fbvFormSection}
	    {fbvFormSection title="plugins.generic.citations.max.height"}
	        {fbvElement type="text" id="citationsMaxHeight" class="checkNum" value=$citationsMaxHeight label="plugins.generic.citations.max.height.desc"}
	    {/fbvFormSection}

    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
</form>

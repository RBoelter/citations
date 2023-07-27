<script>
	$(function () {ldelim}
		$('#citationsSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});

	document.querySelectorAll('.checkNum').forEach(function (el) {ldelim}
		el.addEventListener("input", elem => el.value = (isNaN(el.value)) ? el.value.replace(elem.data, '') : el.value);
        {rdelim})
</script>
<form
		class="pkp_form"
		id="citationsSettings"
		method="POST"
		action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
    {csrf}
    {fbvFormArea id="citationsSettings"}
	    {fbvFormSection list=true label="plugins.generic.citations.provider" description="plugins.generic.citations.provider.desc"}
	        {fbvElement type="select" id="provider" from=$citationsProviderOptions selected=$provider size=$fbvStyles.size.SMALL}
	    {/fbvFormSection}
	    <div id="api_description">{translate key="plugins.generic.citations.api.desc"}</div>
	    <br/>
	    {fbvFormSection label="plugins.generic.citations.scopus" description="plugins.generic.citations.scopus.desc"}
	        {fbvElement type="text" password="true" id="scopusKey" class=$citationsScopusSaved value=$scopusKey label="plugins.generic.citations.scopus.key"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.crossref" description="plugins.generic.citations.crossref.desc"}
	        {fbvElement type="text" password="true" id="crossrefUser" class=$citationsCrossrefUserSaved value=$crossrefUser label="plugins.generic.citations.crossref.name" inline=true}
            {fbvElement type="text" password="true" id="crossrefPwd" class=$citationsCrossrefPwdSaved value=$crossrefPwd label="plugins.generic.citations.crossref.pwd" inline=true}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.total" list=true description="plugins.generic.citations.show.total.desc"}
	        {fbvElement type="checkbox" id="showTotal" value="1" checked=$showTotal label="plugins.generic.citations.show.total.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.list" list=true description="plugins.generic.citations.show.list.desc"}
            {fbvElement type="checkbox" id="showList" value="1" checked=$showList label="plugins.generic.citations.show.list.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.google" list=true description="plugins.generic.citations.show.google.desc"}
	        {fbvElement type="checkbox" id="showGoogle" value="1" checked=$showGoogle label="plugins.generic.citations.show.google.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.pmc" list=true description="plugins.generic.citations.show.pmc.desc"}
	        {fbvElement type="checkbox" id="showPmc" value="1" checked=$showPmc label="plugins.generic.citations.show.pmc.check"}
	    {/fbvFormSection}
	    {fbvFormSection title="plugins.generic.citations.max.height"}
	        {fbvElement type="text" id="maxHeight" class="checkNum" value=$maxHeight label="plugins.generic.citations.max.height.desc"}
	    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
</form>

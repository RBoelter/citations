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
    {fbvFormArea}
	    {fbvFormSection list=true label="plugins.generic.citations.provider" description="plugins.generic.citations.provider.desc"}
	        {fbvElement type="select" id="citationsProvider" from=$citationsProviderOptions selected=$citationsProvider size=$fbvStyles.size.SMALL}
	    {/fbvFormSection}
	    {fbvFormSection title="plugins.generic.citations.scopus.key" }
	        {fbvElement type="text" id="citationsScopusKey" value=$citationsScopusKey label="plugins.generic.citations.scopus.key.desc" size=$fbvStyles.size.SMALL}
            {fbvElement type="hidden" id="citationsScopusKeyS" value=$citationsScopusKeyS}
	    {/fbvFormSection}
	    {fbvFormSection title="plugins.generic.citations.crossref" }
	        {fbvElement type="text" id="citationsCrossrefUser" value=$citationsCrossrefUser label="plugins.generic.citations.crossref.name.desc" inline=true}
            {fbvElement type="hidden" id="citationsCrossrefUserS" value=$citationsCrossrefUserS}
            {fbvElement type="text" id="citationsCrossrefPwd" value=$citationsCrossrefPwd label="plugins.generic.citations.crossref.pwd.desc" inline=true}
            {fbvElement type="hidden" id="citationsCrossrefPwdS" value=$citationsCrossrefPwdS}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.total" list=true description="plugins.generic.citations.show.total.desc"}
	        {fbvElement type="checkbox" id="citationsShowTotal" value="1" checked=$citationsShowTotal label="plugins.generic.citations.show.total.check"}
	    {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.citations.show.list" list=true description="plugins.generic.citations.show.list.desc"}
            {fbvElement type="checkbox" id="citationsShowList" value="1" checked=$citationsShowList label="plugins.generic.citations.show.list.check"}
	    {/fbvFormSection}
	    {fbvFormSection title="plugins.generic.citations.max.height"}
	        {fbvElement type="text" id="citationsMaxHeight" class="checkNum" value=$citationsMaxHeight label="plugins.generic.citations.max.height.desc" size=$fbvStyles.size.SMALL}
	    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
</form>
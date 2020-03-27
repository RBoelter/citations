<script>
	$(function () {ldelim}
		$('#mostViewedSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});

	document.querySelectorAll('.checkNum').forEach(function (el) {ldelim}
		el.addEventListener("input", elem => el.value = (isNaN(el.value)) ? el.value.replace(elem.data, '') : el.value);
        {rdelim})
</script>
<form
		class="pkp_form"
		id="mostViewedSettings"
		method="POST"
		action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
    {csrf}
    {fbvFormArea}
        {fbvFormSection title="plugins.generic.most.viewed.head"}
            {fbvElement type="text" id="mostViewedTitle"  value=$mostViewedTitle label="plugins.generic.most.viewed.head.desc"}
        {/fbvFormSection}
        {fbvFormSection title="plugins.generic.most.viewed.days"}
            {fbvElement type="text" id="mostViewedDays" required="true" class="checkNum" value=$mostViewedDays label="plugins.generic.most.viewed.days.desc"}
        {/fbvFormSection}
        {fbvFormSection title="plugins.generic.most.viewed.amount"}
            {fbvElement type="text" id="mostViewedAmount" required="true" class="checkNum" value=$mostViewedAmount label="plugins.generic.most.viewed.amount.desc"}
        {/fbvFormSection}
        {fbvFormSection title="plugins.generic.most.viewed.years"}
            {fbvElement type="text" id="mostViewedYears" class="checkNum" value=$mostViewedYears label="plugins.generic.most.viewed.years.desc"}
        {/fbvFormSection}
	    {fbvFormSection label="plugins.generic.most.viewed.position" list=true description="plugins.generic.most.viewed.position.desc"}
            {fbvElement type="checkbox" id="mostViewedPosition" value="1" checked=$mostViewedPosition label="plugins.generic.most.viewed.position.check"}
	    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
</form>
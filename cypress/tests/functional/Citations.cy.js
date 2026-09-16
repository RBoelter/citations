/* npx cypress open  --config integrationFolder=plugins/generic/openid/cypress/tests */
describe('Scopus/Crossref Plugin tests', function () {

	it('Disable Scopus/Crossref Plugin', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		// OJS 3.5's top nav is a client-side Vue menu with no stable selector
		// for a fresh page load, so visit the settings page directly instead
		// of clicking through it.
		cy.visit('index.php/publicknowledge/management/settings/website');
		cy.get('button[id="plugins-button"]').click();
		// disable plugin if enabled
		cy.get('input[id^="select-cell-citationsplugin-enabled"]')
			.then($btn => {
				if ($btn.attr('checked') === 'checked') {
					cy.get('input[id^="select-cell-citationsplugin-enabled"]').click();
					// The confirm dialog is a Vue component in 3.5 (no more
					// pkp_modal_panel/pkpModalConfirmButton).
					cy.contains('[data-cy="dialog"] button', 'OK').click();
					cy.get('div:contains(\'The plugin "Scopus/Crossref Plugin" has been disabled.\')');
				}
			});
	});

	it('Enable Scopus/Crossref Plugin', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		cy.visit('index.php/publicknowledge/management/settings/website');
		cy.get('button[id="plugins-button"]').click();
		// Find and enable the plugin
		cy.get('input[id^="select-cell-citationsplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Scopus/Crossref Plugin" has been enabled.\')');
	});
});

/* npx cypress open  --config integrationFolder=plugins/generic/openid/cypress/tests */
describe('Scopus/Crossref Plugin tests', function () {

	it('Disable Scopus/Crossref Plugin', function () {
		cy.login(Cypress.env("ojs_username"), Cypress.env("ojs_password"), Cypress.env("context"));
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// disable plugin if enabled
		cy.get('input[id^="select-cell-citationsplugin-enabled"]')
			.then($btn => {
				if ($btn.attr('checked') === 'checked') {
					cy.get('input[id^="select-cell-citationsplugin-enabled"]').click();
					cy.get('div[class*="pkp_modal_panel"] button[class*="pkpModalConfirmButton"]').click();
					cy.get('div:contains(\'The plugin "Scopus/Crossref Plugin" has been disabled.\')');
				}
			});
	});

	it('Enable Scopus/Crossref Plugin', function () {
		cy.login(Cypress.env("ojs_username"), Cypress.env("ojs_password"), Cypress.env("context"));
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		// Find and enable the plugin
		cy.get('input[id^="select-cell-citationsplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Scopus/Crossref Plugin" has been enabled.\')');
	});
});

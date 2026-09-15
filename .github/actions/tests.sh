#!/bin/bash
# Copy into a plugin repo as .github/actions/tests.sh (same content works on
# every branch - it only cares about the plugin's own path). Sourced by
# pkp-github-actions' run-plugin-actions.sh once the app + this plugin are
# installed and built. Pattern verified against pkp/quickSubmit and
# pkp/oaiJats's real tests.sh.
set -e

npx cypress run --headless --browser chrome \
  --config '{"specPattern":["plugins/generic/citations/cypress/tests/functional/*.cy.js"]}'

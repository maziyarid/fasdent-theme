#!/usr/bin/env bash
set -euo pipefail
THEME=${1:-}; PLUGIN=${2:-}
[[ -n "$THEME" && -n "$PLUGIN" ]] || { echo "usage: $0 THEME PLUGIN" >&2; exit 64; }
fail=0
pass(){ printf 'PASS\t%s\n' "$1"; }
bad(){ printf 'FAIL\t%s\n' "$1"; fail=1; }

while IFS= read -r -d '' f; do
  php -l "$f" >/dev/null 2>&1 || bad "php_lint:$f"
done < <(find "$THEME" "$PLUGIN" -type f -name '*.php' -print0)
[[ $fail -eq 0 ]] && pass "php_lint_all:$(php -r 'echo PHP_VERSION;')"

if command -v node >/dev/null 2>&1; then
  node --check "$THEME/assets/js/theme.js" >/dev/null && pass theme_js_syntax || bad theme_js_syntax
else
  pass node_not_available_js_runtime_pending
fi

# The 1.3.2 incident proved php -l alone cannot detect a typo such as literal
# "\\tWP_CLI::..." because PHP parses it as a different fully-qualified class.
# Fail executable PHP containing an escaped-whitespace-looking class token at
# line indentation, and explicitly fail the historical WP_CLI variants.
if grep -RIn --include='*.php' -E '^[[:space:]]*\\[tnr][A-Za-z_][A-Za-z0-9_]*::' "$THEME" "$PLUGIN" >/tmp/escaped-class.$$ 2>/dev/null; then
  cat /tmp/escaped-class.$$
  bad suspicious_literal_escape_class_token
else
  pass no_suspicious_literal_escape_class_token
fi
rm -f /tmp/escaped-class.$$
if grep -RInF --include='*.php' '\tWP_CLI' "$THEME" "$PLUGIN" >/tmp/wpcli-literal.$$ 2>/dev/null || \
   grep -RInF --include='*.php' '\nWP_CLI' "$THEME" "$PLUGIN" >>/tmp/wpcli-literal.$$ 2>/dev/null || \
   grep -RInF --include='*.php' '\rWP_CLI' "$THEME" "$PLUGIN" >>/tmp/wpcli-literal.$$ 2>/dev/null; then
  cat /tmp/wpcli-literal.$$
  bad wp_cli_literal_escape_typo
else
  pass no_wp_cli_literal_escape_typo
fi
rm -f /tmp/wpcli-literal.$$

if grep -RIn --include='*.php' --include='*.js' --include='*.css' -E 'drkeyvanalipasandi\.com|drkeyvanalipasandi@gmail\.com|maps\.google|google\.com/maps|goo\.gl/maps' "$THEME" "$PLUGIN" >/tmp/stale.$$ 2>/dev/null; then
  cat /tmp/stale.$$
  bad stale_runtime_reference
else
  pass no_stale_runtime_reference
fi
rm -f /tmp/stale.$$

grep -q '^Version: 1\.4\.28$' "$THEME/style.css" && pass theme_header_1.4.28 || bad theme_header
grep -q '^Stable tag: 1\.4\.28$' "$THEME/readme.txt" && pass theme_readme_1.4.28 || bad theme_readme
grep -q '^ \* Version: 1\.3\.3$' "$PLUGIN/alipasandi-service-content.php" && pass plugin_header_1.3.3 || bad plugin_header
grep -q '^Stable tag: 1\.3\.3$' "$PLUGIN/readme.txt" && pass plugin_readme_1.3.3 || bad plugin_readme
grep -q '^Requires PHP: 8\.2$' "$THEME/style.css" && grep -q '^ \* Requires PHP: 8\.2$' "$PLUGIN/alipasandi-service-content.php" && pass minimum_php_8.2_consistent || bad minimum_php_consistency
grep -q "ALIPASANDI_THEME_VERSION', '1.4.28'" "$THEME/functions.php" && pass theme_constant || bad theme_constant
grep -q "ALIPASANDI_SERVICE_PLUGIN_MIN_VERSION', '1.3.3'" "$THEME/functions.php" && pass theme_plugin_min || bad theme_plugin_min
grep -q "ALIPASANDI_SERVICE_PRODUCTION_THEME_MIN', '1.4.28'" "$PLUGIN/includes/compatibility.php" && pass plugin_pairing || bad plugin_pairing

# WP-CLI registration and schema-repair safety.
grep -q "WP_CLI::add_command( 'alipasandi service migrate'" "$PLUGIN/includes/service-content.php" && \
grep -q "WP_CLI::add_command( 'alipasandi service schema-repair'" "$PLUGIN/includes/service-content.php" && \
grep -q "WP_CLI::add_command( 'alipasandi service health'" "$PLUGIN/includes/service-content.php" && pass wp_cli_commands_source || bad wp_cli_commands_source
grep -q "production_apply_forbidden_use_staging_clone_with_backup" "$PLUGIN/includes/service-content.php" && pass schema_apply_production_guard || bad schema_apply_production_guard
grep -q "confirm-plan-sha" "$PLUGIN/includes/service-content.php" && grep -q "schema_repair_apply_requires_matching_dry_run_plan_sha" "$PLUGIN/includes/service-content.php" && pass schema_apply_requires_confirmed_dry_run || bad schema_apply_requires_confirmed_dry_run
if grep -q "admin_post_alipasandi_service_schema_repair" "$PLUGIN/includes/service-content.php"; then bad schema_apply_admin_handler_present; else pass schema_apply_cli_only; fi

# Rank Math observation freshness + static front-page guard.
grep -q "'rank_math_version' => (string) RANK_MATH_VERSION" "$PLUGIN/includes/site-settings.php" && \
grep -q "'observed_at'" "$PLUGIN/includes/site-settings.php" && pass rank_math_observation_version_timestamp || bad rank_math_observation_version_timestamp
grep -q 'rank_math_local_entity_observation_version_mismatch' "$PLUGIN/includes/health-checks.php" && \
grep -q 'rank_math_local_entity_observation_stale_or_missing' "$PLUGIN/includes/health-checks.php" && pass rank_math_observation_freshness_health || bad rank_math_observation_freshness_health
grep -q 'front_page_mode_not_static' "$PLUGIN/includes/health-checks.php" && \
grep -q 'front_page_missing_or_invalid' "$PLUGIN/includes/health-checks.php" && \
grep -q 'blog_page_missing_or_invalid' "$PLUGIN/includes/health-checks.php" && pass static_front_page_health || bad static_front_page_health

# Production debug visibility guard.
grep -q 'production_debug_display_enabled' "$PLUGIN/includes/health-checks.php" && pass production_debug_display_health || bad production_debug_display_health

# Stable-key registry path.
grep -q 'alipasandi_bookable_services()' "$THEME/page-appointments.php" && grep -q 'value="<?php echo esc_attr( \$service_key ); ?>"' "$THEME/page-appointments.php" && pass appointment_ui_stable_key || bad appointment_ui_stable_key
grep -q "in_array( \$fields\['service'\], alipasandi_allowed_services(), true )" "$PLUGIN/includes/forms.php" && grep -q "alipasandi_service_label( \$fields\['service'\] )" "$PLUGIN/includes/forms.php" && pass appointment_server_stable_key || bad appointment_server_stable_key
if grep -RIn --include='*.php' 'function alipasandi_appointment_service_extras' "$THEME" "$PLUGIN" >/dev/null; then bad secondary_appointment_list; else pass no_secondary_appointment_list; fi
grep -q 'duplicate_label:' "$PLUGIN/includes/service-registry.php" && pass duplicate_label_warning || bad duplicate_label_warning
grep -q 'registry_page_missing:' "$PLUGIN/includes/health-checks.php" && pass registry_page_existence_health || bad registry_page_existence_health

# Time/hours policy.
grep -q 'alipasandi_validate_appointment_datetime' "$PLUGIN/includes/forms.php" && pass same_day_datetime_backend || bad same_day_datetime_backend
grep -q 'alipasandi_booking_min_lead_minutes' "$PLUGIN/includes/validators.php" && pass lead_time_ssot || bad lead_time_ssot
grep -q 'data-booking-policy' "$THEME/page-appointments.php" && grep -q 'isWithinWeeklyHours' "$THEME/assets/js/theme.js" && grep -q 'isStrictlyFutureSlot' "$THEME/assets/js/theme.js" && pass date_aware_time_ui || bad date_aware_time_ui
if grep -n '365' "$THEME/page-appointments.php" >/dev/null; then bad theme_horizon_literal; else pass no_theme_horizon_literal; fi

# SEO failure mode.
grep -q 'function alipasandi_emergency_robots_policy' "$THEME/inc/seo.php" && pass emergency_robots || bad emergency_robots
grep -q 'wp_sitemaps_enabled' "$THEME/inc/seo.php" && pass emergency_core_sitemap_guard || bad emergency_core_sitemap_guard
grep -q 'test_04_appointment_inactive_is_noindex_follow' "$THEME/tests/test-seo-fallback.php" && grep -q 'test_10_core_sitemap_is_disabled' "$THEME/tests/test-seo-fallback.php" && pass seo_policy_tests_shipped || bad seo_policy_tests_shipped

# Form/rate/privacy.
grep -q 'alipasandi_form_rate_limit_consume' "$PLUGIN/includes/forms.php" && pass quota_consumed_after_mail_acceptance_only || bad quota_policy
if grep -q 'set_transient( \$key, \$count + 1' "$PLUGIN/includes/forms.php" && grep -q 'hash_hmac' "$PLUGIN/includes/forms.php"; then pass hmac_counter_only; else bad hmac_counter_only; fi
grep -q 'alipasandi_valid_patient_phone' "$PLUGIN/includes/forms.php" && pass patient_phone_validator_used || bad patient_phone_validator

# Explicit schema diagnosis/repair; never silent.
grep -q 'function alipasandi_service_schema_repair_plan' "$PLUGIN/includes/service-content.php" && grep -q 'schema-repair' "$PLUGIN/includes/service-content.php" && pass explicit_schema_repair_tool || bad schema_repair_tool

# Theme remains presentation-only.
if grep -RIn --include='*.php' -E "add_action\( *'admin_post_(nopriv_)?alipasandi_(contact|appointment)'|update_post_meta\(|register_post_meta\(" "$THEME" >/tmp/theme-write.$$ 2>/dev/null; then
  cat /tmp/theme-write.$$
  bad theme_write_handler_present
else
  pass theme_presentation_only
fi
rm -f /tmp/theme-write.$$
if grep -q 'accessibility-ready' "$THEME/style.css"; then bad accessibility_claim; else pass no_accessibility_ready_claim; fi

# Current docs should identify the 1.4.28/1.3.3 pair; old versions are allowed
# only where explicitly described as historical/superseded.
grep -q 'Theme 1.4.28 / Plugin 1.3.3' "$THEME/docs/MAINTENANCE-GUIDE-FA.md" && pass maintenance_pair_current || bad maintenance_pair_current

exit "$fail"

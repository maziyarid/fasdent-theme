<?php
/**
 * Local executable bootstrap regression for the WP-CLI registration path.
 * This is NOT a substitute for real WP-CLI + WordPress runtime evidence.
 * It exists specifically to catch class-token errors such as the historical
 * literal "\\tWP_CLI::add_command" bug that php -l accepted.
 */
if ( $argc < 2 ) {
    fwrite( STDERR, "usage: php wp-cli-bootstrap-smoke.php /path/to/plugin\n" );
    exit( 64 );
}
$plugin = rtrim( $argv[1], '/\\' );
define( 'ABSPATH', __DIR__ . '/fake-wp/' );
define( 'WP_CLI', true );
$GLOBALS['environment_type'] = 'production';
function wp_get_environment_type() { return $GLOBALS['environment_type']; }

class WP_Post {
    public $ID;
    public $post_name;
    public $post_type = 'page';
    public $post_status = 'publish';
    public $post_parent = 0;
    public function __construct( $id, $slug ) { $this->ID = $id; $this->post_name = $slug; }
}
class WP_CLI {
    public static $commands = array();
    public static $lines = array();
    public static function add_command( $name, $callable ) { self::$commands[ $name ] = $callable; }
    public static function line( $line ) { self::$lines[] = $line; }
    public static function halt( $code ) { throw new RuntimeException( 'WP_CLI halt ' . $code, $code ); }
}
function add_action() { return true; }
function add_filter() { return true; }
function sanitize_key( $v ) { return strtolower( preg_replace( '/[^a-z0-9_-]/i', '', (string) $v ) ); }
function alipasandi_service_keys() { return array( 'implant', 'crown', 'surgery', 'general' ); }
function alipasandi_service_registry_item( $key ) { return array( 'page_slug' => 'services/' . $key ); }
function get_posts( $args ) {
    static $ids = array( 'implant'=>256, 'crown'=>257, 'surgery'=>258, 'general'=>259 );
    $key = isset( $args['meta_value'] ) ? (string) $args['meta_value'] : '';
    return isset( $ids[ $key ] ) ? array( new WP_Post( $ids[ $key ], $key ) ) : array();
}
function get_page_by_path() { return null; }
function metadata_exists() { return true; }
function get_post_meta( $id, $key, $single = true ) { return array( 'schema_version' => 1 ); }
function wp_json_encode( $v, $flags = 0 ) { return json_encode( $v, $flags ); }

require $plugin . '/includes/service-content.php';

$expected = array(
    'alipasandi service migrate',
    'alipasandi service schema-repair',
    'alipasandi service health',
);
foreach ( $expected as $name ) {
    if ( ! isset( WP_CLI::$commands[ $name ] ) || ! is_callable( WP_CLI::$commands[ $name ] ) ) {
        fwrite( STDERR, "FAIL missing command: {$name}\n" );
        exit( 1 );
    }
}

// Execute the schema-repair dry-run callback against four current-schema fake
// records. This proves command registration can be invoked without Fatal.
try {
    WP_CLI::$commands['alipasandi service schema-repair']( array(), array() );
} catch ( Throwable $e ) {
    fwrite( STDERR, "FAIL schema-repair dry-run callback: " . $e->getMessage() . "\n" );
    exit( 1 );
}
$last = end( WP_CLI::$lines );
$decoded = json_decode( (string) $last, true );
if ( ! is_array( $decoded ) || empty( $decoded['pass'] ) || ! empty( $decoded['write'] ) ) {
    fwrite( STDERR, "FAIL schema-repair dry-run result\n" );
    exit( 1 );
}
printf( "PASS WP-CLI registration: %s\n", implode( ', ', $expected ) );
printf( "PASS schema-repair dry-run callback: write=false pass=true\n" );

// Production apply must fail closed before any write path.
$apply = alipasandi_service_schema_repair_apply( str_repeat( 'a', 64 ) );
if ( ! is_array( $apply ) || empty( $apply['blocked'] ) || ! empty( $apply['write'] ) || 'production_apply_forbidden_use_staging_clone_with_backup' !== ( $apply['reason'] ?? '' ) ) {
    fwrite( STDERR, "FAIL schema-repair production apply guard\n" );
    exit( 1 );
}
printf( "PASS schema-repair production apply guard: blocked=true write=false\n" );

// On staging, apply requires the exact SHA emitted by the immediately
// preceding dry-run. This makes dry-run a code-enforced prerequisite.
$GLOBALS['environment_type'] = 'staging';
$missing_confirm = alipasandi_service_schema_repair_apply();
if ( empty( $missing_confirm['blocked'] ) || 'schema_repair_apply_requires_matching_dry_run_plan_sha' !== ( $missing_confirm['reason'] ?? '' ) ) {
    fwrite( STDERR, "FAIL schema-repair staging confirm-plan guard\n" );
    exit( 1 );
}
$plan = alipasandi_service_schema_repair_plan();
$confirmed = alipasandi_service_schema_repair_apply( $plan['plan_sha256'] );
if ( empty( $confirmed['pass'] ) || empty( $confirmed['write'] ) || ! empty( $confirmed['blocked'] ) ) {
    fwrite( STDERR, "FAIL schema-repair confirmed staging no-op apply\n" );
    exit( 1 );
}
printf( "PASS schema-repair staging confirm-plan guard and matching-plan apply\n" );

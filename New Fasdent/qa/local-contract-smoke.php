<?php
$plugin_root = isset( $argv[1] ) ? rtrim( $argv[1], '/\\' ) : '';
if ( '' === $plugin_root || ! is_dir( $plugin_root ) ) { fwrite( STDERR, 'usage: php local-contract-smoke.php /path/to/plugin\n' ); exit(64); }
define('ABSPATH',__DIR__.'/');
$GLOBALS['filters']=array();
function add_filter($t,$c,$p=10){$GLOBALS['filters'][$t][$p][]=$c;} function remove_all_filters($t){unset($GLOBALS['filters'][$t]);}
function apply_filters($t,$v){if(empty($GLOBALS['filters'][$t]))return$v;ksort($GLOBALS['filters'][$t]);foreach($GLOBALS['filters'][$t] as$cbs)foreach($cbs as$cb)$v=$cb($v);return$v;}
function sanitize_key($k){return preg_replace('/[^a-z0-9_\-]/','',strtolower((string)$k));} function sanitize_text_field($v){return trim(strip_tags((string)$v));}
function alipasandi_normalize_digits($v){return strtr((string)$v,array('۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'));}
class WP_Error{private$c;function __construct($c,$m){$this->c=$c;}function get_error_code(){return$this->c;}} function is_wp_error($v){return$v instanceof WP_Error;}
function wp_timezone(){return new DateTimeZone('Asia/Tehran');} function wp_timezone_string(){return'Asia/Tehran';} function current_datetime(){return new DateTimeImmutable('2026-08-14 17:00:30',wp_timezone());}
$GLOBALS['clinic_options']=array('clinic_opening_hours'=>''); function alipasandi_clinic_option($k){return$GLOBALS['clinic_options'][$k]??'';}
require $plugin_root . '/includes/service-registry.php';
require $plugin_root . '/includes/validators.php';
$P=0;$F=0; function t($n,$ok,$d=''){global$P,$F;$ok?$P++:$F++;echo($ok?'PASS':'FAIL'),"\t$n\t$d\n";}
$r=alipasandi_service_registry(); $book=alipasandi_bookable_services();
t('registry_default_count',8===count($r),'count='.count($r)); t('allowed_services_stable_keys',array_keys($book)===alipasandi_allowed_services(),implode(',',alipasandi_allowed_services())); t('label_not_identity',''===alipasandi_service_label('ایمپلنت دندان')); t('implant_label_resolves','ایمپلنت دندان'===alipasandi_service_label('implant'));
add_filter('alipasandi_service_registry',function($x){$x['dupe']=array('key'=>'dupe','label'=>$x['implant']['label'],'bookable'=>true,'page_slug'=>'','content_managed'=>false,'icon'=>'tooth');return$x;}); $s=alipasandi_service_registry_state(true); t('duplicate_label_warns',!empty($s['warnings']),implode(',',$s['warnings'])); remove_all_filters('alipasandi_service_registry');alipasandi_service_registry_state(true);
add_filter('alipasandi_service_registry',function($x){$x['bad']=array('key'=>'bad','label'=>'Bad','bookable'=>true,'page_slug'=>'','content_managed'=>false,'icon'=>'tooth','evil'=>'x');return$x;}); $s=alipasandi_service_registry_state(true);t('unknown_field_fail_closed',!isset($s['items']['bad'])&&in_array('unknown_field:bad:evil',$s['issues'],true),implode(',',$s['issues']));remove_all_filters('alipasandi_service_registry');alipasandi_service_registry_state(true);
t('horizon_365',365===alipasandi_booking_horizon_days());t('lead_0',0===alipasandi_booking_min_lead_minutes());
$d=alipasandi_validate_appointment_date('2026-08-14');t('today_date_valid',$d instanceof DateTimeImmutable);t('same_day_past_fail',is_wp_error(alipasandi_validate_appointment_datetime($d,'10:00')));t('same_day_equal_minute_fail',is_wp_error(alipasandi_validate_appointment_datetime($d,'17:00')));t('same_day_future_pass',alipasandi_validate_appointment_datetime($d,'17:01') instanceof DateTimeImmutable);
t('plus365_pass',alipasandi_validate_appointment_date('2027-08-14') instanceof DateTimeImmutable);t('plus366_fail',is_wp_error(alipasandi_validate_appointment_date('2027-08-15')));t('invalid_2026_leap_fail',is_wp_error(alipasandi_validate_appointment_date('2026-02-29')));
add_filter('alipasandi_booking_now',fn()=>new DateTimeImmutable('2028-01-01 09:00:00',wp_timezone()));t('valid_2028_leap_pass',alipasandi_validate_appointment_date('2028-02-29') instanceof DateTimeImmutable);remove_all_filters('alipasandi_booking_now');
$cases=array("MO=09:00-13:00,13:00-18:00\nFR=CLOSED"=>true,"MO=09:00-12:00\nMO=13:00-14:00"=>false,'MO=09:00-13:00,12:00-18:00'=>false,'MO=18:00-09:00'=>false,'FR=CLOSED,09:00-10:00'=>false,"MO=۰۹:۰۰-۱۳:۰۰\nFR=CLOSED"=>true);
foreach($cases as$raw=>$want){$p=alipasandi_parse_opening_hours($raw);t('hours_'.substr(hash('sha1',$raw),0,8),empty($p['errors'])===$want,implode(';',$p['errors']));}
t('e164_valid',alipasandi_valid_e164('+989123456789'));t('e164_leading_zero_fail',!alipasandi_valid_e164('+0989123456789'));t('country_ir_normalizes',alipasandi_valid_country_code('ir'));t('geo_bounds',alipasandi_valid_geo('-90','lat')&&alipasandi_valid_geo('90','lat')&&!alipasandi_valid_geo('90.1','lat')&&alipasandi_valid_geo('-180','lng')&&alipasandi_valid_geo('180','lng')&&!alipasandi_valid_geo('180.1','lng'));t('phone_real_digits',alipasandi_valid_patient_phone('0912 345 6789')&&!alipasandi_valid_patient_phone('-------')&&!alipasandi_valid_patient_phone('09ABC123456'));
echo"SUMMARY\tPASS=$P\tFAIL=$F\tPHP=".PHP_VERSION."\n";exit($F?1:0);

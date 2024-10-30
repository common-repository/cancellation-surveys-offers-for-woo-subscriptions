<?php namespace MeowCrew\CancellationOffers\SurveyAnswers;

class SurveyAnswersTable {
	
	const TABLE_NAME = 'wc_cancellation_offer_survey_answers';
	
	public static function create() {
		global $wpdb;
		
		$sql = 'CREATE TABLE IF NOT EXISTS ' . self::getTableName() . ' (
			id BIGINT(10) NOT NULL AUTO_INCREMENT,
			subscription_id BIGINT(10) NOT NULL,
			user_id BIGINT(10) NOT NULL,
			offer_id BIGINT(10) NOT NULL,
			
			survey_selected_answer TEXT(3005),
			survey_text_answer TEXT(3005),
			
			coupon_id BIGINT(10),
			
			is_seen TINYINT(1) NOT NULL DEFAULT 0,
			
			is_survey_enabled TINYINT(1) NOT NULL,
			
			is_discount_offer_enabled TINYINT(1) NOT NULL,
			is_discount_offer_accepted TINYINT(1) DEFAULT 0,
			
			date_created DATETIME NOT NULL,
			PRIMARY KEY  (`id`)
		) ' . $wpdb->get_charset_collate() . ";\n";
		
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		
		dbDelta( $sql );
	}
	
	public static function delete() {
		global $wpdb;
		
		$wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS {$wpdb->prefix}wc_cancellation_offer_survey_answers" ) );
	}
	
	public static function getTableName(): string {
		global $wpdb;
		
		return $wpdb->prefix . self::TABLE_NAME;
	}
}

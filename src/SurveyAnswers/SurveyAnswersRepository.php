<?php namespace MeowCrew\CancellationOffers\SurveyAnswers;

use Exception;
use MeowCrew\CancellationOffers\Core\ServiceContainerTrait;
use WP_Error;

class SurveyAnswersRepository {
	
	use ServiceContainerTrait;
	
	/**
	 * Get feedbacks
	 *
	 * @param  int  $offset
	 * @param  int  $limit
	 * @param  string  $where
	 *
	 * @return SurveyAnswer[]
	 * @throws Exception
	 */
	public static function get(
		int $offset = 0,
		int $limit = 10,
		string $where = ''
	): array {
		global $wpdb;
		
		// because phpcs has too many checking for wpdb;
		$database = $wpdb;
		
		$where = ' WHERE 1=1 ' . $where;
		
		$feedbacks = $database->get_results( $database->prepare( "SELECT * FROM %i {$where} ORDER BY `date_created` DESC LIMIT %d OFFSET %d",
			SurveyAnswersTable::getTableName(), $limit, $offset ), ARRAY_A );
		
		if ( $feedbacks instanceof WP_Error ) {
			throw new Exception( 'Feedbacks error' );
		}
		
		return array_filter( array_map( function ( $feedback ) {
			try {
				return SurveyAnswer::buildFromArray( $feedback );
			} catch ( Exception $e ) {
				return null;
			}
		}, $feedbacks ) );
	}
	
	public static function getById( $feedbackId ): ?SurveyAnswer {
		return self::getByColumn( 'id', $feedbackId );
	}
	
	public static function getByColumn( $column, $value ): ?SurveyAnswer {
		global $wpdb;
		
		$rawFeedback = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %i WHERE  %i = %d',
			SurveyAnswersTable::getTableName(), $column, $value ), ARRAY_A );
		
		if ( $rawFeedback ) {
			try {
				return SurveyAnswer::buildFromArray( $rawFeedback );
			} catch ( Exception $e ) {
				return null;
			}
		}
		
		return null;
	}
	
	public static function getUnseenSurveyAnswersCount(): int {
		global $wpdb;
		
		return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE is_seen = 0',
			SurveyAnswersTable::getTableName() ) );
	}
	
	public static function getAllSurveyAnswersCount(): int {
		global $wpdb;
		
		return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i', SurveyAnswersTable::getTableName() ) );
	}
	
	
	public static function markAllAsSeen( array $ids = array() ) {
		global $wpdb;
		// because phpcs has too many checking for wpdb;
		$database = $wpdb;
		
		$idsWhere = '';
		
		if ( ! empty( $ids ) ) {
			$idsWhere = ' WHERE id IN (' . implode( ',', array_map( 'absint', $ids ) ) . ')';
		}
		
		$database->query( 'UPDATE ' . SurveyAnswersTable::getTableName() . ' SET is_seen = 1 ' . $idsWhere );
	}
	
	public static function bulkDelete( array $feedbacksIds ) {
		global $wpdb;
		
		$ids = implode( ',', array_map( 'absint', $feedbacksIds ) );
		$wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE ID IN(%s)', SurveyAnswersTable::getTableName(), $ids ) );
	}
}

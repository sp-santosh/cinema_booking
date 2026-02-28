<?php
/**
 * Scheduled job to deactivate movies whose final screening ended yesterday.
 */
class ReleaseMoviesJob
{
    public function handle(array $args): void
    {
        $db = Database::getInstance()->getConnection();

        // Find movies where their *latest* screening end_time is in the past
        $query = "
            UPDATE movies m
            SET is_active = 0
            WHERE is_active = 1 
            AND NOT EXISTS (
                SELECT 1 FROM screenings sc 
                WHERE sc.movie_id = m.movie_id 
                AND sc.end_time > NOW()
            )
        ";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();

        echo "Success: ReleaseMoviesJob completed. Deactivated {$affectedRows} expired movies.\n";
    }
}

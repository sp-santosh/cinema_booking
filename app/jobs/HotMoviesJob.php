<?php
/**
 * Scheduled job to dynamically find trending movies based on active bookings.
 */
class HotMoviesJob
{
    public function handle(array $args): void
    {
        $db = Database::getInstance()->getConnection();

        // 1. Calculate trending score based on recent bookings
        $query = "
            SELECT m.movie_id, m.title, COUNT(b.booking_id) AS booking_count
            FROM movies m
            LEFT JOIN screenings sc ON sc.movie_id = m.movie_id
            LEFT JOIN bookings b ON b.screening_id = sc.screening_id 
                AND b.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            WHERE m.is_active = 1
            GROUP BY m.movie_id
            ORDER BY booking_count DESC
            LIMIT 5
        ";
        
        $stmt = $db->query($query);
        $trendingMovies = $stmt->fetchAll();

        echo "--- Trending Movies This Week ---\n";
        foreach ($trendingMovies as $tm) {
            echo "- {$tm['title']} (Bookings: {$tm['booking_count']})\n";
        }
        
        // In a real app, you might save this to a Redis cache or a `trending_movies` table.
        // Cache::set('hot_movies', $trendingMovies, 86400);

        echo "Success: Trending movies recalculated.\n";
    }
}

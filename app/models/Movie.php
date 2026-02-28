<?php
class Movie extends Model
{
    protected string $table = 'movies';
    protected string $pk    = 'movie_id';

    public function create(array $data): int
    {
        return $this->insert([
            'title'            => $data['title'],
            'duration_minutes' => (int) $data['duration_minutes'],
            'age_rating'       => $data['age_rating'],
            'movie_rating'     => $data['movie_rating'] ?? null,
            'description'      => $data['description']  ?? null,
            'poster_url'       => $data['poster_url']   ?? null,
            'is_active'        => 1,
        ]);
    }

    public function updateMovie(int $id, array $data): int
    {
        return $this->update($data, ['movie_id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->deleteById($id);
    }

    /** Paginated list of active movies. */
    public function getActive(int $page = 1, int $limit = ITEMS_PER_PAGE): array
    {
        $offset = ($page - 1) * $limit;
        $stmt   = $this->db->prepare(
            'SELECT * FROM movies WHERE is_active = 1 ORDER BY title LIMIT ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function countActive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM movies WHERE is_active = 1")->fetchColumn();
    }

    /** Full-text search by title. */
    public function search(string $query): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM movies WHERE is_active = 1 AND title LIKE ? ORDER BY title"
        );
        $stmt->execute(['%' . $query . '%']);
        return $stmt->fetchAll();
    }

    public function setActive(int $id, bool $active): int
    {
        return $this->update(['is_active' => (int) $active], ['movie_id' => $id]);
    }
}

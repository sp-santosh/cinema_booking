<?php
class Hall extends Model
{
    protected string $table = 'halls';
    protected string $pk    = 'hall_id';

    public function create(int $cinemaId, string $name): int
    {
        return $this->insert(['cinema_id' => $cinemaId, 'name' => $name]);
    }

    public function getByCinema(int $cinemaId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM halls WHERE cinema_id = ? ORDER BY name');
        $stmt->execute([$cinemaId]);
        return $stmt->fetchAll();
    }

    public function delete(int $id): int
    {
        return $this->deleteById($id);
    }
}

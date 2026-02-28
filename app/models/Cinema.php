<?php
class Cinema extends Model
{
    protected string $table = 'cinemas';
    protected string $pk    = 'cinema_id';

    public function create(array $data): int
    {
        return $this->insert([
            'name'          => $data['name'],
            'address_line1' => $data['address_line1'],
            'city'          => $data['city'],
            'postcode'      => $data['postcode'],
        ]);
    }

    public function updateCinema(int $id, array $data): int
    {
        return $this->update($data, ['cinema_id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->deleteById($id);
    }

    /** Get cinema with its halls. */
    public function getWithHalls(int $cinemaId): ?array
    {
        $cinema = $this->findById($cinemaId);
        if (!$cinema) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT * FROM halls WHERE cinema_id = ? ORDER BY name');
        $stmt->execute([$cinemaId]);
        $cinema['halls'] = $stmt->fetchAll();

        return $cinema;
    }
}

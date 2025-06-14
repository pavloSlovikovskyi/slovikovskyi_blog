<?php

namespace App\Repositories;

use App\Models\BlogCategory as Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BlogCategoryRepository.
 */
class BlogCategoryRepository extends CoreRepository
{
    protected function getModelClass()
    {
        // Абстрагування моделі BlogCategory, для легшого створення іншого репозиторія
        return Model::class;
    }

    /**
     * Отримати модель для редагування в адмінці
     * @param int $id
     * @return Model
     */
    public function getEdit($id)
    {
        return $this->startConditions()->find($id);
    }

    /**
     * Отримати список категорій для виводу в випадаючий список
     * @return Collection
     */
    /**
     * @return string
     */

    public function getForComboBox(): Collection
    {
        $columns = implode(', ', [
            'id',
            'title',
            'parent_id',
        ]);

        $result = $this->startConditions()
            ->select($columns)
            ->with(['parentCategory:id,title']) // Додаємо цей рядок для eager loading
            ->toBase()
            ->get();

        return $result;
    }
    /**
     * Отримати категорію для виводу пагінатором
     * * @param int|null $perPage
     * * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPaginate($perPage = null)
    {
        $columns = ['id', 'title', 'parent_id'];

        $result = $this
            ->startConditions()
            ->select($columns)
            ->paginate($perPage); // можна $columns додати сюди

        return $result;
    }
}

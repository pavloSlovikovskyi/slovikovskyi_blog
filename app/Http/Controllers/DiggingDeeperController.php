<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Support\Collection; // Додано для підказки типу Collection
use App\Jobs\ProcessVideoJob;
use App\Jobs\GenerateCatalog\GenerateCatalogMainJob;

class DiggingDeeperController extends Controller
{
    /**
     * Базова інформація
     * @url https://laravel.com/docs/11.x/collections#introduction
     *
     * Довідкова інформація
     * @url https://laravel.com/api/11.x/Illuminate/Support/Collection.html
     *
     * Варіант колеції для моделі eloquent
     * @url https://laravel.com/api/11.x/Illuminate/Database/Eloquent/Collection.html
     */
    public function collections()
    {
        $result = [];

        /**
         * @var \Illuminate\Database\Eloquent\Collection $eloquentCollection
         */
        // Отримуємо всі пости, включаючи ті, що були "м'яко" видалені (soft deleted)
        $eloquentCollection = BlogPost::withTrashed()->get();

         dd(__METHOD__, $eloquentCollection, $eloquentCollection->toArray());

        /**
         * @var \Illuminate\Support\Collection $collection
         */
        // Перетворюємо Eloquent Collection у базову колекцію Laravel
        $collection = collect($eloquentCollection->toArray());

        /* dd(
             get_class($eloquentCollection), // Illuminate\Database\Eloquent\Collection
             get_class($collection),      // Illuminate\Support\Collection
             $collection
         );*/

        // Вибираємо перший елемент колекції
        $result['first'] = $collection->first();
        // Вибираємо останній елемент колекції
        $result['last'] = $collection->last();

        // Фільтруємо елементи, де category_id дорівнює 10
        // values() переіндексовує колекцію, щоб ключі йшли по порядку (0, 1, 2...)
        // keyBy('id') встановлює значення 'id' кожного елемента як ключ елемента в колекції
        $result['where']['data'] = $collection
            ->where('category_id', 10)
            ->values()
            ->keyBy('id');

        // Отримуємо кількість елементів у відфільтрованій колекції
        $result['where']['count'] = $result['where']['data']->count();
        // Перевіряємо, чи колекція порожня
        $result['where']['isEmpty'] = $result['where']['data']->isEmpty();
        // Перевіряємо, чи колекція не порожня
        $result['where']['isNotEmpty'] = $result['where']['data']->isNotEmpty();

        // Приклад використання isNotEmpty
        if ($result['where']['data']->isNotEmpty()) {
            // Можна виконувати якісь дії, якщо колекція не порожня
        }

        // Знаходимо перший елемент, де created_at більше вказаної дати
        $result['where_first'] = $collection
            ->firstWhere('created_at', '>' , '2020-02-24 03:46:16');

        // Метод map не змінює оригінальну колекцію, повертаючи нову
        $result['map']['all'] = $collection->map(function ($item) {
            $newItem = new \stdClass(); // Створюємо новий об'єкт для перетвореного елемента
            $newItem->item_id = $item['id'];
            $newItem->item_name = $item['title'];
            $newItem->exists = is_null($item['deleted_at']); // Перевіряємо, чи елемент не був видалений (soft delete)

            return $newItem;
        });

        // Фільтруємо перетворені елементи, щоб отримати лише ті, що були видалені
        $result['map']['not_exists'] = $result['map']['all']->where('exists', '=', false)->values()->keyBy('item_id');

        // dd ($result); // Розкоментуйте, щоб побачити результат 'first', 'last', 'where', 'where_first', 'map'

       // Метод transform змінює оригінальну колекцію (трансформує її)
        $collection->transform(function ($item) {
            $newItem = new \stdClass();
            $newItem->item_id = $item['id'];
            $newItem->item_name = $item['title'];
            $newItem->exists = is_null($item['deleted_at']);
            $newItem->created_at = Carbon::parse($item['created_at']); // Перетворюємо дату в об'єкт Carbon

            return $newItem;
        });

        // dd ($collection); // Розкоментуйте, щоб побачити трансформовану колекцію

        $newItem = new \stdClass;
        $newItem->id = 9999;

        $newItem2 = new \stdClass;
        $newItem2->id = 8888;

        // dd ($newItem, $newItem2); // Розкоментуйте, щоб побачити створені об'єкти

        // Додаємо елемент на початок колекції (prepend)
        $newItemFirst = $collection->prepend($newItem)->first();
        // Додаємо елемент в кінець колекції (push)
        $newItemLast = $collection->push($newItem2)->last();
        // Видаляємо елемент за ключем (pull) і повертаємо його
        $pulledItem = $collection->pull(1);

        // dd(compact('collection', 'newItemFirst' , 'newItemLast', 'pulledItem')); // Розкоментуйте, щоб побачити результати prepend, push, pull

        // Фільтрація колекції за складними умовами
        $filtered = $collection->filter(function ($item) {
            $byDay = $item->created_at->isFriday();    // Перевіряємо, чи дата є п'ятницею (використовуючи Carbon)
            $byDate = $item->created_at->day == 11;    // Перевіряємо, чи день місяця дорівнює 11

            $result = $byDay && $byDate; // Об'єднуємо умови
            // $result = $item->created_at->isFriday() && ($item->created_at->day == 11); // Такий запис теж можливий, але попередній більш читабельний

            return $result;
        });

        // dd(compact('filtered')); // Розкоментуйте, щоб побачити відфільтровану колекцію (закоментувати 91-106 рядки перед перевіркою)

        // Сортування простої колекції чисел
        $sortedSimpleCollection = collect([5, 3, 1, 2, 4])->sort()->values();
        // Сортування колекції об'єктів за полем created_at (за зростанням)
        $sortedAscCollection = $collection->sortBy('created_at');
        // Сортування колекції об'єктів за полем item_id (за спаданням)
        $sortedDescCollection = $collection->sortByDesc('item_id');

         dd(compact('sortedSimpleCollection', 'sortedAscCollection', 'sortedDescCollection')); // Розкоментуйте, щоб побачити результати сортування

        // Просто повертаємо порожній рядок, оскільки основна логіка демонстрації через dd()
        return '';
    }
    public function processVideo()
    {
        ProcessVideoJob::dispatch();
        //  Відкладення виконання завдання від моменту потрапляння в чергу.
        //  Не впливає на паузу між спробами виконання завдання.
        //->delay(10)
        //->onQueue('name_of_queue')
    }

    /**
     * @link http://localhost:8000/digging_deeper/prepare-catalog
     *
     * php artisan queue:listen --queue=generate-catalog --tries=3 --delay=10
     */
    public function prepareCatalog()
    {
        GenerateCatalogMainJob::dispatch();
    }
}

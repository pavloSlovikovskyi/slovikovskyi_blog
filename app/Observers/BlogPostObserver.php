<?php

namespace App\Observers;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BlogPostObserver
{
    /**
     * Handle the BlogPost "created" event.
     */
    public function created(BlogPost $blogPost): void
    {
        //
    }

    /**
     * Handle the BlogPost "updated" event.
     */
    public function updated(BlogPost $blogPost): void
    {
        //
    }

    /**
     * Handle the BlogPost "deleted" event.
     */
    public function deleted(BlogPost $blogPost): void
    {
        //
    }

    /**
     * Handle the BlogPost "restored" event.
     */
    public function restored(BlogPost $blogPost): void
    {
        //
    }

    /**
     * Handle the BlogPost "force deleted" event.
     */
    public function forceDeleted(BlogPost $blogPost): void
    {
        //
    }

    /**
     * Обробка перед оновленням запису.
     *
     * @param  BlogPost  $blogPost
     *
     */
    public function updating(BlogPost $blogPost)
    {
        $this->setPublishedAt($blogPost);

        $this->setSlug($blogPost);
    }

    /**
     * якщо поле published_at порожнє і нам прийшло 1 в ключі is_published,
     * то генеруємо поточну дату
     *
     * @param BlogPost $blogPost
     */
    protected function setPublishedAt(BlogPost $blogPost)
    {
        if (empty($blogPost->published_at) && $blogPost->is_published) {
            $blogPost->published_at = Carbon::now();
        }
    }

    /**
     * якщо псевдонім порожній
     * то генеруємо псевдонім
     *
     * @param BlogPost $blogPost
     */
    protected function setSlug(BlogPost $blogPost)
    {
        if (empty($blogPost->slug)) {
            $blogPost->slug = \Str::slug($blogPost->title);
        }
    }

    /**
     * Обробка перед створенням запису.
     *
     * @param  BlogPost  $blogPost
     * @return void
     */
    public function creating(BlogPost $blogPost): void
    {
        $this->setPublishedAt($blogPost);
        $this->setSlug($blogPost);
        $this->setHtml($blogPost);
        $this->setUser($blogPost);
    }

    /**
     * Встановлюємо значення полю content_html з поля content_raw.
     * * @param BlogPost $blogPost
     * @return void
     */
    protected function setHtml(BlogPost $blogPost): void
    {
        if ($blogPost->isDirty('content_raw')) {
            // Тут треба зробити генерацію markdown -> html
            // Поки що просто копіюємо, пізніше можна інтегрувати Markdown-парсер
            $blogPost->content_html = $blogPost->content_raw;
        }
    }

    /**
     * Якщо user_id не вказано, то встановимо юзера 1 (UNKNOWN_USER).
     * * @param BlogPost $blogPost
     * @return void
     */
    protected function setUser(BlogPost $blogPost): void
    {
        if (empty($blogPost->user_id)) {
            $blogPost->user_id = auth()->id() ?? BlogPost::UNKNOWN_USER;
        }
    }

}



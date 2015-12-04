<?php

namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Riari\Forum\Models\Traits\HasAuthor;

class Post extends BaseModel
{
    use SoftDeletes, HasAuthor;

    /**
     * Eloquent attributes
     */
    protected $table        = 'forum_posts';
    public    $timestamps   = true;
    protected $fillable     = ['thread_id', 'author_id', 'post_id', 'content'];
    protected $guarded      = ['id'];
    protected $with         = ['author'];

    /**
     * Create a new post model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setPerPage(config('forum.preferences.pagination.posts'));
    }

    /**
     * Relationship: Thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    /**
     * Relationship: Parent post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Relationship: Child posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Post::class, 'post_id')->withTrashed();
    }

    /**
     * Attribute: First post flag.
     *
     * @return boolean
     */
    public function getIsFirstAttribute()
    {
        return $this->id == $this->thread->firstPost->id;
    }
}

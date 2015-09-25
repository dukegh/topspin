<?php

namespace App;

use Config;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Article
 * @package App
 *
 * @property string content
 * @property string picture
 * @property mixed introduction
 */
class Article extends Model implements SluggableInterface {

	use SoftDeletes;
	use SluggableTrait;

	protected $dates = ['deleted_at'];

	protected $sluggable = [
		'build_from' => 'title',
		'save_to'    => 'slug',
	];

	protected $guarded  = array('id');

	/**
	 * Returns a formatted post content entry,
	 * this ensures that line breaks are returned.
	 *
	 * @return string
	 */
	public function content()
	{
		return nl2br($this->content);
	}

	/**
	 * Returns a formatted post content entry,
	 * this ensures that line breaks are returned.
	 *
	 * @return string
	 */
	public function introduction()
	{
		return nl2br($this->introduction);
	}

	/**
	 * Get the post's author.
	 *
	 * @return User
	 */
	public function author()
	{
		return $this->belongsTo('App\User', 'user_id');
	}

	/**
	 * Get the post's language.
	 *
	 * @return Language
	 */
	public function language()
	{
		return $this->belongsTo('App\Language');
	}

	/**
	 * Get the post's category.
	 *
	 * @return ArticleCategory
	 */
	public function category()
	{
		return $this->belongsTo('App\ArticleCategory', 'article_category_id');
	}

	function getPictureUrl($size = null) {
		if ($this->picture) {
			$picture = $size ? substr($this->picture, 0, -4) . '_' . $size . substr($this->picture, -4) : $this->picture;
			return '//' . Config::get('topspin.imageHost') . '/article/' . $this->id . '/' . $picture;
		} else {
			return '/appfiles/photoalbum/no_photo.png';
		}
	}

}

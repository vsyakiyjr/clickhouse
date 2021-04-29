<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cms\FeedbackFormEmails
 *
 * @property int $id
 * @property string $code
 * @property array $emails
 * @property array|null $config
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\FeedbackFormEmails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\FeedbackFormEmails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\FeedbackFormEmails query()
 * @mixin \Eloquent
 */
class FeedbackFormEmails extends Model {
	protected $table = 'cms__feedback_forms_emails';

	public $timestamps = false;

	protected $fillable = [
		'code',
		'emails',
		'config',
	];

	protected $casts = [
		'emails' => 'array',
		'config' => 'array',
	];
}

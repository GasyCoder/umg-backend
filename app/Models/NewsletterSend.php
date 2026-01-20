<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterSend extends Model
{
    use HasFactory;

    const STATUS_QUEUED = 'queued';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'newsletter_campaign_id',
        'newsletter_subscriber_id',
        'status',
        'sent_at',
        'opened_at',
        'open_count',
        'error',
    ];

    protected $casts = [
        'newsletter_campaign_id' => 'integer',
        'newsletter_subscriber_id' => 'integer',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'open_count' => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(NewsletterCampaign::class, 'newsletter_campaign_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(NewsletterSubscriber::class, 'newsletter_subscriber_id');
    }
}

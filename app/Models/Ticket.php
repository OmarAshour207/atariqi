<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    public const TYPE_INQUIRY = 'inquiry';
    public const TYPE_COMPLAINT = 'complaint';
    public const TYPE_TECHNICAL = 'technical';

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_CLOSED = 'closed';

    public const SENDER_CUSTOMER = 'customer';
    public const SENDER_EMPLOYEE = 'employee';

    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'ticket_type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'title',
        'description',
        'complaint_type',
        'related_service_reference',
        'device_type',
        'status',
        'assigned_employee_id',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public static function generateTicketNumber(): string
    {
        do {
            $number = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('ticket_number', $number)->exists());

        return $number;
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_employee_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->orderBy('created_at');
    }

    public function ticketLevelAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->whereNull('ticket_reply_id')->orderBy('created_at');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(TicketStatusLog::class)->orderBy('created_at');
    }

    public function employeeReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->where('sender_type', self::SENDER_EMPLOYEE);
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function hasEmployeeReply(): bool
    {
        return $this->employeeReplies()->exists();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->ticket_type) {
            self::TYPE_INQUIRY => __('Inquiry'),
            self::TYPE_COMPLAINT => __('Complaint'),
            self::TYPE_TECHNICAL => __('Technical Issue'),
            default => $this->ticket_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => __('New'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_CLOSED => __('Closed'),
            default => $this->status,
        };
    }

    public function getSubtypeLabelAttribute(): string
    {
        return match ($this->ticket_type) {
            self::TYPE_COMPLAINT => $this->complaint_type ?: __('Complaint'),
            self::TYPE_INQUIRY => $this->title ?: __('Inquiry'),
            self::TYPE_TECHNICAL => match ($this->device_type) {
                'android' => __('Android'),
                'ios' => __('iOS'),
                'web' => __('Web'),
                default => $this->device_type ?: __('Technical Issue'),
            },
            default => $this->type_label,
        };
    }

    public function changeStatus(string $newStatus, ?int $employeeId = null): void
    {
        if ($this->status === $newStatus) {
            return;
        }

        $oldStatus = $this->status;

        $this->status = $newStatus;

        if ($newStatus === self::STATUS_CLOSED) {
            $this->closed_at = now();
        }

        $this->save();

        TicketStatusLog::create([
            'ticket_id' => $this->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_employee_id' => $employeeId,
            'created_at' => now(),
        ]);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('ticket_type', $type);
    }

    public function scopeFilterAdmin(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['email'])) {
            $query->where('customer_email', 'like', '%' . trim($filters['email']) . '%');
        }

        if (!empty($filters['ticket_number'])) {
            $query->where('ticket_number', 'like', '%' . trim($filters['ticket_number']) . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function scopeSortAdmin(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'status' => $query
                ->orderByRaw("CASE status WHEN 'new' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'closed' THEN 3 ELSE 4 END")
                ->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };
    }

    public static function pageTypeMap(): array
    {
        return [
            'complaints' => self::TYPE_COMPLAINT,
            'inquiries' => self::TYPE_INQUIRY,
            'technical' => self::TYPE_TECHNICAL,
        ];
    }

    public static function pageTypeLabel(string $page): string
    {
        return match ($page) {
            'complaints' => __('Complaints'),
            'inquiries' => __('Inquiries'),
            'technical' => __('Technical Issues'),
            default => __('Support Tickets'),
        };
    }

    public static function filterStatuses(): array
    {
        return [
            self::STATUS_NEW => __('New'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_CLOSED => __('Closed'),
        ];
    }
}

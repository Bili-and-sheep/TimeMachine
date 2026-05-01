<?php

namespace App\Enum;

enum SubmissionStatus: string
{
    case Pending = 'pending';
    case NeedsChanges = 'needs_changes';
    case ApprovedByReview = 'approved_by_review';
    case Approved = 'approved';
    case RejectedByReview = 'rejected_by_review';
    case Rejected = 'rejected';

    public function isRejected(): bool
    {
        return $this === self::RejectedByReview || $this === self::Rejected;
    }

    public function label(): string
    {
        return match($this) {
            self::Pending           => 'Pending',
            self::NeedsChanges      => 'Needs changes',
            self::ApprovedByReview  => 'Approved by reviewer',
            self::Approved          => 'Approved',
            self::RejectedByReview  => 'Rejected by reviewer',
            self::Rejected          => 'Rejected',
        };
    }
}

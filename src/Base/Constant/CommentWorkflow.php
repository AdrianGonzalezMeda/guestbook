<?php

namespace App\Base\Constant;

class CommentWorkflow
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_HAM = 'ham';
    public const STATUS_POTENTIAL_SPAM = 'potential_spam';
    public const STATUS_SPAM = 'spam';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_READY = 'ready';
    public const STATUS_PUBLISHED = 'published';

    public const TRANSITION_ACCEPT = 'accept';
    public const TRANSITION_MIGHT_BE_SPAM = 'might_be_spam';
    public const TRANSITION_REJECT_SPAM = 'reject_spam';
    public const TRANSITION_PUBLISH = 'publish';
    public const TRANSITION_REJECT = 'reject';
    public const TRANSITION_PUBLISH_HAM = 'publish_ham';
    public const TRANSITION_REJECT_HAM = 'reject_ham';
    public const TRANSITION_OPTIMIZE = 'optimize';
}

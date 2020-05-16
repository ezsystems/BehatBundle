<?php


namespace EzSystems\BehatBundle\Event;


class Events
{
    public const START = 'volume_testing.start';

    public const START_TO_DRAFT = 'volume_testing.transition.start_to_draft';

    public const DRAFT_TO_REVIEW = 'volume_testing.transition.draft_to_review';
    public const DRAFT_TO_END = 'volume_testing.transition.draft_to_end';

    public const REVIEW_TO_REVIEW = 'volume_testing.transition.review_to_review';
    public const REVIEW_TO_PUBLISH = 'volume_testing.transition.review_to_publish';
    public const REVIEW_TO_PUBLISH_LATER = 'volume_testing.transition.review_to_publish_later';
    public const REVIEW_TO_END = 'volume_testing.transition.review_to_end';

    public const PUBLISH_TO_END = 'volume_testing.transition.publish_to_end';
    public const PUBLISH_TO_EDIT = 'volume_testing.transition.publish_to_edit';

    public const EDIT_TO_REVIEW = 'volume_testing.transition.edit_to_review';
    public const EDIT_TO_END = 'volume_testing.transition.edit_to_end';

    public const PUBLISH_LATER_TO_END = 'volume_testing.transition.publish_later_to_end';
}
<?php


namespace EzSystems\BehatBundle\Event;


class Events
{
    public const START = 'volume_testing.start';

    public const START_TO_DRAFT = 'volume_testing.transition.start_to_draft';

    public const DRAFT_TO_PUBLISH = 'transition.draft_to_publish';

    public const DRAFT_TO_END = 'transition.draft_to_end';

    public const PUBLISH_TO_END = 'transition.publish_to_end';

    public const PUBLISH_TO_EDIT = 'transition.publish_to_edit';

    public const EDIT_TO_PUBLISH = 'transition.edit_to_publish';

    public const EDIT_TO_END = 'transition.edit_to_end';

    public const EDIT_TO_EDIT = 'transition.edit_to_edit';
}
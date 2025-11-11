<?php

namespace App\Listeners;

use Filament\Actions\Action;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Kirschbaum\Commentions\Events\UserWasMentionedEvent;
use Filament\Notifications\Notification;
class SendMentionNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserWasMentionedEvent $event): void
    {
        $mentionedUser = $event->user;
        $comment = $event->comment;
        dd($comment);
        // ✅ Ensure commenter relationship is loaded safely
        $commenterName = optional($comment->commenter)->name ?? 'Someone';

        Notification::make()
            ->title('📣 You were mentioned in a comment!')
            ->body("{$commenterName} mentioned you in a comment.")
            ->actions([
                Action::make('View Comment')
                    ->url($this->getCommentUrl($comment) . '/edit')
                    ->openUrlInNewTab(),
            ])
            ->sendToDatabase($mentionedUser);
    }

    /**
     * Build the URL for the commentable item (Course, Quiz, etc.)
     */
    protected function getCommentUrl($comment): string
    {
        // Check what type of model was commented on
        $type = class_basename($comment->commentable_type);

        return match ($type) {
            'CourseMaterial' => url('/admin/course-materials/' . $comment->commentable_id),
            'Quiz' => url('/admin/quizzes/' . $comment->commentable_id),
            'Assignment' => url('/admin/assignments/' . $comment->commentable_id),
            default => url('/admin/comments/' . $comment->id),
        };
    }
}
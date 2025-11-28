<?php

namespace App\Mail;

use App\Models\UserMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public UserMessage $messageModel;

    public function __construct(UserMessage $message)
    {
        $this->messageModel = $message->loadMissing(['fromUser', 'toUser']);
    }

    public function build()
    {
        return $this->subject($this->messageModel->subject)
            ->view('emails.user_message_notification')
            ->with([
                'msg' => $this->messageModel,
            ]);
    }
}

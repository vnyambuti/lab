<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Setting;
use App\Traits\GeneralTrait;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels,GeneralTrait;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $info;
    public $emails;

    public function __construct(User $user)
    {
        $this->user=$user;

        //info
        $this->info=$this->setting('info');

        //patient code email
        $emails=$this->setting('emails');
        $this->emails=$emails;


    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset_password',[
                        'user'=>$this->user,
                        'emails'=>$this->emails,
                        'info'=>$this->info,
                    ])
                    ->subject($this->emails->reset_password->subject)->from($this->info->email,'noreply');
    }
}

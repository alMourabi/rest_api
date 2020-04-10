<?php

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailBase
{
   use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $prefix = config('frontend.url') . config('frontend.email_verify_url');
        $temporarySignedURL = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]
        );

        // I use urlencode to pass a link to my frontend.
        return $prefix . urlencode($temporarySignedURL);
    }
}
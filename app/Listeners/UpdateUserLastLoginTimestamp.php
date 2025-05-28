<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;

class UpdateUserLastLoginTimestamp
{
    public function handle(Login $event): void
    {
        $event->user->forceFill([ // forceFill umgeht Mass Assignment Schutz
            'last_login_at' => Carbon::now(),
        ])->save();
    }
}